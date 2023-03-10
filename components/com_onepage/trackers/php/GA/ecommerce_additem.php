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
?>

 ga('ecommerce:addItem', {
      'id': "<?php  echo $this->escapeDouble($idformat); ?>",
      'sku': "<?php echo $this->escapeDouble($order_item->order_item_sku); ?>",
      'name': "<?php echo $this->escapeDouble($order_item->order_item_name); ?>",
      'category': "<?php echo $this->escapeDouble($order_item->category_name ); ?>",
      'price': "<?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>",
      'quantity': "<?php echo number_format($order_item->product_quantity , 0, '.', ''); ?>",
	  'currency': '<?php echo $this->order['details']['BT']->currency_code_3; ?>'
   });