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


$iter = 0;

echo $intro_article; 
/*
 Anything before and after this template is generated by a template file /pages/shop.cart.tpl.php of your VM template
 There you can change page title from Cart to Checkout and erase any text and button that are genereated under the checkout page 
*/
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
//echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on


if (!empty($checkoutAdvertises)) {
?>
<div id="checkout-advertise-box">
		<?php
		if (!empty($checkoutAdvertises)) {
			foreach ($checkoutAdvertises as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>
<?php 
}
?>


<?php if (!empty($google_checkout_button)) { ?>
<div id="op_google_checkout" >
 <?php echo $google_checkout_button;  // will load google checkout button if you have powersellersunite.com/googlecheckout installed
 ?>
</div>
<?php } ?>

<br style="clear: both;" />
<!-- start main onepage div, if javascript fails it will remain hidden -->
<div class="dob0log">





<!-- start of checkout form -->
<form action="<?php $action_url; ?>" method="post" name="adminForm" novalidate="novalidate" data-ajax="false">
    <div class="dob1" id="dob1">
        <!-- user info details -->
        <h4><?php 
		$iter++;
		echo $iter.'. '.OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?> </h4>
        <?php echo $op_userfields; ?>
        
        <!-- end of user info details -->
		<br style="clear: both;" />
        <!-- ship to address details -->
        <?php if (NO_SHIPTO != '1') { ?>

<div data-role="fieldcontain">
<fieldset data-role="controlgroup" data-type="horizontal" >
<legend><h4><?php $iter++; echo $iter.'. ';  echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></h4>                           		</legend>
        
        <?php 
		
		
		echo $op_shipto; // user data and his shipping addresse, they are fetched from checkout/get_shipping_address.tpl.php ?>
        
</fieldset>
</div>        
        <?php } ?>
        <!-- end ship to address details -->
        
        
    </div>
    
    <div class="dob2" id="dob2">
	<div class="op_inside" <?php if (!empty($no_shipping) || ($shipping_inside_basket)) echo 'style="display: none;"'; ?>>
	<!-- shipping methodd -->
	<?php 
	if ((empty($no_shipping)) && (empty($shipping_inside_basket))) {
	?>
	<h4><?php 
	$iter++; echo $iter.'. '; echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></h4>
	<?php } ?>
        <div id="ajaxshipping">
        <?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
        </div>
	 </div>
        <!-- end shipping methodd -->
        <?php if (!empty($op_payment))
            {
			?>
<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >

            

            <h4><?php $iter++; echo $iter.'. ';  echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?> </h4>



            <?php echo $op_payment; ?>
            
            
            </div>
            <?php 
			
            } 
            ?>
     </div>
   
    
    <div class="dob3" id="dob3">
<h4><?php $iter++; echo $iter.'. '; echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></h4>
<div >
<div id="totalam" >
<div class="ui-grid-a" id="tt_order_subtotal_div" ><div id="tt_order_subtotal_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_subtotal" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_order_payment_discount_before_div" ><div id="tt_order_payment_discount_before_txt" class="bottom_totals_txt ui-block-a"></div><div class="bottom_totals ui-block-b" id="tt_order_payment_discount_before"></div></div>
<div class="ui-grid-a" id="tt_order_discount_before_div"><div id="tt_order_discount_before_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_discount_before" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_shipping_rate_div"><div id="tt_shipping_rate_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_shipping_rate" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_shipping_tax_div"><div id="tt_shipping_tax_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_shipping_tax" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_0_div"><div id="tt_tax_total_0_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_0" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_1_div"><div id="tt_tax_total_1_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_1" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_2_div"><div id="tt_tax_total_2_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_2" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_3_div"><div id="tt_tax_total_3_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_3" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_4_div"><div id="tt_tax_total_4_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_4" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_order_payment_discount_after_div"><div id="tt_order_payment_discount_after_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_payment_discount_after" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_order_discount_after_div"><div id="tt_order_discount_after_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_discount_after" class="bottom_totals ui-block-b"></div></div>
<div id="tt_genericwrapper_bottom" class="ui-grid-a dynamic_lines_bottom" style="display: none;"><div class="bottom_totals_txt ui-block-a dynamic_col1_bottom">{dynamic_name}</div><div class="bottom_totals ui-block-b dynamic_col2_bottom">{dynamic_value}</div></div>
<div class="ui-grid-a" id="tt_total_div"><div id="tt_total_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_total" class="bottom_totals ui-block-b"></div></div>
</div>
<div class="op_hr" >&nbsp;</div>
</div>
                           
	   
       
        
        <!-- customer note box -->
         <div >
							 <span id="customer_note_input" class="">
								<label for="customer_note_field"><?php 
								    $comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; 
									?>:</label>
							   <textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" ></textarea>
							
							 </span>
							 <br style="clear: both;" />
							 
          </div>
        <!-- end of customer note -->
        <!-- show TOS and checkbox before button -->
     
                                     	 <div id="rbsubmit" >
                        	   <!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->
<div id="onepage_info_above_button">

<?php
/*
 content of next divs will be changed by javascript, please don't change it's id, you may freely format it and if you add any content of txt fields it will not be overwritten by javascript 
*/
?>
<?php 
/*
* END of order total at the bottom
*/
?>


<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->
 <br />
 
 <!-- show TOS and checkbox before button -->
<?php
	



if ($show_full_tos) {

 ?>
<!-- show full TOS -->
	
<?php echo $tos_con; ?>
<!-- end of full tos -->
<?php } 
if ($tos_required)
{

{

?>
	<div id="agreed_div" class="formLabel " >
	<input value="1" type="checkbox" id="agreed_field"  name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service" <?php if (VmConfig::get('agree_to_tos_onorder', 1)) echo ' required="required" '; ?> autocomplete="off" />

					<label for="agreed_field"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					JHTML::_('behavior.modal', 'a.opcmodal'); 
					?><a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " ><br />
					<?php 
					$text = OPCLang::_('COM_VIRTUEMART_CART_TOS'); 
					$text = trim($text); 
					if (!empty($text))
					{
					?>
					(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)
					<?php 
					}
					?>
					</a><?php } ?></label>
				
		
	</div>
	<div class="formField" id="agreed_input">
</div>


<?php
}

}
?>

<!-- end show TOS and checkbox before button -->

 <div>
 <div id="payment_info"></div>
	<button id="confirmbtn_button" type="submit" <?php echo $op_onclick; ?> ><h4 id="confirmbtn"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></h4></button>
 </div>
<br style="clear: both;"/>
</div>
<!-- end of submit button -->




                        	 </div>   

      
            </div>

<!-- end of submit button -->
<?php
echo $captcha; 
?>


        
</form>


<!-- end of checkout form -->
<!-- end of main onepage div, if javascript fails it will remain hidden -->

    </div>
<div id="tracking_div"></div>
<script type="text/javascript">

</script>

<script>
var opc_autoresize = false; 
</script>

<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />
<div style="min-height: 400px; width: 100%;">&nbsp;</div>