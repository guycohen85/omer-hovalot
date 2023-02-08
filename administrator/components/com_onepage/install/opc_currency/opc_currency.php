<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemOpc_currency extends JPlugin
{
    function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
    function onAfterRoute()
	{
	 $app = JFactory::getApplication(); 
	 if ($app->isAdmin()) return; 
	 
	 if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
	 
	 if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php')) return; 
	 
	 include_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php');
	 
	 
	 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	 $can_change = OPCconfig::getValueNoCache('currency_config', 'can_change', 0, true); 
	 
	 
	 if (empty($can_change))
	 {
	 $ci = JRequest::getVar('virtuemart_currency_id'); 
	 
	 $session = JFactory::getSession(); 
	 if (!empty($ci))
	  {
	    //currency was set elsewhere
		
		$session->set('opc_currency', $ci); 
		return; 
	  }
	  $ci2 = $session->get('opc_currency');
      if (!empty($ci2)) return; 	  
	 }
	 
	 
	 //debug: 
	 if ($_SERVER['REMOTE_ADDR'] == '192.168.122.122')
	 $_SERVER['REMOTE_ADDR'] = '92.240.237.203'; 
	 
	 if (class_exists('geoHelper')) 
	 $c2c = geoHelper::getCountry2Code(); 
	 
	 
	 
	 if (empty($c2c)) return; 
	 
	 
	 
	 
	 
	 $default = 0; 
	 $c_int = OPCconfig::getValueNoCache('currency_config', $c2c, 0, $default); 
	
	 if (empty($c_int)) return; 
	 $c_int = (int)$c_int; 
	 
	 
	 
	 // set global request variable
	 JRequest::setVar('virtuemart_currency_id', $c_int); 
	 
	 $app->setUserState('virtuemart_currency_id', $c_int); 
	 $app->setUserState('com_virtuemart.virtuemart_currency_id', $c_int); 
	 
	 
	// $virtuemart_currency_id = $app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getVar('virtuemart_currency_id',$currencyDisplay->_vendorCurrency) );
	 
	}
		
}
