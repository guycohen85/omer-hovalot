<?php
/** 
	* @version		$Id: canonical.php 3 2014-01-07 16:31:21Z grigormihov $ 
	* @package		Joomla/StyleWare 
	* @subpackage	Content * @copyright	Copyright (C) 2011 StyleWare.EU. All rights reserved. 
	* @license		GNU/GPL, see LICENSE.php
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

// Set the separator as some idiot removed it from the core
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

jimport( 'joomla.plugin.plugin' );

class plgSystemCanonical extends JPlugin {

	protected $canonical = null;
	
	function plgSystemCanonical (&$subject, $config) {
		parent::__construct($subject, $config);
	}
	
	function onBeforeCompileHead () {
		$mainframe 		= JFactory::getApplication();
		if ($mainframe->isAdmin()) {
			return;
		}
		$doc = JFactory::getDocument();
		// remove the shits set by Joomla!
		foreach ( $doc->_links as $k => $array ) {
			if ( $array['relation'] == 'canonical' ) {
				unset($doc->_links[$k]);
			}
		}
		
		// Set the correct URL as canonical if we were able to generate it
		if(!empty($this->canonical)){
		//$doc->addHeadLink(htmlspecialchars($this->canonical), 'canonical');
		}
	}
	
	public function onContentBeforeDisplay( $article, $params, $limitstart )
	{
		static $is_done = false;
		
		if($is_done) return;
		
		$is_done = true;
	

		$mainframe 		= JFactory::getApplication();
		$document    	= JFactory::getDocument();
		
		//don't load in administration
		if ($mainframe->isAdmin()) {
			return;
		}
		
		//get current option and view
		$option   	= JRequest::getCmd('option');
		$view   	= JRequest::getCmd('view');
		$reqid   	= explode(":",JRequest::getVar('id'));
		$reqid		= $reqid["0"];
		
		
		
		
		// echo $reqid;
		
		//get base and remove the trailing slash
		$base = ''; //substr_replace(JURI::base(),"", -1);
		
		//check if homepage
		$current = explode("?",JURI::getInstance()->toString());
		$current = $current["0"];
		$homepage = JURI::ROOT();
		
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		
		if ($menu->getActive()->home) {
			
		
			$url = rtrim(JURI::base(), '/').JRoute::_('index.php?Itemid=' . $menu->getActive()->params->get('aliasoptions'));
			
			$this->canonical =  rtrim($url, '/');
		}
		
		// let's fix the tags routing. No canonical, directly make redirect
		if($option == "com_tags" && isset($reqid)) {
			$db = JFactory::getDbo();
			$db->setQuery('SELECT path FROM #__menu WHERE link LIKE "index.php?option=com_tags%" AND client_id = 0 LIMIT 1');
			$result = $db->loadResult();
			$db->setQuery('SELECT alias FROM #__tags WHERE id = '.$reqid.'');
			$tagalias = $db->loadResult();
			$redirect = JRoute::_(''.$result.'/'.$reqid.'-'.$tagalias.'', false);
			$current = explode("?",JURI::getInstance()->toString());
			if($redirect == $current) {
				return;
			}
			else {
				$application = JFactory::getApplication();
				$application->redirect($redirect, "");
			}
		}
		
		//if not in com_content kill it
		if($option != "com_content"){
			return;
		}
		
		//don't load if not com_content and view is not article
		if($article == "com_content.article" && ($option == "com_content" && $view == "article") && empty($this->canonical)){
			//get proper article URL
			require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
			//$alias1 = (int)$params->id.'-';
			//$alias1.= $params->alias;

			$articlecanonicalurl=JRoute::_( ContentHelperRoute::getArticleRoute( (int)$params->id, $params->catslug),false,-1);
						
			//create link
			$this->canonical = $base.$articlecanonicalurl;
		}
		
		if($article == "com_content.category" && ($option == "com_content" && $view == "category") && empty($this->canonical)) {
		jimport('joomla.database.tablenested');
	//if(class_exists("JCategories")) die("test");
			// get the proper category URL
			require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'category.php');
			require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
	
		//die(ContentHelperRoute::getCategoryRoute($params->id));
			
			
			$this->canonical = $base.JRoute::_(ContentHelperRoute::getCategoryRoute($params->catid),false,-1);
		}
	}
}