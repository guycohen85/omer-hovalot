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

class VitabookViewImport extends JViewLegacy
{
	protected $guestbooks;
    protected $vbMessages;
    protected $vbReplies;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//-- Set toolbar and document
        $this->addToolbar();
		$this->setDocument();

        // load legacy templates if joomla version < 3.0.0
        $jversion = new JVersion();
        if(version_compare($jversion->getShortVersion(),'3.0.0','lt'))
        {
            $tpl .= "legacy";
        }
        else 
        {
            $this->sidebar = JHtmlSidebar::render();
        }
        
        $this->guestbooks = $this->get('guestbooks');
        $this->vbMessages = $this->get('VbMessages');
        $this->vbReplies = $this->get('VbResponses');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	JError::raiseError(500, implode("<br />", $errors));
        	return false;
        }
                
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
        $canDo	= VitabookHelper::getActions();
        
		JToolBarHelper::title(JText::_('COM_VITABOOK_IMPORT_TITLE'));
        
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_vitabook');
		}
	}

	/**
	 * Method to set up the document properties
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_VITABOOK_IMPORT_TITLE'));
	}
}
