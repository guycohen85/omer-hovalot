<?php
/**
 * @version		$Id: tracking.php 
 * @package		tracking model for opc
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class JModelTracking extends OPCModel
{

  function store()
  {
    $enabled = JRequest::getVar('adwords_enabled_0', false); 
	 
	 $order = JRequest::getInt('tracking_order', 9999); 
	 
    $this->setEnabled($enabled, $order); 

    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'config.php'); 
	if (!OPCJ3)
	{
	 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opcparameters.php'); 
	}
	else
	{
	   require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'jformrender.php'); 
	}
	
	
	
	
	 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $files = $config->getPhpTrackingThemes();
		 $statuses = $config->getOrderStatuses();
		 $data = JRequest::get('post');
		  jimport('joomla.filesystem.file');
     foreach ($files as $file)
	 {
	  
	   
	  
	   $file = JFile::makeSafe($file);
	
	   $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.$file.'.xml'; 
	   $nd = new stdClass(); 
	   
	   //$params = new OPCparameters($nd, $file, $path, 'opctracking'); 
	   $config = OPCconfig::buildObject($data[$file]); 

	   $key = 'enabled'; 
	   $is_enabled = (int)JRequest::getVar('plugin_'.$file, -1); 
	   $config->$key = $is_enabled; 
	   
	   if ($file == 'adwordstracking')
	   {
	     //var_dump($config); die(); 
	   }
	   
	   OPCconfig::store('tracking_config', $file, 0, $config); 
	   
	   if (false)
	   foreach ($data[$file] as $key=>$param)
	    {
		  echo $key.' '.$param; 
		}
	   
	   
	 }
	 
	 $aba = JRequest::getVar('aba_enabled', false); 
	 if (!empty($aba)) $aba = true; 
	 
	 OPCconfig::store('aba', '', 0, $aba); 
	 
	 
	
	  
	
	 //echo $file; 
	 //var_dump($is_enabled); die(); 
	 
	 foreach ($statuses as $status)
	 {
	    $status2 = $status['order_status_code']; 
		
		$default = new stdClass(); 
		$config = OPCconfig::getValue('tracking', $status2, 0, $default); 
	    
		
		
		
		
	    foreach ($files as $file)
		{
		
		if (!empty($config->$file))
		$wasEnabled = true; 
		else $wasEnabled = false; 
		
		  $enabled = JRequest::getVar($file.'_'.$status2, 0); 
		  
		  
		   $t1 = JRequest::getVar('plugin_'.$file, -1); 
	  $is_enabled = (int)JRequest::getVar('plugin_'.$file, -1); 
	
		  
		  
	   
	   if (empty($is_enabled))
	   {
	     $enabled = false; 
		 $key = $file.'_enabled'; 
		 $config->$key = false; 
	   }
	   else
	   {
	    $key = $file.'_enabled'; 
	    $config->$key = true; 
	   }
	   
		  
		  
		  $config->$file = $enabled; 
		  
		  $key = 'since'.$file; 
		  
		  $ct2 = new stdClass(); 
		  //$ct = OPCconfig::getValue('tracking', $status2, 0, $ct2); 
		  
		  // stAn - do not ovewrite since time when not altering status
		  if (!$wasEnabled)
		  if ($enabled)
		  if (empty($ct2->$key))
		  $config->$key = time(); 
		  
		  // default is zero: 
		  if (!isset($config->$key)) $config->$key = 0; 
		  
		}
		$config->code = JRequest::getVar('adwords_code_'.$status2, '', 'post', 'string', JREQUEST_ALLOWRAW); 
		$only_when = JRequest::getVar('opc_tracking_only_when_'.$status2, ''); 
		$config->only_when = $only_when; 
		OPCconfig::store('tracking', $status2, 0, $config); 
	 }
	 
	 $negative_statuses = JRequest::getVar('negative_statuses', array());
	 if (is_array($negative_statuses))
	  {
		//var_dump($negative_statuses); die(); 	 
        OPCconfig::store('tracking_negative', 'negative_statuses', 0, $negative_statuses);	    
	  }

	 
	 
	 
	 
	 
	   
	   
	 return;
  }
  
  function isPluginEnabled($file, &$config)
  {
     $enabled = false; 
	 foreach ($config as $status=>$c)
					 {
					    if (!empty($c->$file)) 
						$enabled = true; 
					 }
     if ($enabled) return true; 
	
     $default = new stdClass(); 
	 $ic = OPCconfig::getValue('tracking_config', $file, 0, $default); 
	 if (empty($ic->enabled)) return false; 
	 else return true; 

     return false; 	 
     				
					
  }
  
  function setEnabled($enabled = null, $order = null)
  {
   if (is_null($enabled))
  $enabled = JRequest::getVar('adwords_enabled_0', false); 
  
   $order = JRequest::getInt('tracking_order', 9999); 
  
       require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	 if (!OPCmini::tableExists('virtuemart_plg_opctracking'))
	 {
	   $db = JFactory::getDBO(); 
	   $q = '
	CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opctracking` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`virtuemart_order_id` int(11) NOT NULL,
	`hash` varchar(32) NOT NULL,
	`shown` text NOT NULL,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`created_by` int(11) NOT NULL,
	`modified` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	`modified_by` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `hash_2` (`hash`,`virtuemart_order_id`),
	KEY `virtuemart_order_id` (`virtuemart_order_id`),
	KEY `hash` (`hash`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;'; 
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
	 }
  
   //update from prior opc versions: 
	 $db = JFactory::getDBO(); 
     $q = "delete from `#__extensions` WHERE  element = 'opctracking' and folder = 'system' "; 
     $db->setQuery($q); 
	 $db->query(); 
  
     
	 if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'))
	    {
		
		   jimport('joomla.filesystem.folder');
		   jimport('joomla.filesystem.file');
		   if (JFolder::create(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking')!==false)
		   {
		   JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'install'.DS.'opctracking'.DS.'opctracking.php', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'opctracking.php');  
		   JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'install'.DS.'opctracking'.DS.'opctracking.xml', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'opctracking.xml'); 
		   JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'install'.DS.'opctracking'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'index.html'); 
		   }
		   else return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'); 
		}
	 
	 if (!empty($enabled))
	   {
	   
	      $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder='vmpayment' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled))
		   {
		      $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, 'plg_vmpayment_opctracking', 'plugin', 'opctracking', 'vmpayment', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_vmpayment_opctracking\",\"type\":\"plugin\",\"creationDate\":\"December 2013\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout Affiliate Tracking support for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', '".$order."', 0) "; 
		      $db->setQuery($q); 
		      $db->query(); 
			  $e = $db->getErrorMsg(); if (!empty($e)) return $e; 
		   }
		   else
		   {
		      //if (empty($isInstalled['enabled']))
			  {
			  
		    $order = JRequest::getVar('tracking_order', null); 
			if (!empty($order))
			{
		     $q = " UPDATE `#__extensions` SET  `enabled` =  '1', `ordering`='".$order."', state = 0 WHERE  element = 'opctracking' and folder = 'vmpayment' "; 
			 $db->setQuery($q); 
			 $db->query(); 
			 $e = $db->getErrorMsg(); if (!empty($e)) { return $e;  }
			}
			
		     

			  }
			  
			  
		   }
		  
	   }
	   else
	   {
	      
			  
			$db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder='vmpayment' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  if (!empty($isInstalled))
		  {
		    $db = JFactory::getDBO(); 
		    $q = " UPDATE `#__extensions` SET  enabled =  '0', ordering='".$order."', state = 0 WHERE  element = 'opctracking' and folder = 'vmpayment' "; 
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
		  }
			
		 

			  
	   }
	   
	   return ''; 
  }
  
  function getAba()
  {
     $ret = OPCconfig::getValue('aba', '', 0, false); 
	 
	 return $ret; 
  }
  
  function isEnabled($order=false)
  {
    $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder='vmpayment' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  
	if (!$order)
	{
		  if (empty($isInstalled)) return false; 
		  if (!empty($isInstalled['enabled'])) return true; 
	return false; 
	}
	else
	{
	  if (empty($isInstalled)) return 9999; 
	  return $isInstalled['ordering']; 
	}
  }
  function getStatusConfig($statuses)
  {
     $arr = array(); 
     foreach ($statuses as $status)
	 {
	   if (!isset($status->order_status_code))
	   {
	     //var_dump($status); die(); 
	   }
	   $status2 = $status['order_status_code']; 
	   $default = new stdClass(); 
	   $default->code = ''; 
	   $default->only_when = ''; 
	   $default->since = time(); 
	   $arr[$status2] = OPCconfig::getValue('tracking', $status2, 0, $default); 
	 }
	 return $arr; 
  }
  
  function showOrderVars()
  {
    $array = array(); 
	$object = new stdClass(); 
	
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
	OPCtrackingHelper::getOrderVars(0, $array, $object, true); 
    
  }
  function getOrderVars(&$named)
  {
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
	 $array = array(); 
	 $object = new stdClass(); 
	 $named = array(); 
	 OPCtrackingHelper::getOrderVars(0, $array, $object, false, $named); 
	 
	 
	 
	 return $array; 
  }
  
  function getJforms($files)
    { 
	
	 
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	  if (!OPCJ3)
	  {
	 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opcparameters.php'); 
	  }
	  else
	  {
	  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'jformrender.php'); 
	  }
	 $ret = array(); 
	 foreach ($files as $file)
	 {
	   $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.$file.'.xml'; 
	   if (!file_exists($path)) continue; 
	   
	   
	   $default = new stdClass();  
	   //$data->adwords_id = 1; 
	   $data = OPCconfig::getValue('tracking_config', $file, 0, $default);
	   
	   $title = $description = ''; 
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   
	   if (!OPCJ3)
	   {
	    $params = new OPCparameters($data, $file, $path, 'opctracking'); 
	    $test = $params->vmRender($file); 
	   }
	   else
	   {
	   
	   
	   
	   	   $xml = file_get_contents($path); 
		$xml = str_replace('extension', 'form', $xml); 
		$xml = str_replace('params', 'fieldset', $xml); 
		$xml = str_replace('<fieldset', '<fields name="'.$file.'"><fieldset name="test" label="'.$title.'" ', $xml); 
		$xml = str_replace('param', 'field', $xml); 
		$xml = str_replace('</fieldset>', '</fieldset></fields>', $xml); 
		//$fullxml = simplexml_load_string($xml);

		
		// removes BOM: 
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $xml);
		if (!empty($text)) $xml = $text; 

		$t1 = simplexml_load_string($xml); 
		if ($t1 === false) continue; 
		
	    $test = JForm::getInstance($file, $xml, array(),true);
		
		//$test->bind($data); 
		foreach ($data as $k=>$vl)
		{
		  $test->setValue($k, $file, $vl); 
		}
		//debug_zval_dump($test); 
		$fieldSets = $test->getFieldsets();
		//var_dump($fieldSets); die(); 
	    //$test->load($fullxml);
		
		$test = OPCparametersJForm::render($test); 
		//debug_zval_dump($testf); die(); 
		
		//var_dump($test); die(); 
		//$test->bind($payment);
	   }
	   
	   
	   
	   
	   
	   $ret[$file]['params'] = $test;
		if (empty($title))
	   $ret[$file]['title'] = $file.'.php'; 
	    else $ret[$file]['title'] = (string)$title; 

		
	   
	    $ret[$file]['description'] = (string)$description; 
		
		
	 }
	 return $ret; 
	}
}
