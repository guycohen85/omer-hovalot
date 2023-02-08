<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
$order_total = $this->order['details']['BT']->order_total;

if (!empty($this->params->universalga)) $uga = 'true'; 
else $uga = 'false'; 

// generic fix: 
if (empty($this->order['details']['BT']->currency_code_3))
$this->order['details']['BT']->currency_code_3 = 'USD'; 

if (empty($this->params->idformat))
{
  $idformat = $this->order['details']['BT']->virtuemart_order_id; 
 
}
else
if ($this->params->idformat===1)
{
  $idformat = $this->order['details']['BT']->virtuemart_order_id.'_'.$this->order['details']['BT']->order_number;
}
else
if ($this->params->idformat==2)
 {
   $idformat = $this->order['details']['BT']->order_number; 
 }


?>
<script type="text/javascript">
//<![CDATA[
  
   if ((<?php echo $uga; ?>) && ((typeof ga == 'undefined') && (!((typeof gaJsHost == 'undefined') || (typeof _gat == 'undefined')))))
   {
    <?php include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ga_init.php'); 
    include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'pageview.php'); ?>
   }
   else
   {
   
  if ((typeof gaJsHost == 'undefined') || (typeof _gat == 'undefined'))
   {
   
      <?php include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'_gat_init.php'); ?>
   }
   }
//]]>
</script>
<script type="text/javascript">
//<![CDATA[

  if (typeof ga != 'undefined')
   {
      <?php 
	  if (!empty($this->params->ec_type)) 
	  { 
	  // if normal ec
	   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ecommerce_init.php'); 	
 	   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ecommerce_addtransaction.php'); 	
		
	foreach ($this->order['items'] as $key=>$order_item) 
	{ 
   // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each 
   if (empty($order_item->category_name)) $order_item->category_name = ''; 
   if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   
    include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ecommerce_additem.php'); 	  
   
	} 
		
		
	    include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ecommerce_send.php'); 
		include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'pageview.php'); 
		

	  }  //if normal ec
	  else 
	  {   //if enhanced
	
		// enhanced ecommerce GA
		include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ec_init.php'); 
		if ($order_total < 0)
		{
		include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ec_refund.php'); 
		}
		else
		{		
			foreach ($this->order['items'] as $key=>$order_item) 
			{ 
			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each 
			if (empty($order_item->category_name)) $order_item->category_name = ''; 
			if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   
			include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ec_addproduct.php'); 
			} 
			//end of foreach
			$action = 'purchase'; 
			include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'ec_action.php'); 
	    
		}
	  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'pageview.php'); 
	 
	}  // end if enhanced ec
	
	?>
   
   }
   else
   {

  var pageTracker = _gat._getTracker("<?php echo $this->params->google_analytics_id; ?>");
  pageTracker._trackPageview();
  <?php include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'_gat_addtrans.php');  

  foreach ($this->order['items'] as $key=>$order_item) { ?>
   // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each 
   <?php if (empty($order_item->category_name)) $order_item->category_name = ''; 
   if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'GA'.DS.'_gat_additem.php');  
   
   } 
   ?>
   pageTracker._trackTrans(); //submits transaction to the Analytics servers
   
   }
  

//]]>
</script>