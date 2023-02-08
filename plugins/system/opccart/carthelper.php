<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
class OPCcarthelper {
  public static function deleteCart()
  {
	
     if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	  
	   
	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
	   
	 $cart = VirtueMartCart::getCart(false); 
	 if (empty($cart)) return; 
	 if (!empty($cart->products))
	 $cart->emptyCart();
	
	 
	  if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
		  $calc = calculationHelper::getInstance(); 
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			$calc->setCartPrices(array()); 
			}
			
			
  }
  
     static function tableExists($table)
  {
   $db =& JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
 
   $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   if (!empty($r)) 
	   {
	   
	   return true;
	   }
   return false;
  }

  
  public static function installTable()
  {
	if (self::tableExists('virtuemart_plg_opccart')) return; 
    
  
     $myisam = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opccart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET ascii NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL DEFAULT \'\',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
   PRIMARY KEY (`id`),
   UNIQUE KEY `hash` (`hash`),
   KEY `mod` (`modified`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
   
   $inno = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opccart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET ascii NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL DEFAULT \'\',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mod` (`modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
   $db = JFactory::getDBO(); 
   $db->setQuery($inno); 
   $db->query(); 
   $e = $db->getErrorMsg(); 
   if (!empty($e))
     {
       $db->setQuery($myisam); 
       $db->query(); 
       $e = $db->getErrorMsg(); 
	   if (!empty($e))
	     {
		   JFactory::getApplication()->enqueueMessage('OPC Cart Plugin could not create tables: '.$e, 'error');
		 }
	 }
  }
  public static function cartKeyToAttributes($cart_key)
   {
     $a1 = explode('::', $cart_key); 
	   
	   if (count($a1) <= 1) {
	   $customPrices = null; 
	   return $customPrices; 
	   }
	   $a2 = explode(';', $a1[1]); 
	   
	   if (count($a2) <= 1) 
	   {
	   $customPrices = null;
	   return $customPrices; 
	   }
	   
	   
	   foreach ($a2 as $val)
	    {
		  if (empty($val)) continue; 
		  $a3 = explode(':', $val); 
		  
		  if (count($a3) <= 1) {
		   continue; 
		  }
		  
		  $customPrices[0][$a3[1]] = $a3[0]; 
		  
		}
		return $customPrices; 
   }
   public static function getCartHashLine($cart_key, $quantity)
   {
     return '___'.$cart_key.'_'.$quantity; 
   }
  public static function getProducts($hash, $user_id=0)
   {
    
     if (defined('GETPRODUCTSCART')) return; 
	 else define('GETPRODUCTSCART', 1); 
	 
     $productLine = OPCcarthelper::getLine($hash, $user_id);
	
	 if (empty($productLine)) return; 
	 if (empty($productLine['cart'])) return; 
	 
	 $products2 = $productLine['cart']; 
	 $pr = $products2; 
	 if (empty($pr)) return; 
	
	  
	 $products = json_decode($pr, true); 
	 if (empty($products)) return; 
	 //if (!is_array($cart)) return; 
	
	
	 
	 if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	   
	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
	 
	 $pModel = VmModel::getModel('product');
	 
	 $cart = VirtueMartCart::getCart(false); 
	 $added = 0; 
	 
	
	 
	 $saved = JRequest::get('default'); 
	 
	 
	 $i = 0; 
	 $cart_hash = ''; 
	 foreach ($products as $key=>$product)
	  {
	    
		
		{
		
		
		
		$cart_key = $product['cart_item_id']; 
		
		$customPrices = self::cartKeyToAttributes($cart_key); 
		
		
		JRequest::setVar('customPrice', $customPrices); 
		
		
		
		}
		
		JRequest::setVar('virtuemart_category_id', array($i=>$product['virtuemart_category_id']), 'default'); 
		JRequest::setVar('virtuemart_product_id', array($i => (int)$product['virtuemart_product_id'])); 
		JRequest::setVar('quantity', array($i => (int)$product['quantity'])); 
		
		$cart_hash .= self::getCartHashLine($cart_key, $product['quantity']); 
		
		$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
		$selected = JRequest::getVar ('virtuemart_product_id',0);
		
		
		$s = ''; 
		$tmpProduct = $pModel->getProduct((int)$product['virtuemart_product_id'], true, true,true,1);
		$tmpProduct = $pModel->getProduct((int)$product['virtuemart_product_id'], true, true,true,(int)$product['quantity']);
		
		$cart->add($virtuemart_product_ids, $s); 
		//var_dump($s); 
		$ign = array('customPlugin', 'quantity', 'customfieldsCart', 'customPrices', 'customfields'); 
		
		JRequest::setVar('customPrice', null); 
		
		
		
		
		$i++; 
	  }
	 
	 if (isset($saved['virtuemart_product_id']))
	 JRequest::setVar('virtuemart_product_id', $saved['virtuemart_product_id'], 'default', true); 

	 if (isset($saved['virtuemart_category_id']))
	 JRequest::setVar('virtuemart_category_id', $saved['virtuemart_category_id'], 'default', true); 

	 if (isset($saved['customPrice']))
	 JRequest::setVar('customPrice', $saved['customPrice'], 'default', true); 

	 if (isset($saved['quantity']))
	 JRequest::setVar('quantity', $saved['quantity'], 'default', true); 

	 
	 
	 $cart->setPreferred();
	
	 $cart->prepareCartViewData(); 
	 $cart->setCartIntoSession();
	 return $cart_hash;
	 
	 if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
		  $calc = calculationHelper::getInstance(); 
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			  $calc->setCartPrices(array()); 
			}
	 $cart->virtuemart_shipmentmethod_id = 0; 
	 $cart->payment_shipmentmethod_id = 0; 
     $cart->setCartIntoSession(); 			
	 
   }
   
   public static function checkLast()
   {
     $view = JRequest::getVar('view', ''); 
	 $controller = JRequest::getVar('controller', ''); 
	 $task = JRequest::getVar('task', ''); 
	 $option = JRequest::getVar('option'); 
	 if ($option == 'com_virtuemart')
	 if (($view == 'cart') || ($controller == 'cart'))
	 if ($task == 'delete')
	  {
	     if (!class_exists('VmConfig'))	  
	     {
	       require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	       VmConfig::loadConfig(); 
	     }
	   
	   if (!class_exists('VirtueMartCart'))
	      require(JPATH_SITE.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
	   
	   $cart = VirtueMartCart::getCart(false); 
		
		
	    if (count($cart->products)==1)
		{
		
	    return true; 
		}
		else return false; 
	  }
	  return false; 
   }
  public static function storeProducts($hash)
  {
     if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	   
	   $cart = OPCcarthelper::hasProducts(true);
	   OPCcarthelper::updateLine($hash, $cart); 
	   return; 
	  
	  
  }
  public static function pD()
  {
    return;
    $x = debug_backtrace(); 
	foreach ($x as $y) echo $y['file'].' '.$y['line']."<br />\n"; 
	die(); 
  }
  public static function removeLine($hash)
   {
      //self::pd(); 
      $db = JFactory::getDBO(); 
	  $q = "delete from #__virtuemart_plg_opccart where hash = '".$db->getEscaped($hash)."' limit 1"; 
	  $db->setQuery($q); 
	  $db->query(); 
   }
  public static function updateLine($hash, $car2t)
  {
         if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	   
	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
	   
	 $cart = VirtueMartCart::getCart(false); 
    $products = $cart->products;
	if (empty($products)) return; 
	/*
	foreach ($products as $key=>$val)
	 foreach ($val as $key2 => $val2)
	 {
	    if (is_object($val))
		if (get_class($val) != 'stdClass')
		unset($products[$key]->$key2); 
	    if (empty($products[$key]->quantity)) 
		unset($products[$key]); 
	 }
	 */
	 if (empty($products)) return; 
	 
	 
	 $store = array(); 
	 foreach ($products as $key=>$val)
	 {
	 $product = array(); 
	 $product['virtuemart_manufacturer_id'] = $val->virtuemart_manufacturer_id; 
	 $product['quantity'] = $val->quantity ; 
	 $product['virtuemart_category_id'] = $val->virtuemart_category_id; 
	 $product['virtuemart_product_id'] = $val->virtuemart_product_id; 
	 $product['cart_item_id'] = $val->cart_item_id; 
	 $store[$product['cart_item_id']] = $product; 
	 
	 }
	
	$cartS = json_encode($store); 
	if (empty($cartS)) return; 
	
	
	
    $db = JFactory::getDBO(); 
    $cartS = $db->getEscaped($cartS); 
	$hash = $db->getEscaped($hash); 
	$user_id = (int)JFactory::getUser()->get('id'); 
    $q = "insert into `#__virtuemart_plg_opccart`  (`id`, `cart`, `hash`, `user_id`, `created`, `created_by`, `modified`, `modified_by`) ";
	$q .= " values (NULL, '".$cartS."', '".$hash."', ".$user_id.", NOW(), ".$user_id.", NOW(), ".$user_id.") on duplicate key "; 
	$q .= "update `cart` = '".$cartS."', `modified`=NOW(), `modified_by`=".$user_id." ";
	$db->setQuery($q); 
	$db->query(); 
	
	
	
  }
  
  public static function removeOld($timeout)
  {
    $unix = time() - $timeout; 
	
	$time = JFactory::getDate($unix); 
	$mysqltime = $time->toMySQL(); 
	$db = JFactory::getDBO(); 
	$q = "delete from #__virtuemart_plg_opccart where (modified < '".$db->getEscaped($mysqltime)."' and modified > 0)"; 
	
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); 
	
	
  }
  
  public static function getLine($hash, $user_id=0)
   {
     $db = JFactory::getDBO(); 
	 $q = "select * from `#__virtuemart_plg_opccart` where hash = '".$db->getEscaped($hash)."' "; 
	 if (!empty($user_id)) $q .= ' and created_by = '.(int)$user_id; 
	 $q .= " limit 0,1"; 
	 $db->setQuery($q); 
	 $res = $db->loadAssoc(); 
	 if (empty($res)) return array(); 
	 $ret = array(); 
	 foreach ($res as $key=>$val)
	  {
	    $ret[$key] = $val; 
	  }
	 return $ret; 
	 
   }
   
   public static function hasProducts($retObj=false)
	{
	
	 if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	
	if (!class_exists('VmImage'))
		require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart' .DS. 'helpers' . DS . 'image.php');

	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
	 
	 if (!class_exists('calculationHelper'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		
	 $cart = VirtueMartCart::getCart(false); 
	 if (!empty($cart->products)) 
	  {
	    $cart_hash = ''; 
	    foreach ($cart->products as $key=>$val)
		  {
		     $cart_hash .= self::getCartHashLine($key, $val->quantity); 
		  }
	  return $cart_hash; 
	  }
	 else return false; 
	
	   $session = JFactory::getSession(); 
	   $cartS = $session->get('vmcart', 0, 'vm');
	   if (empty($cartS)) return false; 
	  try {
	    $cart = @unserialize($cartS); 
		if (empty($cart)) return;
	  } catch (Exception $e) {
		 return null;
	  }
	  
	  if (!empty($cart->products)) 
	  {
	   if ($retObj) return $cart; 
	   return true; 
	  }
	  else return false; 
	  
	  return null; 
	}

}