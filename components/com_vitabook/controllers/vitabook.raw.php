<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Vitabook controller class.
 */
class VitabookControllerVitabook extends JControllerForm
{
	public function __construct($config = array())
	{
	    parent::__construct($config);

	    // Set the option as com_NameOfController.
	    if (empty($this->option))
	    {
	        $this->option = 'com_vitabook';
	    }
	}

	/**
	 * Method to retrieve a group of messages.
	 * @return	JController		This object to support chaining.
	 */
	public function getMessages()
	{
    	// Check for request forgeries.
        Jsession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get/Create the view
		$view = $this->getView('Vitabook', 'html');

		// Get/Create the models
		$view->setModel($this->getModel('vitabook'), true);

	    // we only want messages, no joomla
        JFactory::getApplication()->input->set('tmpl', 'component');

		// Display the view
		$view->display('messages');
		return $this;
	}

    /**
     * Function to block ip address
     */
    public function blockip()
    {
        JSession::checkToken();
        $user = JFactory::getUser();
        if ($user->authorise('core.manage', 'com_vitabook'))
        {
            $messageIp = JFactory::getApplication()->input->get('ip', null, 'cmd');
            $messageId = JFactory::getApplication()->input->get('messageId', null, 'int');

            // block ip from sender of message
            $res = $this->getModel()->blockip($messageIp);
            if($res == 'added') {
                $result = 'COM_VITABOOK_IP_BLOCK_SUCCES';
            } elseif($res == 'removed') {
                $result = 'COM_VITABOOK_IP_UNBLOCK_SUCCES';                
            } else {
                $result = 'COM_VITABOOK_IP_BLOCK_FAILED';
            }

            $this->setRedirect(JRoute::_('index.php?option=com_vitabook&messageId='.$messageId).'#vb'.$messageId, JText::_($result));
            $this->redirect();
        }
        else
        {
            $this->setRedirect(JRoute::_('index.php?option=com_vitabook'), JText::_('COM_VITABOOK_IP_BLOCK_NOT_ALLOWED'));
            $this->redirect();            
        }
    }
    
}
