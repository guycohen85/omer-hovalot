<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
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
* NOTE: It's not compatible with ArtioSEF to call JRoute from onAfterRoute
*/

class OPCplugin {
 
 public static function detectMobile()
 {
        if (defined('OPC_DETECTED_DEVICE'))
		if (OPC_DETECTED_DEVICE != 'DESKTOP') return true; 
		else return false; 
		
        $isMobile = false;
    	
    	if(!class_exists('uagent_info')){
    		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'mdetect.php');
    	}
		
    	$ua = new uagent_info();
    	
    	if($ua->DetectMobileQuick()){
		    
    		define('OPC_DETECTED_DEVICE', 'MOBILE');
    		$isMobile = true;
    	}
    	if ($ua->DetectTierTablet() ){
    		define('OPC_DETECTED_DEVICE', 'TABLET');
    		$isMobile = true;
    	}
    	
    	if($isMobile == false){
    		define('OPC_DETECTED_DEVICE', 'DESKTOP');
    	}
    	
    	
    	    	
    	return $isMobile;
	
 }
 
 public static function checkGiftCoupon(&$order, $last_state)
 {
    include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	
	if (!empty($do_not_allow_gift_deletion))
	if (!empty($gift_order_statuses))
	 {
	 
	 
		 if (is_object($order))
		 {
		 
		   if (!isset($order->order_status)) return; 
		   $status = $order->order_status; 
		   if (in_array($status, $gift_order_statuses))
		     {
			    $coupon_code = $order->coupon_code; 
				$value = abs($order->coupon_discount);
				$value = (double)str_replace(',', '.', $value); 
				if (!empty($coupon_code))
				  {
				     $db=JFactory::getDBO(); 
				     $q = "delete from `#__virtuemart_coupons` where `coupon_code` = '".$db->getEscaped($coupon_code)."' and `coupon_type` = 'gift' limit 1"; 
					 $db->setQuery($q); 
					 $db->query(); 
					 
				     /*
				     $db=JFactory::getDBO(); 
				     $q = "select * from `#__virtuemart_coupons` where `coupon_code` = '".$db->getEscaped($coupon_code)."' and `coupon_type` = 'gift' limit 0,1"; 
					 $db->setQuery($q); 
					 $res = $db->loadAssoc(); 
					 if (empty($res))
					  {
					  //
					    $q = 'INSERT INTO `rg6ma_virtuemart_coupons` (`virtuemart_coupon_id`, `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value`, `coupon_start_date`, `coupon_expiry_date`, `coupon_value_valid`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES '; 
						$q .= "(NULL, '".$db->getEscaped($coupon_code)."', 'total', 'gift', '".$value."', '0000-00-00 00:00:00', '2050-01-01 00:00:00', 0.00000, 1, NOW(), 42, '2013-06-10 12:50:23', 0, '0000-00-00 00:00:00', 0)"; 
					  }
					  */
					 
				  }
			 }
		 }
	 }
	
	return null; 
 }
 
 public static function alterActivation()
 {
	//index.php?option=com_users&task=registration.activate&token=64e7109fe98d1f9988f9e6560f9b644a
   include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	
	$user = JFactory::getUser(); 
	$uid = $user->get('id');
	$task = JRequest::getWord('task'); 
	$option = JRequest::getWord('option'); 
	
   	  //if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	if ((($task == 'registration.activate') || ($task == 'registrationactivate')) && ($option == 'com_users'))
	  {
		  
	   if (!empty($opc_do_not_alter_registration)) return true;   
	  //if (!empty($uid))
	   {
		   
		   $blocked = $user->get('block');
		   $uParams	= JComponentHelper::getParams('com_users');
		   $useractivation = $uParams->get('useractivation');
		   //if (!empty($blocked))
		   if ($useractivation == 1)
		   {
			   
			// if user is already logged in with a blocked account we need to enable the activation here   
			$db		= JFactory::getDBO(); 
			$token = JRequest::getVar('token', null, 'request', 'alnum');
			if (empty($token)) return true; 
			// Get the user id based on the token.
			$db->setQuery(
			'SELECT '.$db->quoteName('id').' FROM '.$db->quoteName('#__users') .
			' WHERE '.$db->quoteName('activation').' = '.$db->Quote($token) .
			' AND '.$db->quoteName('block').' = 1 limit 1'
			);
			$userId = (int) $db->loadResult();
			if ((!empty($userId)) && ($userId > 0))
			{
				
				/*
			$q = 'update #__users set (block=0, activation='') where id = '.$userId.' limit 1'; 
			$db->setQuery($q); 
			$db->query(); 
			   */
				$user = JFactory::getUser($userId);
				$user->set('activation', '');
				$user->set('block', '0');
				if ($user->save())
				{
					
					   $jlang =& JFactory::getLanguage(); 
   	 

				$jlang->load('com_users', JPATH_SITE, 'en-GB', true); 
				$jlang->load('com_users', JPATH_SITE, $jlang->getDefault(), true); 
				$jlang->load('com_users', JPATH_SITE, null, true); 

				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'), 'notice');
				$msg = JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'); 
				//$link = JRoute::_('index.php?option=com_users&view=login'); 
				
				JRequest::setVar('task', null); 
				JRequest::setVar('layout', null); 
				JRequest::setVar('option', 'com_users'); 
				JRequest::setVar('view', 'login'); 
				
				//$mainframe->redirect($link, $msg, 'notice'); 
				//$mainframe->close(); 
				
				
				
				return true; 
				}
				else
				{
					
				}
				//$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			}
			/*
			ob_start(); 
					$options = array('silent' => true, 'skip_joomdlehooks'=>true );
					$mainframe = JFactory::getApplication(); 
					$mainframe->logout($uid, $options); 
					ob_get_clean(); 
			*/
		   }
	   }
	  
	  return true;
	  }
	  
	// proceed further
	return false; 
 }
 
 public static function loadShoppergroups()
 {
     $option = JRequest::getCmd('option');   
	  $task = JRequest::getWord('task'); 
	  $view = JRequest::getWord('view');

   if (($option == 'com_onepage') && (($task=='opc') || ($task=='checkout')))
	 {
   if (!class_exists('VmConfig'))
   require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	   
        if (!class_exists ('VirtueMartCart')) {
            require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
        }
		
        $cart = VirtueMartCart::getCart(false);
	    $euvatid = JRequest::getVar('eu_vat_id', ''); 
		if (is_array($cart->BT))
		if (isset($cart->BT['eu_vat_id']))
		if ($cart->BT['eu_vat_id'] != $euvatid)
		{
		$cart->BT['eu_vat_id'] = ''; 
		$cart->setCartIntoSession(); 
		
		
		}
    }
 
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	  OPCloader::getSetShopperGroup(true); 
 }
 
 public static function getContinueLink()
 {
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
    $format = JRequest::getVar('format', 'html'); 
	  
	  $option = JRequest::getCmd('option'); 
	   $session = JFactory::getSession();
	    $task = JRequest::getWord('task'); 
		  $view = JRequest::getWord('view');
	    if ($option == 'com_k2')
	  {
	   
		  if ($view == 'item')
		  {
		
		 $id = JRequest::getVar('id'); 
		 $itemid = JRequest::getInt('Itemid', 0); 
		 $lang = JRequest::getWord('lang'); 
		 $url = 'index.php?option=com_k2&view='.$view.'&id='.$id;
		 
		 if (!empty($itemid))
		 $url .= '&Itemid='.$itemid; 
         //$u = JRoute::_($url);
		 
	     $session->set('lastcontiuelink', $url, 'opc');
		 }
	  }
	  
	  if(('com_virtuemart' == $option))
	  {
	     $session = JFactory::getSession();
		 
	  if (($view == 'productdetails') && (empty($task)))
	   {
	    // /index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=1&virtuemart_category_id=1
		 $id = JRequest::getInt('virtuemart_product_id', 0); 
		 if (!empty($id))
		 {
		 
		 if (!empty($opc_only_parent_links))
		 {
		  $db = JFactory::getDBO(); 
		  $q = 'select product_parent_id from #__virtuemart_products where virtuemart_product_id = '.(int)$id.' limit 0,1'; 
		  $db->setQuery($q); 
		  $virtuemart_parent_id = $db->loadResult(); 
		  
		  if (!empty($virtuemart_parent_id)) $id = $virtuemart_parent_id; 
		 }
		  
		 $cid = JRequest::getInt('virtuemart_category_id', 0); 
		 $url = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$id; 
		
		 if (!empty($cid))
		 $url .= '&virtuemart_category_id'.$cid; 
		 
		 $itemid = JRequest::getInt('Itemid', 0); 
		 if (!empty($itemid))
		 $url .= '&Itemid='.$itemid; 
		 
		 //$u = JRoute::_($url);
		 
	     $session->set('lastcontiuelink', $url, 'opc');
		
		}
	   }
	  }
 }
 
 public static function checkOPCtask()
 {
	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		$view = JRequest::getWord('view');
		  
		 $task = JRequest::getWord('task'); 
		 //$session = JFactory::getSession(); 
		 
	  // site refresh, logout, hacking attempt protection
	  if (($view == 'opc') && ($task!='opc') && ($task!='checkout') && (($task!='tracker')))
	  {
	  
	  	 JRequest::setVar('controller', 'virtuemart'); 
		 JRequest::setVar('view', 'virtuemart'); 
		 JRequest::setVar('layout', 'default'); 
		 JRequest::setVar('format', 'html'); 

		 // we can safely remove all variables in session: 
		 /*
		 if (($task!='tracker') && ($task != 'opc'))
		 {
		  $session = JFactory::getSession();
          $session->clear('opcuniq');
	      $session->clear($rand2); 
		 }
		 */
		 return false; 
	  }
	   if ($task == 'edit') return false; 
	  // continue
	  // only load rest for virtuemart and onepage
	  $option = JRequest::getVar('option'); 
	  if(('com_virtuemart' == $option) || ('com_onepage' == $option)) return true; 
	  else return false; 
	  
	  return true; 
 }
 // not used any more
 public static function checkOPCunique()
 {
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
 /*
	  $ot1 = JRequest::getVar('opcuniq', ''); 
	  if (!empty($ot1))
	   {
	     $rand2 = $session->get('opcuniq', '');
	   }
      $rand2 = $session->get('opcuniq', '');
	  $finished = (int)$session->get($rand2, 0); 
	  
	  
	  if (($view == 'opc') && (($task == 'checkout') || ($task=='')))
	  if ($finished > 0)
	   {
	     // we alredy had the checkout here and we need to disable double payment and redirect the user to some other page
		 // will redirect the page to the frontpage of VM
		 
		 JRequest::setVar('controller', 'virtuemart'); 
		 JRequest::setVar('view', ''); 
		 JRequest::setVar('layout', 'default'); 
		 JRequest::setVar('format', 'html'); 
		 
		 return; 
	   }
	  */
 }
 public static function isOPCcheckoutEnabled()
 {
     include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	  $set = array('com_virtuemart', 'com_user', 'com_users', 'com_onepage'); 
	  $set2 = array('com_user', 'com_users'); 
	  $option = JRequest::getVar('option'); 
	  
	  
	  
	  if ((in_array($option, $set) && (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))))
	  include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	  else return false; 
	  
	  if (!empty($opc_disable_for_mobiles))
		{
			/* OLD CODE: 
			$usera = @$_SERVER['HTTP_USER_AGENT']; 
			$isMobile = preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry'.
                    '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $usera );
			*/
			$isMobile = self::detectMobile(); 
			
			if (!empty($isMobile)) return false; 
		}
	  $task = JRequest::getCMD('task');
	   $view = JRequest::getVar('view'); 

	  if (!empty($disable_onepage)) return false;
	  
	   
	   if (stripos($task, 'reset')!==false) 
	   {
	   return false; 
	   }
	   if (stripos($task, 'login')!==false) 
	   {
	   return false; 
	   }
	   if (stripos($task, 'remind')!==false) 
	   {
	   return false; 
	   }
	    
	   if (stripos($view, 'reset')!==false) 
	   {
	   return false; 
	   }
	   if (stripos($view, 'login')!==false) 
	   {
	   return false; 
	   }
	   if (stripos($view, 'remind')!==false) 
	   {
	   return false; 
	   }
	  return true; 
 }
 
 public static function getCache()
 {
	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	if (!empty($opc_calc_cache))
	  if (!class_exists('calculationHelper'))
	  if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'calculationh_patched.php'))
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'calculationh_patched.php'); 
	  
	  
	  
 }
 
 public static function checkLoad()
 {
 
   $app = JFactory::getApplication();
   // if we are not at FE, do not alter anything
		if ($app->getName() != 'site') {
			return false;
		}
 
 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 

 
   // if (!class_exists('VmConfig'))
//	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	  
	  
	
		
	if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php')) return false;

		  // test format: 
	  $format = JRequest::getVar('format', 'html'); 
	  $option = JRequest::getCmd('option'); 
	  
	  $tmpl = JRequest::getVar('tmpl', ''); 

	  
	  if (($tmpl == 'component') && ($format != 'opchtml')) return false; 
	  
	  // speed up json requests 

	  $okformat = array('opchtml', 'html'); 
	  if (!in_array($format, $okformat)) return false; 
	  
	  $doc = JFactory::getDocument(); 
	  $class = get_class($doc); 
	  $class = strtolower($class); 
	 
	  $format = str_replace('jdocument', '', $class); 
	  if (!in_array($format, $okformat)) return; 
	  
	if ($app->isAdmin()) return false; 
	
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	
	
	// load basic stuff:
	OPCLang::loadLang(); 
	if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
	
	 if (!defined('JPATH_OPC'))
	  define('JPATH_OPC', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'); 
	
	 
	  //VmConfig::loadConfig(); 
	  
	 
   
   return true; 
 }
 public static function alterRegistration()
 {
    include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	$user = JFactory::getUser(); 

	
	$set2 = array('com_user', 'com_users'); 
	
	$user = JFactory::getUser(); 
	$uid = $user->get('id');
	$task = JRequest::getWord('task'); 
	$option = JRequest::getWord('option'); 
	$view = JRequest::getWord('view'); 
	$layout = JRequest::getVar('layout', ''); 
	
	  if ($uid <= 0)
	  {
	 
	  if (($view == 'profile') && ($task == 'saveUser'))
	  {
	   //return; 
	  JRequest::setVar('option', 'com_virtuemart'); 
	  JRequest::setVar('view', 'user'); 
	  }
	  
	  if (!empty($op_redirect_joomla_to_vm))
	   {
	   
	        if (in_array($option, $set2))
			{
	        
			
			 // allowed tasks
			 
			 
			 $set3 = array('user.logout', 'logout', 'user.login', 'login', 'reset', 'remind'); 
			 if ((in_array($task, $set3)) || (in_array($view, $set3)))
			 {
			 
			 return true; 
			 }
			 
			 // do not redirect, but show the proper page: 
			 JRequest::setVar('option', 'com_virtuemart'); 
			 JRequest::setVar('view', 'user'); 
			 JRequest::setVar('task', 'display'); 
			 JRequest::setVar('layout', 'edit'); 
			  JRequest::setVar('controller', 'user'); 
			 
			}
	   }
	  }
	  
	  // dont' proceed OPC when user is logged and is editing the address: 
	  if ($option == 'com_virtuemart')
	  if ($uid>0)
	   {
	     // virtuemart_user_id[]=51&virtuemart_userinfo_id=18
		 $virtuemart_user_id = JRequest::getVar('virtuemart_user_id', ''); 
		 $virtuemart_userinfo_id = JRequest::getVar('virtuemart_userinfo_id', ''); 
		 //http://vm2onj25.rupostel.com/index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT&virtuemart_userinfo_id=11&cid[]=51
		 if (($view=='user') && ($task == 'editaddresscart') && (!empty($virtuemart_userinfo_id))) return true;
	     if (($view=='user') && ($task == 'editaddressST') && (!empty($virtuemart_user_id)) && (!empty($virtuemart_userinfo_id)))
		  {
		 
		  return true;
		  }
	   }
	  
	  
	 
	  if (($view == 'user') && ($layout == 'edit'))
	  {
	   return true;
	  }
	  return false; 
 }
 
 public static function loadOPCcartView()
 {
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	$task = JRequest::getWord('task'); 
	$option = JRequest::getWord('option'); 
	$view = JRequest::getWord('view'); 
	$layout = JRequest::getVar('layout', ''); 
	$controller = JRequest::getWord('controller', JRequest::getWord('view', 'virtuemart'));
	
   	  if  ($view == 'cart2')
	   {
	     $view = 'opc'; 
		 
		 $_POST['view'] = 'opc'; 
		 $_GET['view'] = 'opc'; 
		 $_REQUEST['view'] = 'opc';
		 $controller = 'opc';
		JRequest::setVar('view', 'opc'); 
		JRequest::setVar('task', 'cart'); 
	   }
	  
	  
	  if ((($view == 'cart') || ($view == 'opc') ) || (($view=='user') && ($task=='editaddresscheckout')) || ($task == 'pluginUserPaymentCancel'))
		{
		
             // require_once(dirname(__FILE__) . DS . 'loader.php');
			  //overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
			  if (!class_exists('VirtueMartViewCart'))
			  {
			    
				
				if (!empty($opc_memory))
				ini_set('memory_limit',$opc_memory);
				else
			    ini_set('memory_limit','128M');
				// we must disable chosen as it causes lot's of troubles: 
				
                require_once(JPATH_OPC.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'virtuemart.cart.view.html.php'); 
			  }
			  else
			  {
			     // opc will not load because some other extension is using cart view override
				 return false; 
			  }
			  
			 if ($view == 'user')
		     {
			 if (!class_exists('VirtueMartViewUser'))
			 require(JPATH_OPC.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'virtuemart.user.view.html.php'); 
			  JRequest::setVar('layout', 'default'); 
			  JRequest::setVar('view', 'cart'); 
			 }
			 if (!class_exists('vmPlugin'))
			 {
			 
			 require(JPATH_OPC.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
			 }
			 else
			  {
			    
			    return false; 
			  }
			 //include_once(JPATH_OPC.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cart.php'); 
			 
			 
		}
		else return false;
		
		if ($controller =='opc')
	    {
		
	     if (strpos($controller, '..')!==false) die('?'); 
	     require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
		 
		 // fix 206 bug here:
		 /*
		 if (!class_exists('VmFilter'))
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmfilter.php'); 
		 */
		 
	    }
		
		// proceed
		return true; 
			 

 }
 // this function fixes vm206 bug on adding a new address
 // it also fixes 'name' field when it not generated within VM for Joomla
 public static function fixVMbugNewShippingAddress()
 {
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
 $task = JRequest::getWord('task'); 
	$option = JRequest::getWord('option'); 
	$view = JRequest::getWord('view'); 
	$layout = JRequest::getVar('layout', ''); 
  // We need to fix a VM206 bugs when a new shipping address cannot be added, savecartuser
		if( ('user'==$view && (('savecartuser' == $task) || ('editaddresscart' == $task)) ))
		{
		 

		   if ('ST' == JRequest::getCMD('address_type'))
		   {
		     if (!isset($_POST['shipto_virtuemart_userinfo_id']))
			  {
			    $_POST['shipto_virtuemart_userinfo_id'] = '0'; 
				JRequest::setVar('shipto_virtuemart_userinfo_id', 0); 
	
			  }
		    
		   }
		   if ('BT' == JRequest::getCMD('address_type'))
		   {
		     if (isset($_POST['shipto_virtuemart_userinfo_id']))
			  {
			    JRequest::setVar('shipto_virtuemart_userinfo_id', null); 
			    unset($_POST['shipto_virtuemart_userinfo_id']); 
				
			  }
		   }
		   
		   
		   // this fixes vm206 bug: Please enter your name. after changing BT address
		   if ('BT' == JRequest::getCMD('address_type'))
		   {
		     $user = JFactory::getUser();
			 
			 
			 //$x = JRequest::getVar('name'); 
		     if (!isset($_POST['name']))
			  {
			    if (!empty($user->name)) 
				{
				  $_POST['name'] = $user->name; 
				  JRequest::setVar('name', $_POST['name']); 
				}
				else
				{
			     $_POST['name'] = $user->get('first_name', '').' '.$user->get('middle_name', '').' '.$user->get('last_name', ''); 
				 JRequest::setVar('name', $_POST['name']); 
				}
				
			  }
			 
		   }
		   
		 }
 }
 public static function enableSilentRegistration()
 {
    	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	 // let's enable silent registration when show login is disabled, but only registered users can checkout: 
		 $t1 = JRequest::getCmd('controller', '', 'post'); 
		 $t2 = JRequest::getCmd('view', 'user', 'post'); 
		 $t3 = JRequest::getCmd('address_type', '', 'post'); 
		 $t32 = JRequest::getWord('addrtype','');
		 $t4 = JRequest::getCmd('task', 'saveUser', 'post'); 
		 $t4 = strtolower($t4); 
		 
		 if (($t1 == 'user') && ($t2 == 'user') && (($t3 == 'BT') || ($t32=='BT')) && ($t4=='saveuser'))
		  {
		 
		    $t5 = JRequest::getVar('username'); 
			if (empty($t5))
			 {
			    $email = JRequest::getVar('email'); 
				if (!empty($email))
				 {
				   JRequest::setVar('username', $email); 
				 }
				// address name: 
				$name = JRequest::getVar('name'); 
				if (empty($name))
				 {
				    $firstname = JRequest::getVar('first_name', 'default'); 
					$lastname = JRequest::getVar('last_name', ' address'); 
					JRequest::setVar('name', $firstname.' '.$lastname); 
				 }
				 
			 }
			 JRequest::setVar('task', 'saveUser'); 
			 
	 
			 
			 
			
			 
		   
		  }
 }
 
 public static function loadOpcForLoggedUser()
 {
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
 		  $task = JRequest::getWord('task'); 
	$option = JRequest::getWord('option'); 
	$view = JRequest::getWord('view'); 
	$layout = JRequest::getVar('layout', ''); 
	if( ('user'==$view && (('savecartuser' == $task) || (strpos($task, 'editadd')!==false ))) )
	{
	//if (!defined('JPATH_COMPONENT')) define('JPATH_COMPONENT', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'); 
	
	
	if ($view != 'opc')
	$config = array ( "base_path"=> JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart',  "layout"=>  "default" );
	else $config = array ( "base_path"=> JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage',  "layout"=>  "default" );
	
	$OPCloader = new OPCloader($config); 
	
	  
	
	if (!class_exists('VirtueMartCart'))
	   require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	$cart =& VirtueMartCart::getCart();
	
	if ($view != 'opc')
	if (!$OPCloader->logged($cart))
	 {
	      // we will load OPC for all edit address links for unlogged
		  JRequest::setVar('view', 'cart'); 
	
	 }
	}
 }
 
 public static function updateAmericanTax()
 {
  
     // this part disables taxes for US mode an all pages unless a proper state is selected
	 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	 
	  $view = JRequest::getVar('view'); 
	  if ($view != 'cart' && ($view != 'opc'))
	  if (!empty($opc_usmode)) 
	  {
	   if (!class_exists('VirtueMartCart'))
	   require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	   $cart =& VirtueMartCart::getCart();
	   
	  
	   
	   if (empty($cart->ST) && (!empty($cart->BT)))
	   {
	    if (empty($cart->BT['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ' '; 
		//$GLOBALS['st_opc_state_empty'] = true; 
		$GLOBALS['opc_state_empty'] = true; 
	   }
	   else
	   if (empty($cart->ST) && (empty($cart->BT)))
	   {
	   $cart->BT = array(); 
	   $cart->BT['virtuemart_state_id'] = ' '; 
	   $GLOBALS['opc_state_empty'] = true; 
	   }
	   if (!empty($cart->ST))
	   {
	     if (empty($cart->ST['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ' '; 
		 $GLOBALS['st_opc_state_empty'] = true; 	
	   }
	   
	  }
 }
 
 public static function updateJoomlaCredentials()
 {
   // next few lines update user's access rights for each view of the page
	  // there is a bug in joomla 1.7 to joomla 2.5.x which does not update the cached authLevels variable of the user in some cases (right after registration)
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
	  $instance = JFactory::getSession()->get('user');
	  if ((!empty($instance->id) && (empty($instance->opc_checked))))
	  {
	   $u = new JUser((int) $instance->id);
	   $u->opc_checked = true; 
	   JFactory::getSession()->set('user', $u); 
	  }
	  }
 }
 public static function setItemid()
 {
  $view = JRequest::getWord('view'); 
  if( (('cart'==$view || (('opc'==$view)))))
	 {
	 //include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
     
	 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	  $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
	 
	 if (!empty($newitemid))
	 {
	   $GLOBALS['Itemid'] = $newitemid; 
	   $_REQUEST['Itemid'] = $newitemid; 
	   $_POST['Itemid'] = $newitemid; 
	   $_GET['Itemid'] = $newitemid; 
	   JRequest::setVar('Itemid', $newitemid); 
	 }
	 }
    
   
	
 }
 
 public static function fixVMbugVirtuemartUser()
 {
   $task = JRequest::getWord('task'); 
	$option = JRequest::getWord('option'); 
	$view = JRequest::getWord('view'); 
	$layout = JRequest::getVar('layout', ''); 
	if (($view == 'user') && empty($task) && ($layout=='default'))
	 {
	   JRequest::setVar('default', null); 
	   unset($_REQUEST['layout']); 
	   
	 }
 }
 public static function keyCaptchaSupport()
 {
 
     $option = JRequest::getVar('option'); 
     if(('com_virtuemart' == $option) || ('com_onepage' == $option)) {
	 
	  $controller = JRequest::getWord('controller', JRequest::getWord('view', 'virtuemart'));
	  $view = JRequest::getWord('view', 'virtuemart'); 
	  $task = JRequest::getCMD('task');
	
	
	
	  ///index.php?option=com_virtuemart&view=opc&controller=opc&task=checkout
	  if (($view == 'opc') && ($task == 'checkout'))
	   {
	     // disable key captcha: 
		 $first_name = JRequest::getVar('first_name', ''); 
		 JRequest::setVar('opc_first_name', $first_name); 
		 JRequest::setVar('first_name', null); 
		 unset($_POST['first_name']); 
		 unset($_GET['first_name']); 
		 unset($_REQUEST['first_name']); 
		
	   }
	  
	 
  }
 }
}
