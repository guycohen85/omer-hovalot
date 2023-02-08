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

class OPCCommonHtml {

 public static function getStateHtmlOptions(&$cart, $country, $type='BT')
 {
    	
    //require_once(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'state.php'); 

    $states = array(); 	
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	$stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	//$html = '<div style="display: none;"><form>'; 
	$states = $stateModel->getStates( $country, true, true );
	$ret = '<option value="none">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'; 
	
	$cs = '';  	
	if (!is_array($cart->$type)) $cs = ''; 
	else
    if ((!empty($cart->{$type}))) 
	if (is_array($cart->{$type})) 
	if (isset($cart->{$type}["virtuemart_state_id"])) 
	if (!empty($cart->{$type}["virtuemart_state_id"] ))
    $cs = $cart->{$type}['virtuemart_state_id']; 	
	
	foreach ($states as $k=>$v)
	 {
	     
	    $ret .= '<option ';
		if ($v->virtuemart_state_id == $cs) $ret .= ' selected="selected" '; 
		$ret .= ' value="'.$v->virtuemart_state_id.'">'.$v->state_name.'</option>'; 
	 }
	 
	 return $ret; 

 }
 
 public static function getStateList(&$ref)
 {
   
	  /*
    if (!class_exists('VirtueMartModelState'))
    require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'state.php'); 
    if (!class_exists('VirtueMartModelCountry'))
	require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'country.php'); 
      */
	require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	$countryModel = OPCmini::getModel('country'); //new VirtueMartModelCountry(); 
	$list = $countryModel->getCountries(true, true, false); 
	$countries = array();
    $states = array(); 	
	$stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	
	// if state is set in BT, let's make it default
	if (!empty($ref->cart->BT) && (!empty($ref->cart->BT['virtuemart_state_id'])))
	$cs = $ref->cart->BT['virtuemart_state_id']; 
	else $cs = '';  
	
	$html = '<div style="display: none;">'; 
	foreach ($list as $c)
	{
	  $states[$c->virtuemart_country_id] = $stateModel->getStates( $c->virtuemart_country_id, true, true );
	  unset($state); 
		//$html .= '<input type="hidden" name="opc_state_list" id="state_for_'.$c->virtuemart_country_id.'" value="" />'; 	  
	  if (!empty($states[$c->virtuemart_country_id])) 
	  {
	  $html .= '<select id="state_for_'.$c->virtuemart_country_id.'">'; 
	  $html .= '<option value="">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'; 
	  foreach ($states[$c->virtuemart_country_id] as $state)
	   {
	     $html .= '<option ';
		 if ($state->virtuemart_state_id == $cs) $html .= ' selected="selected" '; 
		 $html .= ' value="'.$state->virtuemart_state_id.'">'.$state->state_name.'</option>'; 
	   }
	  $html .= '</select>';
	  }
	  // debug

	  
	  
	}
	$html .= '</div>'; 
	return $html; 
 }
public static function getExtras(&$ref)
{
   $html = OPCCommonHtml::getStateList($ref); 
  //test ie8: 
  //$html = ''; 
  if (!empty(OPCloader::$extrahtml)) $html .= OPCloader::$extrahtml; 
  $html .= '<div id="opc_totals_hash">&nbsp;</div>'; 	
  $html = '<form action="#" name="hidden_form">'.$html.'<div style="display: none;"><input type="text" name="fool" value="1" required="required" class="required hasTip" title="fool::fool" /></div></form>'; 
  return $html;
}

 function getFormVars(&$ref)
 {
     
   if (!isset(OPCloader::$inform_html)) OPCloader::$inform_html = array(); 
   $ih = implode('', OPCloader::$inform_html); 
   $html = '<input type="hidden" value="com_virtuemart" name="option" id="opc_option" />
		<input type="hidden" value="checkout" name="task" id="opc_task" />
		<input type="hidden" value="opc" name="view" id="opc_view" />
		<input type="hidden" value="1" name="nosef" id="nosef" />
		<input type="hidden" name="saved_shipping_id" id="saved_shipping_id" value=""/>
		<input type="hidden" value="opc" name="controller" id="opc_controller" />
		<input type="hidden" name="form_submitted" value="0" id="form_submitted" />
		<div style="display:none;" id="inform_html">&nbsp;'.$ih.'</div>';
		
	
  return $html;
		
 }


}
