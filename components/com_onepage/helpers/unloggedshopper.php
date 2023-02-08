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

class OPCUnloggedShopper {

 public static function getRegistrationHhtml(&$obj, &$OPCloader)
 {
       // if (!empty($no_login_in_template)) return "";
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
    if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	 
	if (!empty($obj->cart))
	$cart =& $obj->cart; 
	else
	$cart = VirtueMartCart::getCart();
	
  
   
   
    $type = 'BT'; 
  // $OPCloader->address_type = 'BT'; 
   // for unlogged
   $virtuemart_userinfo_id = 0;
   //$OPCloader->$virtuemart_userinfo_id = 0;
   $new = 1; 
   $fieldtype = $type . 'address';
   if (method_exists($cart, 'prepareAddressDataInCart'))
   $cart->prepareAddressDataInCart($type, $new);
   /*
   if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
   */
   $OPCloader->setRegType(); 		

   
   if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
   $corefields = VirtueMartModelUserfields::getCoreFields();
   
   
   $userFields = $cart->$fieldtype;
require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   //$OPCloader->_model = OPCmini::getModel('user'); //new VirtuemartModelUser();
    $layout = 'default';
   


   foreach ($userFields['fields'] as $key=>$uf)   
   {
	 
	   if (!in_array($key, $corefields) || ($key=='agreed'))
	   {
		   unset($userFields['fields'][$key]); 
		   continue; 
	   }
	   /*
	  $userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']);  
	   
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
		*/			
	 if (!empty($opc_email_in_bt) || ($OPCloader->isNoLogin()) || ($OPCloader->isNoLogin()))
	 {
	   if ($userFields['fields'][$key]['name'] == 'email') 
	   {
	    unset($userFields['fields'][$key]); 
	    continue; 
	   }
	 }
	 /*
	 if (!empty($op_no_display_name))
	 if ($userFields['fields'][$key]['name'] == 'name')
	  {
		unset($userFields['fields'][$key]); 
	    continue; 
	  }
	  */
     /*
	 if ($key == 'username')
     if (!empty($op_usernameisemail) && ($userFields['fields'][$key]['name'] == 'username')) 
	 {
		 
	  unset($userFields['fields'][$key]); 
	  continue; 
	 }
	 
	 
	 if (($userFields['fields'][$key]['name'] == 'email') && (!empty($op_usernameisemail)))
	 {
	   
	 }
	 */
     //if ($f == $userFields['fields'][$key]['name'] && ($userFields['fields'][$key]['name'] != 'agreed'))
	 {
	  
	  //$l = $userFields['fields'][$key];
	  /*
	  if ($key != 'email')
	  {
	  $userFields['fields'][$key]['formcode'] = str_replace('/>', ' autocomplete="off" />', $userFields['fields'][$key]['formcode']); 
	  }
	  */
	   if ($key == 'email')
	  {
	   
	    $user = JFactory::getUser();
		// special case in j1.7 - guest login (activation pending)
		/*
	   	if (!empty($currentUser->guest) && ($currentUser->guest == '1'))
		{
		  // we have a guest login here, therefore we will not let the user to change his email
		  $l['formcode'] = str_replace('/>', ' readonly="readonly" />', $l['formcode']); 
		}
		else
		*/
		{
		$uid = $user->get('id');
		// user is logged, but does not have a VM account
		if ((!$OPCloader->logged($cart)) && (!empty($uid)))
		{
		  // the user is logged in only in joomla, but does not have an account with virtuemart
		  $userFields['fields'][$key]['formcode'] = str_replace('/>', ' readonly="readonly" />', $userFields['fields'][$key]['formcode']); 
		}
		}
	  }
	  /*
	  if ($key == 'password')
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
		  unset($userFields['fields'][$key]);
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
	*/

	
	/*
	if ($key == 'email')
    {
	  $userFields['fields'][$key]['formcode'] = str_replace('class="required', 'class="required email ', $userFields['fields']['email']['formcode']); 
	  
	}
	*/
	
	  //$fields['fields'][$key]  = $l;
     }
	 
	 
	 
	
   }
      

   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'userfields.php'); 
   OPCUserFields::getUserFields($userFields, $OPCloader, $cart); 
     // lets move email to the top
		$copy = array(); 
	
		
		
		
   	// we will reorder the fields, so the email is first when used as username
	
		
		$u = OPCLang::_('COM_VIRTUEMART_REGISTER_UNAME'); 
		
		//$e = OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'); 
		
   
	// disable when used for logged in 
	if (!empty($userFields['fields']))
	{
	 /*
     if (empty($opc_email_in_bt) && (!empty($double_email)))
	  {
	    // email is in BT, let's check for double mail
		
		$email2 = $userFields['fields']['email'];
		$email2['name'] = 'email2'; 
		$title = OPCLang::_('COM_ONEPAGE_EMAIL2'); 
		if ($title != 'COM_ONEPAGE_EMAIL2')
		$email2['title'] = $title;
		$email2['formcode'] = str_replace('"email', '"email2', $email2['formcode']); 
		$email2['formcode'] = str_replace('id=', ' onblur="javascript: doublemail_checkMail();" id=', $email2['formcode']);
		
		$h = '<span style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email2_info" class="email2_class">';
		$emailerr = OPCLang::_('COM_ONEPAGE_EMAIL_DONT_MATCH');
		if ($emailerr != 'COM_ONEPAGE_EMAIL_DONT_MATCH')
		$h .= $emailerr;
		else $h .= "Emails don't match!";
		$h .= '</span>';
		$email2['formcode'] .= $h;
	  }
	  */
	  
	  /*
	 if (!empty($opc_check_username))
	 if ((!$OPCloader->logged($cart)) && (empty($uid)))
	 if (!empty($userFields['fields']['username']))
	  {
	   
	     $un = $userFields['fields']['username']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.username_check(this);" id=', $un);
		 $un .=  '<span class="username_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="username_already_exists">';
		 $un .= OPCLang::sprintf('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME', $u); 
		 $un .= '</span>'; 
		 $userFields['fields']['username']['formcode'] = $un; 
	  }
	  */
	  
	  /*
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
	  */
	  
	}
	/*
	$OPCloader->reorderFields($userFields); 
    */
    if (count($userFields['fields'])===0) 
	{
	 // no fields found
	 return '';
	}
   
   
   
   //if (empty($opc_email_in_bt) && (!empty($double_email)))
   //$OPCloader->insertAfter($userFields['fields'], 'email', $email2, 'email2'); 




   $vars = array('rowFields' => $userFields, 
				 'cart'=> $obj,
				 'is_registration' => true);
   $html = $OPCloader->fetch($OPCloader, 'list_user_fields.tpl', $vars); 
   
   $html = str_replace("'password'", "'opc_password'", $html); 
   $html = str_replace("password2", "opc_password2", $html); 
   
   if (strpos($html, 'email_field')!==false) $html .= '<input type="hidden" name="email_in_registration" value="1" id="email_in_registration" />'; 
   else $html .= '<input type="hidden" name="email_in_registration" value="0" id="email_in_registration" />'; 
   
   return $html; 
 }

}