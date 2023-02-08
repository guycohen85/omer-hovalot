<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */
 
defined('JPATH_PLATFORM') or die;
 
class JFormRuleParentid extends JFormRule
{
    /**
     * Method to test validity of supplied parent_id.
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
        if((int)$value){
            // Get the database object and a new query object.
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
     
            // Build the query.
            $query->select('level,date');
            $query->from('#__vitabook_messages');
            $query->where('id = ' . $db->quote($value));
     
            // Set and query the database.
            $db->setQuery($query);

            $message = $db->loadObject();
            
       		$params = JComponentHelper::getParams('com_vitabook');
            if($message->level < $params->get('max_level')) {
                //-- If message is reply, check if replies are still allowed
                if($message->level > 0) {
                    return VitabookHelper::messageReply($message->date);
                }
                return true;
            }
        }
        // if parent level is too high for replies
        return false;
    }
}