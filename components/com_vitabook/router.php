<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

/**
 * @param       array   A named array
 * @return      array
 */
function VitabookBuildRoute(&$query)
{
    // get menu item id for vitabook
    $menu = JFactory::getApplication()->getMenu();
    $items = $menu->getItems(array(), array());
    foreach($items as $item){
        if( (isset($item->query['option']) && $item->query['option'] == 'com_vitabook') && (isset($item->query['view']) && $item->query['view'] == 'vitabook') )
        {
            // set menu item id of vitabook in query for route from module
            $query['Itemid'] = $item->id;
        }
        if(isset($query['start']) || isset($query['limitstart'])):
            unset($query['messageId']);
        endif;
    }
    // return empty array to prevent joomla from popping up errors
    return array();
}

/**
 * @param       array   A named array
 * @param       array
 */
function VitabookParseRoute($segments)
{
    // return empty array to prevent joomla from popping up errors
    return array();
}
