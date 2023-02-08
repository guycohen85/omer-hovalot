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

/**
 * Mail helper.
 */
abstract class VitabookHelperMail
{  
    /**
    * Method to send an admin notification email
    * @input object message data
    * @return bool true on success, false on fail 
    */
    public static function sendAdminMail($data = 'default')
    {
        if($data == 'default')
            return false;
        
        $app = JFactory::getApplication();
        
        // get JMail object
        $mailer = JFactory::getMailer();
        
        // set sender from global configuration
        $mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
        // set subject
        $mailer->setSubject(JText::_('COM_VITABOOK_EMAIL_SUBJECT'));
        // set recipient
        $mailer->addRecipient($app->getCfg('mailfrom'));
        
        // get/set recipients (BCC)
        $mail_group = JComponentHelper::getParams('com_vitabook')->get('admin_mail_group');
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('email');
        $query->from('#__users');
        $query->where('id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id IN ('.implode(',',$mail_group).'))');
        $db->setQuery((string)$query);
        $recipients = $db->loadColumn();
        $mailer->addBCC($recipients);
        
        // set mail body
        $mailer->setBody(VitabookHelperMail::getAdminMailBody($data));
        
        // send the e-mail
        $mailer->Send();
    }

    /**
    * Method to generate the admin email body
    * @input array message data
    * @return string unique hash for this message id 
    */      
    public static function getAdminMailBody($data)
    {
        $url = VitabookHelper::getAbsoluteRoute('index.php?option=com_vitabook&messageId='.$data['id']).'#'.$data['id'];
        $body = JText::_('COM_VITABOOK_EMAIL_BODY_OPENING')."\n\n";
        $body .= JText::_('COM_VITABOOK_EMAIL_BODY_POSTED_BY').": ".$data['name']." (".$data['email']." / ".$data['ip'].")\n\n";
        $body .= JText::_('COM_VITABOOK_EMAIL_BODY_URL').": ".$url."\n\n";
        $body .= JText::_('COM_VITABOOK_EMAIL_BODY_MESSAGE').":\n-----\n".strip_tags($data['message'])."\n-----\n\n";
        
        if(JFactory::getApplication()->getParams()->get('guest_post_state')){
            $body .= JText::_('COM_VITABOOK_EMAIL_BODY_UNPUBLISH').":\n";
            $body .= VitabookHelper::getAbsoluteRoute('index.php?option=com_vitabook&task=message.unpublish&format=raw&messageId='.$data['id'].'&code='.VitabookHelperMail::getMailHash($data['id'],'publish'))."\n\n";
        }
        else {
            $body .= JText::_('COM_VITABOOK_EMAIL_BODY_PUBLISH').":\n";
            $body .= VitabookHelper::getAbsoluteRoute('index.php?option=com_vitabook&task=message.publish&format=raw&messageId='.$data['id'].'&code='.VitabookHelperMail::getMailHash($data['id'],'publish'))."\n\n";
        }
        
        $body .= JText::_('COM_VITABOOK_EMAIL_BODY_DELETE').":\n";
        $body .= VitabookHelper::getAbsoluteRoute('index.php?option=com_vitabook&task=message.delete&format=raw&messageId='.$data['id'].'&code='.VitabookHelperMail::getMailHash($data['id'],'delete'))."\n\n";
        
        return $body;
    }


    /**
    * Method to generate the email hash
    * @input int message id
    * @return string unique hash for this message id 
    */
    public static function getMailHash($messageId,$task)
    {
        if(!empty($messageId) && !empty($task)){
            $secret = JFactory::getConfig()->get('secret');
            return sha1($secret.(int)$messageId.(string)$task);
        }
    }    

    public static function checkMailHash($messageId,$task)
    {
        if(!empty($messageId)){
            $validHash = VitabookHelperMail::getMailHash($messageId,$task);
            
            $jinput = JFactory::getApplication()->input;
            $mailHash = (string)$jinput->getString('code');
            
            return ($validHash == $mailHash ? true : false);
        }
    }
    
    /**
    * Method to send a guest an activation email
    * @input object message data
    * @return bool true on success, false on fail 
    */
    public static function sendGuestActivationMail($data = 'default')
    {
        if($data == 'default')
            return false;

        $app = JFactory::getApplication();
        // get JMail object
        $mailer = JFactory::getMailer();
        
        // set sender from global configuration
        $mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
        // set subject
        $mailer->setSubject(JText::_('COM_VITABOOK_GUEST_ACTIVATION_EMAIL_SUBJECT'));
        // set recipient
        $mailer->addRecipient($data['email']);
        
        // set mail body
        $mailer->setBody(VitabookHelperMail::getGuestActivationMailBody($data));
        
        // send the e-mail
        $mailer->Send();
    }  

    /**
    * Method to generate the email body
    * @input array message data
    * @return string unique hash for this message id 
    */      
    public static function getGuestActivationMailBody($data)
    {     
        $body = JText::_('COM_VITABOOK_GUEST_ACTIVATION_EMAIL_BODY_GREETING').' '.$data['name'].',';
        $body .= "\n\n".JText::_('COM_VITABOOK_GUEST_ACTIVATION_EMAIL_BODY_INSTRUCTIONS')."\n\n";
        $body .= "----------------------------------------------\n".strip_tags($data['message'])."\n----------------------------------------------\n\n";
        $body .= VitabookHelper::getAbsoluteRoute('index.php?option=com_vitabook&task=message.activate&format=raw&messageId='.$data['id'].'&code='.VitabookHelperMail::getMailHash($data['id'],'activate'));
        return $body;
    }

}
