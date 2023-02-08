<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

if (!defined('JPATH_VM_PLUGINS'))
{
   if (!class_exists('VmConfig'))
   require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		 
   VmConfig::loadConfig(); 
}

if (!class_exists('vmPSPlugin')) {
   
	require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'plugins'.DS.'vmpsplugin.php');
}


class plgVmPaymentOpctracking extends vmPSPlugin
{

   // this is called as the first event after creating an order, 
   // but before any payment or shipping triggers are called
   // it pairs the cookie with the actual order 
  
  public function plgOpcOrderCreated($cart, $order)
  {
    $this->plgVmConfirmedOrder($cart, $order); 
  }
	
    
	public function plgVmConfirmedOrderOPCExcept ($except, $cart, $order)
	{
	  $this->plgVmConfirmedOrder($cart, $order); 
	}
	static $delay; 
	static $_storedOrder; 
	public function plgVmConfirmedOrder($cart, $data)
	{
	  if (!self::_check()) return; 
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	  self::_tyPageMod($data, false);  
	  
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  else define('OPCTRACKINGORDERCREATED', 1); 
	  
	  if (!is_object($data))
	  if (isset($data['details']['BT']))
	  {
	   //self::$delay = true; 
	   $order = $data['details']['BT']; 
	   self::orderCreated($order, 'P');  
	  }
	}
	
	public function plgVmOnUpdateOrderPayment(&$data,$old_order_status)
	{
	   
	  if (!self::_check()) return; 
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  else define('OPCTRACKINGORDERCREATED', 1); 
	  
	  self::orderCreated($data, $old_order_status);  
	  
	  
	}
	
	
	public function plgVmOnPaymentResponseReceived(&$html)
	{
	
	   if (empty($html)) return; 
	   if (empty(self::$_storedOrder))
	   {
	    if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}
		if (!class_exists('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}
		// PPL, iDeal, heidelpay: 
	    $order_number = JRequest::getString('on', 0);
		// eway:
		if (empty($order_number))
		 {
		   $order_number = JRequest::getString('orderid', 0);
		   if (empty($order_number))
		    {
			   //systempay
			  $order_number = JRequest::getString('order_id', 0);
			}
		 }
		$orderModel = VmModel::getModel('orders');
	    $virtuemart_order_id = (int)VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
		if (empty($virtuemart_order_id)) return;
	    self::$_storedOrder = $orderModel->getOrder($virtuemart_order_id);
	   }
	   
	   if (!empty(self::$_storedOrder))
	   $ret = self::_tyPageMod(self::$_storedOrder, false, $html); 
	   if (!empty($ret)) $html = $ret; 
	   
	}
	
	//$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertise));
	static function _tyPageMod(&$data, $afterrender=false, $html='')
	{
		
		if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
		if (empty($html))
		$html = JRequest::getVar('html', '', 'default', 'STRING', JREQUEST_ALLOWRAW);
		
		if (!empty($html))
		{
		
		   if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'thankyou.php')) 
		    {
			  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'thankyou.php'); 
			  return OPCThankYou::updateHtml($html, $data, $afterrender); 
			}
		}
		
				$user = JFactory::getUser(); 
				$id = $user->id; 
				$user = new JUser($id); 
				$session = JFactory::getSession(); 
				$session->set('user', $user); 

		
		return ''; 
	}
	
	public function plgVmOnCheckoutAdvertise($cart, &$html)
	{
	
	  // we will create hash only when cart view calls checkoutAdvertise
	  if (method_exists('plgSystemOpc', 'registerCart'))
	  plgSystemOpc::registerCart(); 
	}
	
	private function orderCreated(&$data, $old_order_status)
	{
	  
	  
	  
	  $hash2 = uniqid('opc', true); 
	  if (method_exists('JApplication', 'getHash'))
	  $hashn = JApplication::getHash('opctracking'); 
	  else $hashn = JUtility::getHash('opctracking'); 
	  $hash = JRequest::getVar($hashn, $hash2, 'COOKIE'); 
      if ($hash2 == $hash) 
	  OPCtrackingHelper::setCookie($hash); 
	  
	    
		OPCtrackingHelper::orderCreated($hash, $data, $old_order_status); 
	
		//OPC add-on: if any other plugin updates user data, they should get refreshed: 
			// refresh user data: 
				$user = JFactory::getUser(); 
				$id = $user->id; 
				$user = new JUser($id); 
				$session = JFactory::getSession(); 
				$session->set('user', $user); 
				// end of refresh
			
		 self::_tyPageMod($data, false);  
	}
	
	public function plgVmOnUpdateOrderShipment(&$data,$old_order_status)
	{
	
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  else define('OPCTRACKINGORDERCREATED', 1); 
	  
	  if (!self::_check()) return; 
	  self::orderCreated($data, $old_order_status);  
	    
	   
		
	}
	
	private static function _check()
	{
	  	$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return false;
		}
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php')) return false;
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') return false;

		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') return false; 
		
		if(version_compare(JVERSION,'3.0.0','ge')) 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'compatibilityj3.php'); 
		else
	    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'compatibilityj2.php'); 

		
		require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
		
		return true; 

	}
	
	
	
	public function onAfterRender()
	{
	  
	   
	}

		
}
