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

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class VitabookViewMessage extends JViewLegacy
{
	protected $state;
	protected $message;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$message		= $this->get('Item');
		$form			= $this->get('Form');
		$document		= JFactory::getDocument();

		//-- Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("<br />", $errors));
			return false;
		}

		//-- Assign message and form
		$this->message 	= $message;
		$this->form 	= $form;

		$this->addToolbar();

        // load legacy templates if joomla version < 3.0.0
        $jversion = new JVersion();
        if(version_compare($jversion->getShortVersion(),'3.0.0','lt')) {
            $tpl .= "legacy";
        }
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
        JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->message->id == 0);

		$canDo = VitabookHelper::getActions();

		JToolBarHelper::title($isNew ? JText::_('COM_VITABOOK_MESSAGE_TITLE_NEW') : JText::_('COM_VITABOOK_MESSAGE_TITLE_EDIT'));

		//-- Build the actions for new and existing records.
		if($isNew)
		{
			//-- For new records, check the create permission.
			if($canDo->get('vitabook.create.new') OR $canDo->get('vitabook.create.reply'))
			{
				JToolBarHelper::save('message.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('message.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if($canDo->get('core.edit') OR $canDo->get('core.edit.own'))
			{
				//-- We can save the new record
				JToolBarHelper::save('message.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('message.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
