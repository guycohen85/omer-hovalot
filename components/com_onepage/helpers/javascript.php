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

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCJavascript
{

function getJavascript(&$ref, &$OPCloader, $isexpress=false)
 {
   
   //include (JPATH_OPC.DS.'ext'.DS.'extension.php');
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 
   
   $bhelper = new basketHelper; 

   //$extHelper = new opExtension();
   //$extHelper->runExt('before');

   include(JPATH_OPC.DS.'config'.DS.'onepage.cfg.php'); 
   
  // $ccjs = "\n".' var op_general_error = "'.OPCmini::slash(JText->_('CONTACT_FORM_NC')).'"; '."\n";
  // $ccjs .= ' var op_cca = "~';
   // COM_VIRTUEMART_ORDER_PRINT_PAYMENT
   
    $logged = $OPCloader->logged($ref->cart);

	$user = JFactory::getUser(); 
	
	if ($user->id > 0)
	$logged_in_joomla = true; 
	else 
	$logged_in_joomla = false; 

	// check if klarna enabled
	 // let's include klarna from loadScriptAndCss: 
$db = JFactory::getDBO(); 
$q = "select published from #__virtuemart_paymentmethods where payment_element = 'klarna' limit 0,1"; 
$db->setQuery($q); 
$enabled = $db->loadResult();

if (!empty($enabled))
{
		if (file_exists(JPATH_ROOT.DS.'plugins'.DS.'vmpayment'.DS.'klarna'.DS.'klarna.php'))
			$path = 'plugins/vmpayment/klarna';
	    else 
				$path = 'plugins/vmpayment';
 		$assetsPath = $path . '/klarna/assets/';
		JHTMLOPC::stylesheet ('style.css', $assetsPath . 'css/', FALSE);
		JHTMLOPC::stylesheet ('klarna.css', $assetsPath . 'css/', FALSE);
		JHTMLOPC::script ('klarna_general.js', $assetsPath . 'js/', FALSE);
		JHTMLOPC::script ('klarnaConsentNew.js', 'http://static.klarna.com/external/js/', FALSE);
		$document = JFactory::getDocument ();
		
		$document->addScriptDeclaration ('
		 klarna.ajaxPath = "' . juri::root () . '/index.php?option=com_virtuemart&view=plugin&vmtype=vmpayment&name=klarna";
	');
}
 // end

	// end
	
	
   
   	$extJs = " var shipconf = []; var payconf = []; "."\n";
	
	$virtuemart_currency_id = OPCloader::getCurrency($ref->cart); 
	$extJs .= " var virtuemart_currency_id = '".$virtuemart_currency_id."'; "; 
	
	//testing: 
	
	if (!empty($opc_dynamic_lines))
	$extJs .= " var opc_dynamic_lines = true; "; 
	else
	$extJs .= " var opc_dynamic_lines = false; "; 
	
	
	if ($opc_debug)
	$extJs .= " var opc_debug = true; "; 
	else
	$extJs .= " var opc_debug = false; "; 
	
	if (!empty($op_customer_shipping))
	$extJs .= " var op_customer_shipping = true; "; 
	else
	$extJs .= " var op_customer_shipping = false; "; 
	
	if ($opc_async)
	$extJs .= " var opc_async = true; "; 
	else
	$extJs .= " var opc_async = false; "; 

	
	if ($payment_inside)
	$extJs .= " var op_payment_inside = true; "; 
	else
	$extJs .= " var op_payment_inside = false; "; 
	
	$extJs .= " var op_logged_in = '".$logged."'; "; 
	$extJs .= " var op_last_payment_extra = null; "; 
	$extJs .= " var op_logged_in_joomla = '".$logged_in_joomla."'; "; 
	$extJs .= ' var op_shipping_div = null; ';
	$extJs .= ' var op_lastq = ""; ';
	$extJs .= ' var op_lastcountry = null; var op_lastcountryst = null; ';
    $extJs .= ' var op_isrunning = false; '; 


	$extJs .= ' var COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING')).'"; ';
	$extJs .= ' var COM_ONEPAGE_PLEASE_WAIT_LOADING = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_PLEASE_WAIT_LOADING')).'"; ';
	$theme = JRequest::getVar('opc_theme', ''); 
	$theme = preg_replace("/[^a-zA-Z0-9_]/", "", $theme);
	$extJs .= ' var opc_theme = "'.OPCloader::slash($theme).'"; '; 
	$extJs .= ' var NO_PAYMENT_ERROR = "'.OPCloader::slash(OPCLang::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '')).'"; ';
	$extJs .= ' var JERROR_AN_ERROR_HAS_OCCURRED = "'.OPCloader::slash(OPCLang::_('JERROR_AN_ERROR_HAS_OCCURRED')).'"; ';
	$extJs .= ' var COM_ONEPAGE_PLEASE_WAIT = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_PLEASE_WAIT')).'"; ';
	//$extJs .= ' var USERNAMESYNTAXERROR = "'.JText::_('', true).'"; ';
	
	if (!empty($op_usernameisemail))
	$extJs .= ' var op_usernameisemail = true; '; 
	else 
	$extJs .= ' var op_usernameisemail = false; '; 
	$url = $OPCloader->getURL(true); 
	if (!empty($op_loader))
	{
	 
	  $extJs .= ' var op_loader = true; ';
	  
	  			
	}	
	else $extJs .= ' var op_loader = false; ';
	
   $extJs .= ' var op_loader_img = "'.$url.'media/system/images/mootree_loader.gif";';  
	
	if (!empty($double_email))
     if (!defined('op_doublemail_js'))
      {
        JHTMLOPC::script('doublemail.js', 'components/com_onepage/ext/doublemail/js/', false);
        define('op_doublemail_js', '1'); 
      }
	
	
	if (!empty($onlyd))
	$extJs .= ' var op_onlydownloadable = "1"; ';
	else $extJs .= ' var op_onlydownloadable = ""; ';

		
	if (!empty($op_last_field))
	$extJs .= ' var op_last_field = true; ';
	else $extJs .= ' var op_last_field = false; ';
	
	$extJs .= ' var op_refresh_html = ""; ';

	if (!empty($no_alerts))
	$extJs .= ' var no_alerts = true; ';
	else
	$extJs .= ' var no_alerts = false; ';
	
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	
	$extJs .= " var username_error = '".OPCmini::slash(OPCLang::sprintf('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME', OPCLang::_('COM_VIRTUEMART_USERNAME'))) ."';"; 
		$extJs .= " var email_error = '".OPCmini::slash(OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'))) ."';"; 
	if (!empty($opc_no_duplicit_username))
	{
		$extJs .= ' var opc_no_duplicit_username = true; ';
	}
	else
	{
		$extJs .= ' var opc_no_duplicit_username = false; ';
	}

   if (!empty($opc_no_duplicit_email))
	{
		$extJs .= ' var opc_no_duplicit_email = true; ';
	}
	else
	{
		$extJs .= ' var opc_no_duplicit_email = false; ';
	}

	
	$extJs .= ' var last_username_check = true; ';
	$extJs .= ' var last_email_check = true; ';
	// stAn mod for OPC2
	/*
	if (!empty($op_delay_ship))
	$extJs .= " var op_delay = true; ";
	else $extJs .= " var op_delay = false; ";
	*/


	if (!empty($op_delay_ship))
	$extJs .= " var op_delay = false; ";
	else $extJs .= " var op_delay = false; ";

	
	if (empty($last_ship2_field)) $last_ship2_field = ''; 
	if (empty($last_ship_field)) $last_ship_field = ''; 
	
	$extJs .= " var op_last1 = '".OPCmini::slash($last_ship_field)."'; ";
	$extJs .= " var op_last2 = '".OPCmini::slash($last_ship2_field)."'; ";

	
	$url = JURI::root(true); 
	if (empty($url)) $url = '/'; 
	if (substr($url, strlen($url)-1)!=='/') $url .= '/'; 
	$actionurl = $url.'index.php'; 
 if(version_compare(JVERSION,'2.5.0','ge')) {
	$extJs .= " var op_com_user = 'com_users'; "; 
	$extJs .= " var op_com_user_task = 'user.login'; "; 
	
	$extJs .= " var op_com_user_action = '".$actionurl."?option=com_users&task=user.login&controller=user'; "; 
	$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_users&task=user.logout&controller=user'; "; 
	$extJs .= " var op_com_user_task_logout = 'user.logout'; "; 
  
 }
 else
 if(version_compare(JVERSION,'1.7.0','ge')) {
	$extJs .= " var op_com_user = 'com_users'; "; 
	$extJs .= " var op_com_user_task = 'user.login'; "; 
	$extJs .= " var op_com_user_action = '".$actionurl."?option=com_users&task=user.login&controller=user'; "; 
	$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_users&task=user.logout&controller=user'; "; 
	$extJs .= " var op_com_user_task_logout = 'user.logout'; "; 


 // Joomla! 1.7 code here
} elseif(version_compare(JVERSION,'1.6.0','ge')) {
// Joomla! 1.6 code here
} else {	
	$extJs .= " var op_com_user = 'com_user'; "; 
	$extJs .= " var op_com_user_task = 'login'; "; 
	$extJs .= " var op_com_user_action = '".$actionurl."?option=com_user&task=login'; "; 
	$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_user&task=logout'; "; 
	$extJs .= " var op_com_user_task_logout = 'logout'; "; 

	}
	
	$op_autosubmit = false;
	//$extHelper->runExt('autosubmit', '', '', $op_autosubmit);
	
	
	$extJs .= " var op_userfields_named = new Array(); ";
	if (!empty(OPCloader::$fields_names))
	 {
	   foreach (OPCloader::$fields_names as $key=>$val)
	    {
		  $extJs .= ' op_userfields_named[\''.OPCmini::slash($key).'\'] = \''.OPCmini::slash($val).'\'; ';  
		}
	 }
	$extJs .= " "; 
	// let's create all fields here
	
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		if (!isset($ref->cart))
	    $ref->cart = $cart = VirtueMartCart::getCart();
	
	{
	$extJs .= " var op_userfields = new Array("; 
	
	// updated on VM2.0.26D:
	/*
	if (!isset($ref->cart->STaddress)) $ref->cart->STaddress = array(); 
	if (!isset($ref->cart->BTaddress)) $ref->cart->BTaddress = array(); 
	$ref->cart->prepareAddressDataInCart('BTaddress', 0);
	$ref->cart->prepareAddressDataInCart('STaddress', 0);
	
	//$ref->cart->prepareAddressDataInCart('BT', 0);
	//$ref->cart->prepareAddressDataInCart('ST', 0);
	*/
	//$userFieldsST = $ref->cart->STaddress;
	$userFieldsST = OPCloader::getUserFields('ST', $ref->cart); 
	//$userFieldsBT = $ref->cart->BTaddress;
	$userFieldsBT = OPCloader::getUserFields('BT', $ref->cart); 
	$fx = array(); 
	
	$ignore = array('delimiter', 'hidden'); 
	foreach ($userFieldsBT['fields'] as $k2=>$v2)
	 {
	   if (in_array($v2['type'], $ignore)) continue;
	   $fx[] = '"'.OPCmini::slash($v2['name'], false).'"'; 
	 }
    foreach ($userFieldsST['fields'] as $k=>$v)
	 {
	   if (in_array($v['type'], $ignore)) continue;
	   
	   $fx[] = '"'.OPCmini::slash($v['name'], false).'"'; 
	 }

	$fx2 = implode(',', $fx); 
	$extJs .= $fx2.'); '; 
	}
	//else
	//$extJs .= " var op_userfields = new Array(); "; 
	
	
	$extJs .= ' var op_firstrun = true; ';
	//$extHelper->runExt('addjavascript', '', '', $extJs);
	
	if (!empty($business_fields))
	  {
	    $business_fields2 = array(); 
	    foreach ($business_fields as $k=>$line)
		 {
		   $business_fields2[$k] = "'".$line."'"; 
		 }
		 $newa = implode(',', $business_fields2); 
	    $extJs .= ' var business_fields = ['.$newa.']; ';
		 
	  }
	  else $extJs .= ' var business_fields = new Array(); '; 
	  
	  
	  
	   if (!empty($custom_rendering_fields))
	  {
	    $custom_rendering_fields2 = array(); 
	    foreach ($custom_rendering_fields as $k=>$line)
		 {
		   $custom_rendering_fields2[$k] = "'".$line."'"; 
		 }
		 $newa = implode(',', $custom_rendering_fields2); 
	    $extJs .= ' var custom_rendering_fields = new Array('.$newa.'); ';
		 
	  }
	  else $extJs .= ' var custom_rendering_fields = new Array(); '; 
	
	//shipping_obligatory_fields
	   if (!empty($shipping_obligatory_fields))
	  {
	    $shipping_obligatory_fields2 = array(); 
	    foreach ($shipping_obligatory_fields as $k=>$line)
		 {
		   $shipping_obligatory_fields2[$k] = "'".$line."'"; 
		 }
		 $newa = implode(',', $shipping_obligatory_fields2); 
	    $extJs .= ' var shipping_obligatory_fields = new Array('.$newa.'); ';
		 
	  }
	  else $extJs .= ' var shipping_obligatory_fields = new Array(); '; 
	
	$extJs .= 'var shippingOpenStatus = false; '; 
	
	
	if (empty($op_autosubmit))
	$extJs .= " var op_autosubmit = false; ";
	else 
	{ 
	 $extJs .= " var op_autosubmit = true; ";
	
	}
	$db=JFactory::getDBO();
	$q = 'select * from #__virtuemart_vendors where virtuemart_vendor_id = 1 limit 0,1 '; 
	$db->setQuery($q); 
	$res = $db->loadAssoc(); 
	if (!empty($res)) extract($res); 
	
	//VmConfig::get('useSSL',0)
	
	$mainframe = Jfactory::getApplication();
$vendorId = JRequest::getInt('vendorid', 1);

/* table vm_vendor */

if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	 
if (!class_exists('CurrencyDisplay'))
require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');

$virtuemart_currency_id = OPCloader::getCurrency($ref->cart); 
if (empty($ref->cart))
{
  $ref->cart = $cart = VirtueMartCart::getCart();
}

if (!empty($virtuemart_currency_id))
$c = CurrencyDisplay::getInstance($virtuemart_currency_id);
else
{	
	$c = CurrencyDisplay::getInstance($ref->cart->paymentCurrency);
	$virtuemart_currency_id = $ref->cart->paymentCurrency;
}

	



	// op_vendor_style = '1|&euro;|2|.|\'|3|0'; 
	$arr = array(); 
	$arr[0] = '1'; 
	$arr[1] = $c->getSymbol(); 
	$arr[2] = $c->getNbrDecimals(); 
	$arr[3] = $c->getDecimalSymbol(); 
	$arr[4] = $c->getThousandsSeperator(); 
	// for now
	$arr[5] = '3';
	$arr[6] = '8';
	$arr[7] = '8';
	$arr[8] = $c->getPositiveFormat(); 
	$arr[9] = $c->getNegativeFormat(); 
	$vendor_currency_display_style = implode('|', $arr);
	//$arr[2] = $c->
	$extJs .= " var op_saved_shipping = null; var op_saved_payment = null; var op_saved_shipping_vmid = '';";
	
	$cs = str_replace("'", '\\\'', $vendor_currency_display_style);
	$extJs .= " var op_vendor_style = '".$cs."'; ";
	$extJs .= " var op_currency_id = '".$virtuemart_currency_id."'; "; 
	//if (!empty($override_basket) || (!empty($shipping_inside_basket)) || (!empty($payment_inside_basket)))
	{
	 $extJs .= ' op_override_basket = true; ';
	 $extJs .= ' op_basket_override = true; ';
	}
	/*
	else 
	{
	 $extJs .= ' op_override_basket = false; ';
	 $extJs .= ' op_basket_override = false; ';
	}
	*/
        // google adwrods tracking code here
        if (!empty($adwords_enabled[0]))
            {
             $extJs .= " var acode = '1'; ";
            }
            else
            {
              $extJs .= " var acode = '0'; ";
            }
	$lang = JRequest::getVar('lang'); 
	if (ctype_alnum($lang))
	$extJs .= " var op_lang = '".$lang."'; ";
	else
	$extJs .= " var op_lang = ''; ";
	
	$ur = JURI::root(true); 
	if (substr($ur, strlen($ur)-1)!= '/')
	 $ur .= '/';
	//$ur .= basename($_SERVER['PHP_SELF']);
	$mm_action_url = $ur;
	
	$isVm202 = false; 
	 if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php'))
		    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
		else 
		$isVm202 = true; 
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) $isVm202 = true; 

	
	if (!$isVm202)
	$extJs .= " var op_securl = '".$ur."index.php?option=com_onepage'; ";
	else
	$extJs .= " var op_securl = '".$ur."index.php?option=com_virtuemart'; ";
	
	$extJs .= " var pay_btn = new Array(); "; 
	$extJs .= " var pay_msg = new Array(); "; 
	$extJs .= " pay_msg['default'] = ''; ";
	
    $extJs .= " pay_btn['default'] = '".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'))."'; ";

        $extJs .= " var op_timeout = 0; ";
		if (!empty($adwords_timeout))
        $extJs .= " var op_maxtimeout = ".$adwords_timeout."; ";
		else $extJs .= " var op_maxtimeout = 3000; ";
        $extJs .= " var op_semafor = false; ";
	if (!empty($op_sum_tax))
	{
	    $extJs .= " var op_sum_tax = true; ";
	}
	else
	{
	  $extJs .= " var op_sum_tax = false; ";
	}
	if (defined("_MIN_POV_REACHED") && (constant("_MIN_POV_REACHED")=='1'))
	{
	 $extJs .= " var op_min_pov_reached = true; ";
	}
	else
	{
	 $extJs .= " var op_min_pov_reached = false; ";
	}
	
	// this setting says if to show discountAmout together with the classic discount
	if (!empty($payment_discount_before))
	$extJs .= " var payment_discount_before = true; ";
	else
	$extJs .= " var payment_discount_before = false; ";
	
	if (empty($hidep) || (!empty($payment_inside)))
	{
	$extJs .= " var op_payment_disabling_disabled = true; ";
	}
	else
	{
	$extJs .= " var op_payment_disabling_disabled = false; ";
	}
	//$extJs .= " var op_show_prices_including_tax = '".$auth["show_price_including_tax"]."'; ";
	$extJs .= " var op_show_prices_including_tax = '1'; ";
	$extJs .= " var never_show_total = ";
	if ((isset($never_show_total) && ($never_show_total==true))) $extJs .= ' true; '."\n";
	else $extJs .= ' false; '."\n";
	$extJs .= " var op_no_jscheck = ";
	// modified for OPC2
	if (!empty($no_jscheck)) $extJs .= " true; "; else $extJs .= " true; ";
	$extJs .= " var op_no_taxes_show = ";
	if ((isset($no_taxes_show) && ($no_taxes_show==true))) $extJs .= ' true; '."\n";
	else $extJs .= ' false; '."\n";

	$extJs .= " var op_no_taxes = ";
	if ((isset($no_taxes) && ($no_taxes==true))) $extJs .= ' true; '."\n";
	else $extJs .= ' false; '."\n";
	
	$selectl = OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION');
	$extJs .= " var op_lang_select = '(".$selectl.")'; ";
	//if ((ps_checkout::tax_based_on_vendor_address()) && ($auth['show_price_including_tax']) && ((!isset($always_show_tax) || ($always_show_tax !== true))))
	//$extJs .= " var op_dont_show_taxes = '1'; ";
	//else
	$extJs .= " var op_dont_show_taxes = '0'; "."\n";
	$extJs .= ' var op_coupon_amount = "0"; '."\n";
	
	$extJs .= ' var op_shipping_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_PRICE_LBL'), false).'"; '."\n"; 
	$extJs .= ' var op_shipping_tax_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_TAX'), false).'"; '."\n"; 
  $country_ship = array();

    
	if (false)
	if (isset($hidep))
	foreach ($hidep as &$h)
	{ 
	  $h .= ','.$payments_to_hide.',';
	  $h = str_replace(' ', '', $h);
	  $h = ','.$h.',';
	}
	
	// found shipping methods
	// $sarr = $bhelper->getShippingArray();
	if (false)
	foreach ($sarr as $k=>$ship)
	{
	   if (isset($hidep[$ship->virtuemart_shipmentmethod_id]))
	   $extJs .= " payconf['".$k."']=\",".$hidep[$k].",\"; ";
	   else $extJs .= " payconf['".$k."']=\",\"; "; 
	  
	}
	// old code for standard shipping
	
	if (!empty($rows))
	foreach ($rows as $r)
	{
	 $id = $r['shipping_rate_id'];
	 $cs = $r['shipping_rate_country'];
	 $car = $r['shipping_rate_carrier_id'];
	 $k = explode(';', $cs, 1000);
	 foreach($k as $kk)
	 {
	  if ($kk!='')
	  {
	  $krajiny[] = $kk;
	  if (!isset($country_ship[$id]))
	    $country_ship[$id] = array();
	  $country_ship[$id][$kk] = $kk;
	  }
	 }
	 $extJs .= "shipconf[".$id."]=\"".$cs.'"; ';
	 
	}
		// end of old code for standard shipping
		
        
        // country_ship description:
        // country_ship[ship_id][country] = country
        // country_ship will be used for default shipping method for selected default shipping country
        
        // global variables: ordertotal, currency symbol, text for order total
//        echo $incship;
        $incship = OPCLang::_('COM_ONEPAGE_ORDER_TOTAL_INCL_SHIPPING'); 	
        if (empty($incship)) $incship = OPCLang::_('COM_VIRTUEMART_ORDER_LIST_TOTAL'); 		
        $incship = OPCmini::slash($incship);

	if (!empty($order_total))
        $extJs .= " var op_ordertotal = ".$order_total."; ";
         else $extJs .= " var op_ordertotal = 0.0; ";
        $extJs .= " var op_textinclship = '".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_CART_TOTAL'))."'; ";
        $extJs .= " var op_currency = '".OPCmini::slash($c->getSymbol())."'; ";
        if (!empty($weight_total))
        $extJs .= " var op_weight = ".$weight_total."; ";
        else $extJs .= " var op_weight = 0.00; ";
        if (!empty($vars['zone_qty']))
        $extJs .= " var op_zone_qty = ".$vars['zone_qty']."; ";
        else $extJs .= " var op_zone_qty = 0.00; ";
        if (!empty($grandSubtotal))
        $extJs .= " var op_grand_subtotal = ".$grandSubtotal."; ";
        else $extJs .= " var op_grand_subtotal = 0.00; ";
        $extJs .= ' var op_subtotal_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL'), false).'"; ';
        $extJs .= ' var op_tax_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX'), false).'"; ';
       
	     $op_disable_shipping = OPCloader::getShippingEnabled($ref->cart);
        if (!empty($op_disable_shipping))
        $nos = 'true'; 
		else 
		$nos = 'false';
		
        $extJs .= "var op_noshipping = ".$nos."; ";
		$extJs .= "var op_autosubmit = false; "; 
//        $extJs .= " var op_tok = '".$_SESSION['__default']['session.token']."'; ";
	// array of avaiable country codes
	if (!empty($krajiny))
	$krajiny = array_unique($krajiny);
   
	
	$rp_js = ''; 
	$extJs .= $rp_js."\n";
	$ship_country_change_msg = OPCLang::_('COM_ONEPAGE_SHIP_COUNTRY_CHANGED'); 
	$extJs .= ' var shipChangeCountry = "'.OPCmini::slash($ship_country_change_msg, false).'"; '."\n";
	$extJs .= ' var opc_free_text = "'.OPCmini::slash(OPCLang::_('COM_ONEPAGE_FREE', false)).'"; '."\n";
	
	if (!empty($use_free_text))
	$extJs .= " var use_free_text = true; "."\n";
	else
	$extJs .= " var use_free_text = false; "."\n";
	
	$ship_country_is_invalid_msg = OPCLang::_('COM_ONEPAGE_SHIP_COUNTRY_INVALID'); 
	$extJs .= ' var noshiptocmsg = "'.OPCmini::slash($ship_country_is_invalid_msg, false).'"; '."\n";
	$extJs .= " var default_ship = null; "."\n";
    $extJs .= ' var agreedmsg = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_NO', false)).'"; '."\n";
	$extJs .= ' var op_continue_link = ""; '."\n";
	if ($must_have_valid_vat)
        $extJs .= "var op_vat_ok = 2; var vat_input_id = \"".$vat_input_id."\"; var vat_must_be_valid = true; "."\n";
		$default_info_message = OPCLang::_('COM_ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO'); 
        $extJs .= ' var payment_default_msg = "'.str_replace('"', '\"', $default_info_message).'"; '."\n";
        $extJs .= ' var payment_button_def = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')).'"; '."\n";
	if (empty($op_dontloadajax))
	$extJs .= ' var op_dontloadajax = false; ';
	else
	$extJs .= ' var op_dontloadajax = true; ';
    
	$extJs .= ' var op_user_name_checked = false; ';
	$extJs .= ' var op_email_checked = false; ';
	// adds payment discount array
	//if (isset($pscript))
	//$extJs .= $pscript;
	if (isset($payments_to_hide))
	{
	 $payments_to_hide = str_replace(' ', '', $payments_to_hide);
	}
	else
	 $payments_to_hide = "";

	// adds script to change text on the button
	if (isset($rp))
	$extJs .= $rp;
	if (!((isset($vendor_name)) && ($vendor_name!='')))
	$vendor_name = 'E-shop';
	$extJs .= ' var op_vendor_name = "'.OPCmini::slash($vendor_name, false).'"; '."\n";

	/*
		if (!isset($_SESSION['__default']['session.token']))
	$_SESSION['__default']['session.token'] = md5(uniqid());
	$next_order_id = $bhelper->getNextOrderId(); 

	jimport( 'joomla.utilities.utility' );
	if (method_exists('JUtility', 'getToken'))
	$token = JUtility::getToken(); 
	else 
	$token = JSession::getFormToken(); 
	$token = md5($token); 
	$g_order_id = $next_order_id."_".$token;
	$extJs .= ' var g_order_id = "'.$g_order_id.'"; '."\n";
	*/
	
	$extJs .= ' var op_order_total = 0; '."\n";
	$extJs .= ' var op_total_total = 0; '."\n";
	$extJs .= ' var op_ship_total = 0; '."\n";
	$extJs .= ' var op_tax_total = 0; '."\n";
	if (empty($op_fix_ins))
	$extJs .= 'var op_fix_payment_vat = false; ';
	
	$extJs .= ' var op_run_google = new Boolean(';
	if (!empty($g_analytics))
	 $extJs .= 'true); ';
	else
	 $extJs .= 'false); ';
	if (!isset($pth_js)) 
	$pth_js = '';
    $extJs .= ' var op_always_show_tax = ';
    if (isset($always_show_tax) && ($always_show_tax===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
    
    $extJs .= ' var op_always_show_all = ';
    if (isset($always_show_all) && ($always_show_all===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
     
    $extJs .= ' var op_add_tax = ';
    if (isset($add_tax) && ($add_tax===true))
      $extJs .= 'true; ';
     else $extJs .= 'false; ';
    
    $extJs .= ' var op_add_tax_to_shipping = ';
    if (isset($add_tax_to_shipping) && ($add_tax_to_shipping===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";

    $extJs .= ' var op_add_tax_to_shipping_problem = ';
    if (isset($add_tax_to_shipping_problem) && ($add_tax_to_shipping_problem===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";


    $extJs .= ' var op_no_decimals = ';
    if (isset($no_decimals) && ($no_decimals===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";

    $extJs .= ' var op_curr_after = ';
    if (isset($curr_after) && ($curr_after===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
	
	if (empty($op_basket_subtotal_taxonly)) $op_basket_subtotal_taxonly = '0.00';
	$extJs .= ' var op_basket_subtotal_items_tax_only = '.$op_basket_subtotal_taxonly.'; ';
/*
	can be send to js if needed: 
			$op_basket_subtotal += $price["product_price"] * $cart[$i]["quantity"];
		$op_basket_subtotal_withtax += ($price["product_price"] * $cart[$i]["quantity"])*($my_taxrate+1);
		$op_basket_subtotal_taxonly +=  ($price["product_price"] * $cart[$i]["quantity"])*($my_taxrate);
*/

	$extJs .= ' var op_show_only_total = ';
    if (isset($show_only_total) && ($show_only_total===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
     
    $extJs .= ' var op_show_andrea_view = ';
    if (isset($show_andrea_view) && ($show_andrea_view===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
      
	$extJs .= ' var op_detected_tax_rate = "0"; ';
    $extJs .= ' var op_custom_tax_rate = ';
    if (empty($custom_tax_rate)) $custom_tax_rate = '0.00';
    $custom_tax_rate = str_replace(',', '.', $custom_tax_rate);
    $custom_tax_rate = str_replace(' ', '', $custom_tax_rate);
    if (!empty($custom_tax_rate) && is_numeric($custom_tax_rate))
      $extJs .= '"'.$custom_tax_rate.'"; '."\n";
     else $extJs .= '""; '."\n";

    $extJs .= ' var op_coupon_discount_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT'), false).'"; '."\n";

    $extJs .= ' var op_other_discount_txt = "'.OPCmini::slash(OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT'), false).'"; '."\n";

    
    if (!empty($shipping_inside_basket))
    {
     $extJs .= " var op_shipping_inside_basket = true; ";
    }
    else $extJs .= " var op_shipping_inside_basket = false; ";

    if (!empty($payment_inside_basket) && (empty($isexpress)))
    {
     $extJs .= " var op_payment_inside_basket = true; ";
    }
    else $extJs .= " var op_payment_inside_basket = false; ";
    
    
	$extJs .= " var op_disabled_payments = \"$pth_js\"; \n";
  
  	$extJs .= "var op_payment_discount = 0; \n var op_ship_cost = 0; \n var pdisc = []; "."\n";
    $extJs .= 'var op_payment_fee_txt = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT')).'"; '."\n"; // fee
    $extJs .= 'var op_payment_discount_txt = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')).'"; '."\n"; // discount
    //$rp_js = ' var pay_msg = []; var pay_btn = []; ';	
    
    // paypal:
    if (false && $paypalActive)
    $extJs .= ' var op_paypal_id = "'.ps_paypal_api::getPaymentMethodId().'"; ';
    else $extJs .= ' var op_paypal_id = "x"; ';
    if (false && $paypalActive && (defined('PAYPAL_API_DIRECT_PAYMENT_ON')) && ((boolean)PAYPAL_API_DIRECT_PAYMENT_ON))
    {
      $extJs .= ' var op_paypal_direct = true; ';
    }
    else
    {
      $extJs .= ' var op_paypal_direct = false; ';
    }
	
	$extJs .= ' var op_general_error = '."'".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED'))."';";
	$extJs .= ' var op_email_error = '."'".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ENTER_A_VALID_EMAIL_ADDRESS'))."';";
    $err = OPCJavascript::getPwdError(); 
	
	$extJs .= ' var op_pwderror = '."'".OPCmini::slash($err)."';\n";
   
   if ($double_email)
   if (!$OPCloader->logged($ref->cart))
     {
      $extJs .= ' callSubmitFunct.push("Onepage.doubleEmailCheck"); ';
   	 }
	 
   if (!empty($disable_payment_per_shipping))
   {
     $extJs .= ' addOpcTriggerer("callAfterShippingSelect", "Onepage.refreshPayment()"); '; 
   }
   
   if (empty($no_coupon_ajax))
   $extJs .= 'jQuery(document).ready(function() {
     jQuery(\'#userForm\').bind(\'submit\',function(){
		 if (userForm.coupon_code != null)
		 if (userForm.coupon_code.value != null)
		 {
		 new_coupon = Onepage.op_escape(userForm.coupon_code.value); 
		 if (typeof Onepage != \'undefined\')
		 if (typeof Onepage.op_runSS != \'undefined\')
		 {
         Onepage.op_runSS(this, false, true, \'process_coupon&new_coupon=\'+new_coupon); 
		 return false; 
		 }
		 }
    });
    });';
	//callAfterShippingSelect.push('hideShipto()'); 
	
	 $inside = JRequest::getCmd('insideiframe', ''); 
			$js = ''; 
			if (!empty($inside))
			{
			$js = "\n".' 
			if (typeof jQuery != \'undefined\' && (jQuery != null))
			{
			 jQuery(document).ready(function() {

			 if (typeof Onepage.op_runSS == \'undefined\') return;
			 '; 
			 
			 if (!empty($inside)) $js .= "\n".' op_resizeIframe(); '."\n"; 
			 
			 $js .= ' 		 });
			}
			else
			 {
			   if ((typeof window != \'undefined\') && (typeof window.addEvent != \'undefined\'))
			   {
			   window.addEvent(\'domready\', function() {
			   ';
			   if (!empty($inside)) $js .= ' op_resizeIframe(); '; 
			$js .= '
			
			    });
			   } 
			  }'; 
			 }
			
			
			
			$document  = JFactory::getDocument();
			$raw_js =   "\n".$extJs."\n".$js."\n"; 
			$src = '<script>'."\n".'//<![CDATA['.$raw_js.'//]]> '."\n".'</script>'; 
			
			
			
$app = JFactory::getApplication(); 
$jtouch = $app->getUserStateFromRequest('jtpl', 'jtpl', -1, 'int');
if ($jtouch > 0)
$opc_php_js2 = true; 

			// stAn, updated on 2.0.218
			// stan, to support gk gavick mobile themes we had to omit the type
			if (empty($opc_php_js2)) 
			 {
			    $document->addCustomTag($src);
				return;
			 }
			$js_dir = JPATH_CACHE.DS.'com_onepage'; 
			$lang = JFactory::getLanguage()->getTag(); 
			$js_file = 'opc_dynamic_'.$lang.'_'.md5($raw_js).'.js'; 
			
			$js_path = $js_dir.DS.$js_file; 
			
			$add = true; 
			
			
			
			
			
			
			if (!file_exists($js_dir)) 
			{
			  if (@JFolder::create($js_dir) === false) $add = true;
			}
			
			if (!file_exists($js_path))
			{
			   if (@JFile::write($js_path, $raw_js) !== false)
			    {
				  JHTMLOPC::script($js_file, 'cache/com_onepage/'); 
				  return; 
			      
				}
				else
				{
				  
				  $add = true; 
				  return; 
				}
			}
			else 
			{
			JHTMLOPC::script($js_file, 'cache/com_onepage/'); 
			
			return;
			}
			
			if ($add)
			$document->addCustomTag($src);
			
			
			
			//echo $src; 
			//$document->addCustomTag('<script type="text/javascript">'."\n".'//<![CDATA[  '."\n".$extJs."\n".$js."\n".'//]]> '."\n".'</script>');

	
    return; 
 }
 
 
 
  public static function getPwdError()
 {
   $jlang = JFactory::getLanguage(); 
   	 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {

   $jlang->load('com_users', JPATH_SITE, 'en-GB', true); 
   $jlang->load('com_users', JPATH_SITE, $jlang->getDefault(), true); 
   $jlang->load('com_users', JPATH_SITE, null, true); 
   
   return OPCLang::_('COM_USERS_FIELD_RESET_PASSWORD1_MESSAGE'); 
   
   }
   else
   {
    $jlang->load('com_user', JPATH_SITE, 'en-GB', true); 
    $jlang->load('com_user', JPATH_SITE, $jlang->getDefault(), true); 
    $jlang->load('com_user', JPATH_SITE, null, true); 

    return OPCLang::_('PASSWORDS_DO_NOT_MATCH'); 
   }
 }
 
 	

public static  function loadJavascriptFiles(&$ref, &$OPCloader)
 {
   
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 /*
 if (!empty($opc_php_js))
 {
   $dc = JFactory::getDocument(); 
   $url = OPCloader::getUrl(true);
      
   $url_onepage = JRoute::_('index.php?option=com_onepage&view=loadjs&task=loadjs&file=sync.js&nosef=1&format=raw'); 
   $dc->addScript($url_onepage, "text/javascript", false, false); 
   $url_onepage = JRoute::_('index.php?option=com_onepage&view=loadjs&task=loadjs&file=onepage.js&nosef=1&format=raw'); 
   $dc->addScript($url_onepage, "text/javascript", true, true); 
   
   
   return; 
 }
 */ 
		if (empty($opc_async))
			{
			$ref->script('onepage.js', 'components/com_onepage/assets/js/', false);
			$ref->script('sync.js', 'components/com_onepage/assets/js/', false);
			}
			else
			{
			  $ref->script('sync.js', 'components/com_onepage/assets/js/', false);
			  $dc = JFactory::getDocument(); 
			  $url = OPCloader::getUrl(true); 
			  $dc->addScript($url.'components/com_onepage/assets/js/onepage.js', "text/javascript", true, true); 
			}
 }
 
}