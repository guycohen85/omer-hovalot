<?php
/*
*
* @copyright Copyright (C) 2007 - 2014 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
?><table class="adminlist" width="100%">
	<thead>
<?php
/*
	
	<tr>
		<th>
		<a class="updateOrder" href="#"><span class="icon-nofloat vmicon vmicon-16-save"></span>
		<?php echo JText::_('COM_VIRTUEMART_ORDER_SAVE_USER_INFO'); ?></a>
		&nbsp;&nbsp;
		<a href="#" onClick="javascript:resetOrderHead(event);" ><span class="icon-nofloat vmicon vmicon-16-cancel"></span>
		<?php echo JText::_('COM_VIRTUEMART_ORDER_RESET'); ?></a>
		<!--
		&nbsp;&nbsp;
		<a class="createOrder" href="#"><span class="icon-nofloat vmicon vmicon-16-new"></span>
		<?php echo JText::_('COM_VIRTUEMART_ORDER_CREATE'); ?></a>
		-->
		</th>
	</tr>
*/
?>	
	<tr>
	  <td align="center"><div align="center">
	  <?php 
	  if (!empty($this->prev_order))
	  echo '<a class="pagenav" href="index.php?option=com_onepage&view=order_details&order_id='.$this->prev_order.'">&lt; '.JText::_('COM_VIRTUEMART_ITEM_PREVIOUS').'</a> '; 
	  else
	  echo '&lt; '.JText::_('COM_VIRTUEMART_ITEM_PREVIOUS');
	  echo '<span class="pagenav"> | </span>'; 
	  if (!empty($this->next_order))
	  echo '<a class="pagenav" href="index.php?option=com_onepage&view=order_details&order_id='.$this->next_order.'">'.JText::_('COM_VIRTUEMART_ITEM_NEXT').' &gt;</a> '; 
	  else 
	  echo '<span class="pagenav">'.JText::_('COM_VIRTUEMART_ITEM_NEXT').' &gt;</span>'; 
	  ?>
	  </div></td>
	</tr>
	</thead>
</table>
