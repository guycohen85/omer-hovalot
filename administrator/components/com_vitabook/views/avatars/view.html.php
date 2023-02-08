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

class VitabookViewAvatars extends JViewLegacy
{
	protected $avatars;
	protected $pagination;
    protected $state;
    protected $user;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        $this->user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_vitabook');

        //-- Access check.
        if (!$this->user->authorise('core.edit', 'com_vitabook') OR !$this->user->authorise('core.delete', 'com_vitabook') OR $params->get('vbAvatar') != 1) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        if($tpl=='avatar')
        {
            $this->avatar = $this->get('Avatar');
        }
        elseif(JFactory::getApplication()->input->getString('layout') == 'close')
        {
			//-- close the modal box
            echo "<script type='text/javascript'>window.parent.location.href=window.parent.location.href;window.parent.SqueezeBox.close();</script>";
        }
        else
        {
            $this->state = $this->get('State');
            $this->avatars = $this->get('Items');
            $this->pagination = $this->get('Pagination');

            // load legacy templates if joomla version < 3.0.0
            $jversion = new JVersion();
            if(version_compare($jversion->getShortVersion(),'3.0.0','lt'))
            {
                // Prepare table
                $this->prepareTableLegacy();
            }
            else
            {
                // Prepare table
                $this->prepareTable();
                $this->sidebar = JHtmlSidebar::render();
            }
            
            //-- Set toolbar
            $this->addToolbar();
            //-- Set the document
            $this->setDocument();
        }

		//-- Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("<br />", $errors));
			return false;
		}
        
        // load legacy templates if joomla version < 3.0.0
        $jversion = new JVersion();
        if(version_compare($jversion->getShortVersion(),'3.0.0','lt')) {
            $tpl .= "legacy";
        }

		parent::display($tpl);
	}


	/**
	 * Add table with JGrid
     * For Joomla > 3.0
	 */
	protected function prepareTable()
	{
		//-- Implement ordering
		$this->listOrder 	= $this->escape($this->state->get('list.ordering'));
		$this->listDirn 	= $this->escape($this->state->get('list.direction'));

		jimport('joomla.html.grid');
        JHtml::_('behavior.modal', 'a.modal');

		$table = new JGrid(array('class' => 'table table-striped'));

		$table	->addColumn('checkbox')
				->addColumn('avatar')
				->addColumn('name')
                ->addColumn('username')
				->addColumn('email')
				->addColumn('id')
		;

		$table	->addRow(array(), 1)
				->setRowCell('checkbox', '<input type="checkbox" name="checkall-toggle" value="" title="'.JText::_('JGLOBAL_CHECK_ALL').'" onclick="Joomla.checkAll(this)" />', array('width' => '1%', 'class' => 'hidden-phone'))
                ->setRowCell('avatar', JText::_('COM_VITABOOK_AVATARS_THEAD_AVATAR'), array('width' => '10%'))
				->setRowCell('name', JHtml::_('grid.sort', 'COM_VITABOOK_AVATARS_THEAD_NAME', 'name', $this->listDirn, $this->listOrder), array())
                ->setRowCell('username', JHtml::_('grid.sort', 'COM_VITABOOK_AVATARS_THEAD_USERNAME', 'username', $this->listDirn, $this->listOrder), array())
				->setRowCell('email', JHtml::_('grid.sort', 'COM_VITABOOK_AVATARS_THEAD_EMAIL', 'email', $this->listDirn, $this->listOrder), array())
				->setRowCell('id', JText::_('COM_VITABOOK_AVATARS_THEAD_ID'), array('width' => '1%', 'class' => 'nowrap'))
		;

		//-- Add pagination
		$table	->addRow(array(), 2)
				->setRowCell('checkbox', $this->pagination->getListFooter(), array('colspan' => 6))
		;
		//-- Fill table
        if(!empty($this->avatars))
        {
            foreach ($this->avatars as $i => $avatar){
                    $table	->addRow(array('class' => 'row'.($i % 2)));
                    $table	->setRowCell('checkbox', JHtml::_('grid.id', $i, $avatar->id), array('class' => 'center hidden-phone'));
                    $table	->setRowCell('avatar', '<img src="'.$avatar->avatar.'" width="50px" height="50px" alt="avatar" class="img-rounded" />', array('class' => 'center'));
                    if($this->user->authorise('core.edit', 'com_vitabook')) {
                        $table	->setRowCell('name', '<a href="'.JRoute::_($avatar->url).'" title="'.JText::_('COM_VITABOOK_AVATARS_TBODY_EDIT').'" class="modal" rel="{size:{x: 600, y: 300}, handler:\'iframe\'}">'.$this->escape($avatar->name).'</a>');
                    } else {
                        $table	->setRowCell('name',$this->escape($avatar->name));
                    }
                    $table	->setRowCell('username', $this->escape($avatar->username));
                    $table	->setRowCell('email', $this->escape($avatar->email));
                    $table	->setRowCell('id', (int) $avatar->id, array('class' => 'center'));
            }
        }
		$this->table = $table;
	}
    
	/**
	 * Add table with JGrid
     * For Joomla 2.5
	 */
	protected function prepareTableLegacy()
	{
		//-- Implement ordering
		$this->listOrder 	= $this->escape($this->state->get('list.ordering'));
		$this->listDirn 	= $this->escape($this->state->get('list.direction'));

		jimport('joomla.html.grid');
        JHtml::_('behavior.modal', 'a.modal');

		$table = new JGrid(array('class' => 'adminlist'));

		$table	->addColumn('checkbox')
				->addColumn('avatar')
				->addColumn('name')
                ->addColumn('username')
				->addColumn('email')
				->addColumn('id')
		;

		$table	->addRow(array(), 1)
				->setRowCell('checkbox', '<input type="checkbox" name="checkall-toggle" value="" title="'.JText::_('JGLOBAL_CHECK_ALL').'" onclick="Joomla.checkAll(this)" />', array('width' => '1%'))
				->setRowCell('avatar', JText::_('COM_VITABOOK_AVATARS_THEAD_AVATAR'), array('width' => '10%'))
				->setRowCell('name', JHtml::_('jhtml.grid.sort', 'COM_VITABOOK_AVATARS_THEAD_NAME', 'name', $this->listDirn, $this->listOrder), array())
                ->setRowCell('username', JHtml::_('jhtml.grid.sort', 'COM_VITABOOK_AVATARS_THEAD_USERNAME', 'username', $this->listDirn, $this->listOrder), array())
				->setRowCell('email', JHtml::_('jhtml.grid.sort', 'COM_VITABOOK_AVATARS_THEAD_EMAIL', 'email', $this->listDirn, $this->listOrder), array())
				->setRowCell('id', JText::_('COM_VITABOOK_AVATARS_THEAD_ID'), array('width' => '1%', 'class' => 'nowrap'))
		;

		//-- Add pagination
		$table	->addRow(array(), 2)
				->setRowCell('checkbox', $this->pagination->getListFooter(), array('colspan' => 6))
		;
		//-- Fill table
        if(!empty($this->avatars))
        {
            foreach ($this->avatars as $i => $avatar){
                    $table	->addRow(array('class' => 'row'.($i % 2)));
                    $table	->setRowCell('checkbox', JHtml::_('grid.id', $i, $avatar->id), array('class' => 'center'));
                    $table	->setRowCell('avatar', '<img src="'.$avatar->avatar.'" width="50px" height="50px" alt="avatar" />', array('class' => 'center'));
                    if($this->user->authorise('core.edit', 'com_vitabook')) {
                        $table	->setRowCell('name', '<a href="'.JRoute::_($avatar->url).'" title="'.JText::_('COM_VITABOOK_AVATARS_TBODY_EDIT').'" class="modal" rel="{size:{x: 500, y: 250}, handler:\'iframe\'}">'.$this->escape($avatar->name).'</a>');
                    } else {
                        $table	->setRowCell('name',$this->escape($avatar->name));
                    }
                    $table	->setRowCell('username', $this->escape($avatar->username));
                    $table	->setRowCell('email', $this->escape($avatar->email));
                    $table	->setRowCell('id', (int) $avatar->id, array('class' => 'center'));
            }
        }
		$this->table = $table;
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo	= VitabookHelper::getActions();

		JToolBarHelper::title(JText::_('COM_VITABOOK_AVATARS_TITLE'), 'items.png');

        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'avatar.deleteList', 'JTOOLBAR_DELETE');
        }
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_vitabook');
		}
	}

	/**
	 * Method to set up the document properties
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_VITABOOK_AVATARS_TITLE'));
	}
}
