<?php
/**
 *
 * Controller for the front end User maintenance
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: user.php 8132 2014-07-16 10:22:39Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * VirtueMart Component Controller
 *
 * @package		VirtueMart
 */
class VirtueMartControllerUser extends JControllerLegacy
{

	public function __construct()
	{
		parent::__construct();
		$this->useSSL = VmConfig::get('useSSL',0);
		$this->useXHTML = false;
		VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
	}

	/**
	 * Override of display to prevent caching
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display(){

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = vRequest::getCmd('view', 'user');
		$viewLayout = vRequest::getCmd('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('layout' => $viewLayout));
		$view->assignRef('document', $document);

		if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->_fromCart = false;
		$cart->setCartIntoSession();
		$view->display();

		return $this;
	}


	function editAddressCart(){

		$view = $this->getView('user', 'html');
		$view->setLayout('edit_address');

		if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->_fromCart = true;
		$cart->setCartIntoSession();
		// Display it all
		$view->display();

	}


	/**
	 * This is the save function for the normal user edit.php layout.
	 *
	 * @author Max Milbers
	 */
	function saveUser(){

		if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();

		$layout = vRequest::getCmd('layout','edit');


		if($cart->_fromCart or $cart->getInCheckOut()){
			vmdebug('saveUser _fromCart',(int)$cart->_fromCart);
			$msg = $this->saveData($cart);
			$task = '';
			if ($cart->getInCheckOut()){
				$task = '&task=checkout';
				vmdebug('saveUser InCheckOut',(int)$cart->_fromCart);
			}
			$this->setRedirect(JRoute::_('index.php?option=com_virtuemart&view=cart'.$task, FALSE) , $msg);
		} else {
			$msg = $this->saveData(false);
			$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, FALSE), $msg );
		}

	}

	function saveAddressST(){

		$msg = $this->saveData(false);
		$layout = 'edit';// vRequest::getCmd('layout','edit');
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, FALSE), $msg );

	}

	/**
	 * Save the user info. The saveData function don't use the userModel store function for anonymous shoppers, because it would register them.
	 * We make this function private, so we can do the tests in the tasks.
	 *
	 * @author Max Milbers
	 * @author Val??rie Isaksen
	 *
	 * @param boolean Defaults to false, the param is for the userModel->store function, which needs it to determine how to handle the data.
	 * @return String it gives back the messages.
	 */
	private function saveData($cartObj) {
		$mainframe = JFactory::getApplication();
		$currentUser = JFactory::getUser();
		$msg = '';

		$data = vRequest::getPost();
		$cart = false;
		if($cartObj){
			if($cartObj->_fromCart or $cartObj->getInCheckOut()){
				if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
				$cart = VirtueMartCart::getCart();
				$prefix= '';
				if ($data['address_type'] == 'STaddress' || $data['address_type'] =='ST') {
					$prefix = 'shipto_';
					vmdebug('Storing user ST prefix '.$prefix);
				}
				$cart->saveAddressInCart($data, $data['address_type'],true,$prefix);
			}
		}

		if(empty($data['address_type'])){
			$data['address_type'] = vRequest::getCmd('addrtype','BT');
		}

		if (isset($_POST['register'])) {
			if($this->checkCaptcha('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT') == FALSE) {
				$msg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				if($cartObj->_fromCart) {
					$this->redirect( JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT'), $msg );
				} else if($cartObj->getInCheckOut()) {
					$this->redirect( JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT'), $msg );
				} else {
					$this->redirect( JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT'), $msg );
				}
				return $msg;
			}
		}

		$userModel = VmModel::getModel('user');

		if(!$cart){
			// Store multiple selectlist entries as a ; separated string
			if (array_key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
				$data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
			}

			$data['vendor_store_name'] = vRequest::getHtml('vendor_store_name');
			$data['vendor_store_desc'] = vRequest::getHtml('vendor_store_desc');
			$data['vendor_terms_of_service'] = vRequest::getHtml('vendor_terms_of_service');
			$data['vendor_letter_css'] = vRequest::getHtml('vendor_letter_css');
			$data['vendor_letter_header_html'] = vRequest::getHtml('vendor_letter_header_html');
			$data['vendor_letter_footer_html'] = vRequest::getHtml('vendor_letter_footer_html');
		}
		//vmdebug('saveData store user',$data);

		//It should always be stored
		if($data['address_type'] == 'ST' or (!isset($_POST['register']) and $currentUser->guest)){
			$ret = $userModel->storeAddress($data);
			//vmdebug('saveData storeAddress only');
		} else {
			$ret = $userModel->store($data);

			if(isset($_POST['register']) or (!$cart and $currentUser->guest==1) ){
				$msg = (is_array($ret)) ? $ret['message'] : $ret;
				$usersConfig = JComponentHelper::getParams( 'com_users' );
				$useractivation = $usersConfig->get( 'useractivation' );

				if (is_array($ret) and $ret['success'] and !$useractivation) {
					// Username and password must be passed in an array
					$credentials = array('username' => $ret['user']->username,
						'password' => $ret['user']->password_clear
					);
					$return = $mainframe->login($credentials);
				} else if(VmConfig::get('oncheckout_only_registered',0)){
					$layout = vRequest::getCmd('layout','edit');
					$this->redirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, FALSE), $msg );
				}
			}
		}

		return $msg;
	}


	/**
	 * Action cancelled; return to the previous view
	 *
	 * @author Max Milbers
	 */
	function cancel()
	{
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
		$cart = VirtueMartCart::getCart();
		vmdebug('cancel executed' );
		if($cart->_fromCart){
			$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE)  );
		} else {
			$return = JURI::base();
			$this->setRedirect( $return );
		}

	}


	function removeAddressST(){

		$virtuemart_userinfo_id = vRequest::getInt('virtuemart_userinfo_id');

		//Lets do it dirty for now
		$userModel = VmModel::getModel('user');
		$userModel->removeAddress($virtuemart_userinfo_id);

		$layout = vRequest::getCmd('layout','edit');
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, $this->useXHTML,$this->useSSL) );
	}

	/**
	 * Check the Joomla ReCaptcha Plg
	 *
	 * @author Maik K??nnemann
	 */
	function checkCaptcha($retUrl){
		if(JFactory::getUser()->guest==1 and VmConfig::get ('reg_captcha')){
			$recaptcha = vRequest::getVar ('recaptcha_response_field');
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$recaptcha);
			if(!$res[0]){
				$data = vRequest::getPost();
				$data['address_type'] = vRequest::getVar('addrtype','BT');
				if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
				$cart = VirtueMartCart::getCart();
				$prefix= '';
				if ($data['address_type'] == 'STaddress' || $data['address_type'] =='ST') {
					$prefix = 'shipto_';
				}
				$cart->saveAddressInCart($data, $data['address_type'],true,$prefix);
				$errmsg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				$this->setRedirect (JRoute::_ ($retUrl . '&captcha=1', FALSE), $errmsg);
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

}
// No closing tag
