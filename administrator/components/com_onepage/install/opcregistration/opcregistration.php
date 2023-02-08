<?php
/**
 * @version		$Id: sef.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/*
if(version_compare(JVERSION,'3.0.0','ge')) {
  if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
  JLoader::register('JDate', JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'joomla3'.DS.'date.php'); 
 // require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'joomla3'.DS.'date.php'); 
// Joomla! 1.7 code here
}
*/

/**
 * Joomla! SEF Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.sef
 */
//require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
class plgSystemOpcregistration extends JPlugin
{
    public function onAfterRoute() {
	
	if (!file_exists(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'plugin.php')) return;
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'pluginregistration.php'); 
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'plugin.php'); 
	
	if (!OPCplugin::checkLoad()) return; 
	 
	 $option = JRequest::getVar('option', ''); 
	 
	 if ($option != 'com_virtuemart') return; 
	 $view = JRequest::getVar('view', ''); 
	 if (($view != 'user') && ($view != 'opcuser')) return;
	 
	 $task = JRequest::getVar('task', ''); 
	 
	 $controller = JRequest::getVar('controller', ''); 
	 if (($controller =='opc') && ($view == 'opcuser'))
	    {
		
		 JRequest::setVar('view', 'opc'); 
		 
	     if (strpos($controller, '..')!==false) die('?'); 
	     require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
		 
		 //OPC checkout loads the user page for editaddresscheckout, registerCheckoutUser, saveCheckoutUser
		 $allowed = array('saveUser', 'display', 'editAddressST', 'editAddressCart',  'saveCartUser', 'registerCartuser', 'saveUser', 'saveAddressST',  'cancelCartUser', 'cancelCheckoutUser', 'cancel', 'removeAddressST'); 
		 if (in_array($task, $allowed))
		   {
		     JRequest::setVar('task_original', $task); 
			 JRequest::setVar('task', 'opcregister');
			 
		   }
		 
	    }
	 
	 
	 // check OPC loaded
	 //if (!defined('JPATH_OPC')) return; 
	 // check OPC cart includes
	 
	 $ign = array('editaddresscheckout', 'pluginUserPaymentCancel', 'opc', 'cart');
	 if (in_array($task, $ign)) return; 
	 
	 // check includes that we do not need: 
	 $ign_layout = array('login', 'mailregisteruser', 'mail_html_reguser', 'mail_html_regvendor', 'mail_raw_reguser', 'mail_raw_regvendor', 'edit_vendor', 'edit_orderlist');
	 
	 $layout = JRequest::getVar('layout', 'default'); 
	 if (in_array($layout, $ign_layout)) return; 
	 
	 
	 if (!class_exists('VirtueMartViewUser'))
	 require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'virtuemart.user.registration.view.html.php'); 
	 
	 
	 
	

	
	}
	
	public function plgVmOnMainController($_controller)
	{
	   $arr = array('user', 'registration'); 
	   if (in_array($_controller, $arr))
	   {
	   $isopc = JRequest::getVar('opcregistration', false); 
	   if (!empty($isopc))
	    {
		  
		  JRequest::setVar('task', 'opcregister'); 
		  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
		  $opc = new VirtueMartControllerOpc(); 
		  $msg = $opc->opcregister(); 
		  $opc->setRedirect(JRoute::_( 'index.php?option=com_virtuemart&view=user',false),$msg);
		  
		  $app = JFactory::getApplication(); 
		  $app->close(); 
		  
		  
		}
	  }
	}
	
	
	
	
	
	
	public function onAfterRender()
	{

		return true;
	}
	
	// triggered from: \administrator\components\com_virtuemart\models\orders.php
	public function plgVmOnUserOrder(&$_orderData)
	{
		
	}
	function plgVmOnUserStore(&$data)
	{
	  
	  //if ((empty($data['username'])) && (!empty($data['email']))) $data['username'] = $data['email']; 
	}
	
	
	
}
