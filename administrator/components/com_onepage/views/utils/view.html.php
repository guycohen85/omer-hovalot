<?php
/**
 * @version		$Id: view.html.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of banners.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
 jimport('joomla.application.component.view');
class JViewUtils extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
	    $model = &$this->getModel();
		$this->menus = $model->getMenus(); 
		$this->sortedmenu = $model->getMenusSorted(); 
		
		$session = JFactory::getSession(); 
	    $res = $session->get('opcsearch', ''); 

		$this->results = $res;
		$this->cats = $model->getCats(); 
		parent::display($tpl);
		
	}
	public function printChildren($arr, $value, $title, $prefix='')
	{
	   $model = &$this->getModel();
	   return $model->printChildren($arr, $value, $title, $prefix='->'); 

	}

}
