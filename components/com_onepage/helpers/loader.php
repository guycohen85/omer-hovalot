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

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 



/*
if (!class_exists('VirtueMartViewCart'))
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'virtuemart.cart.view.html.php'); 
*/






//extends VirtueMartViewCart
class OPCloader extends OPCView {
 public static $totals_html; 
 public static $extrahtml; 
 public static $debugMsg; 
 public static $inform_html; 
 public static $fields_names; 
 static $totalIsZero; 
 static $modelCache; 
 static $methods; 
 function getName()
 {
   return 'OPC'; 
 }
 function getPluginElement($type, $vmid, $extra=false)
  {
    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'pluginhelper.php'); 
	return OPCPluginHelper::getPluginElement($type, $vmid, $extra); 
  
  
  }
 function getPluginData(&$cart)
 {
    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'pluginhelper.php'); 
	return OPCPluginHelper::getPluginData($cart);
	
 }
 
 function getMainJs()
 {
   
 }
 function opcDebug($msg)
 {
   if (empty(OPCloader::$debugMsg)) OPCloader::$debugMsg = array(); 
   if (!is_string($msg))
   $msg = var_export($msg, true); 
   OPCloader::$debugMsg[] = $msg; 
 }
 
 
 function loadJavascriptFiles(&$ref)
 {
 
  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'javascript.php'); 
   return OPCJavascript::loadJavascriptFiles($ref, $this);
 
 }
 
 function getShippingEnabled($cart=null)
 {
 
   if (defined('DISABLE_SHIPPING')) return DISABLE_SHIPPING; 
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   
   if (empty($op_zero_weight_override)) 
   {
    define('DISABLE_SHIPPING', $op_disable_shipping);
    return $op_disable_shipping;
   }
   
   if (!empty($op_disable_shipping))
   {
   define('DISABLE_SHIPPING',1); 
   return true; 
   }
   
   if (empty($cart)) 
     $cart = VirtueMartCart::getCart();
   //else $cart=$ref->cart; 
   $weight = 0; 
   foreach( $cart->products as $pkey =>$prow )
    {
	  if (isset($prow->product_weight))
	  if (!empty($prow->product_weight))
	  {
	  $w = (float)$prow->product_weight;  
	  if ( $w > 0)
	  {
	    
	    //echo $prow->product_weight.'<br />'; 
	    $weight = 1; 
		continue;
	  }
	  }
	}
	if ($weight > 0)
	{
	  
	  define('DISABLE_SHIPPING',0); 
	  return false; 
	}
	
	define('DISABLE_SHIPPING',1); 
	return true; 
   
	
 }
  function getShiptoEnabled($cart=null)
 {
 
   if (defined('NO_SHIPTO')) return NO_SHIPTO; 
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   
   if (!empty($op_disable_shipto))
   {
   define('NO_SHIPTO', true); 
   return true; 
   }
   
   if (empty($disable_ship_to_on_zero_weight)) 
   {
   define('NO_SHIPTO', false); 
   return false;
   }
   
   // will check the weitht only if ship to is enabled + shop to per weithet is enabled
   
   if (empty($ref)) 
     $cart = VirtueMartCart::getCart();
   else $cart=$ref->cart; 
   $weight = 0; 
   foreach( $cart->products as $pkey =>$prow )
    {
	  if (isset($prow->product_weight))
	  if (!empty($prow->product_weight))
	  {
	  $w = (float)$prow->product_weight;  
	  if ( $w > 0)
	  {
	    
	    //echo $prow->product_weight.'<br />'; 
	    $weight = 1; 
		continue;
	  }
	  }
	}
	
	if ($weight > 0)
	{
	  define('NO_SHIPTO', false); 
	  return false; 
	}
	
	
	define('NO_SHIPTO', true); 
	return true; 
   
	
 }
 
 /* deprecated */
 function getShiptoEnabled2($cart=null)
 {
 
   $disable_shipping = OPCloader::getShippingEnabled($cart); 
   
   if (defined('NO_SHIPTO')) return NO_SHIPTO; 
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   
   // disabled by master config
   if (!empty($op_disable_shipto))
   if (!defined('NO_SHIPTO'))
   {
		define('NO_SHIPTO', 1);
		return true; 
   }
   // shipping is disabled by weight and config says to disable ship to as well
   if (!empty($op_disable_shipto) && (!empty($disable_ship_to_on_zero_weight)))
    {
		define('NO_SHIPTO', 1);
		return true; 
	}
   	
	define('NO_SHIPTO', 0);
	return false; 
   
	
 }
 
 
 // returns the domain url ending with slash
 function getUrl($rel = false)
 {
   $url = JURI::root(); 
   if ($rel) $url = JURI::root(true);
   if (empty($url)) return '/';    
   if (substr($url, strlen($url)-1)!='/')
   $url .= '/'; 
   return $url; 
 }
 
 // returns a modified user object, so the emails can be sent to unlogged users as well
 function getUser(&$cart)
  {
    $currentUser = JFactory::getUser();
	return $currentUser; 
	$uid = $currentUser->get('id');
	if (!empty($uid))
				 {
				   
				 }
				 
  }
 
 function getReturnLink(&$ref)
 {
   $itemid = JRequest::getVar('Itemid', ''); 
   if (!empty($itemid))
   $itemid = '&Itemid='.$itemid; 
   else $itemid = ''; 
   return base64_encode($this->getUrl().'index.php?option=com_virtuemart&view=cart'.$itemid);
   /*
   if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
  
   }
   else
   {
    return base64_encode(JURI::root().'/index.php?option=com_virtuemart&page=cart');
   }
   */
 }
 
 function getShowFullTos(&$ref)
 {
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'tos.php'); 
  return OPCTos::getShowFullTos($ref, $this); 

 }
 
  function getArticle($id, $repvals=array())
 {
	$article = JTable::getInstance("content");
	   
	   $article->load($id);
	
		
		$parametar = new OPCParameter($article->attribs);
	    $x = $parametar->get('show_title', false); 
		$x2 = $parametar->get('title_show', false); 
		
		$intro = $article->get('introtext'); 
		$full = $article->get("fulltext"); // and/or fulltext
		 JPluginHelper::importPlugin('content'); 
		  $dispatcher = JDispatcher::getInstance(); 
		  $mainframe = JFactory::getApplication(); 
		  $params = $mainframe->getParams('com_content'); 
		  
		 if ($x || $x2)
		 {
		
		

		  $title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
		  
		  }
		  else $title = ''; 
		  if (empty($article->text))
		  $article->text = $title.$intro.$full; 
		  
		  if (!empty($repvals))
	      foreach ($repvals as $key=>$val)
		  {
		     $article->text = str_replace('{'.$key.'}', $val, $article->text); 
		  }
	     
		  $results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
		  $results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
		  
		  return $article->get('text');
		
	
 }
 
 function getTosRequired(&$ref)
 {
 
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'tos.php'); 
   return OPCTos::getTosRequired($ref, $this);
 
 }
 
 public static function checkOPCSecret()
 {
	 	$config     = JFactory::getConfig();
		
		if (method_exists($config, 'getValue'))
		$secret       = $config->getValue('secret');
		else 
		$secret       = $config->get('secret');
		
		$secret = md5('opcsecret'.$secret); 
		$opc_secret = JRequest::getVar('opc_secret', null); 
		if ($opc_secret == $secret)
		{
		$preview = JRequest::getVar('preview', false); 
		if (empty($preview)) return false; 
		
		return true; 
		}
	
	return false; 
 }
 
 function addtocartaslink(&$ref)
 {
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'addtocartaslink.php'); 
   OPCAddToCartAsLink::addtocartaslink($ref, $this); 
	

}
 function getTosLink(&$ref)
 {
    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'tos.php'); 
   return OPCTos::getTosLink($ref, $this); 
 
 } 
 function getFormVars(&$ref)
 {
     require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'commonhtml.php'); 
	return OPCCommonHtml::getFormVars($ref); 
    
  
		
 }
 
 function getCaptcha(&$ref)
 {
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'captcha.php'); 
   return OPCCaptcha::getCaptcha($ref);    
 }
    // input parameters: STaddress or BTaddress fields
	// will change country and state to it's named equivalents
    function setCountryAndState($address)
	{
	  // get rid of the references
	  $address = $this->copyObj($address); 
	  if (!class_exists('ShopFunctions'))
	  require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
	  
	  if ((isset($address) && (!is_object($address))) || ((!is_object($address)) && (empty($address->virtuemart_country_id))))
	  {
	  
	  if (!empty($address['virtuemart_country_id']) && (!empty($address['virtuemart_country_id']['value'])) && (((is_numeric($address['virtuemart_country_id']['value'])))))
	   {
	     $address['virtuemart_country_id']['value_txt'] = shopFunctions::getCountryByID($address['virtuemart_country_id']['value']); 
		 //shopFunctions::getCountryByID($address['virtuemart_country_id']['value']); 
	   }
	  else 
	  {
	  $address['virtuemart_country_id']['value'] = ''; 
	  }
	   
	  if (!empty($address['virtuemart_state_id']) && (!empty($address['virtuemart_state_id']['value'])) && ((is_numeric($address['virtuemart_state_id']['value']))))
	   {
	     $address['virtuemart_state_id']['value_txt'] = shopFunctions::getStateByID($address['virtuemart_state_id']['value']); 
	   }
	  else $address['virtuemart_state_id']['value'] = ''; 
	  }
	  else
	  {
	  if (!empty($address->virtuemart_country_id) && (((is_numeric($address->virtuemart_country_id)))))
	   {
	     $address->virtuemart_country_id = shopFunctions::getCountryByID($address->virtuemart_country_id); 
	   }
	  else $address->virtuemart_country_id = ''; 
	   
	  if (!empty($address->virtuemart_state_id)  && ((is_numeric($address->virtuemart_state_id))))
	   {
	     $address->virtuemart_state_id = shopFunctions::getStateByID($address->virtuemart_state_id); 
	   }
	  else $address->virtuemart_state_id = ''; 
	  
	  }
	  return $address; 
	}
	
	function txtToVal(&$address)
	{
	  foreach ($address as $k=>$v)
	   if (isset($v['value_txt']))
	     $address[$k]['value'] = $v['value_txt']; 
	  
	  
	}
	
	function getNamedFields(&$BTaddress, $fields, $_u)
	 {
	 
	  $db = JFactory::getDBO(); 
	 $sysa = array('virtuemart_state_id', 'virtuemart_country_id'); 
	 foreach ($BTaddress as $k=>$val)
	 {
	   if (!in_array($BTaddress[$k]['name'], $sysa))
	   
				   switch ($BTaddress[$k]['type'])
				   {
				     	case 'multicheckbox':
						case 'multiselect':
						case 'select':
						case 'radio':
						case 'checkbox':
						    $vals = explode('|*|', $fields[$val['name']]); 
							//$BTaddress[$k]['value'] = ''; 
							foreach ($vals as $vv)
							 {
							
							if (!isset($_u[$BTaddress[$k]['name']]->virtuemart_userfield_id)) break;
							
							$_qry = 'SELECT fieldtitle, fieldvalue '
							. 'FROM #__virtuemart_userfield_values '
							. 'WHERE virtuemart_userfield_id = ' . $_u[$BTaddress[$k]['name']]->virtuemart_userfield_id
							. " and fieldvalue = '".$db->getEscaped($vv)."' " 
							. ' limit 0,1 ';
							$db->setQuery($_qry); 
							
							
							$res = $db->loadAssoc(); 
							$e = $db->getErrorMsg(); 
							if (!empty($e)) echo $e; 
							if (isset($res))
							 {
							   if (!isset($BTaddress[$k]['value_txt'])) $BTaddress[$k]['value_txt'] = ''; 
							   //$BTaddress[$k]['value'] = $res['fieldvalue']; 
							   $BTaddress[$k]['value_txt'] .= OPCLang::_($res['fieldtitle']); 
							   if (count($vals)>1) $BTaddress[$k]['value_txt'].='<br />'; 
							 }
							 else
							 {
							   
							 }
							 }
							 break;
					
							
					
							
							 
							
				   }
		}
		
	 
	 }
	 
 	function getUserInfoBT(&$ref)
			{
			
			   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loggedshopper.php'); 
			   return OPCLoggedShopper::getUserInfoBT($ref, $this); 
			}
	
	// VM uses too many references and we need to copy the object to change it, otherwise it will change other objects as well
	function copyObj($obj)
	{
	   // we don't want references
	   if (empty($obj)) return $obj; 
	   return unserialize(serialize($obj)); 
		if (is_object($obj))
		$new = new stdClass(); 
		if (is_array($obj))
		$new =  array();
		
		
		
		if (is_array($obj))
		foreach ($obj as $k=>$v)
		{
		  if (is_array($v))
		  foreach ($v as $n=>$r)
		  {
		   $new[$k][$n] = $r; 
		  }
		}
		
	}
	
	function getUserInfoST($ref)
			{
			
			require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loggedshopper.php'); 
			return OPCLoggedShopper::getUserInfoST($ref, $this); 

			}
// variables outside the form, so it does not slow down the POST			
function getExtras(&$ref)
{
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'commonhtml.php'); 
  return OPCCommonHtml::getExtras($ref); 
 
}
  // we will not use json or jquery here as it is extremely unstable when having too many scripts on the site
  function getStateHtmlOptions(&$cart, $country, $type='BT')
   {
	
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'commonhtml.php'); 
	return OPCCommonHtml::getStateHtmlOptions($cart, $country, $type);
   }
  function getStateList(&$ref)
  {
    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'commonhtml.php'); 
	return OPCCommonHtml::getStateList($ref);
  

  }
			
 function getMediaData($id)
 {
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
   return OPCimage::getMediaData($id); 
   
  
 }
 function getImageFile($id, $w=0, $h=0)
 {
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
   return OPCimage::getImageFile($id, $w, $h); 
 
  
 
 }
 function getImageUrl($id, &$tocreate, $w=0, $h=0)
 {
  
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
   return OPCimage::getImageUrl($id, $tocreate, $w, $h);
   
  
 }
 function getActionUrl(&$ref, $onlyindex=false)
 {
   return JRoute::_('index.php'); 
   if ($onlyindex) return JURI::root(true).'/index.php'; 
   return JURI::root(true).'/index.php?option=com_virtuemart&amp;view=opc&amp;controller=opc&amp;task=checkout';
   
   
 }
 
 
 
 static function getCheckoutPrices(&$cart, $auto, &$vm2015, $other=null)
 {
  $cart->virtuemart_shipmentmethod_id  = (int)$cart->virtuemart_shipmentmethod_id; 
  $cart->virtuemart_paymentmethod_id  = (int)$cart->virtuemart_paymentmethod_id; 
  
  $saved_id =  $cart->virtuemart_shipmentmethod_id;
  $payment_id =  $cart->virtuemart_paymentmethod_id;
  $savedcoupon = $cart->couponCode; 
  
 if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
		  
		  // support for vm2.0.2 where getInstance returned fatal error
		  if (isset(calculationHelper::$_instance) && (is_object(calculationHelper::$_instance)))
		  $calc = calculationHelper::$_instance; 
		  else
		  $calc = calculationHelper::getInstance(); 
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			//$calc->_cartData = null; 
			//$ref->cart->cartData = null; 
			$calc->setCartPrices(array()); 
			}
			$prices = $calc->getCheckoutPrices($cart, false, $other); 
			if (is_null($prices))
			 {
			   $prices = $calc->_cart->cartPrices; 
			 }
			 if (method_exists($calc, 'getCartData'))
			 $cart->OPCCartData = $calc->getCartData();
			 
			 
			
			  
			 
   $cart->virtuemart_shipmentmethod_id = $saved_id; 
   $cart->couponCode = $savedcoupon; 			
   $cart->virtuemart_paymentmethod_id = $payment_id; // =  $cart->virtuemart_paymentmethod_id;
   
		return $prices; 
 }
 
 function getBasket(&$ref, $withwrapper=true, &$op_coupon='', $shipping='', $payment='', $isexpress=false)
 {
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'basket.php'); 
   return OPCBasket::getBasket($ref, $this, $withwrapper, $op_coupon, $shipping, $payment, $isexpress); 
 		
 }
 // this is needed for klarna like payment methods
	public function prepareBT(&$cart)
	{
	  if (empty($cart->BT)) $cart->BT = array(); 
	  if (!isset($cart->BT['email'])) $cart->BT['email'] = ''; 
	  if (!isset($cart->BT['first_name'])) $cart->BT['first_name'] = ''; 
	  if (!isset($cart->BT['last_name'])) $cart->BT['last_name'] = ''; 
	  if (!isset($cart->BT['virtuemart_country_id'])) $cart->BT['virtuemart_country_id'] = ''; 
	  if (!isset($cart->BT['title'])) $cart->BT['title'] = ''; 	
	}
	
	
	
	function getAdminTools(&$ref)
	{
	    $admin = false;
		$user = JFactory::getUser();
		if (!method_exists($user, 'authorise')) return ''; 
		if($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart')){
			$admin  = true;
			/*
			$adminid = $session->get('vmAdminID', 0); 
			if (empty($adminid))
			{
			$session = JFactory::getSession(); 
			$userModel = OPCmini::getModel('user'); 
			$user = $userModel->getCurrentUser(); 
			$session->set('vmAdminID', $user->virtuemart_user_id); 
			}
			*/
		}
		if (!$admin) return ''; 
		if (!isset($ref->user))
		$ref->user = JFactory::getUser(); 
		
		if (!isset($ref->cart->user))
		{
		  $ref->cart->user = OPCmini::getModel('user');
		  $ref->cart->userDetails = $ref->cart->user->getUser();
		}
		
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'cart'.DS.'tmpl'.DS.'default_shopperform.php')) return ''; 
		$adminID = JFactory::getSession()->get('vmAdminID');
		if ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser($adminID)->authorise('core.admin', 'com_virtuemart')) && (VmConfig::get ('oncheckout_change_shopper', 0))) { 

		  return $ref->loadTemplate ('shopperform');
		}

		
		
		
		//$html = '<input type="text" placeholder="'.JText::_('COM_VIRTUEMART_CART_CHANGE_SHOPPER').'" '; 
		return ''; 
	}
	
 function getPayment(&$ref, &$num, $ajax=false, $isexpress=false)
 {
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'pluginhelper.php'); 
	return OPCPluginHelper::getPayment($ref, $this, $num, $ajax, $isexpress); 

 }

 function isExpress(&$cart)
 {
 // express checkout: 
 $isexpress = false; 
 $session = JFactory::getSession(); 
 $data = $session->get('paypal', '', 'vm');
 if (empty($data)) return; 
 $ppl = @unserialize($data);
 if (!empty($ppl))
   {
       if (!empty($ppl->token)) 
	    {
			$isexpress = true; 
		}
		else return false; 
   }   
   else 
   return false; 
 
		
		//if (!empty($cart->virtuemart_paymentmethod_id))
		{
		 $payment_id = 0; //$cart->virtuemart_paymentmethod_id; 
		 
		 JPluginHelper::importPlugin('vmpayment');
		 $dispatcher = JDispatcher::getInstance();
		 $ret = array(); 
		 
		 $dispatcher->trigger('getPPLExpress', array( &$payment_id, &$cart));
		 if (!empty($payment_id))
		  {
		    $cart->virtuemart_paymentmethod_id = $payment_id; 
			
			return true; 
		  }
		  return false; 
		 //$dispatcher->trigger('getPluginOPC', array( $payment_id, &$cart, &$ret));
		 
	     if (!empty($ret))
		   {
		      foreach ($ret as $plugin)
			  {
		      if (isset($plugin->paypalproduct))
			  {
			    if ($plugin->paypalproduct == 'exp')
				  {
				   return true; 
					
				  }
			  }
			  }
		   }
		}
		return false; 
		// expressEND
 }
 
 
 // copyright: http://stackoverflow.com/questions/3810230/php-how-to-close-open-html-tag-in-a-string
 function closetags($html) {
   if (class_exists('Tidy'))
   {
    $tidy = new Tidy();
    $clean = $tidy->repairString($html, array(
    'output-xml' => true,
    'input-xml' => true
	));
	
	return $clean;
	}
    preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</'.$openedtags[$i].'>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
} 
 
 /**
	 * Fill the array with all plugins found with this plugin for the current vendor
	 *
	 * @return True when plugins(s) was (were) found for this vendor, false otherwise
	 * @author Oscar van Eijk
	 * @author max Milbers
	 * @author valerie Isaksen
	 */
    
	public static function getPluginMethods ($type='shipment', $vendorId=1) {
		if (!empty(OPCloader::$methods[$type])) return OPCloader::$methods[$type]; 
		if (!class_exists ('VirtueMartModelUser')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
		}
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
		$usermodel = OPCmini::getModel ('user');
		$user = $usermodel->getUser ();
		$user->shopper_groups = (array)$user->shopper_groups;

		$db = JFactory::getDBO ();

		$select = 'SELECT l.*, v.*, ';

		if (JVM_VERSION === 1) {
			$extPlgTable = '#__plugins';
			$extField1 = 'id';
			$extField2 = 'element';

			$select .= 'j.`' . $extField1 . '`, j.`name`, j.`element`, j.`folder`, j.`client_id`, j.`access`,
				j.`params`,  j.`checked_out`, j.`checked_out_time`,  s.virtuemart_shoppergroup_id ';
		} else {
			$extPlgTable = '#__extensions';
			$extField1 = 'extension_id';
			$extField2 = 'element';

			$select .= 'j.`' . $extField1 . '`,j.`name`, j.`type`, j.`element`, j.`folder`, j.`client_id`, j.`enabled`, j.`access`, j.`protected`, j.`manifest_cache`,
				j.`params`, j.`custom_data`, j.`system_data`, j.`checked_out`, j.`checked_out_time`, j.`state`,  s.`virtuemart_shoppergroup_id` ';
		}
		if (isset(VmConfig::$vmlang))
		$vmlang = VmConfig::$vmlang; 
		else 
		$vmlang = VMLANG; 
		$q = $select . ' FROM   `#__virtuemart_' . $type . 'methods_' . $vmlang . '` as l ';
		$q .= ' JOIN `#__virtuemart_' . $type . 'methods` AS v   USING (`virtuemart_' . $type . 'method_id`) ';
		$q .= ' LEFT JOIN `' . $extPlgTable . '` as j ON j.`' . $extField1 . '` =  v.`' . $type . '_jplugin_id` ';
		$q .= ' LEFT OUTER JOIN `#__virtuemart_' . $type . 'method_shoppergroups` AS s ON v.`virtuemart_' . $type . 'method_id` = s.`virtuemart_' . $type . 'method_id` ';
		$q .= ' WHERE v.`published` = "1" ';
		//AND j.`' . $extField2 . '` = "' . $this->_name . '"
		$q .= ' AND  (v.`virtuemart_vendor_id` = "' . $vendorId . '" OR   v.`virtuemart_vendor_id` = "0")  AND  (';

		foreach ($user->shopper_groups as $groups) {
			$q .= ' (s.`virtuemart_shoppergroup_id`= "' . (int)$groups . '") OR';
		}
		$q .= ' (s.`virtuemart_shoppergroup_id` IS NULL )) GROUP BY v.`virtuemart_' . $type . 'method_id` ORDER BY v.`ordering`';

		$db->setQuery ($q);

		$methods = $db->loadAssocList ();
		$arr = array(); 
		if (!empty($methods))
		foreach ($methods as $m)
		{
		  $arr[$m['virtuemart_'.$type.'method_id']] = $m; 
		  
		}
	    OPCloader::$methods[$type] = $arr; 
		return $arr; 
		$err = $db->getErrorMsg ();
		if (!empty($err)) {
			vmError ('Error reading getPluginMethods ' . $err);
		}
		/*
		if ($this->methods) {
			foreach ($this->methods as $method) {
				VmTable::bindParameterable ($method, $this->_xParams, $this->_varsToPushParam);
			}
		}
		*/
	
		return count ($this->methods);
	}
 
 function getShipping(&$ref, &$cart, $ajax=false)
 {
 
	if (empty($cart))
	{
     if (!empty($ref->cart))
		{
		  $cart =& $ref->cart; 
		}
		else
		  $cart = VirtueMartCart::getCart(false, false); 
	}
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   $cmd = JRequest::getVar('cmd', false); 
   
   
     	
   
   //$methods = OPCloader::getPluginMethods(); 
   if (!$ajax)
   {
	  
	  if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
	  JPluginHelper::importPlugin('vmpayment');
	  $dispatcher = JDispatcher::getInstance(); 
	  if (!isset($cart))
	  $cart = VirtueMartCart::getCart ();
	  $plugins = array(); 
	  $html = ''; 
	  $results = $dispatcher->trigger('loadPluginJavascriptOPC', array( &$cart, &$plugins, &$html)); 

	  
     //include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_shipping_javascript.php'); 
	 if (!empty($html))
	  {
	    OPCloader::$extrahtml .= $html; 
	  }
	  unset($html); 
    // so we don't update the address twice   
     require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
	 $c = new VirtueMartControllerOpc();  
     $c->setAddress($cart, true, false, true); 
	 
   }	
  
     if ($cmd != 'customershipping')
   if (!empty($op_customer_shipping))
   {

    $onclick = 'onclick="javascript: return Onepage.op_runSS(null, false, true, \'customershipping\');" ';
    $html = $this->fetch($ref, 'customer_shipping', array('onclick'=>$onclick)); 
	if (empty($html))
	$html = '<a href="#" '.$onclick.'  >'.OPClang::_('COM_ONEPAGE_CLICK_HERE_TO_DISPLAY_SHIPPING').'</a>'; 
	$html .= '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />'; 
	return '<div id="customer_shipping_wrapper">'.$html.'</div>'; 
   }
  
  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 
   $bhelper = new basketHelper; 
  
   $sh = $bhelper->getShippingArrayHtml($ref, $cart, $ajax);
    
   
   
	 
   if (empty($cart) || (empty($cart->products)))
   {
    
      $op_disable_shipping = OPCloader::getShippingEnabled($cart); 
	 if (empty($op_disable_shipping))
	 {
     $html = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />'; 
	 }
	 $html .= '<div style="color: red; font-weight: bold;">'.OPCLang::_('COM_VIRTUEMART_EMPTY_CART').'</div>'; 
     $sh = array($html); 	 
   }
   
  
   
   if (!empty($disable_payment_per_shipping))
   {
   
   $session = JFactory::getSession(); 
   $dpps =  array();
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'transform.php');
   foreach ($sh as $k=>$cs)
   {
     foreach ($dpps_search as $key=>$val)
	  {
	    // if we find the need in the shipping, let's associate it with an id
		$val = urldecode($val); 
	    if (strpos($cs, $val)!==false)
		 {
		 //if (!empty($dpps[$key])) continue; 
		   $id = OPCTransform::getFT($cs, 'input', 'virtuemart_shipmentmethod_id', 'name', 'virtuemart_shipmentmethod_id', '>', 'value');
		   if (is_array($id)) $id = reset($id); 
		   if (empty($dpps[$key])) $dpps[$key] = array(); 
		   $dpps[$key][] = $id; 
		 
		 
		 }
	  }
   }
   $session->set('dpps', $dpps); 
   }
   
   if (($cart->pricesUnformatted['billTotal']) && empty($cart->pricesUnformatted['billTotal']))
   $ph = array();
   else
   $ph = $bhelper->getPaymentArray(); 
	
 
   
  
    $bhelper->createDefaultAddress($ref, $cart); 
	
	
	
	$html = $bhelper->getPaymentArrayHtml($ref->cart, $ph, $sh); 
	self::$totals_html = basketHelper::$totals_html; 
	
	$bhelper->restoreDefaultAddress($ref, $cart); 
	
	//$ret = implode('<br />', $sh); 
	$ret = '';
	
	$ret .= $html; 
  
	
	return $ret; 
 }
 
 function setDefaultShipping($sh, $ret)
 {
 }
 
 function addListeners($html)
 {
 
   	//if (constant('NO_SHIPPING') != '1')
	{
	// add ajax to zip, address1, address2, state, country
	$html = str_replace('id="shipto_zip_field"', ' onblur="javascript:Onepage.op_runSS(this);" id="shipto_zip_field"', $html);
	$html = str_replace('id="shipto_address_1_field"', ' id="shipto_address_1_field" onblur="javascript:Onepage.op_runSS(this);" ', $html); 
	$html = str_replace('id="shipto_address_2_field"', ' id="shipto_address_2_field" onblur="javascript:Onepage.op_runSS(this);" ', $html); 
	
	$user = JFactory::getUser(); 
	$uid = $user->get('id'); 
	$usersConfig = JComponentHelper::getParams( 'com_users' );
	$usernamechange = $usersConfig->get( 'change_login_name', true );
	if (empty($usernamechange))
	if (!empty($uid))
	{
	  // username readonly
	  $html = str_replace('name="username"', ' readonly="readonly" name="username"', $html); 
	  $html = str_replace('name="opc_username"', ' readonly="readonly" name="opc_username"', $html); 
	}
	
	$html = str_replace('id="shipto_virtuemart_state_id"', 'id="shipto_virtuemart_state_id" onchange="javascript:Onepage.op_runSS(this);" ', $html);
	
	$cccount = strpos($html, '"shipto_virtuemart_state_id"'); 

	 if ($cccount !== false)
	 {
	   $par = "'true', ";
	   $isThere = true;
	 }
	 else
	 {
	     $par = "'false', ";
	     $isThere = false;
	 }
	  $html = str_replace('id="shipto_virtuemart_country_id"', 'id="shipto_virtuemart_country_id" onchange="javascript: Onepage.op_validateCountryOp2('.$par.'\'true\', this);" ', $html, $count);
	}
	
	 // state fields
	 $cccount = strpos($html, '"virtuemart_state_id"'); 
	 if ($cccount !== false)
	 {
	   $par = "'true', ";
	   $isThere = true;
	 }
	 else
	 {
	     $par = "'false', ";
	     $isThere = false;
	 }
	 
	 $count = 0; 
	$html = str_replace('id="zip_field"', ' onblur="javascript:Onepage.op_runSS(this);" id="zip_field"', $html);
	$html = str_replace('id="address_1_field"', ' id="address_1_field" onblur="javascript:Onepage.op_runSS(this);" ', $html); 
	$html = str_replace('id="address_2_field"', ' id="address_2_field" onblur="javascript:Onepage.op_runSS(this);" ', $html); 
	
	$html = str_replace('id="virtuemart_state_id"', 'id="virtuemart_state_id" onchange="javascript:Onepage.op_runSS(this);" ', $html);

	
	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	if (!empty($opc_euvat))
	if (empty($opc_euvat_button))
	{
	
	  $html = str_replace('id="opc_vat_field"', 'id="opc_vat_field" onchange="javascript: return Onepage.validateOpcEuVat(this);" ', $html);
     
	}
	
	 $html = str_replace('id="virtuemart_country_id"', 'id="virtuemart_country_id" onchange="javascript: Onepage.op_validateCountryOp2('.$par.'\'false\', this);" ', $html, $count);
	
	 //pluginistraxx_euvatchecker_field
	$html = str_replace('id="pluginistraxx_euvatchecker_field"', ' id="pluginistraxx_euvatchecker_field" onblur="javascript:Onepage.op_runSS(this, false, true);" ', $html); 
	// support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
	
			if  ( VmConfig::isJ15() ) { 
				$plugin_short_path = 'plugins/system/bitvatidchecker/';
				}
			else {
				$plugin_short_path = 'plugins/system/bit_vm_check_vatid/bitvatidchecker/';
			}
	
	if (file_exists(JPATH_SITE.DS.$plugin_short_path))
	{
		include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'system'.DS.'bit_vm_check_vatid'.DS.'include.php'); 
	}
		
	// end support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
	
	
	return $html;
 }
 function getJSValidatorScript($obj)
 {
   return $this->fetch($this, 'formvalidator', array()); 
 }
 
 function isRegistered()
 {
 }
 
 function isNoLogin()
 {
    include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	$currentUser = JFactory::getUser();
 $uid = $currentUser->get('id');
 if (!empty($uid)) 
 { 
 
 $no_login_in_template = true; 
 }
 if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
 {
 $no_login_in_template = true; 
 }
   return $no_login_in_template; 
 }
 
 // input param is object
 function hasMissingFieldsST($STaddress)
 {
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   $ignore = array('delimiter', 'captcha', 'hidden'); 
  
  $types = array(); 
   foreach ($STaddress as $key=>$val)
     {
	   //if (in_array($val['name'], $corefields)) continue; 
	   //if (in_array($val['type'], $ignore)) continue; 
	   //if (empty($val['value']))
	   if (empty($val))
	   if (in_array($key, $shipping_obligatory_fields))
	    {
		 //if (!empty($val['required']))
		  if ($key == 'virtuemart_state_id')
				{
				  $c = $val;
				  $stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	
				  $states = $stateModel->getStates( $c, true, true );
				  if (!empty($states)) return true; 
				  continue; 
				}
				return true; 
		}
	    //$types[] = $val['type']; 
	 }
	 
	 return false; 
 }
 function hasMissingFields(&$BTaddress)
 {
 	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 
	
  return OPCUserFields::hasMissingFields($BTaddress); 
   
 
	 
 }
 
 function getRegistrationHhtml(&$obj)
 {
  
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'unloggedshopper.php'); 
  return OPCUnloggedShopper::getRegistrationHhtml($obj, $this);
 
   
 }
 
  public function customizeFieldsPerOPCConfig(&$userFields)
  {
   
   if (empty($userFields)) return;
   if (count($userFields['fields'])===0) 
	{
	 // no fields found
	 return '';
	}
	
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 
   
    
	
	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 

/* 
$newf = array(); 
$newf['fields'] = array(); 

if (isset($userFields['fields']['name']))
$newf['fields']['name'] = $userFields['fields']['name']; 
*/
$user = JFactory::getUser(); 
$user_id = $user->get('id'); 
if (empty($user_id))
if (isset($userFields['fields']['password']))
if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
{
  $ra = array(); 
  $ra['formcode'] = '<input type="checkbox" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox checkbox inline" onchange="Onepage.showFields( this.checked, new Array('; 
						
						if (empty($op_usernameisemail))
						$ra['formcode'] .= '\'username\', \'password\', \'password2\', \'opc_password\''; 
						else $ra['formcode'] .= '\'password\', \'password2\', \'opc_password\''; 
					$ra['formcode'] .= ') );" '; 
					if (empty($op_create_account_unchecked)) 
					$ra['formcode'] .= ' checked="checked" '; 
					$ra['formcode'] .= '/>';
					$ra['name'] = 'register_account'; 
					$ra['title'] = OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER'); 
					$ra['required'] = false; 
					$ra['type'] = 'checkbox'; 
					$ra['readonly'] = false; 
					$ra['hidden'] = false; 
					$ra['description'] = ''; 
					
$userFields['fields']['register_account'] = $ra; 
}


 if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	 
 $cart = VirtuemartCart::getCart(); 
 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 
 OPCUserFields::getUserFields($userFields, $this, $cart); 
	
	$user = JFactory::getUser(); 
	$uid = $user->get('id'); 
	$guest = $user->get('guest'); 
	if (empty($guest) || (!empty($uid)))
	 {
	 
	    $arr = array('password', 'opc_password', 'password2', 'opc_password2', 'username', 'virtuemart_state_id', 'shipto_virtuemart_state_id'); 
	    foreach ($userFields['fields'] as $key=>$f)
		 {
		
			if (in_array($key, $arr))
			 {
			 
			    $userFields['fields'][$key]['formcode'] = str_replace('required', 'notrequired', $f['formcode']); 
				$userFields['fields'][$key]['required'] = false; 
				
				
			 }
			 if ($key == 'virtuemart_state_id')
			 {
			 
			 }
			 
		 }
	 }
	
	  
  }
 
  function getHtmlInBetween(&$ref)
  {
   $html = '<div class="opc_errors" id="opc_error_msgs" style="display: none; width: 100%; clear:both; border: 1px solid red;">&nbsp;</div>';
   return $html;
  }
 
 
 
 static function setShopperGroup($id, $remove=array())
 {
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
	return OPCShopperGroups::setShopperGroups($id, $remove); 
	
    
 }
 
 
 
 // only for unlogged users 
 static function getSetShopperGroup($debug=false)
 {
 
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
	return OPCShopperGroups::getSetShopperGroup($debug); 
 }
 
 static function getDefaultCountry(&$cart, $searchBT=false )
  {
     if ($searchBT)
	 {
	   if (!empty($cart->BT['virtuemart_country_id']))
	   return $cart->BT['virtuemart_country_id'];
	 }
     if (defined('OPC_DEFAULT_COUNTRY')) return OPC_DEFAULT_COUNTRY; 
	 if (defined('DEFAULT_COUNTRY')) 
     if (is_numeric(DEFAULT_COUNTRY))
	 {
	  define('OPC_DEFAULT_COUNTRY', DEFAULT_COUNTRY); 
	  return DEFAULT_COUNTRY;
	 }
     include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 if (!empty($op_use_geolocator))
	  {
	    if (file_exists(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php'))
		{
	     include_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php');
		 if (class_exists('geoHelper')) 
		 $c2 = geoHelper::getCountry2Code("");
		 if (!empty($c2))
		 {
		  $db = JFactory::getDBO(); 
		  $q = "select virtuemart_country_id from #__virtuemart_countries where country_2_code = '".$db->getEscaped($c2)."' limit 0,1"; 
		  $db->setQuery($q); 
		  $c = $db->loadResult(); 
		  if (!empty($c)) 
		    {
			  define('OPC_DEFAULT_COUNTRY', $c); 
			  if (!defined('DEFAULT_COUNTRY'))
			  define('DEFAULT_COUNTRY', $c); 
			  
			  // case IP address
			  return $c; 
			}
		 }
		}
	  }
	  $lang = JFactory::getLanguage();
	  $tag = $lang->getTag();
	  if (!empty($default_country_array[$tag]))
	  {
	   define('DEFAULT_COUNTRY', $default_country_array[$tag]); 
	   define('OPC_DEFAULT_COUNTRY', $default_country_array[$tag]); 
	   return $default_country_array[$tag];
	  }
	  
	  if (!empty($default_shipping_country))
	  {
	   define('DEFAULT_COUNTRY', $default_shipping_country ); 
	   define('OPC_DEFAULT_COUNTRY', $default_shipping_country ); 
	  
	    return $default_shipping_country; 
	  }
	  
	  //
	
	//$default_country_array["en-GB"] = "222"; 

     //$default_country_array["sk-SK"] = "189"; 
	 
	 
     $vendor = OPCloader::getVendorInfo($cart); 
	 if (!empty($vendor))
	 {
	  $c = $vendor['virtuemart_country_id']; 
	 define('DEFAULT_COUNTRY', $c ); 
	 define('OPC_DEFAULT_COUNTRY', $c ); 
	  return $c; 
	 }
	 
	  
  }
 
 function setRegType()
 {
   if (!defined('VM_REGISTRATION_TYPE'))
   {
    if (VmConfig::get('oncheckout_only_registered', 0))
	{
	  if (VmConfig::get('oncheckout_show_register', 0))
	  define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
	  else 
	  define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
	}
	else
	{
	if (VmConfig::get('oncheckout_show_register', 0))
    define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
	else 
	define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
	}
   } 
 }
 function getSTfields(&$obj, $unlg=false, $no_wrapper=false, $dc='')
 {
  //$x = debug_backtrace(); foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />\n"; 
  static $isUpdated; 
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 

  if ($this->logged($obj->cart) && (empty($unlg)))
  {
   
    return $this->getUserInfoST($obj); 
  }


  
    if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	
	if (!empty($obj->cart))
	$cart =& $obj->cart; 
	else
	$cart = VirtueMartCart::getCart();
	
	
	if (!empty($dc))
	$default_shipping_country = $dc; 
	else
    $default_shipping_country = OPCloader::getDefaultCountry($cart); 
    
	if ($cart->ST === 0)
    if (isset($cart->savedST))
    {
	  $cart->ST = $cart->savedST;
	  if (isset($cart->ST['shipto_virtuemart_country_id']))
      $default_shipping_country = $cart->ST['virtuemart_country_id']; 
	  

    }
	
  
  
   $type = 'ST'; 
   $this->address_type = 'ST'; 
   // for unlogged
// for unlogged
   $virtuemart_userinfo_id = 0;
   //$this->virtuemart_userinfo_id = 0;
   $new = 1; 
   if (!empty($unlg)) $new = false;
   $fieldtype = $type . 'address';
   
   //$cart->STaddress = null; 
   if (method_exists($cart, 'prepareAddressDataInCart'))
   $cart->prepareAddressDataInCart($type, $new);
   /*
   if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
   */
	$this->setRegType(); 
   
   $op_disable_shipto = $this->getShiptoEnabled($cart); 
   if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
   $corefields = VirtueMartModelUserfields::getCoreFields();
   $userFields = $cart->$fieldtype;
   
 
   //foreach ($corefields as $f)
   foreach ($userFields['fields'] as $key=>$uf)   
   {
     
     OPCloader::$fields_names['shipto_'.$key] = $userFields['fields'][$key]['title']; 
     $userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']); 
     if (!empty($corefields))
     foreach($corefields as $k=>$f)
	  {

	    if ($f == $uf['name'])
		 {
	 	   unset($userFields['fields'][$key]);   
		   unset($corefields[$k]);
		 }
		 
	  }
	  if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
	   if (in_array($uf['name'], $custom_rendering_fields))
				    {
					  unset($userFields['fields'][$key]); 
					  continue; 
					}
	  
	  $userFields['fields'][$key]['formcode'] = str_replace('class="virtuemart_country_id required"', 'class="virtuemart_country_id"', $userFields['fields'][$key]['formcode']);
	  
	  $userFields['fields'][$key]['formcode'] = str_replace('required>', '', $userFields['fields'][$key]['formcode']);
	  $userFields['fields'][$key]['formcode'] = str_replace(' required ', '', $userFields['fields'][$key]['formcode']);
      
	  $userFields['fields'][$key]['formcode'] = str_replace('required"', '"', $userFields['fields'][$key]['formcode']);
	  
	  if ($key == 'address_type_name')
		 {
		 $userFields['fields'][$key]['formcode'] = str_replace('Shipment', JText::_('COM_VIRTUEMART_SHOPPER_FORM_SHIPTO_LBL'), $userFields['fields'][$key]['formcode']); 
		 
		 }
	  
	  if (!empty($userFields['fields'][$key]['required']))
	  {
	    $userFields['fields'][$key]['required'] = false; 
	  }
	  if (!empty($shipping_obligatory_fields))
	  {
	    if (in_array($key, $shipping_obligatory_fields))
		$userFields['fields'][$key]['required'] = true; 
	  }
	  // let's add a default address for ST section as well: 
	  if ((($key == 'virtuemart_country_id')))
	  if (((empty($unlg))) || (!empty($default_shipping_country)))
	  {
	 
	  
	  $userFields['fields'][$key]['formcode'] = str_replace('selected="selected"', '', $userFields['fields'][$key]['formcode']);

	  $search = 'value="'.$default_shipping_country.'"';
	  $replace = ' value="'.$default_shipping_country.'" selected="selected" ';
	  $userFields['fields'][$key]['formcode'] = str_replace($search, $replace, $userFields['fields'][$key]['formcode']);

	  
	  
	 
	 }
	 
	  if (($key == 'virtuemart_country_id'))
	   {
	   
	      $userFields['fields'][$key]['formcode'] = str_replace('name=', ' autocomplete="off" name=', $userFields['fields'][$key]['formcode']); 
	   }
	 
	 //if (false)
	 if (isset($userFields['fields'][$key]))
	 {
	 
		
	 if ($key == 'virtuemart_state_id')
	  {
	  
	  if (!empty($cart->ST['virtuemart_country_id']))
	  $c = $cart->ST['virtuemart_country_id']; 
	  else $c = $default_shipping_country; 
	  
	  if (empty($c))
	  {
	    $vendor = $this->getVendorInfo($cart); 
		$c = $vendor['virtuemart_country_id']; 
	  }
	  
	     $html = $this->getStateHtmlOptions($cart, $c, 'ST');
		
		 if (!empty($cart->ST['virtuemart_state_id']))
		 {
		   $html = str_replace('value="'.$cart->ST['virtuemart_state_id'].'"', 'value="'.$cart->ST['virtuemart_state_id'].'" selected="selected"', $html); 
		 }
		 else
		 if (!empty($cart->ST['shipto_virtuemart_state_id']))
		 {
		   $html = str_replace('value="'.$cart->ST['shipto_virtuemart_state_id'].'"', 'value="'.$cart->ST['shipto_virtuemart_state_id'].'" selected="selected"', $html); 
		 
		 }
		
		 if (!empty($userFields['fields'][$key]['required']))
		 $userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple opcrequired" id="shipto_virtuemart_state_id" opcrequired="opcrequired" size="1"  name="shipto_virtuemart_state_id" >'.$html.'</select>'; 
		 else
	     $userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple" id="shipto_virtuemart_state_id"  size="1"  name="shipto_virtuemart_state_id" >'.$html.'</select>'; 
	    //$f2 = $userFields['fields'][$key]; 
		//unset($userFields['fields'][$key]); 
		$userFields['fields']['virtuemart_state_id']['formcode'] = str_replace('id="virtuemart_state_id"', 'id="'.$userFields['fields']['virtuemart_state_id']['name'].'"', $userFields['fields']['virtuemart_state_id']['formcode']); 
	  }
	 //$orig = $userFields['fields'][$key]['name'];
	 //$new = 'sa_'.strrev($orig); 
	 //$userFields['fields'][$key]['name'] = $new;
	 //$userFields['fields'][$key]['formcode'] = $this->reverseId($userFields['fields'][$key]['formcode'], $orig, $new ); 
	 }
   }
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   $this->_model = OPCmini::getModel('user'); //new VirtuemartModelUser();
   $layout = 'default';
  
	$hidden = array(); 
	$hidden_html = ''; 
	foreach ($userFields['fields'] as $key=>$val)
	 {
	   
	   if (!empty($val['hidden']))
	   {
	    $hidden[] = $val; 
		$hidden_html .= $val['formcode']; 
		unset($userFields['fields'][$key]); 
	   }
	 }
  $vars = array('rowFields' => $userFields, 
				 'cart' => $cart, 
				 'opc_logged' => $unlg,
				 );
   $html = $this->fetch($this, 'list_user_fields_shipping.tpl', $vars); 
   
   $html .= $hidden_html; 
   
   $html = $this->addListeners($html);
   if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
   if (in_array('virtuemart_country_id', $custom_rendering_fields)) $html .= '<input type="hidden" id="shipto_virtuemart_country_id" name="shipto_virtuemart_country_id" value="'.$default_shipping_country.'" />'; 
   if ((in_array('virtuemart_state_id', $custom_rendering_fields)))
   $html .= '<input type="hidden" id="shipto_virtuemart_state_id" name="shipto_virtuemart_state_id" value="0" />';   
   
   $html = str_replace('class="required"', 'class=" "', $html);
   
   $vars = array('op_shipto' => $html); 
   
   if (!empty($only_one_shipping_address_hidden) && (!empty($unlg)))
   {
   
     $html2 = '<input type="hidden" id="sachone" name="sa" value="adresaina" /><div id="ship_to_wrapper"><div id="idsa">'.$html.'</div></div>'; 
	 
	 
   }
   else
   {
   $html2 = $this->fetch($this, 'single_shipping_address.tpl', $vars); 
   
   if (empty($html2) && (!empty($unlg)))
   {
     // if the new theme file not found:
	 $html2 = '<div id="ship_to_wrapper"><input type="checkbox" id="sachone" name="sa" value="adresaina" onkeypress="javascript: Onepage.showSA(this, \'idsa\');" onclick="javascript: Onepage.showSA(this, \'idsa\');" autocomplete="off" />'.OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL').'<div id="idsa" style="display: none;">
								  '.$html.'</div></div>'; 
   }
   }
   
   // if theme does not exists, return legacy html
   if (empty($html2) || (!empty($no_wrapper))) 
     return $html; 
   
   
   
   return $html2;
   
 }
 function reverseId($html, $orig, $new)
 {
   // replaces name and id
   $html = str_replace($orig, $new, $html); 
   //$html = str_replace('id="'.$orig.'_field', 'id="'.$new.'_field', $html); 
   return $html;
 }
 function logged(&$cart)
 {
   /*
	if (!class_exists('VirtuemartModelUser'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
			*/
				require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
				$umodel = OPCmini::getModel('User'); //new VirtuemartModelUser();
				
				$virtuemart_userinfo_id = 0;
				/*
				$currentUser =& JFactory::getUser();
				$uid = $currentUser->get('id');
				*/
				
			
				
				$user = JFactory::getUser();
			    $userId = (int)$user->id; 
				
				
				
				// support for j1.7+
				if (!empty($user->guest) && ($user->guest == '1')) return false; 
				
				if (empty($userId)) return false; 
				
				$db = JFactory::getDBO(); 
				$q = "select virtuemart_userinfo_id from #__virtuemart_userinfos where virtuemart_user_id = '".$userId."' and address_type = 'BT' limit 0,1 "; 
				$db->setQuery($q); 
				$uid = $db->loadResult(); 
				
				
				
				if (empty($uid)) return false;
				if (method_exists($umodel, 'setId'))
				$umodel->setId($userId); 
			
				$virtuemart_userinfo_id = $uid; 
			
				$userFields = $umodel->getUserInfoInUserFields('default', 'BT', $uid);
				
				
				
				if (empty($userFields[$virtuemart_userinfo_id]))
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				else $virtuemart_userinfo_id = $userFields[$virtuemart_userinfo_id]; 
				
				$id = $umodel->getId(); 
				
				
				
				if (empty($virtuemart_userinfo_id)) return false; 
				else return true;
 
  /* 
  if (!class_exists('VirtueMartModelUser'))
  require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
   */
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
  $usermodel = OPCmini::getModel('user'); //new VirtueMartModelUser();
  $user = JFactory::getUser();
  $usermodel->setId($user->get('id'));

  
  $user = $usermodel->getUser();
  
  if (empty($user->virtuemart_user_id)) return false;
  if (!empty($cart) && (!empty($cart->BTaddress))) return true; 
   return false; 
 }
 
 public static $vendorInfo;
 
 function &getVendorInfo(&$cart)
 {
  
  if (OPCloader::$vendorInfo == null) 
	 if (OPCloader::tableExists('virtuemart_vmusers'))
    {
  if (empty($cart->vendorId)) $vendorid = 1; 
  else $vendorid = $cart->vendorId;
{
  $dbj = JFactory::getDBO(); 

  $q = "SELECT * FROM `#__virtuemart_userinfos` as ui, #__virtuemart_vmusers as uu WHERE ui.virtuemart_user_id = uu.virtuemart_user_id and uu.virtuemart_vendor_id = '".(int)$vendorid."' limit 0,1";
  $dbj->setQuery($q);
	
   $vendorinfo = $dbj->loadAssoc();
   
   OPCloader::$vendorInfo = $vendorinfo; 
	
	return $vendorinfo; 
}
   }
	else
	return null; 
   
   return OPCloader::$vendorInfo; 
 }
 
 
 
 function getBTfields(&$obj, $unlg=false, $no_wrapper=false)
 {
   
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
    $default_shipping_country = OPCloader::getDefaultCountry($cart); 
   // $default_shipping_country
   $islogged = $this->logged($obj->cart); 
   
   if ($islogged && (empty($unlg)))
   {
   
     return $this->getUserInfoBT($obj); 
   }
   else
   {
    if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	
	if (!empty($obj->cart)) 
	$cart =& $obj->cart; 
	else
	$cart = VirtueMartCart::getCart();
		
   $type = 'BT'; 
   $this->address_type = 'BT'; 
   // for unlogged
   $virtuemart_userinfo_id = 0;
   $this->$virtuemart_userinfo_id = 0;
   $new = 1; 
   if (!empty($unlg)) $new = false;
   $fieldtype = $type . 'address';

    if (empty($cart->BT)) $cart->BT = array();    
   $user = JFactory::getUser();
   $uid = $user->get('id');
   
   // PPL Express address: 
   $moveBT = false; 
   $count = 0; 
   if (!empty($cart->savedST))
   if (!$islogged)
   {
   
   foreach ($cart->savedST as $key=>$val)
   {
     if ($key == 'virtuemart_country_id') continue; 
	 if ($key == 'virtuemart_state_id') continue; 
     if (empty($cart->BT[$key]) && (!empty($val)))
	  {
	    $count++; 
	  }
	  else
	 if ((!empty($cart->BT[$key])) && ($val != $cart->BT[$key]))
	  {
	    $count--; 
	  }
   }
   if ($count > 0)
    {
	  if ($cart->savedST['virtuemart_country_id'] != $cart->BT['virtuemart_country_id'])
	   {
	     $cart->BT['virtuemart_state_id'] = 0; 
	   }
	  foreach ($cart->savedST as $key=>$val)
	    {
		  if (!empty($val))
		  $cart->BT[$key] = $val; 
		}
	}
   }

   if (empty($cart->BT['virtuemart_country_id'])) 
   {

    if (!empty($default_shipping_country) && (is_numeric($default_shipping_country)))
	 {
	   $cart->BT['virtuemart_country_id'] = $default_shipping_country; 
	 }
	 else
	 {
    // let's set a default country
	$vendor = $this->getVendorInfo($cart); 
	$cart->BT['virtuemart_country_id'] = $vendor['virtuemart_country_id']; 
	 }
   }
   
   
   $savedBT = $cart->BT; 
   if (method_exists($cart, 'prepareAddressDataInCart'))
   $cart->prepareAddressDataInCart($type, false);
   foreach ($savedBT as $key=>$val)
    {
	  if (!empty($savedBT->$key))
	  $cart->$key = $savedBT->$key; 
	}
   
   /*
   if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
   */
   
   
   
   $this->setRegType(); 
   
   $op_disable_shipto = $this->getShiptoEnabled($cart); 
   if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
   $corefields = VirtueMartModelUserfields::getCoreFields();
   $userFields = $cart->$fieldtype;
   
   
    
    if ((isset($cart->BTaddress)) && (isset($cart->BTaddress['fields'])) && (isset($cart->BTaddress['fields']['virtuemart_country_id'])) && (!empty($cart->BTaddress['fields']['virtuemart_country_id']['value'])))
	{
	   if (is_numeric($cart->BTaddress['fields']['virtuemart_country_id']['value']))
	   $cart->BT['virtuemart_country_id'] = $cart->BTaddress['fields']['virtuemart_country_id']['value'];
	   
	}
   

	// unset corefields
      $onlyf = array(); 
	  if (empty($bt_fields_from))
	  {
	    $q = 'select name from #__virtuemart_userfields where published=1 and registration = 1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$onlyf2 = $db->loadAssocList(); 
		foreach ($onlyf2 as $k=>$v)
		 {
		  $onlyf[] = $v['name']; 
		 }
	  }
   
   foreach ($userFields['fields'] as $key=>$uf)   
   {
   
   // disable fields that are not marked for registration
   if (!empty($onlyf))
   {
     if (!in_array($uf['name'], $onlyf)) 
	  {
	    unset($userFields['fields'][$key]); 
		continue; 
	  }
   }
   
	   $userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']); 
	OPCloader::$fields_names[$key] = $userFields['fields'][$key]['title']; 
	if ($userFields['fields'][$key]['type'] == 'delimiter') 
	    {
		  unset($userFields['fields'][$key]); 
		  continue; 
		}
     foreach ($corefields as $f)
	 {
     if ($f == $uf['name'])
	 {
	  // will move the email to bt section
	   if (empty($no_login_in_template) || ($unlg))
	  {
	   if ($f == 'email') 
	    {
		  if (empty($opc_email_in_bt))
		  if (!$this->isNoLogin())
		  unset($userFields['fields'][$key]);
		}
	   else	
	   unset($userFields['fields'][$key]);
	   continue;
	  }
	  
	 
	  
	  
	 }
	 }
	  
	  if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
	  if (!empty($custom_rendering_fields))
	  if (in_array($uf['name'], $custom_rendering_fields))
				    {
					  unset($userFields['fields'][$key]); 
					  continue; 
					}
	
     if ($key == 'name')	
	 if (!empty($op_no_display_name))
	 if (!empty($userFields['fields']['name']))
	  {
	    unset($userFields['fields']['name']);
	  }					
	 
	 } // end of for each
 	 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 	 
	 
	 $skipreorder = array('email'); 
	 OPCUserFields::getUserFields($userFields, $this, $cart, array(), array(), $skipreorder); 
	
   
   	

     
	  
	  // logic reversed, if email is not in BT, remove it
     if (!((!empty($opc_email_in_bt) || (($this->isNoLogin()))) && (!empty($double_email))))
	  {
	    unset($userFields['fields']['email2']);
	    // email is in BT, let's check for double mail

	  }
	  
	 
	  $skipreorder = array(); 
	 if ((!empty($opc_email_in_bt) || (($this->isNoLogin()))))
	 {
	 $skipreorder[] = 'email'; 
	 if (!empty($opc_check_email))
	 {
	  
	  
	  if ((!$this->logged($cart)) && (empty($uid)))
	  if (!empty($userFields['fields']['email']))
	  {
		  
	     $un = $userFields['fields']['email']['formcode']; 
		 if (stripos($un, 'id="email_already_exists"')===false)
		 {
		 //if (!$double_email)
		 $un = str_replace('id=', ' onblur="javascript: Onepage.email_check(this);" id=', $un);
	     
		 
		 $un .=  '<span class="email_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email_already_exists">';
		 $un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL')); 
		 $un .= '</span>'; 
		 $userFields['fields']['email']['formcode'] = $un; 
		 }
	  }
	  }
	  }
		
      
	  
	  
	  
	   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 
		OPCUserFields::reorderFields($userFields, $skipreorder); 

		
		
     
   
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php');    
   $this->_model = OPCmini::getModel('user'); //new VirtuemartModelUser();
    $layout = 'default';
  
  $hidden = array(); 
	$hidden_html = ''; 
	
	foreach ($userFields['fields'] as $key=>$val)
	 {
	   
	   if (!empty($val['hidden']))
	   {
	    $hidden[] = $val; 
		$hidden_html .= $val['formcode']; 
		unset($userFields['fields'][$key]); 
	   }
	 }
   $vars = array('rowFields' => $userFields, 
				 'cart'=> $obj, 
				 'is_logged'=> $unlg);
   $html = $this->fetch($this, 'list_user_fields.tpl', $vars); 
   $hidden_html = str_replace('"required"', '""', $hidden_html); 
   $html .= $hidden_html; 
	
   $html = $this->addListeners($html);
	if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
   if (in_array('virtuemart_country_id', $custom_rendering_fields)) $html .= '<input type="hidden" id="virtuemart_country_id" name="virtuemart_country_id" value="'.$default_shipping_country.'" />'; 
   if ((in_array('virtuemart_state_id', $custom_rendering_fields)))
   $html .= '<input type="hidden" id="virtuemart_state_id" name="virtuemart_state_id" value="0" />';   
   
  
   
   return $html;
   }
 }
 
 function reorderFields(&$userFields)
 {
 
 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 
 return OPCUserFields::reorderFields($userFields); 


 }
 
 function insertAfter(&$arr, $field, $ins, $newkey, $before=false)
 {
   //deprecated 
 }

function getJavascript(&$ref, $isexpress=false)
 {
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'javascript.php'); 
   return OPCJavascript::getJavascript($ref, $this, $isexpress=false); 
 }   
 
 public static function getUserFields($address_type='BT', &$cart=null)
  {
  
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
				$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
				
				$virtuemart_userinfo_id = 0; 
				$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				$new = false; 
			
	if ($uid != 0)
				{
				$userDetails = $umodel->getUser();
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				}
				else $virtuemart_userinfo_id = 0; 
				$layoutName = 'edit'; 
    $task = JRequest::getVar('task'); 
    $userFields = null;
	$view = JRequest::getVar('view', ''); 
	if ((strpos($task, 'cart') || strpos($task, 'checkout') || ($view=='cart')) && empty($virtuemart_userinfo_id)) {

	    //New Address is filled here with the data of the cart (we are in the cart)
		if (empty($cart))
		{
	    if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	    $cart = VirtueMartCart::getCart();
        }
	    $fieldtype = $address_type . 'address';
		if (method_exists($cart, 'prepareAddressDataInCart'))
	    $cart->prepareAddressDataInCart($address_type, $new);

	    $userFields = $cart->$fieldtype;

	    $task = JRequest::getWord('task', '');
	} else {
		$userFields = $umodel->getUserInfoInUserFields($layoutName, $address_type, $virtuemart_userinfo_id);
		$userFields = $userFields[$virtuemart_userinfo_id];
		$task = 'editaddressST';
	}
	return $userFields;
  }
  
 public static function getCurrency(&$cart)
 {
   static $curr = 0; 
   if (!empty($curr)) return $curr;
	if (!empty($cart))
   $vendorId = $cart->vendorId; 
   else $vendorId = 1; 
   
   $db = JFactory::getDBO();
$q  = 'SELECT `vendor_accepted_currencies`, `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`='.$vendorId;
$db->setQuery($q);
$vendor_currency = $db->loadAssoc();
 $mainframe = Jfactory::getApplication();
$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id', $vendor_currency['vendor_currency']) );
  $curr = $virtuemart_currency_id; 
  return $virtuemart_currency_id; 
 }
 function getContinueLink(&$ref)
 {
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 if (!empty($no_continue_link)) return ""; 
 $cl = ''; 
  $reff = @$_SERVER['HTTP_REFERER']; 
    if (!empty($reff))
	  {
	    
	    $reff = OPCloader::slash($reff); 
		if (stripos($reff, 'script')===false)
		  {
		    
		    $cl = $reff; 
		  }
	  }
      if (empty($cl))
	  {
		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		    $categoryLink = '';
			if ($virtuemart_category_id) {
			  $categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		    }
			
		    $cl = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink);
	  }
	$session = JFactory::getSession();
	if (!empty($cl)) 
	{
	 $cl2 = $session->get('lastcontiuelink', '', 'opc');
	 if (!empty($cl2)) return $cl2; 
	 
	 $session->set('lastcontiuelink', $cl, 'opc');
	 return $cl; 
	}
	$cl = $session->get('lastcontiuelink', '', 'opc');
	return $cl; 
 }
 
 function slash($string, $insingle = true)
 {
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   return OPCmini::slash($string, $insingle); 
 }
 

 function getIntroArticle(&$ref)
 {
    include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
	$add = JRequest::getVar('opc_adc'); 
	
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	
	$op_articleid = OPCconfig::getValue('opc_config', 'op_articleid', 0, 0, true); 
	
	if (!empty($add))
	{
	  $adc_op_articleid = OPCconfig::getValue('opc_config', 'adc_op_articleid', 0, 0, true); 
	  
	  if (!empty($adc_op_articleid)) $op_articleid = $adc_op_articleid; 
	  
	}
	
   if (empty($op_articleid))   
   return "";
   if (!is_numeric($op_articleid)) return "";
   
   if (is_numeric($op_articleid))
    {
	   $article = JTable::getInstance("content");
	   
	   $article->load($op_articleid);
	
	    
		$parametar = new OPCParameter($article->attribs);
		
		
	    $x = $parametar->get('show_title', false); 
		$x2 = $parametar->get('title_show', false); 
		
		$intro = $article->get('introtext'); 
		$full = $article->get("fulltext"); // and/or fulltext
		 JPluginHelper::importPlugin('content'); 
		  $dispatcher = JDispatcher::getInstance(); 
		  $mainframe = JFactory::getApplication(); 
		  $params = $mainframe->getParams('com_content'); 
		  
		 if ($x || $x2)
		 {
		
		

		  $title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
		  
		  }
		  else $title = ''; 
		  if (empty($article->text))
		  $article->text = $title.$intro.$full; 
		  
	      
	     
		  $results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
		  $results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
		  
		  return $article->get('text');
		
		
	}
   return ""; 

 }
 
 function getItalianCheckbox(&$ref)
 {
		include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		
		$ita = $acy = ''; 
		
		if (!empty($opc_acymailing_checkbox))
		$acy = $this->fetch($ref, 'acymailing_checkbox', array(), ''); 
		
		if (!empty($opc_italian_checkbox))
		$ita = $this->fetch($ref, 'italian_checkbox', array(), ''); 
		// default
		return $ita.$acy; 
 }
 
 function getTos(&$ref)
 {
  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
  $link = $this->getTosLink($ref); 
  
  if (!empty($link))  
  if (!empty($tos_scrollable))
  {
   $start = '<iframe src="'.$link.'" class="tos_iframe" >'; 
   $end = '</iframe>'; 
   return $start.$end; 
  }
  
   
    $start = ''; 
    $end = ''; 
   
   if (empty($ref->cart->vendor->vendor_terms_of_service))
   {
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   $vendorModel = OPCmini::getModel('vendor'); 
   $vendor = $vendorModel->getVendor(); 
   $ref->cart->vendor->vendor_terms_of_service = $vendor->vendor_terms_of_service; 
   
   }
   
   
   $tos_config = OPCconfig::getValue('opc_config', 'tos_config', 0, 0, true); 
   

   
   if (empty($tos_config))   
   return $ref->cart->vendor->vendor_terms_of_service;  
   if (!is_numeric($tos_config)) return $start.$ref->cart->vendor->vendor_terms_of_service.$end;  
   
   if (is_numeric($tos_config))
    {
	   $article = JTable::getInstance("content");
	   
	   $article->load($tos_config);
	  
		$intro = $article->get('introtext'); 
		$full = $article->get("fulltext"); // and/or fulltext
		 JPluginHelper::importPlugin('content'); 
		  $dispatcher = JDispatcher::getInstance(); 
		  $mainframe = JFactory::getApplication(); 
		  $params = $mainframe->getParams('com_content'); 
		  
		  $title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
		  if (empty($article->text))
		  $article->text = $title.$intro.$full; 
		  
	      
	     
		  $results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
		  $results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
		  
		  return $start.$article->get('text').$end;
		
		
	}
   return ""; 
 }
 
 function fetch(&$ref, $template, $vars, $new='')
 {
    if (!class_exists('OPCrenderer'))
		require (JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		return $renderer->fetch($ref, $template, $vars, $new); 
 }
 
 function getCoupon(&$obj)
 {
   if (!VmConfig::get('coupons_enable')) 
   {
    return ""; 
   }
   $this->couponCode = (isset($this->cart->couponCode) ? $this->cart->couponCode : '');
   $coupon_text = $obj->cart->couponCode ? OPCLang::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : OPCLang::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
   
   
    if (!class_exists('OPCrenderer'))
    require (JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'renderer.php'); 
    $renderer = OPCrenderer::getInstance(); 
    $renderer->assignRef('coupon_text', $coupon_text);
   return $this->fetch($obj, 'couponField.tpl', array(), 'coupon'); 
   
 }
 
 public function getJSValidator($ref)
	{
	  $html = 'javascript:return Onepage.validateFormOnePage(event, this, true);" autocomplete="off'; 
	  //$html = '" autocomplete="off"'; 
	  return $html;
	}
 function renderOPC()
  {
    
  }
   	function op_image_info_array($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0)
	{ 
	 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
	 return OPCimage::op_image_tag($image, $args, $resize, $path_appendix, $thumb_width, $thumb_height, true );
	}
	function path2url($path)
	{
	 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
	 return OPCimage::path2url($path); 
	}
	function op_image_tag($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0, $retA = false )
	{
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
		return OPCimage::op_image_tag($image, $args, $resize, $path_appendix, $thumb_width, $thumb_height, $retA );
	}
	public function resizeImg($orig, $new,  $new_width, $new_height, $ow, $oh)
	{
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
	return OPCimage::resizeImg($orig, $new,  $new_width, $new_height, $ow, $oh); 
	}
 	public function op_show_image(&$image, $extra, $width, $height, $type)
	{
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'image.php'); 
	  return OPCimage::op_show_image($image, $extra, $width, $height, $type);
	}
	
	

	
	// for backward compatibility


  
	static function tableExists($table)
{
 $db = JFactory::getDBO();
 $prefix = $db->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   if (!empty($r)) return true;
 return false;
}

/**
	 * Check if a minimum purchase value for this order has been set, and if so, if the current
	 * value is equal or hight than that value.
	 * @author Oscar van Eijk
	 * @return An error message when a minimum value was set that was not eached, null otherwise
	 */
	public static function checkPurchaseValue($cart) {
		$s = $cart->virtuemart_shipmentmethod_id; 
		$p = $cart->virtuemart_paymentmethod_id; 
		
		$cart->virtuemart_shipmentmethod_id = 0; 
		$cart->virtuemart_paymentmethod_id = 0; 
		
		$ret = ''; 
	    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
		$vendor = OPCmini::getModel('vendor');
		if (empty($vendor)) return; 
		$vendor->setId($cart->vendorId);
		$store = $vendor->getVendor();
		if ($store->vendor_min_pov > 0) {
		    $vm2015 = true; 
			$prices = OPCloader::getCheckoutPrices($cart, false, $vm2015, null);
			
			if (!empty($prices['couponValue']) || (!empty($prices['salesPriceCoupon'])))
			$ret = ''; 
			else
			if ($prices['salesPrice'] < $store->vendor_min_pov) {
				if (!class_exists('CurrencyDisplay'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$ret = JText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
			}
		}
		
		$cart->virtuemart_shipmentmethod_id = $s; 
		$cart->virtuemart_paymentmethod_id = $p; 
		
		return $ret;
	}



	public static function fetchUrl($url, $XPost='')
	{
	
	 if (!function_exists('curl_init'))
	 {
	  return file_get_contents($url); 
	 
	 }
		
	 $ch = curl_init(); 
	 
//	 curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	 curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
	 curl_setopt($ch, CURLOPT_TIMEOUT, 4000); // times out after 4s
     curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost); // add POST fields
     if (!empty($XPost))
	 curl_setopt($ch, CURLOPT_POST, 1); 
	 else
	 curl_setopt($ch, CURLOPT_POST, 0); 
     curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	 $result = curl_exec($ch);   
	
    
    
    if ( curl_errno($ch) ) {      
	    
	    OPCloader::opcDebug('ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch));
		@curl_close($ch);
		return false; 
    } else {
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		OPCloader::opcDebug($url.' -> '.$returnCode);
        switch($returnCode){
            case 404:
			    @curl_close($ch);
                return false; 
                break;
            case 200:
        	break;
            default:
				 @curl_close($ch);
            	return false; 
                break;
        }
    }
    
    @curl_close($ch);
    
  
    return $result;   
    
    

	}
	


}
