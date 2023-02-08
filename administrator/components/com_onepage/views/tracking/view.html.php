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
class JViewTracking extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
	    $model = $this->getModel();
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'videohelp.php');
		 //$config = JController::getModel('config', 'JModel'); 
		 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'config.php'); 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		//var_dump($config); die(); 
		$this->statuses = $config->getOrderStatuses();
		
		$this->trackingfiles = $config->getPhpTrackingThemes(); 
		$this->forms = $model->getJforms($this->trackingfiles); 
		$this->isEnabled = $model->isEnabled(); 
		$this->tracking_order = $model->isEnabled(true); 
		$this->config = $model->getStatusConfig($this->statuses); 
		$rand_order = JRequest::getVar('show_order_vars', false); 
		$named = array(); 
		$this->orderVars = $model->getOrderVars($named); 
		$this->named = $named; 
		$this->model =& $model; 
		$this->aba_enabled = $model->getAba();
		if ($rand_order)
		  {
		    $model->showOrderVars(); 
			JFactory::getApplication()->close(); 
		  }
		//debug_zval_dump ($this->forms['adwordstracking']); die(); 
		parent::display($tpl);
		
	}

}
