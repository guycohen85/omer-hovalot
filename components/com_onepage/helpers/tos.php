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

class OPCTos {
 public static function getShowFullTos(&$ref, $OPCloader)
 {
  
  
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
  $l = $OPCloader->logged($ref->cart); 
  if (!empty($l))
  {
   // logged
   if (!isset($full_tos_logged)) return VmConfig::get('oncheckout_show_legal_info', 0);  
   
   return (!empty($full_tos_logged)); 
  }
  else 
  {
   // unlogged
   if (!isset($full_tos_unlogged)) return VmConfig::get('oncheckout_show_legal_info', 0);  
   
   return (!empty($full_tos_unlogged)); 
   
  }
  return VmConfig::get('oncheckout_show_legal_info', 0);  
  
 
 }
 
  public static function getTosRequired(&$ref, &$OPCloader)
 {
 

 
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 
  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
			$userFieldsModel = OPCmini::getModel('Userfields'); // new VirtueMartModelUserfields();
			if($userFieldsModel->getIfRequired('agreed'))
			{
				if(!class_exists('VmHtml'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
				$tos_required = true; 
			}
			else $tos_required = false;


 $l = $OPCloader->logged($ref->cart); 
 if (!empty($l))
 {
 // logged
 if (!empty($tos_logged)) return true; 
 if (!isset($tos_logged))
  {
    return $tos_required; 
  }
 else return (!empty($tos_logged)); 
 }
 else
 {
   if (!empty($tos_unlogged)) return true; 
   if (!isset($tos_unlogged)) return $tos_required; 
   return (!empty($tos_unlogged)); 
 }
	
	return $tos_required; 
 }

 public static function getTosLink(&$ref, &$OPCloader)
 {
 
 
 $cart = $ref->cart; 
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 
 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
 
 $opclang = JFactory::getLanguage()->getTag(); 
 
 $tos_config = OPCconfig::getValue('opc_config', 'tos_config', 0, 0, true); 
 
 if (empty($tos_config) || (!is_numeric($tos_config)))
 {

 $itemid = JRequest::getVar('Itemid', ''); 
 if (!empty($itemid)) $itemid = '&Itemid='.$itemid; 
 else $itemid=''; 
 
 $tos_link = $OPCloader->getUrl().'index.php?nosef=1&format=html&option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=' . $cart->vendor->virtuemart_vendor_id.'&tmpl=component'.$itemid;
 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'version.php'); 
 $x = VmVersion::$RELEASE;
 if (strpos($x, '${PHING.VM.RELEASE}')===false)
 if (!version_compare($x, '2.0.2', '>=')) return ""; 
 }
 else 
  {
    //if (!empty($newitemid))
    //$tos_link = JRoute::_('index.php?option=com_content&view=article&id='.$tos_config.'&tmpl=component&Itemid='.$newitemid);
	//else 
    $tos_itemid = OPCconfig::getValue('opc_config', 'tos_itemid', 0, 0, true); 
	if (!empty($tos_itemid))
	$tos_link = JRoute::_('index.php?option=com_content&view=article&id='.$tos_config.'&tmpl=component&Itemid='.$tos_itemid);
	else
	$tos_link = JRoute::_('index.php?option=com_content&view=article&id='.$tos_config.'&tmpl=component');
  }
 
 
 
 
 
 $b1 = JURI::root(true); 
 if (!empty($b1))
 if (strpos($tos_link, $b1) === 0) $tos_link = substr($tos_link, strlen($b1)); 
 
 
 
			if (strpos($tos_link, 'http')!==0)
			 {
			   $base = JURI::root(); 
			   if (substr($base, -1)=='/') $base = substr($base, 0, -1);
			   
			   if (substr($tos_link, 0, 1)!=='/') $tos_link = '/'.$tos_link; 
			   
			   $tos_link = $base.$tos_link; 
			   
			 }
			 if (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
				$tos_link = str_replace('http:', 'https:', $tos_link); 
			 }
			 
			 return $tos_link;
 } 


}