<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemOpccart extends JPlugin
{
    function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
        

    public function onAfterRoute() {

	  if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php')) return false;
	  require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php'); 
	  $session = JFactory::getSession(); 
		$user_id = JFactory::getUser()->get('id'); 
		// check installation once per backend impression:
		
		if (!empty($user_id))
		{
	  	$app = JFactory::getApplication();
		$installed = $session->get('opc_cart_installed', 0); 
		if (!$installed)
		if ($app->getName() != 'site') {
			OPCcarthelper::installTable(); 
			$session->set('opc_cart_installed', 1); 
		}
		}

	  
	  
	  if (OPCcarthelper::checkLast())
	   {
	      $hash = $this->_getHash(); 
		  OPCcarthelper::removeLine($hash); 
		  
	   }
	
	  if (!$this->_check()) return; 
	  //OPCcarthelper::deleteCart(); 
	  $cart_hash = OPCcarthelper::hasProducts(); 
	  if ($cart_hash===false)
	   {
	     
	     // do action here
		 $hash = $this->_getHash(); 
		 
		 $mode = $this->params->get('mode'); 
		 if (empty($mode))
		 $cart_hash = OPCcarthelper::getProducts($hash); 
	     else
		 $cart_hash = OPCcarthelper::getProducts($hash, $user_id); 
		 
		 $session->set('opc_last_cart_hash', $cart_hash); 
	   
	   }
	  
	  
	  
	  return; 
	 
	  
	  
	}
	
	private function _getHash()
	{
	   
	   $mode = $this->params->get('mode'); 
	   $user_id = JFactory::getUser()->get('id'); 
	   if (!empty($mode)) $mode .= '.'.$user_id.'.'; 
	   
	   $hash2 = uniqid('cart'.$mode, true); 
	   $hash2 = substr($hash2, 0, 50); 
	   
	   jimport( 'joomla.utilities.utility' );
	   $hashn = JUtility::getHash('opccart'.$mode); 
	   $hashn = substr($hashn, 0, 20); 
	   $hash = JRequest::getVar($hashn, $hash2, 'COOKIE'); 
	   
	   plgSystemOpccart::_setCookie($hashn, $hash, $this->params->get('cookie_timeout', 2592000) ); 
	  
	   return $hash; 
	}
	
	private static function _setCookie($hashn, $hash, $timeout=0)
    {
     if (empty($timeout)) $timeout = time()+60*60*24*30; 
	 else $timeout += time(); 
	 if ($timeout<0) $timeout = 1; 
	
	 
	 
	 $config =  JFactory::getConfig(); 
         $config = new OPCObj($config); 
	 $domain = $config->getValue('config.cookie_domain'); 
	 $path = $config->getValue('config.cookie_path'); 
	 if (empty($path)) $path = '/'; 

	 if (empty($domain))
	 {
	 $x = setcookie($hashn, $hash, $timeout, $path, null);
	 
	 }
	 else
	 {
	  //$hash, $timeout, $path, $domain);
	 $x = setcookie($hashn, $hash, $timeout, $path, $domain);
	 //echo $domain.':'; 
	 //echo 'setcookie: '.$hashn.':::'.$hash.':'.$timeout.':'.$path.':'.$domain."<br />\n"; 
	 }
	 $_COOKIE[$hashn] = $hash; 
	
	
	 
    }
	
	
	public function plgVmOnUpdateOrderPayment(&$data,$old_order_status)
	{
	  
	  
	  if (!$this->_check()) return; 
	  if (defined('OPCTRACKINGORDERCREATED2')) return; 
	  else define('OPCTRACKINGORDERCREATED2', 1); 
	  
	  
	  $hash = $this->_getHash(); 
	  OPCcarthelper::removeLine($hash); 
	  
	}
	
	
	
	
	public function plgVmOnUpdateOrderShipment(&$data,$old_order_status)
	{
	  if (defined('OPCTRACKINGORDERCREATED2')) return; 
	  else define('OPCTRACKINGORDERCREATED2', 1); 
	  
	  if (!$this->_check()) return; 
	  
	  $hash = $this->_getHash(); 
	  OPCcarthelper::removeLine($hash); 
	    
	   
		
	}
	
	private function _check()
	{
	  	$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return false;
		}
		
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') return false;

		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') return false; 
		
		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php')) return false;
		require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php'); 
		
		$user_id = JFactory::getUser()->get('id'); 
	    $mode = $this->params->get('mode'); 
	    if ($mode == 1)
	    if (empty($user_id)) return false; 
		
		return true; 

	}
	
	public function plgVmOnRemoveFromCart($cart, $product_id)
	{
	  
	   if (empty($cart->products))
	   {
	      if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php')) return false;
	      require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php'); 
		  $hash = $this->_getHash(); 
		  OPCcarthelper::removeLine($hash); 
		
	   }
	   
	}
	
	public function onAfterRender()
	{
	
	   if (!$this->_check()) return; 
	   
	   $cart_hash = OPCcarthelper::hasProducts(); 
	   if (empty($cart_hash)) return; 
	   
	   //if ($hasProducts===true)
	   {
	  
	  
	   
	   $session = JFactory::getSession(); 
	   $cart_hash2 = $session->get('opc_last_cart_hash', ''); 
	    // update db only when the cart has changed
		if ($cart_hash2 != $cart_hash)
	    {
		
	     $hash = $this->_getHash(); 
	     OPCcarthelper::storeProducts($hash); 
		 OPCcarthelper::removeOld($this->params->get('cookie_timeout', 2592000)); 
		 $session->set('opc_last_cart_hash', $cart_hash); 
		 
	    }
	   }
	   
	   
	   
	   
		return; 
	   
	   
	}

		
}
