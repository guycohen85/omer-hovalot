<?php

/**

 * {neat}logo

 * 

 * @package		{neat}logo

 * @subpackage	Modules

 * @license		GNU/GPL, see LICENSE.php

 */

 

defined('_JEXEC') or die('');


// parameters
$theme = $params->get('theme', 'default');

// includes
$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_neatlogo/css/common.css');
require(JModuleHelper::getLayoutPath('mod_neatlogo'));

?>