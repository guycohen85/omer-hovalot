<?php
/**
 * @package 	mod_universal_menu
 * @version		1.10.1
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Site
 * @subpackage	mod_universal_menu
 * @since		1.0.0
 */
class modUniversalMenuHelper
{
	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @since	1.0.0
	 */
	static function getList( &$params )
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$user = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);
		$key = 'menu_items'.$params.implode(',', $levels).'.'.$active->id;

		$cache = JFactory::getCache('mod_universal_menu', '');
		if (!($items = $cache->get($key)))
		{
			// Initialise variables.
			$list		= array();
			$path		= $active->tree;
			$start		= (int) $params->get('startLevel');
			$end		= (int) $params->get('endLevel');
			$showAll	= $params->get('showAllChildren');
			$items 		= $menu->getItems('menutype', $params->get('menutype'));

			$lastitem	= 0;

			if ($items) {
				foreach($items as $i => $item)
				{
					if (($start && $start > $item->level)
						|| ($end && $item->level > $end)
						|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						|| ($start > 1 && !in_array($item->tree[$start-2], $path))
					) {
						unset($items[$i]);
						continue;
					}

					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;

					if (isset($items[$lastitem])) {
						$items[$lastitem]->deeper		= ($item->level > $items[$lastitem]->level);
						$items[$lastitem]->shallower	= ($item->level < $items[$lastitem]->level);
						$items[$lastitem]->level_diff	= ($items[$lastitem]->level - $item->level);
					}

					// Added "Show All Children" option check (since ver 1.2.13)
					$item->parent = ($showAll && (boolean) $menu->getItems('parent_id', (int) $item->id, true));

					$lastitem			= $i;
					$item->active		= false;
					$item->flink		= $item->link;

					// Reverted back for CMS version 2.5.6
					switch ($item->type)
					{
						case 'separator':
							// No further action needed.
							continue;

						case 'url':
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link.'&Itemid='.$item->id;
							}
							break;

						case 'alias':
							// If this is an alias use the item id stored in the parameters to make the link.
							$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
							break;

						default:
							$router = JSite::getRouter();
							if ($router->getMode() == JROUTER_MODE_SEF) {
								$item->flink = 'index.php?Itemid='.$item->id;
							}
							else {
								$item->flink .= '&Itemid='.$item->id;
							}
							break;
					}

					if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
						$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
					}
					else {
						$item->flink = JRoute::_($item->flink);
					}

					$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
					$item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
					$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
					$item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
				}

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper		= (($start?$start:1) > $items[$lastitem]->level);
					$items[$lastitem]->shallower	= (($start?$start:1) < $items[$lastitem]->level);
					$items[$lastitem]->level_diff	= ($items[$lastitem]->level - ($start?$start:1));
				}
			}

			$cache->store($items, $key);
		}
		return $items;
	}

	/**
	 * Get a HTML code of the item's link.
	 *
	 * @param	JRegistry $item		The module options.
	 * @param	Boolean $imageTitle	How to display image title, before(false) or after(true) the image
	 * @param	Boolean $subOpen	How to display sub-category arrow, before(false) or after(true) item title
	 *
	 * @return	array
	 * @since	1.1.30
	 */	
	static function htmlLinkType( &$item, $imageTitle = true, $subOpen = true )
	{
		// Process a menu item
		if (isset($item->title)) {
			if ($item->menu_image) {
				// Insert image
				$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /> ';
				if ($item->params->get('menu_text', 1 )) {
					$linkspan = '<span class="image-title">'.$item->title.'</span>';
					// How to display image title, before or after the image?
					$imageTitle
						? $linktype = $linktype . $linkspan
						: $linktype = $linkspan . $linktype;
				}
			}
			else { 
				$linktype = $item->title;
			}
			
			// Insert break-line
			$linktype = str_replace(array("\t", '{BR}', '{br}'), '<br>', $linktype);
			
			// Sub-category arrow indicator
			if ($item->parent && $subOpen !== 'none') {
				$subOpen
					? $linktype = $linktype . ' <i></i>'
					: $linktype = '<i></i> ' . $linktype;
			}

			return $linktype;
		}
		else return false;
	}	
	
/**
* VirtueMart Full Category List Module for Joomla! and Virtuemart
* Helper class
* @author		Andrew Patton
* @version		1.2.0
* @copyright	(C) 2012 Andrew Patton. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is free software.
* See COPYRIGHT.php for copyright notices and details.
*/

	public static $cache;
	public static $category_model;
	
	/**
	 * Gets all children of a parent category
	 *
	 * @access	public
	 * @param int $catPId (the category id of desired parent category)
	 * @return array (object list with category id, name, if published, order, and parent id)
	 **/
	static function getCatChildren( $catPId = 0 )
	{
		return modUniversalMenuHelper::$cache->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ), 1, $catPId ); // the 1 is for vendor_id
	}

	/**
	 * Returns an array of the ids of all parent categories of specified category
	 *
	 * @access	public
	 * @param int $catid (the category id to check)
	 * @return array (list of parents, with current category as the last item and top-level parent category as the first)
	 **/
	static function getCatParentIds( $catid = 0 )
	{
		// can't do this with cache object
		$parents = array( $catid );
		$cat_relations = modUniversalMenuHelper::$category_model->getRelationInfo( $catid );
		$cat = new stdClass;
		$cat->virtuemart_category_id = $catid;
		while ( isset( $cat->virtuemart_category_id ) && $cat_relations->category_parent_id ) {
			$cat = modUniversalMenuHelper::$category_model->getParentCategory( $cat->virtuemart_category_id );
			array_unshift( $parents, $cat->virtuemart_category_id );
			$cat_relations = modUniversalMenuHelper::$category_model->getRelationInfo( $cat->virtuemart_category_id );
			if ($cat_relations->category_parent_id) {
				error_log(print_r($cat_relations, true));
			}
		}
		return $parents;
	}

	/**
	 * Gets the Itemid for main virtuemart link in the menu
	 *
	 * @access	public
	 * @return int (Itemid for VirtueMart menu item)
	 **/
	static function getVMItemId( $catid = false )
	{
		$menu = &JSite::getMenu(); // use getMenu() function and totally avoid DB queries
		$items = $menu->getItems('access', 1); // get all menu items with access: public

		// If generic VM itemid hasn't yet been set by this function:
		if (JRequest::getInt('vmGenericItemid', -1) == -1) {
			$vmItemid = 0;
			$vmItemid1 = 0;
			$vmItemid2 = 0;
			$vmItemid3 = 0;
			foreach($items as $item) {
				$itemid = $item->id;
				if (strpos($item->link, 'com_virtuemart') !== false) { // first virtuemart menu item
					// let's get the sure bet out of the way:
					if (isset($item->query['view']) && $item->query['view'] == 'virtuemart') { // then it's definitely the VM home menu item
						$vmItemid = $itemid;
						break;
					}
					elseif (!$item->parent_id) { // if it's a top-level menu item (more likely to be important)
						$vmItemid1 = ($vmItemid1 ? $vmItemid1 : $itemid );
					}
					else { // for any other kind of VM link (will only be used if ALL other options fail)
						$vmItemid2 = ($vmItemid2 ? $vmItemid2 : $itemid );
					}
				}
			}
			$vmItemid = ($vmItemid ? $vmItemid : ($vmItemid1 ? $vmItemid1 : $vmItemid2));
			JRequest::setVar('vmGenericItemid', $vmItemid);
		}
		// Now check for specific category menu item if a catid was passed:
		if ($catid) {
			foreach($items as $item) {
				$itemid = $item->id;
				// if it's a virtuemart menu item and is the category page:
				if (strpos($item->link, 'com_virtuemart') !== false && isset($item->query['virtuemart_category_id']) && $item->query['virtuemart_category_id'] == $catid) {
					return $itemid;
				}
			}
		}
		return JRequest::getInt('vmGenericItemid');
	}
}