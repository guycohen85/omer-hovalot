<?php
defined('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id$
 */


defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

class JFormFieldVmjpluginwarning extends JFormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	var $type = 'vmjpluginwarning';

	protected function getLabel() {

	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput() {

		VmConfig::loadJLang('com_virtuemart');

		$option = vRequest::getCmd('option');
		if ($option == 'com_virtuemart') {
			$js = "
 jQuery(document).ready(function( $ ) {
   $( '#vmjpluginwarning' ) .closest('.control-group').hide();
 });
 ";

			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
			return '<span id="vmjpluginwarning"></span>';
		} else {
						$js = "
			window.addEvent('domready', function() {
			   $$( '.panel h3 a' ) .hide();
			   $$( '.panel h3#warning-options a' ) .show();
			    $$( '.panel .content' ) .hide();
			      $$( '.panel .alert alert-info' ) .show();
			 });
			 ";
			$doc = JFactory::getDocument();
			//$doc->addScriptDeclaration($js);

			$css =".panel {display:none;}\n";
			JFactory::getDocument()->addStyleDeclaration($css);

			return '<div id="vmjpluginwarning">'.vmText::_('COM_VIRTUEMART_PLUGIN_WARNING').'</div>';
	}

}
}