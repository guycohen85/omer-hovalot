<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Items list controller class.
 */
class VitabookControllerMessages extends JControllerAdmin
{
	public function __construct($config = array())
	{
	    parent::__construct($config);
        
		$this->registerTask('activate', 'changeActivated');
		$this->registerTask('deactivate', 'changeActivated');
    }

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'message', $prefix = 'VitabookModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
    
    /**
     * Method to change activated status of a message
     *
     */
    public function changeActivated()
    {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $jinput = JFactory::getApplication()->input;
        $ids    = $jinput->get('cid', array(), 'array');
		$values = array('activate' => 1, 'deactivate' => 0);
		$task   = $this->getTask();
		$value  = JArrayHelper::getValue($values, $task, 0, 'int');     

		if (empty($ids))
		{
			throw new Exception(JText::_('COM_VITABOOK_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($ids);

			// Change the state of the records.
			if (!$model->activate($ids, $value))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				if ($value == 1)
				{
					$this->setMessage(JText::plural('COM_VITABOOK_N_ITEMS_ACTIVATED', count($ids)));
				}
				elseif ($value == 0)
				{
					$this->setMessage(JText::plural('COM_VITABOOK_N_ITEMS_DEACTIVATED', count($ids)));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_vitabook&view=messages'));        
    }
}