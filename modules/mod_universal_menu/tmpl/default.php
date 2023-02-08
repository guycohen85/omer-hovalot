<?php
/**
 * @package 	mod_universal_menu
 * @version		1.8.24
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
?>

<?php if ($mobileMenuIcon) { ?><a href="#" onclick="return false;" class="unim-icon <?php echo $unique_class . ($cssTheme ? ' ' . $cssTheme : ''); ?>">
	<span>&equiv;</span><?php if($mobileMenuTitle) echo ' <b>' . $mobileMenuTitle . '</b>'; ?>

</a><?php } ?>

<ul class="unim<?php echo $class_sfx . ($cssTheme ? ' ' . $cssTheme : ''); ?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php
foreach ($list as $i => &$item) :
	$class = 'item-'.$item->id;
	if ($item->anchor_css){
		$class .= ' '.$item->anchor_css;
	}
	
	if ($item->id == $active_id) {
		$class .= ' current';
	}

	if (in_array($item->id, $path)) {
		$class .= ' active';
	}
	elseif ($item->type == 'alias') {
		$aliasToId = $item->params->get('aliasoptions');
		if (count($path) > 0 && $aliasToId == $path[count($path)-1]) {
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $path)) {
			$class .= ' alias-parent-active';
		}
	}
	
	// Menu item's level
	$level = $item->level;
	
	// Is the item - category / categories of VirtueMart?
	$virtuemart_category = FALSE;
	$virtuemart_categories = FALSE;
	if ($virtuemart_component && isset($item->query) && $item->query['option'] == 'com_virtuemart' && $item->query['view'] == 'category') {
		if ($showParentCategory == '1' || ($showParentCategory == 'default' && $item->query['categorylayout'] != 'categories')) { $item->parent = $virtuemart_category = TRUE; } 
			// VirtueMart categories layout option, render only sub-categories and skip parent category!
			else { $virtuemart_categories = TRUE; }
	}

	if ($item->deeper) {
		$class .= ' deeper';
	}

	if ($item->parent) {
		$class .= ' parent';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	/* Subcategory styling element
	if ($item->parent) {
		echo '<i></i>';
	} deprecated in 1.2.4 */

	if (!$virtuemart_categories) {
		// Standard code
		echo '<li'.$class.'>';
		
		// Render the menu item.
		switch ($item->type) :
			case 'separator':
			case 'url':
			case 'component':
				require JModuleHelper::getLayoutPath('mod_universal_menu', 'default_'.$item->type);
				break;

			default:
				require JModuleHelper::getLayoutPath('mod_universal_menu', 'default_url');
				break;
		endswitch;
		
		// Render VirtueMart's subcategories.
		if ($virtuemart_category)
		{
			$level_vm = 1; // Render <ul></ul> as usually
			$catid = $item->query['virtuemart_category_id'];
			require JModuleHelper::getLayoutPath('mod_universal_menu', 'default_virtuemart');
		}	

		// The next item is deeper.
		if ($item->deeper) {
			// From 1.2.17: added class (also in default_virtuemart.php)
			echo '<ul class="submenu level' . $level . '">';
		}
		// The next item is shallower.
		elseif ($item->shallower) {
			echo '</li>';
			echo str_repeat('</ul></li>', $item->level_diff);
		}
		// The next item is on the same level.
		else {
			echo '</li>';
		}
	}
	else {
		// Render VirtueMart's subcategories on current level.
		$catid = $item->query['virtuemart_category_id'];
		$level_vm = 0; // Don't render <ul></ul>
		require JModuleHelper::getLayoutPath('mod_universal_menu', 'default_virtuemart');
	}
	
endforeach;
?></ul>
