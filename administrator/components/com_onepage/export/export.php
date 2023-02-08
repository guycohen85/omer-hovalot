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
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
// JPATH_BASE should point to Joomla!'s root directory
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);
$path = realpath(dirname(__FILE__) .'/../../../../'); 
//localhost hack: 
if ($path == '/srv/www/rupostel.com/web/vm2') $path = '/srv/www/rupostel.com/web/vm2onj25'; 
define( 'JPATH_BASE',  $path) ;
define( 'DS', DIRECTORY_SEPARATOR );

 


if (file_exists(JPATH_BASE. '/defines.php')) {
	include_once JPATH_BASE . '/defines.php';
}

if (!defined('_JDEFINES')) {	
	require_once JPATH_BASE.'/includes/defines.php';
}



$_POST = $_GET = $_REQUEST = array(); 
$_SERVER['REQUEST_METHOD'] = 'GET'; 
$_SERVER['HTTP_HOST'] = 'localhost'; 
$_SERVER["QUERY_STRING"] = "option=com_onepage&view=xmlexport";
$_SERVER["REQUEST_SCHEME"]='http'; 

$_SERVER["DOCUMENT_ROOT"] = JPATH_SITE; 
$_SERVER['SCRIPT_FILENAME'] = JPATH_SITE.DS.'index.php'; 
$_SERVER['SCRIPT_NAME'] = '/index.php'; 
$_SERVER["PHP_SELF"] = '/index.php'; 
$_SERVER['HTTP_USER_AGENT'] = 'Cron'; 



require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$app = JFactory::getApplication('site');
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);
$app->initialise();

//disable buffer: 
echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); 
define('JPATH_COMPONENT', JPATH_SITE.DS.'components'.DS.'com_onepage'); 

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 



JRequest::setVar('view', 'xmlexport'); 
JRequest::setVar('option', 'com_onepage'); 
JRequest::setVar('format', 'opchtml'); 
JRequest::setVar('tmpl', 'component'); 
JRequest::setVar('template', 'system'); 


//ini_set('memory_limit','256Mb');


require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'xmlexport.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'xmlexport.php'); 
$livesite = OPCconfig::getValue('xmlexport_config', 'xml_live_site', 0, ''); 

$livesite = substr($livesite, strpos($livesite, '//')+2, -1); 
$_SERVER['HTTP_HOST'] = $livesite; 



require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'xmlexport.php'); 
	  
$VirtueMartControllerXmlexport = new VirtueMartControllerXmlexport(); 
$VirtueMartControllerXmlexport->createXml(); 
echo "\n"; 
$app->close(); 
die(0); 	  



