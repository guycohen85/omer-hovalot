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

class OPCUserFields {

  function getUserFields(&$userFields, &$OPCloader, &$cart, $remove=array(), $only=array(), $skipreorder=array())
  {
  
  
     include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 
	 $user = JFactory::getUser(); 
	  $uid = $user->get('id');
	 
       //$userFields = $userFieldsOrig; 
	   if (!empty($userFields))
       foreach ($userFields['fields'] as $key=>$uf)   
	    {
		   
		 $userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']);  
		 $userFields['fields'][$key]['formcode'] = str_replace('maxlength', 'disabledmaxlength', $userFields['fields'][$key]['formcode']);  
		 
		  if ($key == 'password')
		  $userFields['fields'][$key]['required'] = true; 
	  
		  if ($key == 'password2')
		  $userFields['fields'][$key]['required'] = true; 
		  
		   $arr = array ('name', 'username'); 
		   if (in_array($key, $arr))
			{	
		    $userFields['fields'][$key]['required'] = 1; 
			}
			
		if (!empty($custom_rendering_fields))
		if (in_array($userFields['fields'][$key]['name'], $custom_rendering_fields))
				    {
					  unset($userFields['fields'][$key]); 
					  continue; 
					}
		  
		   if ($key != 'email')
			{
			$userFields['fields'][$key]['formcode'] = str_replace('/>', ' autocomplete="off" />', $userFields['fields'][$key]['formcode']); 
			}
			
			
			if ($key == 'email')
			if (!empty($cart->BT['email']))
			{
			  $userFields['fields'][$key]['formcode'] = str_replace('value=""', ' value="'.$cart->BT['email'].'"', $userFields['fields'][$key]['formcode']); 
			  $userFields['fields'][$key]['formcode'] = str_replace('type="text"', 'type="email"', $userFields['fields'][$key]['formcode']); 
			  
			}
			
			$userFields['fields'][$key]['formcode'] = str_replace('size="0"', '', $userFields['fields'][$key]['formcode']); 
			
		// get proper state listing: 
		if (($key == 'virtuemart_state_id'))
	  {
	    if (!empty($cart->BT['virtuemart_country_id']))
	  $c = $cart->BT['virtuemart_country_id']; 
	  else $c = $default_shipping_country; 
	  
	  
	  
	  if (empty($c))
	  {
	    $vendor = $OPCloader->getVendorInfo($cart); 
		$c = $vendor['virtuemart_country_id']; 
	  }
	  
	  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'commonhtml.php'); 
	  $html = OPCCommonHtml::getStateHtmlOptions($cart, $c, 'BT');
	  
	   
		 if (!empty($cart->BT['virtuemart_state_id']))
		 {
		   $html = str_replace('value="'.$cart->BT['virtuemart_state_id'].'"', 'value="'.$cart->BT['virtuemart_state_id'].'" selected="selected"', $html); 
		 }
		
	  
	    //
		if (!empty($userFields['fields']['virtuemart_state_id']['required']))
		$userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple opcrequired" id="virtuemart_state_id" opcrequired="opcrequired" size="1"  name="virtuemart_state_id" >'.$html.'</select>'; 
		 else
	     $userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple" id="virtuemart_state_id"  size="1"  name="virtuemart_state_id" >'.$html.'</select>';
		
		//$userFields['fields'][$key]['formcode'] = '<select class="inputbox multiple" id="virtuemart_state_id"  size="1"  name="virtuemart_state_id" >'.$html.'</select>'; 
	  }
		
		
		
		// add klarna button: 
		 if (!empty($klarna_se_get_address))
	  if (($key == 'socialNumber'))
		{
		  $newhtml = '<input type="button" id="klarna_get_address_button" onclick="return Onepage.send_special_cmd(this, \'get_klarna_address\' );" value="'.OPCLang::_('COM_ONEPAGE_KLARNA_GET_ADDRESS').'" />';
		  //$userFields['fields'][$key]['formcode'] = str_replace('name="socialNumber"', ' style="width: 70%;" name="socialNumber"', $userFields['fields'][$key]['formcode']).$newhtml;
		  $userFields['fields'][$key]['formcode'] .= $newhtml; 
		}
		
		// mark email read only when logged in
		if ($key == 'email')
	  {
	    
		// user is logged, but does not have a VM account
		if ((!$OPCloader->logged($cart)) && (!empty($uid)))
		{
		  // the user is logged in only in joomla, but does not have an account with virtuemart
		  $userFields['fields'][$key]['formcode'] = str_replace('/>', ' readonly="readonly" />',  $userFields['fields'][$key]['formcode']); 
		}
		else
		$userFields['fields'][$key]['formcode'] = str_replace('type="text"', 'type="email"', $userFields['fields'][$key]['formcode']); 
	   }
		
		
		// remove autocomplete for multi dependant fields
	if (($key == 'virtuemart_country_id'))
	   {
	      $userFields['fields'][$key]['formcode'] = str_replace('name=', ' autocomplete="off" name=', $userFields['fields'][$key]['formcode']); 
	   }
		
	// set required properly: 
	if (isset($userFields['fields'][$key]['name']))
	 if (!empty($uf['required']) && (strpos($uf['formcode'], 'required')===false))
	 if ($userFields['fields'][$key]['name'] != 'virtuemart_state_id')
	  {
	    
	    $x1 = strpos($uf['formcode'], 'class="');
		if ($x1 !==false)
		{
		  $userFields['fields'][$key]['formcode'] = str_replace('class="', 'class="required ', $uf['formcode']);
		}
		else
		{
		$userFields['fields'][$key]['formcode'] = str_replace('name="', 'class="required" name="', $uf['formcode']);
		 
		 
		}
		
		
	  }
		
	if ($uf['type'] == 'date')
	 {
		 $userFields['fields'][$key]['formcode'] = str_replace(OPCLang::_('COM_VIRTUEMART_NEVER'), $userFields['fields'][$key]['title'], $userFields['fields'][$key]['formcode']); 
	 }
		
			
			
			
			if (!empty($op_no_display_name))
	 if ($userFields['fields'][$key]['name'] == 'name')
	  {
		unset($userFields['fields'][$key]); 
	    continue; 
	  }
	  
	  	 if ($key == 'username')
     if (!empty($op_usernameisemail) && ($userFields['fields'][$key]['name'] == 'username')) 
	 {
		 
	  unset($userFields['fields'][$key]); 
	  continue; 
	 }

	 if (($key == 'password') )
	   {
	     $userFields['fields']['opc_password'] = $userFields['fields'][$key];
		 $userFields['fields']['opc_password']['formcode'] = str_replace('password', 'opc_password', $userFields['fields']['opc_password']['formcode']); 
		 $userFields['fields']['opc_password']['formcode'] = str_replace('type="opc_password"', 'type="password" autocomplete="off" ', $userFields['fields']['opc_password']['formcode']); 
		 $userFields['fields']['opc_password']['name'] = 'opc_password'; 
		 //unset($userFields['fields'][$key]); 
		  if (!empty($password_clear_text))
		  {
		  
		  
				$userFields['fields']['opc_password']['formcode'] = str_replace('type="password"', 'type="text" ', $userFields['fields']['opc_password']['formcode']); 
		  }
		  unset($userFields['fields']['password']);
		 //$l = $userFields['fields']['opc_password'];
		
	   }
	   
	if ($key == 'password2')
    {
		
		
	   		 if (!empty($password_clear_text))
		  {
				$userFields['fields']['password2']['formcode'] = str_replace('type="password"', 'type="text" ', $userFields['fields']['password2']['formcode']); 
		  }
		  
		  $userFields['fields']['opc_password2'] = $userFields['fields']['password2']; 
		  unset($userFields['fields']['password2']); 

	}	
	 
	 
	 if ($key == 'email')
    {
	
	
	
	  $userFields['fields'][$key]['formcode'] = str_replace('class="required', 'class="required email ', $userFields['fields']['email']['formcode']); 
      $userFields['fields'][$key]['formcode'] = str_replace('type="text"', 'type="email"', $userFields['fields'][$key]['formcode']); 	  
	  if (!empty($double_email))
	  {
	    $email2 = $userFields['fields']['email'];
		$email2['name'] = 'email2'; 
		$title = OPCLang::_('COM_ONEPAGE_EMAIL2'); 
		if ($title != 'COM_ONEPAGE_EMAIL2')
		$email2['title'] = $title;
		$email2['formcode'] = str_replace('"email', '"email2', $email2['formcode']); 
		$email2['formcode'] = str_replace('id=', ' onblur="javascript: doublemail_checkMail();" id=', $email2['formcode']);
		$email2['formcode'] = str_replace('type="email2"', 'type="email"', $email2['formcode']); 
		$h = '<span style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email2_info" class="email2_class">';
		$emailerr = OPCLang::_('COM_ONEPAGE_EMAIL_DONT_MATCH');
		if ($emailerr != 'COM_ONEPAGE_EMAIL_DONT_MATCH')
		$h .= $emailerr;
		else $h .= "Emails don't match!";
		$h .= '</span>';
		$email2['formcode'] .= $h;
	  }
	  
	   if (!empty($opc_check_email))
	  if ((!$OPCloader->logged($cart)) && (empty($uid)))
	  if (!empty($userFields['fields']['email']))
	  {

	     $un = $userFields['fields']['email']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.email_check(this);" id=', $un);
		 $un .=  '<span class="email_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email_already_exists">';
		 $un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL')); 
		 $un .= '</span>'; 
		 $userFields['fields']['email']['formcode'] = $un; 
	  }
	  
	  
	  
	  
	  
	 }
	 
	if (($key == 'EUVatID') || ($key == 'eu_vat_id'))
	  {
	    $h = '<br /><span style="display: none; position: relative; float: left; clear: both; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="vat_info" class="vat_info">';
		$h .= '</span>';
		$userFields['fields'][$key]['formcode'] .= $h; 
		
		
	  }	 
	 
	 if ($key == 'opc_vat')
	 {
	 
	 if (!empty($opc_euvat))
	  if (!empty($userFields['fields']['opc_vat']))
	  {

	     $un = $userFields['fields']['opc_vat']['formcode']; 
		 if (!empty($opc_euvat_button))
		 {
		    $un .= '<br /><input type="button" value="'.OPCLang::_('COM_ONEPAGE_VALIDATE_VAT_BUTTON').'" onclick="javascript:  Onepage.validateOpcEuVat(this);" class="opc_euvat_button" />'; 
		 }
		 $un .=  '<br /><span class="vat_info" style="display: none; position: relative;  color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="vat_info">';
		 $un .= OPCLang::_('COM_ONEPAGE_VAT_CHECKER_INVALID'); 
		 $un .= '</span>'; 
		 $userFields['fields']['opc_vat']['formcode'] = $un; 
	  }
	}
	 
	 
	  if ($key == 'username')
	   {
	   
	   
	       if (!empty($opc_check_username))
	 if ((!$OPCloader->logged($cart)) && (empty($uid)))
	 if (!empty($userFields['fields']['username']))
	  {
	     $u = OPCLang::_('COM_VIRTUEMART_REGISTER_UNAME'); 
	     $un = $userFields['fields']['username']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.username_check(this);" id=', $un);
		 $un .=  '<span class="username_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="username_already_exists">';
		 $un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', $u); 
		 $un .= '</span>'; 
		 $userFields['fields']['username']['formcode'] = $un; 
	  }
	   }
	  
	  
	}
	
	if (!empty($email2))
	$userFields['fields']['email2'] = $email2; 
	
	
	if (!empty($userFields))
	self::reorderFields($userFields, $skipreorder); 
	 
		  
		   
		  
	
		
		
  }
  public static function reorderFields(&$userFields, $skip=array())
 {
 if (empty($userFields)) return;
 if (empty($userFields['fields'])) return;
 
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
    // reorder the registration fields (display name, email, email2, username, pwd1, pwd2): 
$orig = $userFields; 
$newf = array(); 
$newf['fields'] = array(); 

if (!empty($userFields['fields']['delimiter_userinfo']))
{
 $newf['fields']['delimiter_userinfo'] = $userFields['fields']['delimiter_userinfo']; 
}



if (isset($userFields['fields']['name']))
$newf['fields']['name'] = $userFields['fields']['name']; 

if (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION')
if (isset($userFields['fields']['username']))
{
$newf['fields']['username'] = $userFields['fields']['username']; 
}

if (!in_array('email', $skip))
if (isset($userFields['fields']['email']))
$newf['fields']['email'] = $userFields['fields']['email']; 

//if (isset($email2))
if (!in_array('email', $skip))
if (!empty($userFields['fields']['email2']))
$newf['fields']['email2'] = $userFields['fields']['email2']; //$email2;

if (isset($userFields['fields']['register_account']))
if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
if ((isset($userFields['fields']['password'])) || (isset($userFields['fields']['opc_password'])))
{
  $newf['fields']['register_account'] = $userFields['fields']['register_account']; 
}

if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
if (isset($userFields['fields']['username']))
{
$newf['fields']['username'] = $userFields['fields']['username']; 
}


if (isset($userFields['fields']['opc_password']))
$newf['fields']['opc_password'] = $userFields['fields']['opc_password']; 

if (isset($userFields['fields']['opc_password2']))
$newf['fields']['opc_password2'] = $userFields['fields']['opc_password2']; 


if (isset($userFields['fields']['password']))
$newf['fields']['password'] = $userFields['fields']['password']; 

if (isset($userFields['fields']['password2']))
$newf['fields']['password2'] = $userFields['fields']['password2']; 

//delimiter_billto
if (!empty($userFields['fields']['delimiter_billto']))
{
 $newf['fields']['delimiter_billto'] = $userFields['fields']['delimiter_billto']; 
}

//delimiter_userinfo

if (!empty($klarna_se_get_address))
if (!empty($userFields['fields']['socialNumber']))
{
 $newf['fields']['socialNumber'] = $userFields['fields']['socialNumber']; 
 $newf['fields']['socialNumber']['formcode'] = str_replace('name="', ' autocomplete="off" name="', $userFields['fields']['socialNumber']['formcode']); 
 
}

$ret = array(); 
$ret['fields'] = array(); 
// adding reg f

	  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'transform.php');
	  if (in_array('email', $skip))
	  if (isset($userFields['fields']['email2']))
	  {
	  $email2 = $userFields['fields']['email2']; 
	  OPCTransform::insertAfter($userFields['fields'], 'email', $email2, 'email2'); 
	  }

$ins = array(); 
foreach ($newf['fields'] as $key=>$val)
 {
   $ret['fields'][$key] = $val;
   $ins[] = $key; 
 }
 if (!empty($ins))
 {
 foreach ($userFields['fields'] as $key2=>$val2)
 {
   if (!in_array($key2, $ins))
   $ret['fields'][$key2] = $val2; 
 }
 }
 else return $userFields; 
 
 
 $userFields['fields'] = $ret['fields']; 
 return $userFields; 

 }

  
  public static function hasMissingFields(&$BTaddress) 
  {
    $ignore = array('delimiter', 'captcha', 'hidden'); 
  $types = array(); 
   foreach ($BTaddress as $key=>$val)
     {
	   //if (in_array($val['name'], $corefields)) continue; 
	   if (in_array($val['type'], $ignore)) continue; 
	   if (empty($val['value']))
	   if (!empty($val['required']))
	    {
		  if ($key == 'virtuemart_state_id')
				{
				  $c = $BTaddress['virtuemart_country_id']['value']; 
				  $stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	
				  $states = $stateModel->getStates( $c, true, true );
				  if (!empty($states)) 
				  {
				  
				  return true; 
				  }
				  continue; 
				}
				
				return true; 
		}
	    //$types[] = $val['type']; 
	 }
	 return false; 
  }
  
}