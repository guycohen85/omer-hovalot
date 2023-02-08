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
	

	
	class JModelConfig extends OPCModel
	{	
		function __construct()
		{
			parent::__construct();
		
		}
		
		function getCurrencies()
		{
		$db = JFactory::getDBO(); 
		$q = 'select vendor_accepted_currencies from #__virtuemart_vendors where 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		echo $db->getErrorMsg(); 
		$arr = array(); 
		$cm = OPCmini::getModel('currency'); 
		if (!empty($res))
		 {
		 foreach ($res as $row)
		 {
		    $a = explode(',', $row['vendor_accepted_currencies']); 
			
			foreach ($a as $c)
			 {
			   $arr[$c] = $cm->getCurrency($c); 
			 }
	     }
		 }
		 
		 
		 
		 
		 
		 return $arr; 
		}
		
		function getDisabledOPC()
		{
		
		  	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
			include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
			
			if ($disable_onepage) return true; 
			else
			{
			if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "select * from #__extensions where element = 'opc' and type = 'plugin' and folder = 'system' limit 1 "; 
	  }
	  else
	  {
	    $q = "select * from #__plugins where element = 'opc' and folder = 'system'  limit 1 "; 
	  }
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$r = $db->loadAssoc(); 
		if (!empty($r['enabled'])) return false; 
		if (!empty($r['published'])) return false; 
		
					
			}
			return true; 
			
		}
// admin
		function listExtsaAdmin(&$exts, &$langsr)
		{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  $xts = array(); 
		  $langs = array(); 
		  
		  $files = JFolder::files(JPATH_ADMINISTRATOR.DS.'language', 'ini', 1, true, array('.svn', 'CVS')); 
		 
		  foreach ($files as $f)
		   {
		     $f = str_replace('/', DS, $f); 
			 $f = str_replace('\\', DS, $f); 
			 $adminpath = JPATH_ADMINISTRATOR.DS.'language'.DS; 
			 $adminpath = str_replace('/', DS, $adminpath); 
			 $adminpath = str_replace('\\', DS, $adminpath); 

		     $f = str_replace($adminpath, '', $f); 
			 $fa = explode(DS, $f); 
			 
			  $lang = $fa[0];
			 
			 // some error: 
			if (strlen($lang)>7) continue; 
			
			$langs[$lang] = $lang; 
			
			$xt = $fa[1]; 
			 
			 $xt = str_replace($lang.'.', '', $xt); 
			 if (stripos($xt, 'bck')===false)
			 if ($xt !== 'ini')
			 {
			  if (!isset($xts[$xt]))
			   {
			     $xts[$xt] = array(); 
			     $xts[$xt]['name'] = $xt;  
				 $xts[$xt]['lang'] = array();  
				 $xts[$xt]['lang'][$lang] = $lang; 
			   }
			   else
			    $xts[$xt]['lang'][$lang] = $lang; 
			 }
			 
		   }
		   
		   $exts = $xts; 
		   $langsr = $langs;
		   
		  return true; 
		}

		// site
		function listExts(&$exts, &$langsr)
		{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  $xts = array(); 
		  $langs = array(); 
		  
		  $files = JFolder::files(JPATH_SITE.DS.'language', 'ini', 1, true, array('.svn', 'CVS')); 
		  foreach ($files as $f)
		   {
			 $jpath_site = JPATH_SITE.DS.'language'.DS;
			 $jpath_site = str_replace('/', DS, $jpath_site); 
		     $f = str_replace('/', DS, $f); 
		     $f = str_replace($jpath_site, '', $f); 
			 $fa = explode(DS, $f); 
			 
			 if (count($fa) <= 1) continue; 
			 
			 $lang = $fa[0];
			
			$langs[$lang] = $lang; 
			
			$xt = $fa[1]; 
			 
			 $xt = str_replace($lang.'.', '', $xt); 
			 if (stripos($xt, 'bck')===false)
			 if ($xt !== 'ini')
			 {
			  if (!isset($xts[$xt]))
			   {
			     $xts[$xt] = array(); 
			     $xts[$xt]['name'] = $xt;  
				 $xts[$xt]['lang'] = array();  
				 $xts[$xt]['lang'][$lang] = $lang; 
			   }
			   else
			    $xts[$xt]['lang'][$lang] = $lang; 
			 }
			 
		   }
		   
		   $exts = $xts; 
		   $langsr = $langs;
		   
		  return true; 
		}
		
		
		function getExtLangVars()
		{
		   
   $jlang = JFactory::getLanguage(); 
   	 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {

   $jlang->load('com_content', JPATH_ADMINISTRATOR, 'en-GB', true); 
   $jlang->load('com_content', JPATH_ADMINISTRATOR, $jlang->getDefault(), true); 
   $jlang->load('com_content', JPATH_ADMINISTRATOR, null, true); 
   
   
  
 }
		}
		
		function getArticleSelector($name, $value, $required=false)
		{
		
		$id = $name; 
	
		if (empty($value) || (!is_numeric($value))) $value = null; 
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$html	= array();
		//if (stripos($id, '{')===false)
		{
		
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
		$html[] = '<script type="text/javascript">'; 
		$html[] = '//<![CDATA['; 
		
		$html[] = '	function jSelectArticle_'.$id.'(id, title, catid, object) {';
		$html[] = '		document.id("'.$id.'_id").value = id;';
		$html[] = '		document.id("'.$id.'_name").value = title;';
		$html[] = '		SqueezeBox.close();';
		$html[] = '	}';
		$html[] = '//]]>'; 
		$html[] = '</script>'; 

		}
		else
		{
		$html[] = '<script type="text/javascript">'; 
		$html[] = '//<![CDATA['; 
		$html[] = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$html[] = '//]]>'; 
		$html[] = '</script>'; 
		
		}
		// Add the script to the document head.
		//JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		}

		// Setup variables for display.
		
		 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
		$link	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$id;
		}
		else
		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object='.$id;
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT title' .
			' FROM #__content' .
			' WHERE id = '.(int) $value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}
		
		if (empty($title)) {
		   if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
			$title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
			else
			$title = JText::_('Select an Article');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		$html[] = '<div class="fltlft">';
		$html[] = '  <input type="text" id="'.$id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '</div>';

		// The user select button.
		
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '	<a class="modal" id="modal_link_'.$id.'" title="';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html[] = JText::_('COM_CONTENT_CHANGE_ARTICLE'); 
		else
		$html[] = JText::_('Select an Article');
		$html[] = '"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html[] = JText::_('COM_CONTENT_CHANGE_ARTICLE_BUTTON');
		else
		$html[] = JText::_('Select');
		$html[] = '</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$value) {
			$value = '';
		} else {
			$value = (int)$value;
		}

		// class='required' for client side validation
		$class = '';
		if ($required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$id.'_id"'.$class.' name="'.$name.'" value="'.$value.'" />';

		return implode("\n", $html);
	
		}
		
		function getUserFieldsLists(&$corefields)
		{
		  
		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'version.php'); 
		  $ver = vmVersion::$RELEASE; 
		  $ok = false; 
		  if (version_compare($ver,'2.0.6','ge')) $ok = true; 
		   
		  if ($ver == '${PHING.VM.RELEASE}') $ok = true; 
		  if (!$ok)
		  return false; 

       $jlang = JFactory::getLanguage(); 

		$jlang->load('com_virtuemart', JPATH_SITE, 'en-GB', true); 
		$jlang->load('com_virtuemart', JPATH_SITE, $jlang->getDefault(), true); 
		$jlang->load('com_virtuemart', JPATH_SITE, null, true); 

		  
		  if (!class_exists('VirtueMartModelUserfields'))
		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'userfields.php'); 
		  $modelu = new VirtueMartModelUserfields();
		  
		  $corefields = $modelu->getCoreFields();
		  $corefields[] = 'register_account';
		  $modelu->setState('limitstart', 0);
		  $modelu->setState('limit', 99999);
		  $modelu->_noLimit = true; 
		  $uf = $modelu->getUserfieldsList();
		  
		  if (empty($uf)) return array(); 
		  
		  
		  $last = array (
  'virtuemart_userfield_id' => '0',
  'virtuemart_vendor_id' => '1',
  'userfield_jplugin_id' => '0',
  'name' => 'register_account',
  'title' => 'COM_VIRTUEMART_ORDER_REGISTER',
  'description' => '',
  'type' => 'checkbox',
  'maxlength' => '1000',
  'size' => NULL,
  'required' => '0',
  'cols' => '0',
  'rows' => '0',
  'value' => '',
  'default' => NULL,
  'registration' => '1',
  'shipment' => '0',
  'account' => '1',
  'readonly' => '0',
  'calculated' => '0',
  'sys' => '0',
  'params' => '',
  'ordering' => '101',
  'shared' => '0',
  'published' => '1',
  'created_on' => '2014-04-01 16:43:17',
  'created_by' => '42',
  'modified_on' => '2014-04-01 16:43:17',
  'modified_by' => '42',
  'locked_on' => '0000-00-00 00:00:00',
  'locked_by' => '0',
);
		  $last = (object)$last; 
		  $last->name = 'register_account'; 
		  $last->title = 'COM_VIRTUEMART_ORDER_REGISTER'; 
		  $last->type = 'checkbox'; 
		  
		  $uf[] = $last; 
		  
		  $uf = array_reverse($uf); 
		  
		  return $uf; 
		}
		function patchcalculationh()
	{
		$msg = ''; 
		$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'calculationh.php'; 
		$savepath = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'calculationh_patched.php'; 
		if (!file_exists($path)) return; 
		$data = file_get_contents($path); 
		if (file_exists($savepath))
		$datas = file_get_contents($savepath); 
	    else $datas = ''; 
		
		$c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	if ($test != 10)
	 {
	   $isadmin =false; 
	 }
	 else $isadmin = true; 
	 
	 if (!$isadmin)
	 {
	   $msg .= JText::_('COM_ONAPEGE_USER_IS_NOT_VIRTUEMART_ADMIN').'<br />'; 
	 }
		
		$data = str_replace("\r\r\n", "\r\n", $data); 
		jimport('joomla.filesystem.folder'); 
		jimport('joomla.filesystem.file'); 
		{
			$count = 0; 
			$data = str_replace('private', 'protected', $data, $count); 
			$data = str_replace('VmError(\'Unrecognised', ' // VmError(\'Unrecognised', $data); 
			$data = str_replace('VmWarn(\'Unrecognised', ' // VmError(\'Unrecognised', $data); 
			$content = '<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' ); 
if (!class_exists(\'calculationHelper\'))
require(JPATH_SITE.DS.\'administrator\'.DS.\'components\'.DS.\'com_virtuemart\'.DS.\'helpers\'.DS.\'calculationh.php\'); 
'; 
			if (empty($count) || ($count == 1))
			{
				 if (strcmp($datas, $content)!==0)
				 {
				 if (file_exists($savepath))
				 if (@JFile::delete($savepath)==false)
				 {
					 //$msg = 'Patch for calculationh.php is not needed';
					 $msg .= 'Couln\'t remove OPC override '.$savepath; 
				 }
			
			if (@JFile::write($savepath, $content)==false)
			{
				$msg .= '<br />Could not write to '.$savepath; 
			}
				 
			}
			}
			else
			{
			  if (strcmp($datas, $data)!==0)
			  {
			  if (@JFile::write($savepath, $data)==false)
			  {
				  $msg = 'Cannot write to '.$savepath; 
			  }
			  else
			  {
				  //test: 
				  $data = file_get_contents($savepath); 
				  $x1 = (@eval('return true; ?> '.$data.' <?php '));
				  $x2 = (@eval('return true; ?> '.$data.'  '));
				  if (!((($x1 !== true) && ($x2 === true)) || (($x1 === true) && ($x2 !== true))))
				  {
					$msg = 'The patch could not be applied'; 
				 
				 if (@JFile::delete($savepath)==false)
				 {
						$msg .= ' and couln\'t remove OPC override '.$savepath; 
				 }   
				  }
			  }
			 }
			}
		}
		/*
		$link = 'index.php?option=com_onepage';
   
		if (empty($msg)) $msg = 'Patch applied sucessfully';
		else $msg = 'Patch not applied ! '.$msg; 
		$this->setRedirect($link, $msg);
		*/
			return $msg; 
	}
		function checkLangFiles()
		{
		
		
		  $orig = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'; 
		  $orig_sys = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'; 

		  jimport('joomla.filesystem.folder');
          jimport('joomla.filesystem.file');
		  jimport('joomla.filesystem.archive');
		  
		  $msg = '';  
		  $orderxml = JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'cart'.DS.'tmpl'.DS.'order.xml'; 
		
		if (!file_exists($orderxml))
		{
		  JFile::copy(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'vm'.DS.'order.xml', $orderxml); 
		}
		  
		$orderxml2 = JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'opc'.DS.'tmpl'.DS.'default.xml';
		if (!file_exists($orderxml2))
		{
		  @JFolder::create(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'opc'); 
		  $data = ''; 
		  @JFile::write(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'opc'.DS.'index.html', $data); 
		  @JFolder::create(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'opc'.DS.'tmpl'); 
		  @JFile::write(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'opc'.DS.'tmpl'.DS.'index.html', $data); 
		  @JFile::copy(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'vm'.DS.'default.xml', $orderxml2); 
		}
		  
		  $msg = $this->patchcalculationh(); 
		  $lang = JFactory::getLanguage();
		  if (method_exists($lang, 'getKnownLanguages'))
		  $list = $lang->getKnownLanguages(); 
		 
		 $key = 'en-GB'; 
	     
	  
		  if (!empty($list))
		  foreach ($list as $key=>$val)
		  {
			  
			  if (!file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'))  
		 {
			 if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'))
			 if (!file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'))
			 {
			 JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini' , JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini');
			 
			 }
			 }
			
			if (!file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.com_onepage.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'language'.DS.$key.DS.$key.'.com_onepage.sys.ini' , JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.com_onepage.sys.ini');
			
			if (!file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.plg_system_opc.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB'.'.plg_system_opc.sys.ini' , JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.plg_system_opc.sys.ini');

			if (!file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.plg_vmpayment_opctracking.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB'.'.plg_vmpayment_opctracking.sys.ini' , JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.plg_vmpayment_opctracking.sys.ini');

			
			if (!file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.plg_system_opcregistration.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB'.'.plg_system_opcregistration.sys.ini' , JPATH_ADMINISTRATOR.DS.'language'.DS.$key.DS.$key.'.plg_system_opcregistration.sys.ini');
			
			  if ($key == 'en-GB') continue; 
			if (!file_exists(JPATH_ROOT.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'))  
			{
				
				$orig_lang = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'; 
		  $orig_sys_lang = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'; 
				 
				 if (file_exists($orig_lang))
				 {
				 if (!JFile::copy($orig_lang, JPATH_ROOT.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'))
			     {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/'.$key.'/'.$key.'.com_onepage.ini <br />';
	    		  
			      }
				 if (!JFile::copy($orig_sys_lang, JPATH_ROOT.DS.'language'.DS.$key.DS.$key.'.com_onepage.ini'))
			     {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/'.$key.'/'.$key.'.com_onepage.ini <br />';
	    		  
			      }
			 }
			 
			 
			 
			}
		  }
		  
		  
		  if (!file_exists(JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'))
		   {
		        
		  
		   
		    if (!JFile::copy($orig, JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'))
			 {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/en-GB/en-GB.com_onepage.ini <br />';
	    		  
			 }
			 //else 
			 //  $msg .= 'OPC Language files installed in /language/en-GB/en-GB.com_onepage.ini <br />'; 

			 if (!JFile::copy($orig_sys, JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'))
			 {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/en-GB/en-GB.com_onepage.sys.ini <br />';
	    		  
			 }
			 //else
			  // $msg .= 'OPC Language files installed in /language/en-GB/en-GB.com_onepage.sys.ini <br />'; 

			 
		  }
		  
		   $x = JFactory::getApplication()->set('messageQueue', array()); 
		  $x = JFactory::getApplication()->set('_messageQueue', array()); 
		  
		$lang = JFactory::getLanguage();
		$extension = 'com_onepage';
		$lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB');
		$tag = $lang->getTag();
		if (file_exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$tag.DS.$tag.'.com_onepage.ini'))
		$lang->load('com_onepage', JPATH_ADMINISTRATOR, $tag, true, true);

		   
		  // since june 2012 we will use our own document type:
		  
		  $fdoc = JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml';
		  $fdocfile = JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml'.DS.'opchtml.php'; 

		  if (!file_exists($fdoc))
		  {
		    if (@JFolder::create($fdoc)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY',$fdoc).' (opc document type)<br />'; 
		  }


		  
		  if (!file_exists($fdocfile))
		   {
		     if (@JFile::copy(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'opchtml.php', $fdocfile)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'opchtml.php', $fdoc).'<br />'; ; //'Cannot create own document type file in '.$fdocfile.'<br />'; 
			 $st = ' '; 
			 if (@JFile::write($fdoc.DS.'index.html', $st)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'index.html', $fdoc).'<br />'; 
		   }
		  
		   
		   
		   
		   

			// we need to check a fatal error in vm 2.0.4: 
			if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php'))
			 {
			   
			   $ver = phpversion(); 
			   if ((strpos($ver, '5.3')===false) && ((strpos($ver, '5.4')===false)))
			    {
				  $content = file_get_contents(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php');
				  if (strpos($content, '__DIR__')!==false)
				  {
				  $content = str_replace('__DIR__', 'dirname(__FILE__)', $content); 
				  if (JFile::write(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php', $content)!==false)
				   {
					   $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php', 'http://www.rupostel.com/').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				     //$msg .= 'Patched a Virtuemart bug (fatal error) in '.JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php'.'<br />'; 
				   }
				   else 
					   $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php', 'http://www.rupostel.com/').' Please replace __DIR__ with dirname(__FILE__) <br />'; 
					 //$msg .= JText::sprintf('COM_ONEPAGE_PATCHED', JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php', 'http://www.rupostel.com/').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				    //
				  }
				}
			 }
			 
			 
			 // another fatal error in vm: \administrator\components\com_virtuemart\models\userfields.php
			if (file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'userfields.php')) 
			 {
			   $f = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'userfields.php'; 
			   //$ver = phpversion(); 
			   //if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents($f);
				  if (strpos($content, '$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, $_selected, \'fieldvalue\', \'fieldtitle\');')!==false)
				  {
				  $content = str_replace('$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, $_selected, \'fieldvalue\', \'fieldtitle\');', '$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, \'fieldvalue\', \'fieldtitle\', $_selected); //// this line was fixed by OPC', $content); 
				  if (JFile::write($f, $content)!==false)
				   {
					   $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				     //$msg .= 'Patched a Virtuemart bug (fatal error) in '.$f.' according to this <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171">description</a><br />'; 
				   }
				   else 
					   $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				    //$msg .= 'Cannot patch a Virtuemart bug (fatal error) in '.$f.' Please read the following if you would like to use checkboxes in your registration <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171">RuposTel support forum</a> <br />'; 
				  }
				}
			 }
			
			$file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'; 
			
			$x = file_get_contents($file); 
			
			$s = 'vmError(\'Could not use path \'.$file.\' to store log\');'; 
			$p = stripos($x, $s); 
			if ($p !== false)
			{
			  JFile::copy($file, $file.'opc_backup.php'); 
			  $data = str_replace($s, '//OPC: removed a line that caused a fatal error', $x); 
			  if (@JFile::write($file, $data)===false)
			 {
			  $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', $file, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=806&start=10').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			 }
			 else
			  $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', $file, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=806&start=10').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			}


			if(JVM_VERSION >= 2) {
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery( $q );
			$template = $db->loadResult();
			
			$fx = JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.'cart'.DS.'helper.php'; 
			jimport( 'joomla.filesystem.folder' );
			if (file_exists($fx))
			 {
			    if (JFolder::move($fx = JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.'cart', 
				$fx = JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.'cart_opcrenamed')===true)
				 {
				   $msg .= JText::_('COM_ONEPAGE_RENAMED_LINELAB_THEMEOVERRIDES').' '. JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.'cart_opcrenamed'."<br />\n"; 
				 }
				 else
				 {
				   $msg .= JText::_('COM_ONEPAGE_RENAMED_LINELAB_THEMEOVERRIDES_ERROR').'  '.JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.'cart'."<br />\n"; 
				 }
				
			 }
			
			
			 // plugin fix for Joomla 1.5
			 if(!(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')))
			 {
			 // we need to fix a bug in VM2.0 and J1.5:
			$search = '$dispatcher->trigger(\'onVmSiteController\', $_controller);';
			$rep = '$dispatcher->trigger(\'onVmSiteController\', array($_controller));';
			$x = file_get_contents(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php'); 
		
		   $search2 = "(JPATH_COMPONENT.DS.'helpers'.DS.'ICal'.DS.'PublicHolidays.php')";
		   $rep2 = "(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'ICal'.DS.'PublicHolidays.php')";
		    
		   if ((strpos($x, $search)!==false) || ((strpos($x, $search2)!==false)))
		   {
		     $x = str_replace($search, $rep, $x); 
			 $x = str_replace($search2, $rep2, $x); 
		     JFile::copy(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.orig.opc_bck.php'); 
		     if (@JFile::write(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', $x)===false)
			 {
			  $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			 }
			 else
			  $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			   ///$msg .= 'Patched Joomla 1.5 compatibility bug in /components/com_virtuemart/virtuemart.php '; 
			 
		   }
			 }

			// we need to check a dual "Product successfully added" error in vm 2.0.6
			$f = JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'controllers'.DS.'cart.php'; 
			if (file_exists($f))
			 {
			   
			   //$ver = phpversion(); 
			   //if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents($f);
				  if ((strpos($content, '$mainframe->enqueueMessage($msg);')!==false) && (strpos($content, 'OPC fix')===false))
				  {
				  $content = str_replace('$mainframe->enqueueMessage($msg);', '//// OPC fix: $mainframe->enqueueMessage($msg);', $content); 
				  if (JFile::write($f, $content)!==false)
				   {
				     $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169').'<br />'; 
				   }
				   else 
					   $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169').'<br />'; 
				    //$msg .= 'Cannot patch a Virtuemart bug in '.$f.' described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169">here</a> <br />'; 
				  }
				}
			 }
		
        // this portion of code will check if any column named id in #__virtuemart_payment_{plugin_name} uses tinyint ID and alters the database structure
		// $msg string holds info for GUI to show what has changed in the DB structure or the mysql error
		// this code is not tested when there are more databases available to mysql user
		// $msg = ''; 
		$dbj = JFactory::getDBO();
		$prefix = $dbj->getPrefix();
		$q = "SHOW TABLES LIKE '".$prefix."virtuemart_payment_plg_%'";
		
	    $dbj->setQuery($q);
	    $r = $dbj->loadAssocList();
		
		if (!empty($r))
		foreach ($r as $key=>$table)
		 {
		   if (!is_array($table)) continue;
		   $plgtable = reset($table); 
		   $q = 'describe '.$plgtable.' id'; 
		   $dbj->setQuery($q); 
		   $res = $dbj->loadAssoc();
		   
		   
		   if (stripos($res['Type'], 'tinyint')!==false)
		    {
			  $msg .= JText::sprintf('COM_ONEPAGE_TINY_INT_ERROR', $plgtable).'<br />'; //.' uses tinyint as default index which is limited to 255 records (orders). OPC tries to fix this bug for you within DB structure.<br />'; 
			  
			  $q = 'ALTER TABLE  `'.$plgtable.'` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT'; 
			  $dbj->setQuery($q); 
			  $x = $dbj->query(); 
			  $e = $dbj->getErrorMsg(); 
			  if (!empty($e)) $msg .= $e.'<br />'; 
			  else $msg .= '<span style="color: green;">'.JText::sprintf('COM_ONEPAGE_DATABASE_UPDATED',$plgtable).'</span><br />'; 
			  
			}
		   
		 }
		
		$q = 'describe '.$prefix.'session data'; 
		$dbj->setQuery($q); 
		$r = $dbj->loadAssoc();
		$type = $r['Type']; 
		
		if ((stripos($type, 'varchar')!==false) || (stripos($type, 'text')===0))
		{
		  $msg .= JText::sprintf('COM_ONEPAGE_SESSION_SMALL', $type); //'Your session data column is too small for your shop: #__session.data is of type <span style="color: red;">'.$type.'</span>. OPC updates your session database structure to <span style="color: green;">mediumtext</span> which is recommended for VirtueMart implementation.<br />'; 
		  $q = 'ALTER TABLE  `'.$prefix.'session` CHANGE  `data`  `data` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL'; 
		  $dbj->setQuery($q); 
		  $dbj->query(); 
		  $e = $dbj->getErrorMsg(); 
		  if (!empty($e))
		  $msg .= $e.'<br />'; 
		  //else $msg .= '<span style="color: green;">Session data column updated sucessfully.</span>'; 
		  
		}
		
				$db = JFactory::getDBO();
		// check shopper groups: 
		$q = 'select * from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		$err = false; 
		if (empty($res)) 
		{
			
			// insert the default anonymous group
		$err = true;
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-anonymous OLD -' where `shopper_group_name` = '-anonymous-' limit 1"; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (1, 1, '-anonymous-', 'Shopper group for anonymous shoppers', 0, NULL, 2, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_ANONYMOUS').'<br />'; 
		}
		else
		{
			// update the default anonymous group
			
		if ($res['virtuemart_vendor_id'] != '1') $err = true; 
		if ($res['default'] != '2') $err = true; 
		if ($res['shared'] != '1') $err = true; 
		if ($res['published'] != '1') $err = true; 
		if ($err)
		{
			$q = 'delete from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 1 limit 1'; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-anonymous OLD -' where `shopper_group_name` = '-anonymous-' limit 1"; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (1, 1, '-anonymous-', 'Shopper group for anonymous shoppers', 0, NULL, 2, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_ANONYMOUS').'<br />'; 
			//$msg .= 'Fixed Virtuemart -anonymous- shopper group which is required for proper Virtueamrt functionality. Please check the configuration of your shipping, payment, calculation plugins.  <br />'; 
		}
		
		}
	
		// check shopper groups: 
		$q = 'select * from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 2'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
		$err = false; 
		if (empty($res)) 
		{
			
			// insert the default -default- group
		$err = true;
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-default OLD -' where `shopper_group_name` = '-default-' limit 1"; 
			$db->setQuery($q); $db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (2, 1, '-default-', 'This is the default shopper group.', 0, NULL, 1, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_DEFAULT').'<br />'; 
			//$msg .= 'Fixed Virtuemart -default- shopper group which is required for proper Virtueamrt functionality. Please check the configuration of your shipping, payment, calculation plugins.  <br />'; 
		}
		else
		{
			// update the default -default- group
			
		if ($res['virtuemart_vendor_id'] != '1') $err = true; 
		if ($res['default'] != '1') $err = true; 
		if ($res['shared'] != '1') $err = true; 
		if ($res['published'] != '1') $err = true; 
		if ($err)
		{
			$q = 'delete from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 2 limit 1'; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-default OLD -' where `shopper_group_name` = '-default-' limit 1"; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (2, 1, '-default-', 'This is the default shopper group.', 0, NULL, 1, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_DEFAULT').'<br />'; 
			//$msg .= 'Fixed Virtuemart -default- shopper group which is required for proper Virtueamrt functionality. Please check the configuration of your shipping, payment, calculation plugins.  <br />'; 
		}
		
		}
		$q = 'update #__virtuemart_shoppergroups set `default` = "0" where ((`default` > 0) and ((virtuemart_shoppergroup_id <> 1) AND (virtuemart_shoppergroup_id <> 2))) limit 10'; 
		
		$db->setQuery($q);
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 		
		
	 if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
	 include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
	
     if (!isset($opc_plugin_order)) $opc_plugin_order = -999; 
	else $opc_plugin_order = (int)$opc_plugin_order; 

			if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update #__extensions set ordering = ".$opc_plugin_order." where element = 'opc' and type = 'plugin' and folder = 'system' limit 2 "; 
	  }
	  else
	  {
	    $q = "update #__plugins set ordering = ".$opc_plugin_order." where element = 'opc' and folder = 'system'  limit 2 "; 
	  }
	  
	  
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder = 'system' limit 0,1"; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   // if plugin installed: 
	   if (!empty($res))
	   {
	    $q = 'select max(ordering) from #__extensions where 1'; 
	    $db->setQuery($q); 
	    $ordering = (int)$db->loadResult(); 
	    $q = "update #__extensions set ordering = ".$ordering." where element = 'opctracking' and type = 'plugin' and folder = 'system' limit 2 "; 
	   }
	  }
	  else
	  {
	  
	      $q = "select * from #__plugins where element = 'opctracking' and folder = 'system' limit 0,1"; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   // if plugin installed: 
	    if (!empty($res))
	    {
		  $q = 'select max(ordering) from #__plugins where 1'; 
	    $db->setQuery($q); 
	    $ordering = (int)$db->loadResult(); 
	    $q = "update #__plugins set ordering = ".$ordering." where element = 'opctracking' and folder = 'system'  limit 2 "; 
		}
	  }

	  
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg();
		if (!empty($e))
		$msg .= $e.'<br />'; 
	    
		
		
		// install OPC config table: 
		require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	 if (!OPCmini::tableExists('onepage_config'))
	 {
		$q = ' CREATE TABLE IF NOT EXISTS `#__onepage_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(100) NOT NULL,
  `config_subname` varchar(100) NOT NULL,
  `config_ref` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_subname`,`config_ref`),
  KEY `config_name_2` (`config_name`,`config_subname`),
  KEY `config_name_3` (`config_name`,`config_subname`,`config_ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=85 ; 
'; 
    $db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); 
	if (!empty($e))
	$msg .= $e.'<br />'; 

	}
	
  
  	 //update from prior opc versions: 
	 $db = JFactory::getDBO(); 
     $q = "delete from `#__extensions` WHERE  element = 'opctracking' and folder = 'system' "; 
     $db->setQuery($q); 
	 $db->query(); 
	 
	 if (is_dir(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opctracking'))
	 @JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opctracking'); 
  
  
			if (!empty($msg))
			if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
		   
		}
		
		function loadVmConfig()
		{
		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		 
		  VmConfig::loadConfig(true); 
		  if (method_exists('VmConfig', 'loadJLang'))
		  {
		  VmConfig::loadJLang('com_virtuemart',TRUE);
		  VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		  }
		  
		 if (method_exists('VmConfig', 'loadJLang'))
		 VmConfig::loadJLang('com_virtuemart');
		 else
		  {
		     $lang = JFactory::getLanguage();
			 $extension = 'com_virtuemart';
			 $base_dir = JPATH_SITE;
			 $language_tag = $lang->getTag();
			 $reload = false;
			 $lang->load($extension, $base_dir, $language_tag, $reload);
			 
			 $lang = JFactory::getLanguage();
			 $extension = 'com_virtuemart';
			 $base_dir = JPATH_ADMINISTRATOR;
			 $language_tag = $lang->getTag();
			 $reload = false;
			 $lang->load($extension, $base_dir, $language_tag, $reload);
			 
		  }
		  
		  
		}
		function listShopperGroups()
		{
		  $db = JFactory::getDBO(); 
		  $q = 'select * from #__virtuemart_shoppergroups where published = 1'; 
		  $db->setQuery($q); 
		  return $db->loadAssocList(); 
		  
		  return "";
		  if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		  
		  ob_start(); 
		  $db =& JFactory::getDBO(); 
		  $q = "SELECT * FROM #__vm_shopper_group WHERE 1 limit 9999";
		  $db->setQuery($q);
		  $groups = $db->loadAssocList(); 
		  foreach ($groups as $g)
		  {
		    $id = $g['shopper_group_id']; 
			$name = $g['shopper_group_name'];
		    echo '<input type="checkbox" value="'.$id.'" name="zerotax_shopper_group[]" id="group'.$id.'" ';
			if (!empty($zerotax_shopper_group))
			if (in_array($id, $zerotax_shopper_group)) echo ' checked="checked" '; 
			echo '/>'; 
			echo '<label for="group'.$id.'">'.$name.'</label>'; 
			echo '<br style="clear: both;" />';
		  }
			
		  return ob_get_clean(); 
		}
		
		function listShopperGroupsSelect()
		{
		  return ""; 
		  if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		  ob_start(); 
		  $db =& JFactory::getDBO(); 
		  $q = "SELECT * FROM #__vm_shopper_group WHERE 1 limit 9999";
		  $db->setQuery($q);
		  $groups = $db->loadAssocList(); 
		  echo '<select name="move_vat_shopper_group">'; 
		  echo '<option value=""';
		  if (empty($move_vat_shopper_group)) echo ' selected="selected" '; 
		  echo '>Not configured</option>';
		  foreach ($groups as $g)
		  {
		    $id = $g['shopper_group_id']; 
			$name = $g['shopper_group_name'];
		    echo '<option  value="'.$id.'"';
			if (!empty($move_vat_shopper_group))
			if ($move_vat_shopper_group == $id) echo ' selected="selected" '; 
			echo '>'; 
			echo $name; 
			echo '</option>'; 
		  }
		  echo '</select>'; 
		  return ob_get_clean(); 
		}
		
		function listUserfields()
		{
		  return ""; 
		  if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		  ob_start(); 
		  $db =& JFactory::getDBO(); 
		  $q = "SELECT * FROM #__vm_userfield WHERE published = '1' and registration = '1' limit 9999";
		  $db->setQuery($q);
		  $groups = $db->loadAssocList(); 
		  echo '<select name="vat_input_id">'; 
		  echo '<option value=""';
		  if (empty($vat_input_id)) echo ' selected="selected" '; 
		  echo '>Not configured</option>';
		  foreach ($groups as $g)
		  {
		    $id = $g['name']; 
			$name = $g['name'];
		    echo '<option  value="'.$id.'"';
			if (!empty($vat_input_id))
			if ($vat_input_id == $id) echo ' selected="selected" '; 
			echo '>'; 
			echo $name; 
			echo '</option>'; 
		  }
		  echo '</select>'; 
		  return ob_get_clean(); 
		}
		
		function getShippigTaxes()
		{
		return ""; 
		ob_start();
		$q = "select * from `#__vm_vendor` where vendor_zip > ''";
 		$db =& JFactory::getDBO();
 		$db -> setQuery($q);
 		$res = $db->loadAssocList();

 		$vendor_country = $res[0]['vendor_country'];
 		$vendor_state = $res[0]['vendor_state'];
 		$vendor_id = $res[0]['vendor_id'];

  	    $vendor_zip = $res[0]['vendor_zip'];
		$tax_rates = array();
		$q = "select * from #__vm_tax_rate where 1"; 
	    $db -> setQuery($q);
 		$res2 = $db->loadAssocList();
 		
 		if (!isset($res2)) echo '<span style="color:red;"> No tax found. Please create a tax rate!</span>'; 
 		else
 		{
 		foreach($res2 as $taxr)
 		{ 
 		  $tax_rates[$taxr['tax_rate_id']] = $taxr['tax_rate'];
 		  echo 'Tax rate ID: '.$taxr['tax_rate_id'].' country: '.$taxr['tax_country'].' state: '.$taxr['tax_state'].' rate: '.$taxr['tax_rate'].'<br />';
 		  echo '  Vendor ID: '.$vendor_id.' country: '.$vendor_country.' state: '.$vendor_state.'<br />';
 		  echo 'Status: <br />';
 		  if ($taxr['tax_country']==$vendor_country) echo 'Country OK'; else echo '<span style="color:red;"> Counry is not the same !</span>';
 		  echo '<br />';
 		  if ($taxr['tax_state']=='-') echo 'State OK'; 
 		  else echo '<span style="color:red;"> State for Tax should be set to NONE ! </span>';
 		  echo '<br />';
 		}
 		}
		


        echo 'Shipping options should have a tax id assigned. Status: <br />';

        $token = md5(uniqid());
        $hash = 'temp'.substr($token, 4); 
        $timestamp = time();
        $q3 = "INSERT INTO `#__vm_user_info` (user_info_id, state, country, zip, cdate) VALUES ('".$hash."', '".$vendor_state."', '".$vendor_country."', '".$vendor_zip."', '$timestamp') ";
        $db->setQuery($q3);
        $db->query();
        
        $new_id = $hash;
        
        $GLOBALS['total'] = 25;
        $total = 25;
		$GLOBALS['tax_total'] = 1.9;
		$d['ship_to_info_id'] = $new_id;

		$GLOBALS['ship_to_info_id'] = $new_id;
		$_REQUEST['ship_to_info_id'] = $new_id;

		$weight = 100;
		$weight_total = $weight;
		$GLOBALS['weight'] = $weight;
		$GLOBALS['weight_total'] = $weight;
		$d['zip'] = $vendor_zip;
		$d['counry'] = $vendor_country;	
		$d['state'] = $vendor_state;
		$vars = $d;
		$_GLOBALS['vars'] = $vars;
		//$tpl = new $GLOBALS['VM_THEMECLASS']();
		//$tpl->set_vars( Array( 'vars' => $vars, ) );
        echo 'Test variables: Total ('.$total.') Weight ('.$weight.') address is set to vendor address <br />';
        global $PSHOP_SHIPPING_MODULES;
        ob_start();
        if (isset($PSHOP_SHIPPING_MODULES))
        foreach ( $PSHOP_SHIPPING_MODULES as $shipping_module ) {
        	if( file_exists( CLASSPATH. "shipping/".$shipping_module.".php" )) {
			 include_once( CLASSPATH. "shipping/".$shipping_module.".php" );
			}
			
			if( class_exists( $shipping_module )) {
			$SHIP_TEST = new $shipping_module();
			//echo $shipping_module.'\' get_tax_rate(0)  returns tax rate: ';
			$SHIP_TEST->list_rates( $vars );
			/*
			if ($rate == 0) echo '<span style="color: red;">0</span>';
			else echo $rate;
			echo '<br />';
			*/
			
        }
        }
        $shipm = @ob_get_clean();
        $poss = $this->strposall($sh, 'value="');
		$sh3 = $sh;
		if ($poss!==false)
		{
			foreach ($poss as $p)
			{
	 			$endp = strpos($sh, '" ', $p+7);
	 			$fu = substr($sh, $p+7, $endp-$p-7);
	 
	 			//echo 'value: '.$fu.'<br />';
   				// netto price
   				// we will create taxes for every shipping
   				$_REQUEST['shipping_rate_id'] = $fu;
   				unset($ps_checkout);
			    $rate_net = 0;
   				$rate_array = explode("|", urldecode($fu));
   				if (count($rate_array)>2)
   				{
   				  $shipping_rate = $rate_array[0];
   				}
 			}
		}
        
        
        
        $q = "DELETE FROM `#__vm_user_info` WHERE user_info_id = '".$hash."' ";
        $db->setQuery($q);
        $db->query();

		$html = ob_get_clean();
		return $html;
		}
		
		function getAllCurrency($limitstart, $limit)
		{
			return "";
		}	
		
		function getExtensions()
		{
		  return ""; 
		  $exts = $this->getExt();
		  $ret = '<p>SAVE YOUR CONFIGURATION BEFORE USING THIS STEP</p>';
		  $ret .= '<table class="admintable" style="width: 100%;">';
		  $ret .= '<tr>';
		  $ret .= '<th style="background-color: #7F807D">Name</th>';
		  $ret .= '<th style="background-color: #7F807D">Enabled</th>';
		  $ret .= '<th style="background-color: #7F807D">Description</th>';
		  $ret .= '<th style="background-color: #7F807D">Configure</th>';
		  $ret .= '</tr>';
		  if (!empty($exts))
		  {
		    $i = 1; 
		    foreach($exts as $ext)
		    {
			  if (empty($i)) 
			  {
			  $i = 1; 
			  $color = '#ECEDCA'; 
			  }
			  else {
			  $i = 0; 
			  $color='white'; 
			  }
		      $ret .= '<tr>';
		      $ret .= '<td style="background-color: '.$color.';">'.$ext['nametxt'].'</td>';
		      $ret .= '<td style="background-color: '.$color.';"><input type="checkbox"';
		      if ($ext['enabled']) $ret .= ' checked="checked" ';
		      $ret .= 'name="opext_'.$ext['name'].'" /></td>';
		      $ret .= '<td style="background-color: '.$color.';">'.$ext['desc'].'</td>';
		      if (!empty($ext['params']))
		      $ret .= '<td style="background-color: '.$color.';"><a href="index.php?option=com_onepage&amp;view=configext&amp;ext='.urlencode($ext['name']).'">Configure...</a></td>';
		      else $ret .= '<td>(no config needed)</td>';
		      $ret .= '</tr>';
		    }
		  }
		  $ret .= '</table>';
		  return $ret;
		}
		function getExt()
		{
		 return ""; 
		 $dir = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext';
		 $arr = scandir($dir);
		 $ret = array();
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (is_dir($dir.DS.$file) && ($file != '.') && ($file != '..')) 
		    {
		     $arr = array();
		     $arr['path'] = $dir.DS.$file;
		     $arr['enabled'] = file_exists($dir.DS.$file.DS.'enabled.html'); 
		     $arr['name'] = $file;
		     // params part here?
		     if (file_exists($dir.DS.$file.DS.'description.txt'))
		     $desc = file_get_contents($dir.DS.$file.DS.'description.txt');
		     else $desc = ''; 
		     $arr['desc'] = $desc;
		     
		     if (file_exists($dir.DS.$file.DS.'extension.xml'))
		     {
		     
		     $xmlDoc = new DOMIT_Lite_Document();
			 $xmlDoc->resolveErrors( true );
				if ($xmlDoc->loadXML( $dir.DS.$file.DS.'extension.xml', false, true )) {
				
					$root =& $xmlDoc->documentElement;
					
					$tagName = $root->getTagName();
					$isParamsFile = ($tagName == 'mosinstall' || $tagName == 'mosparams');
					if ($isParamsFile && $root->getAttribute( 'type' ) == 'opext') {
						if ($params = &$root->getElementsByPath( 'params', 1 )) {
							$element =& $params;
						}
					}
					$arr['params'] = $params;
					$desce = &$root->getElementsByPath('description', 1); 
					$desc = $desce->getText();
					if ($desc)
					$arr['desc']  = (string)$desc;
					$namee = &$root->getElementsByPath('name', 1); 
					$name = $namee->getText();
					if ($name)
					 $arr['nametxt'] = (string)$name;
				}
				else
				{
				  $app =& JFactory::getApplication(); 
				  $app->enqueueMessage(
			'OPC Extensions XML Error: '.$xmlDoc->errorString
);
				
				}
		     
		     }
		     if (empty($arr['nametxt'])) $arr['nametxt'] = $file;
		     $ret[] = $arr;
		     
		     
		    }
		  }
		 }
		 return $ret;
		}
		
		function renameTheme()
		{
		
		   $from = JRequest::getVar('orig_selected_template');   
		   $to = JRequest::getVar('selected_template');   
		   jimport('joomla.filesystem.folder');
           jimport('joomla.filesystem.file');
		   jimport('joomla.filesystem.archive');
		   $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS; 
		   if (!file_exists($path.$from)) 
		   {
		    JRequest::setVar('selected_template', JRequest::getVar('orig_selected_template'));   
		    return; 
		   }
		   $to = JFile::makeSafe($to); 
		   JRequest::setVar('selected_template', $to);   
		   
		   if (JFolder::copy($path.$from, $path.$to, '', true)===false)
		   return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', $path.$to); 
		 
		}
		
		function storeTY($data)
		{
		   
		   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
		   $tosave = array(); 
		   foreach ($data as $key=>$val)
		    {
			  if (stripos($key, 'op_ostatus_')!==false)
			   {
			      $n = str_replace('op_ostatus_', '', $key); 
				  $status = $val; 
				  $payment_id = $data['op_opayment_'.$n]; 
				  $article_id = $data['op_oarticle_'.$n]; 
				  $mode = $data['op_omode_'.$n]; 
				  $lang = $data['op_olang_'.$n]; 
				  if (empty($payment_id)) continue; 
				  if (empty($article_id)) continue; 
				  $ndata = array(); 
				  $ndata['order_status'] = $status; 
				  $ndata['payment_id'] = $payment_id; 
				  $ndata['article_id'] = $article_id; 
				  $cl = strtolower(str_replace('-', '_', $lang)); 
				  $ndata['language'] = $lang; 
				  $ndata['mode'] = $mode; 
				  
				  $tosave[] = $ndata; 
				  
			   }
			}
			OPCconfig::store('ty_page', 'ty_page', 0, $tosave); 
		}
		
		function store()
		{
		
	    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
	    // load basic stuff:
	    OPCLang::loadLang(); 

		$this->removeCache(); 		
		
		$this->loadVmConfig(); 
		$user =& JFactory::getUser();

		
		
		 jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		 jimport('joomla.filesystem.archive');

	        $msg = '';
			
		$rename = JRequest::getVar('rename_to_custom', false); 
		if ($rename)
		$msg .= $this->renameTheme(); 

		 $db = JFactory::getDBO();
		 $data = JRequest::get('post');
		
		$this->storeTY($data); 
		
		 $cfg = "<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date 31.March 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*/




";

$cfg .= '
		  if (!class_exists(\'VmConfig\'))
		  require(JPATH_ADMINISTRATOR.DS.\'components\'.DS.\'com_virtuemart\'.DS.\'helpers\'.DS.\'config.php\'); 
		  VmConfig::loadConfig(); 

';

	if (!empty($data['delete_ht']))
	{
	   if (JFile::delete(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'js'.DS.'.htaccess')===false)
	     {
		   $msg .= JText::sprintf('COM_VIRTUEMART_STRING_DELETED_ERROR', JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'js'.DS.'.htaccess'); 
		 }
	}

	if (!empty($data['opc_cr_type']))
		$cfg .= ' $opc_cr_type = \''.$data['opc_cr_type'].'\'; '; 
	
	if (isset($data['bt_fields_from']))
		$cfg .= ' $bt_fields_from = \''.$data['bt_fields_from'].'\'; '; 
	
	if (isset($data['op_default_shipping_search']))
	{
	   $cfg .= ' $op_default_shipping_search = array(); '; 
	   if (is_array($data['op_default_shipping_search']))
	   {
	     $i = 0; 
	     foreach ($data['op_default_shipping_search'] as $key=>$val)
		  {
		     if (empty($val)) continue; 
		     $cfg .= ' $op_default_shipping_search['.$i.'] = "'.str_replace('"', '\"', $val).'"; ';    
			 $i++; 
		  }
	   }
	   else
	   {
		     $cfg .= ' $op_default_shipping_search[0] = "'.str_replace('"', '\"', $val).'"; ';    
	      
	   }
	}
	
	if (!empty($data['home_vat_countries']))
	 {
	    $home = str_replace('"', '', $data['home_vat_countries']); 
		$cfg .= ' $home_vat_countries = "'.$home.'"; ';    
	 }
	 
	 
	 
	
	 if (isset($data['use_original_basket']))
    $cfg .= '$use_original_basket = true;
    ';
    else $cfg .= '$use_original_basket = false; 
    ';
	//opc_php_js
	//theme_fix1
	if (isset($data['theme_fix1']))
    $cfg .= '$theme_fix1 = true;
    ';
    else $cfg .= '$theme_fix1 = false; 
    ';

	if (isset($data['opc_override_registration']))
	{
	$cfg .= '$opc_override_registration = true; 
    ';
	$this->enableOpcRegistration(true); 
	}
	else
	{
	$this->enableOpcRegistration(false); 
	}
	
if (isset($data['opc_euvat']))
{
    $cfg .= '$opc_euvat = true;
    ';

$coref = array();  
 $ulist = $this->getUserFieldsLists($coref); 
 
 $found = false; 
 $published = 1; 
 $datau = array(); 
  $datau2 = array(); 
 foreach ($ulist as $key=>$val)
  {
     if ($val->name == 'opc_vat')
	 {
	 $published = $val->published; 
	 $found = true;
	 if (empty($published))
	  foreach ($val as $key2=>$v)
	   {
	     $datau[$key2] = $v; 
	   }
	 }
	 
	 if ($val->name == 'opc_vat_info')
	 {
	 $published2 = $val->published; 
	 $found = true;
	
	 if (empty($published2))
	  foreach ($val as $key2=>$v)
	   {
	     $datau2[$key2] = $v; 
	   }
	 }
	 
	
	 
  }	
 
 
  if ((!$found) || (empty($published) || (empty($published2))))
   {
      $modelu = new VirtueMartModelUserfields();
	  if (empty($datau))
	   {
	   
		 
		 $datau = array (
  'virtuemart_userfield_id' => '0',
  'virtuemart_vendor_id' => '0',
  'userfield_jplugin_id' => '0',
  'name' => 'opc_vat',
  'title' => 'COM_ONEPAGE_EUVAT_FIELD',
  'description' => '',
  'type' => 'text',
  'maxlength' => '25',
  'size' => '30',
  'required' => '0',
  'cols' => '0',
  'rows' => '0',
  'value' => '',
  'default' => NULL,
  'registration' => '1',
  'shipment' => '0',
  'account' => '1',
  'readonly' => '0',
  'calculated' => '0',
  'sys' => '0',
  'params' => '',
  'ordering' => '99',
  'shared' => '0',
  'published' => '1',
  'created_on' => '0000-00-00 00:00:00',
  'created_by' => '0',
  'modified_on' => '0000-00-00 00:00:00',
  'modified_by' => '0',
  'locked_on' => '0000-00-00 00:00:00',
  'locked_by' => '0', );
  }
   else
	   {
	     $datau['published'] = 1; 
	   }
  if (empty($datau2)) {
  $datau2 = array (
  'virtuemart_userfield_id' => '0',
  'virtuemart_vendor_id' => '0',
  'userfield_jplugin_id' => '0',
  'name' => 'opc_vat_info',
  'title' => 'COM_ONEPAGE_EUVAT_FIELD_INFO',
  'description' => '',
  'type' => 'hidden',
  'maxlength' => '1000',
  'size' => NULL,
  'required' => '0',
  'cols' => '0',
  'rows' => '0',
  'value' => '',
  'default' => NULL,
  'registration' => '1',
  'shipment' => '0',
  'account' => '1',
  'readonly' => '0',
  'calculated' => '0',
  'sys' => '0',
  'params' => '',
  'ordering' => '99',
  'shared' => '0',
  'published' => '1',
  'created_on' => '0000-00-00 00:00:00',
  'created_by' => '0',
  'modified_on' => '0000-00-00 00:00:00',
  'modified_by' => '0',
  'locked_on' => '0000-00-00 00:00:00',
  'locked_by' => '0',
  
);
		 
		 
		 
	   }
	   else
	   {
	     $datau2['published'] = 1; 
	   }
	   $modelu->store($datau); 
	   $modelu->store($datau2); 
   }
	
}
else
{
   $q = 'update #__virtuemart_userfields set published = 0 where name LIKE "opc_vat" or name LIKE "opc_vat_info"'; 
   $db = JFactory::getDBO(); 
   $db->setQuery($q); 
   $db->query(); 
}
if (isset($data['opc_euvat_button']))
{
    $cfg .= '$opc_euvat_button = true;
    ';
 
 
}

if (isset($data['opc_euvat_contrymatch']))
{
    $cfg .= '$opc_euvat_contrymatch = true;
    ';
 
 
}


	//disable_check
	if (isset($data['opc_no_activation']))
    $cfg .= '$opc_no_activation = true;
    ';
    else $cfg .= '$opc_no_activation = false; 
    ';

	
	//disable_check
	if (isset($data['disable_check']))
    $cfg .= '$disable_check = true;
    ';
    else $cfg .= '$disable_check = false; 
    ';
	
		 if (isset($data['opc_php_js2']))
    $cfg .= '$opc_php_js2 = true;
    ';
    else $cfg .= '$opc_php_js2 = false; 
    ';

	
	//only_one_shipping_address_hidden
	 if (isset($data['only_one_shipping_address_hidden']))
    $cfg .= '$only_one_shipping_address_hidden = true;
    ';
    else $cfg .= '$only_one_shipping_address_hidden = false; 
    ';
		 if (isset($data['opc_only_parent_links']))
    $cfg .= '$opc_only_parent_links = true;
    ';
    else $cfg .= '$opc_only_parent_links = false; 
    ';
	
	
	//opc_show_weight
	if (isset($data['opc_show_weight']))
    $cfg .= '$opc_show_weight = true;
    ';
    else $cfg .= '$opc_show_weight = false; 
    ';
	
	if (isset($data['opc_dynamic_lines']))
    $cfg .= '$opc_dynamic_lines = true;
    ';
    else $cfg .= '$opc_dynamic_lines = false; 
    ';
	
	
	
		 if (isset($data['opc_editable_attributes']))
    $cfg .= '$opc_editable_attributes = true;
    ';
    else $cfg .= '$opc_editable_attributes = false; 
    ';
	
	
	 if (isset($data['op_customer_shipping']))
    $cfg .= '$op_customer_shipping = true;
    ';
    else $cfg .= '$op_customer_shipping = false; 
    ';

	//allow_sg_update
	 if (isset($data['allow_sg_update']))
    $cfg .= '$allow_sg_update = true;
    ';
    else $cfg .= '$allow_sg_update = false; 
    ';
	
	 if (isset($data['allow_sg_update_logged']))
    $cfg .= '$allow_sg_update_logged = true;
    ';
    else $cfg .= '$allow_sg_update_logged = false; 
    ';
	
	
	 if (isset($data['do_not_allow_gift_deletion']))
    $cfg .= '$do_not_allow_gift_deletion = true;
    ';
    else $cfg .= '$do_not_allow_gift_deletion = false; 
    ';
	
	$gift_order_statuses = JRequest::getVar('gift_order_statuses', array());
	if (empty($gift_order_statuses))
	{
	  $cfg .= ' $gift_order_statuses = array(); '; 
	}
	else
	 {
	    $str = var_export($gift_order_statuses, true); 
		$cfg .= "\n".' $gift_order_statuses = '.$str.';'."\n"; 
	 }
	
	//opc_async 
	 if (isset($data['opc_async']))
    $cfg .= '$opc_async = true;
    ';
    else $cfg .= '$opc_async = false; 
    ';
	
	 if (isset($data['use_free_text']))
    $cfg .= '$use_free_text = true;
    ';
    else $cfg .= '$use_free_text = false; 
    ';
	
    if (isset($data['disable_op']))
    $cfg .= '$disable_onepage = true;
    ';
    else $cfg .= '$disable_onepage = false; 
    ';
	
	
    if (isset($data['opc_italian_checkbox']))
    $cfg .= '$opc_italian_checkbox = true;
    ';
    else $cfg .= '$opc_italian_checkbox = false; 
    ';
	
	if (isset($data['opc_acymailing_checkbox']))
    $cfg .= '$opc_acymailing_checkbox = true;
    ';
    else $cfg .= '$opc_acymailing_checkbox = false; 
    ';
	
	$data['opc_acy_id'] = (int)$data['opc_acy_id']; 
	$cfg .= ' $opc_acy_id = (int)"'.$data['opc_acy_id'].'"; '; 
	
	//opc_do_not_alter_registration
	if (isset($data['opc_do_not_alter_registration']))
    $cfg .= '$opc_do_not_alter_registration = true;
    ';
    else $cfg .= '$opc_do_not_alter_registration = false; 
    ';
	
	
	if (isset($data['opc_debug']))
    $cfg .= '$opc_debug = true;
    ';
    else $cfg .= '$opc_debug = false; 
    ';

	if (isset($data['opc_memory']))
    $cfg .= ' $opc_memory = \''.addslashes($data['opc_memory']).'\'; '; 
    
	if (isset($data['rupostel_email']))
	$cfg .= ' $rupostel_email = \''.addslashes($data['rupostel_email']).'\'; '; 
	
    if (isset($data['opc_plugin_order']) && is_numeric($data['opc_plugin_order']))
    $cfg .= '$opc_plugin_order = \''.$data['opc_plugin_order'].'\';
    ';
    else $cfg .= '$opc_plugin_order = -9999; 
    ';
	
	//
	if (isset($data['opc_disable_for_mobiles']))
    $cfg .= '$opc_disable_for_mobiles = true;
    ';
    else $cfg .= '$opc_disable_for_mobiles = false; 
    ';

	
	if (isset($data['opc_request_cache']))
    $cfg .= '$opc_request_cache = true;
    ';
    else $cfg .= '$opc_request_cache = false; 
    ';

	 if (isset($data['opc_check_username']))
      $cfg .= '$opc_check_username = true;';
      else $cfg .= '$opc_check_username = false;';

	 if (isset($data['opc_rtl']))
      $cfg .= '$opc_rtl = true;';
      else $cfg .= '$opc_rtl = false;';  
	  
	  
	  
	
	if (!empty($data['opc_no_duplicit_username']) && (empty($data['op_usernameisemail'])))
	{
    $cfg .= '$opc_no_duplicit_username = true;
    ';
	$cfg .= '$opc_check_username = true;';
	}
    else $cfg .= '$opc_no_duplicit_username = false; 
    ';

if (isset($data['klarna_se_get_address']))
      $cfg .= '$klarna_se_get_address = true;';
      else $cfg .= '$klarna_se_get_address = false;';


if (isset($data['ajaxify_cart']))
      $cfg .= '$ajaxify_cart = true;';
      else $cfg .= '$ajaxify_cart = false;';

	  
	  
if (isset($data['opc_check_email']))
      $cfg .= '$opc_check_email = true;';
      else $cfg .= '$opc_check_email = false;';

	
	if (!empty($data['opc_no_duplicit_email']))
	{
    $cfg .= '$opc_no_duplicit_email = true;
    ';
	$cfg .= '$opc_check_email = true;';
	$cfg .= '$allow_duplicit = false;';
	}
    else $cfg .= '$opc_no_duplicit_email = false; 
    ';

	
	
	//show_single_tax
    if (!empty($data['show_single_tax']))
    $cfg .= '$show_single_tax = true;
    ';
    else $cfg .= '$show_single_tax = false; 
    ';
	
	 if (!empty($data['opc_calc_cache']))
    $cfg .= '$opc_calc_cache = true;
    ';
    else $cfg .= '$opc_calc_cache = false; 
    ';
	
	
	  if (!empty($data['visitor_shopper_group']))
    $cfg .= '$visitor_shopper_group = "'.$data['visitor_shopper_group'].'";
    ';
    else $cfg .= '$visitor_shopper_group = 0; 
    ';
	
		  if (!empty($data['no_coupon_ajax']))
    $cfg .= '$no_coupon_ajax = true;
    ';
    else $cfg .= '$no_coupon_ajax = false; 
    ';

	
	  if (!empty($data['business_shopper_group']))
    $cfg .= '$business_shopper_group = "'.$data['business_shopper_group'].'";
    ';
    else $cfg .= '$business_shopper_group = 0; 
    ';

	  if (!empty($data['zero_total_status']))
    $cfg .= '$zero_total_status = "'.$data['zero_total_status'].'";
    ';
    else $cfg .= '$zero_total_status = "C"; 
    ';
	
	//op_never_log_in
//option_sgroup
	  if (!empty($data['option_sgroup']))
    $cfg .= '$option_sgroup = '.(int)$data['option_sgroup'].';;
    ';
    else $cfg .= '$option_sgroup = false; 
    ';


	  if (!empty($data['op_never_log_in']))
    $cfg .= '$op_never_log_in = true;
    ';
    else $cfg .= '$op_never_log_in = false; 
    ';
	//no_alerts
	if (!empty($data['no_alerts']))
    $cfg .= '$no_alerts = true;
    ';
    else $cfg .= '$no_alerts = false; 
    ';


	if (!empty($data['disable_ship_to_on_zero_weight']))
    $cfg .= '$disable_ship_to_on_zero_weight = true;
    ';
    else $cfg .= '$disable_ship_to_on_zero_weight = false; 
    ';
	
	//
	if (!empty($data['op_use_geolocator']))
    $cfg .= '$op_use_geolocator = true;
    ';
    else $cfg .= '$op_use_geolocator = false; 
    ';
	
	
	if (!empty($data['append_details']))
    $cfg .= '$append_details = true;
    ';
    else $cfg .= '$append_details = false; 
    ';
	
	if (!empty($data['op_redirect_joomla_to_vm']))
    $cfg .= '$op_redirect_joomla_to_vm = true;
    ';
    else $cfg .= '$op_redirect_joomla_to_vm = false; 
    ';
	
	
	
		 if (!empty($data['password_clear_text']))
    $cfg .= '$password_clear_text = true;
    ';
    else $cfg .= '$password_clear_text = false; 
    ';

	
	$cfg .= ' $dpps_search = array(); $dpps_disable = array(); $dpps_default=array(); '."\n";
	if (!empty($data['disable_payment_per_shipping']))
	 {
	   $search = JRequest::getVar('dpps_search'); 
	   $cfg .= ' $disable_payment_per_shipping = true; '; 
	   foreach ($search as $i=>$v)
	    {
		  if ((!empty($data['dpps_disable'][$i])) && (!empty($v)))
		  {
		  $val = urlencode($v);
		  $val = str_replace("'", "\\'", $val); 
	      $cfg .= ' $dpps_search['.$i.'] = '."'".$val."';\n"; 
		  $cfg .= ' $dpps_disable['.$i.'] = '."'".$data['dpps_disable'][$i]."';\n"; 
		  if ($data['dpps_default'][$i] != $data['dpps_disable'][$i])
		  $cfg .= ' $dpps_default['.$i.'] = '."'".$data['dpps_default'][$i]."';\n"; 
		  else $cfg .= ' $dpps_default['.$i.'] = \'\'; ';  
		  }
		}
	 }
	 else 
	 $cfg .= ' $disable_payment_per_shipping = false; '; 
	

		  if (!empty($data['euvat_shopper_group']))
    $cfg .= '$euvat_shopper_group = "'.$data['euvat_shopper_group'].'";
    ';
    else $cfg .= '$euvat_shopper_group = 0; 
    ';
	
	
		
  if (!empty($data['payment_discount_before']))
    $cfg .= '$payment_discount_before = true;
    ';
    else $cfg .= '$payment_discount_before = false; 
    ';
	
	
  if (!empty($data['only_one_shipping_address']))
    $cfg .= '$only_one_shipping_address = true;
    ';
    else $cfg .= '$only_one_shipping_address = false; 
    ';



	 if (isset($data['no_extra_product_info']))
    $cfg .= '$no_extra_product_info = true;
    ';
    else $cfg .= '$no_extra_product_info = false; 
    ';

	
		 if (isset($data['enable_captcha_unlogged']))
    $cfg .= '$enable_captcha_unlogged = true;
    ';
    else $cfg .= '$enable_captcha_unlogged = false; 
    ';
	
	 if (isset($data['send_pending_mail']))
    $cfg .= '$send_pending_mail = true;
    ';
    else $cfg .= '$send_pending_mail = false; 
    ';
	
	 if (isset($data['enable_captcha_logged']))
    $cfg .= '$enable_captcha_logged = true;
    ';
    else $cfg .= '$enable_captcha_logged = false; 
    ';

	
	 if (isset($data['hide_advertise']))
    $cfg .= '$hide_advertise = true;
    ';
    else $cfg .= '$hide_advertise = false; 
    ';

	
	 if (isset($data['hide_payment_if_one']))
    $cfg .= '$hide_payment_if_one = true;
    ';
    else $cfg .= '$hide_payment_if_one = false; 
    ';
	
	if (!empty($data['disable_op']))
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update #__extensions set enabled = 0 where element = 'opc' and type = 'plugin' limit 2 "; 
	   
	  }
	  else
	  {
	    $q = "update #__plugins set published = 0 where element = 'opc'  limit 2 "; 
	  }
	  $db =& JFactory::getDBO(); 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $msg .= $e; }
	  
	  
	  
	  
	}
	else
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update #__extensions set enabled = 1 where element = 'opc' and type = 'plugin' and folder = 'system' limit 2 "; 
	   // disable other opc solutions: 
	   $q2 = "update #__extensions set enabled = 0 where element = 'onestepcheckout' and type = 'plugin' and folder = 'system' "; 
	   $q3 = "update #__extensions set enabled = 0 where element = 'vponepagecheckout' and type = 'plugin' and folder = 'system' "; 
	  }
	  else
	  {
	    $q = "update #__plugins set published = 1 where element = 'opc' and folder = 'system'  limit 2 "; 
		$q2 = "update #__plugins set published = 0 where element = 'onestepcheckout' and folder = 'system' "; 
		$q3 = "update #__plugins set published = 0 where element = 'vponepagecheckout' and folder = 'system' "; 
	  }
	  $db = JFactory::getDBO(); 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $msg .= $e; }
	  
	  
	  $db = JFactory::getDBO(); 
	  $db->setQuery($q2); 
	  $db->query(); 

	  $db = JFactory::getDBO(); 
	  $db->setQuery($q3); 
	  $db->query(); 

	  
	}
    
    
		 $cfg .= "
/* If user in Optional, normal, silent registration sets email which already exists and is registered 
* and you set this to true
* his order details will be saved but he will not be added to joomla registration and checkout can continue
* if registration type allows username and password which is already registered but his new password is not the same as in DB then checkout will return error
*/
";

if (isset($data['email_after']))
      $cfg .= '$email_after = true;
      ';
      else $cfg .= '$email_after = false;
      ';
	  
if (empty($data['opc_link_type']))
      $cfg .= '$opc_link_type = 0;
      ';
      else 
	   $cfg .= '$opc_link_type = '.$data['opc_link_type'].'; 
      ';





	  
	  if (!empty($data['business_fields']))
	  {
	    foreach ($data['business_fields'] as $k=>$line)
		 {
		   
		   
		   $data['business_fields'][$k] = "'".$line."'"; 
		 }
		 // special cases
		 if (in_array('password', $data['business_fields'])) $data['business_fields'][] = 'opc_password'; 
		 if (in_array('username', $data['business_fields'])) $data['business_fields'][] = 'opc_username'; 
		 
		 $newa = implode(',', $data['business_fields']); 
	    $cfg .= ' $business_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $business_fields = array(); '; 
	  
	  
	   if (!empty($data['custom_rendering_fields']))
	  {
	    foreach ($data['custom_rendering_fields'] as $k=>$line)
		 {
		   $data['custom_rendering_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['custom_rendering_fields']); 
	    $cfg .= ' $custom_rendering_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $custom_rendering_fields = array(); '; 
	  
	  
	    if (!empty($data['shipping_obligatory_fields']))
	  {
	    foreach ($data['shipping_obligatory_fields'] as $k=>$line)
		 {
		   $data['shipping_obligatory_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['shipping_obligatory_fields']); 
	    $cfg .= ' $shipping_obligatory_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $shipping_obligatory_fields = array(); '; 
	  
if (!empty($data['op_disable_shipping']))
 $cfg .= '$op_disable_shipping = true;
      ';
      else $cfg .= '$op_disable_shipping = false;
      ';
 
if (!empty($data['op_disable_shipto']))
 $cfg .= '$op_disable_shipto = true;
      ';
      else $cfg .= '$op_disable_shipto = false;
      ';
 

 if (!empty($data['op_no_display_name']))
 $cfg .= '$op_no_display_name = true;
      ';
      else $cfg .= '$op_no_display_name = false;
      ';
if (!empty($data['op_create_account_unchecked']))
 $cfg .= '$op_create_account_unchecked = true;
      ';
      else $cfg .= '$op_create_account_unchecked = false;
      ';	  

/*	  
	  	  if (!empty($data['tos_itemid']))
	    $cfg .= ' $tos_itemid = "'.$data['tos_itemid'].'"; '; 
	*/
	

	  
if (!empty($data['product_price_display']))
{
  $cfg .= ' $product_price_display = "'.$data['product_price_display'].'";'."\n"; 
}

if (!empty($data['subtotal_price_display']))
{
  $cfg .= ' $subtotal_price_display = "'.$data['subtotal_price_display'].'";'."\n"; 
}

if (!empty($data['opc_usmode']))
{
  $cfg .= ' $opc_usmode = true; '; 
}
else
{
  $cfg .= ' $opc_usmode = false; '; 
}


if (!empty($data['full_tos_logged']))
{
  $cfg .= ' $full_tos_logged = true; '; 
}
else
{
  $cfg .= ' $full_tos_logged = false; '; 
}

if (!empty($data['tos_scrollable']))
{
  $cfg .= ' $tos_scrollable = true; '; 
}
else
{
  $cfg .= ' $tos_scrollable = false; '; 
}

$legal_info = VmConfig::get('oncheckout_show_legal_info', '1'); 
if ((!empty($data['full_tos_unlogged'])))
{
  $cfg .= ' $full_tos_unlogged = true; '; 
}
else
{
  $cfg .= ' $full_tos_unlogged = false; '; 
}

$tosx = VmConfig::get('agree_to_tos_onorder', '1');


if (!empty($data['tos_logged']))
{
  $cfg .= ' $tos_logged = true; '; 
}
else
{
  $cfg .= ' $tos_logged = false; '; 
}



if (!empty($data['tos_unlogged']))
{
  $cfg .= ' $tos_unlogged = true; '; 
}
else
{
  $cfg .= ' $tos_unlogged = false; '; 
}



if (!empty($data['opc_email_in_bt']))
{
  $cfg .= ' $opc_email_in_bt = true; '; 
}
else
{
  $cfg .= ' $opc_email_in_bt = false; '; 
}


if (!empty($data['double_email']))
{
  $cfg .= ' $double_email = true; '; 
}
else
{
  $cfg .= ' $double_email = false; '; 
}

if (!empty($data['coupon_price_display']))
{
  $cfg .= ' $coupon_price_display = "'.$data['coupon_price_display'].'";'."\n"; 
}

if (!empty($data['other_discount_display']))
{
  $cfg .= ' $other_discount_display = "'.$data['other_discount_display'].'";'."\n"; 
}

 
if (isset($data['agreed_notchecked']))
      $cfg .= '$agreed_notchecked = true;
      ';
      else $cfg .= '$agreed_notchecked = false;
      ';

if ((int)$data['opc_default_shipping']===1)
      $cfg .= '
	  $opc_default_shipping = 1; 
	  $op_default_shipping_zero = true;
	  $shipping_inside_choose = false; 
      ';
      else 
	  if ((int)$data['opc_default_shipping']===3)
	  {
	   $cfg .= ' $shipping_inside_choose = true; 
	    $opc_default_shipping = 3; 
	   ';
	  
	  }
	  else
	  $cfg .= '
	   $op_default_shipping_zero = false;
	   $opc_default_shipping = '.(int)$data['opc_default_shipping'].';
       $shipping_inside_choose = false;
	  ';
	  
if (!empty($data['never_count_tax_on_shipping']))
      $cfg .= '$never_count_tax_on_shipping = true;
      ';
      else $cfg .= '$never_count_tax_on_shipping = false;
      ';

if (!empty($data['save_shipping_with_tax']))
      $cfg .= '$save_shipping_with_tax = true;
      ';
      else $cfg .= '$save_shipping_with_tax = false;
      ';


	  
if (isset($data['op_no_basket']))
      $cfg .= '$op_no_basket = true;
      ';
      else $cfg .= '$op_no_basket = false;
      ';
	  

if (isset($data['shipping_template']))
      $cfg .= '$shipping_template = true;
      ';
      else $cfg .= '$shipping_template = false;
      ';

	  
	  $opclang = JRequest::getVar('opc_lang_orig', ''); 
      require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php');

	  $currency_config = JRequest::getVar('country_currency', array()); 
	  $aset = array(); 
	  OPCconfig::clearConfig('currency_config'); 
	  if (!empty($currency_config))
	  {
	    
	    foreach ($currency_config as $cid=>$arr)
		 {
		    if (!empty($arr))
		    foreach ($arr as $country_id)
			 {
			    if (!empty($aset[$country_id])) continue; 
			    $aset[$country_id] = $country_id; 
				$q = 'select country_2_code from #__virtuemart_countries where virtuemart_country_id = '.(int)$country_id.' limit 0,1'; 
				$db = JFactory::getDBO(); 
				$db->setQuery($q); 
				$c2c = $db->loadResult(); 
				if (!empty($c2c))
				OPCconfig::store('currency_config', $c2c, 0, (int)$cid); 
			 }
		 }
	     
		
	  }
	  
	  if (!empty($data['currency_plg_can_change']))
	  OPCconfig::store('currency_config', 'can_change', 0, true); 
	  else 
	  OPCconfig::store('currency_config', 'can_change', 0, false); 
		
	  if (!empty($data['currency_plg']))
	  $this->setPluginEnabled('opc_currency', 'system', true); 
	  else
	  $this->setPluginEnabled('opc_currency', 'system', false); 
	  
	  
	  
	  OPCconfig::store('opc_config', 'op_articleid'.$opclang, 0, $data['op_articleid']); 
	  OPCconfig::store('opc_config', 'adc_op_articleid'.$opclang, 0, $data['adc_op_articleid']); 
	  OPCconfig::store('opc_config', 'tos_itemid'.$opclang, 0, $data['tos_itemid']); 
	  OPCconfig::store('opc_config', 'newitemid'.$opclang, 0, $data['newitemid']); 
	  OPCconfig::store('opc_config', 'op_customitemidty'.$opclang, 0, $data['op_customitemidty']); 
	  
	  /*
	  if (!empty($data['op_customitemidty']))
 {
  $cfg .= '$op_customitemidty = "'.(int)trim($data['op_customitemidty']).'";
      ';
 }
*/

/* 
if (!empty($data['newitemid']))
 $cfg .= '$newitemid = "'.trim($data['newitemid']).'";
      ';
      else $cfg .= '$newitemid = "";
      ';
*/
	  
	  //
	  /*
	  if (!empty($data['op_articleid']))
      $cfg .= '$op_articleid = "'.$data['op_articleid'].'";
	  ';
	  else $cfg .= '$op_articleid = "";
	  ';
	  

	  	  if (!empty($data['adc_op_articleid']))
      $cfg .= '$adc_op_articleid = "'.$data['adc_op_articleid'].'";
	  ';
	  else $cfg .= '$adc_op_articleid = "";
	  ';
     */

if (isset($data['op_sum_tax']))
      $cfg .= '$op_sum_tax = true;
      ';
      else $cfg .= '$op_sum_tax = false;
      ';

if (isset($data['op_last_field']))
      $cfg .= '$op_last_field = true;
      ';
      else $cfg .= '$op_last_field = false;
      ';


if (!empty($data['op_default_zip']))
{
	$cfg .= '$op_default_zip = "'.urlencode($data['op_default_zip']).'"; 
	';
}
else 
{
    if (($data['op_default_zip'] === '0')  || ($data['op_default_zip'] === 0))
	$cfg .= '$op_default_zip = 0; ';
	else
	$cfg .= '$op_default_zip = "99999";
	'; 
}



if (!empty($data['op_numrelated']) && (is_numeric($data['op_numrelated'])))
      $cfg .= '$op_numrelated = "'.$data['op_numrelated'].'"; 
      ';
      else $cfg .= '$op_numrelated = false;
      ';


$cfg .= '
// auto config by template
$cut_login = false;
      ';

if (isset($data['op_delay_ship']))
      $cfg .= '$op_delay_ship = true;
      ';
      else $cfg .= '$op_delay_ship = false;
      ';

if (isset($data['op_loader']))
      $cfg .= '$op_loader = true;
      ';
      else $cfg .= '$op_loader = false;
      ';


if (isset($data['op_usernameisemail']))
      $cfg .= '$op_usernameisemail = true;
      ';
      else $cfg .= '$op_usernameisemail = false;
      ';
      
      
if (isset($data['no_continue_link_bottom']))
      $cfg .= '$no_continue_link_bottom = true;
      ';
      else $cfg .= '$no_continue_link_bottom = false;
      ';

if (isset($data['op_default_state']))
      $cfg .= '$op_default_state = true;
      ';
      else $cfg .= '$op_default_state = false;
      ';
       
if (isset($data['list_userfields_override']))
      $cfg .= '$list_userfields_override = true;
      ';
      else $cfg .= '$list_userfields_override = false;
      ';
      
if (isset($data['no_jscheck']))
      $cfg .= '$no_jscheck = true;
      ';
      else $cfg .= '$no_jscheck = true;
      ';
      
if (isset($data['op_dontloadajax']))
      $cfg .= '$op_dontloadajax = true;
      		   $no_jscheck = true;
      ';
      else $cfg .= '$op_dontloadajax = false;
      ';
      
if (isset($data['shipping_error_override']))
		{
		$serr = urlencode($data['shipping_error_override']);
      $cfg .= '$shipping_error_override = "'.$serr.'";
      ';
       }
      else $cfg .= '$shipping_error_override = "";
      ';


if (isset($data['op_zero_weight_override']))
      $cfg .= '$op_zero_weight_override = true;
      ';
      else $cfg .= '$op_zero_weight_override = false;
      ';


if (isset($data['email_after']))
      $cfg .= '$email_after = true;
      ';
      else $cfg .= '$email_after = false;
      ';

if (isset($data['override_basket']))
      $cfg .= '$override_basket = true;
      ';
      else $cfg .= '$override_basket = false;
      ';

	  
if ($data['selected_template'] != 'default')
{
   $data['selected_template'] = JFile::makeSafe($data['selected_template']); 
      $cfg .= '$selected_template = "'.$data['selected_template'].'";
	    if (empty($is_admin))
		{
		$selected_template_override = JRequest::getVar(\'opc_theme\', \'\'); 
		if (!empty($selected_template_override))
		{
		$test = str_replace(\'_\', \'\', $selected_template_override); 
		if (ctype_alnum($test))
		 {
		   $selected_template = $selected_template_override; 
		 }
		}
		}
		
      ';
}
else
{
       $cfg .= '$selected_template = ""; 
       ';
}


if (!empty($data['mobile_template']))
{

  $data['mobile_template'] = JFile::makeSafe($data['mobile_template']); 
  
  $cfg .= ' 
  $is_mobile = false; 
  $mobile_template = "'.$data['mobile_template'].'";
  if (empty($is_admin))
  if (empty($selected_template_override))
  {
$app = JFactory::getApplication(); 
$jtouch = $app->getUserStateFromRequest(\'jtpl\', \'jtpl\', -1, \'int\');
if (($jtouch > 0) || (defined(\'OPC_DETECTED_DEVICE\') && ((constant(\'OPC_DETECTED_DEVICE\')==\'MOBILE\') || ((constant(\'OPC_DETECTED_DEVICE\')==\'TABLET\')))))
 {
   $is_mobile = true; 
   $selected_template = $mobile_template; 
 }
 
 
 }
  
  ';
}

if (!isset($data['adwords_timeout']))
$data['adwords_timeout'] = 4; 

$op_timeout = (int)$data['adwords_timeout']; 
$cfg .= ' $adwords_timeout = '.$op_timeout.'; '; 

if (isset($data['dont_show_inclship']))
      $cfg .= '$dont_show_inclship = true;
      ';
      else $cfg .= '$dont_show_inclship = false;
      ';

if (isset($data['no_continue_link']))
      $cfg .= '$no_continue_link = true;
      ';
      else $cfg .= '$no_continue_link = false;
      ';

	  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'tracking.php'); 
	  $modelT = new JModelTracking(); 
	  $modelT->setEnabled(); 
	  
	 // removed in 2.0.207
	 /*
	 if (false)
if (isset($data['adwords_enabled_0']) && (!empty($_POST['adwords_code_0'])))
{
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
	
   $code = JRequest::getVar('adwords_code_0', '', 'post', 'string', JREQUEST_ALLOWRAW); // $_POST['adwords_code_0']; $code = $_POST['adwords_code_0'];
    if (JFile::write(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html', $code) === false)
    {
         $msg .= 'Cannot write to: '.JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html<br />';
    }
    else
    {
    $cfg .= '
    $adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
	$adwords_name[0] = "body.html";
	
        
 		$adwords_amount[0] = "'.$data['adwords_amount_0'].'";
        $adwords_enabled[0] = true;
 	';
  }
}
else
{
 $cfg .= '
 	$adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
 	$adwords_name[0] = "body.html";
 	$adwords_amount[0] = "'.$data['adwords_amount_0'].'";
 	';
	
	jimport('joomla.filesystem.file');
	$code = ""; 
	if (JFile::write(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html', $code) === false)
    {
         $msg .= 'Cannot write to: '.JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html';
    }
	
}
  */
if (isset($data['no_login_in_template']))
      $cfg .= '$no_login_in_template = true;
      ';
      else $cfg .= '$no_login_in_template = false;
      ';


$cfg .'


/* Following variables are to change shipping or payment to select boxes
*/
';

if (isset($data['shipping_inside']))
      $cfg .= '$shipping_inside = true;
      ';
      else $cfg .= '$shipping_inside = false;
      ';

if (isset($data['payment_inside']))
      $cfg .= '$payment_inside = true;
      ';
      else $cfg .= '$payment_inside = false;
      ';

if (isset($data['payment_saveccv']))
      $cfg .= '$payment_saveccv = true;
      ';
      else $cfg .= '$payment_saveccv = false;
      ';

	  
if (isset($data['payment_advanced']))
      $cfg .= '$payment_advanced = true;
      ';
      else $cfg .= '$payment_advanced = false;
      ';
	  

if (isset($data['fix_encoding']))
      $cfg .= '$fix_encoding = true;
      ';
      else $cfg .= '$fix_encoding = false;
      ';

if (isset($data['fix_encoding_utf8']))
      $cfg .= '$fix_encoding_utf8 = true;
$fix_encoding = false;
      ';
      else $cfg .= '$fix_encoding_utf8 = false;
      ';


if (isset($data['shipping_inside_basket']))
      $cfg .= '$shipping_inside_basket = true;
      ';
      else $cfg .= '$shipping_inside_basket = false;
      ';

if (isset($data['payment_inside_basket']))
      $cfg .= '$payment_inside_basket = true;
      ';
      else $cfg .= '$payment_inside_basket = false;
      ';

if (isset($data['email_only_pok']))
      $cfg .= '$email_only_pok = true;
      ';
      else $cfg .= '$email_only_pok = false;
      ';
      
if (!empty($data['no_taxes_show']))
      $cfg .= '$no_taxes_show = true;
      ';
      else $cfg .= '$no_taxes_show = false;
      ';
      
if (!empty($data['use_order_tax']))
      $cfg .= '$use_order_tax = true;
      ';
      else $cfg .= '$use_order_tax = false;
      ';
      
if (isset($data['no_taxes']))
      $cfg .= '$no_taxes = true;
      ';
      else $cfg .= '$no_taxes = false;
      ';

if (isset($data['never_show_total']))
      $cfg .= '$never_show_total = true;
      ';
      else $cfg .= '$never_show_total = false;
      ';

if (isset($data['email_dontoverride']))
      $cfg .= '$email_dontoverride = true;
      ';
      else $cfg .= '$email_dontoverride = false;
      ';



if (isset($data['allow_duplicit']))
      $cfg .= '$allow_duplicit = true;
      ';
      else $cfg .= '$allow_duplicit = false;
      ';

if (isset($data['show_only_total']))
      $cfg .= '$show_only_total = true;
      ';
      else $cfg .= '$show_only_total = false;
      ';

if (isset($data['show_andrea_view']))
      $cfg .= '$show_andrea_view = true;
      ';
      else $cfg .= '$show_andrea_view = false;
      ';

    
    if (isset($data['always_show_tax']))
    $cfg .= '$always_show_tax = true;
';
    else $cfg .= '$always_show_tax = false;
';
   if (isset($data['always_show_all']))
    $cfg .= '$always_show_all = true;
';
    else $cfg .= '$always_show_all = false;
';


     if (isset($data['add_tax']))
      $cfg .= '$add_tax = true;
      ';
      else $cfg .= '$add_tax = false;
      ';

 if (isset($data['add_tax_to_shipping_problem']))
      $cfg .= '$add_tax_to_shipping_problem = true;
      ';
      else $cfg .= '$add_tax_to_shipping_problem = false;
      ';

 
 if (isset($data['add_tax_to_shipping']))
      $cfg .= '$add_tax_to_shipping = true;
      ';
      else $cfg .= '$add_tax_to_shipping = false;
      ';

 if (isset($data['custom_tax_rate']))
      $cfg .= '$custom_tax_rate = "'.addslashes($data['custom_tax_rate']).'"; 
      ';
      else $cfg .= '$custom_tax_rate = 0;
      ';
 
if (isset($data['opc_auto_coupon']))
      $cfg .= '$opc_auto_coupon = "'.addslashes($data['opc_auto_coupon']).'"; 
      ';
      else $cfg .= '$opc_auto_coupon = \'\';
      ';
  
	 

     if (isset($data['no_decimals']))
      $cfg .= '$no_decimals = true;';
      else $cfg .= '$no_decimals = false;';

     if (isset($data['curr_after']))
      $cfg .= '$curr_after = true;';
      else $cfg .= '$curr_after = false;';

 
	  //
	  if (isset($data['load_min_bootstrap']))
      $cfg .= '$load_min_bootstrap = true;';
      else $cfg .= '$load_min_bootstrap = false;';

	  

	  
    
    $cfg .= "
/*
Set this to true to unlog (from Joomla) all shoppers after purchase
*/
";

 
   if (isset($data['unlog_all_shoppers']))
    $cfg .= '$unlog_all_shoppers = true;
		$no_login_in_template = true; 
';
    else $cfg .= '$unlog_all_shoppers = false;
'; 
  
  // vat_input_id, eu_vat_always_zero, move_vat_shopper_group, zerotax_shopper_group
    if (!empty($data['vat_input_id']))
	  $cfg .= '$vat_input_id = "'.$data['vat_input_id'].'"; '; 
	else $cfg .= '$vat_input_id = ""; '; 

    if (!empty($data['eu_vat_always_zero']))
	  $cfg .= '$eu_vat_always_zero = "'.$data['eu_vat_always_zero'].'"; '; 
	else $cfg .= '$eu_vat_always_zero = ""; '; 

	if (empty($data['vat_except'])) $data['vat_except'] = ''; 
    $te = strtoupper($data['vat_except']); 
	$eu = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'); 
	
	
    if (!empty($data['vat_except']))
	{
	  if (!in_array($te, $eu)) 
	 {
	 $msg .= 'Country code is not valid for EU ! Code used:'.$data['vat_except'].'<br />'; 
	 $msg .= "These are valid codes : 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' without apostrophies <br />"; 
	 }
	  $cfg .= '$vat_except = "'.$data['vat_except'].'"; '; 
	 }
	else $cfg .= '$vat_except = ""; '; 
	
	 if (!empty($data['move_vat_shopper_group']))
	  $cfg .= '$move_vat_shopper_group = "'.$data['move_vat_shopper_group'].'"; '; 
	 else $cfg .= '$move_vat_shopper_group = ""; '; 
	
	if (!empty($data['zerotax_shopper_group']))
	{
	  $str = ''; 
	  foreach ($data['zerotax_shopper_group'] as $g)
	   {
	     if (!empty($str)) $str .= ','.$g.'';
		 else $str = "".$g.""; 
	   }
	   $cfg .= ' $zerotax_shopper_group = array('.$str.'); '; 
	}
	else $cfg .= ' $zerotax_shopper_group = array(); '; 
	
$cfg .= " 
/* set this to true if you don't accept other than valid EU VAT id */
";
 if (!empty($data['must_have_valid_vat']))
	  $cfg .= '$must_have_valid_vat = true; '; 
	 else $cfg .= '$must_have_valid_vat = false; '; 

		 $cfg .= "
/*
* Set this to true to unlog (from Joomla) all shoppers after purchase
*/
";
		 if (isset($data['unlog_all_shoppers']))
		 {
		  $cfg .= ' $unlog_all_shoppers = true;
      ';
     }
     else $cfg .= ' $unlog_all_shoppers = false;
     ';
		 
		 $cfg .= "
/* This will disable positive messages on Thank You page in system info box */

";
      

       
    $cfg .= "
/* please check your source code of your country list in your checkout and get exact virtuemart code for your country
* all incompatible shipping methods will be hiddin until customer choses other country
* this will also be preselected in registration and shipping forms
* Your shipping method cannot have 0 index ! Otherwise it will not be set as default
*/     
";
     if (isset($data['default_country']))
     {
      $cfg .= ' $default_shipping_country = "'.$data['default_country'].'";
      ';
     }
     else $cfg .= ' $default_shipping_country = "";
     ';
	 
	 /*
	$cfg .= '
	if (!defined("DEFAULT_COUNTRY"))
	{
	 if (file_exists(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"))
	 {
	  require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		 if (!empty($country_2_code))
		 {
		 $db=&JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = \'".$country_2_code."\' "); 
		 $r = $db->loadResult(); 
		 if (!empty($r)) 
		 $default_shipping_country = $r; 
		 }
	     //$default_shipping_country = 
	   }
	 }
	  define("DEFAULT_COUNTRY", $default_shipping_country); 
	 }
	 else
	 {
	  $default_shipping_country = DEFAULT_COUNTRY; 
	 
	 }
	';  
	*/
		 $cfg .= "
/* since VM 1.1.5 there is paypal new api which can be clicked on image instead of using checkout process
* therefore we can hide it from payments
* These payments will be hidden all the time
* example:  ".'$payments_to_hide = "4,3,5,2";
*/
';
		 
		 $cfg .= "
/* default payment option id
* leave commented or 0 to let VM decide
*/
";
	$pd = $data['default_payment'];
	if (!isset($data['default_payment']) || ($pd == 'default')) $pd = '""';
	$cfg .= '$payment_default = '.$pd.';
	';
	
	
	$cfg .= "
/* turns on google analytics tracking, set to false if you don't use it */
";
    /*
	if ($data['g_analytics']=='1')
	{
	  $cfg .= ' $g_analytics = true;
';
	}
	else 
	  $cfg .= ' $g_analytics = false;
';
    */
	
	$cfg .= "
/* set this to false if you don't want to show full TOS
* if you set show_full_tos, set this variable to one of theses:
* use one of these values:
* 'shop.tos' to read tos from your VirtueMart configuration
* '25' if set to number it will search for article with this ID, extra lines will be removed automatically
* both will be shown without any formatting
*/
";

/* disabled, now differenciated between logged and unlogged within the loader file which is further sent to the template
 	if (isset($data['show_full_tos']))
 	{
 	  $cfg .= ' $show_full_tos = true; 
';
 	} else  	  $cfg .= ' $show_full_tos = false; 
';
*/

	//tos_config
	$opclang = JRequest::getVar('opc_lang_orig', ''); 
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	
	OPCconfig::store('opc_config', 'tos_config'.$opclang, 0, $data['tos_config']); 

	/*
 	$t = $data['tos_config'];
 	$t = trim(strtolower($t));
 	$cfg .= ' $tos_config = "'.$t.'"; 
';
 	*/
 	
	/*
 	if (isset($data['op_show_others']))
 	{
 	  $cfg .= ' $op_show_others = true; 
';
 	} else  	  $cfg .= ' $op_show_others = false; 
	
	
	
';
	*/

 	if (isset($data['op_fix_payment_vat']))
 	{
 	  $cfg .= ' $op_fix_payment_vat = true; 
';
 	} else  	  $cfg .= ' $op_fix_payment_vat = false; 
';

	
 	if (isset($data['op_free_shipping']))
 	{
 	  $cfg .= ' $op_free_shipping = true; 
';
 	} else  	  $cfg .= ' $op_free_shipping = false; 
';

 	
 	$cfg .= "
/* change this variable to your real css path of '>> Proceed to Checkout'
* let's hide 'Proceed to checkout' by CSS
* if it doesn't work, change css path accordingly, i recommend Firefox Firebug to get the path
* but this works for most templates, but if you see 'Proceed to checkout' link, contact me at stan@rupostel.sk
* for rt_mynxx_j15 template use '.cart-checkout-bar {display: none; }'
*/
";
 	
	$cfg .= '
$payment_info = array();
$payment_button = array();
$default_country_array = array();
';
	
	$cfg .= "\n".' /* URLs fetched after checkout encoded by base64_encode */'."\n";
	$cfg .= ' $curl_url = array('; 
    $arr = array(); 
	foreach ($data as $key=>$val)
	 {
	    
	
	    if (strpos($key, 'curl_url_')!==false)
		 {
		   if (!empty($val))
		   $arr[] = "'".base64_encode($val)."'"; 
		 }
	 }
	
	$arrt = implode(',', $arr); 
	if (empty($arr)) $arrt = ''; 
    $cfg .= $arrt.');'."\n"; 

	
	
	$payment_info = array();
 	$payment_button = array();
	
	// needs update:
	$langs = array(); 
	
	foreach ($langs as $l)
	{
	 $langcfg[$l] = "";
	}

	$exts = $this->getExt();
	jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
	
	/*
	if (!empty($exts))
	foreach($exts as $ext)
	{
	   if (isset($data['opext_'.$ext['name']]))
	   {
	     //if (!JFile::write($ext['path'].DS.'enabled.html')) 
	       if (@JFile::write($ext['path'].DS.'enabled.html', ' ')===false)
	       {
	         $msg .= 'Cannot write to: '.$ext['path'].DS.'<br />';
	       }
	     
	   }
	   else 
	    {
	    if (file_exists($ext['path'].DS.'enabled.html'))
	    {
	      if (JFile::delete($ext['path'].DS.'enabled.html')===false)
	      {
	       $msg .= 'Delete file \'enabled.html\' manually: '.$ext['path'].DS.'enabled.html<br />';
	      }
	    }
	    //else $msg .= 'Cannot find: '.$ext['path'].DS.'enabled.';
	    }
	   
	}
	*/
jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
	foreach ($data as $k=>$d)
	{
	
	/*
	  if (strpos($k, 'payment_contentid_')!==false)
	  {
	    $pid = str_replace('payment_contentid_', '', $k); 
	    $ofolder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'payment'.DS.'onepage';
	    $filename = $ofolder.DS.$pid.'.part.html';
	    if (is_numeric($pid))
	    {
	     $dt = JRequest::getVar('payment_content_'.$pid, '', 'post', 'string', JREQUEST_ALLOWRAW);
	     $dt = str_replace('<p>', '', $dt); 
	     $dt = str_replace('</p>', '<br />', $dt);
	     if (!empty($dt))
	     if (@JFile::write($filename, $dt)===false)
	     {
	      $msg .= 'Cannot save payment content to: '.$filename.'<br />';
	     }
	    }
	  }
	 */
	  // ok we will add a default country for a lang
	  if (strpos($k, 'op_lang_code_')!==false)
	  {
	   $id = str_replace('op_lang_code_', '', $k);
	   if (!empty($data[$k]) && (!empty($data['op_selc_'.$id])))
	   {
	    $cfg .= '
$default_country_array["'.$data[$k].'"] = "'.$data['op_selc_'.$id].'"; 
';
	   }
	  }
	  
	  	  if (strpos($k, 'op_group_')!==false)
	  {
	   $id = str_replace('op_group_', '', $k);
	   if (!empty($data[$k]) && (!empty($data['op_group_'.$id])))
	   {
	   if (!empty($data['op_lang_code2_'.$id]))
	    $cfg .= '
$lang_shopper_group["'.$data['op_lang_code2_'.$id].'"] = "'.$data['op_group_'.$id].'"; 
';
	   }
	  }
	  

	  	  if (strpos($k, 'op_selc2_')!==false)
	  {
	   $id = str_replace('op_selc2_', '', $k);
	   if (isset($data[$k]) && (!empty($data['op_group_ip_'.$id])))
	   {
	   if (isset($data['op_selc2_'.$id]))
	    $cfg .= '
$lang_shopper_group_ip["'.$data['op_selc2_'.$id].'"] = "'.$data['op_group_ip_'.$id].'"; 
';
	   }
	  }

	  
	  
	  if (strpos($k, 'hidepsid_')!==false)
	  {
	    $ida = explode('_', $k, 2);
	    $ida = $ida[1];
	    $id = $data[$k];

	    //$id = $d;
	    if (($id != 'del') && (count($data["hidep_".$ida])>0))
	    {
	    $def = $data["hidepdef_".$ida];
	    $cfg .= ' $hidep["'.$id.'"] = "';

	    if (isset($data["hidep_".$ida]))
	    {
	    foreach ($data["hidep_".$ida] as $h)
	    {
	      $cfg .= $h.'/'.$def.',';

	    }
	    } 
	    else
	    {

	    }
	    $cfg .= '";
';
	    }
	  }
	  
	
	  if (strpos($k, 'ONEPAGE_PAYMENT_EXTRA_INFO')!==false)
	  {
	    $arr = explode('_', $k);
	    $lang = $arr[1];
	    $id = $arr[count($arr)-1];
	    if (!isset($payment_info[$id]))
	    {
	    $payment_info[$id] = $id;
	    $cfg .= '$payment_info["'.$id.'"] = JText::_("COM_ONEPAGE_PAYMENT_EXTRA_INFO_'.$id.'"); 
';
	    }
	  }
	  if (strpos($k, 'ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON')!==false)
	  {
	    $arr = explode('_', $k);
	    $lang = $arr[1];
	    $id = $arr[count($arr)-1];
	    if (!isset($payment_button[$id]))
	    {
	    $payment_button[$id] = $id;
	    $cfg .= '$payment_button["'.$id.'"] = JText::_("COM_ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_'.$id.'"); 
';
	    }
	  }
	  

	  
	  
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
	
	
	$cfg .= '
if (defined(\'OPC_THEME_OVERRIDE\') && (constant(\'OPC_THEME_OVERRIDE\'))) include(OPC_THEME_OVERRIDE); 
else
if (!empty($selected_template) && (file_exists(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php")))
{
  define(\'OPC_THEME_OVERRIDE\', JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php"); 
  include(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php");
 
}
else
if (!defined(\'OPC_THEME_OVERRIDE\'))
define(\'OPC_THEME_OVERRIDE\', false); 


';

		

		
		$conf_file = JPATH_ROOT.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php";
		$ret = true;
		jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		 
		 
		if (@JFile::write($conf_file, $cfg)===false) 
		{
			$msg .= JText::_('COM_ONEPAGE_ACCESS_DENIED_CONFIG').' '.$conf_file.'<br />';
			$ret = false;
			// lets test if it is php valid
		
		}
		else
		{
		
			//unset($disable_onepage);
			
			
			
		    
			if (eval('?>'.file_get_contents($conf_file))===false)
			{
			
			 $msg .= JText::_('COM_ONEPAGE_CONFIG_VALIDATION_ERROR').' <br />';
			 $ret = false;
			 // we have a big problem here, generated file is not valid
			 if (!JFile::copy(JPATH_ROOT.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php", JPATH_ROOT.DS."components".DS."com_onepage".DS."config".DS."onepage.invalid.cfg.php"))
			 {
			 
			 }
			 if (!JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'default'.DS.'onepage.cfg.php', JPATH_ROOT.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php"))
			 {
	    		  $msg .= 'Copying of default onepage.cfg.php was not successfull <br />';
	    		  
			 }
			}

		}
		
	// let's alter VM config here as last step: 
	
	{
    if (VmConfig::get('oncheckout_only_registered', 0))
	{
	  if (VmConfig::get('oncheckout_show_register', 0))
	  define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
	  else 
	  define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
	}
	else
	{
	if (VmConfig::get('oncheckout_show_register', 0))
    define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
	else 
	define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
	}
   }
	$reg_type = $data['opc_registraton_type']; 
	$set = array(); 
	switch ($reg_type)
	{
		case 'NO_REGISTRATION': 
		$set['oncheckout_only_registered'] =  0;
		$set['oncheckout_show_register'] =  0;
		break; 
		case 'OPTIONAL_REGISTRATION': 
		$set['oncheckout_only_registered'] =  0;
		$set['oncheckout_show_register'] =  1;

		break; 
		case 'SILENT_REGISTRATION': 
					$set['oncheckout_only_registered'] =  1;
		$set['oncheckout_show_register'] =  0;
		break; 
		default: 
					$set['oncheckout_only_registered'] =  1;
		$set['oncheckout_show_register'] =  1;

		break; 
		
	}
	
	if (!empty($data['use_ssl']))
	{
	  $set['useSSL'] = 1; 
	}
	else
	 $set['useSSL'] = 0; 
	 
	if (!empty($data['full_tos_unlogged']))
	{
		$set['oncheckout_show_legal_info'] =  1;
	}
	else
		$set['oncheckout_show_legal_info'] =  0;
	
	if (!empty($data['tos_logged']) && (!empty($data['tos_unlogged'])))
	{
		$set['agree_to_tos_onorder'] =  1;
	}
	else
	{
		$set['agree_to_tos_onorder'] =  0;
	}
	
	if (!empty($data['op_disable_shipping']))
	{
			$set['automatic_shipment'] =  0;
	}
	
	
	$this->updateVmConfig($set);
	/*
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	
	$configm = OPCmini::getModel('Config'); 
	
    $c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	
	
	
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	
	
	if ($test != 10)
	 {
	   $isadmin =false; 
	 }
	 else $isadmin = true; 
	 
	 if ((method_exists('VmConfig', 'isAtLeastVersion')) || (!$isadmin))
	 {
	   $msg .= 'Notice: You are running an old version of Virtuemart or you are not logged in as shop Administrator. Some Virtuemart settings cannot be updated with OPC. Please update TOS, registration type, SSL in your virtuemart configuration. (oncheckout_show_register, oncheckout_only_registered, agree_to_tos_onorder, automatic_shipment, oncheckout_show_legal_info, useSSL)  '; 
	   
	   $isadmin = false; 
	 }
	if ($isadmin)
	if (!$configm->store($set))
	{
		//$msg .= 'Error saving virtuemart configuration'; 
	}
	
	VmConfig::loadConfig(true); 
	*/
  
		
if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
		 
		 
		 return $ret;
	}
	
	   private function setPluginEnabled($element, $folder='system', $enabled=false, $type='plugin') 
	    {
		  $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = '".$db->escape($element)."' and type='".$db->escape($type)."' and folder='".$db->escape($folder)."' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  if (empty($isInstalled) && (!$enabled)) return; 
		  
		  
		  if (!empty($isInstalled))
		  {
		    if ($enabled)
			{
		      $q = " UPDATE `#__extensions` SET  enabled =  '1' WHERE  element = '".$db->escape($element)."' and folder = '".$db->escape($folder)."' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
			else
			{
			  $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  element = '".$db->escape($element)."' and folder = '".$db->escape($folder)."' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
		  }
		  
		 
		 
		 //always copy files: 
		 
		     $element = JFile::makeSafe($element); 
			 $mf = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'install'.DS.$element.DS.$element.'.xml'; 
			 
		     if (file_exists($mf))
			  {
			    $xml = @simplexml_load_file($mf); 
				
				//if (empty($xml)) return; 
				$jtype = $xml->getName(); 
				$xml_type = (string)$xml['type']; 
				$xml_group = (string)$xml['group']; 
				if ($jtype == 'extension')
				{
				  $xml_name = (string)$xml->name; 
				  if (!empty($xml->files))
				   {
				      foreach ($xml->files->children() as $k=>$file)
					  {
					 
					  foreach ($file->attributes() as $aname=>$e)
					   {
					   
					      if ($aname == $xml_type)
						  $xml_element = (string)$e; 
					   }
					  }
				   }
				}
				$xml_description = (string)$xml->description; 
				$xml_version = (string)$xml->version; 
				$xml_creationDate = (string)$xml->creationDate; 
				
				
				if ($xml_type === 'plugin')
				 {
				    $xml_element = JFile::makeSafe($xml_element); 
					$xml_group = JFile::makeSafe($xml_group); 
					$xml_element = JFile::makeSafe($xml_element); 
					
					$dest = JPATH_SITE.DS.'plugins'.DS.$xml_group.DS.$xml_element; 
					
				    if (!file_exists($dest))
					 if (@JFolder::create($dest)===false) return;
					 
					$src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'install'.DS.$xml_element; 
					$files = @scandir($src); 
					foreach ($files as $k=>$f)
					 {
					   if (($f == '.') || (($f == '..'))) 
					   {
					   unset($files[$k]); 
					   continue; 
					   }
					   if (is_dir($src.DS.$f))
					   JFolder::copy($src.DS.$f, $dest.DS.$f); 
					   else
					   JFile::copy($src.DS.$f, $dest.DS.$f); 
					 }
					
				 }
				
			  }
			  
			  
		  if (empty($isInstalled) && ($enabled))
		  {
		     if (!empty($xml_element) 
			 && (!empty($xml_version))
			 && (!empty($xml_name))
			 && (!empty($xml_description)))
			 {
		  
		     $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, '".$db->escape($xml_name)."', '".$db->escape($xml_type)."', '".$db->escape($xml_element)."', '".$db->escape($xml_group)."', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"".$db->escape($xml_name)."\",\"type\":\"".$db->getEscape($xml_type)."\",\"creationDate\":\"".$db->escape($xml_creationDate)."\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"".$db->escape($xml_version)."\",\"description\":\"".$db->escape($description)."\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		      
			  
			  $db->setQuery($q); 
		      $db->query(); 
			  $e = $db->getErrorMsg(); 
			    if (!empty($e)) { echo $e; die(); }
			  }
		  }
		  
		}
	
		function enableOpcRegistration($enabled=false)
		{
		   $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opcregistration' and type='plugin' and folder='system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled) && (!$enabled)) return; 
		  if (!empty($isInstalled))
		  {
		    if ($enabled)
			{
		      $q = " UPDATE `#__extensions` SET  enabled =  '1' WHERE  element = 'opcregistration' and folder = 'system' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
			else
			{
			  $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  element = 'opcregistration' and folder = 'system' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
		  }
		  if (empty($isInstalled) && ($enabled))
		  {
		     $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, 'plg_system_opcregistration', 'plugin', 'opcregistration', 'system', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opcregistration\",\"type\":\"plugin\",\"creationDate\":\"December 2013\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"2.0.0\",\"description\":\"One Page Registration helper\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		      $db->setQuery($q); 
		      $db->query(); 
		  }
		  
		  
		}
	
	
		function updateVmConfig($arr)
		{
		   $db = JFactory::getDBO(); 
		   /*
		   if(!class_exists('VirtueMartModelConfig')) require(JPATH_VM_ADMINISTRATOR .'/models/config.php');
		   $configTable  = VirtueMartModelConfig::checkConfigTableExists();
		   */
		   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
		   $configTable = OPCmini::tableExists('virtuemart_configs'); 
		   if (!empty($configTable))
		    {
			   $q = ' SELECT `config` FROM `#__virtuemart_configs` WHERE `virtuemart_config_id` = "1" limit 0,1';
			   $db->setQuery($q);
			   $res = $db->loadResult(); 
			   
			   $new = array(); 
			   
			   $config = explode('|', $res);
			   foreach($config as $item)
			   {
			   
			      $citem = explode('=', $item); 
			      $key = $citem[0]; 
				  $val = $citem[1]; 
				  $new[$key] = $val; 
				
				  
			   }
			   
			   
			   foreach ($arr as $key=>$val)
			    {
				  $new[$key] = serialize($val); 
				}
			   
			}
			
			$string = ''; 
			foreach ($new as $key => $val)
			{
			  if (!empty($string)) $string .= '|'; 
			  $string .= $key.'='.$val; 
			}
			//echo $string; die(); 
	       $q = "update #__virtuemart_configs set config = '".$db->escape($string)."' where virtuemart_config_id = 1";
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
		   
		}
		function getOPCExtensions()
		{
		   if (!function_exists('simplexml_load_file')) return array(); 
		   
		   jimport( 'joomla.filesystem.folder' );
		   $exts = array(); 
		   if (!method_exists('JFolder', 'folders')) return array(); 
		   $folders = JFolder::folders(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'install', '.', false, true); 

		   if (!empty($folders))
		   foreach ($folders as $fo)
		     {
			    $files = JFolder::files($fo, '.xml', false, true); 
				
				if (!empty($files))
				$exts[] = reset($files); 
			 }
			 $xt = array(); 
			 $db = JFactory::getDBO(); 
			 foreach ($exts as $file)
			 {
			  $xml=simplexml_load_file($file);
			  $attribs = new stdClass(); 
			  foreach($xml->attributes() as $key=>$at)
			   {
			     $attribs->$key = (string)$at; 
			   }
			   
			   $files = new stdClass(); 
			   if (isset($xml->files))
			   if (isset($xml->files->filename))
			   foreach ($xml->files->filename as $fn)
			   foreach($fn->attributes() as $key=>$at)
			   {
			     $files->$key = (string)$at; 
				 $element = (string)$at; 
			   }
			    
			   
			  
			  if (isset($attribs->group))
			  if (isset($xml->name))
			   {
			     $name = (string)$xml->name; 
				 $group = $attribs->group; 
				 $type = $attribs->type; 
			     $xt2['name'] = JText::_($name); 
				 $xt2['description'] = JText::_((string)$xml->description); 
				 
				 
				 $q = "select * from #__extensions where name='".$db->getEscaped($name)."' and folder='".$db->getEscaped($group)."' and type='".$db->getEscaped($type)."' "; 
				 if (!empty($element))
				  {
				    $q .= " and element = '".$db->getEscaped($element)."'"; 
				  }
				  $q .= " limit 0,1"; 
				  $db->setQuery($q); 
				  $res = $db->loadAssoc(); 
				  
				  $xt2['data'] = $res; 
				  $xt2['link'] = ''; 
				  if (!empty($res))
				  {
				    if ($type == 'plugin')
				    $xt2['link'] = 'index.php?option=com_plugins&view=plugin&layout=edit&extension_id='.$res['extension_id']; 
					if ($type == 'module')
					$xt2['link'] = 'index.php?option=com_modules&view=module&layout=edit&id='.$res['extension_id']; 
					
				  }
				  
				  $xt[] = $xt2;  
			   }
			 }
			 return $xt; 
			 
		}
		
		
		function copylang()
		{
		   $err = $this->getlangerr(); 
		   $dbj = JFactory::getDBO(); 
		   $prefix = $dbj->getPrefix(); 
		   // CREATE TABLE recipes_new LIKE production.recipes; INSERT recipes_new SELECT * FROM production.recipes;
		   foreach ($err as $table)
		    {
			  $table = $prefix.$table;
			  $orig = str_replace(VMLANG, 'en_gb', $table); 
			  
			  
			  if ($this->tableExists($orig))
			  {
			    
			   $q = 'create table '.$table.' like '.$orig; 
			   $dbj->setQuery($q); 
			   $dbj->query(); 
			   $err = $dbj->getErrorMsg(); if (!empty($err)) { echo $err; die('err config.php'); }
			   // INSERT INTO recipes_new SELECT * FROM production.recipes;
			   $q = 'insert into '.$table.' select * from '.$orig; 
			   $dbj->setQuery($q); 
			   $dbj->query(); 
			   $err = $dbj->getErrorMsg(); if (!empty($err)) { echo $err; die('err config.php'); }
			   
			  }
			}
			
		}
		
		function getlangerr()
		{
		  $this->loadVmConfig(); 
		  
		  if (!defined('VMLANG')) return array(); 
		  $le = array(); 
		  if (!$this->tableExists('virtuemart_paymentmethods_'.VMLANG))
		   {
		    $le[] = 'virtuemart_paymentmethods_'.VMLANG;
		   }
		  		  if (!$this->tableExists('virtuemart_categories_'.VMLANG))
		   {
		    $le[] = 'virtuemart_categories_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_manufacturercategories_'.VMLANG))
		   {
		    $le[] = 'virtuemart_manufacturercategories_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_manufacturers_'.VMLANG))
		   {
		    $le[] = 'virtuemart_manufacturers_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_products_'.VMLANG))
		   {
		    $le[] = 'virtuemart_products_'.VMLANG;
		   }
		  
		  if (!$this->tableExists('virtuemart_shipmentmethods_'.VMLANG))
		   {
		    $le[] = 'virtuemart_shipmentmethods_'.VMLANG;
		   }
		  
		  if (!$this->tableExists('virtuemart_vendors_'.VMLANG))
		   {
		    $le[] = 'virtuemart_vendors_'.VMLANG;
		   }
		  
		 return $le; 
		   
		}

		function getPaymentMethods()
		{
		$this->loadVmConfig(); 
		$onlyPublished = true; 
		
		if (!defined('VMLANG')) define('VMLANG', 'en_gb');
		
			$where = array();
		if ($onlyPublished) {
			$where[] = ' `#__virtuemart_paymentmethods`.`published` = 1';
		}

		$whereString = '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		if ($this->tableExists('virtuemart_paymentmethods_'.VMLANG))
		$table = 'virtuemart_paymentmethods_'.VMLANG;
		else
		if ($this->tableExists('virtuemart_paymentmethods_en_gb'))
		$table = 'virtuemart_paymentmethods_en_gb';
		else
		$table = ''; 
		
		if (!empty($table))
		{
		$select = ' * FROM `#__'.$table.'` as l ';
		$joinedTables = ' JOIN `#__virtuemart_paymentmethods`   USING (`virtuemart_paymentmethod_id`) ';
		$joinedTables .= $whereString ;
		$q = 'SELECT '.$select.$joinedTables;
		
		}
		else
		$q = 'select * from #__virtuemart_paymentmethods where published = 1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$err = $db->getErrorMsg(); 
		
		
		
		foreach ($res as $k=>$p)
		 {
		   $res[$k]['payment_method_id'] = $p['virtuemart_paymentmethod_id']; 
		   $res[$k]['payment_method_name'] = $p['payment_name']; 
		 }
		
		return $res; 
		
		

		}
		
		function getSC()
		{
		
		
		
	     $db = JFactory::getDBO();
		 $q = 'select * from #__virtuemart_countries where published = 1'; 
		
		 $db->setQuery($q);
		 $res = $db->loadAssocList();
		 
		 return $res;
		
		}
		
		function getShippingCountries()
		{
		return $this->getSC();
		
		
		}

	function install_ps_checkout()
	{
  		return true;
	}

	function cleanupdb()
    {
    
     return true;
    
    }
    
    function restorebasket()
    {
   
     return true;
    }

	function install_ps_order()
	{
      return true;
	}
	function install($firstRun = false)
	{

	   return true;
	  
	}
	function getShippingRates()
	{
	  return array(); 
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
		   $value = $db->getEscaped($value);
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
	
	function getDefaultC()
	{
		
	 $dbj = JFactory::getDBO(); 
	   // array of avaiable country codes
	   if (!OPCJ3)
	   {
	   $q = "select virtuemart_country_id from #__virtuemart_userinfos as u, #__virtuemart_vmusers as v where v.virtuemart_vendor_id = '1' and v.user_is_vendor = 1 and v.perms = 'admin' limit 0,1";  
	   }
	   else
	   {
	   $q = "select virtuemart_country_id from #__virtuemart_userinfos as u, #__virtuemart_vmusers as v where v.virtuemart_vendor_id = '1' and v.user_is_vendor = 1 limit 0,1 ";  
	   }
	  $dbj->setQuery($q); 
	  $vendorcountry = $dbj->loadResult(); 

	   return $vendorcountry;
	   

		}
		
		function removeCache()
		{
		   $dir = JPATH_SITE.DS.'cache'.DS.'com_onepage';
		   if (file_exists($dir))
		    {
			  $arr = @scandir($dir);
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (($file != 'overrides') && ($file != '.') && ($file != '..')) $ret[] = $file;
		  }
		 }
		 if (!empty($ret))
				foreach ($ret as $file)
				 {
				    @JFile::delete($dir.DS.$file); 
				 }
			}
		}
		
		function getTemplates()
		{
		 $dir = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'themes';;
		 $arr = @scandir($dir);
		 $ret = array();
		 
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (is_dir($dir.DS.$file) && ($file != 'overrides') && ($file != '.') && ($file != '..')) $ret[] = $file;
		  }
		 }
		 return $ret;
		}
		function getClassNames()
		{
		return array(); 
		
    	}
    
    /**
 * strposall
 *
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */
function strposall($haystack,$needle){
   
    $s=0;
    $i=0;
   
    while (is_integer($i)){
       
        $i = strpos($haystack,$needle,$s);
       
        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i+strlen($needle);
        }
    }
    if (isset($aStrPos)) {
        return $aStrPos;
    }
    else {
        return false;
    }
}

function retCss()
	{
		return ""; 	
	}

function retPhp()
	{
		return array(); 
	}

function tableExists($table)
{

 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$dbj->getPrefix().$table."'";
	   $dbj->setQuery($q);
	   $r = $dbj->loadResult();
	   if (!empty($r)) return true;
 return false;

 $db = JFactory::getDBO();
 $q = "SHOW TABLES LIKE '".$db->getPrefix().$db->getEscaped($table)."'";
 $db->setQuery($q);
 $r = $db->loadResult();
 if (!empty($r))
 return true;
 return false;
}
function createTempOrderTables()
{
 $db = JFactory::getDBO();
 if (!$this->tableExists('vm_orders_opctemp'))
 {
   $q = 'CREATE TABLE '.$db->getPrefix().'vm_orders_opctemp LIKE '.$db->getPrefix().'vm_orders';
   $db->setQuery($q);
   $db->query();
   $q = '';  
 }
 
}

// gets list of order statuses 
function getOrderStatuses()
{
  $db = JFactory::getDBO();
  $q = 'select * from #__virtuemart_orderstates where 1 limit 999';
  $db->setQuery($q);
  $res = $db->loadAssocList();
  if (empty($res)) return array();
  return $res; 
}

// get joomfish languages
function getJLanguages()
{
		$db = JFactory::getDBO();
	   $q = "SHOW TABLES LIKE '".$db->getPrefix()."languages'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (!empty($r))
	   {
	    if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$q = "select lang_code from #__languages where 1 limit 999";
		else
	    $q = "select code from #__languages where 1 limit 999";
	    $db->setQuery($q);
	    $codes = $db->loadAssocList(); 
	   }
	   else $codes = array();
	   
	    if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		foreach ($codes as $k=>$v)
		 {
		   $codes[$k]['code'] = $codes[$k]['lang_code'];
		 }
	   
	   return $codes;
}
function getPhpTrackingThemes()
{ 
  $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'; 
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
	$path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.$file.'.xml'; 
	if (!file_exists($path)) continue; 
	$arr[] = $file; 
	
    
  }
  return $arr; 
  
}
/**
* Compiles a list of installed languages
*/
function getLanguages()
{
	global $mainframe, $option;

	// Initialize some variables
	$db		= JFactory::getDBO();
	$client	= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

	$rowid = 0;

	// Set FTP credentials, if given
	jimport('joomla.client.helper');
	$ftp = JClientHelper::setCredentialsFromRequest('ftp');

	//load folder filesystem class
	jimport('joomla.filesystem.folder');
	$path = JLanguage::getLanguagePath($client->path);
	$dirs = JFolder::folders( $path );

 

	foreach ($dirs as $dir)
	{
		$files = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
		foreach ($files as $file)
		{
		    $file = str_replace('/', DS, $file); 
			$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$file);

			$row 			= new StdClass();
			$row->id 		= $rowid;
			$row->language 	= substr($file,0,-4);
 
			if (!is_array($data)) {
				continue;
			}
			foreach($data as $key => $value) {
				$row->$key = $value;
			}

			// if current than set published
			$params = JComponentHelper::getParams('com_languages');
			if ( $params->get($client->name, 'en-GB') == $row->language) {
				$row->published	= 1;
			} else {
				$row->published = 0;
			}

			$row->checked_out = 0;
			$row->mosname = JString::strtolower( str_replace( " ", "_", $row->name ) );
			$pos = strpos($row->mosname, '(');
			$sh = trim(substr($row->name, 0, $pos));
      $row->short = $sh;
			$rows[] = $row;
			$rowid++;
		}
	}
	return $rows; 
}

function getErrMsgs()
{
 $msg = ''; 
   $conf = JPATH_SITE.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php";
   if ((file_exists($conf) && (!is_writable($conf))))
   $msg = 'File is not writable: '.$conf."<br />";
   
   $db = JFactory::getDBO(); 
   
   // check if there is ANY vendor within the shop
   $q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1 and virtuemart_vendor_id <> 0'; 
   $db->setQuery($q); 
   $r = $db->loadAssocList(); 
   $novendor = false; 
   if (empty($r))
    {
	  $msg .= JText::_('COM_ONEPAGER_VENDOR_ERROR').' <br />'; 
	  $novendor = true; 
	}
	
	// more users marked as vendors are sharing the same vendor ID
	$arr = array(); 
	if (count($r)>1)
	{
	
	    foreach ($r as $vendor)
		 {
		   if (empty($arr[$vendor['virtuemart_vendor_id']])) $arr[$vendor['virtuemart_vendor_id']] = array(); 
		   $arr[$vendor['virtuemart_vendor_id']][] = $vendor['virtuemart_user_id']; 
		 }
	
		 foreach ($arr as $v_id => $users)
		  {
		     $count = count($users); 
			 $names = array(); 
		     if ($count > 1)
			   {
			      $msg .= 'PROBLEM: More than one user shares the same Vendor ID ('.$v_id.') which will lead to various problems<br />'; 
				  foreach ($users as $user_id)
				  {
				   
				    $q = 'select * from #__users where id = '.(int)$user_id.' limit 0,1'; 
				    $db->setQuery($q); 
				    $res = $db->loadAssoc(); 
					
				    if (empty($res))
					  {
					     $msg .= 'FIXED: User ID ('.$user_id.') in #__virtuemart_vmusers does not exists in #__users! OPC deactivates this vendor to fix further problems.<br />'; 
						 $q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "0", `virtuemart_vendor_id` = "0" where `virtuemart_user_id` = "'.(int)$user_id.'" and `user_is_vendor` = "1" and `virtuemart_vendor_id` = "'.$v_id.'" limit 1'; 
						 $db->setQuery($q); 
						 $db->query(); 
						 $msg .= $db->getErrorMsg(); 
						 $count--; 
					  }
					  else
					  $names[] = $res['username']; 
				  }
			   }
			  if ($count === 0)
			   {
			      $msg .= 'None of the vendors had a record in #__users and thus they all were deactivated. Deactivated users in #__virtuemart_users are: '.implode(', ', $users).'<br />'; 
			   }
			  if ($count > 1)
			   {
			      $msg .= 'MANUAL ACTION REQUIRED: There are still two vendors sharing the same virtuemart_vendor_id, please make sure that only one has virtuemart_vendor_id = 1 and user_is_vendor = 1 in your #__virtuemart_vmusers. List of original user_id\'s: '.implode(', ', $users).' with usernames ('.implode(', ', $names).') Having two or more vendors sharing the same Vendor ID will lead to unpredicted email or other issues. This also may be fixed by removing one of the users with Virtuemart user management.<br />'; 
			   }
			   
		  }
		 
	}
	
	// note - user is marked as vendor, but has zero vendor id
	// this can lead either to make him a real vendor
	// OR to unmark him as a vendor
	$q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1 and virtuemart_vendor_id = 0'; 
	$db->setQuery($q); 
	$res = $db->loadAssoc(); 
	if (!empty($res))
	 {
	   $q = 'select * from #__users where id = '.(int)$res['virtuemart_user_id'].' limit 0,1'; 
	   $db->setQuery($q); 
	   $juser = $db->loadAssoc(); 
	   
	    if (($novendor === false) || (empty($juser)))
		{
	     $q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "0", `virtuemart_vendor_id` = "0" where `virtuemart_user_id` = "'.(int)$res['virtuemart_user_id'].'" and `user_is_vendor` = "1" and virtuemart_vendor_id = 0'; 
		 $msg .= 'FIXED: A user ('.$res['virtuemart_user_id'].') was marked as a vendor, but had no Vendor ID associated. He was unmarked as vendor by OPC.'; 
		}
		else
		if ($novendor)
		{
		
		$q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "1", `virtuemart_vendor_id` = "1" where `virtuemart_user_id` = "'.(int)$res['virtuemart_user_id'].'" and `user_is_vendor` = "1" and virtuemart_vendor_id = 0'; 
		$msg .= 'FIXED: A user ('.$res['virtuemart_user_id'].') was marked as a vendor, but had no Vendor ID associated. Because OPC detected you had no valid vendors in your shop, this users was marked as your vendor. Please check your Virtuemart vendor settings closely.'; 
		}
		$db->setQuery($q); 
		$db->query(); 
		$msg .= $db->getErrorMsg(); 
		
	 }
	 // note: user is marked as vendor, but has no record in #__users - joomla
	 $q = 'select * from #__virtuemart_vmusers where user_is_vendor = "1"'; 
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 if (!empty($res))
	 foreach ($res as $user)
	  {
	     $q = 'select * from #__users where id = '.(int)$user['virtuemart_user_id'].' limit 0,1'; 
		 $db->setQuery($q); 
		 $juser = $db->loadAssoc(); 
		 if (empty($juser))
		  {
		     $msg .= 'Problem: A user ID ('.$user['virtuemart_user_id'].') in your #__virtuemart_vmusers is marked as vendor, but does not exists in #__users <br />'; 
			 $q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "0" where `virtuemart_user_id` = "'.(int)$res['virtuemart_user_id'].'" and `user_is_vendor` = "1" '; 
			 $db->setQuery($q); 
			 $db->query(); 
			 $msg .= $db->getErrorMsg(); 
			 $msg .= 'FIXED: A user ID ('.$user['virtuemart_user_id'].') in your #__virtuemart_vmusers was unmarked as vendor because he is not registered in #__users<br />'; 
		  }
		 
	  }
	 
	 
	
	/*
   $db = JFactory::getDBO(); 
   $q = 'select * from #__virtuemart_vmusers where user_is_vendor = 0 and virtuemart_vendor_id <> 0'; 
   $db->setQuery($q); 
   $r = $db->loadAssocList(); 
    */   

	
   
		if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }

}

/* this function is from Virtuemart SVN for editing language files
*/

function getDecodeFunc($langCharset) {
	$func = 'strval';
	// get global charset setting
	$iso = explode( '=', @constant('_ISO') );
	// If $iso[1] is NOT empty, it is Mambo or Joomla! 1.0.x - otherwise Joomla! >= 1.5
	$charset = !empty( $iso[1] ) ? $iso[1] : 'utf-8';
	// Prepare the convert function if necessary
	if( strtolower($charset)=='utf-8' && stristr($langCharset, 'iso-8859-1' ) ) {
		$func = 'utf8_decode';
	} elseif( stristr($charset, 'iso-8859-1') && strtolower($langCharset)=='utf-8' ) {
		$func = 'utf8_encode';
	}
	if( !function_exists( $func )) {
		$func = 'strval';
	}
	return $func;
}



function template_update_upload()
{
 return false; 
 jimport('joomla.filesystem.file');
 $file = "";
 $msg = '';
 foreach ($_FILES as $k=>$v)
 {
 // $msg .= 'key: '.$k.'<br />';
 // $msg .= 'val: '.$v.'<br />';
  if ((strpos($k, 'uploadedupdatefile_')!==false) && (!empty($_FILES[$k]['name'])))
  $file = $k;
 }
 
 $arr = explode('_', $file);
 if (count($arr)>1)
 {
 $tid = $arr[1];
 if (!is_numeric($tid)) return "Error!";
 // get previous file
 $ehelper = new OnepageTemplateHelper();
 $tt = $ehelper->getTemplate($tid);
 $target_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS;
 if (file_exists($target_path.$tt['file']))
 {
  if (!JFile::delete($target_path.$tt['file']))
   $msg .= 'Could not remove old template file: '.$tt['file'];
 }
 $newname = JFile::makesafe(basename( $_FILES['uploadedupdatefile_'.$tid]['name']));
 $msg .= $ehelper->updateFileName($tid, $newname);
 //$userfile = JRequest::getVar('uploadedupdatefile_'.$tid, null, 'files');
 //var_dump($userfile); die();
 $target_path = $target_path . $newname; 

 if(JFile::upload($_FILES[$file]['tmp_name'], $target_path)) {
    $msg .=  "The template file ".  $newname. 
    " has been uploaded";
	} else{
    $msg .= "There was an error uploading the file, please try again! file: ".$newname;
	}
 }
 else $msg .= "There was an error uploading the file, please try again! ";
 
return $msg;
 






}



function template_upload()
{
 return false; 
 $target_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS;
 
 $userfile = JRequest::getVar('uploadedfile', null, 'files');
 
 jimport('joomla.filesystem.file'); 
 $file = JRequest::getVar('uploadedfile', null, 'files', 'array'); 
 $filename = JFile::makeSafe($file['name']); 
 $src = $file['tmp_name']; 
 
 // $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
 $target_path .= $filename; 
 
 if (JFile::upload($src, $target_path))
 {
    $msg =  "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
    
    
} else{
    $msg = "There was an error uploading the file, please try again!";
}

return $msg;
 
}

function check_syntax($file)
{
// load file
$code = file_get_contents($file);

$bom = pack("CCC", 0xef, 0xbb, 0xbf);
				if (0 == strncmp($code, $bom, 3)) {
					//echo "BOM detected - file is UTF-8\n";
					$code = substr($code, 3);
				}

// remove non php blocks
$x = 0; 
ob_start(); 
$f = @eval('$x = 1;'."?>$code"); 
$y = ob_get_clean(); 
return $x; 

}


function getLangVars()
{


  return array(); 
}
	
	function removepatchusps()
	{
		$msg = ''; 
	$file = JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'alatak_usps'.DS.'alatak_usps.php'; 

		$file2 = str_replace('.php', '_opc_backup.php', $file); 			
if (@JFile::copy($file2, $file)===false)
{
$msg = 'Could not copy '.$file2.' to '.$file.'<br />';
	
}
else 
{
	
if (@JFile::delete($file2)===false)
{
$msg = 'Could not remove backup file'.$file2.'<br />';	
}

return $msg.'Patch was removed'; 
}
if (@JFile::delete($file)===false)
{
$msg = 'Could not remove '.$file.'<br />';	
}
if (@JFile::move($file2, $file)===false)
{
$msg = 'Could not move '.$file2.' to '.$file.'<br />';
	
}
else return 'Patch was removed'; 

if (!empty($msg))
{
  $msg .= 'Please restore the original file '.$file.' from '.$file2;	
  
}
return $msg; 



	}		
	function patchusps()
	{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		
		$file = JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'alatak_usps'.DS.'alatak_usps.php'; 
		if (file_exists($file))
		{
$file2 = str_replace('.php', '_opc_backup.php', $file); 			
if (@JFile::copy($file, $file2)===false)
{
$msg = 'Could not copy '.$file.' to '.$file2. ' patch wasn\'t applied';
return $msg; 	
}
		  $data = file_get_contents($file); 	
		  $data = str_replace("\r\r\n", "\r\n", $data); 
		  $data = str_replace('function _sendRequest', "\r\n\tstatic ".'$uspsCache;'." \r\n\tfunction _sendRequest", $data); 
		  $x1 = strpos($data, 'function _sendRequest'); 
		  $x2 = strpos($data, '{', $x1); 
		  $x3 = strpos($data, 'return true;', $x2); 
		  $data2 = substr($data, 0, $x2+1)."\r\n".'
	if (!empty(plgVmShipmentAlatak_USPS::$uspsCache))
		if (isset(plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost]))
		{
			if (isset(plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'method\']))
			if (plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'method\'] == $method)
			{
				$xmlResult = plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'result\']; 
				return  true; 
			}
		}			  
		'.substr($data, $x2+1, $x3-($x2+1))."\r\n".'
		if (empty(plgVmShipmentAlatak_USPS::$uspsCache)) plgVmShipmentAlatak_USPS::$uspsCache = array(); 
		if (empty(plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost])) plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost] = array(); 
		plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'method\'] = $method;
		plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'result\'] = $xmlResult; 		
		
		
		'.substr($data, $x3); 
		  
		if (@JFile::write($file, $data2)===false)
		{
			$msg = 'Could not write to '.$file;
			return $msg; 
		}
		else
		{
			$msg = 'Patch applied in '.$file;
			return $msg; 
			
		}
		}
		
	}
	
	/**
	 * Joomla modified function from installer.php file of /libraries/joomla/installer.php
	 *
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @access	public
	 * @param	string  $file 	 The SQL file
	 * @param	string	$version	The database connector to use
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	function parseSQLFile($file)
	{
		// Initialize variables
		$queries = array();
		$db =  JFactory::getDBO();
		$dbDriver = strtolower($db->get('name'));
		if ($dbDriver == 'mysqli') {
			$dbDriver = 'mysql';
		}
		$dbCharset = ($db->hasUTF()) ? 'utf8' : '';

		if (!file_exists($file)) return 0;

		// Get the array of file nodes to process

		// Get the name of the sql file to process
		$sqlfile = '';
			// we will set a default charset of file to utf8 and mysql driver
			$fCharset = 'utf8'; //(strtolower($file->attributes('charset')) == 'utf8') ? 'utf8' : '';
			$fDriver  = 'mysql'; // strtolower($file->attributes('driver'));

			if( $fCharset == $dbCharset && $fDriver == $dbDriver) {
				$sqlfile = $file;
				// Check that sql files exists before reading. Otherwise raise error for rollback

				$buffer = file_get_contents($file);

				// Graceful exit and rollback if read not successful
				if ( $buffer === false ) {
					return false;
				}

				// Create an array of queries from the sql file
				jimport('joomla.installer.helper');
				$queries = JInstallerHelper::splitSql($buffer);

				if (count($queries) == 0) {
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
							return false;
						}
					}
				}
			}
		

		return (int) count($queries);
	}


		
	}

