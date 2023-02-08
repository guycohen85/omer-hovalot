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

if (empty($this->params->product_ident))
$id = 'virtuemart_product_id'; 
else
$id = 'order_item_sku'; 

?>

<script type="text/javascript">
var _hrq = _hrq || [];

_hrq.push(['setKey',               

'<?php echo $this->escapeSingle($this->params->heureka_key); ?>' 
]); 


_hrq.push(['setOrderId',

'<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>'
]);
<?php foreach ($this->order['items'] as $order_item) { ?>

_hrq.push(['addProduct',           

'<?php echo $this->escapeSingle($order_item->order_item_name); ?>',                    
                                     
  	                           

'<?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>',

'<?php echo number_format($order_item->product_quantity , 0, '.', ''); ?>'
]);
<?php } ?>

_hrq.push(['trackOrder']);
(function() {
var ho = document.createElement('script');
ho.type = 'text/javascript'; ho.async = true;
ho.src = ('https:' == document.location.protocol
? 'https://ssl' : 'http://www') +
'.heureka.cz/direct/js/cache/1-roi-async.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ho, s);
})();
</script>
<?php
if (!empty($this->params->allow_visitor_data))
 {


 include_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.'API'.DS.'HeurekaOvereno.php'); 
 try {
    $overeno = new OPCHeurekaOvereno($this->params->heureka_secret_key, OPCHeurekaOvereno::LANGUAGE_CZ);
    // SK shops should use $overeno = new HeurekaOvereno('9b011a7086cfc0210cccfbdb7e51aac8', HeurekaOvereno::LANGUAGE_SK);
    $email = $this->order['details']['BT']->email; 
    // set customer email - MANDATORY
    $overeno->setEmail($email);

    /**
     * Products names should be provided in UTF-8 encoding. The service can handle
     * WINDOWS-1250 and ISO-8859-2 if necessary             
     */
	foreach ($this->order['items'] as $key=>$order_item) { 
    $overeno->addProduct($order_item->order_item_name);
    /**
     * And/or add products using item ID
     */
    $overeno->addProductItemId($order_item->$id);
	}
    // add order ID - BIGINT (0 - 18446744073709551615)
    $overeno->addOrderId($this->order['details']['BT']->virtuemart_order_id);
    // send request
    $overeno->send();
} catch (HeurekaOverenoException $e) {
    // handle errors
   // print $e->getMessage();
}

 
 
 }