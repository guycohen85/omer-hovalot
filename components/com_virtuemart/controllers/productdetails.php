<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: productdetails.php 8026 2014-06-12 17:01:23Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport ('joomla.application.component.controller');

/**
 * VirtueMart Component Controller
 *
 * @package VirtueMart
 * @author Max Milbers
 */
class VirtueMartControllerProductdetails extends JControllerLegacy {

	public function __construct () {

		parent::__construct ();
		$this->registerTask ('recommend', 'MailForm');
		$this->registerTask ('askquestion', 'MailForm');
	}

	function display($cachable = false, $urlparams = false) {

		$format = vRequest::getCmd ('format', 'html');
		if ($format == 'pdf') {
			$viewName = 'Pdf';
		} else {
			$viewName = 'Productdetails';
		}

		$view = $this->getView ($viewName, $format);

		$view->display ();
	}

	/**
	 * Send the ask question email.
	 *
	 * @author Kohl Patrick, Christopher Roussel
	 * @author Max Milbers
	 */
	public function mailAskquestion () {

		JSession::checkToken () or jexit ('Invalid Token');

		$app = JFactory::getApplication ();
		if(!VmConfig::get('ask_question',false)){
			$app->redirect (JRoute::_ ('index.php?option=com_virtuemart&tmpl=component&view=productdetails&task=askquestion&virtuemart_product_id=' . vRequest::getInt ('virtuemart_product_id', 0)), 'Function disabled');
		}

		$view = $this->getView ('askquestion', 'html');
		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}

		$vars = array();
		$min = VmConfig::get ('asks_minimum_comment_length', 50) + 1;
		$max = VmConfig::get ('asks_maximum_comment_length', 2000) - 1;
		$commentSize = mb_strlen (vRequest::getString ('comment'));
		$validMail = filter_var (vRequest::getVar ('email'), FILTER_VALIDATE_EMAIL);

		if ($commentSize < $min or $commentSize > $max or !$validMail) {
			$errmsg = vmText::_ ('COM_VIRTUEMART_COMMENT_NOT_VALID_JS');
			if ($commentSize < $min) {
				vmdebug ('mailAskquestion', $min, $commentSize);
				$errmsg = vmText::_ ('COM_VIRTUEMART_ASKQU_CS_MIN');

			} else {
				if ($commentSize > $max) {
					$errmsg = vmText::_ ('COM_VIRTUEMART_ASKQU_CS_MAX');

				} else {
					if (!$validMail) {
						$errmsg = vmText::_ ('COM_VIRTUEMART_ASKQU_INV_MAIL');

					}
				}
			}

			$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&tmpl=component&view=productdetails&task=askquestion&virtuemart_product_id=' . vRequest::getInt ('virtuemart_product_id', 0)), $errmsg);
			return;
		}

		if(JFactory::getUser()->guest == 1 and VmConfig::get ('ask_captcha')){
			$recaptcha = vRequest::getVar ('recaptcha_response_field');
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$recaptcha);
			$session = JFactory::getSession();
			if(!$res[0]){
				$askquestionform = array('name' => vRequest::getVar ('name'), 'email' => vRequest::getVar ('email'), 'comment' => vRequest::getString ('comment'));
				$session->set('askquestion', $askquestionform, 'vm');
				$errmsg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&tmpl=component&view=productdetails&task=askquestion&virtuemart_product_id=' . vRequest::getInt ('virtuemart_product_id', 0)), $errmsg);
				return;
			} else {
				$session->set('askquestion', 0, 'vm');
			}
		}

		$user = JFactory::getUser ();
		if (empty($user->id)) {
			$fromMail = vRequest::getVar ('email'); //is sanitized then
			$fromName = vRequest::getVar ('name', ''); //is sanitized then
			$fromMail = str_replace (array('\'', '"', ',', '%', '*', '/', '\\', '?', '^', '`', '{', '}', '|', '~'), array(''), $fromMail);
			$fromName = str_replace (array('\'', '"', ',', '%', '*', '/', '\\', '?', '^', '`', '{', '}', '|', '~'), array(''), $fromName);
		} else {
			$fromMail = $user->email;
			$fromName = $user->name;
		}
		$vars['user'] = array('name' => $fromName, 'email' => $fromMail);

		$virtuemart_product_id = vRequest::getInt ('virtuemart_product_id', 0);
		$productModel = VmModel::getModel ('product');

		$vars['product'] = $productModel->getProduct ($virtuemart_product_id);


		$vendorModel = VmModel::getModel ('vendor');
		$VendorEmail = $vendorModel->getVendorEmail ($vars['product']->virtuemart_vendor_id);
		$vars['vendor'] = array('vendor_store_name' => $fromName);

		if (shopFunctionsF::renderMail ('askquestion', $VendorEmail, $vars, 'productdetails')) {
			$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
		} else {
			$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
		}
		$app->enqueueMessage (vmText::_ ($string));


		$view->setLayout ('mail_confirmed');
		$view->display ();
	}

	/**
	 * Send the Recommend to a friend email.
	 *
	 * @author Kohl Patrick
	 * @author Max Milbers
	 */
	public function mailRecommend () {

		JSession::checkToken () or jexit ('Invalid Token');

		$app = JFactory::getApplication ();
		if(!VmConfig::get('show_emailfriend',false)){
			$app->redirect (JRoute::_ ('index.php?option=com_virtuemart&tmpl=component&view=productdetails&task=askquestion&virtuemart_product_id=' . vRequest::getInt ('virtuemart_product_id', 0)), 'Function disabled');
		}

		if(JFactory::getUser()->guest == 1 and VmConfig::get ('ask_captcha')){
			$recaptcha = vRequest::getVar ('recaptcha_response_field');
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$recaptcha);
			$session = JFactory::getSession();
			if(!$res[0]){
				$mailrecommend = array('email' => vRequest::getVar ('email'), 'comment' => vRequest::getString ('comment'));
				$session->set('mailrecommend', $mailrecommend, 'vm');
				$errmsg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&tmpl=component&view=productdetails&task=recommend&virtuemart_product_id=' . vRequest::getInt ('virtuemart_product_id', 0)), $errmsg);
				return;
			} else {
				$session->set('mailrecommend', 0, 'vm');
			}
		}

		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}
		if(!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');

		$vars = array();

		$toMail = vRequest::getVar ('email'); //is sanitized then
		$toMail = str_replace (array('\'', '"', ',', '%', '*', '/', '\\', '?', '^', '`', '{', '}', '|', '~'), array(''), $toMail);

		if (shopFunctionsF::renderMail ('recommend', $toMail, $vars, 'productdetails', TRUE)) {
			$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
		} else {
			$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
		}
		$app->enqueueMessage (vmText::_ ($string));

		$view = $this->getView ('recommend', 'html');

		$view->setLayout ('mail_confirmed');
		$view->display ();
	}

	/**
	 *  Ask Question form
	 * Recommend form for Mail
	 */
	public function MailForm () {

		if (vRequest::getCmd ('task') == 'recommend') {
			$view = $this->getView ('recommend', 'html');
		} else {
			$view = $this->getView ('askquestion', 'html');
		}

		// Set the layout
		$view->setLayout ('form');

		// Display it all
		$view->display ();
	}

	/**
	 * Add or edit a review
	 */
	public function review () {
		$msg="";


		$model = VmModel::getModel ('ratings');
		$virtuemart_product_id = vRequest::getInt('virtuemart_product_id',0);

		$allowReview = $model->allowReview($virtuemart_product_id);
		$allowRating = $model->allowRating($virtuemart_product_id);
		if($allowReview || $allowRating){
			$return = $model->saveRating ();
			if ($return !== FALSE) {
				$errors = $model->getErrors ();
				if (empty($errors)) {
					$msg = vmText::sprintf ('COM_VIRTUEMART_STRING_SAVED', vmText::_ ('COM_VIRTUEMART_REVIEW'));
				}
				foreach ($errors as $error) {
					$msg = ($error) . '<br />';
				}
				if (!class_exists ('ShopFunctionsF')) {
					require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
				}
				$data = vRequest::getPost();
				if($allowReview){

				}

				shopFunctionsF::sendRatingEmailToVendor($data);
			}
		}

		$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id, FALSE), $msg);

	}

	/**
	 * Json task for recalculation of prices
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 *
	 */
	public function recalculate () {

		//$post = vRequest::get('request');


		$virtuemart_product_idArray = vRequest::getInt ('virtuemart_product_id', array()); //is sanitized then
		if(is_array($virtuemart_product_idArray) and !empty($virtuemart_product_idArray[0])){
			$virtuemart_product_id = $virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = $virtuemart_product_idArray;
		}
		$customProductData = vRequest::getVar ('customProductData', array()); //is sanitized then

		/*$customPrices = array();
		$customVariants = vRequest::getVar ('customPrice', array()); //is sanitized then
		//echo '<pre>'.print_r($customVariants,1).'</pre>';

		//MarkerVarMods
		foreach ($customVariants as $customVariant) {
			//foreach ($customVariant as $selected => $priceVariant) {
			//In this case it is NOT $selected => $variant, because we get it that way from the form
			foreach ($customVariant as $priceVariant => $selected) {
				//Important! sanitize array to int
				$selected = (int)$selected;
				$customPrices[$selected] = $priceVariant;
			}
		}*/

		$quantityArray = vRequest::getInt ('quantity', array()); //is sanitized then
		if(is_array($quantityArray) and !empty($quantityArray[0])){
			$quantity = $quantityArray[0];
		} else {
			$quantity = $quantityArray;
		}

		if (empty($quantity)) {
			$quantity = 1;
		}

		$product_model = VmModel::getModel ('product');


		if(!empty($virtuemart_product_id)){
			$prices = $product_model->getPrice ($virtuemart_product_id, $quantity);
		} else {
			jexit ();
		}
		$priceFormated = array();
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();

		$priceFieldsRoots = array('basePrice','variantModification','basePriceVariant',
			'basePriceWithTax','discountedPriceWithoutTax',
			'salesPrice','priceWithoutTax',
			'salesPriceWithDiscount','discountAmount','taxAmount','unitPrice');

		foreach ($priceFieldsRoots as $name) {
// 		echo 'Price is '.print_r($name,1).'<br />';
			//if ($name != 'costPrice') {
				if(isset($prices[$name])){
					$priceFormated[$name] = $currency->createPriceDiv ($name, '', $prices, TRUE);
				}

			//}
		}

		// Get the document object.
		$document = JFactory::getDocument ();
		// stAn: setName works in JDocumentHTML and not JDocumentRAW
		if (method_exists($document, 'setName')){
			$document->setName ('recalculate');
		}

		JResponse::setHeader ('Cache-Control', 'no-cache, must-revalidate');
		JResponse::setHeader ('Expires', 'Mon, 6 Jul 2000 10:00:00 GMT');
		// Set the MIME type for JSON output.
		$document->setMimeEncoding ('application/json');
		JResponse::setHeader ('Content-Disposition', 'attachment;filename="recalculate.json"', TRUE);
		JResponse::sendHeaders ();
		echo json_encode ($priceFormated);
		jexit ();
	}

	public function getJsonChild () {

		$view = $this->getView ('productdetails', 'json');

		$view->display (NULL);
	}

	/**
	 * Notify customer
	 *
	 * @author Seyi Awofadeju
	 *
	 */
	public function notifycustomer () {

		$data = vRequest::get ('post');

		$model = VmModel::getModel ('waitinglist');
		if (!$model->adduser ($data)) {
			$errors = $model->getErrors ();
			foreach ($errors as $error) {
				$msg = ($error) . '<br />';
			}
			$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $data['virtuemart_product_id'], FALSE), $msg);
		} else {
			$msg = vmText::sprintf ('COM_VIRTUEMART_STRING_SAVED', vmText::_ ('COM_VIRTUEMART_CART_NOTIFY'));
			$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $data['virtuemart_product_id'], FALSE), $msg);
		}

	}
	/*
	 * Send an email to all shoppers who bought a product
	 */

	public function sentProductEmailToShoppers () {

		$model = VmModel::getModel ('product');
	    $model->sentProductEmailToShoppers ();
	}

	/*
	 * View email layout on browser
	 */
	function viewRecommendMail(){

		$view = $this->getView('recommend', 'html');
		$viewLayout = vRequest::getCmd('layout', 'mail_html');
		$view->setLayout($viewLayout);
		$view->display();
	}

	function viewAskQuestionMail(){

		$view = $this->getView('askquestion', 'html');
		$viewLayout = vRequest::getCmd('layout', 'mail_confirmed');
		$view->setLayout($viewLayout);
		$view->display();
	}

}
// pure php no closing tag
