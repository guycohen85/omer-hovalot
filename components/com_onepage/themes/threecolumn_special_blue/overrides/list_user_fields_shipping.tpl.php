<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	/**
	 * This allows us to print the user fields on
	 * the various sections of the shop
	 *
	 * @param array $rowFields An array returned from ps_database::loadObjectlist
	 * @param array $skipFields A one-dimensional array holding the names of fields that should NOT be displayed
	 * @param ps_DB $db A ps_DB object holding ovalues for the fields
	 * @param boolean $startform If true, print the starting <form...> tag
	 * 
	 * content of this file is a modification of function listUserFields( $rowFields, $skipFields=array(), $db = null, $startForm = true ) 
	 * of Virtuemart 1.1.7
	 */
	 
	{
/*		global $mm_action_url, $ps_html, $VM_LANG, $my, $default, $mainframe, $vm_mainframe,
			$vendor_country_3_code, $mosConfig_live_site, $mosConfig_absolute_path, $page;
*/		
		
		
		// we can overrride the default shipping country here
  $current_lang = &JFactory::getLanguage();
  
  if (!empty($current_lang))
  if (isset($current_lang->_lang))
  if (!empty($default_country_array))
  if (!empty($default_country_array[$current_lang->_lang]))
  if (strlen($default_country_array[$current_lang->_lang])==3)
   $default_shipping_country = $default_country_array[$current_lang->_lang];
		
		$default['country'] = $default_shipping_country;
		
		$missing = '';

		// collect all required fields
		$required_fields = Array(); 

		
		
		
		
	
		echo '
		<div style="width:100%;">';
		
		// Form validation function
		
		$delimiter = 0;
		
	   	foreach( $rowFields['fields'] as $field) {
			if (empty($field['type'])) continue;
			if (empty($field['readonly'])) $field['readonly'] = false;
			
			
			if (!empty($shipping_obligatory_fields))
			{
			$name = str_replace('shipto_', '', $field['name']); 
		   if (in_array($name, $shipping_obligatory_fields))
			{
			  $field['title'] .= ' *'; 
			}
			}
	   		// Title handling.
	   		$key = $field['title'];
			
	   		if( $field['name'] == 'agreed') {
				// we've got this in the unlogged, logged file
				continue; 
	   		}
	   		if( $field['name'] == 'username' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' ) {
				echo '<div class="formLabel">
						<input type="checkbox" id="register_account" name="register_account" value="1" class="inputbox" onclick="return Onepage.showFields( this.checked, new Array(\'username\', \'password\', \'password2\') );if( this.checked ) { document.adminForm.remember.value=\'yes\'; } else { document.adminForm.remember.value=\'yes\'; }" checked="checked" />
						<label for="register_account">'.OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER').'</label>
					</div>
					';
			} elseif( $field['name'] == 'username' ) {
				echo '<input type="hidden" id="register_account" name="register_account" value="1" />';
			}
	   		// a delimiter marks the beginning of a new fieldset and
	   		// the end of a previous fieldset
	   		if( $field['type'] == 'delimiter') {
	   			if( $delimiter > 0) {
	   				echo '<div class="op_hr" >&nbsp;</div>';
	   			}
	   			if( VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION' && $field->title == OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_CUST_INFO_LBL') && $page == 'checkout.index' ) {
	   				continue;
	   			}
	   			echo '
				    
';
	   			$delimiter++;
	   			continue;
	   		}
			

	   		
							

	      	echo ' 
	      <div class="formField" id="'.$field['name'].'_input" title="'.$field['title'].'">'."\n";
			echo '<span id="'.$field['name'].'_div" style="" class="formLabel ';
	   		if (stristr($missing,$field['name'])) {
	   			echo 'missing';
	   		}
	   		echo '">Missing</span>';
$arr = array('select', 'dropdown','multicheckbox', 'multiselect', 'radio','checkbox' );

			if (in_array($field['type'], $arr))
			{
	        echo '<label class="label_selects" style="clear: both; " for="'.$field['name'].'_field">';
			echo $field['title']; 
			echo '</label>'; 
			}	      	
	      	/**
	      	 * This is the most important part of this file
	      	 * Here we print the field & its contents!
	      	 */
			$field['formcode'] = str_replace('type="text"', 'onfocus="inputclear(this)" type="text"', $field['formcode']); 
	   		switch( $field['name'] ) {
	   			case 'title':
					echo '<div class="before_select"></div><div class="middle_select" style=""><div class="after_select" style="" >&nbsp;</div>';
	   				echo $field['formcode'];
					echo '</div>';
	   				break;
	   			
	   			case 'country':
	   				
					echo '<div class="before_select"></div><div class="middle_select" style=""><div class="after_select" style="" >&nbsp;</div>';
	   				echo $field['formcode'];
					echo '</div>';
	   				break;
	   			
	   			case 'state':
	   				
					echo '<div class="before_select"></div><div class="middle_select" style=""><div class="after_select" style="" >&nbsp;</div>';
	   				echo $field['formcode']; 
					//$state = str_replace('class="inputbox"', 'onblur="javascript: clearState(this);" onfocus="javascript: clearState(this);" class="inputbox"', $state); 
					echo $state;
					echo '</div>';
	   				break;
				case 'agreed':
					echo $field['formcode'];
					break;
				case 'password':
				case 'password2':
				    echo '<div class="before_input"></div><div class="middle_input">'; 
					//echo '<input type="password" id="'.$field->name.'_field" name="'.$field->name.'" size="13" class="inputbox" />'."\n";
							$ind = strlen($field['title'])-1; 
							if (substr($field['title'], $ind) != '*') 
							$field['title'] .= '*';
							echo '<input type="password" id="'.$field['name'].'_field" value="" alt="'. $field['title'] .'" onfocus="inputclear(this)" name="'.$field['name'].'" class="inputbox"  '.$maxlength . $readonly . ' />'."\n";
							echo '<input type="hidden" id="saved_'.$field['name'].'_field" name="savedtitle" value="'. $field['title'] .'" />';
							
							if (empty($field['value']))
							echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields">'.$field['title'].'</label>';
							else
							echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields"></label>';

					echo '<div class="after_input">&nbsp;</div></div>';
		   			break;
					
	   			default:
	   				
	   				switch( $field['type'] ) {
	   					case 'date':
							echo '<div class="before_input"></div><div class="middle_input">'; 
							echo $field['formcode']; 
							echo '<input type="hidden" id="saved_'.$field['name'].'_field" name="savedtitle" value="'.$value.'" />';
							echo '<div class="after_input">&nbsp;</div></div>';
					        echo '<input name="reset" type="reset" class="button" onclick="return showCalendar(\''.$field['name'].'_field\', \'y-mm-dd\');" value="..." />';
	   						break;
	   					case 'text':
	   					case 'emailaddress':
	   					case 'webaddress':
	   					case 'euvatid':	   			
							if ($field['name'] == 'username')
							if (substr($field['title'], strlen($field['title'])-1)!='*') $field['title'] .= '*';
	   						echo '<div class="before_input"></div><div class="middle_input">'; 
							
					        //echo '<input type="text" id="'.$field['name'].'_field" alt="'. $field['title'] .'" onfocus="inputclear(this)" name="'.$field['name'].'" value="" class="inputbox" '.$maxlength . $readonly . ' />'."\n";
							echo $field['formcode']; 
							echo '<input type="hidden" id="saved_'.$field['name'].'_field" name="savedtitle" value="'. $field['title'] .'" />';
							
														if (empty($field['value']))
							echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields">'.$field['title'].'</label>';
							else
							echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields"></label>';

							
							//echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields">'.$field['title'].'</label>';
							echo '<div class="after_input">&nbsp;</div></div>';
							
				   			break;
				   			
						
						case 'multicheckbox':
						case 'checkbox':
						  echo '<div class="usercheckbox">'; 
							echo $field['formcode']; 
							echo '</div>'; 
							break;
						case 'radio':
						
						    echo '<div class="userradio">'; 
							echo $field['formcode']; 
							echo '</div>'; 
							break;
						case 'age_verification':
							echo '<div class="before_input"></div><div class="middle_input" >'; 
							echo $field['formcode']; 
							echo '<input type="hidden" id="saved_'.$field['name'].'_field" name="savedtitle" value="'.$field['title'].'" />';
							//echo '<input name="reset" type="reset" class="button" style="position: relative; right: 0px; top: 0px;" onclick="return showCalendar(\''.$field['name'].'_field\', \'y-mm-dd\');" value="..." />';
							echo '<div class="after_input">&nbsp;</div></div>';
					        
	   						break;
						case 'editorta':
							$field['formcode'] = str_replace('style="width: 300px;', 'style="width:100px;', $field['formcode']); 
							echo $field['formcode'];
							break; 
	   					case 'date':
							echo '<div class="before_input"></div><div class="middle_input" >'; 
							echo $field['formcode']; 
							echo '<input type="hidden" id="saved_'.$field['name'].'_field" name="savedtitle" value="'.$field['title'].'" />';
							//echo '<input name="reset" type="reset" class="button" style="position: relative; right: 0px; top: 0px;" onclick="return showCalendar(\''.$field['name'].'_field\', \'y-mm-dd\');" value="..." />';
							echo '<div class="after_input">&nbsp;</div></div>';
					        
	   						break;
						case 'delimeter':
							echo '<div style="margin-top: 20px; clear: both;">&nbsp;</div>'; 
							break;
						case 'select':
						case 'multiselect':
						
							echo '<div class="before_select"></div><div class="middle_select" style=""><div class="after_select" style="" >&nbsp;</div>';
							echo $field['formcode']; 
							echo '</div>';
							break;
						default: 
						  echo '<div class="before_input"></div><div class="middle_input">'; 
						  echo $field['formcode']; 
						  echo '<input type="hidden" id="saved_'.$field['name'].'_field" name="savedtitle" value="'. $field['title'] .'" />';
						  	
							if (empty($field['value']))
							echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields">'.$field['title'].'</label>';
							else
							echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields"></label>';

							
						  //echo '<label for="'.$field['name'].'_field" id="label_'.$field['name'].'_field" class="userfields">'.$field['title'].'</label>';
						  echo '<div class="after_input">&nbsp;</div></div>';
						  break;
	   				}
	   				break;
	   		}
	   		
	   		echo '</div>
				  ';
	   }
		if( $delimiter > 0) {
		
		if( !empty( $required_fields ))  {
			echo '<div style="clear: both; padding:5px;text-align:center;"><strong>(* = '.OPCLang::_('CMN_REQUIRED').')</strong></div>';
		  	 
		}
			echo "\n";
		}
	   
	
	   	   echo '</div>';

	}


?>