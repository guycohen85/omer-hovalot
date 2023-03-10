<?php
/**
 *
 * Data module for shipment
 *
 * @package	VirtueMart
 * @subpackage Shipment
 * @author RickG
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: shipmentmethod.php 8139 2014-07-22 14:16:45Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shop shipment
 *
 * @package	VirtueMart
 * @subpackage Shipment
 * @author RickG
 */
class VirtueMartModelShipmentmethod extends VmModel {

	//    /** @var integer Primary key */
	//    var $_id;
	/** @var integer Joomla plugin ID */
	var $jplugin_id;
	/** @var integer Vendor ID */
	var $virtuemart_vendor_id;

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('shipmentmethods');
		$this->_selectedOrdering = 'ordering';
		$this->setToggleName('shared');
	}

	/**
	 * Retrieve the detail record for the current $id if the data has not already been loaded.
	 *
	 * @author RickG
	 */
	function getShipment($id = 0) {

		if(!empty($id)) $this->_id = (int)$id;

		if (empty($this->_cache[$this->_id])) {
			$this->_cache[$this->_id] = $this->getTable('shipmentmethods');
			$this->_cache[$this->_id]->load((int)$this->_id);


			if(empty($this->_cache[$this->_id]->virtuemart_vendor_id)){
				if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
				$this->_cache[$this->_id]->virtuemart_vendor_id = VirtueMartModelVendor::getLoggedVendor();;
			}

			if ($this->_cache[$this->_id]->shipment_jplugin_id) {
				JPluginHelper::importPlugin ('vmshipment');
				$dispatcher = JDispatcher::getInstance ();
				$blind = 0;
				$retValue = $dispatcher->trigger ('plgVmDeclarePluginParamsShipmentVM3', array(&$this->_cache[$this->_id]));
			}

			if(!empty($this->_cache[$this->_id]->_varsToPushParam)){
				VmTable::bindParameterable($this->_cache[$this->_id],'shipment_params',$this->_cache[$this->_id]->_varsToPushParam);
			}

			/* Add the shipmentcarreir shoppergroups */
			$q = 'SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_shipmentmethod_shoppergroups WHERE `virtuemart_shipmentmethod_id` = "'.$this->_id.'"';
			$this->_db->setQuery($q);
			$this->_cache[$this->_id]->virtuemart_shoppergroup_ids = $this->_db->loadResultArray();
			if(empty($this->_cache[$this->_id]->virtuemart_shoppergroup_ids)) $this->_cache[$this->_id]->virtuemart_shoppergroup_ids = 0;

		}

		return $this->_cache[$this->_id];
	}

	/**
	 * Retireve a list of shipment from the database.
	 *
	 * @author RickG
	 * @return object List of shipment  objects
	 */
	public function getShipments() {

		$table = '#__extensions';
		$enable = 'enabled';
		$ext_id = 'extension_id';

		$whereString = '';
		$select = ' * FROM `#__virtuemart_shipmentmethods_'.VmConfig::$vmlang.'` as l ';
		$joinedTables = ' JOIN `#__virtuemart_shipmentmethods`   USING (`virtuemart_shipmentmethod_id`) ';
		$datas =$this->exeSortSearchListQuery(0,$select,$joinedTables,$whereString,' ',$this->_getOrdering() );

		if(isset($datas)){
			if(!class_exists('shopfunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');
			foreach ($datas as &$data){
				// Add the shipment shoppergroups
				$q = 'SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_shipmentmethod_shoppergroups WHERE `virtuemart_shipmentmethod_id` = "'.$data->virtuemart_shipmentmethod_id.'"';
				$db = JFactory::getDBO();
				$db->setQuery($q);
				$data->virtuemart_shoppergroup_ids = $db->loadColumn();
			}
		}
		return $datas;
	}



	/**
	 * Bind the post data to the shipment tables and save it
	 *
	 * @author Max Milbers
	 * @return boolean True is the save was successful, false otherwise.
	 */
	public function store(&$data)
	{

		if ($data) {
			$data = (array)$data;
		}

		if(!empty($data['params'])){
			foreach($data['params'] as $k=>$v){
				$data[$k] = $v;
			}
		}

		if(empty($data['virtuemart_vendor_id'])){
			if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
			$data['virtuemart_vendor_id'] = VirtueMartModelVendor::getLoggedVendor();
		} else {
			$data['virtuemart_vendor_id'] = (int) $data['virtuemart_vendor_id'];
		}

		$tb = '#__extensions';
		$ext_id = 'extension_id';

		$q = 'SELECT `element` FROM `' . $tb . '` WHERE `' . $ext_id . '` = "'.$data['shipment_jplugin_id'].'"';
		$db = JFactory::getDBO();
		$db->setQuery($q);
		$data['shipment_element'] = $db->loadResult();

		$table = $this->getTable('shipmentmethods');

		if(isset($data['shipment_jplugin_id'])){

			JPluginHelper::importPlugin('vmshipment');
			$dispatcher = JDispatcher::getInstance();
			//bad trigger, we should just give it data, so that the plugins itself can check the data to be stored
			//so this trigger is now deprecated and will be deleted in vm2.2
			$retValue = $dispatcher->trigger('plgVmSetOnTablePluginParamsShipment',array( $data['shipment_element'],$data['shipment_jplugin_id'],&$table));

			$retValue = $dispatcher->trigger('plgVmSetOnTablePluginShipment',array( &$data,&$table));

		}

		$table->bindChecknStore($data);
		$errors = $table->getErrors();
		foreach($errors as $error){
			vmError($error);
		}
		$xrefTable = $this->getTable('shipmentmethod_shoppergroups');
		$xrefTable->bindChecknStore($data);
		$errors = $xrefTable->getErrors();
		foreach($errors as $error){
			vmError($error);
		}

		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		//Add a hook here for other shipment methods, checking the data of the choosed plugin
		$dispatcher = JDispatcher::getInstance();
		$retValues = $dispatcher->trigger('plgVmOnStoreInstallShipmentPluginTable', array(  $data['shipment_jplugin_id']));

		return $table->virtuemart_shipmentmethod_id;
	}
	/**
	 * Creates a clone of a given shipmentmethod id
	 *
	 * @author Val??rie Isaksen
	 * @param int $virtuemart_shipmentmethod_id
	 */

	public function createClone ($id) {

		//	if (is_array($cids)) $cids = array($cids);
		$this->setId ($id);
		$shipment = $this->getShipment();
		$shipment->virtuemart_shipmentmethod_id = 0;
		$shipment->shipment_name = $shipment->shipment_name.' Copy';
		if (!$clone = $this->store($shipment)) {
			JError::raiseError(500, 'createClone '. $shipment->getError() );
		}
		return $clone;
	}
}

//no closing tag
