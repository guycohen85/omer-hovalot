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
class OPCLoggedShopper {

public static function getUserInfoBT(&$ref, &$OPCloader)
			{
			
			include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
			/*
			if (!class_exists('VirtuemartModelUser'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
		    */
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
			$uid = JFactory::getUser()->id;
		    $userDetails = $umodel->getUser();
			$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
			/*
			if (!class_exists('VirtueMartModelState'))
			 require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'state.php'); 
			if (!class_exists('VirtueMartModelCountry'))
			require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'country.php'); 
		    */
			require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			$countryModel = OPCmini::getModel('country'); //new VirtueMartModelCountry(); 
			$stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
			
							
			$userFields = $umodel->getUserInfoInUserFields('edit', 'BT', $virtuemart_userinfo_id);
			
			$userFieldsModel = OPCmini::getModel('userfields');
			
			/* opc 2.0.115+ */
			//  causes compatiblity issues in old vm versions
			$_userFields = $userFieldsModel->getUserFields(
					 'account'
					, array('captcha' => true, 'delimiters' => true) // Ignore these types
					, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);
			/*
			$first = reset($userDetails->userInfo);
			
			$userfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$first
			);
			*/
			//
			/* end opc 2.0.115+ */
			$_u = array(); 
			foreach ($_userFields as $k=>$v)
			 {
			    if (isset($v->name))
			    $_u[$v->name] = $v; 
			 }
			
			
			// will set the BT address:
			$ref->cart->BT = null; 
			if (method_exists($ref->cart, 'setPreferred'))
			$ref->cart->setPreferred(); 
			
				$db = JFactory::getDBO(); 
				$q = "select * from #__virtuemart_userinfos as uu, #__users as ju where uu.virtuemart_user_id = '".$uid."' and ju.id = uu.virtuemart_user_id and uu.address_type = 'BT' limit 0,1 "; 
				$db->setQuery($q); 
				$fields = $db->loadAssoc(); 
				//		echo $db->getErrorMsg();
			  if (!empty($virtuemart_userinfo_id) && (!empty($userFields[$virtuemart_userinfo_id])))
			   {
			    if (method_exists($umodel, 'getCurrentUser'))
				{
			    $user = $umodel->getCurrentUser();
				foreach ($user->userInfo as $address) {
				if ($address->address_type == 'BT') {
					$ref->cart->BT = (array)$address;
					
					continue; 
				}
				}
				}
			
			  }
			  //$ref->cart->BTaddress = $userFields[$virtuemart_userinfo_id]['fields']; 
			    // ok, the user is logged, in but his data might not be in $ref->cart->BT[$BTaddress[$k]['name']]
			    // updated on vm2.0.26D
				//$ref->cart->prepareAddressDataInCart('BTaddress', 0);
				if (method_exists($ref->cart, 'prepareAddressDataInCart'))
				$ref->cart->prepareAddressDataInCart('BT', 0);
			    
				if (isset($ref->cart->BTaddress))
				$BTaddress = $ref->cart->BTaddress['fields']; 
				
				if (empty($BTaddress))
				{
				
				$userFieldsBT = $userFieldsModel->getUserFieldsFor('cart','BT');
				$BTaddress = $userFieldsModel->getUserFieldsFilled(
					$userFieldsBT
					,$ref->cart->BT
					,''
				);
				
				}
				
				
				if (!empty($BTaddress['fields']))
				$BTaddress = $BTaddress['fields']; 
				
				// opc 2.0.115: 
				// $BTaddress = $userfields['fields']; 
				// end
				
				
				$useSSL = VmConfig::get('useSSL', 0);
				$edit_link = JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT&virtuemart_userinfo_id='.$virtuemart_userinfo_id.'&cid[]='.$uid, true, $useSSL);
				
				$ghtml = array(); 
				
				{
				$OPCloader->getNamedFields($BTaddress, $fields, $_u); 
				foreach ($BTaddress as $k=>$val)
				 {
				   
				   // let update the value per type
				  if (isset($fields[$val['name']]))
				   $BTaddress[$k]['value'] = $fields[$val['name']]; //trim($BTaddress[$k]['value']); 
				  
				   
				   
				
				   //if (empty($BTaddress[$k]['value']) && (!empty($ref->cart->BT)) && (!empty($ref->cart->BT[$BTaddress[$k]['name']]))) $BTaddress[$k]['value'] = $ref->cart->BT[$BTaddress[$k]['name']]; 
				 
				   
				   if ($val['name'] == 'agreed') unset($BTaddress[$k]);
				   if ($val['name'] == 'username') unset($BTaddress[$k]);
				   if ($val['name'] == 'password') unset($BTaddress[$k]);
				if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
				    if (in_array($val['name'], $custom_rendering_fields))
				    {
					  unset($BTaddress[$k]); 
					  continue; 
					}
				   
				   $gf = array('city', 'virtuemart_state_id', 'virtuemart_country_id'); 
				   
				   if (in_array($val['name'], $gf))
				    {
					  $a = array();
					  if ($val['name'] == 'city')
					  {
					  
					    $a['name'] = 'city_field'; 
						$a['value'] = $fields[$val['name']]; 
						
					  }
					  else
					  if (($val['name'] == 'virtuemart_state_id'))
					  {
					    
						if (!empty($fields[$val['name']]))
						{
						$a['name'] = 'virtuemart_state_id'; 
						//$a['value'] = $fields[$val['name']];
						$sid = (int)$fields[$val['name']];; 
						$q = "select state_name from #__virtuemart_states where virtuemart_state_id = '".$sid."' limit 0,1"; 
						$db->setQuery($q); 
						$state_name = $db->loadResult(); 
						$a['value'] = OPCmini::slash($state_name); 
						}
						else
						{
								$a['name'] = 'virtuemart_state_id'; 
								$a['value'] = "";

						}
						// we will override the generated html in order to provide better autocomplete functions
						
					    
					  }
					  /*
					  else
					  if (false)
					  if ($val['name'] == 'virtuemart_country_id')
					  {
					  	if (!empty($fields[$val['name']]))
						{
						$a['name'] = 'virtuemart_country_id'; 
						//$a['value'] = $fields[$val['name']];
						$cid = (int)$fields[$val['name']];; 
						$q = "select country_name from #__virtuemart_countries where virtuemart_country_id = '".$cid."' limit 0,1"; 
						$db->setQuery($q); 
						$c_name = $db->loadResult(); 
						$a['value'] = OPCmini::slash($c_name, false); 
						}
						else
						{
								$a['name'] = 'virtuemart_country_id'; 
								$a['value'] = "";

						}

					   
					  }
					  */
					  if (!empty($a))
					  $ghtml[] = $a;
					}
				   
				 }
				 }
			  //check missing new fields
			  $hasmissing = $OPCloader->hasMissingFields($BTaddress); 
			  
		      $htmlsingle_all = $OPCloader->getBTfields($ref, true, false); 
			  $htmlsingle = '<div '; 
			  if (empty($hasmissing))
			  $htmlsingle .= ' style="display: none;" '; 
			  $htmlsingle .= ' id="opc_stedit_'.$virtuemart_userinfo_id.'">'.$htmlsingle_all.'</div>'; 
  			  $BTaddress = $OPCloader->setCountryAndState($BTaddress); 
			
			  $edit_link = '#" onclick="return Onepage.op_showEditST('.$virtuemart_userinfo_id.')';
				$google_html = ''; 
				
				if (!empty($ghtml))
				foreach ($ghtml as $ii)
				{
				  
				  $google_html .= '<input type="hidden" name="google_'.$ii['name'].'" id="google_'.$ii['name'].'" value="'.$ii['value'].'" />'; 
				}
				
				
				
				$OPCloader->txtToVal($BTaddress); 
						
				$html = $OPCloader->fetch($OPCloader, 'customer_info.tpl', array('BTaddress' => $BTaddress, 'virtuemart_userinfo_id' => $virtuemart_userinfo_id, 'edit_link' => $edit_link)); 
				if (empty($op_disable_shipto))
				{
				  $html .= '<input type="hidden" name="default_ship_to_info_id" value="'.$virtuemart_userinfo_id.'" checked="checked" />'; 
				}
				$html .= '<input type="hidden" id="bt_virtuemart_userinfo_id" name="bt_virtuemart_userinfo_id" value="'.$virtuemart_userinfo_id.'" />'; 
				$html2 = $html.$google_html; 
				$html = '<div '; 
				if (!empty($hasmissing))
				$html .= ' style="display: none;" '; 
				$html .= ' id="opc_st_'.$virtuemart_userinfo_id.'">'.$html2.'</div>'.$htmlsingle.'<input type="hidden" id="opc_st_changed_'.$virtuemart_userinfo_id.'" name="opc_st_changed_'.$virtuemart_userinfo_id.'" value="'; 
				if (!empty($hasmissing))
				$html .= '1'; 
				else
				$html .= '0'; 
				$html .= '" />'; 
				$html = str_replace('password2', 'opc_password2', $html); 
				return $html; 
			}
			
public static function getUserInfoST(&$ref, &$OPCloader)
{
  			
			   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
			   
			   if (empty($ref->cart))
			    {
				  $ref->cart = VirtueMartCart::getCart();
				}
			   //$ref->cart->ST = 0; 
			   if (method_exists($ref->cart, 'prepareAddressDataInCart'))
			   $ref->cart->prepareAddressDataInCart('ST', 1);
			   
			   if (!empty($ref->cart->ST))
			   {
			   
			    $STaddress = $ref->cart->STaddress['fields']; 
				
				foreach ($STaddress as $k=>$val)
				 {
				   
				   $kk = str_replace('shipto_', '', $STaddress[$k]['name']); 
				   if (empty($STaddress[$k]['value']) && (!empty($ref->cart->ST)) && (!empty($ref->cart->ST[$kk]))) $STaddress[$k]['value'] = $ref->cart->ST[$kk]; 				
				   $STaddress[$k]['value'] = trim($STaddress[$k]['value']); 
				   if ($val['name'] == 'agreed') unset($STaddress[$k]);
				   
				 }
				 $STnamed = $STaddress; 
				 $STnamed = $OPCloader->setCountryAndState($STnamed); 
				 
				}
				else $STaddress = array(); 
				//$bt_user_info = $ref->cart->BTaddress->user_infoid; 
			
				/*
				if (!class_exists('VirtuemartModelUser'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
			    */
				require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
				$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
				
				$virtuemart_userinfo_id = 0; 
				$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				
			
				
				$userDetails = $umodel->getUser();
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				
				$userFields = $umodel->getUserInfoInUserFields('default', 'BT', $virtuemart_userinfo_id);
				
				/*
				if (empty($userFields[$virtuemart_userinfo_id]))
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				else $virtuemart_userinfo_id = $userFields[$virtuemart_userinfo_id]; 
				*/
				
				
				//$id = $umodel->getId(); 
				
				if (empty($virtuemart_userinfo_id)) return false; 
				
				$STaddressList = $umodel->getUserAddressList($uid , 'ST');
				
				$STaddressListOrig = $STaddressList; 
				$addressCount = count($STaddressListOrig); 
				
				if ($addressCount > 10) $addressCountAjax = true; 
				else $addressCountAjax = false; 
				
				// getUserAddressList uses references/pointers for it's objects, therefore we need to create a copy manually:
				/*
				if (false)
				{
				$STaddressListOrig = array(); 
				if (!empty($STaddressList))
				foreach ($STaddressList as $k => $v)
				{
				 foreach ($v as $n=>$r)
				  {
				    $STaddressListOrig[$k]->$n = $r;       
				  }
				}
				}
				*/
				if (isset($ref->cart->STaddress['fields']))
				$BTaddress = $ref->cart->STaddress['fields']; 
				else
				$BTaddress = $ref->cart->BTaddress['fields']; 
				
				
				if (!empty($ref->cart->savedST))
				  {
				    
					foreach ($STaddressList as $key2=>$adr2)
					foreach ($ref->cart->savedST as $key=>$val)
					foreach ($adr2 as $keya => $vala)
					  {
					    if ($keya==$key)
						if ($val == $vala)
					     {
						   if (!isset($bm[$key2])) $bm[$key2] =0; 
						   $bm[$key2]++; 
						 }						 
					  }
				  
				  $largest = 0; 
				  $largest_key = 0; 
				  if (!empty($bm))
				  foreach ($bm as $key=>$bc)
				   {
				      if ($bc >= $largest)  
					  {
					    $largest = $bc; 
						$largest_key = $key; 
					  }
				   }
				   if (!empty($largest))
				     {
					   
					   $selected_id = $STaddressList[$largest_key]->virtuemart_userinfo_id;
					   
					 }
				   
				   }
				
				$x = VmVersion::$RELEASE;	
				$useSSL = VmConfig::get('useSSL', 0);
				
				
				foreach ($STaddressList as $ke => $address)
				 {
				
				  $STaddressList[$ke] = $OPCloader->setCountryAndState($STaddressList[$ke]); 
				
				  
				   if (empty($address->address_type_name))
				    {
					  $address->address_type_name = OPCLang::_('COM_VIRTUEMART_USER_FORM_ADDRESS_LABEL'); 
					  //$address->address_type_name = OPCLang::_('JACTION_EDIT'); 
					}
					
					$link = self::getEditLink($uid, $address->virtuemart_userinfo_id); 
				 
				 
				     $STaddressList[$ke]->edit_link = 	$link; 
				 


				   }

				 
				 
				 
					$new_address_link = '#" onclick="return Onepage.op_showEditST();';
				//version_compare(
				//vm204: index.php?option=com_virtuemart&view=user&task=editaddresscart&new=1&addrtype=ST&cid[]=51
	// don't use ST 
				
				
				
				
				if (empty($only_one_shipping_address))
				{
				$arr = array ('virtuemart_userinfo_id' => $virtuemart_userinfo_id, 
						'STaddressList'=>$STaddressList, ); 
				$html3 = $OPCloader->fetch($OPCloader, 'list_select_address.tpl', $arr); 
				$bm = array(); 
				
				
				
				if (empty($html3))
				 {
				   //theme file not found, please create or copy /overrides/list_select_address.tpl.php to your theme directory
				   if (!$addressCountAjax)
				   $html3 = '<select class="opc_st_select" name="ship_to_info_id" id="id'.$virtuemart_userinfo_id.'" onchange="return Onepage.changeST(this);" >';
				   else
				   $html3 = '<select class="opc-chzn-select opc_st_select" name="ship_to_info_id" id="id'.$virtuemart_userinfo_id.'" onchange="return Onepage.changeSTajax(this);" >';
				   
				   $html3 .= '<option value="'.$virtuemart_userinfo_id.'">'.OPCLang::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT').'</option>';
				foreach ($STaddressList as $stlist)
				{
				  $html3 .= '<option value="'.$stlist->virtuemart_userinfo_id.'">';
				  if (!empty($stlist->address_type_name)) 
				     $html3 .= $stlist->address_type_name;

				  if (isset($stlist->address_1))
					 $html3 .= ','.$stlist->address_1; 
					 
					 if (isset($stlist->city))
					 $html3 .= ','.$stlist->city; 
					 
				  $html3 .= '</option>'; 
				}
				$html3 .= '<option value="new">'.OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL').'</option>';
				$html3 .= '</select>'; 
				   
				   
				 }
				 if (!empty($selected_id))
				 {
				   $html3 = str_replace('value="'.$selected_id.'"', 'value="'.$selected_id.'" selected="selected" ', $html3); 
				 }
				$html3 .= '<input type="hidden" name="sa" id="sachone" value="" />'; 
				}
				else
				{
				  // load single_shipping_address.tpl.php
				  if (!empty($STaddressList))
				  {
				  $adr1 = reset($STaddressListOrig); 
				
  
				  foreach ($adr1 as $k=>$v)
				  {
				    $ada[$k] = $v; 
				    $ada['shipto_'.$k] = $v; 
				  }
				  $ref->cart->ST = $ada; 
				  }
				  else $ref->cart->ST = 0; 
				  
				if (!empty($ref->cart->ST['virtuemart_country_id']))
				$dc = $ref->cart->ST['virtuemart_country_id']; 
				else
				$dc = OPCloader::getDefaultCountry($ref->cart, true); 				
				
				  
				  $htmlsingle = $OPCloader->getSTfields($ref, true, false, $dc); 
				  if (!empty($adr1))
				  $htmlsingle .= '<input type="hidden" name="shipto_logged" value="'.$adr1->virtuemart_userinfo_id.'" />'; 
				  else  $htmlsingle .= '<input type="hidden" name="shipto_logged" value="new" />'; 
				  // a default BT address
				  $htmlbt = '<input type="hidden" name="ship_to_info_id_bt" id="ship_to_info_id_bt" value="'.$virtuemart_userinfo_id.'"  class="stradio"/>'; 
				  $htmlsingle.= $htmlbt; 
				  $ref->cart->ST = 0; 
				  return $htmlsingle; 
				  // end of load single shipping address for a logged in user
				}
				$i = 2;
				
				$BTaddressNamed = $BTaddress; 
				$BTaddressNamed = $OPCloader->setCountryAndState($BTaddressNamed); 
				
				if (!empty($STaddressList) && (empty($htmlsingle)))
				if (!$addressCountAjax)
				{
				foreach ($STaddressListOrig as $ind=>$adr1)
				{
				
				
				$html2 = self::renderNamed($BTaddressNamed, $adr1, $ref->cart, $OPCloader, $virtuemart_userinfo_id); 
				
				
				}
				}
				else
				{
				  // we have a problem, the too many addresses will cause a memory leak, therefore we load them over ajax
				  
				}
				
				
				
				// add a new address: 
				if (empty($htmlsingle))
				{
				$ref->cart->ST = 0; 
				$dc = OPCloader::getDefaultCountry($ref->cart, true); 
				
				$html22 = $OPCloader->getSTfields($ref, true, true, $dc); 
				$html22 .= '<input type="hidden" name="shipto_logged" value="new" />'; 
				//$html2 .= '<div id="hidden_st_" style="display: none;">'.$html22.'</div>'; 

				$html22 = str_replace('id="', 'id="REPLACEnewREPLACE', $html22); 
				$html22 = str_replace('name="', 'name="REPLACEnewREPLACE', $html22); 
				
				
				$html22 = '<div id="hidden_st_new" style="display: none;">'.$html22.'<div id="opc_st_new">&nbsp;</div><input type="hidden" name="opc_st_changed_new" id="opc_st_changed_new" value="1" /></div>'; 
				
				
				
				if (!isset(OPCloader::$extrahtml)) OPCloader::$extrahtml = ''; 
				OPCloader::$extrahtml .= $html22; 
				$html22 = ''; 
				
				if (!isset($html2)) $html2 = ''; 
				}
				else $html2 = ''; 
				
				$ref->cart->ST = 0; 
				$STnamed = $STaddress; 
				$STnamed = $OPCloader->setCountryAndState($STnamed); 
				 
				$vars = array(
				 'STaddress' => $STnamed, 
				 'bt_user_info_id' => $virtuemart_userinfo_id, 
				 'BTaddress' => $BTaddress,
				 'STaddressList' => $STaddressList,
				 'uid'=>$uid,
				 'cart'=>$ref->cart,
				 'new_address_link' => $new_address_link, 
				
				);
				
				// a default BT address
				$htmlbt = '<input type="hidden" name="ship_to_info_id_bt" id="ship_to_info_id_bt" value="'.$virtuemart_userinfo_id.'"  class="stradio"/>'; 
				$html2 .= '<div id="hidden_st_'.$virtuemart_userinfo_id.'" style="display: none;">'.$htmlbt.'</div>'; 
				
				//$ref->cart->STaddress = $STaddress; 
				//$ref->cart->BTaddress = $BTaddress; 
				
				if (empty($html3) && (empty($htmlsingle)))
				$html =  $OPCloader->fetch($OPCloader, 'list_shipto_addresses.tpl', $vars); 
				else $html = ''; 
				
				
				
				//if (!empty($html) && (!empty($html2)))
				if ((!empty($html2)))
				$html = $html3.'<div id="edit_address_list_st_section">'.$html.'</div>'.$html2; 
				
			
				
			
				foreach ($STaddressList as $ST)
				 {
				   $html = str_replace('for="'.$ST->virtuemart_userinfo_id.'"', ' for="id'.$ST->virtuemart_userinfo_id.'" ', $html); 
				   $html = str_replace('id="'.$ST->virtuemart_userinfo_id.'"', ' id="id'.$ST->virtuemart_userinfo_id.'" onclick="javascript:Onepage.op_runSS(this);" ', $html); 
				 }
				   $html = str_replace('for="'.$virtuemart_userinfo_id.'"', ' for="id'.$virtuemart_userinfo_id.'" ', $html); 
				   $html = str_replace('id="'.$virtuemart_userinfo_id.'"', ' id="id'.$virtuemart_userinfo_id.'" onclick="javascript:Onepage.op_runSS(this);" ', $html); 
				
				if (!empty($selected_id))
				{
				  $jsst = '
//<![CDATA[				  
if (typeof jQuery != \'undefined\')
jQuery(document).ready(function($) {
				  var elst = document.getElementById(\'id'.$virtuemart_userinfo_id.'\'); 
				  if (elst != null)
				   {
				   '; 
				   if ($addressCountAjax)
				    {
					$jsst .= '
				  Onepage.changeSTajax(elst);
				    '; 
					}
					else
					{
				   $jsst .= '
				  Onepage.changeST(elst);
				    '; 
					}
					$jsst .= '
				   }
				  });
//]]>				  
				  '; 
				  
				  $doc = JFactory::getDocument(); 
				  $doc->addScriptDeclaration($jsst); 
				}
				
				if ($addressCountAjax)
				{
				   		if (OPCJ3)
		 {
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 else
		 {
		   vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
	     $document = JFactory::getDocument(); 
		 $document->addScriptDeclaration ( '
//<![CDATA[
		 if (typeof jQuery != \'undefined\')
		 jQuery( function() {
			var d = jQuery(".opc-chzn-select"); 
			if (typeof d.chosen != \'undefined\')
			d.chosen({
			    enable_select_all: false,
				});
		});
//]]>
				');
		 

				}
				 
				
				return $html; 

}
 public static function renderNamed(&$BTaddressNamed, $adr1, &$cart, $OPCloader, $virtuemart_userinfo_id, $returnHtml=false)
  {
				$uid = JFactory::getUser()->get('id'); 
				//if ($ind >= 10) continue; 
				{
				// will load all the shipping addresses
				$ada = array(); 
				foreach ($adr1 as $k=>$v)
				 {
				   $ada[$k] = $v; 
				   $ada['shipto_'.$k] = $v; 
				 }
				 
				$cart->ST = $ada; 
				}
				//do_dump($ref->cart->ST); echo '<br /><br />'; 
				$adr1->edit_link = '#" onclick="return Onepage.op_showEditST('.$adr1->virtuemart_userinfo_id.')';
				
				$i = 2;
				$adr1 = $OPCloader->setCountryAndState($adr1); 
				$arr = array(
				 'ST' => $adr1, 
				 'bt_user_info_id' => $virtuemart_userinfo_id, 
				 'BTaddress' => $BTaddressNamed,
				 'uid'=>$uid,
				 'cart'=>$cart,
				 'i'=>$i,
				 ); 
				
				$html2_1 = $OPCloader->fetch($OPCloader, 'get_shipping_address_v2.tpl', $arr); 
				
				if (empty($html2_1))
				{
				  // theme file not found, please create or copy /overrides/get_shipping_address_v2.tpl.php
				  /// ************** start of customer info / shipping address
				    
					foreach ($BTaddressNamed as $key=>$val)
					 {
					   if (!empty($adr1->$key))
					    $BTaddressNamed[$key]['value'] = $adr1->$key; 
					   else 
					    unset($BTaddressNamed[$key]); 
					 }
					
					
					 
				  	$vars = array ('BTaddress' => $BTaddressNamed, 
									'edit_link' => $adr1->edit_link ); 
					
					$html2_1 = $OPCloader->fetch($OPCloader, 'customer_info.tpl', $vars); 
					
					
					$edit_label = OPCLang::_('JACTION_EDIT'); 
					if ($edit_label == 'JACTION_EDIT') $edit_label = OPCLang::_('EDIT'); 
					$html2_1 = str_replace(OPCLang::_('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL'), $edit_label, $html2_1); 

					/// ************** end of customer info
				}
				
				
				if (!empty($cart->ST['virtuemart_country_id']))
				$dc = $cart->ST['virtuemart_country_id']; 
				else
				$dc = OPCloader::getDefaultCountry($cart, true); 				

				$hasmissing = $OPCloader->hasMissingFieldsST($cart->ST); 
				
				
				$html2_id = '<div '; 
				if (empty($hasmissing))
				$html2_id .= ' style="display: none;" '; 
				$html2_id .= ' id="opc_stedit_'.$adr1->virtuemart_userinfo_id.'">'; 
				$html2_id .= ' <input type="hidden" name="st_complete_list" value="'.$adr1->virtuemart_userinfo_id.'" />';
				$gf = $OPCloader->getSTfields($OPCloader, true, true, $dc); 
				
				//do_dump($gf); echo '<br /><br />'; 
				$html2_id .= $gf; 
				$html2_id .= '</div>';  
				
				$html2_id = str_replace('id="', 'id="REPLACE'.$adr1->virtuemart_userinfo_id.'REPLACE', $html2_id); 
				$html2_id = str_replace('name="', 'name="REPLACE'.$adr1->virtuemart_userinfo_id.'REPLACE', $html2_id); 
				
				$html2 = '<input type="hidden" id="opc_st_changed_'.$adr1->virtuemart_userinfo_id.'" name="opc_st_changed_'.$adr1->virtuemart_userinfo_id.'" value="'; 
				if (!empty($hasmissing)) $html2 .= '1'; 
				else $html2 .= '0'; 
				$html2 .= '" />';
				$html2 .= '<div '; 
				if (!empty($hasmissing))
				$html2 .= ' style="display: none;" '; 
				$html2 .= ' id="opc_st_'.$adr1->virtuemart_userinfo_id.'">'.$html2_1.'</div>'.$html2_id; 
				
				if($i == 1) $i++;
				elseif($i == 2) $i--;
				
				
				
				if (!empty($STaddressList))
				{
				
				$html2 .= '<input type="hidden" name="shipto_logged" value="'.$adr1->virtuemart_userinfo_id.'" />'; 
				}
				else
				{
				  $html2 .= '<input type="hidden" name="shipto_logged" value="new" />'; 
				}
				
				$html2 = '<div id="hidden_st_'.$adr1->virtuemart_userinfo_id.'" style="display: none;">'.$html2.'</div>'; 
				
				if ($returnHtml) return $html2; 
				
				if (!isset(OPCloader::$extrahtml)) OPCloader::$extrahtml = ''; 
				OPCloader::$extrahtml .= $html2; 
				
				
				return $html2; 
  }
  public static function getEditLink($uid, $stid)
  {
     $useSSL = VmConfig::get('useSSL', 0);
	 $x = VmVersion::$RELEASE;	
     if (version_compare($x, '2.0.3', '<')) 
	  {
	  return JRoute::_('index.php?option=com_virtuemart&view=user&task=editAddressSt&addrtype=ST&cid[]='.$uid.'&virtuemart_userinfo_id='.$stid, true, $useSSL); 
	    
	  }
	  return JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=ST&cid[]='.$uid.'&virtuemart_userinfo_id='.$stid, true, $useSSL); 
	  
	  
	  
	  
  }
  
 public static function getSTHtml(&$cart)
 {
      $html = ''; 
      $stId = JRequest::getVar('ship_to_info_id', 0); 
	   $stId = (int)$stId; 
	   if (!empty($stId))
	    {
		   $user_id = JFactory::getUser()->get('id'); 
		   if (!empty($user_id))
		    {
			  $db = JFactory::getDBO(); 
			  $q = 'select * from #__virtuemart_userinfos where virtuemart_userinfo_id = '.$db->escape($stId).' limit 0,1'; 
			  $db->setQuery($q); 
			  $adr1 = $db->loadObject(); 
			  if (!empty($adr1))
			  if ($adr1->virtuemart_user_id == $user_id)
			   {
			   /*
			   $new_adr1 = new stdClass(); 
			   foreach ($adr1 as $key=>$val)
			    {
				  $new_adr1->$key = $val; 
				}
				*/
			   
			   
			   require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
				$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
				
				$virtuemart_userinfo_id = 0; 
				$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				
			
				
				$userDetails = $umodel->getUser();
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				
				$userFields = $umodel->getUserInfoInUserFields('default', 'BT', $virtuemart_userinfo_id);
				$userFields = $umodel->getUserInfoInUserFields('default', 'ST', $stId);
				
			   
			   
			     /*
				
				 */
				 
				 if (method_exists($cart, 'prepareAddressDataInCart'))
			     $cart->prepareAddressDataInCart('ST', 1);
			   
			   
			    if (isset($cart->STaddress['fields']))
				 $BTaddress = $cart->STaddress['fields']; 
				 else
				 $BTaddress = $cart->BTaddress['fields']; 
				 
				 $new_address_link = '#" onclick="return Onepage.op_showEditST();';
				 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
				 $OPCloader = new OPCloader(); 
				 
				 $BTaddressNamed = $OPCloader->setCountryAndState($BTaddress); 
				 $html = self::renderNamed($BTaddressNamed, $adr1, $cart, $OPCloader, $virtuemart_userinfo_id, true); 
				 
				 
				  
			   }
			}
		}
	return $html; 
 } 
}			