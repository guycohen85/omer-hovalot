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

class OPCThankYou {
  public static function updateHtml($html, &$order, $afterrender=false)
   {
      
      require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	  $ty_page = OPCconfig::getValue('ty_page', 'ty_page', 0, array());
	   
	  
	  if (empty($ty_page)) return; 
	  if (empty($order)) return; 
	 
	 
	 
	  if (is_array($order))
	  if (isset($order['details']))
	  $order = $order['details']['BT'];
	 
	  //we must reload order here in case any plugin had changed it in meantime: 
	  $id = $order->virtuemart_order_id; 
	   if (!empty($id))
	   {
		require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
		$orderModel = OPCmini::getModel('orders');
	    $order_full = $orderModel->getOrder($id);
		if (empty($order_full)) return; 
		$order = $order_full['details']['BT']; 
		
	   }
	  $status = $order->order_status; 
	  if (!is_object($order)) return; 
	  
	  $payment_id = $order->virtuemart_paymentmethod_id; 
	  if (empty($order->order_language))
	  {
	   $jlang = JFactory::getLanguage(); 
	   $cl = $jlang->getTag(); 
	   $cl = strtolower(str_replace('-', '_', $cl)); 
	  
	  }
	  else $cl = $order->order_language; 
	  
	  // check conditions: 
	  
	 
	  $orightml = $html; 
	  $todo = array(); 
	  foreach ($ty_page as $k=>$ty)
	  {
	     if (!is_object($ty)) continue; 
		 if ($ty->payment_id != $payment_id) continue; 
		 if (!empty($ty->language))
		 if ($ty->language != $cl) continue; 
		 if ($ty->order_status != $status) continue; 
		 $todo[] = $ty; 
		 

		 
	  }
	  
	  if (empty($todo)) return; 
	  /*
	  COM_ONEPAGE_TY_MODE_0="Prepend to payment generated html"
COM_ONEPAGE_TY_MODE_1="Append to payment generated html"
COM_ONEPAGE_TY_MODE_2="Replace payment generated html"
*/
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
	  
	    $repvals = array(); 
		$order_object = new stdClass(); 
		OPCtrackingHelper::getOrderVars($id, $repvals, $order_object, false); 
		
	  foreach ($todo as $ty2)
	  {
	  
	  
	    $htmla = OPCloader::getArticle($ty2->article_id, $repvals); 
		if (empty($ty2->mode))
		 {
		       $html = $htmla.'<br />'.$html;
  
		 }
		 else
		 if ($ty2->mode == 1)
		 {
		       $html = $html.'<br />'.$htmla; 
		 }
		 else
		 if ($ty->mode == 2)
		 $html = $htmla; 
		
	  }
	  
	  if (defined('TYMODDONE')) return $html; 
	  else define('TYMODDONE', true); 
	  
	  if ($orightml != $html)
	  {
	     if (!$afterrender)
		 {
			JRequest::setVar('html', $html); 
		 }
		 else
		 {
		 
		 
		   $buffer = JResponse::getBody();
		   $buffer = str_replace($orightml, $html, $buffer); 
		   JResponse::setBody($buffer);
		 }
	  }
	  return $html; 
	  
	  
   }
}