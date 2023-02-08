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

class OPCShopperGroups {
  public static function setShopperGroups($id, $remove=array())
  {
 
    include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
    $user = JFactory::getUser(); 
	if (empty($allow_sg_update_logged))
	if (($user->id != 0) && (empty($user->guest))) 
	return $id; 
	
    if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
		else return 1;
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 
  
	OPCloader::opcDebug('OPC: setShopperGroup: '.$id);  
    
	$arr = array(1, 2); 
	
	if (!empty($id) && ($id>0) && (!in_array($id, $arr)))
	{
	 //remove default and anonymous
	 $remove[] =1; 
	 $remove[] =2; 
	}
    
	
	if (!empty($id))
	{
	 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	 $shoppergroupmodel = OPCmini::getModel('ShopperGroup'); //new VirtueMartModelShopperGroup(); 
	 if (method_exists($shoppergroupmodel, 'removeSessionSgrps'))
	 if (method_exists($shoppergroupmodel, 'appendShopperGroups'))
	 {
		
	
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 
	 
	 
	 
	 //$shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = $shoppergroup_ids;
	 foreach ($remove as $rid)
	 foreach ($new_shoppergroups as $key=>$val)
	  {
	    if ($rid == $val)
		unset($new_shoppergroups[$key]); 
	  }
	  
	  $session->set('vm_shoppergroups_remove', $remove, 'vm');
	  
	 
	 if ($id > 0)
	 if (!in_array($id, $shoppergroup_ids))
	 {
	   $new_shoppergroups[] = $id;  
	   JRequest::setVar('virtuemart_shoppergroup_id', $id, 'post');
	 }
	 $session = JFactory::getSession(); 
	 $shoppergroup_ids = $session->set('vm_shoppergroups_add',$new_shoppergroups,'vm');
	 $user = JFactory::getUser(); 
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, $user); 
	 
	
	 
	OPCloader::opcDebug('OPC: setShopperGroup changed: '.$id); 
	  if ($id > 0)
	 return $id; 
	}
	}
	static $default_id; 
	if (!empty($default_id)) return $default_id; 
	// else 
	// this is a VM default group:
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	$cart = VirtueMartCart::getCart();
	if (empty($cart->vendorId))
	$vid = 1; 
	else
	$vid = (int)$cart->vendorId;
	
	$user = JFactory::getDBO(); 
	$sid = $user->get('id'); 
	if (empty($id))
	{
	if (empty($sid) || ($user->guest))
	 {
	   //anonymous: 
	    $db = JFactory::getDBO(); 
		$q = "select virtuemart_shoppergroup_id from #__virtuemart_shoppergroups where default = '2' virtuemart_vendor_id = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	   
	 }
	 else
	 {
		$db = JFactory::getDBO(); 
		$q = "select virtuemart_shoppergroup_id from #__virtuemart_shoppergroups where default = '1' virtuemart_vendor_id = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	 
	 }
	}
	
	return $id;
    
  }
  
  public static function getDefault($cart)
  {
    if (!empty($cart) && (isset($cart->vendorId))) $vid = $cart->vendorId; 
	else $vid = 1; 

   
    $user = JFactory::getDBO(); 
	$sid = $user->get('id'); 
	
	{
	if (empty($sid) || ($user->guest))
	 {
	   //anonymous: 
	    $db = JFactory::getDBO(); 
		$q = "select virtuemart_shoppergroup_id from #__virtuemart_shoppergroups where default = '2' virtuemart_vendor_id = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	   
	 }
	 else
	 {
		$db = JFactory::getDBO(); 
		$q = "select virtuemart_shoppergroup_id from #__virtuemart_shoppergroups where default = '1' virtuemart_vendor_id = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	 
	 }
	}
	return $default_id; 
  }
  
  public static function getAllDefault($cart)
  {
  
	if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
		else return;
	 }
	if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 
    if (!empty($cart) && (isset($cart->vendorId))) $vid = $cart->vendorId; 
	else $vid = 1; 
	
    $db = JFactory::getDBO(); 
	$q = "select virtuemart_shoppergroup_id from #__virtuemart_shoppergroups where default = '1' or default = '2' virtuemart_vendor_id = ".$vid." limit 1"; 
	$db->setQuery($q); 
	$ids = $db->loadAssocList(); 
	if (empty($ids)) $ids = array(1,2); 
	return $ids; 
  }
  
  public static function removeShopperGroups($arr)
 {
   return; 
   if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
	  else return 1;
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 
   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   $shoppergroupmodel = OPCmini::getModel('ShopperGroup'); //new VirtueMartModelShopperGroup(); 
   
   
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 /*
	 foreach ($arr as $id)
	  {
	     if (in_array($id, $shoppergroup_ids))
		  {
		     
		  }
	  }
	 */
   
 }
 
 public static function getSetShopperGroup($debug=false) 
 {
         $user = JFactory::getUser(); 
	    
	   $uid = (int)$user->get('id'); 
	   
	  if ($uid > 0) return; 
	 
	$session = JFactory::getSession();
   
   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
   
   
   if (empty($option_sgroup)) return;
   // language shopper group
   //$lang_shopper_group['en-GB'] = '4'; 
	  if (!empty($option_sgroup) && ($option_sgroup===1))
	  {
	  $lang = JFactory::getLanguage();
	  $tag = $lang->getTag();
	 
	  
	  if (!empty($tag))
	  if (!empty($lang_shopper_group))
	  if (!empty($lang_shopper_group[$tag]))
	   {
	    // end of lang shopper group
		
		return OPCShopperGroups::setShopperGroups($lang_shopper_group[$tag]); 
		
	
		
	   }
	 }
	 else
	 if ($option_sgroup == 2)
	 {
	 
	  // geo ip based shopper group: 
	  $ip_vm_country = $session->get('opc_ip_country', 0); 
	  $ip_sg = $session->get('opc_ip_sg');
	  if (!empty($ip_sg))
	    {
		  // ip sg was already set
		  //return OPCloader::setShopperGroup($lang_shopper_group_ip[$ip_vm_country]); 
		}
		
		
	  if (empty($ip_vm_country))
	  {
	  if (file_exists(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"))
	 {
	  require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		
		 if (!empty($country_2_code))
		 {
		 $country_2_code = strtolower($country_2_code); 
		 $db=JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = '".$country_2_code."' limit 1 "); 
		 $r = $db->loadResult(); 
		 //$e = $db->getErrorMsg(); echo $e;
		 if (!empty($r)) 
		 $ip_vm_country = $r; 
		 
		 
		 }
	     
	   }
	  }
	  }
	  
	  
	   if (!empty($lang_shopper_group_ip[$ip_vm_country]))
	   {
	   $id = OPCShopperGroups::setShopperGroups($lang_shopper_group_ip[$ip_vm_country]); 
	   
	   
	    $session->set('opc_ip_country', $ip_vm_country); 
	    $session->set('opc_ip_sg', $id); 
		return $id; 
	   }
	 
	

	 }
	 

	 
	  // we should set default here
	  $a = $session->get('vm_shoppergroups_add', null, 'vm'); 
	  
	  if (!empty($a))
	  $session->set('vm_shoppergroups_add', array(),'vm');
  

  
	  return;
	 
 }
 
 	public static function setShopperGroupsController($cart=null)
	{
	
	
	  // we need to alter shopper group for business when set to: 
	     $is_business = JRequest::getVar('opc_is_business', 0); 
		
	  
	  $remove = array(); 
     
	  //require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 		
	  OPCShopperGroups::getSetShopperGroup(); 
	
	if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
		else return;
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 
	
	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	
     if (!empty($business_shopper_group) || (!empty($visitor_shopper_group)))
     {	 
     
	
	 if (class_exists('VirtueMartModelShopperGroup')) 
	 {
	 $shoppergroupmodel = new VirtueMartModelShopperGroup(); 
	 if (method_exists($shoppergroupmodel, 'removeSessionSgrps'))
	 if (method_exists($shoppergroupmodel, 'appendShopperGroups'))
	 {
		 if (!empty($is_business))
		  {
		  
		 
		    // we will differenciate between default and anonymous shopper group
			// default is used for non-logged users
			// anononymous is used for logged in users as guests
     OPCShopperGroups::setShopperGroups($business_shopper_group); 
	 $remove[] = $visitor_shopper_group; 
	 // function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){
	 // remove previous: 
	 /*
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 $shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = array(); 
	 $new_shoppergroups[] = $business_shopper_group;  
	 $shoppergroup_ids = $session->set('vm_shoppergroups_add',$new_shoppergroups,'vm');
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, null); 
	 
	 JRequest::setVar('virtuemart_shoppergroup_id', $new_shoppergroups, 'post');
	 */
	
	//appendShopperGroups
	
	    }
		else
		{
	 OPCShopperGroups::setShopperGroups($visitor_shopper_group); 
	 $remove[] = $business_shopper_group; 
	 /*
	 $shoppergroupmodel = new VirtueMartModelShopperGroup(); 
	 // function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){
	 // remove previous: 
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 $shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = array(); 
	 $new_shoppergroups[] = $visitor_shopper_group; 
	 $shoppergroup_ids = $session->set('vm_shoppergroups_add',$new_shoppergroups,'vm');
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, null); 
	 JRequest::setVar('virtuemart_shoppergroup_id', $new_shoppergroups, 'post');
		 */
		  }
	  }
	  }
		}
		
		// EU VAT shopper group: 
		if (!empty($euvat_shopper_group))
		{
		$removeu = true; 
		$session = JFactory::getSession(); 
		$vatids = $session->get('opc_vat', array());
	
		if (!is_array($vatids))
		$vatids = @unserialize($vatids); 
	   
		//BIT vat checker: 
		if (!empty($vatids['field']))
		{
		 
		   $euvat = JRequest::getVar($vatids['field'], ''); 
		   $euvat = preg_replace("/[^a-zA-Z0-9]/", "", $euvat);
		   $euvat = strtoupper($euvat); 
		   
		   if (!empty($cart))
		   {
		   $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		   $country = $address['virtuemart_country_id']; 
		   }
		   else
		   {
		     $country = JRequest::getVar('virtuemart_country_id'); 
		   }
		   
		   
		   $vathash = $country.'_'.$euvat; 
		   
		   $home = 'NL'; 
		   
		   if (!class_exists('ShopFunctions'))
		   require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		   
		   $country_2_code = shopFunctions::getCountryByID($country, 'country_2_code'); 
		   
		   $home = explode(',', $home_vat_countries); 
		   $list = array(); 
		   if (is_array($home))
		   {
		     foreach ($home as $k=>$v)
			  {
			    $list[] = strtoupper(trim($v)); 
			  }
			 
		   }
		   else
		   $list[] = $v; 
		   
		   if (!in_array($country_2_code, $list))
		   if (!empty($euvat))
		    {
			  $euvat = strtoupper($euvat); 
			  if (!empty($vatids[$vathash]))
			   {
			     //change OPC VAT shopper group: 
				OPCShopperGroups::setShopperGroups($euvat_shopper_group); 
				$removeu = false; 
				
			   }

			}
		}
		  if ($removeu)
		   $remove[] = $euvat_shopper_group; 
		}
		
		
		 OPCShopperGroups::setShopperGroups(-1, $remove); 
		 
		 if (class_exists('calculationHelper'))
		 calculationHelper::$_instance = null; 
	  	 
		  $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');

	
		
	}

 

}