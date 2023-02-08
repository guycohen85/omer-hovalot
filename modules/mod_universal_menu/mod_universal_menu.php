<?php
/**
 * @package 	mod_universal_menu
 * @version		1.10.1
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

/* BASIC OPTIONS */
// Main module parameters
$showAllChildren	= $params->get('showAllChildren', 1);

// VirtueMart parameters
$loadVMCategories	= $params->get('loadVMCategories', 0);
$showParentCategory	= $params->get('showParentCategory', 'default');

// Module's style
$cssTheme			= $params->get('cssTheme', 'default');
$imageTitle			= $params->get('imageTitlePosition', 1);
$arrowPosition		= $params->get('arrowPosition', 1);

// Mobile menu parameters
$mobileMenuIcon 	= $params->get('mobileMenuIcon', 1);
$mobileMenuTitle	= $params->get('mobileMenuTitle', '');
$mobileMenuTitle = html_entity_decode($mobileMenuTitle, ENT_QUOTES, 'UTF-8');

/* JAVASCRIPT */
// JS/CSS module parameters
$javascriptMenu		= $params->get('javascriptMenu', 1);
$animationEffect	= $params->get('animationEffect', 'slide');
$animationDuration	= $params->get('animationDuration', 250);
$loadjQuery			= $params->get('loadjQuery', 'auto');

// Menu settings
$expandSubMenus 	= $params->get('expandSubMenus', 0);
$autoStretchMenu	= $params->get('autoStretchMenu', 0);
$toggleOnHover		= $params->get('toggleOnHover', 0);
$disableParentLink	= $params->get('disableParentLink', 0);

// Mobile menu and icon parameters
$responsiveMenu		= $params->get('responsiveMenu', 0);
$mobileViewportSize	= $params->get('mobileViewportSize', 480);

// Mobile theme overrides
if ($cssTheme=='mobile') {
	$mobileMenuIcon = 1; $responsiveMenu = 1;
	$mobileViewportSize = 2147483647;
}

/* ADVANCED OPTIONS */
// Generate unique menu class
$class_sfx	= htmlspecialchars($params->get('class_sfx'));
$unique_class = 'unim' . $module->id;
$class_sfx .= ' ' . $unique_class;

if ($loadVMCategories && file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php')) {
	// Process VirtueMart categories
	$virtuemart_component = TRUE;
	
	// Set up VirtueMart (based on example in mod_virtuemart_category)	
	if ( !class_exists('VmConfig') ) require(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
	$config = VmConfig::loadConfig();
	if ( !class_exists('VirtueMartModelVendor') ) require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'vendor.php');
	if ( !class_exists('TableMedias') ) require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'medias.php');
	if ( !class_exists('TableCategories') ) require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'categories.php');
	if ( !class_exists('VirtueMartModelCategory') ) require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'category.php');
	
	// Initialize public static properties
	modUniversalMenuHelper::$cache = JFactory::getCache('com_virtuemart', 'callback');
	modUniversalMenuHelper::$category_model = new VirtueMartModelCategory();
	
	// Set up variables
	$current_cat = JRequest::getInt( 'virtuemart_category_id', -1 );
	
	// VM categories
	$settings = array(
		'level_start' => (int) $params->get('startLevel'),
		'level_end' => (int) $params->get('endLevel'),
		'show_all' => $showAllChildren
	);
}
else {
	// Don't process VM categories or VirtueMart Component not installed
	$virtuemart_component = FALSE;
}

// Standard mod-menu's code
$list	= modUniversalMenuHelper::getList($params);
$app	= JFactory::getApplication();
$menu	= $app->getMenu();

$active	= $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path	= isset($active) ? $active->tree : array();


// Loading menu...
if (count($list)) {
	require JModuleHelper::getLayoutPath('mod_universal_menu', $params->get('layout', 'default'));
	
	// Include selected CSS-theme
	$document = JFactory::getDocument();
	if ($cssTheme && $cssTheme != 'none') {
		$document->addStyleSheet('modules/mod_universal_menu/assets/themes/unim-' . $cssTheme . '.css');
	}
	
	// Enable javascript menu scripts
	if ($javascriptMenu) {
		if ($loadjQuery == 1) {
			$jversion = new JVersion();
			if ($jversion->RELEASE >= 3) {
				JHtml::_('jquery.framework', true, null, false);
			}
			else {
				$document->addScript('modules/mod_universal_menu/assets/jquery.min.js');
			}
		}
		$document->addScript('modules/mod_universal_menu/assets/unim.js#' . $document->direction);
		
		// JS parameters
		$paramsToJS = '
universalMenu({
	unipath:"' . $unique_class . '",
	menuicon:' . $mobileMenuIcon . ',
	expand:' . $expandSubMenus . ',
	stretch:' . $autoStretchMenu . ',
	effect:"' . $animationEffect . '",
	duration:' . $animationDuration . ',
	tohover:' . $toggleOnHover . ',
	dplink:' . $disableParentLink . ',
	mobimenu:' . $responsiveMenu . ',
	mobisize:' . $mobileViewportSize . '
}' . ($loadjQuery == 'auto' ? ',1' : '') . ');
		';
		$document->addScriptDeclaration($paramsToJS);
	}
}
