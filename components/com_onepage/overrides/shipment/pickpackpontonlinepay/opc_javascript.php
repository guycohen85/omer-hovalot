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

$js = '
//<![CDATA[  
if (typeof jelolo == "undefined")
function jelolo() {
	var e=document.getElementById("valasztott_ppk_ki");
	var d=document.getElementById("valasztott_ppk");
	var c=document.chooseShipmentRate;
	var a=document.getElementById("shipping_rate_ppk");
	var b=document.getElementById("shipment_id_'.$method->virtuemart_shipmentmethod_id.'");
	if(a.value!="0")
	{
	b.value="'.$method->virtuemart_shipmentmethod_id.'";
	e.value=a.value;
	b.checked=true
	}
	else
	{
	b.checked=false;
	}
	return true
	}
	function jelolo2()
	{
	var e=document.getElementById("valasztott_ppk_ki");
	var d=document.getElementById("valasztott_ppk");
	var b=document.getElementById("shipment_id_'.$method->virtuemart_shipmentmethod_id.'");
	var a=document.getElementById("shipping_rate_ppk");
	if(b.checked){
	a.options[0].selected="1";b.value="'.$method->virtuemart_shipmentmethod_id.'";e.value=a.value;
	}
	return true
	};
//]]> 
'; 
JFactory::getDocument()->addScriptDeclaration($js);