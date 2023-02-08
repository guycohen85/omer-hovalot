<?php
/**
 * @package		mod_articles_megamenu
 * @version		1.6.2
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

/* BASIC OPTIONS */
// Titles parameters
$item_title = $params->get('item_title', 1);
$link_titles = $params->get('link_titles');
$item_heading = $params->get('item_heading', 'h3');

// Images parameters
$intro_images = $params->get('intro_images', 1);
$intro_image_link = $params->get('intro_image_link', 'default');
$intro_only = $params->get('intro_only');

// HTML settings
$listing_tags = $params->get('listing_tags', 'ul-lu');
$showReadmore = $params->get('readmore', 1);
$showLastSeparator = $params->get('showLastSeparator', 1);

$list = modArticlesMegamenuHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_articles_megamenu', $params->get('layout', 'default'));
