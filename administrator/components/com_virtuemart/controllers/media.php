<?php
/**
*
* Media controller
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: media.php 8063 2014-06-22 23:45:01Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Product Controller
 *
 * @package    VirtueMart
 * @author Max Milbers
 */
class VirtuemartControllerMedia extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */
	function __construct() {
		VmConfig::loadJLang('com_virtuemart_media');
		parent::__construct('virtuemart_media_id');

	}


	/**
	 * for ajax call media
	 */
	function viewJson() {

		/* Create the view object. */
		$view = $this->getView('media', 'json');

		/* Now display the view. */
		$view->display(null);
	}

	function save($data = 0){

		$fileModel = VmModel::getModel('media');

		//Now we try to determine to which this media should be long to
		$data = vRequest::getRequest();

		//$data['file_title'] = vRequest::getVar('file_title','','post','STRING',JREQUEST_ALLOWHTML);
		$data['file_description'] = vRequest::getHtml('file_description','');

		$data['media_attributes'] = vRequest::getCmd('media_attributes');
		$data['file_type'] = vRequest::getCmd('file_type');
		if(empty($data['file_type'])){
			$data['file_type'] = $data['media_attributes'];
		}

		if ($id = $fileModel->store($data)) {
			$msg = vmText::_('COM_VIRTUEMART_FILE_SAVED_SUCCESS');
		} else {
			$msg = $fileModel->getError();
		}

		$cmd = vRequest::getCmd('task');
		if($cmd == 'apply'){
			$redirection = 'index.php?option=com_virtuemart&view=media&task=edit&virtuemart_media_id='.$id;
		} else {
			$redirection = 'index.php?option=com_virtuemart&view=media';
		}

		$this->setRedirect($redirection, $msg);
	}

	function synchronizeMedia(){

		$user = JFactory::getUser();
		if($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart')){

			$configPaths = array('assets_general_path','media_category_path','media_product_path','media_manufacturer_path','media_vendor_path');
			foreach($configPaths as $path){
				$this -> renameFileExtension(JPATH_ROOT.DS.VmConfig::get($path) );
			}

			if(!class_exists('Migrator')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'migrator.php');
			$migrator = new Migrator();
			$result = $migrator->portMedia();

			$this->setRedirect($this->redirectPath, $result);
		} else {
			$msg = 'Forget IT';
			$this->setRedirect('index.php?option=com_virtuemart', $msg);
		}


	}

	function renameFileExtension($path){

		$results = array();
		$handler = opendir($path);

		// open directory and walk through the filenames
		while ($file = readdir($handler)) {
			// if file isn't this directory or its parent, add it to the results
			if ($file != "." && $file != "..") {
				if(preg_match('/JPEG$/', $file)) {
					$results['jpeg'][] = $file;
				} else if(preg_match('/JPG$/', $file)) {
					$results['jpg'][] = $file;
				} else if(preg_match('/PNG$/', $file)) {
					$results['png'][] = $file;
				} else if(preg_match('/GIF$/', $file)) {
					$results['gif'][] = $file;
				}
			}
		}
		//vmdebug('renameFileExtension',$results);
		foreach($results as $filetype => $files){
			foreach($files as $file){
				$new = JFile::stripExt($file);
				//vmdebug('Rename file ',$path.$file,$path.$new.'.'.$filetype);
				if(!JFile::exists($file)){
					$succ = rename ($path.$file,$path.$new.'.'.$filetype);
				}
			}
		}

	}
}
// pure php no closing tag
