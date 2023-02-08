<?php
/**
 * @version		$Id: view.html.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of banners.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
jimport('joomla.application.component.view');
class JViewPairing extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
	    $model = $this->getModel();
		
		 //$config = JController::getModel('config', 'JModel'); 
		 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'config.php'); 

		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 
		 $this->data = $model->getData(); 
		$this->model = $model; 
		$this->cats = $model->getVmCats(); 
		
		
		
		
		//debug_zval_dump ($this->forms['adwordstracking']); die(); 
		parent::display($tpl);
		
	}

}
