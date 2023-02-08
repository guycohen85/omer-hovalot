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
  JLoader::register('JDate', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'joomla3'.DIRECTORY_SEPARATOR.'date.php'); 
 // require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'joomla3'.DIRECTORY_SEPARATOR.'date.php'); 
// Joomla! 1.7 code here
}
*/

/**
 * Joomla! SEF Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.sef
 */
//require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
class plgSystemOpc extends JPlugin
{
    public function onAfterRoute() {
	
	
	
	  if (self::_check()) 
	  {
	  
	   JHTMLOPC::script('opcping.js', 'components/com_onepage/assets/js/', false);
	  }

	  
	
	
	
	if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php'); 
	
	if (!OPCplugin::checkLoad()) return; 
	
	OPCplugin::loadShoppergroups();
	  
	if (!OPCplugin::isOPCcheckoutEnabled()) return;
	OPCplugin::getContinueLink(); 
	OPCplugin::getCache(); 

	  if (OPCplugin::alterActivation()) return; 
	  if (OPCplugin::alterRegistration()) return; 
	  
	  
	  
	  OPCplugin::enableSilentRegistration(); 
	  if (!OPCplugin::checkOPCtask()) return; 
	  OPCplugin::keyCaptchaSupport(); 
	  
	 
	  if (!OPCplugin::loadOPCcartView()) return; 
	  OPCplugin::fixVMbugVirtuemartUser(); 
	  OPCplugin::fixVMbugNewShippingAddress(); 
	  
	  OPCplugin::setItemid(); 
	  OPCplugin::loadOpcForLoggedUser(); 
	  OPCplugin::updateJoomlaCredentials(); 
	  OPCplugin::updateAmericanTax(); 
	
	  
	
	}
	
	// this function is triggered on OPC cart display
	public static function registerCartEnter()
	{
	
	   if (!self::_check()) return; 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'aba.php'); 
	   OPCAba::cartEnter(); 
	}
	
	// this function is triggered from ajax content to update abandoned cart measurement and cart data
	public static function updateAbaData()
	{
	 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'aba.php'); 
	   OPCAba::update(); 
	}
	// this function pairs user with an order for abandoned cart measurement
	public static function registerOrderAttempt(&$order)
	{
	   if (!self::_check()) return; 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'aba.php'); 
	   OPCAba::orderMade($order); 
	}
	
	public static function registerCart()
	{
	   
	   if (!self::_check()) return; 
	   if (!class_exists('OPCtrackingHelper')) return;
	   $hash2 = uniqid('opc', true); 
	   $hashn = JApplication::getHash('opctracking'); 
	   $hash = JRequest::getVar($hashn, $hash2, 'COOKIE'); 
	   if ($hash2 == $hash)
	   {
	   // create new cookie if not set
	   OPCtrackingHelper::setCookie($hash); 
	   }
	   
	   OPCtrackingHelper::registerCart($hash); 
	}
	
		private static function _check()
	{
	  	$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return false;
		}
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php')) return false;
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') return false;

		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') return false; 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 

		
		require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
		
		return true; 

	}

	private function _opcTrackingCheck()
	{
	   if (self::_check()) 
	   {
	   if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php')) return false;
	   
	   if (class_exists('plgSystemOpctracking'))
	   if (!empty(plgSystemOpctracking::$_storedOrder))
	   {
	     plgSystemOpctracking::_tyPageMod(self::$_storedOrder, true); 
	   }
	   
	   
	   
	   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
	   
	   //if (!class_exists('OPCtracking')) return; 
	   
	   
	   if (class_exists('OPCtrackingHelper'))
	   {
	   
	   if (!OPCtrackingHelper::checkStatus()) return; 
	   
	   if (empty(OPCtrackingHelper::$html)) return; 
	   $html = OPCtrackingHelper::$html; //OPCtrackingHelper::getHTML(); 
	   
	   $buffer = JResponse::getBody();
	   $bodyp = stripos($buffer, '</body'); 
	   $buffer = substr($buffer, 0, $bodyp).$html.substr($buffer, $bodyp); 
	   
	   
	   JResponse::setBody($buffer);
	   }
	   }	
	}
	/**
	 * Converting the site URL to fit to the HTTP request
	 */
	public function onAfterRender()
	{

		//opc tracking start
		 //if (!empty(self::$delay)) return; 
	   
	   
	   $this->_opcTrackingCheck(); 
	   

		//opc tracking end
		
	  if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	  //if ($format != 'html') return;

		$app = JFactory::getApplication();

		if ($app->getName() != 'site') {
			return true;
		}
		$format = JRequest::getVar('format', 'html'); 
		if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php')) return;
		
		$task = JRequest::getVar('task', ''); 
		$view = JRequest::getCMD('view'); 
		
		 if(('com_virtuemart' == JRequest::getCMD('option') && !$app->isAdmin()) && (('cart'==$view) || ($view=='pluginresponse'))) 
		 {
			 $buffer = JResponse::getBody();
		if ($task != 'checkout')
		{
		//Replace src links
		$base	= JURI::base(true).'/';
		
		 //orig opc: 
		 $buffer = str_replace('$(".virtuemart_country_id").vm2', '// $(".virtuemart_country_id").vm2', $buffer); 
		 //$buffer = str_replace('$(".shipto_virtuemart_country_id").vm2', '// $(".virtuemart_country_id").vm2', $buffer); 
		 //$buffer = str_replace('$(".virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		 //$buffer = str_replace('$("select.virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		 $buffer = str_replace('$("select.virtuemart_country_id").vm2', '// $("select.virtuemart_country_id").vm2', $buffer); 
		 $buffer = str_replace('$("select.shipto_virtuemart_country_id").vm2', '// $("select.shipto_virtuemart_country_id").vm2', $buffer); 
		 //$("select.virtuemart_country_id")
		 $buffer = str_replace('$(".virtuemart_country_id").vm2front', '// $(".virtuemart_country_id").vm2front', $buffer); 
		 $buffer = str_replace('$("#virtuemart_country_id").vm2front', '// $("#virtuemart_country_id").vm2front', $buffer);
		 $buffer = str_replace('$("#shipto_virtuemart_country_id").vm2front', '// $("#shipto_virtuemart_country_id").vm2front', $buffer);
		 $buffer = str_replace('jQuery(\'#zip_field, #shipto_zip_field\')', '// jQuery(\'#zip_field, #shipto_zip_field\')', $buffer); 
		 //$buffer = str_replace('$(".vm-chzn-select").chosen', '// $(".vm-chzn-select").chosen', $buffer);
		 $buffer = str_replace('/plugins/vmpayment/klarna/klarna/assets/js/klarna_general.js', '/components/com_onepage/overrides/payment/klarna/klarna_general.js', $buffer); 
		 
		 $inside = JRequest::getVar('insideiframe', ''); 
		 if (!empty($inside))
		  {
		    $buffer = str_replace('<body', '<body onload="javascript: return parent.resizeIframe(document.body.scrollHeight);"', $buffer); 
		  }
		 //$buffer = str_replace('$(".virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		$buffer = str_replace('jQuery("input").click', 'jQuery(null).click', $buffer);
		$buffer = str_replace('#paymentForm', '#adminForm', $buffer);
		
		/*
		if (isset(VmPlugin::$ccount))
	    $buffer .= '<script type="text/javascript">console.log("positive cache: '.VmPlugin::$ccount.'")</script>';
		*/
		
		 }
		
		    if (class_exists('plgSystemBit_vm_change_shoppergroup'))
			{
				$js_text = "<script type=\"text/javascript\">location.reload()</script>";
				//$js_text = "location.reload()";
				$c = 0; 
				$buffer = str_replace($js_text, '', $buffer, $c); 
				
				 
				
			}
			
			JResponse::setBody($buffer);
		
		
		}

		
		
		return true;
	}
	// we will disable the 
	public function plgVmInterpreteMathOp2($calc, $rule, $price, $revert)
	{
		return false; 
	}
	
	public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, &$cart_prices, &$cart_prices_name) {
	  
	  if (!empty($cart->virtuemart_shipmentmethod_id))
	  if ($cart->virtuemart_shipmentmethod_id < 0)
	  if (class_exists('OPCcache'))
	  if (!empty(OPCcache::$cachedResult['currentshipping']))
	  {
		return OPCcache::getStoredCalculation($cart, $cart_prices, $cart_prices_name); 
	  }
	}
	// triggered from: \administrator\components\com_virtuemart\models\orders.php
	public function plgVmOnUserOrder(&$_orderData)
	{
		// fix vm2.0.22 bug
		if (empty($_orderData->order_payment) && (empty($_orderData->order_payment_tax)))
		{
			
		 if (!class_exists('VirtueMartCart'))
		 require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

			
					$cart = VirtueMartCart::getCart();
					$prices = $cart->getCartPrices();
					if (!empty($prices['salesPricePayment']))
					{
						$_orderData->order_payment = (float)$prices['salesPricePayment'];
					}
			
		}
	}
	function plgVmOnUserStore(&$data)
	{
	  
	  //if ((empty($data['username'])) && (!empty($data['email']))) $data['username'] = $data['email']; 
	}
	public function plgVmRemoveCoupon($_code, $_force)
	{
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	   if (empty($_force))
	    {
		   include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		   if (!empty($do_not_allow_gift_deletion)) return true; 
		}
		return null; 
	}
	public function plgVmOnUpdateOrderPayment(&$data,$old_order_status)
	{
	  if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php'); 
	  OPCplugin::checkGiftCoupon($data, $old_order_status);  
	  
	  
	}
	
	// code to update shopper groups: 
	public static $saveGroups; 
	public function onUserLogin($user, $options = array())
	{
	   $session = JFactory::getSession();
	   self::$saveGroups = $session->get('vm_shoppergroups_add',array(),'vm'); 
	   $session->set('vm_shoppergroups_add',array(),'vm'); 
	   
	}
	public function onUserLoginFailure($resp)
	{
	
	   if (!empty(self::$saveGroups))
	   {
	    $session = JFactory::getSession();
	    $session->set('vm_shoppergroups_add',self::$saveGroups,'vm'); 
	   }
	}
	
	public function onUserLogout($user, $options = array())
	{
	 
	}
	
}
