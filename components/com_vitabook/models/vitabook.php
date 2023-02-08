<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;

// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');

// Import model classes
jimport('joomla.application.component.model');
jimport('joomla.application.component.modellist');

//Import filesystem libraries.
jimport('joomla.filesystem.file');


/**
 * Model
 */
class VitabookModelVitabook extends JModelList
{
    protected $_canDo;
    protected $_userId;
    protected $_messages;
    protected $_childMessages;
    protected $_parentCounter = array();
    protected $state;
    protected $_avatarSource;
    protected $_sourceAvailable;
    protected $_messageId;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   11.1
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Get user permissions
        $this->_canDo = VitabookHelper::getActions();
        // Get user id
        $this->_userId = JFactory::getUser()->get('id');
        //-- Get avatar parameters
        $this->_avatarSource = JComponentHelper::getParams('com_vitabook')->get('vbAvatar');
        $this->_sourceAvailable = VitabookHelperAvatar::checkAvatarSystem($this->_avatarSource);
        //-- get message id
        $this->_messageId = JFactory::getApplication()->input->get('messageId', null, 'int');
    }

    /**
     * Method to get a JDatabaseQuery object for retrieving the data set from a database.
     *
     * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
     *
     * @since   11.1
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('m.id,m.parent_id,m.level,m.jid,m.name,m.email,m.site,m.location,m.date,m.message,m.published,m.activated,m.ip');
        $query->from('#__vitabook_messages AS m');
        $query->where('m.parent_id = 1');
        if(!$this->_canDo->get('core.edit.state'))
        {
            $query->where('m.published = 1');
            $query->where('m.activated = 1');
        }
        // get database join for avatar
        if(!empty($this->_avatarSource) && $this->_sourceAvailable == true)
        {
            VitabookHelperAvatar::setAvatarQuery($query);
        }
        $query->order('m.lft DESC');
        return $query;
    }

    /**
     * Method to get an array of data items.
     * @return  mixed  An array of data items on success, false on failure.
     */
        public function getItems()
        {
            // have JModelList retrieve desired messages
            $items = parent::getItems();

            // postproces messages
            if(!empty($items))
            {
                foreach ($items as $k => $message):
                    // get actions
                    $message->actions = VitabookHelper::messageActions($this->_canDo,$message);
                    // get avatar
                    $message->avatar = VitabookHelperAvatar::getAvatarUrl($message);
                    // format date according to settings
                    $message->date = VitabookHelper::formatDate($message);
                    // check ip block
                    $message->ipblock = VitabookHelper::checkIpBlock($message->ip);
                    // add message id to list for retrieval of children
                    $parent_ids[] = $message->id;
                    // store message
                    $messages[$message->id] = $message;
                    // set empty 'children-pagination' counter
                    $this->_parentCounter[$message->id] = 0;
                endforeach;
                $this->_messages = $messages;

                // get children and inject into messages list
                $children = $this->getChildren($parent_ids);
                if(!empty($children))
                {
                    $this->sortChildMessages();
                }
            }
            return $this->_messages;
        }


    /**
     * Method to get a set of children of a (group of) parent id(s)
     *      Will recursively loop through the levels of replies, executing one db-query per level
     * @return  mixed An array of data items on success, false on failure.
     */
    public function getChildren($parent_ids,$start=0)
    {
        if(empty($parent_ids))
            return false;

        $limit = JFactory::getApplication()->getParams()->get('reply_limit');
        $parent_ids = implode(',',$parent_ids);

        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('m.id,m.parent_id,m.level,m.jid,m.name,m.email,m.site,m.location,m.date,m.message,m.published,m.activated,m.ip');
        $query->from('#__vitabook_messages AS m');
        $query->where('m.parent_id IN ('.$parent_ids.')');
        // if no rights: only published messages
        if(!$this->_canDo->get('core.edit.state'))
        {
            $query->where('m.published = 1');
            $query->where('m.activated = 1');
        }
        // get database join for avatar
        if(!empty($this->_avatarSource) && $this->_sourceAvailable == true)
        {
            VitabookHelperAvatar::setAvatarQuery($query);
        }
        $query->order('m.lft DESC');
        $db->setQuery($query,$start,1000); // retrieve max 1000 messages per level, it's unlikely this will limit will be reached in practice
        $children = $db->loadObjectList('id');
        unset($parent_ids);
        if(!empty($children))
        {
            foreach ($children as $message):
                // set parentCounters (for both message and parent) if not set yet (to prevent php notices)
                if(!isset($this->_parentCounter[$message->id]))
                    $this->_parentCounter[$message->id] = 0;
                if(!isset($this->_parentCounter[$message->parent_id]))
                    $this->_parentCounter[$message->parent_id] = 0;
                // if parent hasn't reached max children; process message
                if($this->_parentCounter[$message->parent_id] < $limit)
                {
                    // get actions
                    $message->actions = VitabookHelper::messageActions($this->_canDo,$message);
                    // get avatar url
                    $message->avatar = VitabookHelperAvatar::getAvatarUrl($message);
                    // format date according to settings
                    $message->date = VitabookHelper::formatDate($message);
                    // check ip block
                    $message->ipblock = VitabookHelper::checkIpBlock($message->ip);
                    // store this message for viewing
                    $this->_childMessages[$message->level][$message->id] = $message;
                    // add id to list for retrieval of higher levels
                    $parent_ids[] = $message->id;
                    $this->_parentCounter[$message->parent_id]++;
                }
                // if parent has reached max children; add load_more trigger to messages list
                elseif($this->_parentCounter[$message->parent_id] == $limit)
                {
                    $this->_childMessages[$message->level][$message->id] = (object)array("parent_id"=>$message->parent_id,"load_more"=>1,"start"=>$start+$limit);
                    $this->_parentCounter[$message->parent_id]++;
                }
            endforeach;
            unset($children);
            $this->getChildren($parent_ids);
        }
        return $this->_childMessages;
    }


    /**
     * Method to sort out a set of children
     * @return  mixed An array of data items on success, false on failure.
     */
    public function sortChildMessages()
    {
//TODO: check this function for php notices
        if(!empty($this->_childMessages))
        {
            // loop over child messages from bottom to top
            for($l=4;$l>1;$l--):
                if(isset($this->_childMessages[$l]))
                    ksort($this->_childMessages[$l]);

                // merge last level children in messages
                if($l == 2)
                {
                    // if no messages are present: just return children
                    if(empty($this->_messages))
                        return $this->_childMessages[$l];
                    //move children to parents
                    foreach($this->_childMessages[$l] as $id => $m):
                        $this->_messages[$m->parent_id]->children[] = $m;
                    endforeach;
                }
                else
                {
                    // if messages in this level and one up;
                    if(isset($this->_childMessages[$l]) && isset($this->_childMessages[$l-1]))
                    {
                        //move children to parents
                        foreach($this->_childMessages[$l] as $id => $m):
                            $this->_childMessages[$l-1][$m->parent_id]->children[] = $m;
                        endforeach;
                    }
                    // if messages in this level, but not in one up; only this level is requested
                    elseif(isset($this->_childMessages[$l]) && !isset($this->_childMessages[$l-1]))
                    {
                        return $this->_childMessages[$l];
                    }
                }
            endfor;
        }
    }


    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   11.1
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // If the context is set, assume that stateful lists are used.
        if ($this->context)
        {
            // force limit from config.xml parameters
            $limit = JFactory::getApplication()->getParams()->get('message_limit');
            $this->setState('list.limit', $limit);

            $get_limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
            $get_start = JFactory::getApplication()->input->get('start', 0, 'int');
            if(empty($this->_messageId) || !empty($get_limitstart) || !empty($get_start)) :
                // start/limitstart = 0 unless set in GET or when messageId is set
                $value = JFactory::getApplication()->input->get('limitstart', 0, 'int');
            else :
                // get messageId of top-level parent if message isn't on the top-level
                $db = $this->getDbo();
                $query = $db->getQuery(true);
                $query->select('m.id');
                $query->from('#__vitabook_messages AS m');
                $query->where('m.parent_id = 1');
                $query->where('m.lft <= (SELECT lft FROM #__vitabook_messages WHERE id = '.$this->_messageId.')');
                // if no rights: only published messages
                if(!$this->_canDo->get('core.edit.state'))
                {
                    $query->where('m.published = 1');
                }
                $query->order('m.lft DESC');
                $db->setQuery($query,0,1); // there is only one top parent for each message ;-)
                $id = $db->loadResult();

                // get page number message is on
                $query = $db->getQuery(true);
                $query->select('COUNT(id) as count');
                $query->from('#__vitabook_messages AS m');
                $query->where('m.parent_id = 1');
                $query->where('m.lft > (SELECT lft FROM #__vitabook_messages WHERE id = '.$id.')');
                // if no rights: only published messages
                if(!$this->_canDo->get('core.edit.state'))
                {
                    $query->where('m.published = 1');
                }
                $query->order('m.lft DESC');
                $db->setQuery($query);
                $value = $db->loadResult();
            endif;

            // set pagination start
            $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
            $this->setState('list.start', $limitstart);

        }
    }

    /**
     * Method to add IP addresses to the blocklist
     */
    public function blockip($ip)
    {
        // Get blocked ips
        $params = VitabookHelper::getParams();

        // Get current blocked ips (if exist) and add new one
        if(isset($params->ipblock) && !empty($params->ipblock)){
            $ips = array_map('trim', explode(',', $params->ipblock));

            if(($key = array_search($ip, $ips)) !== false){
                // Remove IP from list
                unset($ips[$key]);
                $message = 'removed';
            } else {
                // Add IP to list
                $ips[] = $ip;
                $message = 'added';
            }
        } else {
            $ips[] = $ip;
            $message = 'added';
        }
        $ips = implode(', ',$ips);
        
        // Add new ips to params object and save
        $params->ipblock = $ips;

        if(!VitabookHelper::setParams($params)) {
            return false;
        }
        
        return $message;
    }

}
