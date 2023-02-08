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

// load OPC loader
//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

defined('_JEXEC') or die('Restricted access');

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 
    
	
class OPCTransform {
 
	
public static function getUnclosedTag($tag, $html, $prop="")
 {
	 // max 10 tries
	 for ($xstart=0; $xstart<strlen($html); $xstart++)
	 {
	 $x1 = stripos($html, '<'.$tag, $xstart); 
	 $x2 = stripos($html, '>', $x1); 
	 if ($x1 !== false)
	 if ($x2 !== false)
	 {
		 if (!empty($prop))
		 {
		 $x3 = stripos($html, $prop, $x1+1); 
		 if ($x3 < $x2)
		 {
			 // found match
			 $html = substr($html, $x1, $x2-$x1+1); 
			 return $html; 
		 }
		 else 
		 {
		 $xstart = $x3; 
		 continue; 
		 }
		 }
		 else
		 {
			 $html = substr($html, $x1, $x2-$x1+1); 
			 return $html; 
		 }
		 
		 
		 return ''; 
	 }
    }
	 return ''; 
	 
 }
 public static function getInnerTag($tag, $html, $which=0)
 {
	 $posa = basketHelper::strposall($html, '<'.$tag); 
	 if (empty($posa)) return ""; 
	 if (!empty($posa[$which]))
	 $x1 = $posa[$which]; //stripos($html, '<'.$tag); 
     else
	 $x1 = stripos($html, '<'.$tag); 
	 $x2 = stripos($html, '</'.$tag, $x1); 
	 if ($x1 !== false)
	 if ($x2 !== false)
	 {
		 $x3 = stripos($html, '>', $x1+1); 
		 $html = substr($html, $x3+1, $x2-($x3+1)); 
		 
		 return $html; 
	 }
	 
	 
 }
 
 public function getOverride($layout_name, $name, $psType, &$ref, &$method='', &$htmlIn=array())
	{
		static $theme; 
		if (empty($theme))
		{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		$theme = $selected_template; 
		}
		
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php'))
		 {
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.$theme.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		   $isset = true; 
		 }
		 else
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php'))
		 {
		  		   $isset = true; 
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.$psType.DS.$name.DS.$layout_name.'.php';
		 }
		 
		  
		 if (!empty($layout)) 
		 {
		  include($layout); 
		  return $htmlIn; 
		 }
		 return $htmlIn; 
		
		
	}
 
 
 public static function overridePaymentHtmlBefore(&$html, $cart, $vm_id=0, $name, $type)
 {
 
 
 
   jimport('joomla.filesystem.file');
   $name = JFile::makeSafe($name); 
   $type = JFile::makeSafe($type); 
   if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'before_render'.'.php'))
     {
	    include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'before_render'.'.php'); 
	 }
   
 }
 
 public static function overridePaymentHtml(&$html, $cart, $vm_id=0, $name, $type)
 {
   jimport('joomla.filesystem.file');
   $name = JFile::makeSafe($name); 
   $type = JFile::makeSafe($type); 
   if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'after_render'.'.php'))
     {
	    include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'payment'.DS.$name.DS.'after_render'.'.php'); 
	 }
   
 }
 
 public static function overrideShippingHtml(&$html, $cart, $vm_id=0)
 {
    //include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_shipping_html.php'); 
	if (empty($vm_id))
	{
	 $vm_id = OPCTransform::getFT($html, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'value');
	 $vm_id = $vm_id[0]; 
	}
	OPCloader::getPluginMethods(); 
	jimport('joomla.filesystem.file');
	if (!isset(OPCloader::$methods['shipment'][$vm_id])) return $html;
	$name = OPCloader::$methods['shipment'][$vm_id]['shipment_element']; 
	$name = JFile::makeSafe($name);
	if (empty($name)) return ''; 
	
    if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.$name.DS.'html.php'))
	{
	include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.$name.DS.'html.php'); 
	
	}
	
	return $html; 
 }
 
 public static function shippingToSelect($htmla, &$num, &$cart)
 {
	 //$extrainside = ''; 
	 $options = '';
	 $extra = array(); 
	 // this will always be rendered inside the checkout form: 
	 
	 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		if (!class_exists('CurrencyDisplay'))	
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);	 		
		
			foreach ($htmla as $shipment_html) {
				
				//$id =  self::getFT($paymentplugin_payment, 'input', 'virtuemart_shipmentmethod_id', 'name', 'virtuemart_shipmentmethod_id', '>', 'value');				
				$id =  self::getFT($shipment_html, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'id');
				$value =  self::getFT($shipment_html, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'value');
				if (empty($id))
				{
					
					continue; 
				}
				foreach ($id as $k=>$multi)
				{
				$newoptions = ''; 
				include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'third_party'.DS.'third_party_shipping_options.php'); 
				if (!empty($newoptions)) 
				{
				$options .= $newoptions; 
				
				continue; 
				}
				
				
				$html = self::getInnerTag('label', $shipment_html, $k); 
				$x = str_replace($html, '', $shipment_html); 
				$t1 = strip_tags($x); 
				$t2 = trim($t1); 
				$hasextra = false; 
				if (!empty($t2))
				{
				  $x2 = self::getUnclosedTag('input', $x, 'virtuemart_shipmentmethod_id'); 
				  // remove the payment input
				  $x = str_replace($x2, '', $x); 
				  
				  
				  $extra[$id[$k]] = '<div class="shipmennt_extra" style="display: none;" id="extra_shipment_'.$id[$k].'">'.$x.'</div>'; 					
				  $hasextra = true; 
				}
				else
				$extra[$id[0]] = ''; 
			
				$html = strip_tags($html); 
				
				$options .= '<option value="'.$value[$k].'" id="'.$id[$k].'"'; 
				if ($hasextra)
					$options .= ' rel="'.$id[$k].'" '; 
				$html = trim($html); 
				$options .= '>'.$html.'</option>'; 
				$num++; 
				}
				
				
			
			}
	include(JPATH_SITE.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php");
	if (!empty($shipping_inside_choose))
	{
	 $options = '<option value="choose_shipping" id="shipment_id_0">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'.$options; 
	}
	$select = '<select autocomplete="off" id="opcShippingSelect" class="opcShippingSelect" onchange="javascript:Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);" name="virtuemart_shipmentmethod_id">'.$options.'</select>';
	//$extra['-1'] = $select; 
	
	$html = $select; 
	foreach ($extra as $l)
		$html .= $l; 
	
	
	
	return $html; 
			
 

 }
 public static function paymentToSelect($htmla, $shipping, $dpps)
 {
	 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 
	  $pid = JRequest::getVar('payment_method_id', $payment_default);
	 
	 $options = '';
	 $extra = array(); 
	 
	 		foreach ($htmla as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
				
				$id =  self::getFT($paymentplugin_payment, 'input', 'virtuemart_paymentmethod_id', 'name', 'virtuemart_paymentmethod_id', '>', 'value');	
				
				 OPCloader::opcDebug('checking shipping '.$shipping); 
				 OPCloader::opcDebug('dpps:'); 
				 OPCloader::opcDebug($dpps); 
				 OPCloader::opcDebug('dpps_disable:'); 
				 OPCloader::opcDebug($dpps_disable); 
					if (!empty($shipping))
				if (!empty($dpps))
				if (!empty($disable_payment_per_shipping))
				{
				  $idp = $id[0]; 
				  foreach ($dpps_disable as $k=>$v)
				   {
				     if (!empty($dpps[$k]))
					 foreach ($dpps[$k] as $y=>$try)
					 {
					 
				     if ((int)$dpps[$k][$y] == (int)$shipping)
					 {
					 OPCloader::opcDebug('found shipping '.$dpps[$k][$y].' testing payment id'.$idp); 
				     if ($dpps_disable[$k] == $idp)
					 {
					 OPCloader::opcDebug('disabling payment id '.$idp.' for shipping id '.$shipping); 
					 $paymentplugin_payment = ''; 
					 continue 3; 
					 }
					 }
					 }
				   }
				}
				
				$html = self::getInnerTag('label', $paymentplugin_payment); 
				
				// remove description: 
				$xd = stripos($html, '<span class="vmpayment_description">'); 
				if ($xd!==false)
				{
				
				$s1 = stripos($html, '</span>', $xd); 
				$len = strlen('</span>');
				$html = substr($html, 0, $xd).substr($html, $s1+$len); 
				 
				}
				$x = str_replace($html, '', $paymentplugin_payment); 
				$t1 = strip_tags($x); 
				$t2 = trim($t1); 
				$hasextra = false; 
				if (!empty($t2))
				{
				  $x2 = self::getUnclosedTag('input', $x, 'virtuemart_paymentmethod_id'); 
				  // remove the payment input
				  $x = str_replace($x2, '', $x); 
				  
				  
				  $extra[$id[0]] = '<div class="payment_extra" style="display: none;" id="extra_payment_'.$id[0].'">'.$x.'</div>'; 					
				  $hasextra = true; 
				}
				else
				$extra[$id[0]] = ''; 
			
				$html = strip_tags($html); 
				$options .= '<option value="'.$id[0].'"'; 
				
				if ($id[0]==$pid)
					$options .= ' selected="selected" '; 
				
				if ($hasextra)
					$options .= ' rel="'.$id[0].'" '; 
				$options .= '>'.$html.'</option>'; 
				
				
				
			}
			}
			}
	$select = '<select autocomplete="off" id="opcPaymentSelect" class="opcPaymentSelect" onchange="javascript: Onepage.runPaySelect(this);" name="virtuemart_paymentmethod_id">'.$options.'</select>';
	//$extra['-1'] = $select; 
	$a2 = array(); 
	$a2['extra'] = ''; 
	$a2['select'] = $select;
    /*	
	foreach ($extra as $key=>$l)
		$a2['extra'] .= $l; 
	*/
	
	
	if (empty($extra)) $extra = array(); 
	$a2['extra'] = $extra; 
	
	return $a2; 
			
 }
 // html = <input type="radio" value="123" name="myname" id="myid" />
// tagname = input
// mustIncl = myname
// mystProp = type
// mustVal = 123
// getProp = id
function getFT($html, $tagname, $mustIncl='', $mustProp='', $mustVal='', $ending='>', $getProp)
{
  $posa = basketHelper::strposall($html, $mustIncl); 
  $rev = strrev($html); 
  $len = strlen($html); 
  $ret = array(); 

//if ($mustIncl == 'usps_id_1')
{
   // $x = htmlentities($html); 
  
  
}
  
  if (!empty($posa))
  foreach ($posa as $x1)
  {
   $x2 = stripos($rev, strrev('<'.$tagname), $len-$x1); 
   $x2 = $len - $x2 - strlen('<'.$tagname) + 1; 
   
   if ($x2 < $x1)
   {
     
     
	 
	 // here we can search for /> or just > depending on what we need... 
	 $x3 = stripos($html, $ending, $x2); 
	 if ($x3 === false) continue; 
	 
	 // our search tag starts at $x2 and ends at $x3
	 $temp = substr($html, $x2, $x3-$x2); 
	
	

	 if (!empty($mustProp))
	  {
	     
	  	 $val = self::getValFT($temp, $mustProp); 
		 if ($val === false) continue; 
		 if (!empty($mustVal))
		 if ($val != $mustVal) continue; 

	  }
	  
	  $val = self::getValFT($temp, $getProp); 
	  if ($val !== false) 
	  {
	
	  $ret[] = $val; 
	  continue;
	  }
	  
	  
   }
   else
   continue;
  }
  if (empty($ret)) return false; 
  return $ret; 
  
}
function getFTArray($html, $tagname, $mustProp, $mustVal)
{
}
// search value of a prop in temp
function getValFT($temp, $mustProp)
{
     // example data-usps='{"service":"Parcel Post","rate":15.09}'
	 // or id="xyz"
	    if (substr($mustProp, strlen($mustProp)-1)=='*')
		{
		$sb = substr($mustProp, 0, -1); 
		
		$x51 = stripos($temp, $sb);
		if ($x51 === false) return false;
		$x5 = stripos($temp, '=', $x51); 
		}
		else
	    $x5 = stripos($temp, $mustProp.'=');
		
	    if ($x5===false) return false; 
		
		$single = false;
		
		 
		   $x4 = stripos($temp, '"', $x5);
		   $x42 = stripos($temp, "'", $x5);
		   
		   if (($x42 !== false) && ($x4 !== false))
		   if ($x42 < $x4)
		   {
		    // we will start with ' instead of "
			$x4 = $x42; 
			$single = true; 
		   }
		   
		   // search for start and end by '
		   if ($single) 
		    {
			//$x4 = stripos($temp, "'", $x5);
			if ($x4 !== false)
			{
			  //$single = true; 
			  if ($single)
			  $x6e = basketHelper::strposall($temp, "'", $x4+1);
			  else $x6e = basketHelper::strposall($temp, '"', $x4+1);
			  
			  if (!empty($x6e))
			  foreach ($x6e as $x6test)
			   {
			     if (substr($temp, $x6test-1, 1)!=urldecode('%5C'))
				 {
				 $x6 = $x6test; 
				 break; 
				 }
			   }
			  //$x6 = stripos($temp, "'", $x4+1);
			}
			}
		   
		   if ($x4 === false) return ""; 
		   
		   // search for end by " 
		   if (!$single)
		   if (!isset($x6))
		   {
		     $x6e = basketHelper::strposall($temp, '"', $x4+1);
			  foreach ($x6e as $x6test)
			   {
			     if (substr($temp, $x6test-1, 1)!=urldecode('%5C'))
				 {
				 $x6 = $x6test; 
				 break; 
				 }
			   }
		     //$x6 = stripos($temp, '"', $x4+1);
		   }
		   if (!isset($x6)) 
		   {
		     return "";
		     echo $mustProp.' in: '.$temp.' '.$x4; 
		   }
		   if ($x6 === false) return ""; 
		   
		   $val = substr($temp, $x4+1, $x6-$x4-1); 
		   
		   return $val; 
		   
		 
	  
	  return false; 
}

// inserts an object or array $ins after/before name $field in $arr
 public static function insertAfter(&$arr, $field, $ins, $newkey, $before=false)
  {
    $new = array(); 
	foreach ($arr as $key=>$val)
	 {
	   if ($key == $field)
	   {
	   if ($before) $new[$newkey] = $ins; 
	   else { 
	     $new[$key] = $val; 
		 $new[$newkey] = $ins; 
	   }
	   }
	   else
	   {
	    $new[$key] = $val; 
	   }
	   
	 }
	 $arr = $new;
	 
	 
  }



}