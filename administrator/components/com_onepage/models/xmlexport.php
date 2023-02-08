<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Cache Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since		1.6
 */
class JModelXmlexport extends OPCModel
{

  private function storeGeneral()
  {
      $enabled = JRequest::getVar('xml_general_enable', false); 
     if (!empty($enabled)) $enabled = true; 
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	 OPCconfig::store('xmlexport_config', 'xml_general_enable', 0, $enabled); 
	 
	 $path = JRequest::getVar('xml_export_path', 'export'); 
	 
	 $path = str_replace('/', DS, $path); 
	 $path = str_replace('\\', DS, $path); 
	 if (substr($path, -1) != DS) $path .= DS; 
	 
	 if (strpos($path, JPATH_SITE.DS)===0)
	 {
	   $path = substr($path, strlen(JPATH_SITE.DS)); 
	 }
	 OPCconfig::store('xmlexport_config', 'xml_export_path', 0, $path); 
	
	 
	 
	 $default = JURI::root(); 
	 if (substr($default, -1) != '/') $default .= '/'; 
	 $livesite = JRequest::getVar('xml_live_site', $default); 
	 
	 OPCconfig::store('xmlexport_config', 'xml_live_site', 0, $livesite); 
	 //$data = OPCconfig::getValue('xmlexport_config', 'xml_live_site', 0, ''); 
	 
	 //
	 
	 $xml_export_customs = JRequest::getVar('xml_export_customs', false); 
	 OPCconfig::store('xmlexport_config', 'xml_export_customs', 0, $xml_export_customs); 
	 
	 $num = JRequest::getInt('xml_export_num', 100000); 
	 OPCconfig::store('xmlexport_config', 'xml_export_num', 0, $num); 
	 
	 jimport( 'joomla.filesystem.folder' );
	 if (@JFolder::create(JPATH_SITE.DS.$path)===false)
	 return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', $path); 
	 
	 jimport('joomla.filesystem.file');
	 $test = true; 
	 $data = ''; 
	 $ret = @JFile::write(JPATH_SITE.DS.$path.'test.writable', $data); 
	 if ($ret === false)
	 return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'test_file', $path); 
	 
	 $ret = @JFile::delete(JPATH_SITE.DS.$path.'test.writable'); 
	 if ($ret === false)
	 return 'Cannot delete test file in '.JPATH_SITE.DS.$path; 
	 
	 $delete = JRequest::getVar('delete_ht', 0); 
	 if (empty($delete))
	 {
	 if (!file_exists(JPATH_SITE.DS.$path.'.htaccess'))
	 {
	    $data = $this->getHt(); 
		$x = JFile::write(JPATH_SITE.DS.$path.'.htaccess', $data); 
	 }
	 }
	 else
	 {
	   JFile::delete(JPATH_SITE.DS.$path.'.htaccess'); 
	 }

	 
	 
	 return; 
  }
  
  public function isPluginEnabled($item, &$forms)
  {
    if (is_object($forms[$item]['config']))
	if (!empty($forms[$item]['config']->enabled))
			   return true; 
			   
	return false; 
  }
  
  public function getNumProducts()
  {
    $db = JFactory::getDBO(); 
	$q = 'select count(*) from #__virtuemart_products'; 
	$db->setQuery($q); 
	return $db->loadResult(); 
	
  }
  public function getGeneral(&$ref)
  {
    $ref->enabled = $this->isEnabled(); 
    $ref->xml_export_path = $this->getPath(); 
	
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	
	$default = JURI::root(); 
	
	
	if (substr($default, -1) != '/') $default .= '/'; 
    $ref->xml_live_site = OPCconfig::getValue('xmlexport_config', 'xml_live_site', 0, $default); 
	$ref->xml_export_num = OPCconfig::getValue('xmlexport_config', 'xml_export_num', 0, 100000); 
	$ref->product_count = $this->getNumProducts(); 
	
  }
  function getHt()
  {
    $data = '

RewriteEngine On	
SetEnvIfNoCase Request_URI "\.xml$" no-gzip dont-vary
SetEnvIfNoCase Request_URI "\.csv$" no-gzip dont-vary
	
AddEncoding gzip .gz
<FilesMatch "\.xml\.gz$">
  ForceType application/xml
</FilesMatch>
<FilesMatch "\.csv\.gz$">
  ForceType text/csv
</FilesMatch>
<FilesMatch "\.(gz)$">
  	Header set Content-Encoding: gzip
	Header set Cache-Control "max-age=1, private"
  	SetEnv no-gzip 1
</FilesMatch>

RewriteCond %{HTTP:Accept-encoding} gzip
RewriteCond %{REQUEST_FILENAME}\.gz -s
RewriteRule ^(.*)\.xml $1\.xml\.gz [L,QSA,T=appliction/xml]

RewriteCond %{HTTP:Accept-encoding} gzip
RewriteCond %{REQUEST_FILENAME}\.gz -s
RewriteRule ^(.*)\.csv $1\.csv\.gz [L,QSA,T=text/csv]

RewriteRule \.xml\.gz$ - [T=application/xml,E=no-gzip:1]
RewriteRule \.csv\.gz$ - [T=text/csv,E=no-gzip:1]


	'; 
	return $data; 
  }
  function checkCompression($ref)
  {
    if (!file_exists($ref->xml_export_path)) return; 
	
	JFolder::create($ref->xml_export_path.'compression_test'); 
	JFile::write($ref->xml_export_path.'compression_test'.DS.'.htaccess', $data); 
	
	$data = '<xml><data>Test OK</data></xml>'; 
	$data = gzencode ($data); 
	JFile::write($ref->xml_export_path.'compression_test'.DS.'test.xml.gz', $data); 
	
  }
  
  
  function store()
  {
  
     require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	
	  
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
		 $files = $this->getPhpExportThemes();
		
		 $data = JRequest::get('post');
		  jimport('joomla.filesystem.file');
		
	  $msg = $this->storeGeneral(); 		
	
	 
     foreach ($files as $file)
	 {
	  
	   $file = JFile::makeSafe($file);
	
	   $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'xmlexport'.DS.'php'.DS.$file.'.xml'; 
	   $nd = new stdClass(); 
	   
	   //$params = new OPCparameters($nd, $file, $path, 'opctracking'); 
	   $enabled = JRequest::getVar('plugin_'.$file, false); 
	   if (!$enabled) $data[$file]['enabled'] = false;
	  else $data[$file]['enabled'] = true;
	   $config = OPCconfig::buildObject($data[$file]); 
	   
	   //var_dump($data[$file]); die(); 
	   
	   OPCconfig::store('xmlexport_config', $file, 0, $config); 
	   /*
	   if (false)
	   foreach ($data[$file] as $key=>$param)
	    {
		  echo $key.' '.$param; 
		}
	   */
	   
	 }
	 
	 
	if (!empty($msg)) return $msg; 
	   
	   
	
  
   return;
  }
 
  function getPhpExportThemes()
{
  $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'xmlexport'.DS.'php'; 
  if (!file_exists($path)) return array(); 
  jimport('joomla.filesystem.folder');
  jimport('joomla.filesystem.file');
  $files = JFolder::files($path, $filter = '.php', false, true);
  $arr = array(); 
  
  foreach ($files as $f)
  {
    $pi = pathinfo($f); 
	$file = $pi['filename']; 
	$jf = JFile::makesafe($file);
    // security here: 	
	if ($jf != $file) continue; 
	$path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'xmlexport'.DS.'php'.DS.$file.'.xml'; 
	if (!file_exists($path)) continue; 
	$arr[] = $file; 
	
    
  }
  return $arr; 
  
}

 function getPath()
 {
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
    return OPCconfig::getValue('xmlexport_config', 'xml_export_path', 0, 'export'.DS); 
 }
  
 function getConfig()
 {
 
 } 
 function isEnabled()
  {
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
    return OPCconfig::getValue('xmlexport_config', 'xml_general_enable', 0, false); 
  }
  
  function getLanguages()
  {
		require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $langs = VmConfig::get('active_languages', array()); 
		 if (empty($langs)) $langs = array('en-GB'); 
		 return $langs; 
		 
		 
  }
  
  function getShopperGroups()
  {
  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 return $config->listShopperGroups(); 
  }
  
  
  function getThemeConfig($file)
  {
  
     
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	$default = new stdClass(); 
	 $data = OPCconfig::getValue('xmlexport_config', $file, 0, $default);
	  
	   $generalconfig = new stdClass(); 
	   
	   $this->getGeneral($generalconfig); 
	   
	   if (!isset($data->xmlfile))
	   $data->xmlfile = $file.'.xml'; 
	   
	   if (empty($data->xmlfile))
	   $data->xmlpath = $generalconfig->xml_export_path.$file.'.xml'; 
	   else
	   $data->xmlpath = $generalconfig->xml_export_path.$data->xmlfile; 
	   
	   
	   
	   $data->xmlurl = $this->getXMLUrl($generalconfig, $data); 
	   
	   if (!isset($data->cname))
	   $data->cname = $file; 
	   
	   return $data; 
	 
	 
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
	   $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'xmlexport'.DS.'php'.DS.$file.'.xml'; 
	   if (!file_exists($path)) continue; 
	   
	   
	   //$data = new stdClass();  
	   //$data->adwords_id = 1; 
	   
	   $data = $this->getThemeConfig($file); 
	   
	   $title = $description = ''; 
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   else
	   return; 
	   
	   
	   if (!OPCJ3)
	   {
	    $params = new OPCparameters($data, $file, $path, 'opcexport'); 
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
		
		
		//echo $file; @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); 
		//echo $xml; 
		$t1 = simplexml_load_string($xml); 
		if ($t1 === false) continue; 
		//die(); 
		
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
	   
	   
	   
	   
	   
	  
	   
	   $ret[$file]['config'] = $data; 
	   $ret[$file]['xml'] = $fullxml; 
	   $ret[$file]['params'] = $test;
		if (empty($title))
	   $ret[$file]['title'] = $file.'.php'; 
	    else $ret[$file]['title'] = (string)$title; 

		
	   
	    $ret[$file]['description'] = (string)$description; 
		
		
	 }
	 return $ret; 
	}
	
	function getAvai()
	{
	
	jimport( 'joomla.filesystem.folder' );
if(!class_exists('shopFunctionsF'))require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
				if (method_exists('shopFunctionsF', 'loadVmTemplateStyle'))
				{
				$vmtemplate = shopFunctionsF::loadVmTemplateStyle();
				
				if(is_Dir(JPATH_ROOT.DS.'templates'.DS.$vmtemplate.DS.'images'.DS.'availability'.DS)){
					$imagePath = 'templates'.DS.$vmtemplate.DS.'images'.DS.'availability'.DS;
				} else {
					$imagePath = 'components'.DS.'com_virtuemart'.DS.'assets'.DS.'images'.DS.'availability'.DS;
				}
				}
				else
				$imagePath = 'components'.DS.'com_virtuemart'.DS.'assets'.DS.'images'.DS.'availability'.DS;
				$imagePath = JPATH_SITE.DS.$imagePath; 
	  	  
	  jimport('joomla.filesystem.file');
	  $avail = JFolder::files($imagePath, '.gif|.png|.jpg|.jpeg', false, false);
	  
	  
	  $ret = array(); 
	  
	  
	  foreach ($avail as $img)
	  {
	      $obj = new stdClass(); 
	      $pattern = '/[^\w]+/'; //'[^a-zA-Z\s]'; 
		  $key2 = preg_replace( $pattern, '_', $img ); 
		  
		  //var_dump($key2); 
		  //var_dump($img); die(); 
		  //$key2 .= md5($img); 
	      $obj->$key2 = $img; 
		  $obj->img = $img; 
		  //1-2m.gif
		  
	  switch ($img)
      {
        case "24h.gif": 
    		    $avai = 24; 
    		   $deliverydate = "24 hours";
    		   break;
        case "ihned.gif": 
    		$avai = 24; 
    		$deliverydate = "24 hours"; 
    		break;
        case "2-3d.gif": 
    		$avai =  60; 
    		$deliverydate = "2-3 days"; 
    		break;
        case "48h.gif": 
    		$avai = 48; 
    		$deliverydate = "48 hours";
    		break;
		case "1-2m.gif":
			$avai = 60;
			$deliverydate = "1 to 2 months";
			break; 
		case "1-4w.gif":
			$avai = 14; 
			$deliverydate = "1 to 4 weeks";
			break; 
		case "14d.gif":
			$avai = 14; 
			$deliverydate = "2 weeks";
			break; 
	   case "24h.gif":
			$avai = 1; 
			$deliverydate = "24 hours";
			break; 
	   case "3-5d.gif":
			$avai = 4; 
			$deliverydate = "3 to 5 days";
			break; 
	   case "48h.gif":
			$avai = 2; 
			$deliverydate = "48 hours";
			break; 
	   case "7d.gif":
			$avai = 7; 
			$deliverydate = "7 days";
			break; 
	  case "not_available.gif":
			$avai = 60; 
			$deliverydate = "Not available";
			break; 		

        case "on-order.gif": 
    		$avai =  168; 
    		$deliverydate = "1 week"; 
    		break;
        default: 
    		$avai = 60; 
    		$deliverydate = "2-3 days"; 
        
      }
		  $obj->avai = $avai; 
		  $obj->deliverytext = $deliverydate; 
		  
		  $ret[$key2] = $obj; 
	  }
	  
	  return $ret; 
	  
	}
	function getXMLUrl($generalconfig, $extconfig)
	{
	   $path = $generalconfig->xml_export_path; 
	   if (stripos($path, JPATH_SITE)===0)
	   $path = substr($path, strlen(JPATH_SITE)); 
	   $path = str_replace(DS, '/', $path); 
	   if (substr($path, 0,1) == '/') $path = substr($path, 1); 
	   $url = $generalconfig->xml_live_site.$path.$extconfig->xmlfile; 
	   return $url; 
	}
}
