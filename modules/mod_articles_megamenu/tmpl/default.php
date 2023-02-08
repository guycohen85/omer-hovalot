<?php
/**
 * @package		mod_articles_megamenu
 * @version		1.4.8
 */

// No direct access
defined('_JEXEC') or die;

// Extract wrapper/nasted listing tags.
@list($tag_wrapper, $tag_nested) = explode('-', $listing_tags);
if (($tag_wrapper != 'ul' && $tag_wrapper != 'div') || ($tag_nested != 'li' && $tag_nested != 'span' && $tag_nested != 'section')) {
	$tag_wrapper = 'div'; $tag_nested = 'div';
}
?>

<<?php echo $tag_wrapper; ?> class="newsflash-II<?php echo $moduleclass_sfx; ?>">
<?php for ($i = 0, $n = count($list); $i < $n; $i ++) {
	$item = $list[$i]; ?>
	<<?php echo $tag_nested; ?> class="newsflash-item newsflash-<?php echo ($i + 1); ?>">
<?php if ($intro_images && isset($item->images)) {
		$images = json_decode($item->images);
		if (isset($images->image_intro) and !empty($images->image_intro)) { ?>
		<div class="article-image">
			<?php
				$title = ($images->image_intro_caption ? ' title="'.htmlspecialchars($images->image_intro_caption).'"' : '');
				$output = '<img src="' . htmlspecialchars($images->image_intro) . '" alt="' . htmlspecialchars($images->image_intro_alt) . '"' . $title . ' />';
				if ($intro_image_link != 'none') {
					$image_link = NULL;
					/* This tries to extract a link from text source */
					if ($intro_image_link != 'readmore' && preg_match("#<a(.*)?href=\"([^\"]+)\".*?>#is", $item->introtext, $matches)) {
						if (strpos($matches[1], "readmore") !== FALSE) {
							$image_link = trim($matches[2]);
						}					
					}
					/* Link from read more */
					if ($image_link === NULL && $intro_image_link != 'article' && isset($item->link) && $item->readmore != 0) {
						$image_link = $item->link;
					}

					/* Update output */
					if ($image_link) {
						$output = '<a href="'.$image_link.'">'.$output.'</a>';		
					}
				}
				echo $output; ?>
		</div>
		<?php } ?>
<?php } ?>
		<div class="article-content">
<?php if ($item_title) { ?>
			<<?php echo $item_heading; ?> class="newsflash-title<?php echo $moduleclass_sfx; ?>">
<?php if ($link_titles && $item->link != '') { ?>
				<a href="<?php echo $item->link;?>">
					<?php echo $item->title;?></a>
<?php } else { ?>
				<?php echo $item->title; ?>
<?php } ?>
			</<?php echo $item_heading; ?>>
<?php } ?>
<?php if (!$intro_only) {
	echo $item->afterDisplayTitle;
} ?>
			<div class="newsflash-article">
				<?php echo $item->beforeDisplayContent . $item->introtext;
					if (isset($item->link) && $item->readmore != 0 && $showReadmore) {
				echo '<a class="readmore" href="' . $item->link . '">' . $item->linkText . '</a>' . PHP_EOL;
			} ?>
			</div>
		</div>
<?php if ($n > 1 && (($i < $n - 1) || $showLastSeparator)) { ?>
		<span class="article-separator">&#160;</span>
<?php } ?>
	</<?php echo $tag_nested; ?>>
<?php } ?>
</<?php echo $tag_wrapper; ?>>
