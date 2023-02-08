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
* loaded from: \components\com_onepage\controllers\opc.php
* function runExt()
* 
*/
defined('_JEXEC') or die('Restricted access');
// support for USPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	
	  // pickup or free
	 if (stripos($shipping_method, 'pickup_or_free')!==false)
	 {
	   //$dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	   	 if (!empty($data))
	   {
	    $data = @base64_decode($data);  
		$data = @json_decode($data); 
		
		
		foreach ($data as $key=>$val)
		 {
		   if (strpos($key, 'service')!==false)
		   JRequest::setVar($key, $val); 
		   //echo $key.' '.$val;    
		 }
	   }
	   
	   /*
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('service', (string)$data['service']);
			 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
		*/
	}
	
	
	if (stripos($shipping_method, 'zasilkovna_')!==false)
	 {
	 
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	   if (!empty($data))
	   {
	    $data = @base64_decode($data);  
		$data = @json_decode($data); 
		
		
		foreach ($data as $key=>$val)
		 {
		   if (strpos($key, 'branch_')!==false)
		   JRequest::setVar($key, $val); 
		   //echo $key.' '.$val;    
		 }
	   }
	   
	 }
	 else
	if (stripos($shipping_method, 'usps_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		   
		  if (!empty($data))
		   {
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			 $uid = $cart->virtuemart_shipmentmethod_id; 
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			 JRequest::setVar('usps_rate-'.$uid, (float)$data['rate']); 
			 $service = base64_decode($data['service']); 
			 $service = html_entity_decode($service, ENT_COMPAT, 'UTF-8'); 
			 $service = base64_encode($service); 
			 JRequest::setVar('usps_service', (string)$service);
			 JRequest::setVar('usps_name-'.$uid, (string)$service);
			 
			  $session = JFactory::getSession();
			$sessionUspsData = new stdClass();

			$sessionUspsData->_usps_id = $uid; 
			$sessionUspsData->_usps_name = $service;
			$sessionUspsData->_usps_rate = (float)$data['rate']; 
			$sessionUsps = $session->set('usps', serialize($sessionUspsData), 'vm');
			 
			 
		   }
		}
	 }
	 // end support USPS
	 	// support for UPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	if (stripos($shipping_method, 'ups_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  //{"id":"03","code3":"USD","rate":8.58,"GuaranteedDaysToDelivery":[]}
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('ups_name', (string)$data['service']); 
			 JRequest::setVar('ups_rate', $data['id']);
			 //JRequest::setVar('virtuemart_ups_rate', $data['id']); 
			 JRequest::setVar('ups_rate-'.$cart->virtuemart_shipmentmethod_id, $data['id']); 
			 
			 
			 $session = JFactory::getSession();
			 $sessionUps = $session->get('ups_rates', 0, 'vm');

			if (!empty($sessionUps)) {
				$ups_rates = json_decode($sessionUps, TRUE);
				
		    }
			
			$ups_rates[$data['id']]['id'] = $data['id'];
			$ups_rates[$data['id']]['code3'] = $data['code3'];
			$ups_rates[$data['id']]['rate'] = $data['rate'];
			$ups_rates[$data['id']]['GuaranteedDaysToDelivery'] = $data['GuaranteedDaysToDelivery'];
			$session->set('ups_rates', json_encode($ups_rates), 'vm');
			
			
			 
			 
		   }
		}
	 }
	 // end support UPS
	  
	// support for ACS: by Dmitry Vadis <dmvadis@gmail.com / info@cmscript.net>
	if (stripos($shipping_method, 'acs_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  //{"id":"03","code3":"USD","rate":8.58,"GuaranteedDaysToDelivery":[]}
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('acs_name', (string)$data['service']); 
			 JRequest::setVar('acs_rate', $data['id']);
			
			 
			 
			 
		   }
		}
	 }
	 // end support ACS
	 
	 
	 // skipcart is not compatible, therefore don't use it: 
	 // $plugin =& JPluginHelper::getPlugin( 'system', 'vmskipcart' );
	 
	if (stripos($shipping_method, 'cpsol_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  //{"id":"03","code3":"USD","rate":8.58,"GuaranteedDaysToDelivery":[]}
		   
		   if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('cpsol_name', (string)$data['name']);
			 JRequest::setVar('cpsol_rate', (string)$data['rate']);
			 JRequest::setVar('cpsol_shippingDate', (string)$data['shippingDate']);
			 JRequest::setVar('cpsol_deliveryDate', (string)$data['deliveryDate']);
			 JRequest::setVar('cpsol_zero_rate', (string)$data['zeroRate']);
			 JRequest::setVar('cpsol_radio', $data['cpsol_radio']); 
			 //$html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }
	 
	 // fedex
	 if (stripos($shipping_method, 'fedex_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  //{"id":"03","code3":"USD","rate":8.58,"GuaranteedDaysToDelivery":[]}
		   
		   if (!empty($data))
		   {
			  JRequest::setVar('fedex_rate', (string)$data['id']);
		   }
		}
	 }
	 // end fedex
	 
	 $session = JFactory::getSession();
	 $session->set('vmcart_redirect', true,'vmcart_redirect');
	 
	 //fedex multi box support: 
	 unset($_SESSION['load_fedex_prices_from_session']); 
	 
	 if (false)
	 {
	 if (!empty($_SESSION['load_fedex_prices_from_session']))
	 if (!empty($_SESSION['fedex_shipping_options']))
	  {
	    $sid = JRequest::getVar('virtuemart_shipmentmethod_id', ''); 
	    if (!empty($_SESSION['fedex_shipping_options'][$sid]))
		if (!isset($_SESSION['fedex_shipping_options'][$sid]['amount']))
		 {
		    $base = $_SESSION['fedex_shipping_options'][$sid]['base'];
			$handling = 0; //$_SESSION['fedex_shipping_options'][$sid]['handling'];
			
			$amount = (float)$base + (float)$handling; 
			$_SESSION['fedex_shipping_options'][$sid]['amount'] = $amount; 
		 }
	  }
	 }
	
	$klarna = JRequest::getVar('opc_payment_method_id', 0); 
	
	if (!empty($klarna))
	 {
	   JRequest::setVar('klarna_paymentmethod', $klarna); 
	 }
	 
	 $id = JRequest::getVar('branch_ppp', ''); 
	 if (!empty($id))
	 {
	  
	   $value = JRequest::getVar('branch_data_'.$id); 
	   
	   JRequest::setVar('shipmentPoint', $value); 
	   $_POST['shipmentPoint'] = $value;
	   $cart->lists['shipmentPoint'] = $value; 
	  $xmlfile = JPATH_CACHE . DS . 'validboltlista.xml';
	  $_SESSION['vm_ppp_xml'] = $xmlfile;
	 
	 }

	 
	 //runExt name="ebrinstalments"
	  $session = JFactory::getSession();
	  $eid = JRequest::getVar('ebrinstalments', null); 
	  if (!is_null($eid))
	  {
	  
	  //object(stdClass)#804 (2) { ["ebrinstalments"]=> string(1) "0" ["ebrinstalmentsamount"]=> string(3) "421" } 
	   $obj = new stdClass(); 
	   $obj->ebrinstalments = $eid; 
	   $s = serialize($obj); 
	   $b = $session->set('eurobnk', $s, 'vm'); 
	  }