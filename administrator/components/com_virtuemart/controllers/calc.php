<?php
/**
*
* Calc controller
*
* @package	VirtueMart
* @subpackage Calc
* @author Max Milbers, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: calc.php 7821 2014-04-08 11:07:57Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');

/**
 * Calculator Controller
 *
 * @package    VirtueMart
 * @subpackage Calculation tool
 * @author Max Milbers
 */
class VirtuemartControllerCalc extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function __construct() {
		parent::__construct();

	}



	/**
	 * We want to allow html so we need to overwrite some request data
	 *
	 * @author Max Milbers
	 */
	function save($data = 0){

		$data = vRequest::getRequest();

		$data['calc_name'] = vRequest::getHtml('calc_name','');
		$data['calc_descr'] = vRequest::getHtml('calc_descr','');

		parent::save($data);
	}


	/**
	* Save the calc order
	*
	* @author jseros
	*/
	public function orderUp()
	{
		// Check token
		vRequest::vmCheckToken();

		$id = 0;
		$cid	= vRequest::getInt( 'cid', array() );

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_virtuemart&view=calc', vmText::_('COM_VIRTUEMART_NO_ITEMS_SELECTED') );
			return false;
		}

		//getting the model
		$model = VmModel::getModel('calc');

		if ($model->orderCalc($id, -1)) {
			$msg = vmText::_('COM_VIRTUEMART_ITEM_MOVED_UP');
		} else {
			$msg = $model->getError();
		}

		$this->setRedirect( 'index.php?option=com_virtuemart&view=calc', $msg );
	}


	/**
	* Save the calc order
	*
	* @author jseros
	*/
	public function orderDown()
	{
		// Check token
		vRequest::vmCheckToken();

		$id = 0;
		$cid	= vRequest::getInt( 'cid', array() );

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_virtuemart&view=calc', vmText::_('COM_VIRTUEMART_NO_ITEMS_SELECTED') );
			return false;
		}

		//getting the model
		$model = VmModel::getModel('calc');

		if ($model->orderCalc($id, 1)) {
			$msg = vmText::_('COM_VIRTUEMART_ITEM_MOVED_DOWN');
		} else {
			$msg = $model->getError();
		}

		$this->setRedirect( 'index.php?option=com_virtuemart&view=calc', $msg );
	}


	/**
	* Save the categories order
	*/
	public function saveOrder()
	{
		// Check for request forgeries
		vRequest::vmCheckToken();

		$cid	= vRequest::getInt( 'cid', array() );

		$model = VmModel::getModel('calc');

		$order	= vRequest::getInt('order');

		if ($model->setOrder($cid,$order)) {
			$msg = vmText::_('COM_VIRTUEMART_NEW_ORDERING_SAVED');
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_virtuemart&view=calc', $msg );
	}

}
// pure php no closing tag
