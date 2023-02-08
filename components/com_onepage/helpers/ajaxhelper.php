<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// load OPC loader
//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 



if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'transform.php');
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 

class basketHelper
{

public static $totals_html; 


function getPaymentArray()
{
  		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance(); 
		$cart = VirtueMartCart::getCart ();
		$payments = array(); 
		$results = $dispatcher->trigger('getPaymentMethodsOPC', array( &$cart, &$payments)); 

		/*
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$paymentmodel = OPCmini::getModel('Paymentmethod'); 
		$paymentmodel->_noLimit = true; 
		$payments = $paymentmodel->getPayments(true, true); 
		*/
		jimport('joomla.filesystem.file');
		if (!empty($payments))
		foreach ($payments as $p)
		 {
		   if (isset($p->payment_element))
		     {

			$name = JFile::makeSafe($p->payment_element); 
			//$type = JFile::makeSafe($type); 
			if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'splitplugin'.'.php'))
			{
				$p->split_plugin_path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'splitplugin'.'.php'; 
			}
			else
			$p->split_plugin_path = false; 
			
			if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'preparecalculation'.'.php'))
			{
				$p->preparecalculation_path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'preparecalculation'.'.php'; 
			}
			else
			$p->preparecalculation_path = false; 
			
			
			 }
		 }
		return $payments; 
}

function getHtmlArray($payment, $shipping)
{

}

  /**
     * Sets a selected shipment to the cart
     *
     * @author Max Milbers
     */
    public function setshipment($virtuemart_shipmentmethod_id) {

	/* Get the shipment ID from the cart */
	//$virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', '0');
	if ($virtuemart_shipmentmethod_id) {
	    //Now set the shipment ID into the cart
	    $cart = VirtueMartCart::getCart();
	    if ($cart) {
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		if (method_exists($cart, 'setShipment'))
		$cart->setShipment($virtuemart_shipmentmethod_id);
		$cart->setCartIntoSession();
		//Add a hook here for other payment methods, checking the data of the choosed plugin
		
		
		
		$_dispatcher = JDispatcher::getInstance();
		$_retValues = $_dispatcher->trigger('plgVmOnSelectCheck', array( &$cart, $cart ));
		
		$dataValid = true;
		foreach ($_retValues as $_retVal) {
		    if ($_retVal === true ) {// Plugin completed succesfull; nothing else to do
			$cart->setCartIntoSession();
			return true; 
		    } else if ($_retVal === false ) {
			   return false;
		       //$mainframe = JFactory::getApplication();
		       //$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=editshipment',$this->useXHTML,$this->useSSL), $_retVal);
			break;
		    }
		}

	    }
		
		
		
		return false;
	}
	//self::Cart();
    }
	function checkShipmentMethodsConfigured() {
		
		//For the selection of the shipment method we need the total amount to pay.
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$shipmentModel = OPCmini::getModel('Shipmentmethod');
		$shipments = $shipmentModel->getShipments();
		if (empty($shipments)) {

			$text = '';
			if (JVM_VERSION <= 2)
			{
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=shipmentmethod';
				$text = OPCLang::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}
			}
	
	
				
			if (!defined('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED'))
			{
			 vmInfo('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text); 
			}

			$tmp = 0;
			//$this->assignRef('found_shipment_method', $tmp);

			return false;
		}
		return true;
	}

function restoreDefaultAddress(&$ref, &$cart)
{
  if (!empty($GLOBALS['opc_cart_empty']))
   {
     $cart->BT = array(); 
	 return;
   }
  if (!empty($GLOBALS['opc_zip_empty'])) 
   {
     $cart->BT['zip'] = ''; 
   }
  if (!empty($GLOBALS['opc_country_empty']))
   {
     $cart->BT['virtuemart_country_id'] = ''; 
   }
  if (!empty($GLOBALS['opc_state_empty'])) 
  {
    $cart->BT['virtuemart_state_id'] = ''; 
  }
  
   if (!empty($GLOBALS['st_opc_cart_empty']))
   {
     $cart->ST = 0;
	 return;
   }
  if (!empty($GLOBALS['st_opc_zip_empty'])) 
   {
     if (is_array($cart->ST))
     $cart->ST['zip'] = ''; 
   }
  if (!empty($GLOBALS['st_opc_country_empty']))
   {
     if (is_array($cart->ST))
     $cart->ST['virtuemart_country_id'] = ''; 
   }
  if (!empty($GLOBALS['st_opc_state_empty'])) 
  {
    if (is_array($cart->ST))
    $cart->ST['virtuemart_state_id'] = ''; 
  }
  
}	



function createDefaultAddress(&$ref, &$cart)
{
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

  $vendor = OPCloader::getVendorInfo($cart); 
  
  if (empty($cart->BT))
   {
     $cart->BT = array(); 
	 $GLOBALS['opc_cart_empty'] = true; 
   }
   if (empty($cart->BT['zip']))
    {
	  $GLOBALS['opc_zip_empty'] = true; 
	  if (!empty($op_default_zip))
	  $cart->BT['zip'] = $op_default_zip; 
	  else
	   {
	      if ($op_default_zip === 0)
		 $cart->BT['zip'] = ''; 
		 else
	     $cart->BT['zip'] = $vendor['zip']; 
	   }
	}
   if (empty($cart->BT['virtuemart_country_id']))
    {
	  $GLOBALS['opc_country_empty'] = true; 
	  // ok, here we decide on default country: 
	   $default_shipping_country = OPCloader::getDefaultCountry($cart); 
	  if (!empty($default_shipping_country))
	  $cart->BT['virtuemart_country_id'] = $default_shipping_country; 
	  else
	  $cart->BT['virtuemart_country_id'] = $vendor['virtuemart_country_id']; 
	}
	
	
   
   
   if (empty($cart->BT['virtuemart_state_id']))
    {
	  $GLOBALS['opc_state_empty'] = true;
	  // this will set taxes to zero:
	  if (!empty($opc_usmode)) $cart->BT['virtuemart_state_id'] = ' '; 
	}
	
	// we need to check the ST address as well
	if (!empty($cart->ST))
	{
	   if (empty($cart->ST['zip']))
    {
	  $GLOBALS['st_opc_zip_empty'] = true; 
	  if (!empty($op_default_zip))
	  $cart->ST['zip'] = $op_default_zip; 
	  else
	   {
	      if ($op_default_zip === 0)
		 $cart->ST['zip'] = '';
		 else
	     $cart->ST['zip'] = $vendor['zip']; 
	   }
	}
   if (empty($cart->ST['virtuemart_country_id']))
    {
	  $GLOBALS['st_opc_country_empty'] = true; 
	  // ok, here we decide on default country: 
	   $default_shipping_country = OPCloader::getDefaultCountry($cart); 
	  if (!empty($default_shipping_country))
	  $cart->ST['virtuemart_country_id'] = $default_shipping_country; 
	  else
	  $cart->ST['virtuemart_country_id'] = $vendor['virtuemart_country_id']; 
	}
	
	
   
   // we will not do the state for now
   if (empty($cart->ST['virtuemart_state_id']))
    {
	  $GLOBALS['st_opc_state_empty'] = true; 
	  if (!empty($opc_usmode)) $cart->ST['virtuemart_state_id'] = ' '; 
	}
	
	}

	
}


function getShippingArrayHtml(&$ref, &$cart, $ajax=false)
{
	
	if (!class_exists('OPCloader'))
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php');

	$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
  
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
      if ((!$ajax) && (!empty($op_delay_ship)))
	   {
	
 	   
if (empty($op_disable_shipping))
{
	     return array('<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />');
}
else
{
	     return array();
	
}
	   }
	  $this->createDefaultAddress($ref, $cart); 
	  $preselected2 = JRequest::getVar('shipping_rate_id', ''); 
	  
	  $preselected = JRequest::getVar('virtuemart_shipmentmethod_id', $preselected2); 
	  
      $found_shipment_method=false;
   
	   $shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		

		$shipments_shipment_rates=array();
		
		if (!isset($ref->cart)) $ref->cart = $cart; 
		
		if (!$this->checkShipmentMethodsConfigured() || (!empty($op_disable_shipping))) {
			//define('NO_SHIPPING', '1'); 
			$this->restoreDefaultAddress($ref, $cart); 
			
			return array(); 
		}
		//
		
		
		$selectedShipment = (empty($cart->virtuemart_shipmentmethod_id) ? 0 : $cart->virtuemart_shipmentmethod_id);
		
		if (empty($selectedShipment) && (!empty($preselected))) $selectedShipment = $preselected; 
		unset($_SESSION['load_fedex_prices_from_session']); 
		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		
		// never set any to be selected, we will select them later on:
		if (empty($selectedShipment)) $selectedShipment = -1; 
		
		
		if (!empty($opc_calc_cache))
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipmentOPC', array( &$cart, $selectedShipment, &$shipments_shipment_rates));
		else
		{
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipmentOPCNocache', array( &$cart, $selectedShipment, &$shipments_shipment_rates));
		if (empty($returnValues))
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $cart, $selectedShipment, &$shipments_shipment_rates));
		}
	
		// if no shipment rate defined
		$extraHtml = array(); 
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				//$extraHtml[] = $returnValue;
				break;
			}
		}
	
	
		$shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
	
	
    $ret = '';
    if ($found_shipment_method) {

	
	    
	// if only one Shipment , should be checked by default
	$arr = array(); 
	$preselected = JRequest::getVar('selectedshipping');
	

	//if (empty($preselected) || ($preselected=='choose_shipping') || ($preselected=='shipment_id_0'))
	if ((!empty($opc_default_shipping)) && ($opc_default_shipping == 3))
	if (empty($shipping_inside) && (!empty($shipping_inside_choose)))
	{
	  $choose =  '
	<input type="radio" name="virtuemart_shipmentmethod_id" onclick="javascript:Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);" id="choose_shipping" value="choose_shipping">
	<label for="choose_shipping"><span class="vmshipment"><span class="vmshipment_name">- '.OPClang::_('COM_VIRTUEMART_CART_EDIT_SHIPPING').' - </span></span></label>';
	$arr[] = $choose; 
	$ret = $choose.$ret; 
	}
	
	    foreach ($shipments_shipment_rates as $shipment_shipment_rate) {
		 
		if (is_array($shipment_shipment_rate)) {
		    foreach ($shipment_shipment_rate as $shipment_shipment_rat) {
			
			//if (!empty($shipping_template))
			//OPCTransform::overrideShippingHtml($shipment_shipment_rat, $cart); 
			
			$arr[] = $shipment_shipment_rat; 
			$ret .= $shipment_shipment_rat.'<br />';
		    }
		}
			

	    }
		
	 
    } else {
	 $shipment_not_found_text = $shipment_not_found_text.'<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />';
	 
    }
   
   $this->restoreDefaultAddress($ref, $cart); 
   if (!empty($arr)) return $arr; 
   else return array($shipment_not_found_text); 
}


static $count; 
static $totalIsZero; 
function getCachedShipping(&$cart, &$prices, $shipping_id, &$calc, $data=array())
{
	 
	   if (!isset(OPCloader::$totalIsZero))
	   OPCloader::$totalIsZero = true; 
	   
	   // cache dimensions:
	   // this is a product hash (quantity, attributes, weight, etc..)
	   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	   self::$count++; 
	   $savedcoupon = $cart->couponCode; 
	   
	   
	   $data[] = $shipping_id; 
	   if (!empty($opc_calc_cache))
	   $hash = OPCcache::getGeneralCacheHash('calc', $cart, $data); 
	   $saved_id =  $cart->virtuemart_shipmentmethod_id; 
	  //opc_request_cache
	   
	   if (class_exists('calculationHelperOPC'))
	   calculationHelperOPC::$_forhash = $hash; 
	   // overrided class with the above hash knows if to re-fetch the shipping
	   
	   if (!empty($opc_request_cache))
	   {
       OPCcache::$cachedResult['currentshipping'] = $shipping_id; 
       if (!empty(OPCcache::$cachedResult['shippingcache'][$shipping_id]))
	   {
		 $cart->virtuemart_shipmentmethod_id = (int)$cart->virtuemart_shipmentmethod_id; 
		 $cart->virtuemart_shipmentmethod_id = abs($cart->virtuemart_shipmentmethod_id) * (-1); 
		 
	   }
	   }
	  
	   
	  
	  
	   $vm2015 = false; 
  	   if (method_exists($calc, 'getCheckoutPricesOPC'))
	   {
	   $prices = $calc->getCheckoutPricesOPC(  $cart, false );
	   
	   if (method_exists($calculator, 'getCartData'))
	   $cart->OPCCartData = $calc->getCartData();
	   
	   }
	   else
	   $prices = OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc' );
	   
	  
	   
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
		   
		   
		   
		 }
		 
		}
		
		if (!empty($prices['billTotal']))
	    OPCloader::$totalIsZero = false; 
		
		OPCloader::opcDebug('OPC: getCheckoutPrices from ajaxhelper.php');    
		OPCloader::opcDebug($prices);    
	    if (isset($cart->OPCCartData))
		OPCloader::opcDebug($cart->OPCCartData);    
  
   
   
       if (!empty($opc_request_cache))
       if (!empty($saved_id))
	   {
   
	   $sprice['shipmentValue'] = $prices['shipmentValue']; 
	   $sprice['shipmentTax'] = $prices['shipmentTax']; 
	   $sprice['salesPriceShipment'] = $prices['salesPriceShipment']; 
	   $sprice['shipment_calc_id'] = $prices['shipment_calc_id']; 
	   $sprice['shipmentName'] = @$prices['shipmentName']; 
	   
	   
	   
	   
	   OPCcache::storeShipingCalculation($cart, $sprice, $sprice['shipmentName'], $shipping_id); 
	   }
	   // in case calculation invalidates it: 
	   $cart->virtuemart_shipmentmethod_id = $saved_id; 
	   $cart->couponCode = $savedcoupon; 
	   
}


function getPaymentArrayHtml($cart2, $payment_array, &$shipping_array)
{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		
		if (is_null(OPCloader::$inform_html))
		OPCloader::$inform_html = array(); 
		
		
		$preselected = JRequest::getVar('virtuemart_shipmentmethod_id', JRequest::getVar('shipping_rate_id', '')); 
		$default = array(); 
		$session =& JFactory::getSession();
		$session->set('fedex_rates', null, 'vm');
   		// if ($op_show_others) $vendor_freeshipping = 0;
   		
   		// $extHelper = new opExtension();
   		// $extHelper->runExt('setFreeShipping', '', '', $vars['country'], $vendor_freeshipping); 

		// coupon will get counted again
	$cart =& VirtueMartCart::getCart();
	$add = array(); 
	if (!empty($payment_array))
	foreach ($payment_array as &$pay)
	{
	  if (isset($pay->payment_element))
	  if (!empty($pay->split_plugin_path))
	   {
	     include($pay->split_plugin_path); 
	   }

	   //if (!empty($params)) break; 
	}
	
	if (!empty($add))
	{
	//array_merge($payment_array, $add); 
	foreach ($add as $v)
	  $payment_array[] = $v; 
	}
	
	
	// again and again we have to do overrides because VM team decides about private functions and properties
			
	$dispatcher = JDispatcher::getInstance();
	$prices = array(); 
	
	// renew parameters
	//For the selection of the shipment method we need the total amount to pay.
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
	$shipmentModel = OPCmini::getModel('Shipmentmethod'); //new VirtueMartModelShipmentmethod();
	
	// the configuration is reloaded only when this function is called interanally
	// getPluginMethods which is called by FEdisplay method
	$html = ''; 
	self::$totals_html = ''; 
	
	if (!empty($cart->couponCode))
	{
	  self::$totals_html = '<input type="hidden" id="opc_coupon_code_returned" name="opc_coupon_code_returned" value="'.OPCloader::slash($cart->couponCode).'" />';
	}
	else
    self::$totals_html = '<input type="hidden" id="opc_coupon_code_returned" name="opc_coupon_code_returned" value="" />'; 
	
	//$opc_calc_cache = true;
	 if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'calculationh.php'))
	 if (!empty($opc_calc_cache))
	 {
		 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'calculationh.php'); 
			 
	 if (!class_exists('calculationHelperOPC'))
	 {
		
		 require(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'calculationh_override.php'); 
	 }
	 }
	 
	 
	 // from vm 2.0.22
	  $calc = calculationHelper::getInstance();
	  
	  //$a = get_class_methods ($calc); 
	  /*
	  $test = true; 
	  if (class_exists('ReflectionClass'))
	  {
	  $class = new ReflectionClass('calculationHelper');
	  if (method_exists($class, 'getMethod'))
		  try
		  {
			$method = $class->getMethod('setShopperGroupIds');
			$test = true; 
		  }
		  catch (ReflectionException  $e)
		  {
			  $test = false; 
		  }
  
	  }
		 //if (in_array('setShopperGroupIds', $a))
	   if ($test)
    */		 
		 {
			 
		    if (!empty($opc_calc_cache))
			if (class_exists('calculationHelperOPC'))
			$calc = calculationHelperOPC::getInstanceOPC(); 
		    /*
			$class = new ReflectionClass('calculationHelper');
			$method = $class->getMethod('setShopperGroupIds');
			$method->setAccessible(true);
			$method->invokeArgs($calc, array('6'));
		    */
		 }
	
	 
	 
	 
	 
 
	 if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
	 else $vm2015 = false; 
	 
	 
	 /*
	 foreach ($cart->products as &$p)
	 $calc->
	 */
	 
	 
$mainframe = Jfactory::getApplication();
$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 
//$calc->setVendorCurrency($virtuemart_currency_id); 
	 		
if (!empty($virtuemart_currency_id))
$currencyDisplay = CurrencyDisplay::getInstance($virtuemart_currency_id);
else
{	
	$currencyDisplay = CurrencyDisplay::getInstance($cart->paymentCurrency);
	$virtuemart_currency_id = $cart->paymentCurrency;
}

	
	 
    if (empty($shipping_array))
	{
	  $shipping_array = array(); 
	  $shipping_array[] = '<input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="0" />'; 
	  $cart->virtuemart_shipmentmethod_id = 0; 

	 
	  
	}
	$shipping_array['choose_shipping'] = '<input type="radio" value="0" id="shipment_id_0" />'; 
	
	 //if (empty($payment_array)) 
	 {
	 $payment_a = new stdClass(); 
	 $payment_a->virtuemart_paymentmethod_id = 0; 
	 $payment_array[] = $payment_a; 
	 
	 }
	 
	
	
	if (!class_exists('CurrencyDisplay'))
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
	$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
	
	//virtuemart_currency_id
	 
	foreach ($shipping_array as &$shipping_method)
	foreach ($payment_array as &$payment)
	{
	if (strpos($shipping_method, 'invalid_country')!==false) 
	{
	$cart->virtuemart_shipmentmethod_id = 0; 
	
	}
	if (strpos($shipping_method, 'virtuemart_shipmentmethod_id')===false) 
	{
	 $cart->virtuemart_shipmentmethod_id = 0; 
	}
	
	
	
	
	
	$multishipmentid = array();
	
	if (!empty($shipping_method))
	{
	 //check opc multi methods: 
	 //multielementgetphp
	 
	 
	 $multishipmentid = OPCTransform::getFT($shipping_method, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'id');
	 
	 //example of a multi method with select drop down:
     /*
		foreach ($pobocky_options as $ppp)
				 {
				    $pobocky .= '<option '; 
					if ($sind == $ppp->id) $pobocky .= ' selected="selected" '; 
					$pobocky .= ' ismulti="true" multi_id="shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$ppp->id.'" value="'.$ppp->id.'">'.$ppp->nazev.'</option>'; 
				 }
		*/				 
	 $real_id = false; 
	 $multishipmentid_test = OPCTransform::getFT($shipping_method, 'option', 'ismulti', 'ismulti', 'true', '>', 'multi_id');
	  if (!empty($multishipmentid_test))
	  {
	 
		if (!empty($multishipmentid))
		$real_id = $multishipmentid[0]; 
	    
	    $multishipmentid = $multishipmentid_test; 
		
	  }
	 if (empty($multishipmentid))
	 $multishipmentid = OPCTransform::getFT($shipping_method, 'input', 'cpsol_radio', 'type', 'radio', '>', 'id');
	 
	 
	 
	
	 //$idth = 'shipment_id_'.$shipmentid;   
	// $idth = $shipmentid;
	}
	else 
	{
	
	$idth = 'shipment_id_0';
	}
	 

	if (empty($multishipmentid))
	 {
	 
	   $idth = 'shipment_id_0';
	   $multishipmentid[0] = $idth;
	   
	   
	 }
	 
	 
	 
	 
	foreach ($multishipmentid as $shipmentid)
	{
	
	$idth = $shipmentid;
	$ida = OPCTransform::getFT($shipping_method, 'input', 'virtuemart_shipmentmethod_id', 'type', 'hidden', '>', 'value');
	
	if (empty($ida))
	{
	$ida = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'value');
	}
	
    if (!empty($ida[0]))
	$id = $ida[0];
    else $id = 0; 
	
	
	

	
	$payment_id = (int)$payment->virtuemart_paymentmethod_id; 
	
	
	$_REQUEST['virtuemart_shipmentmethod_id'] = $id; 
	
	$cart->automaticSelectedShipment = true; 
	$cart->automaticSelectedPayment = true; 
	
	$cart->automaticSelectedShipment = true; 
	$cart->automaticSelectedPayment = true; 
	$cart->setPaymentMethod($payment_id); 
	if (method_exists($cart, 'setShipment'))
	$cart->setShipment($id);
	$cart->virtuemart_shipmentmethod_id = $id; 
	$cart->virtuemart_paymentmethod_id = $payment_id; 
	
	$payment_id_override = 0; 

	//
	if (isset($payment->payment_element))
	if (!empty($payment->preparecalculation_path))
	 {
	    include($payment->preparecalculation_path); 
	 }
	
	
	$htmlsaved = $html; 
	$html = ''; 
	require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_shipping.php'); 
	$md5 = md5($html); 
	OPCloader::$inform_html[$md5] = $html; 
    //self::$totals_html .= $html; 	
	$html = $htmlsaved; 
	
	
	//$cart = VirtueMartCart::getCart();
	
	//$returnValues = $dispatcher->trigger('calculateSalesPrice',array(  &$cart, &$id, &$cart->priceUnformatted  ));
	
	$cart->setCartIntoSession();
	/*
	if (!class_exists('VirtueMartControllerCartOpc'))
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'cartcontroller.php'); 
	$cartcontroller = new VirtueMartControllerCartOpc(); 
	*/
	// will trigger plugin code for the selected shipment
	
	
	
	
	JRequest::setVar('virtuemart_shipmentmethod_id', $id); 
	
	//OLD: $cartcontroller->setshipment($cart, $id, false, false); 
	$savedp = $cart->virtuemart_paymentmethod_id; 
	$seveds = $cart->virtuemart_shipmentmethod_id; 
	
		
		
	
	
	if (!empty($id))
	{
	ob_start(); 
	$_dispatcher = JDispatcher::getInstance();
	$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckShipment', array(   &$cart));
	$x = ob_get_clean(); 
	}
	
	$cart->virtuemart_paymentmethod_id = $savedp; 
	$cart->virtuemart_shipmentmethod_id = $seveds; 
	
	$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
	
	if (empty($op_disable_shipping))
	{
	 if (!$vm2015)
	 $prices = $calc->calculateShipmentPrice($cart, $id, true);
	 
	}
	else
	 {
	   $cart->virtuemart_shipmentmethod_id = 0; 
	   //if (!$vm2015)
	   //$calc->calculateShipmentPrice($cart, $id, false); 
	   //$calc->_cartPrices
	 }
	
	if (!$vm2015) 
	$calc->calculatePaymentPrice($cart, $payment_id, true); 
	else
	{
			//$calc->_cartData = null; 
			//$ref->cart->cartData = null; 
			$calc->setCartPrices(array()); 
	}
	$prices = array(); 
	
		
	
		
	$this->getCachedShipping($cart, $prices, $idth, $calc); 
	
	$cart->pricesUnformatted = $prices; 
	
	
	
	
	
	if (empty($subtotal_price_display)) $subtotal_price_display = 'salesPrice'; 
	if ($subtotal_price_display != 'diffTotals')
	{
	$order_subtotal = $prices[$subtotal_price_display]; 
	/*
	if (empty($order_subtotal))
	  {
	    $order_subtotal = $prices['salesPrice']; 
	  }
	  */
	}
	
	if (empty($order_subtotal))
	 {
	   $order_subtotal = $prices['salesPrice'];
	   $subtotal_price_display = 'salesPrice'; 

	 }
	if ($subtotal_price_display == 'diffTotals')
	{
		// difference of billTotal and billTaxAmount
	   $order_subtotal = $prices['billTotal'] - $prices['billTaxAmount']; 
	}
	
	$order_subtotal = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $order_subtotal,false);
	
	$order_total = $prices['billTotal']; 
	$order_total = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $order_total,false);
	$order_tax = 0;
	
	
	if ($coupon_price_display == 'salesWithoutTax')
	{
	if (isset($prices['couponTax']))
	$cT = $prices['couponTax']; 
	else $cT = 0; 
	if (isset($prices['salesPriceCoupon']))
	$cS = $prices['salesPriceCoupon']; 
	else $cS = 0; 
	
	$prices['salesWithoutTax'] = $cS - $cT; 
	}
	if (empty($coupon_price_display)) $coupon_price_display = 'discountAmount'; 
	if (!empty($prices[$coupon_price_display]))
	$coupon_discount = $prices[$coupon_price_display]; 
	else $coupon_discount = 0; 
	
	$coupon_discount = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $coupon_discount,false);
	
	
	if (!empty($payment_discount_before))
	{
	$coupon_discount2 = 0; 
	if (empty($other_discount_display)) $other_discount_display = 'billDiscountAmount'; 
			  switch ($other_discount_display)
			  {
			    case 'billDiscountAmount': 
				//$coupon_display_before = $currencyDisplay->createPriceDiv('billDiscountAmount','', $ref->cart->prices,false,false, 1);
				$coupon_discount2 = $coupon_discount2 = $prices['billDiscountAmount']; 
				
				break; 
				
				case 'discountAmount': 
				//$coupon_display_before = $currencyDisplay->createPriceDiv('discountAmount','', $ref->cart->prices,false,false, 1);
				$coupon_discount2 = $prices['billDiscountAmount']; 
				break;
				case 'minus': 
				$billD = abs($prices['billDiscountAmount']); 
				foreach ($prices as $key=>$val)
				{
				  if (!empty($cart->products[$key]))
				   if (is_array($val))
				   {
				     $billD -= abs($val['subtotal_discount']); 
				   }
				}
				$billD = abs($billD) * (-1); 
				$prices_new['billTotal'] = $billD;
				$coupon_discount2 = $billD; 
				
				
				
				break; 
				case 'sum': 
				$billD = 0; 
				foreach ($prices as $key=>$val)
				{
				   if (!empty($cart->products[$key]))
				   if (is_array($val))
				   {
				     $billD += $val['subtotal_discount']; 
				   }
				}
				$billD = abs($billD) * (-1);
				$prices_new['billTotal'] = $billD; 
				$coupon_discount2 = $billD; 
				
				break; 
				
				
			  }
			  if (!empty($coupon_discount2))
			  $coupon_discount2 = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $coupon_discount2,false);
	
	
	/*	
	  if (!empty($prices['billDiscountAmount']))
	  {
	  $coupon_discount2 = $prices['billDiscountAmount']; 
	  $coupon_discount2 = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $coupon_discount2,false);
	  
	  }
	  else
	  if (!empty($prices['discountAmount']))
	  {
	  $coupon_discount2 = $prices['discountAmount']; 
	  $coupon_discount2 = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $coupon_discount2,false);
	  }
	  else $coupon_discount2 = 0; 
	  */
	}
	else $coupon_discount2 = 0; 
	
	if (!empty($payment_discount_before))
	if (empty($coupon_discount2))
	 {
	 
	    if (!empty($prices['couponValue']))
		 {
		  // $coupon_discount2 = $prices['couponValue']; 
		   
		 }
	 }
	
	
	
	
	if (($product_price_display == 'basePriceWithTax') || ($product_price_display == 'salesPrice'))
	$shippingpayment_price_display = 'salesPrice'; 
	else $shippingpayment_price_display = 'basePrice'; 
	
	if (empty($prices['basePriceWithTax']))
	$shippingpayment_price_display = 'basePrice'; 
	
	$subtotal_price_display = $product_price_display; 
	if (!isset($prices[$shippingpayment_price_display.'Shipment']))
	{
	if ($shippingpayment_price_display != 'salesPrice')
	$order_shipping = $prices['shipmentValue'];
	else
	$order_shipping = $prices['salesPriceShipment']; 
	}
	else
	$order_shipping = $prices[$shippingpayment_price_display.'Shipment']; 
	
	$order_shipping = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $order_shipping,false);
	
	
	
	
	if ($shippingpayment_price_display != 'salesPrice')
	{
	  $ps = $prices['salesPriceShipment']; 
	  $ps = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $ps,false);
	  $ps = $currencyDisplay->priceDisplay ($ps);
	  $os = $currencyDisplay->priceDisplay ($order_shipping);
	  $shipping_method = str_replace($ps, $os, $shipping_method);
	}
	
	$order_shipping = (float)$order_shipping; 
	
	if (empty($order_shipping)) 
	{
	 
	
	}
	
	// lets select a default shipping method: 
	// first shipping found: 
	
	// none to be selected by default if nothing is already selected: 
	if (empty($preselected) && ((!empty($opc_default_shipping)) && ($opc_default_shipping == 3)))
	{
	  $default['id'] = 0; 
	  $default['shipmentid'] = 'choose_shipping'; 
	  $default['price'] = 0; 
	}
	else
	if (!empty($id))
    if (empty($default))
    {
	  $default['id'] = $id; 
	  $default['shipmentid'] = $shipmentid; 
	  $default['price'] = $order_shipping; 
	}	
	else
	{
	  // preselected found (from $_REQUEST)
	  
	  if ($preselected == $id)
	   {
	   
	   
	     // if we found the preselected, let's leave it there
	     $default['p'] = true; 
		 $default['id'] = $id; 
		 if (!empty($real_id))
		 $default['shipmentid'] = $real_id; 
		 else
		 $default['shipmentid'] = $shipmentid; 
		 $default['price'] = $order_shipping; 
	   }
	  // if we haven't found the preselected, lets make the  cheapest not 0 to be selected
	  //if (empty($default['p']))
	  {
	  
	  
	   // check if we already selected: 
	    if (empty($default['p']))
		{
			if (!empty($op_default_shipping_search))
		{
		foreach ($op_default_shipping_search as $s)
		if ($shipmentid == $s)
		{
	    $default['id'] = $id; 
	    $default['shipmentid'] = $shipmentid; 
	    $default['price'] = $order_shipping; 
		$default['p'] = true; 
	    break;
		}
		}
		if (empty($default['p']))
		if (empty($opc_default_shipping) || ($opc_default_shipping === 1))
		{
	    if ((($default['price'] > $order_shipping) || (empty($default['price']) && (!empty($order_shipping)))))
		{
			
			{
				if (($op_default_shipping_zero) && (empty($order_shipping)))
				{
					 $default['id'] = $id;
				     $default['shipmentid'] = $shipmentid; 
					 $default['price'] = $order_shipping; 
				}
				else
			   if ((!$op_default_shipping_zero) && (!empty($order_shipping)))
				{
					 $default['id'] = $id;
					 $default['shipmentid'] = $shipmentid; 
					 $default['price'] = $order_shipping; 
				}
			}
			
	    
		}
		
		}
		else
		if (!empty($opc_default_shipping) && ($opc_default_shipping === 2))
		{
		
		  // select the most expensive here: 
		  if ((($default['price'] < $order_shipping) || (empty($default['price']) && (!empty($order_shipping)))))
		{
			
			{
			   if ((!$op_default_shipping_zero) && (!empty($order_shipping)))
				{
					 $default['id'] = $id;
					 $default['shipmentid'] = $shipmentid; 
					 $default['price'] = $order_shipping; 
					 
					 
				}
			}
			
	    
		}
		  
		}
		}
		
	  }
	}
		
	
	if ($shippingpayment_price_display == 'basePrice')
	{
	  $paymentPriceType = 'paymentValue'; 
	}
	else
	{
	 $paymentPriceType = 'salesPricePayment';  
	}
	
	if (isset($prices[$shippingpayment_price_display.'Payment']))
	$payment_discount = (-1)*$prices[$shippingpayment_price_display.'Payment']; 
    else
	if (!empty($prices[$paymentPriceType]))
	{
	 $payment_discount = (-1)*$prices[$paymentPriceType]; 
	}
	else
	$payment_discount = (-1)*$prices['salesPricePayment']; 
	$payment_discount = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $payment_discount,false);
	
	 
	
	$tax_id = 0; 
	$taxname = array(); 
	$taxrate = array(); 
	$taxamount = array(); 
	
	if (!empty($cart->cartData['DBTaxRulesBill']))
	foreach ($cart->cartData['DBTaxRulesBill'] as $rule) {
		  $tax_id = $rule['virtuemart_calc_id']; 
		  $taxname[$tax_id] = $rule['calc_name']; 
		  $taxrate[$tax_id] = $rule['calc_value']; 
		  
		  $tax = $prices[$tax_id.'Diff']; 
		  if (empty($tax))
	      $tax = $cart->pricesUnformatted[$tax_id . 'Diff'];
		 
		  if (!empty($tax))
		  $taxamount[$tax_id] = $tax; 

	}
	
	if (!empty($cart->cartData['taxRulesBill']))
	foreach ($cart->cartData['taxRulesBill'] as $x)
	 {
	   
	   //if (isset($x['calc_name']) && (stripos($x['calc_kind'], 'tax')!==false))
	    {
		  $tax_id = $x['virtuemart_calc_id']; 
		  $taxname[$tax_id] = $x['calc_name']; 
		  $taxrate[$tax_id] = $x['calc_value']; 
		  if (isset($prices[$tax_id.'Diff']))
		  $tax = $prices[$tax_id.'Diff']; 
	      else $tax = 0; 
		  
		  if (empty($tax) && (isset($cart->pricesUnformatted[$tax_id . 'Diff'])))
	      $tax = $cart->pricesUnformatted[$tax_id . 'Diff'];
	      
	
	      // convert the tax
		  if (!empty($tax))
		  {
	      $tax = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $tax,false);
		  
		  $taxamount[$tax_id] = $tax; 
		  }
		}
		
	 }
	 
	
	// this tax is already included in the subtotal
	if (!empty($prices))
	foreach ($prices as $k=>$x2)
	 {
	
	
	   if (isset($x2['Tax']) && (is_array($x2['Tax'])))
	   foreach ($x2['Tax'] as $ind=>$r)
	    {
		 
		  $tax_id = $ind; 
		  $taxname[$tax_id] = $r[0]; 
		  $taxrate[$tax_id] = $r[1]; 
		  if (isset($prices[$tax_id.'Diff']))
		  $tax = $prices[$tax_id.'Diff']; 
	      
		  
		  if (empty($tax))
		  if (isset($cart->pricesUnformatted[$tax_id . 'Diff']))
	      $tax = $cart->pricesUnformatted[$tax_id . 'Diff'];
	      
		  if (!empty($tax))
		  $taxamount[$tax_id] = $tax; 
		  
		}
		
	 }
	 //stAn, 2.0.226: 
	 //dynamic lines start
	 
	 if (!empty($opc_dynamic_lines))
	 {
	 $types = array('DATax', 'VatTax', 'Tax', 'DBTax'); 
	 $results = array(); 
	 $resultsNames = array(); 
	 foreach ($prices as $key=>$val)
	  {
	     if (is_array($prices[$key]))
		 if (!empty($prices[$key]['subtotal_tax_amount']))
		  {
		    foreach ($types as $ttype)
			  {
			     if(!empty($prices[$key][$ttype]))
				 {
				 
				 foreach ($prices[$key][$ttype] as $id=>$calcOp)
				   {
				     if (empty($calcOp)) continue; 
				     $tax = array(); 
				     $tax['calc_name'] = $calcOp[0];
					 $tax['calc_value'] = $calcOp[1]; 
					 $tax['calc_value_mathop'] = $calcOp[2]; 
					 $tax['calc_shopper_published'] = $calcOp[3]; 
					 $tax['calc_currency'] = $calcOp[4]; 
					 $tax['calc_params'] = $calcOp[5]; 
					 $tax['virtuemart_vendor_id'] = $calcOp[6]; 
					 $tax['virtuemart_calc_id'] = $calcOp[7]; 
					 
				     $res =  $calc->interpreteMathOp($tax, $prices[$key]['subtotal']);
					 if (!isset($results[$id])) $results[$id] =0; 
					 $results[$id] += ($res - $prices[$key]['subtotal']); 
					 $resultsNames[$id] = JText::_($tax['calc_name']); 
					 
				   }
				 }
			  }
		  }
		 
	  }
	  
	  
	  $billRuls = array();
	  if (!empty($cart->OPCCartData['DBTaxRulesBill']))
	  foreach ($cart->OPCCartData['DBTaxRulesBill'] as $rule)
	   {
	      if (empty($rule)) continue; 
	      $results[$rule['virtuemart_calc_id']] = $prices[$rule['virtuemart_calc_id'] . 'Diff'];
		  $restulsNamed[$rule['virtuemart_calc_id']] = $rule['calc_name']; 
		  $billRuls[$rule['virtuemart_calc_id']] = $rule['virtuemart_calc_id']; 
	   }
	   
	  if (!empty($cart->OPCCartData['taxRulesBill']))
	  foreach ($cart->OPCCartData['taxRulesBill'] as $rule)
	   {
	      if (empty($rule)) continue; 
	      $results[$rule['virtuemart_calc_id']] = $prices[$rule['virtuemart_calc_id'] . 'Diff'];
		  $restulsNamed[$rule['virtuemart_calc_id']] = $rule['calc_name']; 
		  $billRuls[$rule['virtuemart_calc_id']] = $rule['virtuemart_calc_id']; 
	   }
	   
	   if (!empty($cart->OPCCartData['DATaxRulesBill']))
	  foreach ($cart->OPCCartData['DATaxRulesBill'] as $rule)
	   {
	      if (empty($rule)) continue; 
	      $results[$rule['virtuemart_calc_id']] = $prices[$rule['virtuemart_calc_id'] . 'Diff'];
		  $restulsNamed[$rule['virtuemart_calc_id']] = $rule['calc_name']; 
		  $billRuls[$rule['virtuemart_calc_id']] = $rule['virtuemart_calc_id']; 
	   }
	   //DATaxRulesBill end
	   
	   //shipping fee
	  if (!empty($results))
	   {
	     reset($results);
		 $first_key = key($results);
		 if (empty($prices['shipment_calc_id']))
		 {
		 if (!empty($prices['shipmentTax']))
			    {
				  $results[$first_key] += $prices['shipmentTax']; 
				}
			   
		 }
		 else
		 {
		
		   if (!is_array($prices['shipment_calc_id']))
		   {
		   if (!empty($prices['shipmentTax']))
			    {

				  if (!isset($results[$prices['shipment_calc_id']])) $results[$prices['shipment_calc_id']] = 0; 
				  
				  $results[(int)$prices['shipment_calc_id']] += $prices['shipmentTax']; 
				}
			}
			else
			 {
			    foreach ($prices['shipment_calc_id'] as $calc_id)
				 {
				  if (!isset($results[$calc_id])) $results[$calc_id] = 0; 
				  $results[$calc_id] += $prices['shipmentTax']; 
				   // maybe we should add it just once !!!
				 }
			 }
		 }
		 if (empty($prices['payment_calc_id']))
		 {
		   if (!empty($prices['paymentTax']))
			    {
				 $results[$first_key] += $prices['paymentTax']; 
				}
		 }
		 else
		 {
		   if (!is_array($prices['payment_calc_id']))
		   {
		   if (!empty($prices['paymentTax']))
			    {
				 if (!isset($results[$prices['payment_calc_id']])) $results[$prices['payment_calc_id']] = 0; 
				 $results[$prices['payment_calc_id']] += $prices['paymentTax']; 
				}
		   }
		   else
		    {
			    foreach ($prices['payment_calc_id'] as $calc_id)
				 {
				   if (!isset($results[$calc_id])) $results[$calc_id] = 0; 
				   $results[$calc_id] += $prices['paymentTax']; 
				    // maybe we should add it just once !!!
				 }
			}
		 }
		 
		   
	   }
	   $dynamic = array(); 
	   foreach ($results as $key=>$val)
	    {
		  //this is for the bill taxes:
		  if ($prices[$key.'Diff']>$val) $val = $key.'Diff'; 
		  $dynamic[$key]['value'] = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $val,false);
		  if (!isset($resultsNames[$key]))
		   {
		       $db = JFactory::getDBO(); 
			   $q = 'select calc_name from #__virtuemart_calcs where virtuemart_calc_id = '.(int)$key.' limit 0,1'; 
			   $db->setQuery($q); 
			   $resultsNames[$key] = $name = $db->loadResult(); 
		   }
		  $dynamic[$key]['name'] = $resultsNames[$key];
		}
	  
	 //stAn, 2.0.226 end
	 
	 
	 
	 if (!empty($prices))
	 foreach ($prices as $k=>$x2)
	 {
	    
	    if (strpos($k, 'Diff')!==false)
		 {
		   $k2= str_replace('Diff', '', $k); 
		   if (!empty($results) && (!array_key_exists($k2, $results)))
		   if (is_numeric($k2))
		     {
			   $k2 = (int)$k2; 
			   $db = JFactory::getDBO(); 
			   $q = 'select calc_name from #__virtuemart_calcs where virtuemart_calc_id = '.$k2.' limit 0,1'; 
			   $db->setQuery($q); 
			   $name = $db->loadResult(); 
			   // to support multilang
			   $name = JText::_($name); 
			   $dynamic[$k2]['name'] = $name; 
			   $val = 0; 
			   if (isset($cart->OPCCartData['VatTax']))
			   if (isset($cart->OPCCartData['VatTax'][$k2]))
			   if (isset($cart->OPCCartData['VatTax'][$k2]['taxAmount']))
			   {
			   $val = $cart->OPCCartData['VatTax'][$k2]['taxAmount'];
			   if (isset($prices['shipmentTax']))
			    {
				  $val += $prices['shipmentTax']; 
				}
			   if (isset($prices['paymentTax']))
			    {
				 $val += $prices['paymentTax']; 
				}
			   
			   }
			   if ($val == 0)
			   $val = $x2; 
			   
			   $dynamic[$k2]['value'] = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $val,false);
			 }
		 }
	 }
	 
	 
	 
	 }
	 //dynamic lines end
	 
	 
	 // add shipment tax to it's plugins subtotal 
	 if (!empty($prices['shipmentTax']))
	  {
	    if (isset($prices['shipment_calc_id']))
		if (!is_array($prices['shipment_calc_id']))
		{
		  if (!isset($taxamount[$prices['shipment_calc_id']])) $taxamount[$prices['shipment_calc_id']] = 0; 
		  $taxamount[$prices['shipment_calc_id']] += $prices['shipmentTax']; 
		}
		else
		{
		  foreach ($prices['shipment_calc_id'] as $calc_id)
		    {
				if (!isset($taxamount[$calc_id])) 
				 {
				   $taxamount[$calc_id] = 0; 			
				 }
			  $taxamount[$calc_id] += $prices['shipmentTax']; 
			   // maybe we should add it just once !!!
			   // break; 
			}
		}
	    
	  }
	  if (!isset($tax)) $tax = 0; 

	 
	  
	  if (!empty($prices['paymentTax']))
	  {
	   
	     if (isset($prices['payment_calc_id']))
		{
		if (!is_array($prices['payment_calc_id']))
		{
		if (!isset($taxamount[$prices['payment_calc_id']])) $taxamount[$prices['payment_calc_id']] = 0; 
	    $taxamount[$prices['payment_calc_id']] += $prices['paymentTax']; 
		}
		else
		   {
		      foreach ($prices['payment_calc_id'] as $calc_id)
			   {
			     if (!isset($taxamount[$calc_id])) $taxamount[$calc_id] = 0; 
	             $taxamount[$calc_id] += $prices['paymentTax']; 
			   }
		   }
		}
		else
		  {
		     if (isset($prices['payment_tax_id']))
			 {
			 if (!isset($taxamount[$prices['payment_tax_id']])) $taxamount[$prices['payment_tax_id']] = 0; 
			 $taxamount[$prices['payment_tax_id']] += $prices['paymentTax']; 
			 }
		  }
	  }
	  
	$order_tax = $prices['billTaxAmount']; 
	$order_tax = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $order_tax,false);
	// ok, here we should reprocess the coupon
	
	
	if (!empty($payment_id_override))
	{
	
	$o = '<input type="hidden" id="payment_id_override_'.$payment_id.'" value="1"/>';
	if (!defined('payment_id_override_'.$payment_id))
	{
	 self::$totals_html .= $o; 
	 define('payment_id_override_'.$payment_id, 1); 
	}
	$payment_id = $payment_id_override; 
	}
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_subtotal" value="'.$order_subtotal.'"/>';
	
	$sum = (float)0; 
	
	// this shows 
	if ((count($taxname)>=1) && (empty($show_single_tax)))
	{
	//if (!defined('.$idth.'_'.$payment_id.'_tax
	$taxhtml = ''; 
	foreach ($taxname as $id=>$name)
	{
	 
	 $rate = ((float)$taxrate[$id]) / 100; 
	 if (empty($taxamount[$id])) continue; 
	 $tax = $taxamount[$id]; 
	 if (!is_numeric($tax)) 
	 { 
	   // we have a possible cross compatiblity error here
	   $tax = 0; 
	 
	 }
	 $sum += $tax; 
	 
	 self::$totals_html .= '<input type="hidden" name="'.$idth.'_'.$payment_id.'_tax" value="'.$rate.'|'.$tax.'"/>';
	 self::$totals_html .= '<input type="hidden" name="'.$idth.'_'.$payment_id.'_taxname" value="'.OPCloader::slash($name).'"/>';

	 
	 
	
	}
	
	
	
	}
	// disabled in 2.0.127 - this line shows total tax in one line
	//if (empty($taxname))
	{
	
	self::$totals_html .= '<input type="hidden" name="'.$idth.'_'.$payment_id.'_tax_all" id="'.$idth.'_'.$payment_id.'_tax_all" value="|'.$order_tax.'"/>';
	
	}
	
	if (!empty($dynamic))
	{
	  foreach ($dynamic as $key=>$val)
	  {
	    if (!empty($dynamic[$key]['value']))
		{
	     self::$totals_html .= '<input type="hidden" name="'.$idth.'_'.$payment_id.'_dynamic" rel="'.$key.'" id="'.$idth.'_'.$payment_id.'_dynamicvalue_'.$key.'" stringname="'.OPCloader::slash($dynamic[$key]['name']).'" value="'.$dynamic[$key]['value'].'"/>'; 

		}
	  }
	}
	
	if (!empty($payment_discount))
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_payment_discount" value="'.$payment_discount.'"/>';
	else self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_payment_discount" value="0.00"/>';
	
	if (!empty($coupon_discount))
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_coupon_discount" value="'.$coupon_discount.'"/>';
	else self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_coupon_discount" value="0.00"/>';
	
	if (!empty($coupon_discount2))
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_coupon_discount2" value="'.$coupon_discount2.'"/>';
	else self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_coupon_discount2" value="0.00"/>';
	
	if (!empty($order_shipping))
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping" value="'.$order_shipping.'"/>';
	else self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping" value="0.00"/>';
	
	if (!empty($order_shipping_tax))
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping_tax" value="'.$order_shipping_tax.'"/>';
	else self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping_tax" value="0.00"/>';

	if (!empty($order_total))
	self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_total" value="'.$order_total.'"/>';
	else self::$totals_html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_total" value="0.00"/>';
	
   
   }
   
   
   }
   unset($ke); unset($html2); 
   if (!empty($shipping_array))
   unset($shipping_array['choose_shipping']); 
   $wrapper = '<!--shipping_goes_here-->';
   $num = 1; 
   if (!empty($shipping_array))
   if (!empty($shipping_inside))
   {
	   $num = 0; 
	   $ret = OPCTransform::shippingToSelect($shipping_array, $num, $cart);
	   
	   if (!empty($num))
	   $html .= $ret; 
   }
   
   if (!empty($shipping_array))
   if (empty($shipping_inside) || (empty($num)))
   {
    $htmla = array(); 
   foreach ($shipping_array as $ke=>&$html2)
    {
	
	
	
	if (strpos($html2, 'virtuemart_shipmentmethod_id')!==false)
	{
	     //$x = $this->getFT($html2, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'id');
	    
	     //$x1 = strpos($shipping_array[$ke], '<input'); 
		 //if ($x1 !== false) 
		 {
		 //$x2 = strpos($shipping_array[$ke], '>', $x1+1); 
		 //if ($x2 !== false) 
		 {
		 $tmp = $tmp2 = $shipping_array[$ke]; //substr($shipping_array[$ke], $x1, $x2); 
		 if (!empty($default))
		 $shipmentid = (string)$default['shipmentid']; 
		 else $shipmentid = ''; 
		 
		 if (strpos($tmp, '"'.$shipmentid.'"')!==false)
		 {
	     $tmp = str_replace('checked="checked"', '', $tmp); 
		 $tmp = str_replace('checked', '', $tmp); 
		 //virtuemart_shipmentmethod_id
	     $tmp = str_replace('name="virtuemart_shipmentmethod_id"', ' autocomplete="off" name="virtuemart_shipmentmethod_id"', $tmp); 
		 if (!empty($default))
		 {
		  
		  
	      $tmp = $this->str_replace_once('"'.$shipmentid.'"', '"'.$shipmentid.'" checked="checked" ', $tmp);
         }
		 }
		 $tmp = str_replace('name="virtuemart_shipmentmethod_id"', 'name="virtuemart_shipmentmethod_id" onclick="javascript:Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);" ', $tmp);  
		 //if (strpos($tmp, 'shipment_id_'.$id.'"')!== false) $tmp.' ok sel ';
		 $shipping_array[$ke] = $tmp; //str_replace($shipping_array[$ke], $tmp, $shipping_array[$ke]);
		 
		  $x1 = strpos($shipping_array[$ke], '<input'); 
		  $x1a = $this->strposall($shipping_array[$ke], '<input'); 
		  if (!empty($x1a))
		  foreach ($x1a as $x1)
		  {
		   $x2 = strpos($shipping_array[$ke], '>', $x1+1); 
		   if ($x2 !== false)
		   {
		     if (substr($shipping_array[$ke], $x2-1, 1)!='/')
			  {
			    // fixed a bug in 2.0.87 !! otherwise the shipping method might be rendered incorrectly
			    $a1 = substr($shipping_array[$ke], 0, $x2); 
				$a2 = substr($shipping_array[$ke], $x2); 
			    $shipping_array[$ke] = $a1.'/'.$a2; 
				
				
			  }
		   }
		   }
		  }
		  }
		 
	  }
		  //$html .= $shipping_array[$ke].'<br />';
		   
	      
		 //echo 'sa:'.$shipping_array[$ke].'endsa';
		 if (strpos($shipping_array[$ke], '<!--shipping_goes_here-->')===false)
			{
				$htmla[] = $shipping_array[$ke].'<br />';
		    }
			else 
			 {
			   $wrapper = $shipping_array[$ke].'<br />';
			 }
	
	   
	}
	
	  $vars = array('shipping' => $htmla, 
				 'cart'=> $cart, );
        
		if (!class_exists('OPCrenderer'))
		require (JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		
				
	   $htmlr = $renderer->fetch($renderer, 'list_shipping_methods.tpl', $vars); 
	   
	   if (empty($htmlr))
	   $html .= implode('', $htmla); 
	   else $html .= $htmlr; 
	
	// create html: 
	
	}
	
	$html = str_replace('<!--shipping_goes_here-->', $html, $wrapper); 
	
	if (strpos($html, 'checked')===false)
	 {
	   $html = $this->str_replace_once('"virtuemart_shipmentmethod_id"', '"virtuemart_shipmentmethod_id" checked="checked"', $html); 
	 }
   
   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_clear_shipping.php'); 

	 
	 // clear the settings: 
   $cart->virtuemart_shipmentmethod_id = 0; 
   $cart->virtuemart_paymentmethod_id = 0; 
   $cart->automaticSelectedShipment = false; 
   $cart->automaticSelectedPayment = false; 
   $cart->setCartIntoSession();
   if (method_exists($calc, 'setCartPrices')) 
	   $calc->setCartPrices(array()); 
	
   
   return $html;

}
// http://tycoontalk.freelancer.com/php-forum/21334-str_replace-only-once-occurence-only.html
function str_replace_once($needle , $replace , $haystack){ 
    // Looks for the first occurence of $needle in $haystack 
    // and replaces it with $replace. 
    $pos = strpos($haystack, $needle); 
    if ($pos === false) { 
        // Nothing found 
    return $haystack; 
    } 
    return substr_replace($haystack, $replace, $pos, strlen($needle)); 
}
  
 function getNextOrderId()
 {
         // get list of avaiable ship to countries from currier configuration
       $db =& JFactory::getDBO();
       $prefix = $db->getPrefix();
       $table = $prefix.'virtuemart_orders';
	
	$db->setQuery("show table status where name='".$table."'");
	$a = $db->loadObjectList();
	if (empty($a)) $next_order_id = rand(990000, 999999);
	else
	foreach ($a as $r)
	{
	if (isset($r) && ($r !== false))
	{
	$next_order_id = $r->Auto_increment;

	}
	else 
	$next_order_id = rand(90000, 100000);
	}
	return $next_order_id; 
	
	
 }

 function calculateShipping()
 {
   $cartData['shipmentName'] = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
	$cartPrices['shipmentValue'] = 0; //could be automatically set to a default set in the globalconfig
	$cartPrices['shipmentTax'] = 0;
	$cartPrices['shipmentTotal'] = 0;
	$cartPrices['salesPriceShipment'] = 0;
 }
 
function getShippingArray()
  {
   
    if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
	JPluginHelper::importPlugin('vmshipment');
	

	// lets create a new instance of the cart
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

	$cart =& VirtueMartCart::getCart(false, false);		
	
		//For the selection of the shipment method we need the total amount to pay.
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$shipmentModel = OPCloader::getMini('Shipmentmethod');
		$shipments = $shipmentModel->getShipments();
	 return $shipments;	
	 foreach ($shipments as &$s)
	 {
	 
	
    
	
	$cartData['shipmentName'] = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
	$cartPrices['shipmentValue'] = 0; //could be automatically set to a default set in the globalconfig
	$cartPrices['shipmentTax'] = 0;
	$cartPrices['shipmentTotal'] = 0;
	$cartPrices['salesPriceShipment'] = 0;

	$savedc = $cart;
	
	$cart->virtuemart_shipmentmethod_id = $s->virtuemart_shipmentmethod_id;
	$cart->automaticSelectedShipment=true;
	$cart->setCartIntoSession();
	$returnValues = $dispatcher->trigger('calculateSalesPrice',array(  &$cart, &$s, &$cart->priceUnformatted  ));
    $found_shipment_method=false;
    $shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
	$shipments_shipment_rates=array();

	$s->op_prices = $cartPrices;
	$s->op_name = $cartData; 
	
	$cart = $savedc;
	   
	 
	 }
	 
	return $shipments; 
	 
	if (empty($shipments))
	{
	  // we have no shipping method avaiable
	  define('NO_SHIPPING', '0');    
	}
	
	
		$selectedShipment = (empty($cart->virtuemart_shipmentmethod_id) ? 0 : $cart->virtuemart_shipmentmethod_id);

		$shipments_shipment_rates = array();

		//JPluginHelper::importPlugin('vmshipment');
		$dispatcher =& JDispatcher::getInstance();
		unset($_SESSION['load_fedex_prices_from_session']); 
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				break;
			}
		}
		$shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		return;
	
	
    $ret = '';
   


	    
	// if only one Shipment , should be checked by default
	    foreach ($shipments_shipment_rates as $shipment_shipment_rates) {
		if (is_array($shipment_shipment_rates)) {
		    foreach ($shipment_shipment_rates as $shipment_shipment_rate) {
			$ret .= $shipment_shipment_rate.'<br />';
		    }
		}
	    }
	 
   
	return $ret; 
  }
  /**
 * strposall
 *
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */

function strposall($haystack,$needle, $offset = 0){
   
    $s=$offset;
    $i=0;
    
	if (empty($needle)) return false; 
	
	if (empty($haystack)) return false; 
	
    while (is_integer($i)){
       
        $i = stripos($haystack,$needle,$s);
       
        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i+strlen($needle);
			
        }
    }
    if (isset($aStrPos)) {
        return $aStrPos;
    }
    else {
        return false;
    }
}
  
}


