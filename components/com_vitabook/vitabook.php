<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

defined('_JEXEC') or die;

// disable display_errors to prevent AJAX from failing
if (ini_get('display_errors')) {
    ini_set('display_errors', 0);
}

// Include dependancies
jimport('joomla.application.component.controller');

//-- Require helper file for ACL
JLoader::register('VitabookHelper', JPATH_COMPONENT_ADMINISTRATOR .'/helpers/vitabook.php');
JLoader::register('VitabookHelperAvatar', JPATH_COMPONENT_ADMINISTRATOR .'/helpers/avatar.php');
JLoader::register('VitabookHelperMail', JPATH_COMPONENT_ADMINISTRATOR .'/helpers/mail.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Vitabook');
$task = JFactory::getApplication()->input->get('task', null, 'word');
$controller->execute($task);
$controller->redirect();
