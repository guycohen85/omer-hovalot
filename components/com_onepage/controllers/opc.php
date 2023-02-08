<?php
/**
 * Controller for the OPC ajax and checkout
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
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

jimport('joomla.application.component.controller');
class VirtueMartControllerOpc extends OPCController {
     /**
     * Construct the cart
     *
     * @access public
     * @author Max Milbers
     */
	
    public function __construct() {
	parent::__construct();
	{
	    if (!defined('JPATH_OPC')) define('JPATH_OPC', JPATH_SITE.DS.'components'.DS.'com_onepage'); 
	    if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	    if (!class_exists('calculationHelper'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		
		
		
	}
	}
	
	private function returnTerminate($msg, $layout='')
	{
	  //$layout = JRequest::getWord('layout','edit');
	  $url = 'index.php?option=com_virtuemart&view=user'; 
	  if (!empty($layout)) $url .= '&layout='.$layout; 
	  $app = JFactory::getApplication(); 
	  $u = JRoute::_($url, FALSE); 
	  $this->setRedirect($u); 
	  $app->redirect($u, $msg); 
	  $app->close(); 
	  return false; 
	}
	
	private function getVendorDataStored(&$data)
	{
	  // Store multiple selectlist entries as a ; separated string
				if (key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
					$data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
				}

				$data['vendor_store_name'] = JRequest::getVar('vendor_store_name','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_store_desc'] = JRequest::getVar('vendor_store_desc','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_terms_of_service'] = JRequest::getVar('vendor_terms_of_service','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_letter_css'] = JRequest::getVar('vendor_letter_css','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_letter_header_html'] = JRequest::getVar('vendor_letter_header_html','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_letter_footer_html'] = JRequest::getVar('vendor_letter_footer_html','','post','STRING',JREQUEST_ALLOWHTML);
	}
	
	private function checkOPCVat()
	{
	    
		//COM_ONEPAGE_VAT_CHECKER_DOWN="EU validation service is currently not available for your country. Please try again later."
		//COM_ONEPAGE_VAT_CHECKER_INVALID="Invalid VAT number"
		//COM_ONEPAGE_VAT_CHECKER_INVALID_COUNTRY="The VAT ID you've entered doesn't match your country."
		$error0 = OPCLang::_('COM_ONEPAGE_VAT_CHECKER_INVALID'); 
			$error2 = OPCLang::_('COM_ONEPAGE_VAT_CHECKER_INVALID_COUNTRY'); 
			$error1 = OPCLang::_('COM_ONEPAGE_VAT_CHECKER_DOWN'); 
		   $vatid = $vat_id = JRequest::getVar('opc_vat'); 
		   
		   if (empty($vatid)) return ''; 
		   
		   $c = substr($vat_id, 0, 2); 
		   $c = strtoupper($c); 
		   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'vat.php'); 
		   
		   if (empty($opc_euvat_contrymatch))
		   {
		     
		   }
		   
		   if ((!in_array($c, OPCVat::$european_states)) || ((empty($opc_euvat_contrymatch))))
		   {
		    $country = JRequest::getVar('virtuemart_country_id', 0); 
		    $db = JFactory::getDBO();
			
			$q = "SELECT country_2_code FROM #__virtuemart_countries WHERE virtuemart_country_id =". (int)$country;
			$db->setQuery($q);
			$db->query();
			$country_2_code = $db->loadResult();
			
			$c = $c == 'EL' ? 'GR' : $c;
			$c = $c == 'UK' ? 'GB' : $c;
			
			if (!is_numeric($c))
			if ($c != $country_2_code) return $error2; 
			
			}
			else $country_2_code = $c; 
			
			
			$ret = $country_2_code.'_'.$vat_id; 
			
			$company = $e = ''; 
			$result = OPCVat::isVIESValidVAT($country_2_code, $vat_id, $company, $e); 
			
			
			if ($result === false) 
			return $error0; 
		    if ($result === -1) 
			{
			if (stripos($e, 'INVALID_INPUT')!==false) return $error0; 
			return $error1.'<br />'.$e; 
			}
			
			// will be used for shopper group handling
			$session = JFactory::getSession(); 
			$vatids = $session->get('opc_vat', array());
			if (!is_array($vatids))
			$vatids = unserialize($vatids); 

			$vatids['field'] = 'opc_vat'; 
	
			$vatid = strtoupper($vatid); 
			$vatid = preg_replace("/[^a-zA-Z0-9]/", "", $vatid);
			
			$country = JRequest::getVar('virtuemart_country_id'); 
			$vathash = $country.'_'.$vatid; 
			
			$vatids[$vathash] = true; 
			$s = serialize($vatids); 
	
			$vatids = $session->set('opc_vat', $s);
			
			
			return OPCLang::_('COM_ONEPAGE_VALIDATE_VAT_VALID'); 
	}
	
	private function checkVM2Captcha($retUrl)
	{
	  $id = JFactory::getUser()->get('id'); 
	  $guest = JFactory::getUser()->guest; 
	  
	  if ($guest || empty($id)) $logged = false; 
	  else $logged = true; 
	  
	  if(($guest || empty($id)) and VmConfig::get ('reg_captcha'))
	  {
			$recaptcha = JRequest::getVar ('recaptcha_response_field');
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$recaptcha);
			if(!$res[0]){
				$data = JRequest::get('post');
				$data['address_type'] = JRequest::getVar('addrtype','BT');
				if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
				$cart = VirtueMartCart::getCart();
				$cart->saveAddressInCart($data, $data['address_type']);
				if (function_exists('vmText'))
				$errmsg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				else
				$errmsg = JText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				$this->setRedirect (JRoute::_ ($retUrl . '&captcha=1', FALSE), $errmsg);
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
		
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		if (((!empty($enable_captcha_logged)) && ($logged)) || ((!empty($enable_captcha_unlogged)) && (!$logged)))
		{
		  $this->checkOPCCaptcha($retUrl); 
    	}
		
	}
	
	function opcregister()
	{
		if (!class_exists('OPCLang'))
		require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
		
	
	OPCLang::loadLang(); 

	
		 $this->checkVM2Captcha('index.php?option=com_virtuemart&view=user'); 
		 
		 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
		 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
		 
	     $msg = ''; //$this->saveData(false,true);
		 
	     if (!class_exists('VmConfig'))
		 require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		 VmConfig::loadConfig(); 
		
		$data = JRequest::get('post');
	    $userinfo_id = (int)JRequest::getInt('virtuemart_userinfo_id', 0); 
		$data['address_type'] = JRequest::getWord('addrtype',JRequest::getWord('address_type', 'BT'));
		if ($data['address_type'] == 'BT')
		{
		$prefix = ''; 
		$data['shipto_virtuemart_userinfo_id'] = null; 
		}
		else 
		{
		$prefix = 'shipto_'; 
		$data['shipto_virtuemart_userinfo_id'] = $userinfo_id; 
		}
		
		$data['quite'] = false; 
		
		$adminmode= false; 

		// logged in users
		if (!empty($userinfo_id))
		{
	      $q = 'select * from #__virtuemart_userinfos where virtuemart_userinfo_id = '.$userinfo_id.' limit 0,1'; 
		  $db = JFactory::getDBO(); 
		  $db->setQuery($q); 
		  $res = $db->loadAssoc();
		  // if user is already registered:
		  $user = JFactory::getUser(); 
		  $uidc = (int)$user->get('id'); 
		  $data['virtuemart_user_id'] = $uidc; 
		  if (!empty($res))
		  {
		  $address_type = $res['address_type']; 
		  $uid = (int)$res['virtuemart_user_id']; 
		 
		  if (!empty($uid))
		    {
			  // 1st security, user ids must match
			  if ($uid != $uidc) 
			  {
			  
			  
			    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			if (!OPCmini::isSuperVendor())
			 {
			    $msg = 'OPC: Access Denied'; 
			  return $this->returnTerminate($msg); 		
			 }
			 else
			 {
			  $adminmode= true; 
			 }
			  
			  
			  }
			  JRequest::setVar('virtuemart_user_id', $uid); 
			  $data['virtuemart_user_id'] = $uid; 
			}
		  }
		  
		  
		  if (!$adminmode)
		  {
		  if (empty($data['user_id']))
				  {
				  $data['user_id'] = $uidc; 
				  $data['virtuemart_user_id'] = $uidc; 
				  $data[$prefix.'user_id'] = $uidc; 
				  $data[$prefix.'virtuemart_user_id'] = $uidc; 
				  }
		  
		  $username = $user->get('username'); 
		  $email = $user->get('email'); 
		  if (empty($data['username']) && (!empty($username)))
		  {
				   $data['username'] = $username; 
				   $data[$prefix.'username'] = $username; 
				   
		  }
		  else
		  if (empty($data['username']) && (!empty($email)))
		  {
		   $data['username'] = $email; 
		   $data[$prefix.'username'] = $email; 
		  }
		  
		  if (empty($data[$prefix.'email']))
		   {
		     $data[$prefix.'email'] = $email; 
		   }
		  $doUpdate = true; 
		  
		 }
		
		
		
		// address name override: 
		if (empty($data[$prefix.'name']))
				  {
				    if (!empty($res))
				    $data[$prefix.'name'] = $res['address_type_name']; 
					else $data[$prefix.'name'] = ''; 
				    if (!empty($data[$prefix.'first_name']))
					$data['name'] .= $data[$prefix.'first_name'];
					if (!empty($data[$prefix.'last_name']))
					$data[$prefix.'name'] .= $data[$prefix.'last_name']; 
				  }
		
		
		
		// end of logged in user
		
		
		
		OPCloader::setRegType(); 
			//NO_REGISTRATION, NORMAL_REGISTRATION, SILENT_REGISTRATION, OPTIONAL_REGISTRATION
		if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
		$register = false; 
		else 
		if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION')
		$register = true; 
		else
		if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION')
		$register = true; 
		else 
		if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
		{
		  $register = JRequest::getVar('register_account', false); 
		}
		
		
		$mainframe = JFactory::getApplication();
		
		$msg = '';
		$userModel = OPCmini::getModel('user');
		

		
		if($user->guest!=1 || $register){
		   self::getVendorDataStored($data); 
		}
		// update address of already registered user
		/*
		if (!empty($doUpdate))
		{
		 $this->userStoreAddress($userModel, $data); 
		 return $this->returnTerminate(''); 
		}
		*/
		
		}
		
		
			$cart = VirtuemartCart::getCart(); 
			
			require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
	        OPCShopperGroups::setShopperGroupsController($cart); 
			
			if (method_exists($cart, 'prepareCartData'))
			{
				$cart->prepareCartData(false); 
			}
			
			$this->prepareFields(); 
			$this->setCartAddress($cart); 
			$this->setExtAddress($cart, false);
			// k2 mod with recaptcha enabled
			$session = JFactory::getSession(); 
			$orig = $session->get('socialConnectData'); 
			$session->set('socialConnectData', true); 
			// end p1 k2 mod with recaptcha enabled
			
			$data = JRequest::get('post');
			
			$reg = JRequest::getVar('register_account'); 
			if (empty($reg)) $reg = false; 
			else $reg = true; 

			if ($data['address_type'] == 'ST')
			{
			if (!isset($data['ship_to_info_id'])) $data['ship_to_info_id'] = 'new'; 
			// opc hack: 
			$data['sa'] = 'adresaina'; 
			
			$suid = JRequest::getVar('shipto_virtuemart_userinfo_id', JRequest::getVar('virtuemart_userinfo_id')); 
			if (empty($suid)) 
			 $data['opc_st_changed_new'] = true; 
			 JRequest::setVar('opc_st_changed_new', true); 
			 JRequest::setVar('sa', 'adresaina'); 
			}
			
			$this->saveData($cart,$reg, false, $data); 
			
			if (!empty($allow_sg_update))
			$this->storeShopperGroup($data, true); 
			 $userModel = OPCmini::getModel('user');	   
			if (method_exists($userModel, 'getCurrentUser'))
				{
				$user = $userModel->getCurrentUser();
				self::$shopper_groups = $user->shopper_groups; 
				}
				
			// k2 mod with recaptcha enabled
			if (empty($orig))
			$session->clear('socialConnectData'); 
			else
			$session->set('socialConnectData', $orig); 
			// end p2 k2 mod with recaptcha enabled
			return $this->returnTerminate($msg); 			
		  
		 
		 
		
	}
	function cart()
	{
	  $view = new VirtueMartViewCartopc(); 
	  $view->display(); 
	  
	}
	function updateattributes(&$cart)
	{
	   @header('Content-Type: text/html; charset=utf-8');
	   @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	   @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	  jimport ('joomla.utilities.arrayhelper');
		$virtuemart_product_idArray = JRequest::getVar ('virtuemart_product_id', array()); //is sanitized then
		
		
		if(is_array($virtuemart_product_idArray)){
			JArrayHelper::toInteger ($virtuemart_product_idArray);
			$virtuemart_product_id = $virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = $virtuemart_product_idArray;
		}

		$customPrices = array();
		$customVariants = JRequest::getVar ('customPrice', array()); //is sanitized then
		//echo '<pre>'.print_r($customVariants,1).'</pre>';

		//MarkerVarMods
		foreach ($customVariants as $customVariant) {
			//foreach ($customVariant as $selected => $priceVariant) {
			//In this case it is NOT $selected => $variant, because we get it that way from the form
			foreach ($customVariant as $priceVariant => $selected) {
				//Important! sanitize array to int
				$selected = (int)$selected;
				$customPrices[$selected] = $priceVariant;
			}
		}

		$quantity = JRequest::getVar ('quantity',1); //is sanitized then
		
		 if (is_array($quantity)) $quantity = reset($quantity); 
		 $quantity = (int)$quantity; 
		 
		 
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$product_model = OPCmini::getModel ('product');

		//VmConfig::$echoDebug = TRUE;
		
		$qs = $quantity; 
		$product = $product_model->getProduct ($virtuemart_product_id, TRUE, TRUE, TRUE,$quantity);
		
		$quantity = $qs; 
		
		$prices = $product_model->getPrice ($product, $customPrices, $quantity);
		
		$priceFormated = array();
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();
		foreach ($prices as $name => $product_price) {
		if (is_numeric($product_price))
		$prices[$name] = $product_price * $quantity; 
// 		echo 'Price is '.print_r($name,1).'<br />';
			if ($name != 'costPrice') {
				$priceFormated[$name] = $currency->createPriceDiv ($name, '', $prices, TRUE);
			}
		}
	    
		$s = ''; 
		
		
		
		$prod_id = JRequest::getVar('cart_virtuemart_product_id', 0); 
		$cart->removeProductCart($prod_id); 
		$keys = array(); 
		if (!empty($cart->products))
		foreach ($cart->products as $key=>$val)
		{
		  $keys[] = $key; 
		}
		$cart->add($virtuemart_product_idArray, $s); 
		/*
		$last = end($cart->products); 
		$new_key = key($cart->products); 
		*/
		$new_key = key( array_slice( $cart->products, -1, 1, TRUE ) );
		$new_quantity = $quantity; 
		
		if (in_array($new_key, $keys))
		 {
		   // a product merge happened here
		   $new_quantity = $cart->products[$new_key]->quantity; 
		 }
	  
	  
	  
	  $arr = array(); 
	  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	    if (empty($product_price_display)) $product_price_display = 'salesPrice'; 
		$test_product_price_display = array($product_price_display, 'salesPrice', 'basePrice', 'priceWithoutTax', 'basePriceWithTax', 'priceBeforeTax', 'costPrice'); 
			  foreach ($test_product_price_display as $product_price_display)
			  {
			  
			   $test = $currency->createPriceDiv($product_price_display,'', '10',false,false, 1);
			   if (empty($test)) continue; 
			   else 
			   break;
			  }
	  //$product['product_price'] = $currentPrice[$product_price_display];
	  $arr['price'] = $priceFormated[$product_price_display]; 
	  $arr['new_key'] = $new_key; 
	  $arr['new_quantity'] = $new_quantity; 
	  return $arr; 
	  echo json_encode($arr); 
	  $app = JFactory::getApplication(); 
	  $app->close(); 
	  die(); 
	}
	function tracker()
	{

	  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'tracker'.DS.'tracker.php'); 
	  $mainframe = JFactory::getApplication();
	  $mainframe->close(); 
	}
	
	function getEscaped(&$dbc, $string)
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  return $dbc->escape($string); 
	  else return $dbc->getEscaped($string);  
	}
	
	function getEmail($id=null)
	{
	    $user =& JFactory::getUser();
		$email = $user->email; 
		if (empty($email) && (!empty($user->id)))
		 {
		   $q = 'select email from #__users where id = '.$user->id.' limit 0,1'; 
		   $db = JFactory::getDBO(); 
		   $db->setQuery($q); 
		   $email = $db->loadResult(); 
		   
		 }
		return $email; 
	    if(!class_exists('VirtuemartModelUser')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
	    $user = new VirtueMartModelUser;
		//$user->setCurrent();
		$d = $user->getUser();
		return $d->JUser->get('email');
	}
	
	function getBTaddress($user_info_id)
	{
	  if(!class_exists('VirtuemartModelUser')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
	  $vmusero = new VirtueMartModelUser();
	  $user = JFactory::getUser();
	  $vmusero->setUserId($user->id);
	  $vmuser = $user->getUser(); 
	  $vmuser->userInfo[$user_info_id]; 
	  
	  
	}
	function setExtAddress(&$cart, $ajax=false, $force_only_st=false)
	{
		
		$kIndex = 'klarna_';
		
		$klarna_method = JRequest::getVar('klarna_opc_method', ''); 
	    if (!empty($klarna_method))
	    JRequest::setVar('klarna_paymentmethod', $klarna_method); 

		$klarna['klarna_paymentmethod'] = JRequest::getVar ($kIndex . 'paymentmethod');
		
		if ($klarna['klarna_paymentmethod'] == 'klarna_invoice') {
			$klarna_option = 'invoice';
		} elseif ($klarna['klarna_paymentmethod'] == 'klarna_partPayment') {
			$klarna_option = 'part';
		} elseif ($klarna['klarna_paymentmethod'] == 'klarna_speccamp') {
			$klarna_option = 'spec';
		} else {
			return NULL;

		}
		$prefix=$klarna_option . '_' . $kIndex ;
	
	  JRequest::setVar($prefix.'paymentPlan', JRequest::getVar('klarna_paymentPlan', ''));
	  
	  if (!isset($cart->BT['socialNumber']))
	  {
	  JRequest::setVar($prefix.'socialNumber', JRequest::getVar('socialNumber', '')); 
	  }
	  else 
	  {
	  JRequest::setVar($prefix.'socialNumber', $cart->BT['socialNumber']); 
	  }
	  
	  if (!isset($cart->BT['first_name']))
	  JRequest::setVar($prefix.'firstName', JRequest::getVar('first_name', '')); 
	  else
	  JRequest::setVar($prefix.'firstName', $cart->BT['first_name']); 
	  
	  if (!isset($cart->BT['last_name']))
	  JRequest::setVar($prefix.'lastName', JRequest::getVar('last_name', '')); 
	  else
	  JRequest::setVar($prefix.'lastName', $cart->BT['last_name']); 
	  
	   JRequest::setVar($prefix.'birth_day', JRequest::getVar('klarna_birth_day', '')); 
	  JRequest::setVar($prefix.'birth_month', JRequest::getVar('klarna_birth_month', '')); 
	  JRequest::setVar($prefix.'birth_year', JRequest::getVar('klarna_birth_year', '')); 
	  
	  if (empty($cart->BT['fax'])) $cart->BT['fax'] = ''; 
	  if (empty($cart->BT['phone_2'])) $cart->BT['phone_2'] = ''; 
	  if (empty($cart->ST['fax'])) $cart->BT['fax'] = ''; 
	  if (empty($cart->ST['phone_2'])) $cart->BT['phone_2'] = ''; 
	  
	  JRequest::setVar($prefix.'fax', JRequest::getVar('fax', '')); 
	  JRequest::setVar($prefix.'phone_2', JRequest::getVar('phone_2', '')); 
	  
	  if (!isset($cart->BT['phone_1']))
	  JRequest::setVar($prefix.'phone', JRequest::getVar('phone_1', '')); 
	  else
	  JRequest::setVar($prefix.'phone', $cart->BT['phone_1']); 
//klarna_birth_day
//klarna_birth_month
//klarna_birth_year
	  if (isset($cart->BT['birthday']))
	  {
		  $bday = $cart->BT['birthday'];
		  $arr = explode('-', $bday); 
		if (count($arr)==3)
		{
		JRequest::setVar($prefix.'birth_day', $arr[2]); 
		JRequest::setVar($prefix.'birth_month', $arr[1]); 
		JRequest::setVar($prefix.'birth_year', $arr[0]); 
		}
	  }
  
	  if (!isset($cart->BT['address_1']))
	  JRequest::setVar($prefix.'street', JRequest::getVar('address_1', '')); 
	  else
	  JRequest::setVar($prefix.'street', $cart->BT['address_1']); 
	  
	  if (!isset($cart->BT['address_2']))
	  JRequest::setVar($prefix.'homenumber', JRequest::getVar('address_2', '')); 
	  else
	  JRequest::setVar($prefix.'homenumber', $cart->BT['address_2']); 
	  
	  //klarna_city
	  if (!isset($cart->BT['city']))
	  JRequest::setVar($prefix.'city', JRequest::getVar('city', '')); 
	  else
	  JRequest::setVar($prefix.'city', $cart->BT['city']); 
	  
	   if (!isset($cart->BT['zip']))
	  JRequest::setVar($prefix.'zipcode', JRequest::getVar('zip', '')); 
	  else
	  JRequest::setVar($prefix.'zipcode', $cart->BT['zip']); 
	  
	  $country_id = JRequest::getVar('virtuemart_country_id', ''); 
	  if (isset($cart->BT['virtuemart_country_id'])) $country_id = $cart->BT['virtuemart_country_id']; 
	  $klarna_country = JRequest::getVar('klarna_country_2_code', null); 
	  if (isset($klarna_country) && (empty($klarna_country)))
	  if (is_numeric($country_id))
	   {
			$q = 'select country_2_code from #__virtuemart_countries where virtuemart_country_id = '.$country_id.' limit 0,1';    
			$db=JFactory::getDBO(); 
			$db->setQuery($q); 
			$country_2_code = $db->loadResult(); 
			JRequest::setVar($prefix.'country_2_code', strtoupper($country_2_code)); 
	   }
	  
	  $emailPost = JRequest::getVar('email', ''); 
	  if (!empty($emailPost)) 
	  {
	  $cart->BT['email'] = $emailPost; 
	  if (is_array($cart->ST))
	    $cart->ST['email'] = $emailPost; 
	  }
	   
	  if (!isset($cart->BT['email']))
	  JRequest::setVar($prefix.'emailAddress', JRequest::getVar('email'));
	  else
	  JRequest::setVar($prefix.'emailAddress', $cart->BT['email']);
	  $em = JRequest::getVar($prefix.'emailAddress', ''); 
	  if (empty($em))
	   {
	     $em = $this->getEmail(); 
		 JRequest::setVar($prefix.'emailAddress', $em); 
	   }
	  if (!empty($cart->BT['title']))
	  {
	  switch ($cart->BT['title']) {
			case OPCLang::_ ('COM_VIRTUEMART_SHOPPER_TITLE_MR'):
				JRequest::setVar('part_klarna_gender', 1); 
				JRequest::setVar('spec_klarna_gender', 1); 
				JRequest::setVar('invoice_klarna_gender', 1); 
				JRequest::setVar($prefix.'gender', 1); 
				break;
			case 'Mr':
				JRequest::setVar('part_klarna_gender', 1); 
				JRequest::setVar('spec_klarna_gender', 1); 
				JRequest::setVar('invoice_klarna_gender', 1); 
				JRequest::setVar($prefix.'gender', 1); 
				break;
			case 'Ms':
			case 'Mrs':
			case OPCLang::_ ('COM_VIRTUEMART_SHOPPER_TITLE_MISS'):
			case OPCLang::_ ('COM_VIRTUEMART_SHOPPER_TITLE_MRS'):
				JRequest::setVar('klarna_gender', 0);
				JRequest::setVar('spec_klarna_gender', 0); 
				JRequest::setVar('invoice_klarna_gender', 0); 
				JRequest::setVar($prefix.'gender', 0);
				break;
			default:
				JRequest::setVar('klarna_gender', NULL );
				JRequest::setVar($prefix.'gender', NULL);
				break;
		}
	}
	  
	  
	  if (isset($cart->BT['house_no']))
	  JRequest::setVar($prefix.'homenumber', $cart->BT['house_no']); 
	  //if (isset($cart->BT['klarna_house_extension
	  $company = JRequest::getVar('company', @$cart->BT['company']); 
	  if (empty($company))
	  JRequest::setVar('klarna_invoice_type', 'private'); 
	  else 
	  {
	   JRequest::setVar('klarna_invoice_type', 'company'); 
	   JRequest::setVar('klarna_company', $company); 
	   JRequest::setVar('klarna_invoice_type', 'company'); 
	   JRequest::setVar('klarna_company_name', $company); 
	  }
	  //JRequest::setVar('klarna_country_2_code', JRequest::getVar('virtuemart_country_id', '')); 
		
			//klarna_birth_day, klarna_birth_month, klarna_birth_year
			/*
	 
	  */
	  //if (empty($cart->BT['birthday'])) $cart->BT['birthday'] = ''; 

	  
	  OPCloader::prepareBT($cart);
	


	}
	function setAddress(&$cart, $ajax=false, $force_only_st=false, $no_post=false)
	{
	
	      $this->setExtAddress($cart, $ajax, $force_only_st); 

			$name = 'cpsolrates';
            $jsess = JFactory::getSession();
            $has_shipping_rate = $jsess->get($name, -1, 'vm');
			if ($has_shipping_rate!=-1)
            if(strlen($has_shipping_rate)){
                $jsess->clear($name, 'vm');
                $jsess->set('updateshipping', 1, 'vm');
            }
	   
	   $state_test = JRequest::getVar('virtuemart_state_id', '');
	   if ($state_test == 'none')
	    {
		  JRequest::setVar('virtuemart_state_id', ''); 
		  $_POST['virtuemart_state_id'] = ''; 
		}

	   $state_test = JRequest::getVar('shipto_virtuemart_state_id', '');
	   if ($state_test == 'none')
	    {
		  JRequest::setVar('shipto_virtuemart_state_id', ''); 
		  $_POST['shipto_virtuemart_state_id'] = ''; 
		}

		$user = JFactory::getUser(); 
		  $user_id = $user->id; 	   

		  
		  
	   $post = JRequest::get('post'); 
	  
	  // POST has priority over cart
	  $emailPost = JRequest::getVar('email', ''); 
	  if (!empty($emailPost)) 
	  {
	  $cart->BT['email'] = $emailPost; 
	  if (is_array($cart->ST))
	    $cart->ST['email'] = $emailPost; 
	  }
	  
	   if (!empty($cart->BT['email']))
	   $post['email'] = $post['shipto_email'] = $cart->BT['email']; 
	   
	   $userNamePost = JRequest::getVar('username', ''); 
	   if (!empty($userNamePost))
	   $cart->BT['username'] = $userNamePost; 
	   
	   if (is_array($cart->ST))
	   $cart->ST['username'] = $userNamePost; 
	   
	   if (!empty($cart->BT['username']))
	   $post['username'] = $post['shipto_username'] = $cart->BT['username']; 
	   
	   
	   
	  
	   
	   
	   
	   // this sets shipping method !!!!
	   if (method_exists($cart, 'setPreferred'))
       $cart->setPreferred(false); 
	   
	   $cart->virtuemart_shipmentmethod_id = 0; 
	   $cart->virtuemart_paymentmethod_id = 0; 
	   
	   
	   
	    if (($no_post) && (!empty($user_id))) return; 
	  if (method_exists($cart, 'prepareAddressDataInCart'))
	  {
	   $cart->BT = 0; 
	   $cart->prepareAddressDataInCart('BT', 1);
	  }
	    
	     if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
		 if(!class_exists('VirtueMartModelUser')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
		 
		 $corefields = VirtueMartModelUserfields::getCoreFields();
		 $fieldtype = 'BTaddress';
		 $userFields = $cart->$fieldtype;
		
		if (method_exists($cart, 'prepareAddressDataInCart'))
		 $cart->prepareAddressDataInCart('ST', 1);
		 $fieldtype = 'STaddress';
		 $userFieldsst = $cart->$fieldtype;

		 
		
		$db = JFactory::getDBO();
		
		// we will populate the data for logged in users
		
		if (!empty($post['ship_to_info_id']))
		{
		 
		 
		
		  // this part for registered users, let's retrieve the selected address
		  $user = JFactory::getUser(); 
		  $user_id = $user->id; 
		  if (!is_numeric($user_id)) return; 
		  if (!is_numeric($post['ship_to_info_id'])) 
		  {
		  if ($post['ship_to_info_id'] != 'new')
		  return; 
		  }
		  

		  
		  //$q = "select * from #__virtuemart_userinfos where virtuemart_userinfo_id = '".$this->getEscaped($db, $post['ship_to_info_id'])."' and virtuemart_user_id = ".$this->getEscaped($db, $user_id)." limit 0,1"; 

		  // ship_to_info_id  (currently selected shipping address when more then one is shown)
		  // opc_st_changed_  (if to update the address)
		  // ship_to_info_id_bt (BT address)
		  // 
		  
		  $bt = JRequest::getVar('ship_to_info_id_bt', ''); 
		  $bt_changed = JRequest::getVar('opc_st_changed_'.$bt, false); 
		 
		  $test = JRequest::getVar('ship_to_info_id'); 
		  $sa = JRequest::getVar('sa', false); 
		  
		  // change ship_to_info_id when no shipping address is selected
		  if ((!$ajax))
		   {
		     $saved_ship_to = $test; 
		     JRequest::setVar('ship_to_info_id', $bt); 
			 $_POST['ship_to_info_id'] = $bt; 
			 $_REQUEST['ship_to_info_id'] = $bt; 
			 $post['ship_to_info_id'] = $bt; 
			
		   }
		  
		  $db =& JFactory::getDBO(); 
		  $q = "select * from #__virtuemart_userinfos as uu, #__users as ju where uu.virtuemart_user_id = '".$user_id."' and ju.id = uu.virtuemart_user_id and uu.virtuemart_userinfo_id = '".$this->getEscaped($db, $post['ship_to_info_id'])."' limit 0,1 "; 
		  $db->setQuery($q); 
		  
		  $res = $db->loadAssoc(); 
		  $err = $db->getErrorMsg(); 
		 
		  $user_id = $res['virtuemart_user_id']; 
		  JRequest::setVar('shipto', $_POST['ship_to_info_id']);
		  $cart->selected_shipto = $this->getEscaped($db, $post['ship_to_info_id']); 
		  
		  // stAn 250
		  
		  $email = $this->getEmail($user_id); 
		  
		  if (empty($post['email']))
		  if (empty($cart->BT['email']))
		  if (!empty($email)) $post['email'] = $email; 
		  
		  if (empty($post['email']))
		  if (!empty($cart->BT['email']))
		  $post['email'] = $cart->BT['email']; 
		  
		  
		  if (empty($post['username']))
		  if (!empty($cart->BT['username']))
		  $post['username'] = $cart->BT['username']; 
		  
		  if (empty($post['username']))
		  if (empty($cart->BT['username']))
		  if (!empty($user_id))
		  {
		    if (!empty($user->username))
		    $post['username'] = $cart->BT['username'] =  $user->username; 
			
			
			
			if (is_array($cart->ST))
			$cart->ST['username'] = $user->username; 
		  }

		  
		
		  
			  foreach ($userFields['fields'] as $key=>$uf22)   
				{
				
				 if ($uf22['type'] == 'delimiter') continue; 
				 // don't save passowrds
				 if (stripos($uf22['name'], 'password')) $post[$uf22['name']] = ''; 
				 
				 // POST['variable'] and POST['shipto_variable'] are prefered from database information
				 if ((!empty($post[$uf22['name']]) || ((($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']]))))) && (empty($force_only_st)))
					{
					    // if the selected address is ST, let's first checkout POST shipto_variable
						// then POST['variable']
						// and then let's insert it from the DB
						// will not override BT address when ST is open a the user is logged in
					    if (($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']])))
						$address[$uf22['name']] = $post['shipto_'.$uf22['name']]; 
						else
						$address[$uf22['name']] = $post[$uf22['name']]; 
						
						
					}
					else
					{
					   // since version 2.0.100 we will update the BT address:
					   if (!empty($bt_changed))
					   {
					   if (isset($post[$uf22['name']]))
					   {
					    $address[$uf22['name']] =  $post[$uf22['name']];
					   }
					   else
					   $address[$uf22['name']] = $res[$uf22['name']]; 

					   }
					   else
					   if (!empty($res[$uf22['name']]))
					   $address[$uf22['name']] = $res[$uf22['name']]; 
					   else $address[$uf22['name']] = ''; 
					}
					
					if (!isset($address[$uf22['name']])) $address[$uf22['name']] = ''; 
					
				}
		
				
		 // the selected is BT
		 if ($res['address_type'] == 'BT') 
		    {
			  
				
				$user = JFactory::getUser(); 
				if (!empty($user->email))
					{
					  $address['email'] = $user->email; 
					}
				$cart->STsameAsBT = 1; 
				$cart->BT = $address; 
				
				
				
				if (!$ajax)
				if ($sa == 'adresaina')
				 {
				   $cart->STsameAsBT = 0; 
				   // lets set the ship to address
		
		   {
		     
		     JRequest::setVar('ship_to_info_id', $saved_ship_to); 
			 $_POST['ship_to_info_id'] = $saved_ship_to; 
			 $_REQUEST['ship_to_info_id'] = $saved_ship_to; 
			 $post['ship_to_info_id'] = $saved_ship_to; 
			 
			 JRequest::setVar('shipto', $saved_ship_to);
		   }


		  
		  
			  
		  $db =& JFactory::getDBO(); 
		  $q = "select * from #__virtuemart_userinfos as uu, #__users as ju where uu.virtuemart_user_id = '".$user_id."' and ju.id = uu.virtuemart_user_id and uu.virtuemart_userinfo_id = '".$this->getEscaped($db, $post['ship_to_info_id'])."' limit 0,1 "; 
		  $db->setQuery($q); 
		  
		  $res = $db->loadAssoc(); 
		  $err = $db->getErrorMsg(); 
		 
		  $user_id = $res['virtuemart_user_id']; 
		  JRequest::setVar('shipto', $_POST['ship_to_info_id']);
		  $cart->selected_shipto = $this->getEscaped($db, $post['ship_to_info_id']); 
		  
		  $email = $this->getEmail($user_id); 
		  if (!empty($email)) 
		  {
		  $post['email'] = $email; 
		  
		  if (empty($post['shipto_email']))
		  $post['shipto_email'] = $email; 
		  }
		  
		   
		   $st_changed = JRequest::getVar('opc_st_changed_'.$saved_ship_to, false); 
		   //if ($st_changed)
		    {
			  $data['shipto_virtuemart_userinfo_id'] = $saved_ship_to;
			  $cart->selected_shipto = $saved_ship_to;
			  JRequest::setVar('shipto', $saved_ship_to); 
			}
			 if ($st_changed)
			  foreach ($userFieldsst['fields'] as $key=>$uf22)   
				{
				 // don't save passowrds
				 if (stripos($uf22['name'], 'password')) $post[$uf22['name']] = ''; 
				 
				 // POST['variable'] and POST['shipto_variable'] are prefered from database information
				 if ((!empty($post[$uf22['name']]) || ((($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']]))))) && (empty($force_only_st)))
					{
					    // if the selected address is ST, let's first checkout POST shipto_variable
						// then POST['variable']
						// and then let's insert it from the DB
						// will not override BT address when ST is open a the user is logged in
					    $u2 = str_replace( 'shipto_', '', $uf22['name']); 
						
						if (($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']])))
						{
						$address[$uf22['name']] = $post['shipto_'.$uf22['name']]; 
						$address[$u2] = $post['shipto_'.$uf22['name']]; 
						}
						else
						{
						$address[$u2] = $post[$uf22['name']]; 
						$address[$uf22['name']] = $post[$uf22['name']]; 
						}
						
						
						
					}
					else
					{
					   $u2 = str_replace( 'shipto_', '', $uf22['name']); 
					   // since version 2.0.100 we will update the BT address:
					  
					   if (!empty($st_changed))
					   {
					   if (isset($post[$uf22['name']]))
					   {
					    $address[$uf22['name']] =  $post[$uf22['name']];
					   }
					   else
					    {
						  if (isset($res[$uf22['name']]))
					      $address[$uf22['name']] = $res[$uf22['name']]; 
						  else
						  if (isset($res[$u2]))
						  $address[$uf22['name']] = $res[$u2]; 
						}
					   }
					   else
					   if (!empty($res[$uf22['name']]))
					   $address[$uf22['name']] = $res[$uf22['name']]; 
					   else $address[$uf22['name']] = ''; 
					}
				}
				   
				   $cart->ST = $address; 
				   
				 }
				else
				$cart->ST = 0; 
		
		
				 if (empty($force_only_st))
				 return;
			}
			else 
			{
			 // if we updated the logged in ST address, don't set it here
			 if (empty($force_only_st)) 
			 $cart->ST = $address; 
			 $cart->STsameAsBT = 0; 
			}
			
			// the selected address is not BT
			// we need to get a proper BT
			// and set up found address as ST
			if ((!$cart->STsameAsBT) && (empty($force_only_st)))
			{
				$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$this->getEscaped($db, $res['virtuemart_user_id'])."' and address_type = 'BT' limit 0,1"; 
				$db->setQuery($q); 
				$btres = $db->loadAssoc();
				if (method_exists($cart, 'prepareAddressDataInCart'))
				 $cart->prepareAddressDataInCart('BT', 1);
				 $fieldtype = 'BTaddress';
				 $userFieldsbt = $cart->$fieldtype;
				foreach ($userFieldsbt['fields'] as $key=>$uf)   
				{
				 // POST['variable'] is prefered form userinfos.variable in db
				 $index = str_replace('shipto_', '', $uf['name']); 
				 if (!empty($post[$index]))
					{
						$address[$index] = $post[$index]; 
					}
					else
					{
					   $address[$index] = $btres[$index]; 
					}
				}
				// spain 195
				// us 223
				$cart->BT = $address; 
				
				 return;
			}
			
		}
		
		if (empty($force_only_st))
		if (!empty($res)) return; 
		
		
		// unlogged users get data from the form BT address
		$stopen = JRequest::getVar('shiptoopen', 0); 
		
		if ($stopen === 'false') $stopen = 0; 
		
		if (empty($stopen)) 
		{
		$sa = JRequest::getVar('sa', ''); 
		if ($sa == 'adresaina') $stopen = 1; 
		}
		foreach ($userFields['fields'] as $key=>$uf33)   
		 {
		   if (!empty($post[$uf33['name']]))
		    {
			  $address[$uf33['name']] = $post[$uf33['name']]; 
			}
			else $address[$uf33['name']] = ''; 
		 }
		 
		 
		 
		 if (!empty($address))
		 foreach ($address as $ka => $kv)
		  {
		    if ($kv === 'false') $address[$ka] = false;
		  }
		
		if ((!$ajax) && (!empty($address)))
		{
		  // update BT address of unlogged
		  if (empty($post['ship_to_info_id']))
		  $cart->BT = $address; 
		}
		
		if ((empty($stopen) && $ajax))
		 {
		  if (!isset($address['address_2'])) $address['address_2'] = ''; 
		  if (!empty($address))
		  $cart->BT = $address; 
		 }
		 else
		 {
		  if (!empty($address) && ($ajax))
		  {
		  // fedex multibox fix: 
		  if (!isset($address['address_2'])) $address['address_2'] = ''; 
		  $cart->ST = $address; 
		  $cart->STsameAsBT = 0; 
		  }
		 }
		 
		// ST address for unlogged
		 $address = array(); 
		 foreach ($userFieldsst['fields'] as $key=>$uf44)   
		 {
		 
		   if (!empty($post['shipto_'.$uf44['name']]) || (!empty($post[$uf44['name']])))
		    {
			  if (strpos($uf44['name'], 'shipto_')!==0)
			  $address[$uf44['name']] = $post['shipto_'.$uf44['name']]; 
			  else
			  $address[$uf44['name']] = $post[$uf44['name']]; 
			  
			  if ($key != $uf44['name'])
			   {
			     // supports address['company_name'] = post['shipto_company_name']; 
			     $address[$key] = $post[$uf44['name']]; 
			   }
			  
			}
			else
			{
			 // set to empty
			 $address[$key] = ''; 
			 if (empty($address[$uf44['name']]))
			 $address[$uf44['name']] = ''; 
			 
			}
			
			if (!isset($address[$uf44['name']])) $address[$uf44['name']] = ''; 
			
		 }
		 
		  foreach ($address as $ka => $kv)
		  {
		    if ($kv === 'false') $address[$ka] = false;
		  }
		  
		
		  if (!empty($address))
		  if (!empty($stopen))
		  {
		  $user = JFactory::getUser(); 
				if (!empty($user->email))
					{
					  $address['email'] = $user->email; 
					  $address['shipto_email'] = $user->email; 
					}
		  
		  $cart->ST = $address; 
		  $cart->STsameAsBT = 0; 

		  }
		  
		  
		  if ((!$ajax) && (!empty($address)))
		  {
		  $cart->ST = $address; 
		  $cart->STsameAsBT = 0; 
		  }
		  if ((!$ajax) && (empty($address)))
		  {
		   // there is no ST info: 
		   $cart->ST = 0; 
		   $cart->STsameAsBT = 1; 
		  }
		 
		 
		  
		 // if we have the user unlogged, and he is using BT, but ST is not deleted from the cart:
		 //	 if ($ajax)
		 if (((!$ajax) && (empty($address))) || (empty($stopen)))
		 {
		   $cart->ST = 0; 
		   $cart->STsameAsBT = 1; 
		 }
		 
		 
		 
		 
	

	}
	
	function setAddress2(&$cart)
	{
	  $address = array(); 
	  $address['virtuemart_country_id'] = JRequest::getInt('virtuemart_country_id', 0); 
	  $address['zip'] = JRequest::getVar('zip', ''); 
	  $address['virtuemart_state_id'] = JRequest::getInt('virtuemart_state_id', ''); 
	  $address['address_1'] = JRequest::getVar('address_1', ''); 
	  $address['address_2'] = JRequest::getVar('address_2', ''); 
	  $cart->ST = $address; 
	  // not used $ship_to_info_id = JRequest::getVar('ship_to_info_id'); 
	}	
	
	
	function checkOPCCaptcha($retUrl='')
	{
	 // before we do anything, let's check captcha: 

include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
JPluginHelper::importPlugin('system');
   JPluginHelper::importPlugin('captcha');
   $dispatcher = JDispatcher::getInstance();
   $code = ''; 
   $returnValues = $dispatcher->trigger('onCheckAnswer', array($code));  
   foreach ($returnValues as $val)
   {
	   if ($val === false) 
	   {
			
		   	$mainframe = JFactory::getApplication();
			if (empty($retUrl))
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'Captcha: '.OPCLang::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'));
			else
			$mainframe->redirect(JRoute::_($retUrl.'&captcha=1'), 'Captcha: '.OPCLang::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'));
			$mainframe->close(); 
			return; 
	   }
   }
    
   

	}
	
	private function prepareFields()
	{
	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	  		$guest_email = JRequest::getVar('guest_email', ''); 
			$emailn = JRequest::getVar('email', ''); 
		$rg = JRequest::getVar('register_account', 0);
		
		if ((!empty($guest_email)) && (empty($rg)))
		{
		  JRequest::setVar('email', $guest_email); 
		  JRequest::setVar('register_account', JRequest::getVar('register_account', 0)); 
		}
		
		if (!empty($guest_email) && (empty($emailn)))
		 {
		   JRequest::setVar('email', $guest_email); 
		 }
		
	  // security: 
	  JRequest::setVar('virtuemart_shoppergroup_id', null, 'post');
	  
	  $email = JRequest::getVar('email', ''); 
	  $stemail = JRequest::getVar('shipto_email', ''); 
	  if (empty($stemail)) JRequest::setVar('shipto_email', $email); 
	  
	   // password modification in OPC
	  $pwd = JRequest::getVar('opc_password', '', 'post', 'string', JREQUEST_ALLOWRAW); 
	  //if (!empty($pwd)) 
	  // stAn 2.0.140: we always set password to opc_password, so it doesn't mix with the login password
	   {
	     // raw
	     $_POST['password'] = $pwd; 
	     JRequest::setVar('password', $pwd); 
		 
		 
	   }
	    if (in_array('password2', $custom_rendering_fields))
				    {
					  
					  $p1 = JRequest::getVar('password'); 
					  if (!empty($p1))
					   {
					     $_POST['opc_password2'] = $p1; 
					     JRequest::setVar('opc_password2', $p1); 
						 JRequest::setVar('password2', $p1); 
						 
					   }
					  
					}
	  
	   // key captcha support: 
	  $first_name = JRequest::getVar('opc_first_name');
	  if (!empty($first_name)) 
	   {
	      $_POST['first_name'] = $first_name; 
		  $_GET['first_name'] = $first_name; 
		  $_REQUEST['first_name'] = $first_name; 
	      JRequest::setVar('first_name', $first_name); 
	   }
	  
	  $opc_password2 = JRequest::getVar('opc_password2', ''); 
	  $password2 = JRequest::getVar('password2', ''); 
	  if (empty($password2) && (!empty($opc_password2)))
	  {
	      $_POST['password2'] = $opc_password2; 
		  $_GET['password2'] = $opc_password2; 
		  $_REQUEST['password2'] = $opc_password2; 
	      JRequest::setVar('password2', $opc_password2); 
	  
	  }
	   
	     // if we used just first name then create a full name by string separation: 
	  $fname = JRequest::getVar('first_name', ''); 
	  $lname = JRequest::getVar('last_name', ''); 
	  if (!empty($fname) && (empty($lname)))
	   {
	      $a = explode(' ', $fname); 
		  if (count($a)>1)
		   {
		     JRequest::setVar('first_name', $a[0]);
			  unset($a[0]); 
			  $lname = implode(' ', $a); 
			 JRequest::setVar('last_name', $lname); 
		   }
		  else 
		   {
		     // no last name
			 JRequest::setVar('last_name', '   '); 
		   }
	   }
	   $fname = JRequest::getVar('shipto_first_name', ''); 
	  $lname = JRequest::getVar('shipto_last_name', ''); 
	  if (!empty($fname) && (empty($lname)))
	   {
	      $a = explode(' ', $fname); 
		  if (count($a)>1)
		   {
		     JRequest::setVar('shipto_first_name', $a[0]);
			  unset($a[0]); 
			  $lname = implode(' ', $a); 
			 JRequest::setVar('shipto_last_name', $lname); 
		   }
		  else 
		   {
		     // no last name
			 JRequest::setVar('shipto_last_name', '   '); 
		   }
	   }
	   // we need to find in what type we are
	  $ship_to_id = JRequest::getVar('shipto_logged', false); 
	  $bt_id = JRequest::getVar('ship_to_info_id_bt', false); 
	  
	  $test = JRequest::getVar('ship_to_info_id', false); 
	   if (empty($test))
	  if (!empty($bt_id)) 
	  {
	  $_POST['ship_to_info_id'] = $bt_id; 
	  JRequest::setVar('ship_to_info_id', $bt_id); 
	  }
	  
	  
	}
	
	
	private function setCartAddress(&$cart)
	{
	 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 require_once(JPATH_OPC.DS.'overrides'.DS.'cart_override.php'); 
	  require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 
	  
	  $OPCcheckout = new OPCcheckout($cart); 
	  $loader = new OPCloader(); 
	  
	  $obj = new stdClass; 
	  $obj->cart = $cart; 
	  
	  $tos_required = $loader->getTosRequired($obj); 
	  
	   if (!empty($op_no_display_name))
	 {
	   JRequest::setVar('name', OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_ADDRESS_1'));
	   JRequest::setVar('shipto_name', OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_ADDRESS_1'));
	 }
	
	  if ($tos_required)
	  {
	    if (!empty($post['tosAccepted']))
		{
	     $cart->tosAccepted = 1; 
		 $cart->BT['agreed'] = 1; 
		 if (!empty($cart->ST)) $cart->ST['agreed'] = 1; 
		 JRequest::setVar('agreed', 1); 
		 JRequest::setVar('shipto_agreed', 1); 
		}
		else
		{
		 
		}
	  }
	  else
	  {
	     JRequest::setVar('agreed', 1); 
		 JRequest::setVar('shipto_agreed', 1); 
		 JRequest::setVar('tosAccepted', 1); 
	  }
	
	    // we need to find in what type we are
	  $ship_to_id = JRequest::getVar('shipto_logged', false); 
	  $bt_id = JRequest::getVar('ship_to_info_id_bt', false); 
	  
	  $test = JRequest::getVar('ship_to_info_id', false); 
	  
	 
	  
	  if (!empty($ship_to_id) && (!empty($bt_id)))
	   {
	     // let's set BT id as the BT address
	     
		 $sa = JRequest::getVar('sa', false); 
		 
		 if ($sa == 'adresaina')
		  {
		    $stopen = true; 
			JRequest::setVar('shiptoopen', true); 
		  }
		  else 
		  {
		  $stopen = false; 
		  JRequest::setVar('shiptoopen', false); 
		  }
		 
		  //$this->setAddress($cart, false, true);
		  $this->setAddress($cart, false);
		 
		  if ($stopen)
		   {
		  $cart->selected_shipto = $ship_to_id; 
		  $cart->STsameAsBT = 0; 	
		  }
		  else 
		   {
		     $cart->ST = 0; 
			 $cart->selected_shipto = $bt_id; 
			 $cart->STsameAsBT = 1; 
		   }
	   }
	  else
	  $this->setAddress($cart, false); 
	}
	
	function checkout()
	{

	  // before we do anything, let's check captcha: 

include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 

		if (!class_exists('OPCLang'))
		require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	OPCLang::loadLang(); 


if (!isset($opc_memory)) $opc_memory = '128M'; 
ini_set('memory_limit',$opc_memory);
ini_set('error_reporting', 0);
// disable error reporting for ajax:
error_reporting(0); 
	
	$logged = false; 
	if(JFactory::getUser()->guest) {
		
	}
	else { 
		$logged = true; 
		
	}  
   
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   if (((!empty($enable_captcha_logged)) && ($logged)) || ((!empty($enable_captcha_unlogged)) && (!$logged)))
   {
		$this->checkOPCCaptcha(); 
    
   
	   }
		
	  
	 
	  
	  
	  
	  if (!class_exists('VmConfig'))	  
	  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	  VmConfig::loadConfig(); 

	  // since vm2.0.21a we need to load the language files here
      
	  OPCLang::loadLang(); 



	  
	  $this->prepareFields(); 
	  
	  

	 
	   // fedex multibox fix: 
	  
	 
	  
	  // register user first: 
	  $reg = JRequest::getVar('register_account'); 
	  if (empty($reg)) $reg = false; 
	  else $reg = true; 
	  
	  
	  
	  // ENABLE ONLY BUSINESS REGISTRATION WHEN REGISTER_ACCOUNT IS SET AS BUSINESS FIELD
	  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
	  
	   $is_business = JRequest::getVar('opc_is_business', 0); 
		
		
	    // we need to alter shopper group for business when set to: 
	     $is_business = JRequest::getVar('opc_is_business', 0); 
		if (!empty($business_fields))
	   if (!$is_business)
	   {
		   // manage required business fields when not business selected: 
		   foreach ($business_fields as $fn)
		   {
		   /*
				$x1 = JRequest::getVar($fn); 
				if (empty($x1))
				JRequest::setVar($fn, '_'); 

				$x1 = JRequest::getVar('shipto_'.$fn); 
				if (empty($x1))
				JRequest::setVar('shipto_'.$fn, '_'); 
			*/
			  
		   }
	   }
	   
	  if ($reg)
	  if (!empty($business_fields))
	  if (in_array('register_account', $business_fields))
	   {
	    
		 if (empty($is_business))
		  {
		    $reg = false;
		  }
	   }
		 
		 
	  
	  
	
	  
	  
	  
	  
	  //if (!class_exists('VirtueMartControllerUser'))
	  //require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'controllers'.DS.'user.php'); 
	  //$userC = new VirtueMartControllerUser(); 

	  
	  

	  $cart = VirtueMartCart::getCart(false);
	   
	  $mainframe = Jfactory::getApplication();
   $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );
	  $cart->paymentCurrency = $virtuemart_currency_id; 
	  
	   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
	      OPCShopperGroups::setShopperGroupsController($cart); 
	  
	  if (method_exists($cart, 'prepareCartData'))
	   {
	     $cart->prepareCartData(false); 
	   }
	  
	  
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	  
	  $cart->virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', ''); 
	  $cart->virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', ''); 
	  $coupon = JRequest::getVar('opc_coupon_code_returned', ''); 
	  if (!empty($coupon) && (empty($cart->couponCode))) $cart->couponCode = $coupon; 
	  
	  $this->runExt($cart); 
	  
	  if (method_exists($cart, 'prepareCartProducts'))
	  $cart->prepareCartProducts(); 
	  
	  $this->setCartAddress($cart); 
	 
	
	  
	  $this->setExtAddress($cart, false);
		
		
		
		
	 // k2 mod with recaptcha enabled
	  $session = JFactory::getSession(); 
	  $orig = $session->get('socialConnectData'); 
	  $session->set('socialConnectData', true); 
	  // end p1 k2 mod with recaptcha enabled
	 

	     
	 
		  //$this->setShopperGroups($cart); 	  
	  
	  $data = JRequest::get('post');
	   
	  $data['quite'] = true; 
				
	
		
	    //suppress messages from thsi function 
	    $this->saveData($cart,$reg, false, $data); 
		

	  
	  
	  if (!empty($allow_sg_update))
	  $this->storeShopperGroup($data, true); 
	  

	   
	  $userModel = OPCmini::getModel('user');	   
	  if (method_exists($userModel, 'getCurrentUser'))
		{
		 $user = $userModel->getCurrentUser();
		 self::$shopper_groups = $user->shopper_groups; 
	    }

	  // k2 mod with recaptcha enabled
	  if (empty($orig))
	  $session->clear('socialConnectData'); 
	  else
	  $session->set('socialConnectData', $orig); 
	  // end p2 k2 mod with recaptcha enabled
	 
	
	
	  
	  if (!class_exists('VirtueMartControllerCartOpc'))
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'cartcontroller.php'); 
	  $cartcontroller = new VirtueMartControllerCartOpc(); 
	  

	  $po = JRequest::getVar('socialNumber', ''); 
	  if (method_exists($cartcontroller, 'setshipment'))
	  $cartcontroller->setshipment($cart); 
	  // fix fedex multibox
	  $saved_bt = $cart->BT; 
	  unset($_SESSION['load_fedex_prices_from_session']); 
	  if (method_exists($cartcontroller, 'setpayment'))
	  $cartcontroller->setpayment($cart); 

	  if ($cart->BT != $saved_bt)
	   {
	     if (empty($cart->ST) || ($cart->STsameAsBT))
		  {
		    $cart->STsameAsBT = 0; 
		    $cart->ST = $saved_bt; 
			$text = JText::_('COM_ONEPAGE_USER_ENTERED_ADDRESS'); ; 
			if ($text == 'COM_ONEPAGE_USER_ENTERED_ADDRESS')
		    $text = 'User Entered Address (Ship To)'; 
			$cart->ST['address_type_name'] = $text; 
			
			$cart->BT['address_type_name'] = JText::_('COM_ONEPAGE_ADDRESS_HAD_CHANGED_NAME'); 
			//$cart->BT = $saved_bt; 
		  }
		  else
		  if ($cart->ST != $saved_bt)
		  {
		      $address_changed = JText::_('COM_ONEPAGE_ADDRESS_HAD_CHANGED'); 
		      $mainframe = JFactory::getApplication();
			  $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), OPCLang::_('COM_ONEPAGE_ADDRESS_HAD_CHANGED'));
			  return; 
		  }
		  else
		  {
		    // $cart->BT = $saved_bt; 
		  }
	   }
	  
	  
	  // security: 
	  JRequest::setVar('html', ''); 
	 

	  $post = JRequest::get('post'); 
	  
	 
	  if (empty($cart->BT)) 
	   {
	   
	   }

	
	   
	 // fix the customer comment
	 // $cart->customer_comment = JRequest::getVar('customer_comment', $cart->customer_comment);
	 $cc = JRequest::getVar('customer_comment', ''); 
	 $cc2 = JRequest::getVar('customer_note', '');
     
	 if (empty($cart->customer_comment)) $cart->customer_comment = $cc2.$cc;
	 else $cart->customer_comment = $cc2.$cc;
	 
	 
	  JRequest::setVar('customer_comment', $cart->customer_comment); 	 
	  JRequest::setVar('customer_note', $cart->customer_comment); 	 
	  
	  $OPCcheckout = new OPCcheckout($cart); 
	  $loader = new OPCloader(); 
	  $OPCcheckout->checkoutData($cart, $OPCcheckout); 

	  
	  
	  
	  if ($cart->_dataValidated)
		{
		
		  

		
			$cart->_confirmDone = true;
			$order = null; 
			$output =  $OPCcheckout->confirmedOrder($cart, $this, $order);
			
			
			
			$session = JFactory::getSession();
			if (!empty($order))
			if ( $order['details']['BT']->order_total <= 0) {

		  $dispatcher = JDispatcher::getInstance();
		  $returnValues = $dispatcher->trigger('registerOrderAttempt', array(&$order));
			
				$cart->emptyCart();
			}
		}
		else 
		{
			$mainframe = JFactory::getApplication();
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'Captcha: '.OPCLang::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'));

		}
			// some extensions somehow reset the language, and we need to set it to the proper one: 
	  $lang     = JFactory::getLanguage();
      $tag = $lang->getTag(); 
      $filename = 'com_virtuemart';
      $lang->load($filename, JPATH_ADMINISTRATOR, $tag, true);
      $lang->load($filename, JPATH_SITE, $tag, true);

	  //$post = JRequest::get('post');
		$mainframe =& JFactory::getApplication();		  
	  			$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();
	  
	//  $html = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'post', 'STRING', JREQUEST_ALLOWRAW);
	 
	  
	  
	  $document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
	  $cart->setCartIntoSession(); 
	  // now the plugins should have already loaded the redirect html
	  // we can safely 
	  $virtuemart_order_id = $cart->virtuemart_order_id; 
	   
	    
		
	   JRequest::setVar('view', 'cart'); 
	  $_REQUEST['view'] = 'cart'; 
	  $_POST['view'] = 'cart'; 
	  $_GET['view'] = 'cart'; 

	  

	 
	  $view = $this->getView('cart', 'html');
	  
	  
	  
	   $display_title = JRequest::getVar('display_title',true);
	   
	   $view->display_title = $display_title;
	    
	
		
	  
	  $view->setLayout('order_done');
	  
	  $view->assignRef('html', $output); 
	  
	  
	  // commented for 2.0.22a
	  //JRequest::setVar('html', $output);  
	  //$view->html = $output; 
	  
	    // Display it all
	   ob_start(); 
	   $view->display();
	   $html1 = ob_get_clean(); 
	   
	   
	    $items = $pathway->getPathwayNames(); 
	  $skipp = false; 
	  foreach ($items as $i)
	  {
	    if ($i == OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'))
		$skipp = true; 
	  }
	  if (!$skipp)
	  $pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
	  
	  
	  
	   
	   
	    if (empty($html1)) $html1 = $output; 
	   JRequest::setVar('view', 'opccart'); 
	   JRequest::setVar('contoller', 'opccart'); 
	   $html2 = ''; 
	   if (!empty($append_details))
	   {
	    // ok, lets try to alter it with the order details 
	   	JRequest::setVar('order_pass',$order['details']['BT']->order_pass);
		JRequest::setVar('order_number',$order['details']['BT']->order_number);
		JRequest::setVar('virtuemart_order_id',$order['details']['BT']->virtuemart_order_id);
		
		
		//JRequest::setVar('tmpl', 'component'); 
			
		$html2 = $this->getVMView('orders', array(), 'orders', 'details', 'html'); 
		
		
	   }
	   
	   $allhtml = $html1.$html2; 
	   $this->runExtAfter($allhtml); 
	  
	  
	   
	   echo $allhtml; 
	   JRequest::setVar('html', ''); 
	   
	    JRequest::setVar('html', $allhtml); 
	   
	   
	    JRequest::setVar('view', 'cart'); 
	   $_REQUEST['view'] = 'cart'; 
	   $_POST['view'] = 'cart'; 
	   $_GET['view'] = 'cart'; 
	   if (!empty($theme_fix1))
	   {
	  
	   JRequest::setVar('layout', 'order_done'); 
	   }
	   
	   
	  
	   return; 
	   
	  
	}
	
	// original code from shopfunctionsF::renderMail
	private static function getVMView($viewName, $vars=array(),$controllerName = NULL, $layout='default', $format='html')
	{
	    $originallayout = JRequest::getVar( 'layout' );
	  	if(!class_exists('VirtueMartControllerVirtuemart')) require(JPATH_VM_SITE.DS.'controllers'.DS.'virtuemart.php');
// 		$format = (VmConfig::get('order_html_email',1)) ? 'html' : 'raw';
		
		// calling this resets the layout
		$controller = new VirtueMartControllerVirtuemart();
		JRequest::setVar( 'layout', $layout );
		

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		$controller->addViewPath(JPATH_VM_SITE.DS.'views');
		
		$view = $controller->getView($viewName, $format);
	
		
		$view->assignRef('layout', $layout); 
		$view->assignRef('format', $format); 
		
		$view->setLayout($layout); 
		
		if (!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst ($controllerName) ;
		if (!class_exists($controllerClassName)) require(JPATH_VM_SITE.DS.'controllers'.DS.$controllerName.'.php');

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		$view->addTemplatePath(JPATH_VM_SITE.'/views/'.$viewName.'/tmpl');

		$vmtemplate = VmConfig::get('vmtemplate','default');
		if($vmtemplate=='default'){
			if(JVM_VERSION >= 2){
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery($q);
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template){
			$view->addTemplatePath(JPATH_ROOT.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.$viewName);
		} else {
			if(isset($db)){
				$err = $db->getErrorMsg() ;
			} else {
				$err = 'The selected vmtemplate is not existing';
			}
			if($err) vmError('renderMail get Template failed: '.$err);
		}

		foreach ($vars as $key => $val) {
			$view->$key = $val;
		}
		ob_start(); 
		$html = $view->display();
		$html2 = ob_get_clean(); 
		
		JRequest::setVar( 'layout', $originallayout );
		return $html.$html2; 
	}
	
	// support for non-standard extensions
	// will be changed in the future over OPC extension tab and API
	function runExt(&$cart)
	{
	  require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_set_shipping.php'); 
	
	}
	/**
	*
	*  ORIGINAL CODE FROM: \components\com_virtuemart\helpers\cart.php
	*
	 * Validate the coupon code. If ok,. set it in the cart
	 * @param string $coupon_code Coupon code as entered by the user
	 * @author Oscar van Eijk
	 * TODO Change the coupon total/used in DB ?
	 * @access public
	 * @return string On error the message text, otherwise an empty string
	 */
	function setCoupon(&$cart)
	{
	  if (!class_exists('CouponHelper')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'coupon.php');
		}
		
		$coupon_code = JRequest::getVar('new_coupon', $cart->couponCode); 
		
		JRequest::setVar('coupon_code', $coupon_code); 
		// stAn, getCartPrices calls coupon process !
		$prices = $cart->getCartPrices();
		$msg = CouponHelper::ValidateCouponCode($coupon_code, $prices['salesPrice']);
		if (!empty($msg)) {

			$cart->couponCode = '';
			$cart->setCartIntoSession();
			JFactory::getApplication()->enqueueMessage($msg); 
			
			return $msg;
		}
		$cart->couponCode = $coupon_code;
		
		$cart->setCartIntoSession();
		// THIS IS NOT TRUE AS THE COUPON HAS NOT YET BEEN PROCESSED: return 'Virtuemarts cart says: '.OPCLang::_('COM_VIRTUEMART_CART_COUPON_VALID');
		/*
		JPluginHelper::importPlugin('vmcoupon');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmCouponHandler', array($_code,&$this->_cartData, &$this->_cartPrices));
		if(!empty($returnValues)){
			foreach ($returnValues as $returnValue) {
				if ($returnValue !== null  ) {
					return $returnValue;
				}
			}
		}
		
		if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		else $vm2015 = false; 
	    if ($vm2015)
	    $calc->setCartPrices(array()); 
		*/
		// this will be loaded by OPC further
        // $calc->getCheckoutPrices(  $ref->cart, false);
		
		
	}
	
	
	function setSecret()
	{
	
	}
	
	private static $shopper_groups; 
	
	private function softVatCheck($vatid)
	{
	  $eu = array('BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'EL', 'ES', 'FR', 'HR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'UK', 'GR', 'GB'); 
	  $vat_country = substr($vatid, 0, 2); 
	  $vat_country = strtoupper($vat_country); 
	  $vat_number = substr($vatid, 2); 
	  $vat_number = strtoupper($vat_number); 
	  if (!in_array($vat_country, $eu)) return 'PREFIXERROR'; 
	  
	  if ($vat_country == 'GR') $vat_country = 'EL'; 
	  if ($vat_country == 'UK') $vat_country = 'GB'; 
	  
	  $vat_number = str_replace(' ', '', $vat_number); 
	  $vat_number = str_replace('-', '', $vat_number); 
	  $vat_number = str_replace('/', '', $vat_number); 
	  if (!class_exists('SwOfficialNumberValidator'))
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'softvat.php'); 
	  
	  $SwOfficialNumberValidator = new SwOfficialNumberValidator(); 
	  
	  //http://ec.europa.eu/taxation_customs/vies/faq.html#item_2
	  switch ($vat_country)
	  {
	    case 'AT':
		  if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 characters'; 
		  if (substr($vat_number, 0, 1)!= 'U')  return 'Invalid format, expected U after country code'; 
		  break; 
		case 'BE': 
		   if (!strlen($vat_number) != 10) return 'Invalid format, expected 10 digits'; 
		   if (!is_numeric($vat_number)) return 'Invalid format, expecting a number after country code'; 
		   $check = $SwOfficialNumberValidator->checkBeVat($vat_number); 
		   if (empty($check)) return 'Vat number did not pass checksum validation'; 
		   break; 
		case 'BG': 
		   if (!((!strlen($vat_number) == 10) || ((!strlen($vat_number) == 9)))) return 'Invalid format, expected 9 or 10 digits'; 
		   if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
		   break; 
		 case 'CY': 
		    if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			break; 
	    case 'CZ': 
		    if (!((strlen($vat_number)>=8) && ((strlen($vat_number)<=10)))) return 'Invalid format, expected 8 to 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'DE': 
		    if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'DK':
		     if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			break; 
		case 'EE': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'EL': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'ES': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			$first = substr($vat_number, 0, 1); 
			$last = substr($vat_number, -1); 
			if (ctype_digit ($first) && (ctype_digit ($last))) return 'Invalid format, first and last character after language code cannot be numeric'; 
			break; 
		case 'FI': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'FR': 
			$first = substr($vat_number, 0,1); 
			$s = substr($vat_number, 1, 1); 
			if (ctype_digit ($first) || (ctyle_digit($s))) return 'Invalid format, first and second characters cannot be a number';
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 characters'; 
			$res = $SwOfficialNumberValidator->checkFrVat($vat_number); 
			if (empty($res)) return 'Vat number did not pass checksum validation'; 
			break; 
		case 'GB': 
		    $p = array(9, 12, 5); 
			$l = strlen($vat_number); 
			if (!in_array($l, $p))  return 'Invalid format, expecting either 5, 9, or 12 characters after the country code';
			if (($l == 9) || ($l==12))
			 {
			   if (!ctype_digit($vat_number))  return 'Invalid format, expecting a number after country code';  
			 }
			 break; 
		case 'HR':
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'HU': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'IE': 
			if (!((strlen($vat_number)>=8) && ((strlen($vat_number)<=9)))) return 'Invalid format, expected 8 or 9 characters'; 
			break; 
		case 'IT': 
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'LT': 
			 $p = array(9, 12); 
			 $l = strlen($vat_number); 
			 if (!in_array($l, $p))  return 'Invalid format, expecting either 9 or 12 digits after the country code';
			 if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			 break; 
		case 'LU':
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'LV':
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'MT': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'NL': 
			 if (!strlen($vat_number) != 12) return 'Invalid format, expected 11 digits'; 
			 if (substr($vat_number, 9, 1) != 'B') return 'Invalid format, expected B at 10th position'; 
			 break; 
		case 'PL': 
			if (!strlen($vat_number) != 10) return 'Invalid format, expected 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'PT': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'RO': 
			if (!((strlen($vat_number)>=2) && ((strlen($vat_number)<=10)))) return 'Invalid format, expected 2 to 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'SE': 
			if (!strlen($vat_number) != 12) return 'Invalid format, expected 12 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'SI': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'SK': 
			if (!strlen($vat_number) != 10) return 'Invalid format, expected 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		
		
			
	  }
	  
	}
	
	private function checkbitVat()
	{
	  include(JPATH_SITE.DS.'components'.DS.'overrides'.DS.'system'.DS.'bit_vm_check_vatid'.DS.'checkvat.php'); 
	  return $return; 
	}
	
	
	function opc()
	{
	

	  if (!class_exists('VmConfig'))
	  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	  VmConfig::loadConfig(); 
	  require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 
	  OPCloader::$debugMsg = ''; 

		if (!class_exists('OPCLang'))
		require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	OPCLang::loadLang(); 
	  
	  
	  
	  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
	  
	  //$this->setShopperGroups(); 
	  
	  
	  	$userModel = OPCmini::getModel('user');
		
		
	  $cmd = JRequest::getVar('cmd', ''); 
	  if ($cmd === 'checkbitvat')
	  $checkvat = $this->checkBitVat(); 
	  if ($cmd === 'checkvatopc')
	  {
	    $checkvat = $this->checkOPCVat(); 
	  }
		
		OPCShopperGroups::setShopperGroupsController(); 
		
		if (method_exists($userModel, 'getCurrentUser'))
		{
			
		$user = $userModel->getCurrentUser();
		
		self::$shopper_groups = $user->shopper_groups; 
		
		if (!empty($user->virtuemart_shipmentmethod_id))
		{
		$user->virtuemart_shipmentmethod_id = 0; 
		$user->virtuemart_paymentmethod_id = 0; 
		}
		}
		
		$session = JFactory::getSession();
		
		$b = $session->set('eurobnk', null, 'vm'); 
		
		
		if (empty($euvat_shopper_group))		 
		{
	  jimport( 'joomla.html.parameter' );
	  if (class_exists('plgSystemBit_vm_change_shoppergroup'))
	  {
		   $session = JFactory::getSession();
		   $sg = $session->get('vm_shoppergroups_add', array(), 'vm'); 
		   
		   $dispatcher =& JDispatcher::getInstance();
		   JPluginHelper::importPlugin('system', 'plgSystemBit_vm_change_shoppergroup', true, $dispatcher); // very important
		   $document =& JFactory::getDocument();
		   JRequest::setVar('format_override', 'html'); 
		   $_REQUEST['view'] = 'cart'; 
		   $_REQUEST['option'] = 'com_virtuemart'; 
		   $doctype = $document->getType();
		   
		   $dispatcher->trigger('onAfterRender'); 
		   JRequest::setVar('format_override', 'raw'); 
		   $sg = $session->get('vm_shoppergroups_add', array(),  'vm'); 
		   
		   
	  }
	   }
	  JResponse::setBody('');
	  
	  // security: 
	  JRequest::setVar('virtuemart_shoppergroup_id', null, 'post');
	 if (!class_exists('VmConfig'))	  
	  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	  VmConfig::loadConfig(true); 

	  // since vm2.0.21a we need to load the language files here
      if (method_exists('VmConfig', 'loadJLang'))
	 {
	  $lang = JFactory::getLanguage();
	  $extension = 'com_virtuemart';
	  $lang->load($extension); //  when AJAX it needs to be loaded manually here >> in case you are outside virtuemart !!!

	  VmConfig::loadJLang('com_virtuemart_orders', true); 
	  VmConfig::loadJLang('com_virtuemart_shoppers', true); 

	}	  
	  
       /// load shipping here
	   $vars = JRequest::get('post'); 
	   
	   // custom tag test
	 $cmd = JRequest::getVar('cmd', ''); 
	
	
	
	
	$doc =& JFactory::getDocument();
	$type = get_class($doc); 
	if ($type == 'JDocumentRAW')
	 {
	    //C:\Documents and Settings\Rupos\Local Settings\Temp\scp02371\srv\www\clients\client1\web90\web\vm2\components\com_onepage\overrides\
	    //require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'opchtml.php'); 
		//JFactory::$instance = new JDocumentOpchtml(); 
		//JFactory::$document = new JDocumentOpchtml(); 
	    
	 }
	 /*
	$doc->addCustomTag = create_function('$string', 'return;');  
	$doc->addCustomTag( '<!-- This is a comment. -->' );
     */
   $c = JRequest::getVar('virtuemart_currency_id', 0); 
   
   JRequest::setVar('virtuemart_currency_id', (int)JRequest::getVar('virtuemart_currency_id'));
   
   /* to test the currency: */
   $mainframe = Jfactory::getApplication();
   $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );
   
   
 
   
	// end custom tag test
	   $view = $this->getView('cart', 'html');
	   $cmd = JRequest::getCmd('cmd', ''); 
	
	$return = array(); 
	$return['cmd'] = $cmd; 
	
	if (!empty($checkvat))
	$return['checkvat'] = $checkvat; 
	
	
	
	
	$db = JFactory::getDBO(); 
	// we will check it on each request 
	// to support google autocomplete 
	// if ($cmd == 'checkusername')
	{
			$username = JRequest::getVar('username', ''); 
			$user = JFactory::getUser(); 
			$un = $user->get('username'); 
			if ($un == $username)
			{
			   // do not complain if entering the same username of already registered
			   $return['username_exists'] = false; 
			}
			else
			if (!empty($username))
			{
			$q = "select username from #__users where username = '".$db->getEscaped($username)."' limit 0,1"; 
			$db->setQuery($q); 
			$r = $db->loadResult(); 
			if (!empty($r))
			 {
			   $return['username_exists'] = true; 
			 }
			 else
			   $return['username_exists'] = false; 
			}
	}
	
	
	
	if ($cmd === 'checkemail')
	{
			$email = JRequest::getVar('email', ''); 
			$return['email'] = $email;
			$user = JFactory::getUser(); 
			$ue = $user->get('email'); 
			if ($email == $ue)
			{
			  // do not complain if user is logged in and enters the same email address
			  $return['email_exists'] = false; 
			}
			else
			if (!empty($email))
			{
			$q = "select email from #__users where username = '".$db->getEscaped($email)."' or email = '".$db->getEscaped($email)."' limit 0,1"; 
			$db->setQuery($q); 
			$r = $db->loadResult(); 
			if (!empty($r))
			 {
			   $return['email_exists'] = true; 
			 }
			 else 
			 $return['email_exists'] = false; 
			}
	}
	
	  
	   if ($cmd === 'get_klarna_address')
	    {
		
		  if (file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'klarna'.DS.'klarna'.DS.'api'.DS.'klarna.php'))
		   {
		    
		     $klarnaaddress = $this->getKlarnaAddress(); 
			 
			 if (!empty($klarnaaddress))
			 {
					
			 echo json_encode( array('cmd'=>'getKlarna', 
			 'shipping'=>'opc_do_not_update', 
			 'klarna'=>$klarnaaddress,
			 'totals_html'=>'', 
			 'payment'=>''
			 
			 )  ); 
			 $mainframe = JFactory::getApplication();
			 // do not allow further processing
			 $mainframe->close(); 
			 }
		   }
		}
	   
	   
	   if (!defined('JPATH_OPC')) define('JPATH_OPC', JPATH_SITE.DS.'components'.DS.'com_onepage'); 
	   require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 
	   require_once(JPATH_OPC.DS.'helpers'.DS.'ajaxhelper.php'); 
	   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	   
	   $OPCloader = new OPCloader; 
	   $cart = VirtueMartCart::getCart(false);
	   //$virtuemart_currency_id;  = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );
	   $cart->paymentCurrency = $virtuemart_currency_id; 
	   if (defined('VM_VERSION') && (VM_VERSION >= 3))
	   {
	    
		if (method_exists($cart, 'prepareCartProducts'))
	    $cart->prepareCartProducts(); 
		
		
		
	   }
	   
	   
	 if ($cmd === 'getST')
	 {
	   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loggedshopper.php'); 
	   $return['sthtml'] = OPCLoggedShopper::getSTHtml($cart); 
	   

	 }
	   
	  
	    
		if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
		
		if ($cmd == 'updateattributes')
		{
		  $arr = $this->updateattributes($cart); 
		  if (!empty($arr))
		  foreach ($arr as $key=>$val)
		   $return[$key] = $val; 
		}
	
	   if ($cmd == 'update_product')
		{
	    if (defined('VM_VERSION') && (VM_VERSION >= 3))
		  {
		     $quantity = JRequest::getVar('quantity'); 
			 if (!is_array($quantity))
			  {
			    $cart_virtuemart_product_id = JRequest::getVar('cart_virtuemart_product_id');
				$arr = array($cart_virtuemart_product_id => (int)$quantity); 
				JRequest::setVar('quantity', $arr); 
				
				$x = JRequest::getVar('quantity'); 
				
			  }
		  }
		$cart->updateProductCart(); 
		}
		
		if ($cmd == 'delete_product')
		{
		
		$cart->updateProductCart(); 
		}
	   
	   if ($cmd == 'removecoupon')
	   {
		   JRequest::setVar('coupon_code', ''); 
		   $_REQUEST['coupon_code'] = $_POST['coupon_code'] = $_GET['coupon_code'] = ''; 
		   $cart->couponCode = ''; 
		   $cart->setCartIntoSession();
		   //$this->setCoupon($cart); 
		   
		   $deletecouponmsg = true; 
	   }
	   
	   $cp = 0; 
	   
	   if (method_exists($cart, 'prepareCartProducts'))
	   $cart->prepareCartProducts(); 

	   if (method_exists($cart, 'prepareCartData'))
	   {
	     $cart->prepareCartData(false); 
	   }
	   
	   
	   
	   
	   $stopen = JRequest::getVar('shiptoopen', false); 

	   if ($stopen == 'true')
	   {
	   $stopen = true; 
	   $sa = JRequest::setVar('sa', 'adresaina'); 
	   }
	   //$this->setAddress($cart, true, $stopen); 
	   $this->setAddress($cart); 
	   if ($stopen)
	   $this->setAddress($cart, true, $stopen); 
	   
	   $this->setExtAddress($cart, false, $stopen);
	  
	   // US and Canada fix, show no tax for no state selected
	   if (!isset($cart->BT['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ''; 
	   if (!empty($cart->ST))
	    {
		// if the VM uses BT address instead of ST address in calculation, uncomment the following line: 
		// $cart->BT = $cart->ST;   // this only applies to the display of the checkout, not actual saving of the data
		if (!isset($cart->ST['virtuemart_state_id'])) $cart->ST['virtuemart_state_id'] = ''; 
		}
		
		
	  
	  
	   @header('Content-Type: text/html; charset=utf-8');
	   @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	   @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	 // run vm main controlle due to compatibilty
	   	JPluginHelper::importPlugin('vmextended');
		JPluginHelper::importPlugin('vmuserfield');
		$dispatcher = JDispatcher::getInstance();
		$_controller = 'cart'; 
		$trigger = 'onVmSiteController'; 
		$trigger = 'plgVmOnMainController'; 
		$dispatcher->trigger($trigger, array($_controller));
	
	
	    // this function will reload the taxes on products per country
		
		// this calls api methods as well, let's disable it for now: 
		$cart->virtuemart_shipmentmethod_id = 0; 
	    if (method_exists($cart, 'prepareCartViewData'))
		$cart->prepareCartViewData();
		
		
	
	if (!empty($virtuemart_currency_id))
    $cart->paymentCurrency = $virtuemart_currency_id; 
	   
	  if ($cmd == 'process_coupon')
	  {
	     $cart->couponCode = JRequest::getVar('coupon_code', ''); 
		 
	     $this->setCoupon($cart); 
		
	     // set coupon 
		 
	  }

	  
	   $view->cart = $cart; 
	   $view->assignRef('cart', $cart); 

	


		
	
	   //if (($cmd != 'runpay') && ($cmd != 'refreshPayment'))
	   
	   // this influences the recalculation of the basket:
	   $cmds = array('process_coupon', 'refresh-totals', 'refresh_totals', 'removecoupon', 'delete_product', 'update_product', 'checkvatopc', 'delete_product', 'update_product', 'updateattributes');
	   if ((empty($cmd)) || (in_array($cmd, $cmds)) || (stripos($cmd, 'shipping')!==false)) 
	   {
	   $shipping = $OPCloader->getShipping($view, $cart, true); 
	 
	   }
	   else 
	   {
	     $shipping = 'opc_do_not_update'; 
		 OPCloader::$totals_html = ''; 
	   }
	 
	 
	   
	   $return['shipping'] = $shipping; 
	   
	   
	   
	   if (empty(OPCloader::$inform_html)) OPCloader::$inform_html = array(); 
	   $return['inform_html'] = implode('', OPCloader::$inform_html); 
	   
	   
	   if (!empty($cart->couponCode))
	    {
		  $db = JFactory::getDBO(); 
		  $q = "select * from #__virtuemart_coupons where coupon_code = '".$db->getEscaped($cart->couponCode)."' limit 0,1"; 
		  $db->setQuery($q); 
		  $res = $db->loadAssoc(); 
		  
		  
		  if (!empty($res))
		  if ($res['percent_or_total'] == 'percent')
		   $cp = $res['coupon_value']; 
		  
		  if (empty($cp))
		  if (OPCloader::tableExists('awocoupon'))
		   {
		      $db = JFactory::getDBO(); 
			  $q = "select * from #__awocoupon where coupon_code = '".$db->getEscaped($cart->couponCode)."' and coupon_value_type = 'percent' limit 0,1"; 
			  $db->setQuery($q); 
		      $res = $db->loadAssoc(); 
			  
		      if (!empty($res))
		      $cp = $res['coupon_value']; 
		  
		   }
		}
	   if (!empty($cp))
	   {
	      $cp = (float)$cp; 
	      if (round($cp) == $cp)
		   {
		      $cp = (int)$cp.' %'; 
		   }
		   else
		   {
		     $cp = number_format($cp, 2, '.', ' ').' %'; 
		   }
	   }
	   
	   $return['couponpercent'] = $cp; 
	   
	   
	   // get payment html
	   
	   $num = 0; 
	   
	   if ($cmd == 'runpay')
	   {
	   $view->cart->virtuemart_shipmentmethod_id = JRequest::getVar('shipping_rate_id', ''); 
	   
	   }
	   $isexpress = OPCloader::isExpress($cart); 
	   $ph2_a = $OPCloader->getPayment($view, $num, false, $isexpress); 
	   $ph2 = $ph2_a['html'];
	   
	   
	   $return['payment_extra'] = $ph2_a['extra']; 
	   /*
	   if (!empty($ph_a['extra']))
	   {
	     foreach ($ph_a['extra'] as $key=>$val)
		  {
		    $return['payment_extra'].$val; 
		  }
	   }
	   */
	   if ($cmd == 'runpay')
	   $cart->virtuemart_shipmentmethod_id = null;
	   if (!empty(OPCloader::$totalIsZero))
	    {
		  $hide_payment_if_one = true; 
		  $num = 1; 
		  $ph2 = '<input type="hidden" value="0" name="virtuemart_paymentmethod_id" id="virtuemart_paymentmethod_id_0" />'; 
		  
		}
	   if ((!empty($hide_payment_if_one) && ($num === 1)) || ($isexpress))
	    {
		  $ph = '<div class="payment_inner_html" rel="force_hide_payments">'.$ph2;
		}
		else $ph = '<div class="payment_inner_html" rel="force_show_payments">'.$ph2;
	   $ph .= '</div>'; 
	   $return['payment'] = $ph;
	   
	   $return['totals_html'] = OPCloader::$totals_html; 
	   
	  
	   
	   if (!empty($return['totals_html']))
	   {
	   $session = JFactory::getSession();
	   /*
	   $r = $session->get('opcuniq'); 
	   if (empty($r))
	   {
	   $rand = uniqid('', true); 
       $session->set('opcuniq', $rand);
       $session->set($rand, '0');
	   }
	   */
	   $rand = uniqid('', true); 
	   $return['totals_html'] .= '<input type="hidden" name="opcuniq" value="'.$rand.'" />';
	   }
	   
  $t = $return['shipping'].' '.$return['payment']; 
	   $t = str_replace('//<![CDATA[', '', $t); 
	   $t = str_replace('//]]> ', '', $t); 
	   $t = str_replace('<![CDATA[', '', $t); 
	   $t = str_replace(']]> ', '', $t); 
	   
	   $t = str_replace('#paymentForm', '#adminForm', $t); 
	   //$t = str_replace('jQuery(document).ready(', ' jQuery( ', $t); 
	   $js = array(); 
	   if (strpos($t, '<script')!==false)
	    {
		   $xa = basketHelper::strposall($t, '<script'); 
		   foreach ($xa as $st)
		    {
			  // end of <script tag
			  $x1 = strpos($t, '>', $st+1); 
			  // end of </scrip tag
			  $x2 = strpos($t, '</scrip', $st+1); 
			  $js1 = substr($t, $x1+1, $x2-$x1-1); 
			  $js[] = $js1; 
		      	  
			}
		}
		
	   $return['shipping'] .= JHtml::_('form.token'); 
	   $return['payment'] .= JHtml::_('form.token'); 
	   
	   if (isset(VmPlugin::$ccount))
	   if (!empty($opc_debug))
	    $js[] = "\n".'op_log("Positive cache match: '.VmPlugin::$ccount.'");'; ;
	   
	   if (!empty($opc_debug))
	   if (defined('OPCMEMSTART'))
	   {
		   $mem = memory_get_usage(true); 
		   $memd = $mem - OPCMEMSTART; 
		   $memd = (float)($memd/1024);
		   $memd = number_format ($memd, 0, '.', ' '); 
		   
		   if (!defined('debugmem')) 
		   {
		   $debugmem = $mem-OPCMEMSTART; 
		   $debugmem = (float)($debugmem/1024);
		   }
		   else
		   $debugmem = (float)(debugmem/1024);
		   $debugmem = number_format ($debugmem, 0, '.', ' '); 
		   
		   $mem = (float)($mem/1024);
		   $mem = number_format ($mem, 0, '.', ' '); 
		   
	   	   $js[] = "\n".'op_log("Memory usage: '.$memd.'kb of '.$mem.'kb, debug mem: '.$debugmem.'kb ");'; ;
	   }
	   $return['javascript'] = $js; 
	   
	   $return['opcplugins'] = OPCloader::getPluginData($cart); 
	   
	   $x = JFactory::getApplication()->getMessageQueue(); 
	   $arr = array(); 
	   $disablarray = array( 'Unrecognised mathop', JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS')); 
	   
	   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_disable_msgs.php'); 
	      $euvat_text = array('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_VALID', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_COUNTRYCODE', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT_REASON', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_SERVICE_UNAVAILABLE', 
		  'VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_REQUIRED'); 
	   
	   foreach ($euvat_text as $k=>$t)
	   {
	   $tt = JText::_($t); 
	   $euvat_text[$k] = substr($tt, 0, 20); 
	   
	   }
	   $euvatinfo = ''; 
	   
	   			 

	   
	   $remove = array(); 
	   foreach ($x as $key=>$val)
	    {
		  
		  
		     foreach ($euvat_text as $kx => $eutext)
			 {
			 // echo 'comparing '.$eutext.' with '.$val['message']."<br />\n"; 
			 if (stripos($val['message'], $eutext)!==false)
			   {
			 // die('match found'); 
			     $euvatinfo .= $val['message']; 
			     $remove[] = $key; 
				 break;
			   }
			 }
			 
		  
		  foreach ($disablarray as $msg)
		  {
		  
		     
			  if (stripos($val['message'], $msg)!==false)
			  {
				  $remove[] = $key; 
			  }
			  if (stripos($val['message'], JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID'))!==false)
			  {
				  $cart->couponCode = ''; 
				  $cart->setCartIntoSession();

			  }
		  }
		  
		}
		
		if (!empty($euvatinfo)) $return['checkvat'] = $euvatinfo; 
		foreach ($x as $key=>$val)
		{
			if (!in_array($key, $remove))
			$arr[] = $val['message']; 		
		}
		
	  
	     
	    $return['msgs'] = $arr; 
	   
	   
	    if (!empty($opc_debug))
	   	if (!empty(OPCloader::$debugMsg))
			{
				$return['debug_msgs'] = OPCloader::$debugMsg;
				
				
				
			}

	   if (!empty($cart->couponCode))
	   {
		   $return['couponcode'] = $cart->couponCode; 
	   }
	   else 
		   $return['couponcode'] = ''; 

	   
	   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
	      OPCShopperGroups::setShopperGroupsController($cart); 
	   $upd = array('update_product', 'delete_product', 'process_coupon', 'removecoupon', 'updateattribute', 'refreshall', 'updateattributes', 'checkvatopc', 'checkvat', 'vat_info'); 
	   if (in_array($cmd, $upd) || (stripos($cmd, 'shipping')!==false) || (!empty($ajaxify_cart)))
	   {
	      if ($shipping=='opc_do_not_update') $shipping = ''; 
		  
		  if (!empty($ph2_a['html']))
		  $payment_html = $op_payment = '<div id="payment_html">'.$ph2_a['html'].'</div>'; 
		  else $payment_html = $op_payment = '<div id="payment_html">&nbsp;</div>'; 
		  
		  $html = $this->getCartHtml($cart, $OPCloader, $shipping, $payment_html); 
		  $return['basket'] = $html;
		 
		  
	   }
	   $cart->virtuemart_shipmentmethod_id = 0; 
	   $cart->virtuemart_paymentmethod_id = 0; 
	   $cart->setCartIntoSession();
	   
	  
	   $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
	   //echo json_encode(''); 
	   echo json_encode($return); 
	   //echo $shipping;
	   
	    $dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('updateAbaData', array());
	  
	    
	  
	  
	   $mainframe = JFactory::getApplication();
	   // do not allow further processing
	   $mainframe->close(); 
	   die(); 
	}
	
	public function &getCartHtml(&$cart, &$OPCloader, $shipping_method_html='', $op_payment='' )
	{
if (!defined('JPATH_OPC'))
			define('JPATH_OPC', JPATH_SITE.DS.'components'.DS.'com_onepage'); 
  
		  if (!class_exists('VirtueMartViewCart'))
			  require(JPATH_OPC.DS.'overrides'.DS.'virtuemart.cart.view.html.php'); 
		  
		  $VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
		  
		  //$ref = new VirtueMartViewCart(); 
		  //$ref->cart =& $cart; 
		  if (!class_exists('OPCrenderer'))
		  require (JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'renderer.php'); 
		  $ref = OPCrenderer::getInstance(); 
		  $ref->cart = $cart; 
		  
		  $op_coupon = $op_coupon = $OPCloader->getCoupon($ref);
		  //$html = $OPCloader->getBasket($ref, false, $op_coupon);
		  
		  $html = $OPCloader->getBasket($ref, false, $op_coupon, $shipping_method_html, $op_payment); 
		  return $html;  		
	}		
	
	private function runExtAfter(&$allhtml)
	{

	 $allhtml = str_replace('Restricted access!', '', $allhtml); 
	  $allhtml = str_replace('Order not found!', '', $allhtml); 
	  $allhtml = str_replace('It may have been deleted.', '', $allhtml); 

	
	  if (stripos($allhtml, 'document.vm_payment_form.submit()')!==false)
	   {
	      $allhtml = str_replace('href="javascript:document.vm_payment_form.submit();"', 'href="#" onclick="return opcFormSubmit();"', $allhtml); 
	      $allhtml .= '
<script type="text/javascript">
function opcFormSubmit()
{
if (typeof document.vm_payment_form.submit != "undefined") 
 {
 document.vm_payment_form.submit(); 
 }
else 
 {
    if (typeof document.vm_payment_form[0] != "undefined")
	{
	 document.vm_payment_form[0].submit(); 
	}
	
 }
 return false; 
}
</script>'; 
		
	   }
	}
	
	
	/**
	 * Save the user info. The saveData function dont use the userModel store function for anonymous shoppers, because it would register them.
	 * We make this function private, so we can do the tests in the tasks.
	 *
	 * @author Max Milbers
	 * @author Valérie Isaksen
	 *
	 * @param boolean Defaults to false, the param is for the userModel->store function, which needs it to determin how to handle the data.
	 * @return String it gives back the messages.
	 */
	private function saveData(&$cart=false,$register=false, $disable_duplicit=false, &$data) {

	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	
	$mainframe = JFactory::getApplication();
		$currentUser = JFactory::getUser();
		
		
		$msg = '';
		
		
		if (empty($data['shipto_address_type_name'])) $data['shipto_address_type_name'] = OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL');

		if (empty($data['address_type'])) $data['address_type'] = 'BT'; 
		$at = JRequest::getWord('addrtype');
		if (!empty($at))
		$data['address_type'] = $at; 

		
		$r = JRequest::getVar('register_account', ''); 
		if (!empty($r) || (VmConfig::get('oncheckout_only_registered', 0)))
		$register = true; 
		
		
		
		//if ($data['address_type'] == 'ST') $register = false; 

		$this->addModelPath( JPATH_VM_ADMINISTRATOR.DS.'models' );
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$userModel = OPCmini::getModel('user');
		if($currentUser->id!=0 || $register){
			$data['user_is_vendor'] = 0; 

			//It should always be stored, stAn: it will, but not here
			if($currentUser->id==0 || (empty($data['ship_to_info_id']))){
			if (!empty($data['email']))
		if (empty($data['shipto_email'])) $data['shipto_email'] = $data['email']; 
	
	
	
		// check for duplicit registration feature
		if (($allow_duplicit) && (empty($disable_duplicit)))
		{
		
		  // set the username if appropriate
		  if (empty($data['username']))
			{
			  if (!empty($currentUser->id))
			  {
			  $data['username'] = $username = $currentUser->username; 
			  JRequest::setVar('username', $username); 
			  $data['email'] = $email = $this->getEmail(); 
			  JRequest::setVar('email', $email); 
			 
			  }
			  else
			  {
			  $username = $data['email']; 
			  $email = $data['email']; 
			  }
			  
			  
			}
			else 
			{
			$username = $data['username'];
			if (!empty($data['email'])) $email = $data['email']; 
			else 
			 {
			   // support for 3rd party exts
			   if (strpos($username, '@')!==false)
			    $email = $username; 
			 }
			}
			$db = JFactory::getDBO(); 
			
			
			
			$q = "select * from #__users where email LIKE '".$this->getEscaped($db, $email)."' limit 0,1"; //or username = '".$db->escape($username)."' ";

			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			$is_dup = false; 
			
			
			if (!empty($res))
			 {
			 
			 
			   //ok, the customer already used the same email address
			   $is_dup = true; 
			   $duid = $res['id']; 
			   $GLOBALS['is_dup'] = $duid; 
			 
				$GLOBALS['opc_new_user'] = $duid; 
			   
			   $data['address_type'] = 'BT';
			   $data['virtuemart_user_id'] = $duid; 
			   $data['shipto_virtuemart_user_id'] = $duid; 
			   
			   $this->saveToCart($data, $cart);
			   // we will not save the user into the jos_virtuermart_userinfos
			   if ($currentUser->id!=0)
			   {
			     // ok, we have a joomla registration + logged in users
				 // but the user might not be registered with virtuemart
				 if ($currentUser->id == $duid)
				  {
				  
				    // yes we are talking about the same user
					// let's associate his data in the cart with his data in VM tables
					$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = ".$duid." "; 
					
					$db->setQuery($q); 
					$res = $db->loadAssocList(); 
					if (empty($res))
					 {
					   foreach ($res as $row)
					   {
					   // ok, he has no BT address assigned
					   if ($row['address_type'] == 'BT')
					   $forceregister = true; 
					   if ($row['address_type'] == 'ST')
					   {
					   $forceregisterst = true; 
					   }
					   
					   }
					 }
					 else
					 {
					  // he is already logged in and all we have to do is to store his data in the order details, not the userinfos
					  return true;
					 }
				  }
			   }
			   else
			   return true; 
			   
			   // ok, we've got a duplict registration here
			   if (empty($currentUser->id))
			   if (!empty($data['password']) && (!empty($data['username'])))
			    {
				   
				   
				 // if we showed the password fields, let try to log him in 
				 
				  // we can try to log him in if he entered password
				  $credentials = array('username'  => $username,
							'password' => $data['password']);
								
				// added by stAn, so we don't ge an error
				$ret = false; 
				if (empty($op_never_log_in))
				{
				
				$options = array('silent' => true );
				$mainframe =& JFactory::getApplication(); 
				ob_start();
				
				$ret = $mainframe->login( $credentials, $options );

				// test123
				/*
				if (false)
				{
				ob_start(); 
					$options = array('silent' => true, 'skip_joomdlehooks'=>true );
					$mainframe->logout($user->id, $options); 
					ob_get_clean(); 
					$return = $mainframe->login($credentials, $options);
				}
				*/
				
				}
				// refresh user data: 
				// refresh user data: 
					// refresh user data: 
					/*
				$session = JFactory::getSession(); 
				$user = JFactory::getUser(); 
				$id = (int)$user->id; 
				$user = new JUser($id); 
				$session->clear('user');
				$user = new JUser($id); 	
				*/
				// end of refresh			
				// end of refresh
				// end of refresh
				$xxy = ob_get_clean();
				unset($xxy); 
				if ($ret === false)
				 {
				  // the login was not sucessfull
				  
				 }
				 else
				 {
				 
				  // login was sucessfull
				  $dontproceed = true; 
				 }

				}
				// did he check: shipping address is different?
				if (method_exists($cart, 'prepareAddressDataInCart'))
			   $cart->prepareAddressDataInCart('BT', 1);
	 
				if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
				$corefields = VirtueMartModelUserfields::getCoreFields();
				$fieldtype = 'BTaddress';
				$userFields = $cart->$fieldtype;
				if (method_exists($cart, 'prepareAddressDataInCart'))
				$cart->prepareAddressDataInCart('ST', 1);
				$fieldtype = 'STaddress';
				$userFieldsst = $cart->$fieldtype;
				
				if ((!empty($data['sa'])) && ($data['sa'] == 'adresaina'))
				{
				 // yes, his data are in the shipto_ fields
				 $address = array(); 
				 foreach ($data as $ksa=>$vsa)
				  {
				    if (strpos($ksa, 'shipto_')===0)
					$address[$ksa] = $vsa; 
				  }
				}
				else
				{
				 // load the proper BT address
				 $q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$duid."' and address_type = 'BT' limit 0,1"; 
				 $db->setQuery($q); 
				 $bta = $db->loadAssoc(); 
				 if (!empty($bta))
				 {
				 $address = array(); 
				 // no, his data are in the BT address and therefore we need to copy them and set a proper BT address
				 foreach ($userFieldsst['fields'] as $key=>$uf)   
				  {
				   $uf['name'] = str_replace('shipto_', '', $uf['name']); 
				   // POST['variable'] is prefered form userinfos.variable in db
				   if (empty($bta[$uf['name']])) $bta[$uf['name']] = ''; 
					{
					  if (!isset($data[$uf['name']])) $data[$uf['name']] = ''; 
					  if (empty($data['address_type_name'])) $data['address_type_name'] = OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL');
					  if (empty($data['shipto_address_type_name'])) $data['shipto_address_type_name'] = OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL');
					  if (empty($data['name'])) $data['name'] = $bta[$uf['name']];
					  JRequest::setVar('shipto_'.$uf['name'], $data[$uf['name']], 'post'); 
					  // this will set the new BT address in the cart later on and in the order details as well
					  if (!empty($bta[$uf['name']]))
					  JRequest::setVar($uf['name'], $bta[$uf['name']], 'post'); 
					  $address['shipto_'.$uf['name']] = $data[$uf['name']]; 
					}
					
				  }
				  }
				  }
				  // ok, we've got the ST addres here, let's check if there is anything similar
				  $q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$duid."'"; 
				  $db->setQuery($q); 
				  $res = $db->loadAssocList(); 
				  $ign = array('virtuemart_userinfo_id', 'virtuemart_user_id', 'address_type', 'address_type_name', 'name', 'agreed', '', 'created_on', 'created_by', 'modified_on', 'modified_by', 'locked_on', 'locked_by');  
				  if (function_exists('mb_strtolower'))
				  $cf = 'mb_strtolower'; 
				  else $cf = 'strtolower'; 
				  $e = $db->getErrorMsg(); 
				  
				  if (!empty($res))
				  {
				  // user is already registered, but we need to fill some of the system fields
				  foreach ($res as $k=>$ad)
				   {
				     $match = false; 
				     foreach ($ad as $nn=>$val)
					  {
					    if (!in_array($nn, $ign))
						{
						  
						  
						  if (!isset($address['shipto_'.$nn])) $address['shipto_'.$nn] = ''; 
						  if ($cf($val) != $cf($address['shipto_'.$nn])) { $match = false; break; }
						  else { $match = true; 
						    $lastuid = $ad['virtuemart_userinfo_id']; 
							$lasttype = $ad['address_type']; 
						  }
						}
					  }
					  if (!empty($match))
					   {
					    // we've got a ST address already registered
						if ($lasttype == 'BT')
						 {
						   // let's set STsameAsBT
						   JRequest::setVar('sa', null); 
						   	
						   // we don't have to do anything as the same data will be saved
							
						   
						 }
						 else
						 {
						   
						   JRequest::setVar('shipto_virtuemart_userinfo_id', $lastuid);
						   $new_shipto_virtuemart_userinfo_id = $lastuid;
						   
						 }
						 break; 
					   }
					  
					  
				   }
				   
				   // the user is registered and logged in, but he wants to checkout with a new address. he might still be in the guest mode
				  
				   	if (empty($match) || (!empty($new_shipto_virtuemart_userinfo_id)))
					   {
					 
					     // we need to store it as a new ST address
						 $address['address_type'] = 'ST'; 
						 $address['virtuemart_user_id'] = $duid; 
						 $address['shipto_virtuemart_user_id'] = $duid; 
						 if (empty($new_shipto_virtuemart_userinfo_id))
						 {
						 $address['shipto_virtuemart_userinfo_id'] = 0; 
						 $address['shipto_virtuemart_userinfo_id'] = $this->OPCstoreAddress($cart, $address, $duid); 
						 
						 
						 // let's set ST address here
						 }
						 else 
						 $address['shipto_virtuemart_userinfo_id'] = $new_shipto_virtuemart_userinfo_id;


						 
						 if (!isset($address['agreed']))
						  {
						    $address['agreed'] = JRequest::getBool('agreed', 1); 
						  }
						// empty radios fix start
						//Notice: Undefined index:  name in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						//Notice: Undefined index:  agreed in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						//Notice: Undefined index:  myradio in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						//Notice: Undefined index:  testcheckbox in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						
						
						require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
						$userFieldsModel = OPCmini::getModel('userfields');
						$prefix = '';

						$prepareUserFieldsBT = $userFieldsModel->getUserFieldsFor('cart','BT');
						$prepareUserFieldsBT = $userFieldsModel->getUserFieldsFor('cart','ST');
						
						if (!empty($prepareUserFieldsBT))
						 foreach ($prepareUserFieldsBT as $fldb) {
						    $name = $fldb->name;
							
							
							if (!isset($btdata[$name]))
							{
							 $btdata[$name] = '';
							}

						  }
						  if (!empty($prepareUserFieldsST))
						  foreach ($prepareUserFieldsST as $flda)
						   {
						     $name = $flda->name;
						     // we need to add empty values for checkboxes and radios
							if (!isset($address['shipto_'.$name]))
							{
							 $address['shipto_'.$name] = '';
							}
						   }
						// empty radios fix end
						
						 $cart->saveAddressInCart($address, 'ST');
						 $btdata = JRequest::get('post'); 
						 $btdata['virtuemart_user_id'] = $duid;
						 $btdata['address_type'] = 'BT'; 
						 
						 if (!isset($btdata['agreed']))
						  {
						    $btdata['agreed'] = JRequest::getBool('agreed', 1); 
						  }
						 
						
						 $cart->saveAddressInCart($btdata, 'BT');
						 
						 return;
					   }

				  
				 }
				 
				

				
				
			 }
			
			
		}
		
		
		
	
		
		if (empty($dontproceed))
		{
		   
		    if (empty($currentUser->id))
			{
		   
			if (empty($data['username']))
			{
			  $data['username'] = $data['email']; 
			}
			if (empty($data['password']) && (!VmConfig::get('oncheckout_show_register', 0)))
			{
			
			$data['password'] = $data['password2'] = uniqid(); 			
			}
			}
			
			if (!empty($data['first_name']))
			$data['name'] = $data['first_name'].' '.$data['last_name']; 
			else
			if (!empty($data['last_name']))
			$data['name'] = $data['last_name']; 
			else $data['name'] = '   '; 


			if (!empty($data['shipto_first_name']) && (!empty($data['shipto_last_name'])))
			$data['shipto_name'] = $data['shipto_first_name'].' '.$data['shipto_last_name']; 
			else
			if (!empty($data['shipto_last_name']))
			$data['shipto_name'] = $data['shipto_last_name']; 
			else $data['shipto_name'] = '   '; 
			
			
			if (empty($_POST['name']))
			 {
			   $_POST['name'] = $data['name']; 
			 }
			 // Bind the post data to the JUser object and the VM tables, then saves it, also sends the registration email
			if (empty($unlog_all_shoppers))
			if (empty($currentUser->id))
            $data['guest'] = 0; 
			
			
			
			$usersConfig = JComponentHelper::getParams( 'com_users' );
			
			// OPC can still register, but will unlog the shopper immidiately when no login is enabled
			if ($usersConfig->get('allowUserRegistration') != '0')
			{
			
			 $ret = $this->userStore($data, $userModel); 
			 
		     // here virtuemart logs in the user, so we need to unlog him
			 /*
			 since opc 2.0.231 VM cannot login the customer, therefore we do not need to unlog him
			 if (!empty($op_never_log_in))
			   {
			   
			     $mainframe =& JFactory::getApplication(); 
				  $options = array('silent' => true, 'skip_joomdlehooks'=>true );
				 $user =& JFactory::getUser();
				 $mainframe->logout($user->id, $options); 
				 $unlog_all_shoppers = true; 
			   }
			   */
			   
			   
			   
			 
			}
			else
			{
			
			  $ret['success'] = true; 
			  $user = JFactory::getUser();
			  $unlog_all_shoppers = true; 
			}
			$data['address_type'] = 'ST'; 
			
			// this gives error on shipping address save
			// this section is used purely for unlogged customers
			if ((!empty($data['sa'])) && ($data['sa'] == 'adresaina'))
			{
				
			  $this->userStoreAddress($userModel, $data); 
			}
			
			
			
			$user = $ret['user']; 
			$ok = $ret['success']; 
			
			$user = JFactory::getUser(); 
			// we will not send this again
			if (empty($unlog_all_shoppers))
			if($user->id==0){
				$msg = (is_array($ret)) ? $ret['message'] : $ret;
				$usersConfig = JComponentHelper::getParams( 'com_users' );
				$useractivation = $usersConfig->get( 'useractivation' );
				
				
				
				if (empty($op_never_log_in))
				if (is_array($ret) && $ret['success'] && ((empty($useractivation)) || (!empty($opc_no_activation)))) {
				
					// Username and password must be passed in an array
					$credentials = array('username' => $ret['user']->username,
			  					'password' => $ret['user']->password_clear
					);
					$options = array('silent' => true );
					
					$return = $mainframe->login($credentials, $options);
					
				//test123
				/*
				   if (false)				
					{
					// this part of code fixes the _levels caching issue on joomla 1.7 to 2.5
					ob_start(); 
					$options = array('silent' => true, 'skip_joomdlehooks'=>true );
					$mainframe->logout($user->id, $options); 
					ob_get_clean(); 
					$return = $mainframe->login($credentials, $options);
					}
					*/
					
				// refresh user data: 
				// refresh user data: 
				/*
				$session = JFactory::getSession(); 
				$user = JFactory::getUser(); 
				$id = (int)$user->id; 
				$user = new JUser($id); 
				$session->clear('user');
				$user = new JUser($id); 	
				*/
				// end of refresh				// end of refresh
			
					
				}
			}
			}
		 
		  }
		  else
		  {
		
		   // the user is logged in and we want to update his address
			$data['address_type'] = 'ST'; 
			// this gives error on shipping address save
			
			$new_shipto = JRequest::getVar('opc_st_changed_new', false); 
			if (((!empty($data['sa'])) && ($data['sa'] == 'adresaina')) || ($new_shipto))
			{
			     $data['address_type'] = 'ST'; 
				 //$data['shipto_virtuemart_userinfo_id'] = null; 
				 if ((empty($data['email'])) && (!empty($currentUser->email)))
				 {
				 $data['email'] = $currentUser->email;
				 $data['shipto_email'] = $currentUser->email;
				 }
				 if ($data['shipto_address_type_name'] == OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'))
				 {
						$data['shipto_address_type_name'].= '('.@$data['first_name'].' '.@$data['last_name'].' '.@$data['address_1'].' '.@$data['city'].')'; 
				 }
				 
				 if (empty($data['name']))
				  {
				    $data['name'] = ''; 
				    if (!empty($data['first_name']))
					$data['name'] .= $data['first_name'];
					if (!empty($data['last_name']))
					$data['name'] .= $data['last_name']; 
				  }
				  if (empty($data['user_id']))
				  {
				  $data['user_id'] = $currentUser->id; 
				  $data['virtuemart_user_id'] = $currentUser->id; 
				  }
				  if (empty($data['username']) && (!empty($currentUser->username)))
				   $data['username'] = $currentUser->username; 
			    
				// to create a new one: 
				$data['shipto_virtuemart_userinfo_id'] = 0; 
			    $this->OPCstoreAddress($cart, $data);
				
				
			}
			
			$bt = JRequest::getVar('ship_to_info_id_bt', ''); 
			if (!empty($bt))
			{
		      $changed = JRequest::getVar('opc_st_changed_'.$bt, ''); 
			  if (!empty($changed))
			   {
			     $data['address_type'] = 'BT'; 
				 $data['shipto_virtuemart_userinfo_id'] = null; 
				 if ((empty($data['email'])) && (!empty($currentUser->email)))
				 $data['email'] = $currentUser->email;
				 
				 if (empty($data['name']))
				  {
				    $data['name'] = ''; 
				    if (!empty($data['first_name']))
					$data['name'] .= $data['first_name'];
					if (!empty($data['last_name']))
					$data['name'] .= $data['last_name']; 
				  }
				  if (empty($data['user_id']))
				  {
				  $data['user_id'] = $currentUser->id; 
				  $data['virtuemart_user_id'] = $currentUser->id; 
				  }
				  if (empty($data['username']) && (!empty($currentUser->username)))
				   $data['username'] = $currentUser->username; 
			   $this->userStoreAddress($userModel, $data); 
			     //$userModel->storeAddress($data);
				 
				 
			   }
			}
			
			
		  }

		}
		
		
		$data['address_type'] = 'BT'; 
		
		$this->saveToCart($data, $cart);
		
		
		
		
		return $msg;
	}
	private function login(&$data)
	 {
	    include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 	
		
	 }
	 
	private function getModifiedData(&$data, $data_orig=null)
	{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 	
		if (!empty($data_orig))
		{		
		$data = $data_orig; 
		return;
		}
		if (empty($data['name']) && (!empty($data['fist_name'])) && (!empty($data['last_name'])))
		 {
		   $data['name'] = $data['fist_name'].' '.$data['last_name'];
		 }
		 if ($data['address_type'] == 'BT')
		 {
		   $orig = @$data['shipto_address_type_name']; 
		   unset($data['shipto_address_type_name']); 
		 }
		 
		 
		 // we have registration fields only
		 if (empty($bt_fields_from))
		 if ($data['address_type'] == 'BT')
		 {
		    $onlyf = array(); 
	  
	  
	    $q = 'select * from #__virtuemart_userfields where `published` = 1 and `required` = 1 and `registration` = 0'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$onlyf2 = $db->loadAssocList(); 
		foreach ($onlyf2 as $k=>$v)
		 {
		  $key = $v['name']; 
		  if (empty($data[$key])) $data[$key] = '_'; 
		 }
	  
		 }
		
		if (empty($data_orig))
		if (!empty($custom_rendering_fields))
		{
			if ($opc_cr_type != 'save_all')
			{
				foreach ($custom_rendering_fields as $fname)
				{
				   if ($fname == 'name') continue; 
					if (isset($data[$fname])) $data[$fname] = ''; 
					if (isset($data['shipto_'.$fname])) $data[$fname] = ''; 
					
				}
			}
			else return;
		}
		else return; 
		
	}
	
	public function userStoreAddress(&$userModel, &$data)
	{
	   
	    $data_orig = $data; 
		$this->getModifiedData($data); 
		
		
		
		if (!isset($data['virtuemart_userinfo_id']))
		if (!empty($data['bt_virtuemart_userinfo_id']))
		 {
		    $data['virtuemart_userinfo_id'] = (int)$data['bt_virtuemart_userinfo_id']; 
			
		 }
		 
		if (!isset($data['address_type']))
		{
		  
		}

		 
		$ret =	$userModel->storeAddress($data);
		
		$this->getModifiedData($data, $data_orig); 
		return $ret; 
    }		
	private function userStore(&$data, &$userModel)
	{
	
	   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'user.php'); 
	  
	  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
	
	  $data_orig = $data; 
	  $this->getModifiedData($data); 
	  			
	  
	  if (isset($data['shipto_address_type_name']))
	  {
	  $stored = $data['shipto_address_type_name']; 
	  unset($data['shipto_address_type_name']); 
	  }

	  $ret = OPCUser::storeVM25($data, false, $userModel, $opc_no_activation, $this); 	
	  
	  if (isset($stored))
	  $data['shipto_address_type_name'] = $stored; 
	  
	  $this->storeShopperGroup($data); 
	  $this->getModifiedData($data, $data_orig); 
	  return $ret; 
	}	

	private function storeShopperGroup(&$data, $update=false)
    {
	
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		   $shoppergroupmodel = OPCmini::getModel('ShopperGroup');
		   $userModel = OPCmini::getModel('user');
		   
		   $default = $shoppergroupmodel->getDefault(0); 
		   if (!empty($default))
		   $default_id = $default->virtuemart_shoppergroup_id; 
		   else
		   $default_id = 1; 
		   
		   $default2 = $shoppergroupmodel->getDefault(1); 
		   if (!empty($default2))
		   $default2_id = $default2->virtuemart_shoppergroup_id; 
		   else
		   $default2_id = 2; 
		   
		   
		   $user = JFactory::getUser(); 
		   $user_id = $user->get('id'); 
		   
		   $db = JFactory::getDBO(); 
		   
		   
		   if (empty($user_id)) 
		   {
		  
		   return;
		   }
		   
		   if (!empty($update))
		   {
		   $q = 'select * from #__virtuemart_vmuser_shoppergroups where virtuemart_user_id = '.(int)$user_id.' limit 0,1'; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   
		   // the shopper group was already set
		   if (!empty($res)) 
		   {
		   
		   
		   return; 
		   }
		   }
		   
		   
		   if (empty($data['virtuemart_shoppergroup_id']) ||  ($data['virtuemart_shoppergroup_id']==$default)){
				
				return; 
			}
			
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
			$usermodel = OPCmini::getModel ('user');
			//$user = $usermodel->getUser ();
			//$user->shopper_groups = (array)$user->shopper_groups;
			
			// Bind the form fields to the table

			$data['virtuemart_shoppergroup_id'] = (int)$data['virtuemart_shoppergroup_id']; 
			//if(!empty($data['virtuemart_shoppergroup_id']))
			
			$db = JFactory::getDBO(); 
			if (empty(self::$shopper_groups)) self::$shopper_groups = array(); 
			if (method_exists($userModel, 'getCurrentUser'))
			{
				$user = $userModel->getCurrentUser();
				self::$shopper_groups = $user->shopper_groups; 
			}
			if (empty(self::$shopper_groups)) self::$shopper_groups = array(); 
			if (!in_array($data['virtuemart_shoppergroup_id'], self::$shopper_groups))
			self::$shopper_groups[] = $data['virtuemart_shoppergroup_id']; 
			
			foreach (self::$shopper_groups as $key=>$group)
			{
			  if (empty($group)) continue; 
			  // anonymous
			  if ($group == $default_id) continue; 
			  // default
			  if ($group == $default2_id) continue; 
			  if (empty($group)) continue; 
				//$user = $userModel->getUser(); 
			  $group = (int)$group; 
				
				$q = "insert into `#__virtuemart_vmuser_shoppergroups` (id, virtuemart_user_id, virtuemart_shoppergroup_id) values (NULL, ".(int)$user_id.", ".(int)$group.")"; 
				$db->setQuery($q); 
				$db->query(); 
				$e = $db->getError(); 
				
				// do not give an error: if (!empty($e)) { echo $e; }
				
				/*
				$shoppergroupData = array('virtuemart_user_id'=>$user_id,'virtuemart_shoppergroup_id'=>$group);
				$user_shoppergroups_table = $userModel->getTable('vmuser_shoppergroups');
				$shoppergroupData = $user_shoppergroups_table -> bindChecknStore($shoppergroupData);
				$errors = $user_shoppergroups_table->getErrors();
				
				
				foreach($errors as $error){
					$this->setError($error);
					vmError('Set shoppergroup '.$error);
					echo $error;  
					$noError = false;
				}
				*/
			}
			
			
	}
	
	// this is an overrided function to support duplict emails
	// the orginal function was in: user.php storeAddress($data)
	function OPCstoreAddress(&$cart, $data, $user_id=0)
	{
	
	   $x = JRequest::getVar('ship_to_info_id', ''); 
	   if (!empty($x) && (is_numeric($x)))
	   {
	     $data['shipto_virtuemart_userinfo_id'] = (int)$x; 
	   }
	   
	   $data['shipto_virtuemart_userinfo_id'] = (int)$data['shipto_virtuemart_userinfo_id']; 
	   
		  //$user =JFactory::getUser();
		  $this->addModelPath( JPATH_VM_ADMINISTRATOR.DS.'models' );
		  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		  $userModel = OPCmini::getModel('user');
		  $user = JFactory::getUser();
	      $userinfo   = $userModel->getTable('userinfos');
		  if($data['address_type'] == 'BT'){
			$userfielddata = VirtueMartModelUser::_prepareUserFields($data, 'BT');

			if (!$userinfo->bindChecknStore($userfielddata)) {
				vmError('storeAddress '.$userinfo->getError());
			}
		}
		// Check for fields with the the 'shipto_' prefix; that means a (new) shipto address.
		$shiptonew = JRequest::getVar('opc_st_changed_new', false); 
		$shiptologged = JRequest::getVar('shipto_logged'); 
		// shipto_logged
		
		
		
		
		
		// special case when using sinel ST address: 
		$skipc = false; 
		$shipto_logged = JRequest::getVar('shipto_logged', null); 
		$shipto_logged = (int)$shipto_logged; 
		if (!empty($shipto_logged))
		 {
		    $q = 'select address_type from #__virtuemart_userinfos where virtuemart_userinfo_id = '.$data['shipto_virtuemart_userinfo_id'].' limit 0,1'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$res2 = $db->loadResult(); 
			if ((!empty($res2)) && ($res2 != 'bt'))
			  {
			     $data['shipto_virtuemart_userinfo_id'] = $shipto_logged; 
				 $skipc = true; 
			  }
		 }
		else
		if (!$skipc)
		{
		
		$q = 'select address_type from #__virtuemart_userinfos where virtuemart_userinfo_id = '.$data['shipto_virtuemart_userinfo_id'].' limit 0,1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadResult(); 
		if (empty($res))
		 {
		  // non existent update to ST 
		   unset($data['shipto_virtuemart_userinfo_id']); 
		   $data['shipto_virtuemart_userinfo_id'] = 0; 
		   
		 }
		 if (strtolower($res) == 'bt')
		 {
		   // trying to update ST with improper ID
		   $data['shipto_virtuemart_userinfo_id'] = 0; 
			
		 }
		 }
		
		if(isset($data['shipto_virtuemart_userinfo_id'])){
			$dataST = array();
			$_pattern = '/^shipto_/';

			foreach ($data as $_k => $_v) {
				if (preg_match($_pattern, $_k)) {
					$_new = preg_replace($_pattern, '', $_k);
					$dataST[$_new] = $_v;
				}
			}

			$userinfo   = $userModel->getTable('userinfos');
			if(isset($dataST['virtuemart_userinfo_id']) and $dataST['virtuemart_userinfo_id']!=0){
			
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			if (!OPCmini::isSuperVendor())
			 {
			   $userinfo->load($dataST['virtuemart_userinfo_id']);
			 }
			
			
			
			}

			if(empty($userinfo->virtuemart_user_id)){
			
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			if (!OPCmini::isSuperVendor())
			 {
			    $dataST['virtuemart_user_id'] = $user->id;
			 }
			 else
			 {
			   if(isset($data['virtuemart_user_id'])){
						$dataST['virtuemart_user_id'] = (int)$data['virtuemart_user_id'];
					} else {
						//Disadvantage is that admins should not change the ST address in the FE (what should never happen anyway.)
						$dataST['virtuemart_user_id'] = $user->id;
					}
			 }
			
			
			
			}
			else
			{
				
				if($userinfo->virtuemart_user_id!=$user->id) 
				{
				
				return;
				}
			}

			$dataST['address_type'] = 'ST';
			
			
			$userfielddata = VirtueMartModelUser::_prepareUserFields($dataST, 'ST');
			

			if (!$userinfo->bindChecknStore($userfielddata)) {
				vmError($userinfo->getError());
			}
			
			if (!empty($userinfo->virtuemart_userinfo_id))
			{
				$shipto = JRequest::setVar('shipto', (int)$userinfo->virtuemart_userinfo_id); 
				$cart->selected_shipto = $userinfo->virtuemart_userinfo_id; 
				$cart->STsameAsBT = 0; 
			}
			
			 
		}

		
		return $userinfo->virtuemart_userinfo_id;
		
	}
	function sendRegistrationMail($user)
	{
	
	  // Compile the notification mail values.
		$data = $user->getProperties();
		$config	= JFactory::getConfig();
		if (method_exists($config, 'get'))
		{
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		
		}
		else
		{
		 $data['fromname']	= $config->getValue('config.fromname');
		 $data['mailfrom']	= $config->getValue('config.mailfrom');
		 $data['sitename']	= $config->getValue('config.sitename');
		}
		$data['siteurl']	= JUri::base();
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$useractivation = $usersConfig->get( 'useractivation' );
		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= OPCLang::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = OPCLang::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= OPCLang::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = OPCLang::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		} else {

			$emailSubject	= OPCLang::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = OPCLang::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl']
			);
		}

		// Send the registration email.
		$return = JUtility::sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

	}
	
		/**
	 * This function just gets the post data and put the data if there is any to the cart
	 *
	 * @author Max Milbers
	 *
	 * this is from user model 
	 */
	function saveToCart($data, $cart=null){

		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$userFieldsModel = OPCmini::getModel('userfields');
		

		$prepareUserFields = $userFieldsModel->getUserFieldsFor('cart',$data['address_type']);
		
						if (!empty($prepareUserFields))
						 foreach ($prepareUserFields as $fld) {
						    $name = $fld->name;
							
							if ($data['address_type'] == 'BT')
							if (isset($cart) && (!empty($cart->BT[$name])))
							{
							  $data[$name] = $cart->BT[$name];
							}

							if ($data['address_type'] == 'ST')
							if (isset($cart) && (!empty($cart->ST[$name])))
							{
							  $data[$name] = $cart->ST[$name];
							}
							
							// we need to add empty values for checkboxes and radios
							if ($data['address_type'] == 'ST')
							if (!isset($data['shipto_'.$name]))
							{
							 $data['shipto_'.$name] = '';
							}
							
							
							
							if (isset($cart) && (empty($cart->BT[$name])))
							if ($data['address_type'] == 'BT')
							if (!isset($data[$name]))
							{
							 $data[$name] = '';
							}
							
						
							
							
							
							

						  }
		
		
	    
						  
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
		
		if (!isset($cart))
		$cart = VirtueMartCart::getCart();
		
		if (method_exists($cart, 'prepareCartData'))
	   {
	     $cart->prepareCartData(false); 
	   }
		
		$type= $data['address_type']; 
		$oldcart = clone($cart); 
		$cart->saveAddressInCart($data, $type );
		if (isset($oldcart->$type))
		foreach ($oldcart->$type as $key=>$val)
		 {
		   if (empty($cart->{$type}[$key]))
		   $cart->{$type}[$key] = $val; 
		   
		   
		 }
		
		
		$sa = JRequest::getVar('sa', ''); 
		if ($sa == 'adresaina')
		{
		$cart->saveAddressInCart($data, 'ST');
		 if (isset($oldcart->ST))
		 foreach ($oldcart->ST as $key=>$val)
		 {
		   if (empty($cart->ST[$key]))
		   $cart->ST[$key] = $val; 
		 }

		}
		else $cart->STsameAsBT = 1; 
		$cart->setCartIntoSession();
		
		
		
	}

  function getKlarnaAddress()
  {
    
if (JVM_VERSION >= 2) {
	
	require_once (JPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'klarna' . DS . 'klarna.php'); 
	require_once (JPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'klarna' . DS . 'klarna'. DS.'api'.DS.'klarnaaddr.php'); 
} else {
	
	require_once (JPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'klarna.php'); 
	require_once (JPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'klarna' . DS.'api'.DS.'klarnaaddr.php'); 
}

	$klarna = new Klarna(); 
	
	$q = "select * from #__virtuemart_paymentmethods where payment_element = 'klarna' and published = '1' limit 0,1"; 
	$db = JFactory::getDBO(); 
	$db->setQuery($q); 
	$res = $db->loadAssoc(); 
	if (empty($res)) return null; 
	$id = $res['virtuemart_paymentmethod_id']; 
	jimport( 'joomla.html.parameter' );
	
	$params = explode('|', $res['payment_params']);
	$obj = new stdclass(); 
	foreach($params as $item){

				$item = explode('=',$item);
				$key = $item[0];
				unset($item[0]);
				$item = implode('=',$item);
				if(!empty($item))
				{
					$obj->$key = @json_decode($item);

				}
			}
	
	$cData = KlarnaHandler::countryData ($obj, 'SWE');
	$language = KlarnaLanguage::fromCode('SE');
	$currency = KlarnaCurrency::fromCode($cData['currency_code']);
	$klarna->config ($cData['eid'], $cData['secret'], $cData['country_code'], $language, $currency, $cData['mode']);
	/*
	$country = JRequest::getVar('virtuemart_country_id', ''); 
	
	if (!empty($country) && (is_numeric($country)))
	 {
	   $q = 'select * from #__virtuemart_countries where virtuemart_country_id = '.$country.' limit 0,1'; 
	   $db->setQuery($q); 
	   $r = $db->loadAssoc(); 
	   $e = $db->getErrorMsg(); 
	   
	   if (empty($r)) $c = 'se';
	   else
	   $c = strtolower($r['country_2_code']); 
	   
	 }
	 else 
	 */
	 $c = 'se'; 
	 
	$klarna->setCountry($c);  
	
	 
    $klarna->setLanguage($language);
    $klarna->setCurrency($currency);
	
	
	//try 
	{  
    //Attempt to get the address(es) associated with the SSN/PNO.  
	$pn = JRequest::getVar('socialNumber', ''); 
	
    $addrs = $klarna->getAddresses($pn);
	
	
	if (empty($addrs)) return null; 
	
	$a = array(); 	
	 foreach ($addrs as $key => $addr)   
	 {
	  
	 $a = $addr->toArray(); 
	 foreach ($a as $k=>$v)
	  $a[$k] = utf8_encode($v);
	 return $a; 
	 
	//if (empty($ar)) return null; 
	if ($addr->isCompany)
	$a['company_name'] = $addr->getCompanyName();
	else $a['company_name'] = ''; 
	
	$a['first_name'] = $addr->getFirstName();
	
	$a['last_name'] = $addr->getLastName();
	$a['address_1'] = $addr->getStreet();
	$a['email'] = $addr->getEmail(); 
	$a['phone_1'] = $addr->getTelno(); 
	$a['phone_2'] = $addr->getCellno(); 
	$a['address_2'] = $addr->getHouseExt(); 
	$a['zip'] = $addr->getZipCode(); 
	$a['city'] = $addr->getCity();
	
	
	
    return $a; 
	}
	
	return null;
    /* If there exists several addresses you would want to output a list in 
       which the customer could choose the address which suits him/her. 
     */  
  
    // Print them if available:  
    foreach ($addrs as $key => $addr) {  
        echo "<table>\n";  
  
        // This only works if the right getAddresses type is used.  
        if ($addr->isCompany) {  
            echo "\t<tr><td>Company</td><td> {$addr->getCompanyName()} </td></tr>\n";  
        } else {  
            echo "\t<tr><td>First name</td><td>{$addr->getFirstName()}</td></tr>\n";  
            echo "\t<tr><td>Last name</td><td>{$addr->getLastName()}</td></tr>\n";  
        }  
  
        echo "\t<tr><td>Street</td><td>{$addr->getStreet()}</td></tr>\n";  
        echo "\t<tr><td>Zip code</td><td>{$addr->getZipCode()}</td></tr>\n";  
        echo "\t<tr><td>City</td><td>{$addr->getCity()}</td></tr>\n";  
        echo "\t<tr><td>Country</td><td>{$addr->getCountryCode()}</td></tr>\n";  
        echo "</table>\n";  
    }  
	} 
	//catch(Exception $e) 
	{  
    //Something went wrong  
	
	return null;
    echo "{$e->getMessage()} (#{$e->getCode()})\n";  
	return null; 
	}  
	return null;

  }
    
}


// http://php.net/manual/en/function.var-dump.php
/**
 * Better GI than print_r or var_dump -- but, unlike var _ dump, you can only dump one variable.  
 * Added htmlentities on the var content before echo, so you see what is really there, and not the mark-up.
 * 
 * Also, now the output is encased within a div block that sets the background color, font style, and left-justifies it
 * so it is not at the mercy of ambient styles.
 *
 * Inspired from:     PHP.net Contributions
 * Stolen from:       [highstrike at gmail dot com]
 * Modified by:       stlawson *AT* JoyfulEarthTech *DOT* com 
 *
 * @param mixed $var  -- variable to dump
 * @param string $var_name  -- name of variable (optional) -- displayed in printout making it easier to sort out what variable is what in a complex output
 * @param string $indent -- used by internal recursive call (no known external value)
 * @param unknown_type $reference -- used by internal recursive call (no known external value)
 */
function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
{

    $do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';
    
    // So this is always visible and always left justified and readable
    echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

    if (is_array($var) && isset($var[$keyvar]))
    {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else
    {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";

        if(is_array($avar))
        {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar))
        {
            echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color\"".htmlentities($avar)."\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> ".htmlentities($avar)."<br>";

        $var = $var[$keyvar];
    }
    
    echo "</div>";
}	