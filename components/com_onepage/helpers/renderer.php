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
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
if (!class_exists('VirtueMartViewCart'))
require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'virtuemart.cart.view.html.php'); 

class OPCrenderer extends VirtueMartViewCart {
  public function __construct() {
			
		$layoutName = $this->getLayout();
		if (!$layoutName) $layoutName = JRequest::getWord('layout', 'default');
		$this->assignRef('layoutName', $layoutName);
		$format = JRequest::getWord('format');
		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$this->assignRef('cart', $cart);
		$checkout_task = 'confirm';
		$this->assignRef('checkout_task', $checkout_task);
		$checkoutAdvertise =$this->getCheckoutAdvertise();
		$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();
		$shippingText = ''; 
		$this->assignRef('select_shipment_text', $shippingText);
		$paymentText = ''; 
		$this->assignRef('select_payment_text', $paymentText);
		$this->assignRef('checkout_link_html', $paymentText);
	    //set order language
	    $lang = JFactory::getLanguage();
	    $order_language = $lang->getTag();
		$this->assignRef('order_language',$order_language);
		$useSSL = VmConfig::get('useSSL', 0);
		$useXHTML = true;
		$this->assignRef('useSSL', $useSSL);
		$this->assignRef('useXHTML', $useXHTML);
		$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
		$this->assignRef('checkoutAdvertise', $checkoutAdvertise);
		$tmp = 0;
	    
		if (method_exists($this, 'assignRef'))
			$this->assignRef('found_shipment_method', $tmp);

	
	
	}
	static $_instance;
	static public function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new OPCrenderer();
		} else {
			//We store in UTC and use here of course also UTC
			
		}
		return self::$_instance;
	}
	private static function getSelected($cart_key, $custom_id)
	{
	   $a1 = explode('::', $cart_key); 
	   
	   if (count($a1) <= 1) return ''; 
	   $a2 = explode(';', $a1[1]); 
	   
	   if (count($a2) <= 1) return ''; 
	   foreach ($a2 as $val)
	    {
		  $a3 = explode(':', $val); 
		  if (count($a3) <= 1) return ''; 
		  if ($a3[1] == $custom_id) return $a3[0]; 
		}
	  return ''; 
	}
	public static function getCustomFields($virtuemart_product_id, $cart_key='', $quantity=1)
	{
	  $html = ''; 
	  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 	
	   $product_model = OPCmini::getModel('product');
	   $product = $product_model->getProduct($virtuemart_product_id,TRUE,TRUE,TRUE,$quantity, true);
	   
	   
	   $customfieldModel = OPCmini::getModel ('Customfields');
	   $product->customfields = $customfieldModel->getproductCustomslist ($virtuemart_product_id);

		if (empty($product->customfields) and !empty($product->product_parent_id)) {
						//$product->customfields = $this->productCustomsfieldsClone($product->product_parent_id,true) ;
				$product->customfields = $customfieldModel->getproductCustomslist ($product->product_parent_id, $virtuemart_product_id);
				$product->customfields_fromParent = TRUE;
		}
	   
	    $customfieldModel->getProductCustomsField($product); 
		
		$product->customfields = $customfieldModel->getProductCustomsFieldCart ($product);
		
		
	    foreach ($product->customfields as $k => $custom) {
		    if (!empty($custom->layout_pos)) {
			    $product->customfieldsSorted[$custom->layout_pos][] = $custom;
			    unset($product->customfields[$k]);
		    }
	    }
	    $product->customfieldsSorted['normal'] = $product->customfields;
	    unset($product->customfields);
		JHTMLOPC::script('opcattributes.js', 'components/com_onepage/assets/js/'); 
		$html .= '
		<form method="post" class="opccartproduct opc-recalculate" action="'.JRoute::_('index.php').'">
		<input name="quantity[0]" class=".quantity-input" type="hidden" value="'.$quantity.'">
		
		<input name="virtuemart_product_id[0]" class="opc_product" type="hidden" value="'.$virtuemart_product_id.'">
		<input name="cart_key" value="'.$cart_key.'" type="hidden" />
		<input name="cart_virtuemart_product_id" value="'.$cart_key.'" type="hidden" />
		<div class="product-fields">'; 
	    
	    $custom_title = null;
		foreach ($product->customfieldsSorted as $positions=>$val)
	    foreach ($val as $field) 
		{
		
	    	if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
	    		continue;
			if ($field->display) 
			{
			
			$html .= '<div class="product-field product-field-type-'.$field->field_type.'">'; 
		    if ($field->custom_title != $custom_title && $field->show_title) { 
			    $html .= '<span class="product-fields-title" >'.JText::_($field->custom_title).'</span>'; 
			    
			    if ($field->custom_tip)
				$html .= JHTML::tooltip($field->custom_tip, JText::_($field->custom_title), 'tooltip.png');
			}
			$display = $field->display; 
			$selected = self::getSelected($cart_key, $field->virtuemart_custom_id); 
			
			
			$display = str_replace(JText::_('COM_VIRTUEMART_CART_PRICE_FREE'), '', $display); 
			$display = str_replace('value="'.$selected.'"', ' checked="checked" selected="selected" value="'.$selected.'" ', $display); 
			
			$html .= '<div class="product-field-display" style="clear:both;">'.$display.'</div>
	    	    <span class="product-field-desc">'.jText::_($field->custom_field_desc).'</span>
	    	</div>'; 
		    
		    $custom_title = $field->custom_title;
			}
	    }
	   $html .= '</div></form>'; 
		
		
		
	return $html; 	
    }
	
	
	
	
	
	public static function renderModuleByPosition($position, $params=null)
	{
	    jimport( 'joomla.application.module.helper' );
		$searchmodules = JModuleHelper::getModules($position);
		$output = ''; 
                foreach ($searchmodules as $searchmodule)
                {
				    $params = new JRegistry;
                    $params->loadString($searchmodule->params);
                    $output .= JModuleHelper::renderModule($searchmodule, array());
                    
                    
                }
	   return $output; 
	}
	
	public static function renderModuleByName($name, $params=null)
	{
	    jimport( 'joomla.application.module.helper' );

	    $document   = JFactory::getDocument();
		$renderer   = $document->loadRenderer('module');
		if (empty($params))
		$params   = array();
		$module = JModuleHelper::getModule($name); 
		return $renderer->render($module, $params);

	}
	
	public function op_show_image(&$image, $extra, $width, $height, $type)
	{
	  return OPCloader::op_show_image($image, $extra, $width, $height, $type);
	}
	
	function fetch(&$ref, $template, $vars, $new='')
 {
   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');   
   
   OPCloader::setRegType(); 
   
   if (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION')
   $op_create_account_unchecked = false; 
   
   if (!empty($ref->cart))
   $cart = $ref->cart; 
   else
   $cart = VirtueMartCart::getCart(false); 
   
   $op_disable_shipping = OPCloader::getShippingEnabled();
   $no_shipping = $op_disable_shipping;
   
   if (OPCloader::checkOPCSecret())
   {
	 $selected_template .= '_preview'; 
   }
   /*
   if ($template == 'update_form.tpl')
    {
	 echo JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'overrides'.DS.$template.'.php'; 
	 die('h');   
	}
	*/
   
   if (file_exists(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'overrides'.DS.$template.'.php'))
    {
	
	  ob_start(); 
	  extract($vars); 

	  include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'overrides'.DS.$template.'.php'); 
	  $ret = ob_get_clean(); 
	  
	  return $ret; 
	}
   else
    {
	  if (!empty($new))
	   {
	     $ly = $ref->layoutName; 
		 if (empty($ly)) $ly = 'default'; 
		 if (empty($new)) $new = 'prices'; 
	     if (file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'cart'.DS.'tmpl'.DS.$ly.'_'.$new.'.php'))
		  {
		    ob_start(); 
			include(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'cart'.DS.'tmpl'.DS.$ly.'_'.$new.'.php'); 
			$ret = ob_get_clean(); 
			return $ret; 
		  }
	     
	   }
	}
 }
 public function fetchVirtuemart($name, $view='cart', $layout='default')
 {
     $template = VmConfig::get( 'vmtemplate', 'default' );
   if (file_exists(JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.$view.DS.$layout.'_'.$name.'.php'))
    {
	 
	  ob_start(); 
	  extract($vars); 
	  include(JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.$view.DS.$layout.'_'.$name.'.php');
	  $ret = ob_get_clean(); 
	  
	  return $ret; 
	}
   else
    {
	  
	   
	    
	     if (file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.$view.DS.'tmpl'.DS.$layout.'_'.$name.'.php'))
		  {
		    ob_start(); 
			include(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.$view.DS.'tmpl'.DS.$layout.'_'.$name.'.php'); 
			$ret = ob_get_clean(); 
			return $ret; 
		  }
	     
	   
	}
 }
 
 public function fetchBasket(&$ref, $template, $vars, $new='')
 {
 
   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');   

   $op_disable_shipping = OPCloader::getShippingEnabled();
   $no_shipping = $op_disable_shipping;
   $instance = OPCrenderer::getInstance(); 
   return $instance->fetchVirtuemart('pricelist', 'cart', 'default'); 
 
 
 }
 public function loadTemplate($theme)
 {
  return ""; 
  $instance = OPCrenderer::getInstance(); 
  return $instance->fetchVirtuemart($theme, 'cart', 'default'); 
 }
 

	
}