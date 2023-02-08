<?php
/**
 * Overrided portion of cart class for OPC2 on Virtuemart 2
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 *
 */

 // Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 

class OPCcheckout extends VirtueMartCart
{
     //public static $_triesValidateCoupon;
    var $cartProductsData = array();
	//public static $_triesValidateCoupon = 0;
	
	var $useSSL = 1;
	
	 var $prices = null;
	 var $pricesUnformatted = null;
	 var $pricesCurrency = null;
	 public static $opc_cart = null; 
     function __construct(&$cart) {
	 
		//self::$_triesValidateCoupon=0;
		self::$opc_cart =& $cart;
		foreach ($cart as $key => &$val)
		 {
		   $this->{$key} = $val; 
		 }
		 
		return; 
	    
		
		
	}
	function checkoutData(&$cart, $cartClass) {
	
		$this->_redirect = false;
		$this->_inCheckOut = true;
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		$cart->_inCheckOut = true;
		
		if (!isset($cart->tosAccepted)) $cart->tosAccepted = 1; 
		$cart->tosAccepted = JRequest::getInt('tosAccepted', $cart->tosAccepted);
		
		if (!isset($cart->customer_comment)) $cart->customer_comment = ''; 
		
		$cart->customer_comment = JRequest::getVar('customer_comment', $cart->customer_comment);
		if (empty($cart->customer_comment))
		{
		  $cart->customer_comment = JRequest::getVar('customer_note', $cart->customer_comment);
		}
		
		$op_disable_shipto = OPCloader::getShiptoEnabled($cart); 
		if (empty($op_disable_shipto))
		{
			$shipto = JRequest::getVar('shipto', null); 
			
		if ($shipto != 'new')
		if (($cart->selected_shipto = $shipto) !== null) {
			//JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
			$userModel = OPCmini::getModel('user'); //JModel::getInstance('user', 'VirtueMartModel');
			$stData = $userModel->getUserAddressList(0, 'ST', $cart->selected_shipto);
			if (isset($stData[0]))
			$this->validateUserData('ST', $stData[0], $cart);
		}
		}
		else
		{
		    $cart->STsameAsBT = 1;
			$cart->ST = $cart->BT;
		}
		
		$cart->setCartIntoSession();

		$mainframe = JFactory::getApplication();
		
		if (isset($cart->cartProductsData))
		$count = count($cart->cartProductsData); 
		else $count = count($cart->products); 
		
		if ($count == 0) {
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart', false), JText::_('COM_VIRTUEMART_CART_NO_PRODUCT'));
		} else {
			foreach ($cart->products as $product) {
				$redirectMsg = $this->checkForQuantities($product, $product->quantity);
				if (!$redirectMsg) {
					//					$this->setCartIntoSession();
					
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
				}
			}
		}

		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		// Check if a minimun purchase value is set
		if (($msg = $this->checkPurchaseValue()) != null) {
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $msg);
		}
		
		//But we check the data again to be sure
		if (empty($cart->BT)) {
		
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)) );
		} else {
			$redirectMsg = $this->validateUserData('BT', null, $cart);
			if ($redirectMsg) {
		
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
			}
		}
	
		if($cart->STsameAsBT!==0){
			$cart->ST = $cart->BT;
		} else {
			//Only when there is an ST data, test if all necessary fields are filled
			if (!empty($cart->ST)) {
				$redirectMsg = $this->validateUserData('ST', null, $cart);
			
				if ($redirectMsg) {
					//				$cart->setCartIntoSession();
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
				}
			}
		}


		// Test Coupon
		$shipment = $cart->virtuemart_shipmentmethod_id; 
		$payment = $cart->virtuemart_paymentmethod_id; 
		
		

		//2.0.144: $prices = $cartClass->getCartPrices();
		
		
		$cart->virtuemart_shipmentmethod_id = $shipment; 
		$cart->virtuemart_paymentmethod_id = $payment; 
		
		//2.0.144 added
		if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
		$calc = calculationHelper::getInstance();
		
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			 $calc->setCartPrices(array()); 
			}
			
		//  $cart->pricesUnformatted = $prices = $calc->getCheckoutPrices(  $cart, false, 'opc');
		$cart->pricesUnformatted = $prices = OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc' ); //$calc->getCheckoutPrices(  $cart, false, 'opc');
		if (!empty($prices['billTotal']))
		{
		 // special case for zero value orders, do not charge payment fee: 
		 if ($prices['billTotal'] == $prices['paymentValue'])
		  {
		   $savedp = $cart->virtuemart_paymentmethod_id; 
		   $cart->virtuemart_paymentmethod_id = 0; 
		   if (method_exists($calc, 'getCheckoutPricesOPC'))
	       {
	         $prices = $calc->getCheckoutPricesOPC(  $cart, false );
	   
	       }
	       else
	       $prices = OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc' );
		 
		 
		   $cart->virtuemart_paymentmethod_id = 0;  
		 }
		 
		}
		
		
		//2.0.144:end
		
		

		if (!empty($cart->couponCode)) {
			
			if (!class_exists('CouponHelper')) {
				require(JPATH_VM_SITE . DS . 'helpers' . DS . 'coupon.php');
			}
			
			$redirectMsg2 = CouponHelper::ValidateCouponCode($cart->couponCode, $prices['salesPrice']);
			/*
			stAn: OPC will not redirect the customer due to incorrect coupons here
			if (!empty($redirectMsg)) {
				$cart->couponCode = '';
				//				$this->setCartIntoSession();
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
			}
			*/
		}

		//Test Shipment and show shipment plugin
	    $op_disable_shipping = OPCloader::getShippingEnabled($cart); 
		if (empty($op_disable_shipping))
		{
		if (empty($cart->virtuemart_shipmentmethod_id)) {
			$redirectMsg = JText::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
		} else {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			//Add a hook here for other shipment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataShipment', array(  $cart));

			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesful; nothing else to do
				} elseif ($retVal === false) {
					// Missing data, ask for it (again)
					$redirectMsg = 'OPC2: '.JText::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
					// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
		}
		}
	 
		//echo 'hier ';
		//Test Payment and show payment plugin

		$total = (float)$prices['billTotal']; 
		
		
		
		if ($total > 0)
		{
		if (empty($cart->virtuemart_paymentmethod_id)) {
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
		} else {
			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmpayment');
			//Add a hook here for other payment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			
			
			
			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataPayment', array( $cart));

		$session = JFactory::getSession ();
		$sessionKlarna = $session->get ('Klarna', 0, 'vm');
		
			
			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesful; nothing else to do
				} elseif ($retVal === false) {
				
				$msg = JFactory::getSession()->get('application.queue');; 
				$msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
		        $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 
				
				$res = array_merge($msg, $msgg1, $msgg2);
				$msg = $res; 
				
				if (!empty($msg) && (is_array($msg)))
				$redirectMsg = implode('<br />', $msg); 
					
					
					
					// Missing data, ask for it (again)
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $redirectMsg);
					// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
		}
		}
		else
		$cart->virtuemart_paymentmethod_id = 0; 

		if (VmConfig::get('agree_to_tos_onorder', 1))
		{
		if (empty($cart->tosAccepted)) {
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 				
			$userFieldsModel = OPCmini::getModel('Userfields'); 
			$required = $userFieldsModel->getIfRequired('agreed');
			if(!empty($required)){
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS'));
			}
		}
		}
		/* stAn: 2.0.231: registered does not mean logged in, therefore we are going to disable this option with opc, so normal registration would still work when activation is enabled 
		if (empty($GLOBALS['is_dup']))
		if(VmConfig::get('oncheckout_only_registered',0)) {
			$currentUser = JFactory::getUser();
			if(empty($currentUser->id)){
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', false, VmConfig::get('useSSL', false)), JText::_('COM_VIRTUEMART_CART_ONLY_REGISTERED') );
			}
		 }
		 */


		//Show cart and checkout data overview
		
		$cart->_inCheckOut = false;
		$cart->_dataValidated = true;

		$cart->setCartIntoSession();

		return true;
	}
	
	
	function doCurl($order)
	{
		
	 if (!function_exists('curl_multi_exec')) return; 
	 $ch = array(); 
	  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	  if (!empty($curl_url) && (is_array($curl_url)))
	   {
	     $i = 0; 
	     foreach ($curl_url as $blink)
		  {
		   $i++; 
		   
		    $link = @base64_decode($blink); 
			if (strpos($link, 'http')===0)
			 {
			   	if (!function_exists('curl_init'))
				 return; 
		
			if (isset($order->email))
			$link = str_replace('{email}', $order->email, $link); 
			
			if (isset($order->first_name))
			$link = str_replace('{first_name}', $order->first_name, $link); 
			
			if (isset($order->last_name))
			$link = str_replace('{last_name}', $order->last_name, $link); 
			
			if (isset($order->virtuemart_order_id))
			$link = str_replace('{order_id}', $order->virtuemart_order_id, $link); 
			
			foreach ($order as $key=>$search)
			{
			  if (is_string($search))
			  $link = str_replace('{'.$key.'}', $search, $link); 
			}
			
			//$link = str_replace('{amount}', $order['details']->
			
		
			// http://arguments.callee.info/2010/02/21/multiple-curl-requests-with-php/
		
		    $ch[$i] = null; 
			$ch[$i] = curl_init($link); 
			$url = $link;
			curl_setopt ($ch[$i], CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, 0); 
			curl_setopt($ch[$i], CURLOPT_URL,$url); // set url to post to
			curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER,1); // return into a variable
			curl_setopt($ch[$i], CURLOPT_TIMEOUT, 4000); // times out after 4s
			curl_setopt($ch[$i], CURLOPT_POST, 0); 
			curl_setopt($ch[$i], CURLOPT_ENCODING , "gzip");
			curl_setopt($ch[$i], CURLOPT_CUSTOMREQUEST, 'GET');
		  }
		  }
		
		  $mh = curl_multi_init();
		  if (!empty($ch))
		  foreach ($ch as $key=>$v)
		   {
		     // build the multi-curl handle, adding both $ch
			curl_multi_add_handle($mh, $ch[$key]);
		   }
		   
		   // execute all queries simultaneously, and continue when all are complete
			$running = null;
			$start = microtime(true); 
			
			do {
			
				curl_multi_exec($mh, $running);
				$now = microtime(true); 
				if (($now-$start) > ($adwords_timeout / 1000)) 
				 {
				 $running = false;
				 break 1; 
				 }
				
			} while ($running);
		  
		 

	

			 }
		  
	   
	}
	
    function getEscaped(&$dbc, $string)
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  return $dbc->escape($string); 
	  else return $dbc->getEscaped($string);  
	}
	
	private function getModifiedData(&$cart, $restore=false)
	{
		static $saved_cart_data; 
		
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 	
		if (!$restore)
		if (!empty($custom_rendering_fields))
		{
			if ($opc_cr_type == 'save_none')
			{
				foreach ($custom_rendering_fields as $fname)
				{
					if (isset($cart->BT[$fname])) {
					
					if (!isset($saved_cart_data['BT'])) $saved_cart_data['BT'] = array(); 
					$saved_cart_data['BT'][$fname] = $cart->BT[$fname]; 
					
					
					$cart->BT[$fname] = ''; 
					}
					if (!empty($cart->ST))
					{
					 if (isset($cart->ST['shipto_'.$fname])) 
					 {
					  if (!isset($saved_cart_data['ST'])) $saved_cart_data['ST'] = array(); 
					  $saved_cart_data['ST']['shipto_'.$fname] = $cart->ST['shipto_'.$fname]; 
					  $cart->ST['shipto_'.$fname] = ''; 
					 }
					 if (isset($cart->ST[$fname])) 
					  {
					  if (!isset($saved_cart_data['ST'])) $saved_cart_data['ST'] = array(); 
					  $saved_cart_data['ST'][$fname] = $cart->ST[$fname]; 
					  $cart->ST[$fname] = ''; 
					  }
					}
					
				}
			}
			else return;
		}
		else return; 
		if ($restore)
		{
			
			if (!empty($custom_rendering_fields))
			{
			if ($opc_cr_type == 'save_none')
			{
				if (empty($saved_cart_data)) return;
				if (!empty($saved_cart_data['ST']))
				foreach ($saved_cart_data['ST'] as $fname=>$val)
				{
					$cart->ST[$fname] = $val; 
				}
				if (!empty($saved_cart_data['BT']))
				foreach ($saved_cart_data['BT'] as $fname=>$val)
				{
					$cart->BT[$fname] = $val; 
					
					
				}

			}
			}
		}
	}
	function getModifiedOrder(&$order, &$cart)
	{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 	
		if (!empty($custom_rendering_fields))
		{
			if ($opc_cr_type == 'save_none')
			{
				
				foreach ($custom_rendering_fields as $fname)
				{
					$order['details']['BT']->$fname = $cart->BT[$fname]; 
					if (!empty($order['details']['ST']))
					if (!empty($cart->ST))
					{
					 if (isset($cart->ST['shipto_'.$fname]))
					 if (isset($order['details']['ST']))
					 if (isset($order['details']['ST']->${'shipto_'.$fname})) $order['details']['ST']->${'shipto_'.$fname} = $cart->ST['shipto_'.$fname];
				     if (isset($cart->ST[$fname]))
					 if (isset($order['details']['ST']))
					 if (isset($order['details']['ST']->$fname)) 
					 $order['details']['ST']->$fname = $cart->ST[$fname]; 
					}
					
				}
			}
			else return;
		}
		else return; 
		
	}
	/**
	 * This function is called, when the order is confirmed by the shopper.
	 *
	 * Here are the last checks done by payment plugins.
	 * The mails are created and send to vendor and shopper
	 * will show the orderdone page (thank you page)
	 *
	 */
	function confirmedOrder(&$cart, $ref, &$order) {
	
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		
		//Just to prevent direct call
		if ($cart->_dataValidated && $cart->_confirmDone) {
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
			$orderModel = OPCmini::getModel('Orders'); 
			

			$this->getModifiedData($cart); 
			
			if (!empty($delivery_date))
			 {
			   $cart->delivery_date = $delivery_date; 
			 }
			 
			  
			$orderID = $orderModel->createOrderFromCart($cart);
			$this->getModifiedData($cart, true); 
			
			$msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
		    $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 

			
			$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
			if ($op_disable_shipping)
			{
			  //$q = 'update #__virtuemart_orders set 
			}
			
			
			if (empty($orderID)) {
				$mainframe = JFactory::getApplication();
				
				
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)) );
			}
			
			$cart->virtuemart_order_id = $orderID;
			$order= $orderModel->getOrder($orderID);
			
			
			$this->getModifiedOrder($order, $cart); 
			
			
			// $GLOBALS['is_dup']
			if (!empty($orderID))
			{
			
			if (!empty($GLOBALS['is_dup']) && (is_numeric($GLOBALS['is_dup'])))
			{
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_orders SET virtuemart_user_id = '".$this->getEscaped($dbj, $GLOBALS['is_dup'])."' where virtuemart_order_id = '".$this->getEscaped($dbj, $orderID)."' limit 1"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 
			  
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_order_userinfos SET virtuemart_user_id = '".$this->getEscaped($dbj, $GLOBALS['is_dup'])."' where virtuemart_order_id = '".$this->getEscaped($dbj, $orderID)."' limit 2"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 

			  
			  $e = $dbj->getErrorMsg(); 
			 
			}
			else
			if (!empty($GLOBALS['opc_new_user']) && (is_numeric($GLOBALS['opc_new_user'])))
			{
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_orders SET virtuemart_user_id = '".$this->getEscaped($dbj, $GLOBALS['opc_new_user'])."' where virtuemart_order_id = '".$this->getEscaped($dbj, $orderID)."' limit 1"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 
			  
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_order_userinfos SET virtuemart_user_id = '".$this->getEscaped($dbj, $GLOBALS['opc_new_user'])."' where virtuemart_order_id = '".$this->getEscaped($dbj, $orderID)."' limit 2"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 

			  
			  $e = $dbj->getErrorMsg(); 
			 
			}

			
			
			}
			//opc_new_user
			
			if (empty($order['details']['ST']->email) && (!empty($order['details']['BT']->email))) $order['details']['ST']->email = $order['details']['BT']->email;
// 			$cart = $this->getCart();
			
			if (isset($order['details']['BT']))
			$this->doCurl($order['details']['BT']); 
			
			$dispatcher =& JDispatcher::getInstance();
// 			$html="";
			
			if ( $order['details']['BT']->order_total <= 0) $no_payment = true; 
			else $no_payment = false; 
			
			
			
			if (empty($op_disable_shipping))	
			JPluginHelper::importPlugin('vmshipment');
			JPluginHelper::importPlugin('vmcustom');
			if (empty($no_payment))
			JPluginHelper::importPlugin('vmpayment');

		
			JPluginHelper::importPlugin('vmcustom');
			JPluginHelper::importPlugin('vmcalculation');
		
			$session = JFactory::getSession();
			$return_context = $session->getId();
			
				
			
		
			//end OPC email mod
			
			ob_start(); 
			
			
		   $order= $orderModel->getOrder($orderID);
			
			if ($order['details']['BT']->order_status != $zero_total_status)
			if ( $order['details']['BT']->order_total <= 0) { 
				require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
				$modelOrder = OPCmini::getModel('orders');
				$order['order_status'] = $zero_total_status;
				$order['customer_notified'] = 1;
				$order['comments'] = '';
				$modelOrder->updateStatusForOneOrder($orderID, $order, true);
                //$order['paymentName']= $dbValues['payment_name'];
                //We delete the old stuff
				$cart->emptyCart();
			}
			$output = ob_get_clean(); 
			
			//$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));
			
			// pairs the cookie with the database 
			$returnValues = $dispatcher->trigger('plgOpcOrderCreated', array($cart, $order ));
			
			// runs shipping confirm as first and payment as last
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPC', array('shipment', $cart, $order));
			
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPC', array('calculation', $cart, $order));
			
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPC', array('custom', $cart, $order));
			
			$except = array('shipment', 'custom', 'calculation'); 
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPCExcept', array($except, $cart, $order));			
			
			
			//OPC: maybe we want to send emil before a redirect: 
			if (!empty($send_pending_mail))
			{
			
			if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');

		//Important, the data of the order update mails, payments and invoice should
		//always be in the database, so using getOrder is the right method
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$orderModel=OPCmini::getModel('orders');
		//$order = $orderModel->getOrder($orderID);

		$payment_name = $shipment_name='';
		$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
		
		$msgqx1 = JFactory::getApplication()->get('messageQueue', array()); 
		$msgqx2 = JFactory::getApplication()->get('_messageQueue', array()); 
		
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		if (empty($op_disable_shipping))
		JPluginHelper::importPlugin('vmshipment');
		if (empty($no_payment))
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		if (empty($op_disable_shipping))
		$returnValues = $dispatcher->trigger('plgVmonShowOrderPrintShipment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_shipmentmethod_id, &$shipment_name));
		if (empty($no_payment))
		$returnValues = $dispatcher->trigger('plgVmonShowOrderPrintPayment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_paymentmethod_id, &$payment_name));
		
		$order['shipmentName']=$shipment_name;
		if (empty($no_payment))
		$order['paymentName']=$payment_name; 
		else $order['paymentName']='';

		$vars['orderDetails']=$order;
		if (!isset($vars['newOrderData'])) $vars['newOrderData'] = array(); 
	    $vars['newOrderData']['customer_notified']=1;

	
		$vars['url'] = 'url';
		
		$vars['doVendor'] = false;
		
		if (!empty($order['details']['BT']->virtuemart_vendor_id))
		$virtuemart_vendor_id = $order['details']['BT']->virtuemart_vendor_id; 
		else
		$virtuemart_vendor_id=1;

		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$vendorModel = OPCmini::getModel('vendor');
		$vendor = $vendorModel->getVendor($virtuemart_vendor_id);
		$vars['vendor'] = $vendor;
		$vendorEmail = $vendorModel->getVendorEmail($virtuemart_vendor_id);
		
		if (empty($vendorEmail)) 
		 {
		     $db = JFactory::getDBO ();
			
			  $query = 'SELECT * FROM `#__virtuemart_vmusers`'; 
			  $db->setQuery($query); 
			  $res = $db->loadAssocList(); 
					
						
			$query = 'SELECT ju.email FROM `#__virtuemart_vmusers` as vmu, `#__users` as ju WHERE `virtuemart_vendor_id`=' . (int)$virtuemart_vendor_id.' and ju.id = vmu.virtuemart_user_id and vmu.user_is_vendor = 1 limit 0,1';
			$db->setQuery ($query);
			$vendorEmail = $db->loadResult ();

		   
		   
		 }
		
		
		$vars['vendorEmail'] = $vendorEmail;

		

		// Send the email
		if (file_exists(JPATH_VM_SITE.DS.'views'.DS.'invoice'.DS.'view.html.php'))
		{
			
		  if (OPCcheckout::renderMail('invoice', $vendorEmail, $vars, null, true, false)) {
		   //ok
		  }
		
		$x = JFactory::getApplication()->set('messageQueue', $msgqx1); 
	    $x = JFactory::getApplication()->set('_messageQueue', $msgqx2); 

		
		
		
		}
			
			}
			//$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), null, 'string', JREQUEST_ALLOWRAW); 
			$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
			$app = JFactory::getApplication(); 
			if (method_exists($app, 'input'))
			$html2 = $app->input->get('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'RAW' ); 
			else 
			$html2 = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
			
			if ($html != $html2) $output .= $html2; 
			$output .= $html; 	


			ob_start(); 
			if ( $order['details']['BT']->order_total <= 0) { 
				$cart->emptyCart();
			}
			$output .= ob_get_clean(); 
			
			$x = JFactory::getApplication()->set('messageQueue', $msgq1); 
			$x = JFactory::getApplication()->set('_messageQueue', $msgq2); 

			
			if (!empty($output))
			return $output; 
			
			
			// may be redirect is done by the payment plugin (eg: paypal)
			// if payment plugin echos a form, false = nothing happen, true= echo form ,
			// 1 = cart should be emptied, 0 cart should not be emptied

		
	 }

	}
	/** Checks if the quantity is correct
	 *
	 * @author Max Milbers
	 */
	 function checkForQuantities($product, &$quantity=0,&$errorMsg ='') {

		$stockhandle = VmConfig::get('stockhandle','none');
		$mainframe = JFactory::getApplication();
		// Check for a valid quantity
		if (!is_numeric( $quantity)) {
			$errorMsg = JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			//			$this->_error[] = 'Quantity was not a number';
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}
		// Check for negative quantity
		if ($quantity < 1) {
			//			$this->_error[] = 'Quantity under zero';
			$errorMsg = JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		// Check to see if checking stock quantity
		if ($stockhandle!='none' && $stockhandle!='risetime') {

			$productsleft = $product->product_in_stock - $product->product_ordered;
			// TODO $productsleft = $product->product_in_stock - $product->product_ordered - $quantityincart ;
			if ($quantity > $productsleft ){
				if($productsleft>0 and $stockhandle='disableadd'){
					$quantity = $productsleft;
					$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY',$quantity);
					$this->setError($errorMsg);
					vmInfo($errorMsg,$product->product_name);
					// $mainframe->enqueueMessage($errorMsg);
				} else {
					$errorMsg = JText::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					$this->setError($errorMsg); // Private error retrieved with getError is used only by addJS, so only the latest is fine
					vmInfo($errorMsg,$product->product_name,$productsleft);
					// $mainframe->enqueueMessage($errorMsg);
					return false;
				}
			}
		}

		// Check for the minimum and maximum quantities
		$min = $product->min_order_level;
		$max = $product->max_order_level;
		if ($min != 0 && $quantity < $min) {
			//			$this->_error[] = 'Quantity reached not minimum';
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_MIN_ORDER', $min);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}
		if ($max != 0 && $quantity > $max) {
			//			$this->_error[] = 'Quantity reached over maximum';
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_MAX_ORDER', $max);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		return true;
	}
	
	private static function renderMail ($viewName, $recipient, $vars = array(), $controllerName = NULL, $noVendorMail = FALSE,$useDefault=true) {
	   	if(!class_exists( 'VirtueMartControllerVirtuemart' )) require(JPATH_VM_SITE.DS.'controllers'.DS.'virtuemart.php');

		$controller = new VirtueMartControllerVirtuemart();
		
		$controller->addViewPath( JPATH_VM_SITE.DS.'views' );

		$view = $controller->getView( $viewName, 'html' );
		if(!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst( $controllerName );
		if(!class_exists( $controllerClassName )) require(JPATH_VM_SITE.DS.'controllers'.DS.$controllerName.'.php');

	
		$view->addTemplatePath( JPATH_VM_SITE.'/views/'.$viewName.'/tmpl' );

		$vmtemplate = VmConfig::get( 'vmtemplate', 'default' );
		if($vmtemplate == 'default') {
			if(JVM_VERSION >= 2) {
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery( $q );
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template) {
			$view->addTemplatePath( JPATH_ROOT.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.$viewName );
		} 

		foreach( $vars as $key => $val ) {
			$view->$key = $val;
		}

		$user = FALSE;
		
		$user = OPCcheckout::sendVmMail( $view, $recipient, $noVendorMail );
	}
	
	
	
	/**
	 * With this function you can use a view to sent it by email.
	 * Just use a task in a controller
	 *
	 * @param string $view for example user, cart
	 * @param string $recipient shopper@whatever.com
	 * @param bool $vendor true for notifying vendor of user action (e.g. registration)
	 */

	private static function sendVmMail (&$view, $recipient, $noVendorMail = FALSE) {

		$jlang = JFactory::getLanguage();
		if(VmConfig::get( 'enableEnglish', 1 )) {
			$jlang->load( 'com_virtuemart', JPATH_SITE, 'en-GB', TRUE );
		}
		$jlang->load( 'com_virtuemart', JPATH_SITE, $jlang->getDefault(), TRUE );
		$jlang->load( 'com_virtuemart', JPATH_SITE, NULL, TRUE );

		if(!empty($view->orderDetails['details']['BT']->order_language)) {
			$jlang->load( 'com_virtuemart', JPATH_SITE, $view->orderDetails['details']['BT']->order_language, true );
			$jlang->load( 'com_virtuemart_shoppers', JPATH_SITE, $view->orderDetails['details']['BT']->order_language, true );
			$jlang->load( 'com_virtuemart_orders', JPATH_SITE, $view->orderDetails['details']['BT']->order_language, true );
		} else {
			if (method_exists('VmConfig', 'loadJLang'))
			{
			 VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
			 VmConfig::loadJLang('com_virtuemart_orders',TRUE);
			}
		}

		ob_start();

		$view->renderMailLayout( $noVendorMail, $recipient );
		$body = ob_get_contents();
		ob_end_clean();

		$subject = (isset($view->subject)) ? $view->subject : JText::_( 'COM_VIRTUEMART_DEFAULT_MESSAGE_SUBJECT' );
		$mailer = JFactory::getMailer();
		$mailer->addRecipient( $recipient );
		$mailer->setSubject(  html_entity_decode( $subject) );
		$mailer->isHTML( VmConfig::get( 'order_mail_html', TRUE ) );
		$mailer->setBody( $body );

		if(!$noVendorMail) {
			$replyto[0] = $view->vendorEmail;
			$replyto[1] = $view->vendor->vendor_name;
			$mailer->addReplyTo( $replyto );
		}
		/*	if (isset($view->replyTo)) {
				 $mailer->addReplyTo($view->replyTo);
			 }*/

		if(isset($view->mediaToSend)) {
			foreach( (array)$view->mediaToSend as $media ) {
				$mailer->addAttachment( $media );
			}
		}

		// set proper sender
		$sender = array();
		if(!empty($view->vendorEmail) and VmConfig::get( 'useVendorEmail', 0 )) {
			$sender[0] = $view->vendorEmail;
			$sender[1] = $view->vendor->vendor_name;
		} else {
			// use default joomla's mail sender
			$app = JFactory::getApplication();
			$sender[0] = $app->getCfg( 'mailfrom' );
			$sender[1] = $app->getCfg( 'fromname' );
			if(empty($sender[0])){
				$config = JFactory::getConfig();
				if (method_exists($config, 'getValue'))
				$sender = array( $config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' ) );
				else
				$sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
			}
		}
		$mailer->setSender( $sender );
		
		// stAn, return the language to original: 
		$jlang = JFactory::getLanguage();
		if(VmConfig::get( 'enableEnglish', 1 )) {
			$jlang->load( 'com_virtuemart', JPATH_SITE, 'en-GB', TRUE );
		}
		
		$lang     = JFactory::getLanguage();
        $tag = $lang->getTag(); 
        $filename = 'com_virtuemart';
        $lang->load($filename, JPATH_ADMINISTRATOR, $tag, true);
        $lang->load($filename, JPATH_SITE, $tag, true);
		
		$jlang->load( 'com_virtuemart_shoppers', JPATH_SITE, $tag, true );
		$jlang->load( 'com_virtuemart_orders', JPATH_SITE, $tag, true );
		
		return $mailer->Send();
	}

	
	
	
	
		/**
	 * Check if a minimum purchase value for this order has been set, and if so, if the current
	 * value is equal or hight than that value.
	 * @author Oscar van Eijk
	 * @return An error message when a minimum value was set that was not eached, null otherwise
	 */
	 function checkPurchaseValue() {
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$vendor = OPCmini::getModel('vendor'); 
		

		$vendor->setId(self::$opc_cart->vendorId);
		$store = $vendor->getVendor();
		if ($store->vendor_min_pov > 0) {
			$prices = $this->getCartPrices();
			if ($prices['salesPrice'] < $store->vendor_min_pov) {
				if (!class_exists('CurrencyDisplay'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$minValue = $currency->priceDisplay($min);
				return JText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
			}
		}
		return null;
	}
	function redirect($x, $y="")
	{
	  $mainframe = JFactory::getApplication();
	  $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), $y);
	}
		/**
	 * Test userdata if valid
	 *
	 * @author Max Milbers
	 * @param String if BT or ST
	 * @param Object If given, an object with data address data that must be formatted to an array
	 * @return redirectMsg, if there is a redirectMsg, the redirect should be executed after
	 */
	 function validateUserData($type='BT', $obj = null, $cart=null) {

	 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 // we disable validation for ST address, because it is still missing at the front-end and shall be added as an optional feature
		if ($type == 'ST') return false; 
		require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 
	 
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$userFieldsModel = OPCmini::getModel('userfields'); 
		
		
		
		if ($type == 'BT')
		$fieldtype = 'account'; else
		$fieldtype = 'shipment';

		if ($type == 'BT')
		if (empty($bt_fields_from))
		$fieldtype = 'registration'; 
		
		$neededFields = $userFieldsModel->getUserFields(
		$fieldtype
		, array('required' => true, 'delimiters' => true, 'captcha' => true, 'system' => false)
		, array('delimiter_userinfo', 'name','username', 'password', 'password2', 'address_type_name', 'address_type', 'user_is_vendor', 'agreed'));

		$redirectMsg = false;
		
		$i = 0 ;
        $missing = ''; 
		foreach ($neededFields as $field) {
			 $is_business = JRequest::getVar('opc_is_business', 0); 
	   
	    // we need to alter shopper group for business when set to: 
	     $is_business = JRequest::getVar('opc_is_business', 0); 
		 if (!empty($business_fields))
	     if (!$is_business)
		 {
		   // do not check if filled
		   if (in_array($field->name, $business_fields)) continue; 
		 }
		 if ($type=='ST')
		 if (!empty($shipping_obligatory_fields))
		 {
		   if (!in_array($field->name, $shipping_obligatory_fields)) continue; 
		 }
		   // manage required business fields when not business selected: 
		   //foreach ($business_fields as $fn)
		   
			if($field->required && empty($cart->{$type}[$field->name]) && $field->name != 'virtuemart_state_id'){
				$redirectMsg = JText::sprintf('COM_VIRTUEMART_MISSING_VALUE_FOR_FIELD',JText::_($field->title) );
				$i++;
				
				//more than four fields missing, this is not a normal error (should be catche by js anyway, so show the address again.
				if($i>2 && $type=='BT'){
				    $missing .= JText::_($field->title); 
					$redirectMsg = JText::_('COM_VIRTUEMART_CHECKOUT_PLEASE_ENTER_ADDRESS');
				}
			}

			if ($obj !== null && is_array($cart->{$type})) {
				$cart->{$type}[$field->name] = $obj->{$field->name};
			}

			//This is a special test for the virtuemart_state_id. There is the speciality that the virtuemart_state_id could be 0 but is valid.
			if ($field->name == 'virtuemart_state_id') {
				if (!class_exists('VirtueMartModelState')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'state.php');
				if(!empty($cart->{$type}['virtuemart_country_id']) && !empty($cart->{$type}['virtuemart_state_id']) ){
					if (!$msg = VirtueMartModelState::testStateCountry($cart->{$type}['virtuemart_country_id'], $cart->{$type}['virtuemart_state_id'])) {
						
						$redirectMsg = $msg;
					}
				}

			}
		}
		
		if (empty($redirectMsg)) return false; 
		
		$redirectMsg .= ' '.$missing; 
		return $redirectMsg;
	}
	/**
	 * Set the last error that occured.
	 * This is used on error to pass back to the cart when addJS() is invoked.
	 * @param string $txt Error message
	 * @author Oscar van Eijk
	 */
	public function setError($txt) {
		$this->_lastError = $txt;
	}
	
	// generic method to get the new data from original cart
	public function __get($name)
	{
	  if (isset(self::$opc_cart->{$name}))
	  return self::$opc_cart->{$name}; 
	  
	  return null; 
	}
	
} 