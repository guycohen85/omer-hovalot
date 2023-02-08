<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

define("UNITEGALLERY_TEXTDOMAIN","unitegallery");

class UniteProviderFunctionsUG{

	
	/**
	 * init base variables of the globals
	 */
	public static function initGlobalsBase(){

		$tablePrefix = "#__";
		
		GlobalsUG::$table_galleries = $tablePrefix.GlobalsUG::TABLE_GALLERIES_NAME;
		GlobalsUG::$table_categories = $tablePrefix.GlobalsUG::TABLE_CATEGORIES_NAME;
		GlobalsUG::$table_items = $tablePrefix.GlobalsUG::TABLE_ITEMS_NAME;
		
		$pluginName = "com_unitegallery";
		
		$pathMedia = JPATH_ROOT."/media/".$pluginName."/";
		GlobalsUG::$path_media_ug = $pathMedia."assets/unitegallery-plugin/";
		
		GlobalsUG::$path_base = JPATH_ROOT."/";
		GlobalsUG::$path_images = JPATH_ROOT."/images/";
		GlobalsUG::$path_cache = $pathMedia."cache/";
		
		GlobalsUG::$pathPlugin = realpath(JPATH_ADMINISTRATOR."/components/".$pluginName)."/";
		
		GlobalsUG::$url_base = JURI::root();
		GlobalsUG::$url_component_client = GlobalsUG::$url_base."index.php?option=".$pluginName;
		GlobalsUG::$url_component_admin = JURI::current()."?option=".$pluginName;
		
		GlobalsUG::$url_ajax = GlobalsUG::$url_component_admin;
		
		$urlMedia = GlobalsUG::$url_base."media/{$pluginName}/";

		GlobalsUG::$url_media_ug = $urlMedia."assets/unitegallery-plugin/";
		GlobalsUG::$url_images = GlobalsUG::$url_base."images/";
		
		GlobalsUG::$urlPlugin = GlobalsUG::$url_base."administrator/components/{$pluginName}/";
		
		
	}
	
	
	/**
	 * add scripts and styles framework
	 */
	public static function addScriptsFramework(){

		$isJoomla3 = UniteFunctionJoomlaUG::isJoomla3();
		
		if($isJoomla3 == false){
			HelperUG::addScriptCommon("jquery-1.11.1.min","jquery");
		}
		else{
			JHtml::_('bootstrap.framework');
		}
		
		HelperUG::addScriptCommon("jquery-ui.min","jquery-ui");
		HelperUG::addStyle("jquery-ui.structure.min","jui-smoothness-structure","css/jui/new");
		HelperUG::addStyle("jquery-ui.theme.min","jui-smoothness-structure","css/jui/new");
		
	}
	
	
	/**
	 * 
	 * register script
	 */
	public static function addScript($handle, $url){
		
		if(empty($url))
			UniteFunctionsUG::throwError("empty script url, handle: $handle");
		
		$document = JFactory::getDocument();
		$document->addScript($url);
		
	}
	
	
	/**
	 *
	 * register script
	 */
	public static function addStyle($handle, $url){
	
		if(empty($url))
			UniteFunctionsUG::throwError("empty style url, handle: $handle");
	
		$document = JFactory::getDocument();
		$document->addStyleSheet($url);
			
	}

	
	/**
	 *
	 * sanitize data, in joomla no need to sanitize
	 */
	public static function normalizeAjaxInputData($arrData){
		
		return $arrData;
	}
	
	
	/**
	 * put footer text line
	 */
	public static function putFooterTextLine(){
		?>
			&copy; <?php _e("All rights reserved",UNITEGALLERY_TEXTDOMAIN)?>, <a href="http://codecanyon.net/user/valiano" target="_blank">Valiano</a>, <a href="http://unitecms.net" target="_blank">UniteCMS</a>. &nbsp;&nbsp;
		<?php
	}
	
	
	/**
	 * add jquery include
	 */
	public static function addjQueryInclude($app, $urljQuery = null){
		UniteFunctionJoomlaUG::addjQueryInclude($app, $urljQuery);
	}
	
	/**
	 * add position settings (like shortcode) based on the platform
	 */
	public static function addPositionToMainSettings($settingsMain){
		
		$textGenerate = __("Generate Shortcode",UNITEGALLERY_TEXTDOMAIN);
		$descShortcode = __("Copy this shortcode into article text",UNITEGALLERY_TEXTDOMAIN);
		$settingsMain->addTextBox("shortcode", "",__("Gallery Shortcode",UNITEGALLERY_TEXTDOMAIN),array("description"=>$descShortcode, "readonly"=>true, "class"=>"input-alias input-readonly", "addtext"=>"&nbsp;&nbsp; <a id='button_generate_shortcode' class='unite-button-secondary' >{$textGenerate}</a>"));
		
		return($settingsMain);
	}
	
	
	/**
	 * add tile size related settings
	 */
	public static function addTilesSizeSettings($settings){
		
		$arrSizes = array();
		$arrSizes["medium"] = "Medium (max width - 300)";
		$arrSizes["large"] = "Large (max width - 768)";
		$arrSizes["full"] = "Full";
		
		$params = array(
				"description"=>__("Tiles thumbs resolution. If selected 'Large', The thumbnails will be generated on the first gallery output", UNITEGALLERY_TEXTDOMAIN)
		);
		
		$settings->addHr();
		
		$settings->addSelect("tile_image_resolution", $arrSizes, "Tile Image Resolution", "medium", $params);
		
		return($settings);
	}
	
	
	/**
	 * get thumb width by size name
	 */
	public static function getThumbWidth($sizeName){
		
		switch($sizeName){
			case "medium":
				return(GlobalsUG::THUMB_WIDTH);
			break;
			case "large":
				return(GlobalsUG::THUMB_WIDTH_LARGE);
			break;
			default:
				UniteFunctionsUG::throwError("Wrong thumb size");
			break;
		}
		
	}
	
	
	/**
	 * print custom script
	 */
	public static function printCustomScript($script, $hardCoded = false){
		
		if($hardCoded == false)
			UniteFunctionJoomlaUG::addScriptDeclaration($script);
		else
			echo "<script type='text/javascript'>{$script}</script>";
		
	}
	
}
?>