<?php
/**
 * OPC script file
 *
 * This file is executed during install/upgrade and uninstall
 *
 * @author stAn, RuposTel s.r.o.
 * @package One Page Checkout
 *
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
*/
defined('_JEXEC') or die('Restricted access');


jimport( 'joomla.application.component.model');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// hack to prevent defining these twice in 1.6 installation
if (!defined('_OPC_SCRIPT_INCLUDED')) {
	define('_OPC_SCRIPT_INCLUDED', true);


	/**
	 * OPC custom installer class
	 */
	class com_onepageInstallerScript {

	  public function preflight()
		{
			
			jimport('joomla.installer.installer');
			$installer =  JInstaller::getInstance();
				if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'))
		 {
			
		  
			
		
		  $installer->set('message', 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>');
		   $installer->abort('Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>'); 
		   //echo 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>'; 
		   return false; 
		 }
		 
		 // check file permissions: 
		 jimport('joomla.filesystem.folder');
		 jimport('joomla.filesystem.file');
		 
		 $tmp_path = JFactory::getConfig()->get('tmp_path'); 
		if (!JFolder::exists($tmp_path))
		{
			$installer->abort('Your tmp_path ('.$tmp_path.')in your configuration.php points to non existing directory. Please fix your joomla global configuration.');
 			return false; 
		}
		$data = ' '; 
		if (!JFile::write($tmp_path.DS.'test.html', $data))
		{
			$installer->abort('Your tmp_path ('.$tmp_path.')in your configuration.php is not writable! ');
 			return false; 
		}
		@JFile::delete($tmp_path.DS.'test.html');

		
		 $errors = ''; 
		 $rand = rand(1000, 10000); 
		 $rand = $rand.'.html'; 
		 $buffer = 'OPC installation tests'; 
		 // check plugin directory
		 if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opc/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'.DS.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opc/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'.DS.$rand); 
			}
		 }
		 
		 // let's install opctracking plugin: 
		 if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opctracking/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opctracking/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.$rand); 
			}
		 }
		 
		 
		 // let's install opctracking plugin: 
		 if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opccart/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opccart/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.$rand); 
			}
		 }
		 
		  // let's install opcregistration plugin: 
		 if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opcregistration/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opcregistration/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.$rand); 
			}
		 }
		 
		 
		 // check FE component directory
		 if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'components'.DS.'com_onepage')===false)
		  {
		    $errors .= 'Cannot create OPC frontend directory in /components/com_onepage/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DS.'components'.DS.'com_onepage');
		  }
		  }
		 		 else
		 {
			if (@JFile::write(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC frontend directory in /components/com_onepage/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.$rand); 
			}
		 }
		// check BE component directory
		 if (!file_exists(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_onepage'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_onepage')===false)
		  {
		    $errors .= 'Cannot create OPC backend directory in /administrator/components/com_onepage/<br />'; 
		  }
		  }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC backend directory in /administrator/components/com_onepage/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.$rand); 
			}
		 }

		
		 if (!file_exists(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml'))
		 {
		 if (@JFolder::create(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml')===false)
		  {
		    $errors .= 'Cannot create OPC ajax document helper directory in /libraries/joomla/document/opchtml/<br />'; 
		  }
		  else
		  {
			@JFolder::delete(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml');  			  
		  }
		 }
		  
		  if (!empty($errors))
		  {
			  $installer->abort('<div style="margin-top: 20px; margin-bottom: 20px; color: white;">'.$errors.'Please ignore other messages printed here by Joomla. Please update your permissions and try again.</div>'); 
			  
			  return false; 
		  }
		  
		 
			return true; 
		}

		/**
		 * Install script
		 * Triggers after database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function install () {

		jimport('joomla.installer.installer');
		$installer =  JInstaller::getInstance();
		
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'))
		 {
		   echo 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>'; 
		   $installer->set('message', 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>');
		   return false; 
		 }

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$path = JPATH_SITE.DS.'components'.DS.'com_onepage';


		$source 	= $installer->getPath('source');
		// installs the themes
		if (substr($source, strlen($source)) != DS) $source .= DS;

		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opc/<br />'; 
		  }
		
		if (@JArchive::extract($source.'opcsystem.zip',JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'.DS)===false)
		{
		  echo 'Cannot extract OPC system plugin to /plugins/system/opc<br />'; 
		}
		
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'))
		 {
		  if (@JFolder::create(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config')===false)
		   echo 'Cannot create config directory in /components/com_onepage/config<br />'; 
		 }
		
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		 {
		   JFile::copy($source.'admin'.DS.'default'.DS.'onepage.cfg.php', JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		 }

		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/vmpayment/opctracking/<br />'; 
		  }
		 
		JFile::copy($source.'admin'.DS.'install'.DS.'opctracking'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'index.html'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opctracking'.DS.'opctracking.php', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'opctracking.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opctracking'.DS.'opctracking.xml', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'opctracking.xml'); 

		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opccart/<br />'; 
		  }
		
		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opcregistration/<br />'; 
		  }
		
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'index.html'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'opccart.php', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'opccart.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'carthelper.php', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'opccart.xml', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'opccart.xml'); 

		
		JFile::copy($source.'admin'.DS.'install'.DS.'opcregistration'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.'index.html'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opcregistration'.DS.'opcregistration.php', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.'opcregistration.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opcregistration'.DS.'opcregistration.xml', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.'opcregistration.xml'); 

		
		$db = JFactory::getDBO(); 
		$q = "select * from #__extensions where name = 'plg_system_onepage' and element = 'onepage' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  element = 'plg_system_onepage' and folder = 'system' "; 
			$db->setQuery($q); 
			$db->query(); 
			//echo 'Disabled Linelab One Page Checkout extension in Plugin Manager <br />'; 
		 }
		 

		 
		// we renamed the plugin so we don't have cross compatiblity issues with linelab opc 
		$db = JFactory::getDBO(); 
		$q = "select * from #__extensions where name = 'plg_system_onepage' and element = 'opc' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = "delete from `#__extensions` WHERE  element = 'plg_system_onepage' and element = 'opc' "; 
			$db->setQuery($q); 
			$db->query(); 
			echo 'Renamed OPC plugin from plg_system_onepage to plg_system_opc <br />'; 
		 }

		 
		$db = JFactory::getDBO(); 
		$q = "select * from #__extensions where name = 'plg_system_opc' and folder = 'system' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		if (empty($res))
		{
		$q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
		$q .= " (NULL, 'plg_system_opc', 'plugin', 'opc', 'system', 0, 0, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opc\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		$db->setQuery($q); 
		$db->query(); 
		}
		else
		{
		 if (count($res)>1) echo 'More then one instance of onepage system plugin found! Please delete one of them manually.'; 
		}

		return true;
		}


		/**
		 * Update script
		 * Triggers after database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function update () {
			jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS;

		jimport('joomla.installer.installer');
		$installer = JInstaller::getInstance();

		$source 	= $installer->getPath('source');
		// installs the themes
		if (substr($source, strlen($source)) != DS) $source .= DS;
		 
		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'))
		 JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'); 
		
		if (JArchive::extract($source.'opcsystem.zip',JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'.DS)===false)
		{
		  echo 'Cannot extract OPC system plugin to /plugins/system/opc <br />'; 
		}

		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opctracking/<br />'; 
		  }
		 
		JFile::copy($source.'admin'.DS.'install'.DS.'opctracking'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'index.html'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opctracking'.DS.'opctracking.php', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'opctracking.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opctracking'.DS.'opctracking.xml', JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'.DS.'opctracking.xml'); 

		if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opccart/<br />'; 
		  }
	
	if (!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'))
		 if (@JFolder::create(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opcregistration/<br />'; 
		  }
		JFile::copy($source.'admin'.DS.'install'.DS.'opcregistration'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.'index.html'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opcregistration'.DS.'opcregistration.php', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.'opcregistration.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opcregistration'.DS.'opcregistration.xml', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'.DS.'opcregistration.xml'); 
		  
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'index.html', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'index.html'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'carthelper.php', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'carthelper.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'opccart.php', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'opccart.php'); 
		JFile::copy($source.'admin'.DS.'install'.DS.'opccart'.DS.'opccart.xml', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'.DS.'opccart.xml'); 


		
		$db = JFactory::getDBO(); 
		$q = "select * from #__extensions where name = 'plg_system_onepage' and element = 'onepage' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  name = 'plg_system_onepage' and folder = 'system' and element = 'onepage' "; 
			$db->setQuery($q); 
			$db->query(); 
			//echo 'Disabled Linelab One Page Checkout extension in Plugin Manager <br />'; 
		 }
		 
		//update from prior opc versions: 
		$db = JFactory::getDBO(); 
		$q = "delete from `#__extensions` WHERE  element = 'opctracking' and folder = 'system' "; 
		$db->setQuery($q); 
		$db->query(); 


		 
		// we renamed the plugin so we don't have cross compatiblity issues with linelab opc 
		$db = JFactory::getDBO(); 
		$q = "select * from #__extensions where name = 'plg_system_onepage' and element = 'opc' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = " delete from `#__extensions` WHERE  name = 'plg_system_onepage' and element = 'opc' "; 
			$db->setQuery($q); 
			$db->query(); 
			echo 'Renamed OPC plugin from plg_system_onepage to plg_system_opc <br />'; 
		 }

		 
	

		
		
		
		$db = JFactory::getDBO(); 
		$q = 'select * from #__extensions where element = "opc" and name="plg_system_opc" limit 999'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (empty($res))
		{
		
		$q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
		$q .= " (NULL, 'plg_system_opc', 'plugin', 'opc', 'system', 0, 0, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opc\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		$db->setQuery($q); 
		$db->query(); 
		}
		
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		 {
		   JFile::copy($source.'admin'.DS.'default'.DS.'onepage.cfg.php', JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		 }
		

			return true; 
		}



		/**
		 * Uninstall script
		 * Triggers before database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function uninstall ($parent=null) {
			jimport('joomla.filesystem.folder');
		    jimport('joomla.filesystem.file');
		    jimport('joomla.filesystem.archive');
			 
			//@JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'vmextended'.DS.'opc'); 
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc'))
			@JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc');
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking'))
			@JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking');
			
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart'))
			@JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opccart');

			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration'))
			@JFolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opcregistration');

			
			if (file_exists(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml'))
			@JFolder::delete(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml'); 
			
			$db = JFactory::getDBO(); 
			$q = "delete from #__extensions where element = 'opc' limit 5"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$db = JFactory::getDBO(); 
			$q = "delete from #__extensions where element = 'opctracking' limit 5"; 
			$db->setQuery($q); 
			$db->query(); 
			/*
			$q = "delete from #__assets where alias = 'com-onepage'"; 
			$db->setQuery($q); 
			$db->query(); 
			*/
			$q = "drop table if exists #__vmtranslator_translations"; 
			$db->setQuery($q); 
			$db->query(); 

			$q = "drop table if exists #__onepage_config"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$q = "drop table if exists #__virtuemart_plg_opctracking"; 
			$db->setQuery($q); 
			$db->query(); 
			
			
			return true;
		}

		/**
		 * Post-process method (e.g. footer HTML, redirect, etc)
		 *
		 * @param string Process type (i.e. install, uninstall, update)
		 * @param object JInstallerComponent parent
		 */
		public function postflight ($type, $parent=null) {
			

			return true;
		}

		

	}

	/**
	 * Legacy j1.5 function to use the 1.6 class install/update
	 *
	 * @return boolean True on success
	 * @deprecated
	 */
	function com_install() {
	 if(version_compare(JVERSION,'1.7.0','ge')) {
	 return true; 
// Joomla! 1.7 code here
} elseif(version_compare(JVERSION,'1.6.0','ge')) {
	 return true; 
// Joomla! 1.6 code here
} elseif(version_compare(JVERSION,'2.5.0','ge')) {
	 return true; 
// Joomla! 2.5 code here
} else {
// Joomla! 1.5 code here

	 ob_start(); 
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS;

		jimport('joomla.installer.installer');
		$installer =  JInstaller::getInstance();
		//var_dump($installer->_manifest->files); 
		$source 	= $installer->getPath('source');
		// installs the themes
		if (substr($source, strlen($source)) != DS) $source .= DS;
		if (@JArchive::extract($source.'opcsystem.zip',JPATH_SITE.DS.'plugins'.DS.'system'.DS)===false)
		{
		  echo 'Cannot extract OPC system plugin'; 
		}
		
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'))
		 {
		  if (@JFolder::create(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config')===false)
		   echo 'Cannot create config directory in /components/com_onepage/config <br />'; 
		 }
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		 {
		   JFile::copy($source.'admin'.DS.'default'.DS.'onepage.cfg.php', JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		 }
		
		
		// we need to fix a bug in VM2.0 and J1.5:
		$search = '$dispatcher->trigger(\'onVmSiteController\', $_controller);';
		$rep = '$dispatcher->trigger(\'onVmSiteController\', array($_controller));';
		$x = file_get_contents(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php'); 
		if (strpos($x, $search)===false)
		 {
		   $x = str_replace($search, $rep, $x); 
		   JFile::copy(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.orig.opc_bck.php'); 
		   JFile::write(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', $x); 
		 }
		
		$db = JFactory::getDBO(); 
		/*
		$q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
		 $q .= " (NULL, 'plg_system_onepage', 'plugin', 'opc', 'vmextended', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_onepage\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0), "; 	
		 $q .= " (NULL, 'plg_system_onepage', 'plugin', 'opc', 'system', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_onepage\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		*/
		$q = ' INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
		(NULL, \'System - RuposTel Onepage\', \'opc\', \'system\', 0, -100, 0, 0, 0, 0, \'0000-00-00 00:00:00\', \'\') ';
		/*
		(NULL, \'Vmextended - Onepage\', \'opc\', \'vmextended\', 0, -100, 1, 0, 0, 0, \'0000-00-00 00:00:00\', \'\') ';
		*/
		$db->setQuery($q); 
		$db->query(); 
	$x = ob_get_clean(); 
	echo $x;
		 return true; 
		}
	}

	/**
	 * Legacy j1.5 function to use the 1.6 class uninstall
	 *
	 * @return boolean True on success
	 * @deprecated
	 */
	function com_uninstall() {
	 if(version_compare(JVERSION,'1.7.0','ge')) {
// Joomla! 1.7 code here
} elseif(version_compare(JVERSION,'1.6.0','ge')) {
// Joomla! 1.6 code here
} else {
// Joomla! 1.5 code here

			jimport('joomla.filesystem.folder');
		    jimport('joomla.filesystem.file');
		    jimport('joomla.filesystem.archive');
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'vmextended'.DS.'opc.xml'))
			JFile::delete(JPATH_SITE.DS.'plugins'.DS.'vmextended'.DS.'opc.xml'); 
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'vmextended'.DS.'opc.php'))
			JFile::delete(JPATH_SITE.DS.'plugins'.DS.'vmextended'.DS.'opc.php');
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc.xml'))
			JFile::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc.xml');
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc.php'))
			JFile::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'opc.php');
			
			
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking.xml'))
			JFile::delete(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking.xml');
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking.php'))
			JFile::delete(JPATH_SITE.DS.'plugins'.DS.'vmpayment'.DS.'opctracking.php');

			
			if (file_exists(JPATH_SITE.DS.'components'.DS.'themes'))
			if (@JFolder::remove(JPATH_SITE.DS.'components'.DS.'themes')===false)
			 echo 'Cannot remove themes directory! Please remove it manually from /components/com_onepage'; 
			
			if (@JFolder::delete(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml')===false)
			 echo 'Cannot remove OPC document type in: '. JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml<br />';
			
			$db = JFactory::getDBO(); 
			$q = "delete from #__plugins where element = 'opc' limit 5"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$db = JFactory::getDBO(); 
			$q = "delete from #__plugins where element = 'opctracking' limit 5"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$q = "drop table if exists #__vmtranslator_translations"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$q = "drop table if exists #__onepage_config"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$q = "drop table if exists #__virtuemart_plg_opctracking"; 
			$db->setQuery($q); 
			$db->query(); 
			
			return true;
}
	}

} // if(defined)


