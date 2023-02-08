<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	jimport( 'joomla.filesystem.file' );
	
	 
    
  // Load the virtuemart main parse code

	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
//	require_once( JPATH_ROOT . '/includes/domit/xml_domit_lite_include.php' );
//	require_once( JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'ajax'.DS.'ajaxhelper.php' );	
	

	
	class JModelOrder_export extends OPCModel
	{	
		function __construct()
		{
			parent::__construct();
		
		}
		
		
		function store()
		{
		
	    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
	    // load basic stuff:
	    OPCLang::loadLang(); 

		
		
		$user =& JFactory::getUser();

		
		
		 jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		 jimport('joomla.filesystem.archive');

	        $msg = '';
			
		
		 $db = JFactory::getDBO();
		 $data = JRequest::get('post');
		jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
	foreach ($data as $k=>$d)
	{

	  
	  
		if (strpos($k, 'tid_')!==false && (strpos($k, 'payment_contentid')===false))
		{
		 {
		  /* we have a standard variable:
		  tid_special_, tid_ai_, tid_num_, tid_back_,  tid_forward_
		  tid_nummax_, tid_itemmax_
		  tid_type_
		  */
		  if (!defined($k))
		  {
		  $this->setTemplateSetting($k, $data[$k]);
		  //echo 'template setting: '.$k.'value: '.$data[$k];
		  define($k, $data[$k]);
		  }
		  $a = explode('_', $k);
		  if (count($a)==3)
		  {
		   $tid = $a[2];
		   $checkboxes = array('tid_special_', 'tid_ai_', 'tid_num_', 'tid_forward_', 'tid_back_', 'tid_enabled_', 'tid_foreign_', 'tid_email_', 'tid_autocreate_');
		   foreach ($checkboxes as $ch)
		   {
		   if (!isset($data[$ch.$tid]) && (!defined($ch.$tid)))
		   {
		    $this->setTemplateSetting($ch.$tid, 0);
		    define($ch.$tid, '0');
		    //echo ':'.$ch.$tid.' val: 0';
		   }
		   }
		  }
			
		 }
		}
		
	  
	} 
	return true; 
	
	}
	
		
	function setTemplateSetting($k, $value)
	{ 
	
	if ($value === 'on') $value = '1';
	
		  $db = JFactory::getDBO();
		  
		  $a = explode('_',$k);
		  
		  if (count($a)==3)
		  {
		   $keyname = $a[0].'_'.$a[1];
		  
		   $tid = $a[2];
		   if (is_numeric($tid))
		   {
		   
		   $keyname = $db->getEscaped($keyname);
		   $q = 'select value from #__onepage_export_templates_settings where `keyname` = "'.$keyname.'" and `tid` = "'.$tid.'"';
		   $db->setQuery($q);
		   $res = $db->loadResult();
		   $value = $db->escape($value);
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {echo $msg; die(); }
		   if (!isset($res) || $res===false)
		   {
		    // ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `tid` INT NOT NULL DEFAULT '0', `keyname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `original` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' )
		    $q = 'insert into #__onepage_export_templates_settings (`id`, `tid`, `keyname`, `value`, `original`) values (NULL, "'.$tid.'", "'.$keyname.'", "'.$value.'", ""); ';
		    
		   }
		   else
		   {
		    $q = 'update #__onepage_export_templates_settings set `value` = "'.$value.'" where `tid`="'.$tid.'" and `keyname`= "'.$keyname.'"';
		     //($res != $data[$k]))
		   }
		  
		   $db->setQuery($q);
		   $db->query();
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {echo $msg; die(); }
		   }
		  }
	
	}
}

