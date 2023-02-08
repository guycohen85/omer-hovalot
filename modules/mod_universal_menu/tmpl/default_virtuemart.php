<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Outputs one level of categories and calls itself for any subcategories
 *
 * @access	public
 * @param int $catPId (the category_id of current parent category)
 * @param int $level (the current category level [main cats are 0, 1st subcats are 1])
 * @param object $params (the params object containing all params for this module)
 * @param int $current_cat (category_id from the request array, if it exists)
 * @return nothing - echos html directly
 **/
 
// Because this function is declared in the view, need to make sure it hasn't already been declared:
if ( ! function_exists( 'vmFCLBuildMenu' ) ) {
	function vmFCLBuildMenu( $catPId = 0, $level = 1, $settings, $current_cat = 0, $active = array(), $level_vm = 1 ) {
		if ( (!$settings['level_end'] || $level < $settings['level_end']) && $rows = modUniversalMenuHelper::getCatChildren($catPId) ) {
			if ( $level_vm > 0 && $level >= $settings['level_start'] ) { ?>
			<ul class="submenu level<?php echo ($level + $level_vm - 1) ?>">
			<?php }
			foreach( $rows as $row ) :
				$cat_active = in_array( $row->virtuemart_category_id, $active );
				if ( $level >= $settings['level_start'] ) :
					$itemid = modUniversalMenuHelper::getVMItemId($row->virtuemart_category_id);
					$itemid = ($itemid ? '&Itemid='.$itemid : '');
					$link =	JFilterOutput::ampReplace( JRoute::_( 'index.php?option=com_virtuemart' . '&view=category&virtuemart_category_id=' . $row->virtuemart_category_id . $itemid ) );
					// Compose class for this item
					$class = 'cat-id-'.$row->virtuemart_category_id;
					if ($current_cat == $row->virtuemart_category_id) $class .= ' current';
					if ( $cat_active ) $class .= ' active';
					if (sizeof(modUniversalMenuHelper::getCatChildren($row->virtuemart_category_id)) > 0) {
						$class .= ' deeper parent';
						// Subcategory styling element
						$styling = ' <i></i>';
					} else $styling = '';
					?>
					<li class="<?php echo trim($class) ?>">
						<a class="level<?php echo $level ?>" href="<?php echo $link ?>">
						<?php echo htmlspecialchars(stripslashes($row->category_name), ENT_COMPAT, 'UTF-8') . $styling ?></a>
				<?php endif;
				// Check for sub categories
				if ($settings['show_all']) vmFCLBuildMenu( $row->virtuemart_category_id, $level, $settings, $current_cat, $active, $level_vm + 1 );
				if ($level >= $settings['level_start']) : ?>
				</li>
			<?php endif;
			endforeach;
			if ( $level_vm > 0 && $level >= $settings['level_start'] ) { ?>
			</ul>
			<?php }
		}
	}
}

// With what category, if any, do we start?
// Default to cat filter param:
if (!isset($catid)) $catid = 0;
if (!isset($level)) $level = 1;
if (!isset($level_vm)) $level_vm = 1;

// Set up current category array (for displaying '.active' class and for current category filter, if applicable)
$active = array();
if ( $current_cat ) {
	$active = modUniversalMenuHelper::getCatParentIds( $current_cat );
}

// Call the display function for the first menu item:
vmFCLBuildMenu( $catid, $level, $settings, $current_cat, $active, $level_vm );

?>
