<?php
/**
* sublayout products
*
* @package	VirtueMart
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
* @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];
if(isset($viewData['class'])){
	$class = $viewData['class'];
} else {
	$class = 'product-fields';
}

if (!empty($product->customfieldsSorted[$position])) {
	?>
	<div class="<?php echo $class?>">
		<?php
		$custom_title = null;
		foreach ($product->customfieldsSorted[$position] as $field) {
			if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
			continue;
			?><div class="product-field product-field-type-<?php echo $field->field_type ?>">
				<?php if ($field->custom_title != $custom_title and $field->show_title) { ?>
					<span class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo vmText::_ ($field->custom_title) ?></strong></span>
						<?php if ($field->custom_tip) {
							echo JHtml::tooltip ($field->custom_tip, vmText::_ ($field->custom_title), 'tooltip.png');
						} ?></span>
				<?php }
				if (!empty($field->display)){
					?><span class="product-field-display"><?php echo $field->display ?></span><?php
				}
				if (!empty($field->display)){
					?><span class="product-field-desc"><?php echo vmText::_($field->custom_desc) ?></span> <?php
				}
				?>
			</div>
		<?php
			$custom_title = $field->custom_title;
		} ?>

	</div><div class="clear"></div>
<?php
} ?>