<?php

/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class OPCmini
{
 function loadJSfile($file)
 {
   jimport('joomla.filesystem.file');
   $file = JFile::makeSafe($file); 
   $pa = pathinfo($file); 
   $fullpath = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'js'.DS.$file; 
   if (!empty($pa['extension']))
   if ($pa['extension']=='js')
    {
	 //http://php.net/manual/en/function.header.php 
	if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
		@header("Content-type: text/javascript");
		@header("Content-Disposition: inline; filename=\"".$file."\"");
		//@header("Content-Length: ".filesize($fullpath));
	} else {
		@header("Content-type: application/force-download");
		@header("Content-Disposition: attachment; filename=\"".$file."\"");
		//@header("Content-Length: ".filesize($fullpath));
	}
	@header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
	if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
	@header("Cache-Control: no-cache");
	@header("Pragma: no-cache");
    }
	//include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'js'.DS.$file);
	echo file_get_contents($fullpath); 
	$doc = JFactory::getApplication(); 
	$doc->close(); 
    die(); 

	}
	
	
 }
 
 public static function isSuperVendor(){

	if (JVM_VERSION <= 2)
			{
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				return true; 
				
			}
			}
			else
			{
			 $text = '';
			$user = JFactory::getUser();
			if($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart') or VmConfig::isSuperVendor()) {
			  return true; 
			}
			}
			
			return false; 
	}
 
   
   static function tableExists($table)
  {
   static $cache; 
   
   $db =& JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   if (isset($cache[$table])) return $cache[$table]; 
   
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (empty($cache)) $cache = array(); 
	   
	   if (!empty($r)) 
	    {
		$cache[$table] = true; 
		return true;
		}
		$cache[$table] = false; 
   return false;
  }

     // moved from opc loaders so we do not load loader when not needed
	static $modelCache; 
   	public static function getModel($model)
	 {
	 
	 // make sure VM is loaded:
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
		if (empty(OPCmini::$modelCache)) OPCmini::$modelCache = array(); 
	    if (!empty(OPCmini::$modelCache[$model])) return OPCmini::$modelCache[$model]; 
		
		
	    if (!class_exists('VirtueMartModel'.ucfirst($model)))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . strtolower($model).'.php');
		if ((method_exists('VmModel', 'getModel')))
		{
		
		$Omodel = VmModel::getModel($model); 
		OPCmini::$modelCache[$model] = $Omodel; 
		return $Omodel; 
		}
		else
		{
			// this section loads models for VM2.0.0 to VM2.0.4
		   $class = 'VirtueMartModel'.ucfirst($model); 
		   if (class_exists($class))
		    {
				
				if ($class == 'VirtueMartModelUser')
				{
				
				//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'user.php'); 
				//$class .= 'Override'; 
				
				 $Omodel = new VirtueMartModelUser; 
				 
				 return $Omodel; 
				 $Omodel->setMainTable('virtuemart_vmusers');
				 
				}
				
				
			    $Omodel = new $class(); 
				
			  OPCmini::$modelCache[$model] = $Omodel; 
			  return $Omodel; 
			}
			else
			{  
			  echo 'Class not found: '.$class; 
			  $app = JFactory::getApplication()->close(); 
			}
			
		}
		echo 'Model not found: '.$model; 
		$app = JFactory::getApplication()->close(); 
		
		//return new ${'VirtueMartModel'.ucfirst($model)}(); 
	 
	 }	
	 
	 public static function slash($string, $insingle = true)
	 {
	    $string = str_replace("\r\r\n", " ", $string); 
   $string = str_replace("\r\n", " ", $string); 
   $string = str_replace("\n", " ", $string); 
   $string = (string)$string; 
   if ($insingle)
    {
	 $string = addslashes($string); 
     $string = str_replace('/"', '"', $string); 
	 return $string; 
	}
	else
	{
	  $string = addslashes($string); 
	  $string = str_replace("/'", "'", $string); 
	  return $string; 
	}
	 
	 }


 
}