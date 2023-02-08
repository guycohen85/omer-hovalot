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

class OPCPluginHelper {
  
  public static function getPluginElement($type, $vmid, $extra=false)
  {
    $db = JFactory::getDBO(); 
	if ($extra)
	$q = 'select * from `#__virtuemart_'.$db->getEscaped($type).'methods` where `virtuemart_'.$db->getEscaped($type).'method_id` = '.(int)$vmid.' limit 0,1'; 
	else
	$q = 'select `'.$db->getEscaped($type).'_element` from `#__virtuemart_'.$db->getEscaped($type).'methods` where `virtuemart_'.$db->getEscaped($type).'method_id` = '.(int)$vmid.' limit 0,1'; 
	$db->setQuery($q); 
	if ($extra)
	 {
	   $res = $db->loadAssoc(); 
	   if (!empty($res)) return $res; 
	   else return array(); 
	 }
	 else 
	 {
	  $res = $db->loadResult(); 
	  return $res; 
	 }
	
  }

 public static function getPluginData(&$cart)
 {
   
	
   $dispatcher = JDispatcher::getInstance();
   $data = array(); 
   $object = new stdClass(); 
   $object->id = ''; 
   $object->data = ''; 
   $object->where = ''; 
   $returnValues = $dispatcher->trigger('plgGetOpcData', array(&$data, &$cart, $object));   
   return $data; 
 }

 public static function getPayment(&$ref, &$OPCloader, &$num, $ajax=false, $isexpress=false)
 {
	
	 
	 if ($isexpress)
	 {
	    $reta = array(); 
		$reta['html'] = '<input type="hidden" name="virtuemart_paymentmethod_id" value="'.$ref->cart->virtuemart_paymentmethod_id.'" />'; 
		$reta['extra'] = ''; 
		return $reta;
	 }
	 
	 
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
    	$payment_not_found_text='';
		$payments_payment_rates=array();
		
		if (!class_exists('OPCrenderer'))
		require (JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		
		if (!$renderer->checkPaymentMethodsConfigured()) {


		if (method_exists($renderer, 'assignRef'))
		{
			$renderer->assignRef('paymentplugins_payments', $payments_payment_rates);
			$renderer->assignRef('found_payment_method', $found_payment_method);
		}
		}
		$p = JRequest::getVar('payment_method_id', $payment_default);
		
		if (empty($p))
		$selectedPayment = empty($ref->cart->virtuemart_paymentmethod_id) ? 0 : $ref->cart->virtuemart_paymentmethod_id;
		else $selectedPayment = $p; 
		
		// set missing fields for klarna
		OPCloader::prepareBT($ref->cart); 
		
		$dpps = array(); 
		
		$shipping = JRequest::getVar('shipping_rate_id', ''); 
		
		
		//if ($ajax)
		if (!empty($shipping))
		if (!empty($disable_payment_per_shipping))
		{
		
		$session = JFactory::getSession(); 
		$dpps = $session->get('dpps', null); 
		if (empty($dpps))
		 $OPCloader->getShipping($ref, $ref->cart, true); 
		$dpps = $session->get('dpps', null); 
		if (!empty($dpps))
		 {
		   
		 }
		}
		// 
		if (!empty($shipping))
		{
		  if (!empty($shipping))
		$ref->cart->virtuemart_shipmentmethod_id=$shipping; 
		
		$vm2015 = false; 
	$ref->cart->prices = $ref->cart->pricesUnformatted = OPCloader::getCheckoutPrices(  $ref->cart, false, $vm2015, 'opc');
		}
		$paymentplugins_payments = array();
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 
		$bhelper = new basketHelper;			
		//$bhelper->createDefaultAddress($ref, $ref->cart); 	 
		//old: 2.0.208 and prior: $returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($ref->cart, $selectedPayment, &$paymentplugins_payments));
		//plgVmDisplayListFEPaymentOPCNocache
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPaymentOPCNocache', array( &$ref->cart, $selectedPayment, &$paymentplugins_payments));
		if (empty($returnValues))
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array( $ref->cart, $selectedPayment, &$paymentplugins_payments));

		// if no payment defined
		$found_payment_method = false;
		$n = 0; 
		$debug = ''; 
		
		
		foreach ($paymentplugins_payments as $p1)
		if (is_array($p1))
		$n += count($p1);
		
		if ($n > 0) $found_payment_method = true;
		
		
		$num = $n; 
		
	
		
		
		if (!$found_payment_method) {
			$link=''; // todo
			$payment_not_found_text = OPCLang::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 
    $bhelper = new basketHelper; 
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'transform.php');
	$ret = array(); 
	if ($found_payment_method) {
		
		if (!empty($payment_inside))
		{	 
		 
		 $ret2 = OPCTransform::paymentToSelect($paymentplugins_payments, $shipping, $dpps);
		 if (!empty($ret2))
		 {
		  $ret = array($ret2['select']); 
		  $extra = $ret2['extra']; 
		  /*
		  foreach ($ret['extra'] as $key=>$val)
		   {
		     $extra[$key] = $val; 
		   }
		   */
		   
		   
		 }
		
		 
		}
		
		
		if (empty($payment_inside) || (empty($ret)))
		{
		
		$sorted = array();
		$unknown = array(); 
		
		foreach ($paymentplugins_payments as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
				
				$id = OPCTransform::getFT($paymentplugin_payment, 'input', 'virtuemart_paymentmethod_id', 'name', 'virtuemart_paymentmethod_id', '>', 'value');				
				
				if (is_array($id)) $id = reset($id); 
				
				$paymentplugin_payment = str_replace('class="vmpayment_description"', 'class="vmpayment_description vmpayment_description_'.$id.'"', $paymentplugin_payment); 
				//vmpayment_cardinfo
				$paymentplugin_payment = str_replace('class="vmpayment_cardinfo"', 'class="vmpayment_cardinfo vmpayment_cardinfo_'.$id.'"', $paymentplugin_payment); 
				//ccDetails
				$paymentplugin_payment = str_replace('class="ccDetails"', 'class="ccDetails ccDetails_'.$id.'"', $paymentplugin_payment); 
				
			 OPCloader::opcDebug('checking shipping '.$shipping); 
				 OPCloader::opcDebug('dpps:'); 
				 OPCloader::opcDebug($dpps); 
				 OPCloader::opcDebug('dpps_disable:'); 
				 OPCloader::opcDebug($dpps_disable); 
				if (!empty($shipping))
				if (!empty($dpps))
				if (!empty($disable_payment_per_shipping))
				{
				  foreach ($dpps_disable as $k=>$v)
				   {
				     if (!empty($dpps[$k]))
					 foreach ($dpps[$k] as $y=>$try)
					 {
					 
				     if ((int)$dpps[$k][$y] == (int)$shipping)
				     if ($dpps_disable[$k] == $id)
					 {
					 OPCloader::opcDebug('disabling payment id '.$id.' for shipping id '.$shipping); 
					 $paymentplugin_payment = ''; 
					 continue 3; 
					 }
					 }
				   }
				}
				// PPL Pro fix
				$paymentplugin_payment = str_replace('<br/><a href="'.JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment&Itemid=' . JRequest::getInt('Itemid'), false).'">'.JText::_('VMPAYMENT_PAYPAL_CC_ENTER_INFO').'</a>', '', $paymentplugin_payment); 
				$paymentplugin_payment = str_replace('name="virtuemart_paymentmethod_id"', 'name="virtuemart_paymentmethod_id" onclick="javascript: Onepage.runPay(\'\',\'\',op_textinclship, op_currency, 0)" ', $paymentplugin_payment); 
			    $ret[] = $paymentplugin_payment;
				
				
				if (($n === 1) && (!empty($hide_payment_if_one)))
				 {
				 
				   
				    $paymentplugin_payment = str_replace('type="radio"', 'type="hidden"', $paymentplugin_payment);  
				 }
				if (is_numeric($id))
				{
				 $ind = (int)$id;	
				 if (empty($sorted[$ind]))
				 $sorted[$ind] = $paymentplugin_payment;
				 else $unknown[] = $paymentplugin_payment;

				}
				else
				if (is_numeric($id[0]))
				{
				 $ind = (int)$id[0];	
				 if (empty($sorted[$ind]))
				 $sorted[$ind] = $paymentplugin_payment;
				 else $unknown[] = $paymentplugin_payment;
				}
				else $unknown[] = $paymentplugin_payment;
			
				
			}
		    }
		}
		
		
		if (!empty($sorted))
		{
		 $dbj = JFactory::getDBO(); 
		 $dbj->setQuery("select * from #__virtuemart_paymentmethods where published = '1' order by ordering asc limit 999"); 
		 $list = $dbj->loadAssocList(); 
		 $msg = $dbj->getErrorMsg(); 
		 if (!empty($msg)) { echo $msg;  }
		 $sortedfinal = array(); 
		 if (!empty($list))
		 {
		 foreach ($list as $pme)
		  {
		    if (!empty($sorted[$pme['virtuemart_paymentmethod_id']]))
			$sortedfinal[] = $sorted[$pme['virtuemart_paymentmethod_id']];
		
		  }
		  if (empty($unknown)) $unknown = array(); 
		  if (!empty($sortedfinal))	  
		  $ret = array_merge($sortedfinal, $unknown); 
		  }
		  }
    
		  }
    } else {
	 $ret[] = $payment_not_found_text.'<input type="hidden" name="virtuemart_paymentmethod_id" id="opc_missing_payment" value="0" />';
    }
	
	   $vars = array('payments' => $ret, 
				 'cart'=> $ref->cart, );
				
	   $html = $OPCloader->fetch($OPCloader, 'list_payment_methods.tpl', $vars); 
	   
 $pid = JRequest::getVar('payment_method_id', ''); 
 if (!empty($pid) && (is_numeric($pid)))
 {
 if (strpos($html, 'value="'.$pid.'"')!==false)
 {
 
	$html = str_replace('checked="checked"', '', $html); 
	$html = str_replace(' checked', ' ', $html); 
	$html = str_replace('value="'.$pid.'"', 'value="'.$pid.'" checked="checked" ', $html); 
 }
 
 }
 else
 if (strpos($html, 'value="'.$payment_default.'"')!==false)
 {
	$html = str_replace('checked="checked"', '', $html); 
	$html = str_replace(' checked', ' ', $html); 
	$html = str_replace('value="'.$payment_default.'"', 'value="'.$payment_default.'" checked="checked" ', $html); 
 }
 
 $html = str_replace('"radio"', '"radio" autocomplete="off" ', $html); 
 
 if (!$payment_inside)
 if (strpos($html, 'checked')===false) 
 {
   
	$x1 = strpos($html, 'name="virtuemart_paymentmethod_id"');
	if ($x1 !== false)
	 {
	    $html = substr($html, 0, $x1).' checked="checked" '.substr($html, $x1); 
	 }
	 else
	 {
	    // we've got no method here !
	 }
	  
 }
 // klarna compatibility
 $count = 0; 
 $html = str_replace('name="klarna_paymentmethod"', 'name="klarna_paymentmethod_opc"', $html, $count);
 
 $html .= '<input type="hidden" name="opc_payment_method_id" id="opc_payment_method_id" value="" />';  
 if ($count>0)
 if (!defined('klarna_opc_id'))
 {
 $html .= '<input type="hidden" name="klarna_opc_method" id="klarna_opc_method" value="" />'; 
 
 
 define('klarna_opc_id', 1); 
 }
   
   $reta = array(); 
   $reta['html'] = $html; 
   if (!empty($extra)) $reta['extra'] = $extra; 
   else $reta['extra'] = ''; 
   return $reta;
 }

 
  
}