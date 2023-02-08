<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Overrided VmPlugin class for the OPC ajax and checkout
 * 
 * This class was overrided due to few serious bugs in the orginal release and to be able to add additional functionality to it 
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
 ORIGINAL LICENSE AND COPYRIGHT
 * abstract class for payment plugins
 *
 * @package	VirtueMart
 * @subpackage Plugins
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: vmplugin.php 4599 2011-11-02 18:29:04Z alatak $
 */
// Load the helper functions that are needed by all plugins
if (!class_exists('ShopFunctions'))
require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'shopfunctions.php');

require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 

// Get the plugin library
jimport('joomla.plugin.plugin');

abstract class vmPlugin extends JPlugin {

	// var Must be overriden in every plugin file by adding this code to the constructor:
	// $this->_name = basename(__FILE, '.php');
	// just as note: protected can be accessed only within the class itself and by inherited and parent classes

	//This is normal name of the plugin family, custom, payment
	protected $_psType = 0;
	
	//Id of the joomla table where the plugins are registered
	protected $_jid = 0;

	protected $_vmpItable = 0;
	//the name of the table to store plugin internal data, like payment logs
	protected $_tablename = 0;

	protected $_tableId = 'id';
	//Name of the primary key of this table, for exampel virtuemart_calc_id or virtuemart_order_id
	protected $_tablepkey = 0;

	protected $_vmpCtableAll = array();
	protected $_vmpCtable = 0;
	//the name of the table which holds the configuration like paymentmethods, shipmentmethods, customs
	protected $_configTable = 0;
	protected $_configTableFileName = 0;
	protected $_configTableClassName = 0;
	protected $_xParams = 0;
	protected $_varsToPushParam = array();
	//id field of the config table
	protected $_idName = 0;
	//Name of the field in the configtable, which holds the parameters of the pluginmethod
	protected $_configTableFieldName = 0;

	protected $_debug = false;
	protected $_loggable = false;
	protected $cost_per_transaction  = 0; 
	protected $cost_percent_total   = 0; 
	protected $min_amount = 0; 
	protected $max_amount = 0; 
	protected $_cryptedFields = false;
	
	
	// OPC addons: 
	public static $iCount; 
	static $qCache; 
	public static $ccount; 
	//static $payment_logos = null;
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);
		
		$this->_psType = substr($this->_type, 2);

		$lang = JFactory::getLanguage();
		$filename = 'plg_' . $this->_type . '_' . $this->_name;
		
		if (method_exists('VmConfig', 'loadJLang'))
		{
		  //VmConfig::loadJLang($filename);
		  $this->loadJLang($filename);
		}
		else
		{
		if(VmConfig::get('enableEnglish', 1)){
		    $lang->load($filename, JPATH_ADMINISTRATOR, 'en-GB', true);
		}
		    $lang->load($filename, JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load($filename, JPATH_ADMINISTRATOR, null, true);
		}
/*
		$knownLanguages=$lang->getKnownLanguages();
		foreach($knownLanguages as $key => $knownLanguage) {
			$lang->load ($filename, JPATH_ADMINISTRATOR, $key, TRUE);
		}
		*/
		if (!OPCJ3)
		if (!class_exists ('JParameter')) {
			require(JPATH_VM_LIBRARIES . DS . 'joomla' . DS . 'html' . DS . 'parameter.php');
		}

		$this->_tablename = '#__virtuemart_' . $this->_psType . '_plg_' . $this->_name;
		$this->_tableChecked = FALSE;
		
		
		
		$this->_xmlFile	= JPath::clean( JPATH_PLUGINS .'/'. $this->_type .'/'.  $this->_name . '/' . $this->_name . '.xml');
	}
	
	
	public function loadJLang($fname,$type=0,$name=0){

		$jlang =JFactory::getLanguage();
		$tag = $jlang->getTag();

		if(empty($type)) $type = $this->_type;
		if(empty($name)) $name = $this->_name;
		$path = $basePath = JPATH_ROOT .DS. 'plugins' .DS.$type.DS.$name;

		if(VmConfig::get('enableEnglish', true) and $tag!='en-GB'){
			$testpath = $basePath.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$fname.'.ini';
			if(!file_exists($testpath)){
				$epath = JPATH_ADMINISTRATOR;
			} else {
				$epath = $path;
			}
			$jlang->load($fname, $epath, 'en-GB');
		}

		$testpath = $basePath.DS.'language'.DS.$tag.DS.$tag.'.'.$fname.'.ini';
		if(!file_exists($testpath)){
			$path = JPATH_ADMINISTRATOR;
		}

		$jlang->load($fname, $path,$tag,true);
	}
	
	function setCryptedFields($fieldNames){
		$this->_cryptedFields = $fieldNames;
	}
	
	 function setPluginLoggable($set=TRUE){
		$this->_loggable = $set;
	 }
/**
	 * @return array
	 */
	function getTableSQLFields() {

		return false;
	}
	
	function plgVmConfirmedOrderOPC($type, $cart, $order)
	{
	  if ($this->_psType != $type) return null; 
	  return $this->plgVmConfirmedOrder($cart, $order); 
	  
	}
	
	function plgVmConfirmedOrderOPCExcept($types, $cart, $order)
	{
	  if (in_array($this->_psType, $types)) return null; 
	  return $this->plgVmConfirmedOrder($cart, $order); 
	}
	

function getOwnUrl(){

		if(JVM_VERSION!=1){
			$url = '/plugins/'.$this->_type.'/'.$this->_name;
		} else{
			$url = '/plugins/'.$this->_type;
		}
		return $url;
	}

	public function getPaymentMethodsOPC(&$cart, &$payments)
	{
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
 	  if ($this->_psType != 'payment') return; 
	 
		
		if ($this->_name == 'klarna')
		{
		  $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		  if (isset($address['virtuemart_country_id']))
		  $country = $address['virtuemart_country_id']; 
		  if (empty($country) && (!empty($cart->BT['virtuemart_country_id']))) $country = $cart->BT['virtuemart_country_id']; 
		   if (!class_exists('ShopFunctions'))
		  require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		  
		  if (empty($country)) return; 
		  
		  $countryCode = shopFunctions::getCountryByID ($country, 'country_2_code');
		  $avai = array('SE', 'DE', 'NL', 'NO', 'DK', 'FI'); 
		  $countryCode = strtoupper($countryCode); 
		  if (!in_array($countryCode, $avai)) 
		  {
		   return; 
		  }
		  
		}
		
		 $nmethods = $this->getPluginMethodsOPC ($cart->vendorId);
		if (empty($this->methods))
	    {
			return;
		}
		
		if (!empty($this->methods))
		foreach ($this->methods as &$method)
		{
		  $method->opcref =& $this; 
		  $payments[] =& $method;   
		}
		
			
	}
	
	
	
	public function getShipmentMethodsOPC($vendor_id=1, &$payments)
	{
	 
 	  if ($this->_psType != 'shipment') return; 
	 
		
		
		 $nmethods = $this->getPluginMethodsOPC ($vendor_id);
		if (empty($this->methods))
	    {
			return;
		}
		
		if (!empty($this->methods))
		foreach ($this->methods as &$method)
		{
		  $method->opcref =& $this; 
		  $payments[] =& $method;   
		}
		
			
	}
	
	public function plgVmDisplayListFEShipmentOPCNocache(&$cart, $selected = 0, &$htmlIn)
	{
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	 if ($this->_psType != 'shipment') return; 
		
	  if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
	  
                return FALSE;            
        }  
	   
	   $return = array(); 
	   
	   if (isset($this->methods))
	   {
	   foreach ($this->methods as $key => $method)
	   {
	   if (isset($method->virtuemart_shipmentmethod_id))
	   {
	     $vm_id = $method->virtuemart_shipmentmethod_id; 
		 
	     $html = ''; 
	     OPCtransform::overrideShippingHtml($html, $cart, $vm_id); 
	     if ($html != '') 
	     {
			$return[] = $html; 
		    
	     }
	   
		 //break; 
		}
	   }
	   
	   if (!empty($return))
	    {
		  $htmlIn[] = $return; 
		  return true; 
		}
	   
	   }
	   else return null; 
	  
	  
	  /*
	  $htmlstart = $htmlIn; 
	  $newhtml = array(); 
	  */
	  return $this->plgVmDisplayListFEShipment($cart, $selected, $htmlIn);
	  /*
	  if (!empty($newhtml))
	  {
	  if (!empty($this->methods))
	  foreach ($newhtml as &$html)
	  {
	  foreach ($this->methods as $key => $method)
	   {
	     OPCTransform::getOverride('opc_transform', $this->_name, $this->_psType, $this, $method, $html); 
	   }
	   
	   
	    
	   $htmlIn[] = $html; 
	   return true; 
		
		}
	   }
	  //$htmlIn .= $newhtml; 
	  */
	  
	}
	
	
	
	
	
	private function _setMissingOPC(&$method)
	{
	  if (!isset($method->payment_logos)) $method->payment_logos = ''; 
	  if (!isset($method->cost_per_transaction)) $method->cost_per_transaction = 0; 
	  if (!isset($method->cost_percent_total)) $method->cost_percent_total = 0; 
		 
		if (!isset($method->tax_id)) $method->tax_id = -1; 
		if (!isset($method->weight_unit)) $method->weight_unit = 'KG'; 
	}
	
		/**
	 * Fill the array with all plugins found with this plugin for the current vendor
	 *
	 * @return True when plugins(s) was (were) found for this vendor, false otherwise
	 * @author Oscar van Eijk
	 * @author max Milbers
	 * @author valerie Isaksen
	 */
	protected function getPluginMethodsOPC ($vendorId) {

		if (!class_exists ('VirtueMartModelUser')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
		}

		$usermodel = VmModel::getModel ('user');
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
				j.`params`, j.`custom_data`, j.`system_data`, j.`checked_out`, j.`checked_out_time`, j.`state`,  s.virtuemart_shoppergroup_id ';
		}

		if(!defined('VMLANG')){
		   if (!class_exists('VmConfig'))
		    {
			     if (!class_exists('VmConfig'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig ();

			}
			if(!defined('VMLANG'))
			VmConfig::setdbLanguageTag();
		}
		$q = $select . ' FROM   `#__virtuemart_' . $this->_psType . 'methods_' . VMLANG . '` as l ';
		$q .= ' JOIN `#__virtuemart_' . $this->_psType . 'methods` AS v   USING (`virtuemart_' . $this->_psType . 'method_id`) ';
		$q .= ' LEFT JOIN `' . $extPlgTable . '` as j ON j.`' . $extField1 . '` =  v.`' . $this->_psType . '_jplugin_id` ';
		$q .= ' LEFT OUTER JOIN `#__virtuemart_' . $this->_psType . 'method_shoppergroups` AS s ON v.`virtuemart_' . $this->_psType . 'method_id` = s.`virtuemart_' . $this->_psType . 'method_id` ';
		$q .= ' WHERE v.`published` = "1" AND j.`' . $extField2 . '` = "' . $this->_name . '"
	    						AND  (v.`virtuemart_vendor_id` = "' . $vendorId . '" OR   v.`virtuemart_vendor_id` = "0")
	    						AND  (';

		foreach ($user->shopper_groups as $groups) {
			$q .= ' s.`virtuemart_shoppergroup_id`= "' . (int)$groups . '" OR';
		}
		$q .= ' (s.`virtuemart_shoppergroup_id`) IS NULL ) GROUP BY v.`virtuemart_' . $this->_psType . 'method_id` ORDER BY v.`ordering`';

		$db->setQuery ($q);

		$this->methods = $db->loadObjectList ();

		$err = $db->getErrorMsg ();
		if (!empty($err)) {
			vmError ('Error reading getPluginMethods ' . $err);
		}
		if ($this->methods) {
			foreach ($this->methods as $method) {
			    if (!empty($this->_xParams))
				VmTable::bindParameterable ($method, $this->_xParams, $this->_varsToPushParam);
			}
		}

		return count ($this->methods);
	}
	
	
	public function plgVmDisplayListFEPaymentOPCNocache(&$cart, $selected = 0, &$htmlIn)
	{
	   
	  if ($this->_psType != 'payment') return; 
	  
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
	    if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		 }
		
		 $currency = CurrencyDisplay::getInstance ();
	   
		
		if ($this->_name == 'klarna')
		{
		  $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		  if (isset($address['virtuemart_country_id']))
		  $country = $address['virtuemart_country_id']; 
		  if (empty($country) && (!empty($cart->BT['virtuemart_country_id']))) $country = $cart->BT['virtuemart_country_id']; 
		   if (!class_exists('ShopFunctions'))
		  require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		  if (empty($country)) return; 
		  $countryCode = shopFunctions::getCountryByID ($country, 'country_2_code');
		  $avai = array('SE', 'DE', 'NL', 'NO', 'DK', 'FI'); 
		  $countryCode = strtoupper($countryCode); 
		  if (!in_array($countryCode, $avai)) 
		  {
		   return; 
		  }
		  
		}
		
	    if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
	  
                return FALSE;            
        }
	   $return = array(); 
	   
	   
	   
	   if (isset($this->methods))
	   {
	   $ref =& $this; 
	   foreach ($this->methods as $key => &$method)
	   {
	   
	   if (isset($method->virtuemart_paymentmethod_id ))
	   {
	     $this->_setMissingOPC($method); 
		 
	     $vm_id = $method->virtuemart_paymentmethod_id; 
	     $html = ''; 
		 //$filename = 'plg_' . $this->_type . '_' . $this->_name;
		 $name = $this->_name; 
		 jimport('joomla.filesystem.file');
   $name = JFile::makeSafe($name); 
   $type = $this->_psType; 
   if ($type == 'vmpayment') $type = 'payment'; 
   if ($type == 'vmshipment') $type = 'shipment'; 
   $type = JFile::makeSafe($type); 
   
   		static $theme; 
		if (empty($theme))
		{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		$theme = $selected_template; 
		}

   $layout_name = 'after_render'; 
   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$type.DS.$name.DS.$layout_name.'.php';
   
   if (file_exists($layout))
		 {
		  $method_name = $this->_psType . '_name';
		  
		  $plugin =& $method; 
		  $pluginmethod_id = $this->_idName;
		  $plugin_name = $this->_psType . '_name';
		  $plugin_desc = $this->_psType . '_desc';
		  $logosFieldName = $this->_psType . '_logos';
		  $logo_list = $plugin->$logosFieldName;
		 
		 	$pricesUnformatted= $cart->pricesUnformatted;
			$arr = array($this, 'setCartPrices'); 
			if (is_callable($arr))
			$pluginSalesPrice = $this->setCartPrices ($cart, $pricesUnformatted,$method);
			else $pluginSalesPrice = 0; 
		
		 
		  $url = JURI::root () . 'images/stories/virtuemart/' . $this->_psType . '/';
		  if (!is_array ($logo_list)) {
				$logo_list = (array)$logo_list;
			}
		  
		 
		  
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   
		   ob_start(); 
		   include($layout); 
		   if (!empty($html)) $null = ob_get_clean(); 
		   else
		   $html = ob_get_clean(); 
		   
		   
		   $isset = true; 
		   
		   
		 }
		 else
		 {
	     OPCtransform::overridePaymentHtml($html, $cart, $vm_id, $this->_name, $this->_type, $method); 
		 }
	     if ($html != '') 
	     {
			$return[] = $html; 
		    
	     }
	   
		 //break; 
		}
	   }
	   
	   if (!empty($return))
	    {
		  $htmlIn[] = $return; 
		  return true; 
		}
	   
	   }
	   else return null; 
	  
	  
	  if (empty($htmlIn)) $htmlIn = array(); 
	  
	  $new = array(); 
	  $return = $this->plgVmDisplayListFEPayment($cart, $selected, $new);
	  // loads render_after
	  foreach ($new as $html)
	    {
		  OPCtransform::overridePaymentHtml($html, $cart, $vm_id, $this->_name, $this->_type); 
		  $htmlIn[] = $html; 
		}
	  return $return; 
	} 
	
	function getPPLExpress(&$payment_id, &$cart)
	{
	  
	  $methods = $this->getPluginMethodsOPC($cart->vendorId); 
	  if ($methods === 0) {
			return FALSE;
		}
		
		foreach ($this->methods as $m)
		 {
		 
		   if (isset($m->paypalproduct))
			  {
			  
			    if ($m->paypalproduct == 'exp')
				  {
				  
				    if (isset($m->virtuemart_paymentmethod_id))
				    $payment_id = $m->virtuemart_paymentmethod_id;
					
					
				    return; 
					
				  }
			  }
			  else
			  return;
		 }
	  
	  
	}
	
	
	function getPluginOPC($vid, &$cart, &$ret)
	{
		
		
		if (isset($this->customerData))
		if (method_exists($this->customerData, 'getVar'))
		{
		  $token = $this->customerData->getVar('token'); 
		  if (!empty($token))
		   {
		     
		   }
		}
		
		$m = $this->getVmPluginMethod($vid); 
		if (empty($m)) return;
		$ret[] = $m; 
		return;

	}
	
	
	function getPluginHtmlOPC(&$result, &$methodOPC, $type='shipment', $virtuemart_id=0, $cart)
	{
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
	   $allowed = array('shipment', 'payment'); 
	   if (!in_array($this->_psType, $allowed)) return; 
	   
	   if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
                return FALSE;            
        }  
	   
	   if ($this->_psType == $type)
	   if (isset($this->methods))
	   foreach ($this->methods as $key => $method)
	   {
	   if (isset($method->virtuemart_shipmentmethod_id))
	   if ($virtuemart_id == $method->virtuemart_shipmentmethod_id) 
	   {
	     $methodSalesPrice = $this->calculateSalesPrice($cart, $method, $cart->pricesUnformatted);  
		 $html = $this->getPluginHtml($method, 0, $methodSalesPrice);
		 $method->OPCname = $this->renderPluginName($method);
		 $method->OPCsalesprice = $methodSalesPrice; 
		 $methodOPC = $method; 
	     $result = $html; 
		 break; 
		}
	   }
	}
	
	function getPluginNameOPC(&$result, &$methodOPC, $type='shipment', $virtuemart_id=0, $cart)
	{
	
	$allowed = array('shipment', 'payment'); 
	   if (!in_array($this->_psType, $allowed)) return; 
	
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
	   if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
                return FALSE;            
        }  
	   
	   if ($this->_psType == $type)
	   if (isset($this->methods))
	   foreach ($this->methods as $key => $method)
	   {
	   if (isset($method->virtuemart_shipmentmethod_id))
	   if ($virtuemart_id == $method->virtuemart_shipmentmethod_id) 
	   {
	     $result = $this->renderPluginName($method);
		 break; 
		}
	   }
	}
	
	function plgGetPluginObject(&$result, $type='shipment', $virtuemart_id=0)
	{
	   //if (empty($virtuemart_id) || ($virtuemart_id == 
	   if (isset($this->virtuemart_shipmentmethod_id))
	   if (!empty($virtuemart_id))
	   if ($virtuemart_id != $this->virtuemart_shipmentmethod_id) return null;
	   
	   if (empty($type) || ($this->_psType == $type))
	   $result[] =& $this; 
	   
	   return $this;
	}

	function display3rdInfo($intro,$developer,$contactlink,$manlink){
		$logolink = $this->getOwnUrl() ;
		return shopfunctions::display3rdInfo($this->_name,$intro,$developer,$logolink,$contactlink,$manlink);
	}
	
	private static $xmlCache; 
	private static $xmlDefaults; 
	
	static public function getVarsToPushByXML ($xmlFile,$name){
		$data = array();
		$defaults = array(); 
		
		
		if (isset(vmPlugin::$xmlCache[$xmlFile][$name])) return vmPlugin::$xmlCache[$xmlFile][$name]; 
		
		if (is_file ( $xmlFile )) {

			//$xml = JFactory::getXML ('simple');
			//$result = $xml->loadFile ($xmlFile);
			if (!OPCJ3)
			{
			$xml =  JFactory::getXML($xmlFile);
			
			if ($xml) {
				if (isset( $xml->document->params) ){
					$params = $xml->document->params;
					foreach ($params as $param) {
						if ($param->_name = "params") {
							if ($children = $param->_children) {
								foreach ($children as $child) {
									if (isset($child->_attributes['name'])) {
										$data[$child->_attributes['name']] = array('', 'char');
										$result = TRUE;
									}
								}
							}
						}
					}
				} else {
					$form = JForm::getInstance($name, $xmlFile, array(),false, '//config');
					
					$fieldSets = $form->getFieldsets();
					foreach ($fieldSets as $name => $fieldSet) {
						foreach ($form->getFieldset($name) as $field) {
							// todo : type?
							$type='char';
							$data[(string)$field->fieldname] = array('',  $type);
						}
					}
			}
			}
			}
			else
			{
			        $xml = simplexml_load_file($xmlFile); 
					if (!empty($xml))
					if (isset( $xml->vmconfig) )
					{
					
					foreach ($xml->vmconfig->fields->children() as $i=>$child)
					 {
					    
					    $tagname = (string)$i; 
						if ($tagname == 'fieldset')
						 { 
						   foreach ($child->children() as $u=>$param)
						    {
							   $tname = (string)$u; 
							   
							   if ($tname == 'field')
							    {
								// repeat 1
								  $attr = current($param->attributes()); 
								 
								  if (isset($attr['name']))
								  {
								  
								    $data[(string)$attr['name']] = array('',  'char');
									
									if (isset($attr['default']))
									 {
									   $defaults[(string)$attr['name']] = (string)$attr['default']; 
									 }
								  
								  }
								// repeat 1	 end
								  
								   
								}
							}
						 }
						 else
						 if ($tagname == 'field')
						  {
						     // repeat 1
						      $attr = current($param->attributes()); 
								  if (isset($attr['name']))
								  {
								  
								    $data[(string)$attr['name']] = array('',  'char');
									
									if (isset($attr['default']))
									 {
									   $defaults[(string)$attr['name']] = (string)$attr['default']; 
									 }
								  }
						    // repeat 1 end
						  }
						
					 }
					
				}
					}
				}
			
		
		vmPlugin::$xmlCache[$xmlFile][$name] = $data; 
		vmPlugin::$xmlDefaults[$name] = $defaults; 
		
		
		return $data;
		}
		
	
	
	/**
	 * Checks if this plugin should be active by the trigger
	 *
	 * @author Max Milbers
	 * @param string $psType shipment,payment,custom
	 * @param        string the name of the plugin for example textinput, paypal
	 * @param        int/array $jid the registered plugin id(s) of the joomla table
	 *
	 * @param int/array $id the registered plugin id(s) of the joomla table
	 */
	protected function selectedThis ($psType, $name = 0, $jid = 0) {

		if ($psType !== 0) {
			if($psType!=$this->_psType){
				vmdebug('selectedThis $psType does not fit');
				
				return false;
			}
		}

		if($name!==0){
			if($name!=$this->_name){
 				vmdebug('selectedThis $name '.$name.' does not fit pluginname '.$this->_name);
				
				return false;
			}
		}

		if($jid===0){
		     
			return false;
		} else {
			if($this->_jid===0){
				$this->getJoomlaPluginId();
			}
			if(is_array($jid)){
				if(!in_array($this->_jid,$jid)){
					//vmdebug('selectedThis id '.$jid.' not in array does not fit '.$this->_jid);
			
			return false;
				}
			} else {
				if($jid!=$this->_jid){
					//vmdebug('selectedThis $jid '.$jid.' does not fit '.$this->_jid);
					//echo $jid; 
				//	echo '<br />'.$this->_jid.'<br />'; 
					return false;
				}
			}
		}

		return true;
	}
	
	public function plgVmDisplayListFEShipmentOPC(&$cart, $selected = 0, &$htmlIn)
	{
	    if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
		if ($this->_type != 'vmshipment') return; 

		$pluginmethod_id = $this->_idName; //virtuemart_shipmentmethod_id
		$pluginName = $this->_psType . '_name'; // shipment_name
			
		$pluginName = $this->_name; 
		$data = array($selected, $pluginmethod_id, $pluginName ); 
			

			
		$hash = OPCcache::getGeneralCacheHash('plgVmDisplayListFEShipment', $cart, $data); 
		$val = OPCcache::getValue($hash); 
			
		VmPlugin::$iCount++; 
		
		
		
		
			if (!empty($val))
			{
				
				if (empty($val[0])) 
				{
				break;
				//return $val[0]; 
				}
				else
				{
				foreach ($val[1] as $vala)
				{
				 $htmlIn[] = $vala; 
				}
				
				return true; 
				}
			}
			
		
		
		
			
		
		
		
		$htmlIn2 = array(); 
		$val = $this->plgVmDisplayListFEShipmentOPCNocache($cart, $selected, $htmlIn2);
		$html = ''; 
		if (is_array($htmlIn2))
		{
		foreach ($htmlIn2 as $h)
		{
			if (is_array($h))
			{
				foreach ($h as $h2)
				  $html .= $h2; 
			}
			  else
			$html .= $h; 
		}
		}
		else $html  = $htmlIn2; 
		if (!empty($html) || (stripos($html, 'virtuemart_shipmentmethod_id')!==false))
		if (stripos($html, 'error')===false)
		$val2 = OPCcache::setValue($hash, array($val, $htmlIn2 )); 
		
	
	
		if (!empty($htmlIn2))
		{
			$htmlIn = array_merge($htmlIn, $htmlIn2); 
		}
		
		return $val; 
	}
	
	public function plgVmGetSpecificCache($cart, $id=0)
	{
		
		if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
		
		if ($this->_type == 'vmshipment')
		{
		
		if (empty($cart->virtuemart_shipmentmethod_id)) 
		{
		
		return ""; 
		}
		else
		$virtuemart_shipmentmethod_id = $cart->virtuemart_shipmentmethod_id; 
	     
	    
		if (!($this->selectedThisByMethodId($virtuemart_shipmentmethod_id))) {
			return "";
		}
		
		
		
		
		$to_address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
	    
		require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_shipping_permanent_cache.php'); 

		}
	}
	function &setCache(&$value)
	{
	  $arg_list = func_get_args();
	  $hash = ''; 
	  for ($i = 1; $i < count($arg_list); $i++) {
        $hash .= serialize($arg_list[$i]); 
	  }
	  $hash = md5($hash); 
	  vmPlugin::$qCache[$hash] = $arg_list[0]; 
	  return $value; 
	  /*
	  $cache = & JFactory::getCache();
	  $orig = $cache->getCaching(); 
	  $cache->setCaching( 1 );
	  $cache->store($value, $hash, 'opccache'); 
	  $cache->setCaching( $orig );
	  */
	  return $value; 
	}
	
	function &getCache()
	{
	  $f = false; 
	  return $f;
	  $arg_list = func_get_args();
	  $hash = ''; 
	  for ($i = 0; $i < count($arg_list); $i++) {
        $hash .= serialize($arg_list[$i]); 
	  }
	  $hash = md5($hash); 
	  if (isset(vmPlugin::$qCache[$hash])) 
	  {
	   // we have a match, let's measure it
	  $counta = vmPlugin::$ccount; 
	  if (empty($counta)) vmPlugin::$ccount = 1; 
	  else vmPlugin::$ccount++;
	  
	  return vmPlugin::$qCache[$hash]; 
	  }
	  $res = false; 
	  /*
	  $cache = & JFactory::getCache();
	  $orig = $cache->getCaching(); 
	  $cache->setCaching( 1 );
	  $res = $cache->get($hash, 'opccache'); 
	  $cache->setCaching( $orig );
	  */
	  return $res; 
	  
	  
	}
	/**
	* Checks if this plugin should be active by the trigger
	* @author Max Milbers
	* @author Valérie Isaksen
	* @param string $psType shipment,payment,custom
	* @param string the name of the plugin for exampel textinput, paypal
	* @param int/array $id the registered plugin id(s) of the joomla table
	*/
	
	
	function selectedThisByMethodId(  $id='type') {
		//echo '<br />selectedThisByMethodId:'.$id.' type:'.$this->_psType.' idName:'.$this->_idName.' name:'.$this->_name.'<br />'; 
		//selectedThisByMethodId:2 type:shipment idName:virtuemart_shipmentmethod_id
		//if($psType!=$this->_psType) return false;
		
		
		$db = JFactory::getDBO();
		
		if($id==='type'){
			return true;
		} else {
			$db = JFactory::getDBO();
			
			if (VmConfig::isJ15()) {
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__plugins AS j WHERE vm.`'.$this->_idName.'` = "'.$id.'"
							AND vm.'.$this->_psType.'_jplugin_id = j.id
							AND j.element = "'.$this->_name.'"';
			} else {
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__extensions AS j WHERE vm.`'.$this->_idName.'` = "'.$id.'"
							AND vm.'.$this->_psType.'_jplugin_id = j.extension_id
							AND j.element = "'.$this->_name.'"';
			}
			$x = vmPlugin::getCache('selectedThisByMethodId', $q); if (!empty($x)) return $x; 
			//echo 'selectedThisByMethod'.$this->_psType;
			$db->setQuery($q);
			if(!$res = $db->loadObject() ){
// 				//vmError('selectedThisByMethodId '.$db->getQuery());
				$res = false; 
				$x = vmPlugin::setCache($res, 'selectedThisByMethodId', $q); 
				return false; 
			} else {
				$x = vmPlugin::setCache($res, 'selectedThisByMethodId', $q); 
				return $res;
			}
		}
	}
/**
	* Checks if this plugin should be active by the trigger
	* @author Max Milbers
	* @author Valérie Isaksen
	* @param string the name of the plugin for exampel textinput, paypal
	* @param int/array $id the registered plugin id(s) of the joomla table
	*/
	protected function selectedThisByJPluginId(  $jplugin_id='type') {

		$db = JFactory::getDBO();

		if($jplugin_id==='type'){
			return true;
		} else {
			$db = JFactory::getDBO();

			if (VmConfig::isJ15()) {
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__plugins AS j WHERE vm.`'.$this->_psType.'_jplugin_id`  = "'.$jplugin_id.'"
							AND vm.'.$this->_psType.'_jplugin_id = j.id
							AND j.`element` = "'.$this->_name.'"';
			} else {
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__extensions AS j WHERE vm.`'.$this->_psType.'_jplugin_id`  = "'.$jplugin_id.'"
							AND vm.`'.$this->_psType.'_jplugin_id` = j.extension_id
							AND j.`element` = "'.$this->_name.'"';
			}
			
			$x = vmPlugin::getCache('selectedThisByJPluginId', $q);  if (!empty($x)) return $x; 
			
			$db->setQuery($q);
			if(!$res = $db->loadObject() ){
// 				vmError('selectedThisByMethodId '.$db->getQuery());
				$res = false; 
				return vmPlugin::setCache($res, 'selectedThisByJPluginId', $q);  
			} else {
				return vmPlugin::setCache($res, 'selectedThisByJPluginId', $q);  
				
			}
		}
	}

	/**
	 * Gets the id of the joomla table where the plugin is registered
	 * @author Max Milbers
	 */
	final protected function getJoomlaPluginId(){

		if(!empty($this->_jid)) return $this->_jid;
		$db = JFactory::getDBO();

		if (VmConfig::isJ15()) {
			$q = 'SELECT j.`id` AS c FROM #__plugins AS j
					WHERE j.element = "'.$this->_name.'" AND j.folder = "'.$this->_type.'"';
		} else {
			$q = 'SELECT j.`extension_id` AS c FROM #__extensions AS j
					WHERE j.element = "'.$this->_name.'" AND j.`folder` = "'.$this->_type.'"';
		}
		$x = vmPlugin::getCache('getJoomlaPluginId', $q);  if (!empty($x)) return $x; 
		
		$db->setQuery($q);
		$this->_jid = $db->loadResult();
		if(!$this->_jid){
			vmError('getJoomlaPluginId '.$db->getErrorMsg());
			$res = false; 
			return vmPlugin::setCache($res, 'getJoomlaPluginId', $q);
			return false;
		} else {
		    return vmPlugin::setCache($this->_jid, 'getJoomlaPluginId', $q);
			return $this->_jid;
		}
	}
/**
	 * Create the table for this plugin if it does not yet exist.
	 * Or updates the table, if it exists. Please be aware that this function is slowing and is only called
	 * storing a method or installing/udpating a plugin.
	 *
	 * @param string $psType shipment,payment,custom
	 * @author Valérie Isaksen
	 * @author Max Milbers
	 */
	protected function onStoreInstallPluginTableVM3 ($psType,$name=FALSE) {

		vmdebug('Executing onStoreInstallPluginTable ');

		if(!empty($name) and $name!=$this->_name){
			return false;
		}

		//Todo the psType should be name of the plugin.
		if ($psType == $this->_psType) {

			$SQLfields = $this->getTableSQLFields();
			if(empty($SQLfields)) return false;

			$loggablefields = $this->getTableSQLLoggablefields();
			$tablesFields = array_merge($SQLfields, $loggablefields);

			$db = JFactory::getDBO();
			$query = 'SHOW TABLES LIKE "%' . str_replace('#__', '', $this->_tablename) . '"';
			$db->setQuery($query);
			$result = $db->loadResult();

			if ($result) {
				$update[$this->_tablename] = array($tablesFields, array(), array());
				$app = JFactory::getApplication();
				$app->enqueueMessage(get_class($this) . ':: VirtueMart2 update ' . $this->_tablename);
				if (!class_exists('GenericTableUpdater'))
					require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
				$updater = new GenericTableUpdater();
				$updater->updateMyVmTables($update);
			} else {
				$query = $this->createTableSQL($name,$tablesFields);
				if(empty($query)){
					return false;
				} else {
					$db->setQuery ($query);
					if (!$db->execute ()) {
						vmWarn($this->_name . '::onStoreInstallPluginTable: ' . vmText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE));
						echo $this->_name . '::onStoreInstallPluginTable: ' . vmText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE);
					} else {
						return true;
					}
				}
			}

			/*$query = $this->getVmPluginCreateTableSQL ();

			if(!class_exists('GenericTableUpdater')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
			$updater = new GenericTableUpdater();

			if(empty($query)){
				return false;
			} else {
			//if ($query !== 0) {
				// 				vmdebug('onStoreInstallPluginTable '.$query);
				$db = JFactory::getDBO ();
				$db->setQuery ($query);
				if (!$db->execute ()) {
					vmWarn($this->_name . '::onStoreInstallPluginTable: ' . vmText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE));
					echo $this->_name . '::onStoreInstallPluginTable: ' . vmText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE);
				} else {
					return true;
				}
			}*/
		}
		return false;
	}
	/**
	* Create the table for this plugin if it does not yet exist.
	* @author Valérie Isaksen
	* @author Max Milbers
	*/
	protected function onStoreInstallPluginTable($psType, $name=FALSE) {
// stAn merge sept 2012
if(!empty($name) and $name!=$this->_name){
			return false;
		}
		
		if (OPCJ3) return $this->onStoreInstallPluginTableVM3($psType, $name); 
		
		if($psType==$this->_psType){
			$query = $this->getVmPluginCreateTableSQL();
			
			if(empty($query)){
				return false;
			} else {
				$db = JFactory::getDBO();
				$db->setQuery($query);
				if (!$db->query()) {
					JError::raiseWarning(1, $this->_name.'::onStoreInstallPluginTable: ' . JText::_('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr(true));
					echo $this->_name.'::onStoreInstallPluginTable: ' . JText::_('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr(true);
				}
else {
return true; 
}

			}
		}

return false;
	}


	function getTableSQLLoggablefields() {
		return array(
		    'created_on' => ' datetime NOT NULL default \'0000-00-00 00:00:00\'',
		    'created_by' => "int(11) NOT NULL DEFAULT '0'",
		    'modified_on' => ' datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
		    'modified_by' => "int(11) NOT NULL DEFAULT '0'",
		    'locked_on' => ' datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
		    'locked_by' => 'int(11) NOT NULL DEFAULT \'0\''
		);

	    }

   /**
	 * @param $tableComment
	 * @return string
	 */
	protected function createTableSQL ($tableComment,$tablesFields=0) {

		$query = "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (";
		if(!empty($tablesFields)){
			foreach ($tablesFields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
		} else {
			$SQLfields = $this->getTableSQLFields ();
			$loggablefields = $this->getTableSQLLoggablefields ();
			foreach ($SQLfields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
			foreach ($loggablefields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . ", ";
			}
		}

		$query .= "	      PRIMARY KEY (`id`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='" . $tableComment . "' AUTO_INCREMENT=1 ;";
		return $query;
	}
	
	/**
	 *
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $xParams
	 * @param $varsToPush
	 * @return bool
	 */
	protected function getTablePluginParams ($psType,$name, $id, &$xParams,&$varsToPush) {
		//vmdebug('getTablePluginParams $this->_psType '.$this->_psType.' sets $psType '.$psType.' $name',$name);
		if (!empty($this->_psType) and !$this->selectedThis ($psType, $name, $id)) {
			return FALSE;
		}
		//$x = $this->myClass(); 
		$varsToPush = $this->_varsToPushParam;
		$xParams = $this->_xParams;
		
		//vmdebug('getTablePluginParams '.$name.' sets xParams '.$xParams.' vars',$varsToPush);
	}

	function myClass()
	 {
	   $y = get_class($this); 
	   return $y; 
	 }
	/**
	 * Set with this function the provided plugin parameters
	 *
	 * @param string $paramsFieldName
	 * @param array $varsToPushParam
	 */
	function setConfigParameterable($paramsFieldName,$varsToPushParam){
	    //$x = $this->myClass(); 
		$this->_varsToPushParam = $varsToPushParam;
		//if (!OPCJ3)
		{
		
		if (!isset($this->_varsToPushParam[$this->_psType.'_logos']))
		$this->_varsToPushParam[$this->_psType.'_logos'] = array('', 'char'); 
		if (!isset($this->_varsToPushParam['weight_unit']))
		$this->_varsToPushParam['weight_unit'] = array('KG', 'char'); 
		
		if (!isset($this->_varsToPushParam['tax_id']))
		$this->_varsToPushParam['tax_id'] = array(-1, 'int'); 
		
		}
		$this->_xParams = $paramsFieldName;
	}

	protected function setOnTablePluginParams($name,$id,&$table){

		//Todo I think a test on this is wrong here
		//Adjusted it like already done in declarePluginParams
		if (!empty($this->_psType) and !$this->selectedThis ($this->_psType, $name, $id)) {
			return FALSE;
		}
		else {
		    //$x = $this->myClass(); 
			$table->setParameterable ($this->_xParams, $this->_varsToPushParam);
			return TRUE;
		}

	}

	/**
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	/**
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	protected function declarePluginParams ($psType, &$data) {

		//vmdebug('declarePluginParams ',$this->_psType,$data);
		if(!empty($this->_psType)){
			$element = $this->_psType.'_element';
			$jplugin_id = $this->_psType.'_jplugin_id';
			if(!isset($data->$element) or !isset($data->$jplugin_id)) return FALSE;
			if(!$this->selectedThis($psType,$data->$element,$data->$jplugin_id)){
				return FALSE;
			}
		}
		if (!class_exists ('VmTable')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtable.php');
		}
		if (OPCJ3)
		 {
		     //Is only used for the config tables!
		//VmTable::bindParameterable ($data, $data->_xParams, $this->_varsToPushParam);
		if(isset($this->_varsToPushParam)){
			if(isset($data->_varsToPushParam)){
				$data->_varsToPushParam = array_merge((array)$data->_varsToPushParam, (array)$this->_varsToPushParam);
			} else {
				$data->_varsToPushParam = (array)$this->_varsToPushParam;
			}
			//vmdebug(' vars to push',$data->_varsToPushParam);
			//$data->_varsToPushParam = $this->_varsToPushParam;
		} else{
			vmdebug('no vars to push?',$this);
		}
		 }
		 else
		 {
		if (!empty($this->_xParams))
		VmTable::bindParameterable ($data, $this->_xParams, $this->_varsToPushParam);
		 }
		 
		 if($this->_cryptedFields){
			$data->setCryptedFields($this->_cryptedFields);
		}
		return TRUE;

	}

	
	
	public function getVmPluginMethod($int, $cache=true){

	$class = $this->myClass(); 	  
	

	  if (OPCJ3)
	   {
	   
	   
	   //if ($this->_vmpCtable === 0 || !$cache) 
	   
	   {
			$db = JFactory::getDBO ();

			if (!class_exists ($this->_configTableClassName)) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . $this->_configTableFileName . '.php');
			}
			$this->_vmpCtable = new $this->_configTableClassName($db);
			if ($this->_xParams !== 0) {
				$this->_vmpCtable->setParameterable ($this->_configTableFieldName, $this->_varsToPushParam);
			}
			if($this->_cryptedFields){
				$this->_vmpCtable->setCryptedFields($this->_cryptedFields);
			}
		}
		
		$ret = $this->_vmpCtable->load ($int);
		
		
		  
		  
		  if (isset(vmPlugin::$xmlDefaults[$this->_name.'Form']))
		   {
		     $defaults = vmPlugin::$xmlDefaults[$this->_name.'Form']; 
			 foreach ($defaults as $key=>$name)
			  {
			   $this->$key = $name; 
			  }
		
		   }
		
		VmTable::bindParameterable ($ret, $this->_xParams, $this->_varsToPushParam);
		return $ret; 
	   
	   
	   }
	
	  
	   
	   $x = vmPlugin::getCache('getVmPluginMethod', $class.$int); 
	   {
	   $this->_vmpCtable = $x;
	   
	   // vm 2.0.20
	   if (!empty($x))
	   if (!isset($x->tax_id)) $x->tax_id = -1; 
	   
	   if (!empty($x)) return $x; 
	   }
	   $x = $this->selectedThisByMethodId($int); 
	   if (empty($x)) 
	   {
	   $res = false; 
	   return vmPlugin::setCache($res, 'getVmPluginMethod', $class.$int); 
	   }

	   
	   
	   
	    /*
		static $lastInt; 
		if (!empty($lastInt))
		{
		  if ($int != $lastInt) 
		   $refresh = true; 
		  else $refresh = false;
		}
		else 
		{
		  $refresh = true; 
		  $lastInt = $int; 
		}
		*/
		
		if (!class_exists ('VmTable')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtable.php');
		}

		
		if (empty($this->_vmpCtableAll)) $this->_vmpCtableAll = array(); 
		$type = $this->_psType; 
		
	    
		
		if((empty($this->_vmpCtableAll[$type.$int])))
		{
			$db = JFactory::getDBO();

			if(!class_exists($this->_configTableClassName))require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.$this->_configTableFileName.'.php');
			$this->_vmpCtableAll[$type.$int] = new $this->_configTableClassName($db);
			
			
			if ($this->_xParams !== 0) {
			    if (method_exists($this->_vmpCtableAll[$type.$int], 'setParameterable'))
				$this->_vmpCtableAll[$type.$int]->setParameterable($this->_xParams, $this->_varsToPushParam);
			}

			if($this->_cryptedFields){
			    if (method_exists($this->_vmpCtableAll[$type.$int], 'setCryptedFields'))
				$this->_vmpCtableAll[$type.$int]->setCryptedFields($this->_cryptedFields);
			}
			
			
			
		}
		
			
			
			
			
		// some plugins are missing some of the params:
		$this->_vmpCtableAll[$type.$int]->setParameterable($this->_xParams,$this->_varsToPushParam);
		
			
			
		
		
		$x = $this->_vmpCtableAll[$type.$int]->load($int);
		
		
		
		$this->_vmpCtable = $this->_vmpCtableAll[$type.$int];
		if (!empty($x))
		{
		if (!isset($x->payment_logos)) $x->payment_logos = ''; 
		if (!isset($x->cost_per_transaction)) $x->cost_per_transaction = 0; 
		if (!isset($x->cost_percent_total)) $x->cost_percent_total = 0; 
		 
		if (!isset($x->tax_id)) $x->tax_id = -1; 
		if (!isset($x->weight_unit)) $x->weight_unit = 'KG'; 
		
		
		}
		return vmPlugin::setCache($x, 'getVmPluginMethod', $class.$int);
	}

	protected function storeVmPluginMethod () {

	}
	
	/**
	 * This stores the data of the plugin, attention NOT the configuration of the pluginmethod,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @author Max Milbers
	 * @param array  $values array or object with the data to store
	 * @param string $tableName When different then the default of the plugin, provid it here
	 * @param string $tableKey an additionally unique key
	 */
	protected function storePluginInternalData (&$values, $primaryKey = 0, $id = 0, $preload = FALSE) {

		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		
		//vmdebug('storePluginInternalData',$value);
		$this->_vmpItable->bindChecknStore ($values, $preload);
		
		$errors = $this->_vmpItable->getErrors ();
		
		
		if (!empty($errors)) {
			foreach ($errors as $error) {
				vmError ($error);


			}
		}
		
		return $values;

	}
	
	
	/**
	 * This loads the data stored by the plugin before, NOT the configuration of the method,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @param int    $id
	 * @param string $primaryKey
	 */
	protected function getPluginInternalData ($id, $primaryKey = 0) {
		$x = vmPlugin::getCache('getPluginInternalData', $this->_vmpItable, $id, $this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable); 
		
		
		
		if (!empty($x)) 
		{
		//OPCloader::opcDebug('cache is_active:'); 
		//OPCloader::opcDebug($x->is_active); 
		 return $x; 
		}
		
		if (isset($this->_vmpItable))
		$vmpItableStored = $this->_vmpItable; 
		else $vmpItableStored = null; 
		
		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		//if ($this->_vmpItable === 0) 
		{
		
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		
		// 		vmdebug('getPluginInternalData $id '.$id.' and $primaryKey '.$primaryKey);
		//$ret = $this->_vmpItable->clear();
		$ret = $this->_vmpItable->load ($id);
		vmPlugin::setCache($ret, 'getPluginInternalData',$vmpItableStored, $id, $this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable); 
		//OPCloader::opcDebug($vmpItableStored); 
		//OPCloader::opcDebug('is_active:'); 
		//OPCloader::opcDebug($ret); 
		
		return $ret; 
		
	}


	

	/**
	 * @param      $tableName
	 * @param      $tableFields
	 * @param      $primaryKey
	 * @param      $tableId
	 * @param bool $loggable
	 * @return VmTableData
	 */
	protected function createPluginTableObject ($tableName, $tableFields, $primaryKey, $tableId, $loggable = FALSE) {

		if (!class_exists ('VmTableData')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtabledata.php');
		}
		$db = JFactory::getDBO ();
		$table = new VmTableData($tableName, $tableId, $db);
		foreach ($tableFields as $field) {
			$table->$field = 0;
		}
		/*
		if (substr($tableName, 0, 6) == 'TableS')
		if (!isset($table->shipment_logos)) $table->shipment_logos = ''; 
		if (substr($tableName, 0, 6) == 'TableP')
		if (!isset($table->payment_logos)) $table->payment_logos = ''; 
		
		if (!isset($table->cost_per_transaction)) $table->cost_per_transaction = 0; 
		if (!isset($table->cost_percent_total)) $table->cost_percent_total = 0; 
		*/
		if ($primaryKey !== 0) {
			$table->setPrimaryKey ($primaryKey);
		}
		if ($loggable) {
			$table->setLoggable ();
		}
		
		if($this->_cryptedFields){
			$this->_vmpCtable->setCryptedFields($this->_cryptedFields);
		}
		
		if (!OPCJ3)
		if (!$this->_tableChecked) {
			$this->onStoreInstallPluginTable ($this->_psType);
			$this->_tableChecked = TRUE;
		}

		return $table;
	}

	/**
	 * @param     $id
	 * @param int $primaryKey
	 * @return mixed
	 */
	protected function removePluginInternalData ($id, $primaryKey = 0) {
		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		vmdebug ('removePluginInternalData $id ' . $id . ' and $primaryKey ' . $primaryKey);
		return $this->_vmpItable->delete ($id);
	}
	
	public function loadPluginJavascriptOPC(&$cart, &$plugins, &$html)
	{
	  
	   if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
 	  

	  
	  $arr = array('payment', 'shipment'); 
	  if (!in_array($this->_psType, $arr)) return; 
	  

	  
	  $nmethods = $this->getPluginMethodsOPC ($cart->vendorId);
	  if (empty($this->methods))
	    {
			return;
		}
	 
	 if (!empty($this->methods))
		foreach ($this->methods as &$method)
		{
		  $m->opcref =& $this; 
		  $plugins[] =& $method;   
		
		 
	     OPCTransform::getOverride('opc_javascript', $this->_name, $this->_psType, $this, $method); 
		
		/*
		$name = $this->_name;
		$psType  = $this->_psType; 
		
		static $theme; 
		if (empty($theme))
		{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		$theme = $selected_template; 
		}
		
		if ($psType === NULL) {
			$psType = $this->_psType;
		}
		
		$layout_name = 'opc_javascript'; 
		
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php'))
		 {
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		   $isset = true; 
		 }
		 else
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php'))
		 {
		  		   $isset = true; 
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		 }
		 
		 
		 
		 if (!empty($layout)) 
		 {
		 ob_start(); 
		 include($layout); 
		 $html .= ob_get_clean(); 
		 }
		 */
		}
		
	}
	
	/**
	 * Get the path to a layout for a type
	 *
	 * @param   string  $type  The name of the type
	 * @param   string  $layout  The name of the type layout. If alternative
	 *                           layout, in the form template:filename.
	 * @param   array   $viewData  The data you want to use in the layout
	 *                           can be an object/array/string... to reuse in the template
	 * @return  string  The path to the type layout
	 * original from libraries\joomla\application\module\helper.php
	 * @since   11.1
	 * @author Patrick Kohl, Valérie Isaksen
	 */
	public function renderByLayout ($layout_name = 'default', $viewData = NULL, $name = NULL, $psType = NULL) {
		if ($name === NULL) {
			$name = $this->_name;
		}
		static $theme; 
		if (empty($theme))
		{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		$theme = $selected_template; 
		}
		
		if ($psType === NULL) {
			$psType = $this->_psType;
		}
		$layout = vmPlugin::_getLayoutPath ($name, 'vm' . $psType, $layout_name);
		jimport('joomla.filesystem.file');
		$psType = strtolower($psType); 
		$psType = JFile::makeSafe($psType); 
		$isset = false; 
		//echo  JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php'))
		 {
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		   $isset = true; 
		 }
		 else
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php'))
		 {
		  		   $isset = true; 
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		 }
		if (!$isset)
		if (strpos($layout, 'payment_form')!==false)
		if (strpos($layout, 'klarna')!==false)
		 {
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.'klarna'.DS.'payment_form.php'; 
		 }
		 if (is_array($viewData))
		 if (!empty($viewData['paymnentForm']) && ($viewData['paymentForm']=='#paymentForm'))
		 {
		   $viewData['paymnentForm'] = '#adminForm'; 
		 }
		 if (!$isset)
		 if ((strpos($layout, 'javascript')!==false) && ($name=='stripe'))
		 {
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.'stripe'.DS.'javascript.php'; 
		 }
		
		 if (strpos($layout, 'display_payment')!==false)
		 if (strpos($layout, 'ddmandate')!==false)
		 {
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.'ddmandate'.DS.'payment_form.php'; 
		 }
		ob_start ();
		include ($layout);
		return ob_get_clean ();

	}
	
	public function __call($method, $arguments)
    {
		if (method_exists($this, $method))
        return $this->__call($method, $arguments);
	    else return null; 
    }
	
	
	/**
	 *  Note: We have 2 subfolders for versions > J15 for 3rd parties developers, to avoid 2 installers
	 *
	 * @author Patrick Kohl, Valérie Isaksen
	 */
	private function _getLayoutPath ($pluginName, $group, $layout = 'default') {
		$app = JFactory::getApplication ();
		// get the template and default paths for the layout
		if (JVM_VERSION >= 2) {
			$templatePath = JPATH_SITE . DS . 'templates' . DS . $app->getTemplate () . DS . 'html' . DS . $group . DS . $pluginName . DS . $layout . '.php';
			$defaultPath = JPATH_SITE . DS . 'plugins' . DS . $group . DS . $pluginName . DS . $pluginName . DS . 'tmpl' . DS . $layout . '.php';
		}
		else {
			$templatePath = JPATH_SITE . DS . 'templates' . DS . $app->getTemplate () . DS . 'html' . DS . $group . DS . $pluginName . DS . $layout . '.php';
			$defaultPath = JPATH_SITE . DS . 'plugins' . DS . $group . DS . $pluginName . DS . 'tmpl' . DS . $layout . '.php';
		}


		// if the site template has a layout override, use it
		jimport ('joomla.filesystem.file');
		if (JFile::exists ($templatePath)) {
			return $templatePath;
		}
		else {
			return $defaultPath;
		}
	}
	public static function pBS($die=true)
	{
		$x = debug_backtrace(); 
		echo 'Bakctrace'."<br />\n"; 
		foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />\n"; 
		if ($die)
		{
		JFactory::getApplication()->close(); 
	    die(); 
		}
	}
	/*
	function plgVmOnSelectedCalculatePriceShipmentOPC (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) 
	{
		// if ($cart->virtuemart_shipmentmethod_id==10)
			{
				$cart_prices['shipmentTax'] = 1;
		$cart_prices['shipmentValue'] = 10; 
		
		return true; 
			}
		if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		 
		
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return FALSE;
		}
		

		$cart_prices['shipmentTax'] = 1;
		$cart_prices['shipmentValue'] = 10; 
		
		return true; 

		
		
	}
    */
	
}
