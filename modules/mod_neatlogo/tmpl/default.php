<?php defined('_JEXEC') or die(''); ?>



<?php
	$uniqueID = 'nl-id' . $module->id;
	$inlineStyle = $params->get('highZ', FALSE) ? 'position: relative; z-index: 999;' : '';
?>

<div class="neatlogo <?php echo $params->get('fade', FALSE) ? 'fade ' : ''; echo $params->get('align', inherit).' '.$uniqueID?>">

	<?php echo $params->get('linkHome', TRUE) ? '<a href="./" alt="Homepage">' : '' ?>

			<img class="nl-image" src="<?php echo $params->get('imagePath') ?>" title="<?php echo $params->get('imageTitle') ?>" alt="<?php echo $params->get('imageAlt', 'Logo') ?>" style="<?php echo $inlineStyle ?>" />

	<?php echo $params->get('linkHome', TRUE) ? '</a>' : '' ?>

</div> <!-- /neatlogo -->