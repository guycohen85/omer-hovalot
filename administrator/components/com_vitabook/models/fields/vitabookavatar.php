<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
 
defined('JPATH_PLATFORM') or die;
jimport('joomla.form.formfield');

/**
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's <input type="color">
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.color.html
 * @since       11.3
 */
class JFormFieldVitabookAvatar extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string 
     * @since  11.3
     */
    protected $type = 'vitabookAvatar';
 
    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.3
     */
    protected function getInput()
    {
        require(JPATH_ADMINISTRATOR .'/components/com_vitabook/helpers/avatar.php');
        $availableAvatarSystems = VitabookHelperAvatar::getAvailableAvatarSystems();

        // Initialize some field attributes.
        // $size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $selected = JComponentHelper::getParams('com_vitabook')->get('vbAvatar');
 
        return JHTML::_( 'select.genericlist', $availableAvatarSystems, $this->name , null, null, null, $selected, $this->id, true);
    }
}