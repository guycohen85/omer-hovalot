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
class JFormRuleSecureform extends JFormRule
{

    /**
     * Method to test validity of supplied secure_form.
     *
     * @param   JXMLElement  &$element  The JXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed        $value     The form field value to validate.
     * @param   string       $group     The field name group control value. This acts as as an array container for the field.
     *                                   For example if the field has name="foo" and the group value is set to "bar" then the
     *                                   full field name would end up being "bar[foo]".
     * @param   JRegistry    &$input    An optional JRegistry object with the entire data set to validate against the entire form.
     * @param   object       &$form     The form object for which the field is being tested.
     *
     * @return  boolean  True if the value is valid, false otherwise.
     *
     * @since   11.1
     * @throws  JException on invalid rule.
     */
    public function test(SimpleXMLElement $element, $value, $group = NULL, JRegistry $input = NULL, JForm $form = NULL)
    {
        if(!empty($value)){
            return false;
        }
    }
    
}