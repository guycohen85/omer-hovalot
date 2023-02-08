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

//Import filesystem libraries and component helper.
jimport('joomla.filesystem.file');
jimport('joomla.application.component.helper');

/**
 * Avatar helper.
 */
abstract class VitabookHelperAvatar
{
    public static $avatarSystems = array("COM_VITABOOK_CONFIG_AVATAR_OPTION_NO_AVATARS",
                                        "COM_VITABOOK_CONFIG_AVATAR_OPTION_VITABOOK_AVATARS",
                                        "COM_VITABOOK_CONFIG_AVATAR_OPTION_COMMUNITYBUILDER_AVATARS",
                                        "COM_VITABOOK_CONFIG_AVATAR_OPTION_KUNENA_AVATARS",
                                        "COM_VITABOOK_CONFIG_AVATAR_OPTION_JOMSOCIAL_AVATARS",
                                        "COM_VITABOOK_CONFIG_AVATAR_OPTION_GRAVATAR_AVATARS");


    /**
    * Method to get the avatar link for a message
    *   executes database query if necessary for avatar source, then proxies to getAvatarUrl method
    * @return link to avatar
    */
    public static function messageAvatar($message) {
        // Get avatar parameters
        $source = JComponentHelper::getParams('com_vitabook')->get('vbAvatar');
        // Get info from database
        if(!empty($message->jid))
        {
            switch ($source)
            {
                case 2:
                    //-- Community Builder's avatar system is used
                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true);
                    $query->select('avatar');
                    $query->from('#__comprofiler');
                    $query->where('user_id = '.$message->jid);
                    $db->setQuery((string)$query);
                    $message->avatar = $db->loadResult();
                    break;
                case 3:
                    //-- Kunena's avatar system is used
                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true);
                    $query->select('avatar');
                    $query->from('#__kunena_users');
                    $query->where('userid = '.$message->jid);
                    $db->setQuery((string)$query);
                    $message->avatar = $db->loadResult();
                    break;
                case 4:
                    //-- Jomsocial's avatar system is used
                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true);
                    $query->select('avatar');
                    $query->from('#__community_users');
                    $query->where('userid = '.$message->jid);
                    $db->setQuery((string)$query);
                    $message->avatar = $db->loadResult();
                    break;
                default:
            }
        }
        // return img url
        return VitabookHelperAvatar::getAvatarUrl($message);
    }

    /**
     * Method to set the avatar join query (if necessary for an avatar source)
     * @param   object $query   JDatabase query object
     * @return  void
     */
    public static function setAvatarQuery(&$query)
    {
        $source = JComponentHelper::getParams('com_vitabook')->get('vbAvatar');

        if($source == 2)
        {
            $query->select('cb.avatar');
            $query->leftjoin('#__comprofiler AS cb ON m.jid = cb.user_id');
        }
        elseif($source == 3)
        {
            $query->select('k.avatar');
            $query->leftjoin('#__kunena_users AS k ON m.jid = k.userid');
        }
        elseif($source == 4)
        {
            $query->select('js.avatar');
            $query->leftjoin('#__community_users AS js ON m.jid = js.userid');
        }
    }


   /**
    * Method to get the avatar src url for a message
    * @param    object  $message    VitaBook message object
    * @return   string  avatar img src url
    */
    public static function getAvatarUrl($message)
    {
        $source = JComponentHelper::getParams('com_vitabook')->get('vbAvatar');
        
        // Default avatar
        $defaultAvatar = JComponentHelper::getParams('com_vitabook')->get('defaultAvatar', 'default1.png');      

        // no avatar
        if(empty($source))
            return false;

        // return default avatar for guests unless gravatar is enabled
        if(($message->jid == 0) && ($source != 5)){
            return JUri::root().'media/com_vitabook/images/avatars/default/'.$defaultAvatar;
        }

        // determine links for supported avatar systems
        switch ($source)
        {
            case 1:
                // Vitabook built-in avatar system
                $path = JPATH_SITE.'/media/com_vitabook/images/avatars/'.$message->jid.'.png';
                if(JFile::exists($path)) {
                    //-- ?filemtime() is workaround for browser-cache
                    return JUri::root().'media/com_vitabook/images/avatars/'.$message->jid.'.png?'.filemtime($path);
                }
                break;
            case 2:
                // Community Builder's avatar system
                if(!empty($message->avatar)){
                    return JUri::root().'images/comprofiler/'.$message->avatar;
                }
                break;
            case 3:
                // Kunena's avatar system
                if(!empty($message->avatar)){
                    return JUri::root().'media/kunena/avatars/'.$message->avatar;
                }
                break;
        case 4:
                // jomsocial avatar service
                if(!empty($message->avatar)){
                    return JUri::root().$message->avatar;
                }
        break;
        case 5:
                // gravatar avatar service
                return 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($message->email))).'?d='.urlencode( JUri::root().'media/com_vitabook/images/avatars/default/'.$defaultAvatar );
                break;
            default:
                return false;
        }
        // return default avatar when no avatar was found
        return JUri::root().'media/com_vitabook/images/avatars/default/'.$defaultAvatar;
    }

   /**
    * Method to check if the avatar source is available
    * @source   avatar source
    * @return   boolean
    */    
    public static function checkAvatarSystem($source)
    {
        switch ($source)
        {
            case 0:
            case 1:
            case 5:
                return true;
            case 2:
                return self::checkDbForAvatarSystem('com_comprofiler');
            case 3:
                return self::checkDbForAvatarSystem('com_kunena');
            case 4:
                return self::checkDbForAvatarSystem('com_community');
            default:
        }
        return false;
    }

    public static function checkDbForAvatarSystem($name)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('enabled');
        $query->from('#__extensions');
        $query->where($db->quoteName('name') . ' = ' . $db->quote($name));
        $db->setQuery($query);
        $installed = $db->loadResult();

        if($installed && $installed == 1)
        {
            return true;
        }
        return false;
    }


    public static function getAvailableAvatarSystems()
    {
        $availableAvatarSystems = array();
        foreach (self::$avatarSystems as $nr => $name) {
            if(self::checkAvatarSystem($nr)){
                $availableAvatarSystems[$nr] = $name;
            }
        }
        return $availableAvatarSystems;
    }
}
