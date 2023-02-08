<?php
/**
 * @version     2.2.1
 * @package     mod_vitabook_latest_messages
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;

class mod_vitabook_latest_messagesHelper extends JObject
{
    /*
     * Check if VitaBook is installed and enabled
     */
    public static function vitabookInstalled()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('enabled');
        $query->from('#__extensions');
        $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
        $db->setQuery($query);
        $installed = $db->loadResult();
        
        if($installed && $installed == 1)
        {
            return true;
        }
        
        return false;
    }

    /*
     * Get requested number of messages ordered by date
     */
    public function getPosts($params)
    {
        $limit = (int) $params->get('number');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id, name, message, date, location, published, activated');
        $query->from('#__vitabook_messages');
        $query->where('parent_id > 0');

        if(!JFactory::getUser()->authorise('core.edit.state', 'com_vitabook')) {
            $query->where('published = 1');
            $query->where('activated = 1');
        }
        
        if(!$params->get('replies')) {
            $query->where('level = 1');
        } 
        
        $query->order('date DESC');
        $db->setQuery((string)$query, 0, $limit);   
        $result = $db->loadObjectList();
        
        if($db->getErrorNum()) {
            JError::raiseWarning(500, $db->stderr());
        }

        return $result;
    }
    
    /*
     * Shorten message if necessary
     */
    public function cutMessage(&$messages, $length)
    {
        foreach($messages as &$message)
        {
            if(strlen($message->message) > $length)
            {
                $message->message = substr($message->message, 0, $length);
                $message->message .= '...';
            }
        }
    }

    /*
     * Method to obey VitaBook's date format.
     */  
    public function formatDate(&$messages)
    {
        foreach($messages as &$message) {
            $message->date = VitabookHelper::formatDate($message);
        }
    }
    
    /*
     * Clean html and white space from message
     */    
    public function cleanMessage(&$messages)
    {
        foreach($messages as &$message) {
            $message->message = trim(strip_tags($message->message));
        }
    }
}
