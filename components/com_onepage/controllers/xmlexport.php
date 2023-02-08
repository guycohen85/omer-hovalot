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

jimport('joomla.application.component.controller');

class VirtueMartControllerXmlexport extends OPCController {
  
  
  var $enabled = false; 
  
  public function __construct() {
	parent::__construct();
	
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
    $enabled = OPCconfig::getValue('xmlexport_config', 'xml_general_enable', 0, false); 
	if (empty($enabled)) die('XML Export not enabled'); 
	$this->enabled = $enabled; 

	
  }
  
  public function createXml($loadfile='')
   {
   
      require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'xmlexport.php'); 
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'xmlexport.php'); 
	  
	  if (!function_exists('simplexml_load_file')) return; 
	  
	  $xmlexport = new JModelXmlexport(); 
	  
	  //$this->addModelPath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_onepage' . DS . 'models' );
      //$xmlexport = $this->getModel('xmlexport'); 
	  
	  $files = $xmlexport->getPhpExportThemes(); 
	  
	  $general = new stdClass(); 
	  $xmlexport->getGeneral($general); 
	  OPCXmlExport::$config = $general; 
	  
	  // single file creation: 
	  $onlyf = JRequest::getVar('file', '');
	  
	  $arr2 = array(); 
	  $langs = array(); 
	  foreach ($files as $k=>$f)
	   {
		   $config = $xmlexport->getThemeConfig($f); 
		   
		   if ((empty($config)) || (empty($config->enabled)))
		   continue; 
		   else
		   {
		    $arr2[$k]['file'] = $f; 
		    $arr2[$k]['config'] = $config; 
		    $langs[$config->language] = $config->language; 
		   }
	   }
	   
	   
	foreach ($arr2 as $x)
	{
	   $file = $x['file']; 
	   
	   // special case: 
	   if (!empty($loadfile))
	   if ($loadfile != $file) continue; 
	   
	   if (!empty($onlyf) && ($onlyf != $file)) continue; 
	   
	   $config = $x['config']; 
	   jimport('joomla.filesystem.file');
	   $file = JFile::makeSafe($file);
	   $xmlpath = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'xmlexport'.DS.'php'.DS.$file.'.xml'; 
	   $phppath = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'xmlexport'.DS.'php'.DS.$file.'.php'; 
	   
	   
	   
	   if (!file_exists($phppath)) continue; 
	   

	    
	   
	   
	   $xml = simplexml_load_file($xmlpath);
	   if (!empty($xml->element))
	   {
	     $class = ucfirst(strtolower($xml->element)).'Xml'; 
	   }
	   else
	   {
	     $class = ucfirst(strtolower($file)).'Xml'; 
	   }
	   
	   if (!class_exists($class))
	   include($phppath); 
	   
	   if (!class_exists($class)) continue; 
	   OPCXmlExport::addClass($class, $config, $xml, $file); 
	   
	   if (!empty($loadfile)) 
	   {
	   return OPCXmlExport::$classes[$class];
	   
	   }
	   
	   
	}
	
	if (empty($loadfile))
	OPCXmlExport::doWork($langs); 
	
	   
   }
}