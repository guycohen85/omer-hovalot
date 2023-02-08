<?php defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];

if ($viewData['showRating']) {
	$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
	if (empty($product->rating)) {
	?>
<div class="vote"><?php echo vmText::_('COM_VIRTUEMART_UNRATED') ?></div>
		<div class="ratingbox dummy" >

		</div>
	<?php
	} else {
		$ratingwidth = $product->rating * 24;
  ?>
<div class="vote">
  <?php echo vmText::_('COM_VIRTUEMART_RATING') . ' ' . round($product->rating) . '/' . $maxrating; ?>
</div>
<div title=" <?php echo (vmText::_("COM_VIRTUEMART_RATING_TITLE") . round($product->rating) . '/' . $maxrating) ?>" class="ratingbox" >
  <div class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>"></div>
</div>
	<?php
	}
}