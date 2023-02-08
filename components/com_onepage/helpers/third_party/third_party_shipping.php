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
defined('_JEXEC') or die('Restricted access');

$jconfig = JFactory::getConfig(); 
if (method_exists($jconfig, 'get'))
if ($jconfig->get('error_reporting') == 'none')
 {
    
    error_reporting(0); 
 }

// support for USPS: 
	if (stripos($shipping_method, 'usps_')!==false)
	 {
	   $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-usps');
	   
	  
	    if (empty($usps_saved_semafor))
	 {
	  $usps_saved = $session->get('usps', null, 'vm');
	  $usps_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('usps', $usps_saved, 'vm'); 
	 }
	   
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
			   
			   $uid = $cart->virtuemart_shipmentmethod_id; 
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			 JRequest::setVar('usps_rate-'.$uid, (float)$data['rate']); 
			 $service = base64_decode($data['service']); 
			 $service = html_entity_decode($service, ENT_COMPAT, 'UTF-8'); 
			 $service = base64_encode($service); 
			 JRequest::setVar('usps_service', (string)$service);
			 JRequest::setVar('usps_name-'.$uid, (string)$service);
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
			 
			 
		   }
		   else
		   {
			   
		   }
		}
		else
		{
			
		}
			
	 }
	 // end support USPS
	 
	 
	 
	 // support for UPS: 
	if (stripos($shipping_method, 'ups_')!==false)
	 {
	    if (empty($ups_saved_semafor))
	 {
	  $ups_saved = $session->get('ups_rates', null, 'vm');
	  $ups_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('ups_rates', $ups_saved, 'vm'); 
	 }
	 unset($_SESSION['load_fedex_prices_from_session']); 
	   $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-ups');
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('ups_rate', (string)$data['id']);
			 JRequest::setVar('ups_rate-'.$cart->virtuemart_shipmentmethod_id, $data['id']); 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }
	 // end support UPS
	
	// pickup or delivery opc plugin: 
	
	 if (stripos($shipping_method, 'data-pickup=')!==false)
	 {
	  $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	  
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
	}
	// pickup or delivery opc plugin end
	// canada post: 
	
	 // canada post: 
	 if (stripos($shipping_method, 'cpsol_')!==false)
	 {
	 
	  if (empty($cpsol_saved_semafor))
	 {
	  $cpsol_saved = $session->get('cpsol_service', null, 'vm');
	  $cpsol_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('cpsol_service', $cpsol_saved, 'vm'); 
	 }
	 
	 
	 /*
	 <input type="radio" name="cpsol_radio" class="js-change-cpsol" data-cpsol="{&quot;name&quot;:&quot;Regular&quot;,&quot;rate&quot;:13.3,&quot;shippingDate&quot;:&quot;2014-06-20&quot;,&quot;deliveryDate&quot;:&quot;2014-06-24&quot;,&quot;deliveryDayOfWeek&quot;:&quot;3&quot;,&quot;nextDayAM&quot;:&quot;false&quot;,&quot;packingID&quot;:&quot;P_0&quot;,&quot;zeroRate&quot;:0}" id="cpsol_id_0" value="0">
	 */

 $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-cpsol');
 $value = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'value');	  

 $vmvalue = OPCTransform::getFT($shipping_method, 'input', 'virtuemart_shipmentmethod_id', 'type', 'hidden', '>', 'value');	  
 
 $vm_id = $vmvalue[0]; 
 
 
 
if (stripos($shipping_method, 'cpsol_radio')!==false)
{
  $shipping_method = str_replace(' checked="checked"', ' ', $shipping_method); 
  //virtuemart_shipmentmethod_id
  if (!defined('only_once_cp'))
  {
  $shipping_method = str_replace('virtuemart_shipmentmethod_id"', 'virtuemart_shipmentmethod_id_old"', $shipping_method); 
  define('only_once_cp', 1); 
  $shipping_method .= '<div style="display: none;" class="hidden_radio"><input type="radio" multielement="cpsol_radio" name="virtuemart_shipmentmethod_id" value="'.$vm_id.'" id="shipment_id_'.$vm_id.'" /></div>'; 
  }
  
  /*
  $js = '
<script type="text/javascript">  
//<![CDATA[
  function selectCspol()
   {
      var d = document.getElementById(\'shipment_id_'.$vm_id.'\'); 
	  if (jQuery != \'undefined\') jQuery(\'shipment_id_'.$vm_id.'\').click(); 
	  else
	  d.onclick(); 
   }
//]]>
</script>
  ';
*/  
  $shipping_method = str_replace('name="cpsol_radio"', 'name="cpsol_radio" onclick="selectCspol('.(int)$vm_id.');"', $shipping_method); 
  //$shipping_method = str_replace('cpsol_radio', 'virtuemart_shipmentmethod_id', $shipping_method); 
  /*
  foreach ($value as $cs_val)
  {
   $shipping_method = str_replace('id="cpsol_id_' . $cs_val . '"   value="' . $cs_val . '"', 
   'id="cpsol_id_' . $cs_val . '"   value="'.$vm_id.'"', $shipping_method);
  }
  */
  
}


	 

	   if (!empty($dataa))
	    {

		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		   $rate_id = str_replace('cpsol_id_', '', $shipmentid); 
		   
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('cpsol_name', (string)$data['name']);
			 JRequest::setVar('cpsol_rate', (string)$data['rate']);
			 JRequest::setVar('cpsol_shippingDate', (string)$data['shippingDate']);
			 JRequest::setVar('cpsol_deliveryDate', (string)$data['deliveryDate']);
			 JRequest::setVar('cpsol_zero_rate', (string)$data['zeroRate']);
			 JRequest::setVar('cpsol_radio', $rate_id); 
			 
			 $data['cpsol_radio'] = $rate_id; 
			 $newdata = json_encode($data); 
			 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($newdata).'"/>';
			 
			 
		   }
		}
	
		
	}
	
	
	 // support for ACS
	if (stripos($shipping_method, 'acs_')!==false)
	 {
	    if (empty($acs_saved_semafor))
	 {
	  $acs_saved = $session->get('acs_rates', null, 'vm');
	  $acs_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('acs_rates', $acs_saved, 'vm'); 
	 }
	 
	 unset($_SESSION['load_fedex_prices_from_session']); 
	   $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-acs');
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('acs_rate', (string)$data['id']);
			
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }
	 // end support ACS
	
	 // canada post: 
	 if (stripos($shipping_method, 'fedex_id')!==false)
	 {
	 $session = JFactory::getSession();
	 if (false)
	 if (empty($fedex_saved_semafor))
	 {
	  $fedex_saved = $session->get('fedex_rates', null, 'vm');
	  $fedex_saved_semafor = true; 
	 }
	 else
	 {
	    $session->set('fedex_rates', $fedex_saved , 'vm');
	 }
	 
	  $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	  
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('fedex_rate', (string)$data['id']);
			 //JRequest::setVar('cpsol_rate', (string)$data['rate']);
			 //JRequest::setVar('cpsol_shippingDate', (string)$data['shippingDate']);
			 //JRequest::setVar('cpsol_deliveryDate', (string)$data['deliveryDate']);
			 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	}
	 
	 $fedex_multi = $session->get("shipping_services", ''); 
	 $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'value');
	 if (!empty($fedex_multi))
	 if (!empty($id) && (strpos($id, ':')!==false))
	 {
	
	 $fi = explode(':', $id); 
	 foreach ($fedex_multi as $key=>$fedex_rate)
	 {
		 $fedex_multi[$key]['baseRequest']['selected'] = $fi[1]; 
		 
	
	 }
	 $session->set('shipping_services', $fedex_multi); 
	
	 }
	 
	 
	 
	 $dispatcher = JDispatcher::getInstance(); 
	 //$shipmentid: 
	 // 	 -> is a from <input type="radio" id="myid1" ...
	 // 	 -> is a from <input type="radio" id="myid2" ...
	 // OR
	 // when using select inside shipping and it is marked with
	 // <option ismulti="true" multi_id="shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$ppp->id.'" 
	 // full multi_id is sent here
	 // to parse additional details such as json, you can use: 
	 // $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	 // where data-json=\''.json_encode... within your html as defined: 
	 // function getFT($html, $tagname, $mustIncl='', $mustProp='', $mustVal='', $ending='>', $getProp)
	 
	 // cart -> virtuemart cart with currently calculated shipping AND payment (will itenerate all payments as well timex number of multi shipping)
	 // shipping_method -> is the html of the current shipping method including all of the multi shipments -> may get transofrmed into options by opc if set up
	 // id -> virtuemart shipment ID
	 // $html -> it is the output html that will always be rendred inside the main checkout form
	 $results = $dispatcher->trigger('setOPCbeforeSelect', array( $cart, $shipmentid, $shipping_method, $id, &$html )); 
	 
	 // your plugin function should use JRequest::set
	 // which you probably will get when calling 
	 // plgVmOnSelectCheckShipment
	 // or by calculation, etc... 
	 
	 
	 
	 