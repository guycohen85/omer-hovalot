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

//jimport('joomla.application.component.helper');

// Include helpers
require_once (dirname(__FILE__).'/helper.php');

// Check if VitaBook is installed and enabled
if(!mod_vitabook_latest_messagesHelper::vitabookInstalled())
{
    echo JText::_('MOD_VITABOOK_LM_NOCOMP');
}
else
{
    require_once(JPATH_ADMINISTRATOR . '/components/com_vitabook/helpers/vitabook.php');
    
    // Get params
    $messagelink = $params->get('messagelink');
    $rounded = $params->get('rounded');
    $showdate = $params->get('showdate');
	$showlocation = $params->get('showlocation');
    $readmore = $params->get('readmore');
    $nameColor = $params->get('name_color');
    $messageColor = $params->get('message_color');
    $dateColor = $params->get('date_color');
    $backgroundColor = $params->get('background_color');
    
    $start = new mod_vitabook_latest_messagesHelper;
    $messages = $start->getPosts($params);
    
    if(!empty($messages))
    {
        // Clean html and white space
        $start->cleanMessage($messages);
        
        // Obey VitaBook's date format 
        $start->formatDate($messages);
        
        if($params->get('length') != 0)
        {
            $start->cutMessage($messages, (int) $params->get('length'));
        }

        // Load necessary css files
        $jversion = new JVersion();
        if( version_compare( $jversion->getShortVersion(), '3.0.0', 'lt' ) )
        {
            JHTML::stylesheet('modules/mod_vitabook_latest_messages/assets/vitabook_lm_legacy.css');
        }
        else
        {
            JHTML::stylesheet('modules/mod_vitabook_latest_messages/assets/vitabook_lm.css');
        }
        
        if($rounded)
        {
            JHTML::stylesheet('modules/mod_vitabook_latest_messages/assets/vitabook_lm_rounded.css');
        }
        
        $style = '
            div.vb_lm_name{color:#'.$nameColor.';}
            div.vb_lm_text{color:#'.$messageColor.';}
            div.vb_lm_date{color:#'.$dateColor.';}
            div.vb_lm_message{background-color:#'.$backgroundColor.';}
            div.vb_lm_messageUnpublished{background-color:#ffe7d7;}
        ';
        
        // add to html head
        JFactory::getDocument()->addStyleDeclaration( $style );
    
        require(JModuleHelper::getLayoutPath('mod_vitabook_latest_messages'));
    }
    else
    {
        echo JText::_('MOD_VITABOOK_LM_NOMESSAGES');
    }
}
