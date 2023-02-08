<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

//-- No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class VitabookControllerImport extends JControllerForm
{
    protected $view_list = 'messages';
    protected $messages;
    protected $guestbook;
    protected $vbMessages;

	public function __construct($config = array())
	{
	    parent::__construct($config);

    }

	/**
	 * Method to import messages into vitabook
	 */
    public function import()
    {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $user = JFactory::getUser();
        if(!$user->authorise('core.admin', 'com_vitabook')) {
            $this->setMessage(JText::_('COM_VITABOOK_IMPORT_NOT_AUTHORIZED'),'error');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view='.$this->view_list, false));        
            return false;
        }

        $model = $this->getModel();

        $jinput = JFactory::getApplication()->input;
        $gb = $jinput->getInt('import',99);
        $vbImport = $jinput->getInt('vbImport',0);
        $vbReplies = $jinput->getInt('vbImportReplies',0);

        if($gb == 99)
        {
            $this->setMessage(JText::_('COM_VITABOOK_IMPORT_NO_GUESTBOOK_SELECTED'),'error');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=import', false));        
            return false;
        }
        
        $this->guestbook = $model->getGuestbook($gb);
        $this->messages = $model->getMessages($this->guestbook);
        
        // Check if table contains messages
        if(!$this->messages)
        {
            $this->setMessage(JText::_('COM_VITABOOK_IMPORT_NO_MESSAGES'),'error');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=import', false));        
            return false;        
        }
        
        // Check for existing VitaBook messages
        $this->vbMessages = $model->getVbMessages($vbReplies);
        if($this->vbMessages && $vbImport)
        {
            $this->messages = array_merge($this->messages, $this->vbMessages);
            ksort($this->messages);
        }
     
        // Clean VitaBook table and make backup, just in case
        $model->backupVbTable();
        $model->cleanVbTable();

        // Set message model
        $messageModel = $this->getModel('message');

        ini_set('max_execution_time', 999);
        foreach ($this->messages as $message)
        {
            $message['id'] = 0;
            $message['parent_id'] = 1;
            $message['activated'] = 1;

            $messageModel->save($message);
        }
        
        $this->setMessage(JText::plural('COM_VITABOOK_N_MESSAGES_IMPORTED', count($this->messages)));
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
        return true;
    }

}