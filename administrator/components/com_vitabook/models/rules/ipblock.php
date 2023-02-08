<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */
 
defined('JPATH_PLATFORM') or die;
 
/**
 * Form Rule class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormRuleIpblock extends JFormRule
{

    /**
     * Method to test the value.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate against the entire form.
     * @param   JForm             $form     The form object for which the field is being tested.
     *
     * @return  boolean  True if the value is valid, false otherwise.
     *
     * @since   11.1
     * @throws  UnexpectedValueException if rule is invalid.
     */
    public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null)
    {
        if(JFactory::getUser()->get('guest'))
            return VitabookHelper::checkIpBlock($value);
        else
            return true;
    }
}