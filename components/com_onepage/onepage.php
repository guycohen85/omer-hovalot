<?php 
/*
 * This file is here for broader compatibility with Joomla system
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
*/
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 


// disable cache for all one page pages
if (class_exists('JCache'))
{
 $options = array(
			'defaultgroup'	=> 'page',
			'browsercache'	=> false,
			'caching'		=> false,
		);
 $caching = JCache::getInstance('page', $options);
 $caching->setCaching(false);
}

$task = JRequest::getVar('task', ''); 
if ($task == 'loadjs')
{
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
  $file = JRequest::getVar('file', ''); 
  if (!empty($file))
  {
   OPCmini::loadJSfile($file); 
   $app  = JFactory::getApplication(); 
   $app->close(); 
   die(); 
  }
}
else
if ($task == 'ping')
{
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
  OPCtrackingHelper::ping(); 
   $app  = JFactory::getApplication(); 
   $app->close(); 
   die(); 
}
//index.php?option=com_onepage&task=loadjs&file=onepage.js
$memstart = memory_get_usage(true); 
define('OPCMEMSTART', $memstart); 

include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 


{
if (!isset($opc_memory)) $opc_memory = '128M'; 
ini_set('memory_limit',$opc_memory);
ini_set('error_reporting', 0);
// disable error reporting for ajax:
error_reporting(0); 
}



if (!empty($opc_calc_cache))
		   {  
			 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 
		     OPCcache::installCache(); 
		   }


// since 2.0.109 we need to load com_onepage instead of com_virtuemart becuase of captcha support 
JRequest::setVar('option', 'com_virtuemart'); 
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
$task = JRequest::getVar('task', ''); 

$view = JRequest::getVar('view', ''); 
if ($view == 'xmlexport')
{

require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'xmlexport.php'); 
$VirtueMartControllerXmlexport = new VirtueMartControllerXmlexport(); 
if ($VirtueMartControllerXmlexport->enabled)
$VirtueMartControllerXmlexport->createXml(); 

}
else
if ($view=='orderexport')
{
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'orderexport.php'); 
$VirtueMartControllerOrderexport = new VirtueMartControllerOrderexport();
$VirtueMartControllerOrderexport->process(); 
$app = JFactory::getApplication()->close(); 
}
else
{
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'virtuemart.cart.view.html.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php'); 
JRequest::setVar('option', 'com_onepage'); 
$task = JRequest::getVar('task', ''); 
}