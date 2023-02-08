<?php
/**
 * @package 	mod_universal_menu
 * @version		1.10.1
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';

// Get a HTML code of the item's link
$linktype = modUniversalMenuHelper::htmlLinkType( $item, $imageTitle, $arrowPosition );

// Mega menu parser
// Proper syntax: { 101, |position-1|, module 132 }
if (strpos($item->title, '{') !== FALSE and strpos($item->title, '}') !== FALSE )
{
	// Get list of published modules
	$db = JFactory::getDBO();	
	$query = "
		SELECT *
		FROM #__modules
		WHERE published=1
		ORDER BY id
		;";
	$db->setQuery($query);
	$modulesList = $db->loadObjectList('id');
	
	
	// Parse modules info from title
	$moduleids = explode(',', trim($item->title, " {,}"));
	
	// Loop through module positions and IDs
	foreach ($moduleids as $moduleid)
	{
		$modname = '';
		$modtitle = '';	
		
		// Module position given
		if (substr_count($moduleid, '|') == 2) {
			$modposition = trim($moduleid, " |");
			foreach ($modulesList as $module) {
				if ($module->position == $modposition) {
					$modname = $module->module;
					$modtitle = $module->title;
					break;
				}
			}
		}
		// Module ID is given
		// Syntax examples: { 101, module 132 } 
		else {
			$moduleid = preg_replace('/module/i', '', $moduleid);		
			$moduleid = trim($moduleid, " {,=}");
			
			// Extract $modtitle and $modname
			if (isset($modulesList[$moduleid]->title)) {
				$modname = $modulesList[$moduleid]->module;
				$modposition = $modulesList[$moduleid]->position;
				$modtitle = $modulesList[$moduleid]->title;
			}
		}
		
		// If module is enabled
		if (JModuleHelper::isEnabled($modname)) {
			jimport('joomla.application.module.helper');
			$megamodule = JModuleHelper::getModule($modname, $modtitle);
			$attributes = array('style' => 'xhtml');
			echo JModuleHelper::renderModule($megamodule, $attributes);
		} else {
			// For debugging only
			echo 'Module ' . $moduleid . ' not found!<br />' . "\n";
		}
	}
}
else { ?>
	<span class="separator"><?php echo $title; ?><?php echo $linktype; ?></span>
<?php }
?>
