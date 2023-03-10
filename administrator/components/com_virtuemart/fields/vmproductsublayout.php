<?php

/**
 *
 * @package	VirtueMart
 * @subpackage   Models Fields
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
defined('JPATH_BASE') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('VirtueMartModelConfig'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'config.php');
jimport('joomla.form.formfield');

/**
 * Supports a modal product picker.
 *
 *
 */
class JFormFieldVmproductsublayout extends JFormField
{
	var $type = 'layout';

	/**
	 * Method to get the field input markup. Use as name the view of the desired layout list + "layout".
	 * For example <field name="categorylayout" for all layouts of hte category view.
	 *
     * @author   Max Milbers
	 * @return	string	The field input markup.
	 * @since	2.0.24a
	 */
  
	function getInput() {

		VmConfig::loadJLang('com_virtuemart');
		$view = substr($this->fieldname,0,-6);
		$model = vmModel::getModel('config');
		$vmLayoutList = $model->getFieldList('products');
		$html = JHtml::_('Select.genericlist',$vmLayoutList, $this->name, 'size=1 width=200', 'value', 'text', array($this->value));

        return $html;

    }

}