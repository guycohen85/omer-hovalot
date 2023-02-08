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

class JControllerXmlexport extends JControllerBase
{
     function getViewName() 
	{ 
		return 'xmlexport';		
	} 

   function getModelName() 
	{		
		return 'xmlexport';
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
		$model	= $this->getModel('xmlexport');
		
		$msg = $model->store(); 
		if (empty($msg)) $msg = 'O.K.'; 

		$this->setRedirect(JRoute::_('index.php?option=com_onepage&view=xmlexport', false), $msg);
		return false;
	}

	
}
