<?php

/**
 * @package Unite Gallery for Joomla 1.7-2.5
 * @version 1.0
 * @author valiano (unitegallery.net), Unite CMS (unitecms.net)
 * @copyright (C) 2014- Unite CMS
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die;

global $uniteGalleryVersion;
$uniteGalleryVersion = "1.4.5 b1";

$currentFile = __FILE__;
$currentFolder = dirname($currentFile);

//include frameword files
require_once $currentFolder . '/inc_php/framework/include_framework.php';

require_once $currentFolder . '/inc_php/unitegallery_globals.class.php';
require_once $currentFolder . '/inc_php/unitegallery_globals_gallery.class.php';
require_once $currentFolder . '/inc_php/unitegallery_operations.class.php';
require_once $currentFolder . '/inc_php/unitegallery_categories.class.php';
require_once $currentFolder . '/inc_php/unitegallery_item.class.php';
require_once $currentFolder . '/inc_php/unitegallery_items.class.php';
require_once $currentFolder . '/inc_php/unitegallery_galleries.class.php';
require_once $currentFolder . '/inc_php/unitegallery_gallery.class.php';
require_once $currentFolder . '/inc_php/unitegallery_gallery_type.class.php';
require_once $currentFolder . '/inc_php/unitegallery_items.class.php';
require_once $currentFolder . '/inc_php/unitegallery_helper.class.php';
require_once $currentFolder . '/inc_php/unitegallery_helper_gallery.class.php';

//include all gallery files
$objGalleries = new UniteGalleryGalleries();
$arrGalleries = $objGalleries->getArrGalleryTypes();

foreach($arrGalleries as $gallery){
	$filepathIncludes = $gallery->getPathIncludes();
	$pathGallery = $gallery->getPathGallery();
	if(file_exists($filepathIncludes))
		require $filepathIncludes;
}


?>