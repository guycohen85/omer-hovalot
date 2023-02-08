<?php 

/*------------------------------------------------------------------------------------------------------------

# VP ProMart! Joomla 2.5 Template for VirtueMart 2.0 Ver. 1.0.4

# ------------------------------------------------------------------------------------------------------------

# Copyright (C) 2012 VirtuePlanet Services LLP. All Rights Reserved.

# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html

# Author: VirtuePlanet Services LLP

# Email: info@virtueplanet.com

# Websites:  http://www.virtueplanet.com

------------------------------------------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');



//dump ($cart,'mod cart');

// Ajax is displayed in vm_cart_products

// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>

<div class="vm-mini-cart">

<!-- Virtuemart 2 Ajax Card -->

	<div class="vmCartModule <?php echo $params->get('moduleclass_sfx'); ?> vm-mini-cart-module" id="vmCartModule">

		<div class="cart-content">

			<div class="total_products">

				<?php echo $data->totalProductTxt ?>

			</div>
		<div class="hidden-cart-content">
			<div class="total">

				<?php if ($data->totalProduct) echo  $data->billTotal; ?>

			</div>

			<div class="show_cart">
				<?php if ($data->totalProduct) echo  $data->cart_show; ?>
			</div>
		</div>

		</div>
		

		<?php if ($show_product_list) { ?>

		<div class="hidden-cart-content">

			<div id="hiddencontainer">

				<div class="container">

					<div class="added-product">

						<?php if ($show_price) { ?>

					  		<div class="prices"></div>

						<?php } ?>

							<div class="product_row">

								<span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span>

							</div>

						<div class="product_attributes"></div>

					</div>

					<div class="show_cart"></div>

				</div>

			</div>

			

			<div class="vm_cart_products">

				<?php if($data->totalProductTxt != JText::_('COM_VIRTUEMART_EMPTY_CART')) {?>

				<div class="container">

				<?php foreach ($data->products as $product) {?>

				<div class="added-product">

				<?php	if ($show_price) { ?>

				  	<div class="prices"><?php echo  $product['prices'] ?></div>

					<?php } ?>

					<div class="product_row">

						<span class="quantity">

							<?php echo  $product['quantity'] ?>

						</span>&nbsp;x&nbsp;

						<span class="product_name">

							<?php echo  $product['product_name'] ?>

						</span>

					</div>

					<?php if ( !empty($product['product_attributes']) ) { ?>

					<div class="product_attributes">

						<?php echo  $product['product_attributes'] ?>

					</div>					

					<?php } ?>

				</div>

				<?php } ?>

				<div class="show_cart">

					<?php if ($data->totalProduct) echo  $data->cart_show; ?>

				</div>

				</div>



				<?php } ?>

			</div>



		</div>

		<?php } ?>

	

	

		

		<noscript>

			<?php echo JText::_('MOD_VIRTUEMART_CART_AJAX_CART_PLZ_JAVASCRIPT') ?>

		</noscript>

					

	</div>

</div>





