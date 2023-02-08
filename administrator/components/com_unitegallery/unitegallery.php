<?php
/*
Plugin Name: Unite Gallery
Plugin URI: http://wp.unitegallery.net
Description: Unite Gallery - All in one image and video gallery
Author: Valiano
Version: 1.4.5 b1
Author URI: http://unitegallery.net
*/

//ini_set("display_errors", "on");
//ini_set("error_reporting", E_ALL);

if(!defined("_JEXEC"))
	define("_JEXEC", true);

$mainFilepath = __FILE__;
$currentFolder = dirname($mainFilepath);


//phpinfo();
require_once $currentFolder.'/includes.php';


require_once $currentFolder."/inc_php/framework/provider/provider_main_file.php";


