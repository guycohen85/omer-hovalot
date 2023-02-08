<?php
/**
 * Overrided Cart View class for the One Page Checkout and Virtuemart 2
 * This is the main loader of the checkout view itself independent on user selected template in virtuemart
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
 * ORIGINAL LICENSE AND COPYRIGHT NOTICE
 *
 * View for the shopping cart, modified for One Page Checkout by RuposTel
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
 * @author Oscar van Eijk
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 4999 2011-12-09 21:31:02Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport('joomla.application.component.view');


require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'version.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 

if(!class_exists('VmView'))
{
if (file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmview.php'))
require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmview.php');
else
require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'vmview.php');
}

//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
/**
 * View for the shopping cart
 * @package VirtueMart
 * @author Max Milbers
 * @author Patrick Kohl
 */
class VirtueMartViewCart extends VmView {
	
	public function display($tpl = null) {
		
		if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
			$calc = calculationHelper::getInstance();
		
		require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php');  
		$OPCloader = new OPCloader; 
		
		$cart = VirtueMartCart::getCart();
		
		
		
		
		$isexpress = $OPCloader->isExpress($cart); 
		
		if (!isset($cart->savedST))
		if (!empty($cart->ST))
		if ($cart->STsameAsBT === 0)
		  {
				$cart->savedST = $cart->ST; 
		  }

		if (!isset($cart->savedBT))
		if (!empty($cart->BT))
		if ($cart->STsameAsBT === 0)
		  {
				$cart->savedBT = $cart->BT; 
		  }


		  
		if (!isset($cart->pricesUnformatted['billTotal']))
		{
	  
		$vm15 = false;
		$cart->virtuemart_shipmentmethod_id = 0; 
		$cart->pricesUnformatted = $OPCloader->getCheckoutPrices($cart, false, $vm15); 
	  
		}

		
		
		//vm2.015+
		if (method_exists($calc, 'setCartPrices')) 
		if (function_exists('ReflectionObject'))
		{
		$reflection  = new ReflectionObject($calc);
		$prop = $reflection->getProperty('_cartData');
		// prevent vm2.0.18a
		if (!$prop->isPrivate())
		 {
		 
		if (!isset($calc->_cartData['VatTax']))
		{
		 $calc->_cartData['VatTax'] = array(); 
		}
		if (!isset($calc->_cartData['taxRulesBill']))
		{
		 $calc->_cartData['taxRulesBill'] = array(); 
		}
		}
		}
		if (OPCJ3)
		{
		  if (!isset($calc->_cart)) $calc->_cart = new stdClass(); 
		}
		
		
		
	// since vm2.0.21a we need to load the language files here
	if (!class_exists('OPCLang'))
		require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
		
	
	OPCLang::loadLang(); 
		
		
	    // opc reset defaults
		$session = JFactory::getSession(); 
	    $rand = uniqid('', true); 
        $session->set('opcuniq', $rand);
        $session->set($rand, '0');

		
	
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();

		$layoutName = $this->getLayout();
		if (!$layoutName)
		$layoutName = JRequest::getWord('layout', 'default');
		
		$product = JRequest::getVar('virtuemart_product_id', null); 
		$quantity = JRequest::getVar('quantity', null); 
		
		$invalidlayout = false; 
		// extra layouts here
		if (($layoutName == 'blog') || ($layoutName == 'category') || ($layoutName == 'product') )
		{
			$layoutName = 'default'; 
			JRequest::setVar('layout', 'default'); 
			$invalidlayout = true; 
		}
		 $task = JRequest::getVar('task', null); 
		 if ($task == 'emptycart')
		  {
		     $cart->emptyCart(); 
		  }
		// fix add to cart on broken scripts
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		if (is_array($product))
		 {
		   
			if (empty($task) || ($invalidlayout))
			 {
					$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
					if ($cart->add($virtuemart_product_ids,$success))
					{
					 $msg = JText::_('COM_VIRTUEMART_PRODUCT_ADDED_SUCCESSFULLY');
					 $mainframe->enqueueMessage($msg);
					}
			 }
		 }
		 
		
		
		
		
		$this->assignRef('layoutName', $layoutName);
		$format = JRequest::getWord('format');
		// if(!class_exists('virtueMartModelCart')) require(JPATH_VM_SITE.DS.'models'.DS.'cart.php');
		// $model = new VirtueMartModelCart;

		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		// was till 2.0.126: $cart = VirtueMartCart::getCart(false, true);
		
		// do not allow update of shipment or payment from user object: 
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$userModel = OPCmini::getModel('user');
		if (method_exists($userModel, 'getCurrentUser'))
		{
		$user = $userModel->getCurrentUser();
		if (!empty($user->virtuemart_shipmentmethod_id))
		{
		$user->virtuemart_shipmentmethod_id = 0; 
		$user->virtuemart_paymentmethod_id = 0; 
	    }
		}
		
		if (!isset($cart->vendorId))
	    {
	     $cart->vendorId = 1; 
	    }
		
		
		$this->assignRef('cart', $cart);

		
		
		
			$VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
			
			$exhtml = $OPCloader->addtocartaslink($this); 
			if (empty($exhtml)) $exhtml = ''; 
		
		$this->user = $OPCloader->getUser($cart); 
		
		
		//Why is this here, when we have view.raw.php
		if ($format == 'raw') {
		    if (method_exists($cart, 'prepareCartViewData'))
			$cart->prepareCartViewData();
			JRequest::setVar('layout', 'mini_cart');
			$this->setLayout('mini_cart');
			$this->prepareContinueLink();
		}
		$opclayouts = array('select_payment', 'select_shipment', 'default');
		/*
	  if($layoutName=='edit_coupon'){

		$cart->prepareCartViewData();
		$this->lSelectCoupon();
		$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'),JRoute::_('index.php?option=com_virtuemart&view=cart'));
		$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_SELECTCOUPON'));
		$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_SELECTCOUPON'));

		} else */
		if ($layoutName == 'order_done') {

			$language = JFactory::getLanguage();
			//$language->load('com_virtuemart', JPATH_SITE);

			$this->lOrderDone();

			$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
			$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
		} 
		else 
		if (in_array($layoutName, $opclayouts))
		{
			if ($layoutName == 'select_shipment') {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			}

			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart'));
			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
			//$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
		

			/* Load the cart helper */
			//			$cartModel = $this->getModel('cart');

			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart'));
			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
			//$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
		
			$cart->virtuemart_shipmentmethod_id = 0; 
			if (empty($isexpress))
			$cart->virtuemart_paymentmethod_id = 0; 
			$cart->setCartIntoSession();
			if (method_exists($calc, 'setCartPrices')) 
			{
			$calc->setCartPrices(array()); 
			}
			
			$calc->getCheckoutPrices($cart, false); 
			
			if (method_exists($cart, 'prepareAjaxData'));
		    $data = $cart->prepareAjaxData(false);

			if (method_exists($cart, 'prepareCartViewData'))
			$cart->prepareCartViewData();
			if (method_exists($cart, 'prepareAddressRadioSelection'))
			$cart->prepareAddressRadioSelection();

			$this->prepareContinueLink();
			$this->lSelectCoupon();
			
			
			$totalInPaymentCurrency =$this->getTotalInPaymentCurrency();
			if ($cart->getDataValidated()) {
				$pathway->addItem(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$document->setTitle(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$text = OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
				$checkout_task = 'confirm';
			} else {
				$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$text = OPCLang::_('COM_VIRTUEMART_CHECKOUT_TITLE');
				$checkout_task = 'checkout';
			}
			
			$this->assignRef('checkout_task', $checkout_task);
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			if ($cart->virtuemart_shipmentmethod_id) {
				$this->assignRef('select_shipment_text', OPCLang::_('COM_VIRTUEMART_CART_CHANGE_SHIPPING'));
			} else {
				$this->assignRef('select_shipment_text', OPCLang::_('COM_VIRTUEMART_CART_EDIT_SHIPPING'));
			}
			if ($cart->virtuemart_paymentmethod_id) {
				$this->assignRef('select_payment_text', OPCLang::_('COM_VIRTUEMART_CART_CHANGE_PAYMENT'));
			} else {
				$this->assignRef('select_payment_text', OPCLang::_('COM_VIRTUEMART_CART_EDIT_PAYMENT'));
			}
			
			if (!empty($cart) && (!empty($cart->pricesCurrency)))
			{
			$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
			$this->assignRef('currencyDisplay', $currencyDisplay); 
			}
			else
			{
			$currencyDisplay = CurrencyDisplay::getInstance();
			$this->assignRef('currencyDisplay', $currencyDisplay); 
			}
			
			if (!VmConfig::get('use_as_catalog')) {
				$checkout_link_html = '<a class="vm-button-correct" href="javascript:document.checkoutForm.submit();" ><span>' . $text . '</span></a>';
			} else {
				$checkout_link_html = '';
			}
			$this->assignRef('checkout_link_html', $checkout_link_html);
		}
		//dump ($cart,'cart');
		$useSSL = VmConfig::get('useSSL', 0);
		$useXHTML = true;
		$this->assignRef('useSSL', $useSSL);
		$this->assignRef('useXHTML', $useXHTML);
		$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
 
		// @max: quicknirty
		$cart->setCartIntoSession();
		shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
		
		// 		vmdebug('my cart',$cart);
		if (($layoutName == 'default') || ($layoutName == 'select_shipment') || ($layoutName=='select_payment')) 
		{
		 
		 $this->lSelectShipment();
		 $this->lSelectPayment();
		 

		
		 $this->prepareVendor($cart);
		 $only_page = JRequest::getCmd('only_page', ''); 
		 $inside = JRequest::getCmd('insideiframe', ''); 
		 
		$url = JURI::base(true); 
		if (empty($url)) $url = '/'; 
		if (substr($url, strlen($url)-1)!=='/') $url .= '/'; 

		 
		 if (!empty($only_page) && (empty($inside))) 
		 {
		   echo '<iframe id="opciframe" src="'.JRoute::_($url.'index.php?option=com_virtuemart&view=cart&insideiframe=1&template=system').'" style="width: 100%; height: 2000px; margin:0; padding:0; border: 0 none;" ></iframe>'; 
		 }
		 if ($inside)
		 {
			$document->addStyleDeclaration('
			  body { width:95% !important; }
			  div#dockcart { display: none !important; }
			  '); 
		 }
		 $document->addStyleDeclaration('
			  
			  div#dockcart { display: none !important; }
			  '); 
		 @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		 @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		 
		 //$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart')); 
		 if (empty($only_page) || (!empty($inside)))
		 $this->renderOnepage($cart, $exhtml, $isexpress, $OPCloader);
		 
		 $cart->virtuemart_shipmentmethod_id = 0; 
		 if (empty($isexpress))
		 $cart->virtuemart_paymentmethod_id = 0; 
		 $cart->setCartIntoSession();
		}
		else
		{
		
		parent::display($tpl);
		}
		
	
		
		
	}
	
	public function renderOnepage(&$cart, $exhtml, $isexpress, $OPCloader)
	{
		
		OPCloader::loadJavascriptFiles($this); 			
		
		
		/* include all cart files */
			   	if(!class_exists( 'VirtueMartControllerVirtuemart' )) require(JPATH_VM_SITE.DS.'controllers'.DS.'virtuemart.php');

		$controller = new VirtueMartControllerVirtuemart();
		$controller->addViewPath( JPATH_VM_SITE.DS.'views' );
		
		$controllerClassName = 'VirtueMartControllerCart';
		if(!class_exists( $controllerClassName )) require(JPATH_VM_SITE.DS.'controllers'.DS.'cart.php');
		$this->addTemplatePath( JPATH_VM_SITE.'/views/cart/tmpl' );

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
			$this->addTemplatePath( JPATH_ROOT.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.'cart' );
		} 
			


		/* end include */ 
		if ($OPCloader->logged($cart))
			 {
			   	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
				$c = new VirtueMartControllerOpc(); 
				
				$c->setAddress($cart, true, false, true); 
				
			 }		
		
		 
		$cart_start = VirtueMartCart::getCart(false);
		
	$mainframe =& JFactory::getApplication(); 
	$useSSL = VmConfig::get('useSSL', 0);
	$mainframe =& JFactory::getApplication(); 
	
	
	
	if ((!empty($useSSL)))	
	{
	
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) {

      $isHttps = true;
	}
	else $isHttps = false;
	
	
	if 	((empty($isHttps)))
	if (empty($_SESSION['op_redirected']))
	{
		$urlh = 'index.php?option=com_virtuemart&view=cart'; 
		$lang = JRequest::getVar('lang', ''); 
		if (!empty($lang))
		$urlh .= '&lang='.$lang; 
		
		$add_id = JRequest::getVar('add_id', array()); 
		if (!empty($add_id))
		 {
		   if (is_array($add_id))
		   {
		   foreach ($add_id as $key=>$val)
		     {
			    $urlh .= '&add_id[]='.$val; 
				$q = JRequest::getVar('qadd_'.$val); 
				if (!empty($q))
				 {
				   $urlh .= '&qadd_'.$val.'='.$q; 
				 }
			 }
		   }
		   else 
		    {
			  $urlh .= '&add_id='.$add_id; 
			  $q = JRequest::getVar('qadd'); 
			  if (!empty($q)) $urlh .= 'quadd='.$q; 
			}
		 }
		$theme = JRequest::getVar('opc_theme'); 
		
		if (!empty($theme))
		$urlh .= '&opc_theme='.$theme; 
		
		$url = JRoute::_($urlh, false, true);
		$_SESSION['op_redirected'] = true;
		$mainframe->redirect($url);
		
	}
	unset($_SESSION['op_redirected']); 
	}
	
	
	
	 
	
	    $language = JFactory::getLanguage();
		//$language->load('com_onepage', JPATH_SITE, 'en-GB', true);
		//$language->load('com_onepage', JPATH_SITE, null, true);
		
		
	    //require_once(JPATH_VM_ADMINISTRATOR.DS.'version.php'); 
	    require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php');  
		//$x = version_compare(vmVersion::$RELEASE, '2.0.0', '>'); 
	    
		// in j1.7+ we have a special case of a guest login where user is logged in (and does not know it) and the registration fields in VM don't show up
			
		   //here we decide if to unlog user before purchase if he is somehow logged
		   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 

		
		 
		   if (!empty($opc_calc_cache))
		   {
			 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 
		     OPCcache::installCache(); 
		   }
		   
		   if ($unlog_all_shoppers)
		   {
		     	$currentUser =& JFactory::getUser();
				$uid = $currentUser->get('id');
				if (!empty($uid))
				 {
				   
				  
				   $mainframe->logout(); 
				 }

		   }
		   
		   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
		   $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
		   
	       if (!empty($newitemid))
		   {
		     if (is_numeric($newitemid))
			  {
			   JRequest::setVar('Itemid', $newitemid); 
			   $GLOBALS['Itemid'] = $newitemid; 
			  }
		   }
		  
		  $op_disable_shipto = OPCloader::getShiptoEnabled($this->cart); 
		 
        
		   
			$this->cart = $cart;
			
			$OPCloader->addtocartaslink($this); 
			$continue_link = $OPCloader->getContinueLink($this); 
		    if (empty($cart) || (empty($cart->products)))
			{
			  $tpla = array('continue_link' => $continue_link); 
			}
			else
			{
			
			// we will force this plugins to load for tracking and similar: 
			$advertises = $this->getCheckoutAdvertise(); 
			
			if (!empty($hide_advertise))
			$advertises = array(); 

			$advertises2 = $OPCloader->getAdminTools($this); 
			if (!empty($advertises2)) $advertises[] = $advertises2; 

			
			$VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
		
		
			
			
			


			//if (empty($ajaxify_cart))
			$op_coupon = $OPCloader->getCoupon($this);
			//else $op_coupon = ''; 
			
			$min_reached = OPCloader::checkPurchaseValue($cart); 
			
		    
			
			$op_userfields = $OPCloader->getBTfields($this); 
			
			$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
			
			if (empty($op_disable_shipping))
			{
			if (!$this->checkShipmentMethodsConfigured()) 
			$no_shipping = $op_disable_shipping; 
			else
			$no_shipping = true; 
			}
			else
			$no_shipping = 1; 
			

			

			
			$num = 0; 
			if (empty($cart->BT['virtuemart_country_id']))
			{
				$bhelper = new basketHelper;	
				$bhelper->createDefaultAddress($this, $this->cart); 	
				$op_payment_a = $OPCloader->getPayment($this, $num, false, $isexpress); 
				$op_payment = $op_payment_a['html']; 
				$bhelper->restoreDefaultAddress($this, $this->cart);
			}
			else
			{
			
			 if (!class_exists ('calculationHelper')) {
			 require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
			 }
			 
			 /*
			 $calc = calculationHelper::getInstance ();
			 if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
			 else $vm2015 = false; 
			 
			 OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc');
			 */
			 $op_payment_a = $OPCloader->getPayment($this, $num, false, $isexpress); 
			 $op_payment = $op_payment_a['html']; 
			}
			
			
			
			$op_payment = '<div id="payment_html">'.$op_payment.'</div>'; 
			$shipping_method_html = $OPCloader->getShipping($this, $cart, false); 
			
			
			
			$op_basket = $OPCloader->getBasket($this, true, $op_coupon, $shipping_method_html, $op_payment, $isexpress); 
			$hascoupon = stripos($op_basket, $op_coupon); 
			
			if ($hascoupon) $op_coupon = '';  
			
			if ($shipping_inside_basket)
			{
			  $shipping_method_html = '<input type="hidden" name="virtuemart_shipmentmethod_id" value="" id="new_shipping" />'; 
			}
			if (($payment_inside_basket) && (empty($isexpress)))
			{
			  $op_payment = '<input type="hidden" name="virtuemart_paymentmethod_id" value="" id="new_payment" />'; 
			}
			
			  $op_payment .= '<div id="payment_extra_outside_basket">'; 
			  if (!empty($op_payment_a['extra']))
			   { 
			     //foreach ($op_payment_a['extra'] as $key=>$hp)
				  {
				  
				    foreach ($op_payment_a['extra'] as $ht)
				    $op_payment .= $ht; 
					
					
				  }
			   }
			   $op_payment .= '</div>'; 
			  
			
			
			
			
			
			if (empty($no_login_in_template))
			$registration_html = $OPCloader->getRegistrationHhtml($this);
			else $registration_html = ''; 
			
			
			
			$jsvalidator = $OPCloader->getJSValidatorScript($this); 
			
			$return_url = $OPCloader->getReturnLink($this); 
			
			
			
			
			
			
			
			
			if (((!empty($hide_payment_if_one) && ($num === 1)) || (($payment_inside_basket))) || (!empty($isexpress)))
			$force_hide_payment = true; 
			else $force_hide_payment = false; 
			
			
			
			
			$op_shipto = $OPCloader->getSTfields($this); 
			$op_formvars = $OPCloader->getFormVars($this).$jsvalidator;
			
			
			$op_userfields .= $op_formvars.$exhtml; 
			
			
			$OPCloader->getJavascript($this, $isexpress); 

			$action_url = $OPCloader->getActionUrl($this); 
			
			//   if ($onlyindex) return JURI::root(true).'/index.php'; 
			$action_url = JURI::root(true).'/index.php?option=com_virtuemart&amp;view=opc&amp;controller=opc&amp;task=checkout&amp;nosef=1';
			
			
			if (!empty($op_customitemidty))
			$action_url .= '&Itemid='.$op_customitemidty; 
			
			$captcha = $OPCloader->getCaptcha($this); 

			$OPCloader->getMainJs(); 
			
			
			
			$op_login_t = ''; 
			$html_in_between = $OPCloader->getHtmlInBetween($this);
			
			//$op_userfields = ''; 
			
			$shippingtxt = ''; 
			$chkship = ''; 
			
			
			
			$tos_required = $OPCloader->getTosRequired($this); 
			
			$op_tos = ''; 
			$extras = $OPCloader->getExtras($this); 
			$tos_con = $OPCloader->getTos($this); 
			$agreement_txt = ''; 
			$show_full_tos = $OPCloader->getShowFullTos($this); //VmConfig::get('oncheckout_show_legal_info', 0); 
			
			$agree_checked = intval(!$agreed_notchecked); 
			$intro_article = $OPCloader->getIntroArticle($this); 


			$italian_checkbox = $OPCloader->getItalianCheckbox($this); 

			// 202 transform old themes; 
			$op_shipto = str_replace('"showSA', '"Onepage.showSA', $op_shipto); 
			$op_shipto = str_replace('"showSA', '"Onepage.showSA', $op_shipto); 
			$op_shipto = str_replace(' op_unhide', ' Onepage.op_unhide', $op_shipto); 
			 
			$google_checkout_button = ''; 
			$paypal_express_button = ''; 
			$related_products = ''; 
			$onsubmit = $OPCloader->getJSValidator($this);
			$op_onclick = $onsubmit; 
			$ref = $this;
			$tos_link = $OPCloader->getTosLink($this); 
			$tpla = Array(
			"force_hide_payment" => $force_hide_payment, 
			"hide_payment" => $force_hide_payment,
			"min_reached_text" => $min_reached,
			"checkoutAdvertises" => $advertises, 
			"intro_article" => $intro_article, 
			"return_url" => $return_url, 
			"captcha" 	=> $captcha, 
			"no_shipping" => $no_shipping,
			"op_onclick" => ' onclick="'.$onsubmit.'" ', 
			"no_shipto" => NO_SHIPTO, 
			"action_url" => $action_url,
			"tos_required" => $tos_required,
			"op_userinfo_st" => "",
            "op_basket" => $op_basket,
            "op_coupon" => $op_coupon, 
            "html_in_between" => $html_in_between, 
            "continue_link" => $continue_link, 
            "op_login_t" => $op_login_t,
            "shipping_method_html" => $shipping_method_html.JHtml::_('form.token'),
            "op_userfields" => $op_userfields,
            "shippingtxt" => $shippingtxt,
            "chkship" => $chkship,
            "op_shipto" => $op_shipto,
            "op_tos" => $op_tos,
             "op_payment" => $op_payment.JHtml::_('form.token'),
             "tos_con" => $tos_con, 
             "agreement_txt" => $agreement_txt,
             "show_full_tos" => $show_full_tos,
             "google_checkout_button" => $google_checkout_button,
             "paypal_express_button" => $paypal_express_button,
             "related_products" => $related_products, 
             "registration_html" => $registration_html,
			 "onsubmit" => $onsubmit,
			 "tos_link" => $tos_link,

          
           ) ;
			}


			
			include_once(JPATH_OPC.DS.'helpers'.DS.'legacy_templates.php');  
			
			
			// will not be included inside form
			if (!empty($extras)) echo $extras; 
			// newScript.onload=scriptLoaded;
			
		    $cart = VirtueMartCart::getCart(false);
			$calc = calculationHelper::getInstance();
			if (method_exists($calc, 'setCartPrices')) 
			{
			$calc->setCartPrices(array()); 
			}
			if (method_exists($cart, 'prepareAjaxData'));
		    $data = $cart->prepareAjaxData(false);
			
			
			// disable execution of other plugins after rendering: 
			//JRequest::setVar('view', 'opccart'); 
			//JRequest::setVar('controller', 'opc'); 
			
	//register cart: 
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('registerCartEnter', array());  
	
			
}
	/*
	 * Trigger to place Coupon, payment, shipment advertisement on the cart
	 */
	public function getCheckoutAdvertise() {
		$checkoutAdvertise=array();
		JPluginHelper::importPlugin('vmcoupon');
		JPluginHelper::importPlugin('vmpayment');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertise));
		
		return $checkoutAdvertise;
	}
	
	
	public function logged($cart)
	{
	  //$OPCloader = new OPCloader; 
	  return OPCloader::logged($cart); 
	}
	public function renderMailLayout($doVendor=false) {
		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

		$cart = VirtueMartCart::getCart(false);
		$this->assignRef('cart', $cart);
		if (method_exists($cart, 'prepareCartViewData'))
		$cart->prepareCartViewData();
		$cart->prepareMailData();

		if ($doVendor) {
			$this->subject = OPCLang::sprintf('COM_VIRTUEMART_VENDOR_NEW_ORDER_CONFIRMED', $this->shopperName, $this->cart->prices['billTotal'], $this->order['details']['BT']->order_number);
			$recipient = 'vendor';
		} else {
			$this->subject = OPCLang::sprintf('COM_VIRTUEMART_ACC_ORDER_INFO', $this->cart->vendor->vendor_store_name, $this->cart->prices['billTotal'], $this->order['details']['BT']->order_number, $this->order['details']['BT']->order_pass);
			$recipient = 'shopper';
		}
		$this->doVendor = true;
		if (VmConfig::get('order_mail_html'))
		$tpl = 'mail_html';
		else
		$tpl = 'mail_raw';
		$this->assignRef('recipient', $recipient);
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$vendorModel = OPCmini::getModel('vendor');
		$this->vendorEmail = $vendorModel->getVendorEmail($cart->vendor->virtuemart_vendor_id);
		
		$this->layoutName = $tpl;
		$this->setLayout($tpl);
		parent::display();
	}
	
	public function prepareContinueLink() {
		// Get a continue link */
		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		$categoryLink = '';
		if ($virtuemart_category_id) {
			$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		}
		$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink);

		$continue_link_html = '<a class="continue_link" href="' . $continue_link . '" >' . OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
		$this->assignRef('continue_link_html', $continue_link_html);
		$this->assignRef('continue_link', $continue_link);
		
		$menuid = JRequest::getVar('Itemid','');
		if(!empty($menuid)){
			$menuid = '&Itemid='.$menuid;
		}
		$this->cart_link = JRoute::_('index.php?option=com_virtuemart&view=cart'.$menuid, FALSE);
		$this->assignRef('cart_link', $cart_link);
	}

	public function lSelectCoupon() {

		$this->couponCode = (isset($this->cart->couponCode) ? $this->cart->couponCode : '');
		$coupon_text = $this->cart->couponCode ? OPCLang::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : OPCLang::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
		
		$this->assignRef('coupon_text', $coupon_text);
	}

	/*
	 * lSelectShipment
	* find al shipment rates available for this cart
	*
	* @author Valerie Isaksen
	*/

	public function lSelectShipment() {
	  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	  if (!empty($op_delay_ship)) return;
	  if (!empty($op_disable_shipto)) return;
	  
	  $op_disable_shipping = OPCloader::getShippingEnabled(); 
	  if (!empty($op_disable_shipping)) return;
	  
	  $x = null; 
	  basketHelper::createDefaultAddress($x, $this->cart); 
	  // USPS returns redirect when no BT address is set here
	
		$found_shipment_method=false;
		$shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);

		$shipments_shipment_rates=array();
		if (!$this->checkShipmentMethodsConfigured()) {
			$this->assignRef('shipments_shipment_rates',$shipments_shipment_rates);
			$this->assignRef('found_shipment_method', $found_shipment_method);
			return;
		}
		$selectedShipment = (empty($this->cart->virtuemart_shipmentmethod_id) ? 0 : $this->cart->virtuemart_shipmentmethod_id);

		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $this->cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				break;
			}
		}
		$shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);
		$this->assignRef('shipments_shipment_rates', $shipments_shipment_rates);
		$this->assignRef('found_shipment_method', $found_shipment_method);
		$x = null; 
		basketHelper::restoreDefaultAddress($x, $this->cart); 
		return;
	}

	/*
	 * lSelectPayment
	* find al payment available for this cart
	*
	* @author Valerie Isaksen
	*/

	public function lSelectPayment() {
	return;
		// let's try deleyad payment
		//return;
		$payment_not_found_text='';
		$payments_payment_rates=array();
		if (!$this->checkPaymentMethodsConfigured()) {
			$this->assignRef('paymentplugins_payments', $payments_payment_rates);
			$this->assignRef('found_payment_method', $found_payment_method);
		}

		$selectedPayment = empty($this->cart->virtuemart_paymentmethod_id) ? 0 : $this->cart->virtuemart_paymentmethod_id;

		$paymentplugins_payments = array();
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($this->cart, $selectedPayment, &$paymentplugins_payments));
		// if no payment defined
		$found_payment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_payment_method = true;
				break;
			}
		}

		if (!$found_payment_method) {
			$link=''; // todo
			$payment_not_found_text = OPCLang::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}

		$this->assignRef('payment_not_found_text', $payment_not_found_text);
		$this->assignRef('paymentplugins_payments', $paymentplugins_payments);
		$this->assignRef('found_payment_method', $found_payment_method);
	}

	public function getTotalInPaymentCurrency() {

		if (empty($this->cart->virtuemart_paymentmethod_id)) {
			return null;
		}

		if (!$this->cart->paymentCurrency or ($this->cart->paymentCurrency==$this->cart->pricesCurrency)) {
			return null;
		}

		$paymentCurrency = CurrencyDisplay::getInstance($this->cart->paymentCurrency);

		$totalInPaymentCurrency = $paymentCurrency->priceDisplay( $this->cart->pricesUnformatted['billTotal'],$this->cart->paymentCurrency) ;

		$cd = CurrencyDisplay::getInstance($this->cart->pricesCurrency);


		return $totalInPaymentCurrency;
	}

	public function lOrderDone() {
		//$html = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'post', 'STRING', JREQUEST_ALLOWRAW);
		
		$display_title = (bool)JRequest::getVar('display_title',true);
		$this->assignRef('display_title', $display_title);
		
		$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
		$this->assignRef('html', $html);

		//Show Thank you page or error due payment plugins like paypal express
	}

	public function checkPaymentMethodsConfigured() {
		if (!class_exists('VirtueMartModelPaymentmethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'paymentmethod.php');
		//For the selection of the payment method we need the total amount to pay.
		$paymentModel = new VirtueMartModelPaymentmethod();
		$payments = $paymentModel->getPayments(true, false);
		if (empty($payments)) {

			$text = '';
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 			
			if (OPCmini::isSuperVendor())
			 {
			   $uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=paymentmethod';
				$text = OPCLang::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			 }
			
			
			
			if (!defined('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED'))
			{
			 
			 vmInfo('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text); 
			}
			

			$tmp = 0;
			$this->assignRef('found_payment_method', $tmp);

			return false;
		}
		return true;
	}

	public function checkShipmentMethodsConfigured() {
		if (!class_exists('VirtueMartModelShipmentMethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shipmentmethod.php');
		//For the selection of the shipment method we need the total amount to pay.
		$shipmentModel = new VirtueMartModelShipmentmethod();
		$shipments = $shipmentModel->getShipments();
		if (empty($shipments)) {

			$text = '';
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			if (OPCmini::isSuperVendor())
			 {
			   $uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=shipmentmethod';
				$text = vmText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED_LINK', '<a href="' . $link . '" rel="nofollow">' . $link . '</a>');
			 }
			
			
		
			if (!defined('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED'))
			{
			 vmInfo('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text); 
			}

			$tmp = 0;
			$this->assignRef('found_shipment_method', $tmp);

			return false;
		}
		return true;
	}

	public function stylesheet($file, $path, $arg=array())
	{
	  $onlypage = JRequest::getCmd('only_page', ''); 
	  if (false)
	  if (!empty($onlypage))
	  {
	    echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		 // content of your Javascript goes here
		 var headID = document.getElementsByTagName("head")[0];    
		 var cssNode = document.createElement(\'link\'); 
		 cssNode.type = \'text/css\';
		 cssNode.rel = \'stylesheet\';
		 cssNode.href = \''.$path.$file.'\';
		 cssNode.media = \'screen\';
		 headID.appendChild(cssNode);
		
		/* ]]> */
		</script>';

	  }
	  //else
	  JHTMLOPC::stylesheet($file, $path, $arg);
	}
	public function script($file, $path, $arg, $onload="")
	{
	  
	 
	  
	  JHTMLOPC::script($file, $path, $arg);
	  
	}
	
	/**
	 * moved to shopfunctionf
	 * @deprecated
	 */
	// add vendor for cart
	public function prepareVendor(&$cart){
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$vendorModel = OPCmini::getModel('vendor');
		$cart->vendor = $vendorModel->getVendor();
		$vendorModel->addImages($cart->vendor,1);
		return $cart->vendor;
	}
	
	public function getUserList() {
		$db = JFactory::getDbo();
		$q = 'SELECT * FROM #__users ORDER BY name';
		$db->setQuery($q);
		$result = $db->loadObjectList();
		foreach($result as $user) {
			$user->displayedName = $user->name .'&nbsp;&nbsp;( '. $user->username .' )';
		}
		return $result;
	}


	
}

class op_languageHelper 
{
 public function _($val)
 {
   $v2 = str_replace('PHPSHOP_', 'COM_VIRTUEMART_', $val); 
   return OPCLang::_($v2);  
 }
 public function load($str='')
 {
 }
}
//no closing tag
if (!function_exists('mm_showMyFileName'))
{
function mm_showMyFileName()
{
}
}
if (!function_exists('vmIsJoomla'))
{
 function vmIsJoomla()
 {
   return false;
 }
}

