<?php
/**
 * @version		$Id: contact.php 21555 2011-06-17 14:39:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerPickup extends JControllerBase
{
     function getViewName() 
	{ 
		return 'pickup';		
	} 

   function getModelName() 
	{		
		return 'pickup';
	}

    public function save()
	{
	  return $this->apply(); 
	}
	public function apply()
	{

		// Check for request forgeries.
		

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('pickup');
		$msg = $model->store(); 


		$this->setRedirect(JRoute::_('index.php?option=com_onepage&view=pickup', false), $msg);
		return false;
	}

	
}
