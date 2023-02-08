<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

defined('_JEXEC') or die;

//jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Vitabook records.
 */
class VitabookModelImport extends JModelLegacy
{
    public $guestbooks;
    private $_legacy;
    
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Get Joomla version and set optional legacy mode
        $jversion = new JVersion();
        if(version_compare($jversion->getShortVersion(),'3.0.0','lt'))
        {
            $this->_legacy = true;
        }
        else
        {
            $this->_legacy = false;
        }
        
        $this->guestbooks = array();
    }
    
    /**
     * Method to determine which guestbooks are available to import
     */
    public function getGuestbooks()
    {
        // PhocaGuestbook changed database structure in Joomla 3
        if($this->_legacy)
        {
            $phoca = $this->getPhocaGuestbookLegacy();
        }
        else
        {
            $phoca = $this->getPhocaGuestbook();
        }
        $easybook = $this->getEasyBook();

        if(!empty($phoca))
        {
            foreach ($phoca as $book)
            {
                $book->type = "phoca";
                $this->guestbooks[] = $book;
            }
        }
        
        if(!empty($easybook))
        {
            $book = $easybook;
            $book->title = "Easybook";
            $book->type = "easybook";
            $this->guestbooks[] = $book;
        }
        
        return $this->guestbooks;
    }
    
    private function getPhocaGuestbookLegacy()
    {
        try {
             // phoca guestbook database tables present
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('b.id,CONCAT(\'Phoca Guestbook: \',b.title) AS title,count(i.id) AS number_of_messages');
            $query->from('#__phocaguestbook_items AS i');
            $query->leftjoin('#__phocaguestbook_books as b ON i.catid = b.id');
            $query->group('i.catid');
            $query->order('b.id');
            $db->setQuery($query);
            $result = $db->loadObjectList('id');
            
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function getPhocaGuestbook()
    {
        try {
            // phoca guestbook database tables present
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('b.id,CONCAT(\'Phoca Guestbook: \',b.title) AS title,count(i.id) AS number_of_messages');
            $query->from('#__phocaguestbook_items AS i');
            $query->rightjoin('#__categories as b ON i.catid = b.id');
            $query->where('b.extension = '. $db->quote('com_phocaguestbook'));
            $query->group('i.catid');
            $query->order('b.id');
            $db->setQuery($query);
            $result = $db->loadObjectList('id');
            
            return $result;
        } catch (Exception $e) {
            return false;
        }        
    }    
    
    private function getEasyBook()
    {
        try {
            // easybook database table present
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('count(id) AS number_of_messages');
            $query->from('#__easybook');
            $db->setQuery($query);
            $result = $db->loadObject();
            
            return $result;
        } catch (Exception $e) {
            return false;
        }            
    }
    

    /**
     * Wrapper method to retrieve details of a guestbook
     */
     public function getGuestbook($gb=null)
     {
        $this->getGuestbooks();

        if($gb !== null){
            return $this->guestbooks[$gb];
        }
     }


    /**
     * Method to retrieve the messages we are going to import
     * @input    guestbook object
     */
    public function getMessages($gb)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        switch ($gb->type):
            case 'phoca':
                $query->select('username AS name,email,homesite AS site,date,content AS message,ip,published');
                $query->from('#__phocaguestbook_items');
                $query->where('catid='.$gb->id);
                $query->order('date ASC');
                $db->setQuery($query);
                $result = $db->loadAssocList();
                break;
            case 'easybook':
                $query->select('gbname AS name,gbmail AS email,gbloca AS location,gbdate AS date,gbtext AS message,gbip AS ip,published');
                $query->from('#__easybook');
                $query->order('gbdate ASC');
                $db->setQuery($query);
                $result = $db->loadAssocList('date');

                // Format easybook messages into proper HTML
                $result = VitabookHelperImport::parseMessages($result);
                break;
        endswitch;

        return $result;
    }
    
    /**
     * Method to retrieve vitabook messages to merge with import messages
     */
    public function getVbMessages($vbReplies = null)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('name, email, parent_id, site, date, message, ip, published');
        $query->from('#__vitabook_messages');
        $query->order('date ASC');
        $query->where('parent_id > 0');
        if(!$vbReplies)
        {
            $query->where('level = 1');
        }

        $db->setQuery($query);
        $result = $db->loadAssocList('date');
        
        return $result;
    }
    
    /**
     * Method to retrieve vitabook response messages
     */
    public function getVbResponses()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('id');
        $query->from('#__vitabook_messages');
        $query->where('(level > 1)');
        
        $db->setQuery($query);
        $result = $db->loadAssocList();
        
        return $result;
    }    

    /**
     * Backup VitaBook messages table
     */
    public function backupVbTable()
    {
        $now = time();
        $db = $this->getDbo();
        $query = 'CREATE TABLE #__backup_vitabook_messages_'.$now.' LIKE #__vitabook_messages;';
        $db->setQuery($query);
        $db->query();
        
        $db = $this->getDbo();
        $query = 'INSERT #__backup_vitabook_messages_'.$now.' SELECT * FROM #__vitabook_messages;';
        $db->setQuery($query);
        $db->query();
        
        return true;
    }
    
    /**
     * Remove existing VitaBook messages
     */
    public function cleanVbTable()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__vitabook_messages');
        $query->where('(parent_id > 0)');        
        $db->setQuery($query);
        $db->query();
        
        return true;
    }
}