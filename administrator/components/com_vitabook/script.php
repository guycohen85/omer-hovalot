<?php
/**
 * @version     2.2.2 
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of VitaBook component
 */
class com_vitabookInstallerScript
{
    protected $param_defaults;
    protected $acl_defaults;
    protected $acl_replace;

    function __construct()
    {
        $this->param_defaults = (object)array(
                    // general settings
                    "introtext" => '',
                    "message_limit" => 25,
                    "reply_limit" => 10,
                    "vbAvatar" => 1,
                    "max_edit_time" => 0,
                    "max_level" => 2,
                    "max_reply_time" => 0,
                    "guest_post_state" => 1,
                    "guest_captcha" => 1,
                    "admin_mail" => 0,
                    "admin_mail_group" => 8, // default id of Super User group
                    // appearance
                    "rounded_corners" => 1,
                    "vb_date_format" => 'DATE_FORMAT_LC2',
                    "vb_text_color" => '',
                    "vb_header_background" => '',
                    "vb_message_background" => '',
                    "vb_border_color" => '',
                    // editor
                    "editor_width" => 500,
                    "vbForm_site" => 1,
                    "vbForm_location" => 0,
                    "vbEditorOutline" => 1,
                    "editor_html" => 0,
                    "upload_image_width" => 500,
                    "upload_image_quality" => 70
                    // permissions
                        // no default settings to define
            );

        $this->acl_defaults = (object)array(
                    "core.admin" => (object)array(),
                    "core.manage" => (object)array(),
                    "vitabook.create.new" => (object)array("1" => 1),
                    "vitabook.create.reply" => (object)array("1" => 1),
                    "core.delete" => (object)array(),
                    "core.edit" => (object)array(),
                    "core.edit.state" => (object)array(),
                    "core.edit.own" => (object)array("2" => 1),
                    "vitabook.insert.video" => (object)array("6" => 1, "2" => 1),
                    "vitabook.insert.image" => (object)array("1" => 1, "2" => 1),
                    "vitabook.upload.image" => (object)array("6" => 1, "2" => 1)
        );

        /*
         * ACL values which were added in later versions of VitaBook and have to be replaced with other values when updating
         * version  new value               replacement for
         * 1.2.0    vitabook.create.new     core.create
         * 1.2.0    vitabook.create.reply   core.create
         */
        $this->acl_replace = (object)array(
                    "vitabook.create.new" => "core.create",
                    "vitabook.create.reply" => "core.create"
        );
}

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {
        echo '<p><img src="../components/com_vitabook/assets/img/vitabook.png" /></p>';
        echo '<p>' . JText::_('COM_VITABOOK_INSTALL_1') . ' ' . $parent->get("manifest")->version . ' ' . JText::_('COM_VITABOOK_INSTALL_2') . '</p>';

        echo '</table><fieldset class="uploadform" style="background-color:white;border-color:red;"><legend style="color:red;">'.JText::_('COM_VITABOOK_CAPTCHA_LEGEND').'</legend>';
        echo '<p>'.JText::_('COM_VITABOOK_CAPTCHA_ADVISE').' <a target="_blank" href="http://joomvita.com/vitabook/documentation/3-configure-captcha">'.JText::_('COM_VITABOOK_CAPTCHA_LINK').'</a>.</p>';

        echo '</fieldset><fieldset class="uploadform" style="background-color:white;border-color:#4B974B;"><legend style="color:#4B974B;">'.JText::_('COM_VITABOOK_PROMO_LEGEND').'</legend>';
        echo '<p>' . JText::_('COM_VITABOOK_PROMO') . ' <a href="http://extensions.joomla.org/extensions/contacts-and-feedback/guest-book/20880">' . JText::_('COM_VITABOOK_PROMO_LINK') . '</a></p>';
        echo '</fieldset>';
        echo '<p>&nbsp;</p><p>&nbsp;</p>';
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {
        // $parent is the class calling this method
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {
        echo '<p><img src="../components/com_vitabook/assets/img/vitabook.png" /></p>';
        echo '<p>' . JText::_('COM_VITABOOK_UPDATE_1') . ' ' . $parent->get("manifest")->version . ' ' . JText::_('COM_VITABOOK_UPDATE_2') . '</p>';

        echo '</table><fieldset class="uploadform" style="background-color:white;border-color:red;"><legend style="color:red;">'.JText::_('COM_VITABOOK_CAPTCHA_LEGEND').'</legend>';
        echo '<p>'.JText::_('COM_VITABOOK_CAPTCHA_ADVISE').' <a target="_blank" href="http://joomvita.com/vitabook/documentation/3-configure-captcha">'.JText::_('COM_VITABOOK_CAPTCHA_LINK').'</a>.</p>';

        echo '</fieldset><fieldset class="uploadform" style="background-color:white;border-color:#4B974B;"><legend style="color:#4B974B;">'.JText::_('COM_VITABOOK_PROMO_LEGEND').'</legend>';
        echo '<p>' . JText::_('COM_VITABOOK_PROMO') . ' <a href="http://extensions.joomla.org/extensions/contacts-and-feedback/guest-book/20880">' . JText::_('COM_VITABOOK_PROMO_LINK') . '</a></p>';
        echo '</fieldset>';
        echo '<p>&nbsp;</p><p>&nbsp;</p>';
    }

    /**
     * method to run before an install/update/uninstall method
     * $type is the type of change (install, update or discover_install)
     * $parent is the class calling this method
     * @return void
     */
    function preflight($type, $parent)
    {
        // When updating the component, we have to make sure that the database update in version 1.3.0 can be executed, or we have to perform a small hack into the joomla schemas table.
        if($type == 'update')
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('extension_id');
            $query->from('#__extensions');            
            $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
            $db->setQuery($query);
            $extension_id = $db->loadResult();
            
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('extension_id');
            $query->from('#__schemas');          
            $query->where($db->quoteName('extension_id') . ' = ' .$extension_id);
            $db->setQuery($query);
            
            if(!$db->loadResult()) {
                // No record in Joomla's schema table, so insert the previous version.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query = "INSERT INTO `#__schemas` (`extension_id`, `version_id`) VALUES ('".$extension_id."', '1.2.3')";
                $db->setQuery($query);
                $db->query();
            }
        }
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        // install/update acl and param defaults
        if($type != 'uninstall')
        {
            $this->setACL();
            $this->setParams();
        }
        // call database update
        if($type == 'update')
        {
            // get the currently installed version
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('manifest_cache');
            $query->from('#__extensions');            
            $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
            $db->setQuery($query);
            $manifest_cache = json_decode($db->loadResult());
            
            $this->oldversion = $manifest_cache->version;
            $this->newversion = $parent->get( "manifest" )->version;
            
        }
        
    }

    /**
     * Method to set params in extensions table
     */
    function setParams()
    {
        // get existing values
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from('#__extensions');
        $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
        $db->setQuery($query);
        $params = $db->loadResult();

        $params = json_decode($params);
        // add new parameters if necessary
        if(count((array)$params) != 0)
        {
            foreach($this->param_defaults as $name => $value){
                if(!isset($params->$name))
                    $params->$name = $value;
            }
        }
        // params empty means new install: install defaults
        else
        {
            $params = $this->param_defaults;
        }

        // install calculated params in database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__extensions'));
        $params = json_encode($params);
        $query->set($db->quoteName('params') . ' = ' . $db->quote($params));
        $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Method to set acl in assets table
     */
    function setACL()
    {
        // get existing values
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('rules');
        $query->from($db->quoteName('#__assets'));
        $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
        $db->setQuery($query);
        $current_acl = $db->loadResult();

        $current_acl = json_decode($current_acl);
        // add new acl values if necessary
        if(count((array)$current_acl) != 0)
        {
            // Replace ACL values for other values if necessary
            foreach($this->acl_replace as $new => $old){
                if(!array_key_exists($new, $current_acl)){
                    $this->acl_defaults->$new = $current_acl->$old;
                }
            }

            // If acl setting already exists, overwrite default value with existing value
            foreach($this->acl_defaults as $acl_default => $name){
                if(array_key_exists($acl_default, $current_acl)) {
                    $this->acl_defaults->$acl_default = $current_acl->$acl_default;
                }
            }
        }

        $acl = json_encode($this->acl_defaults);

        // install updated acl values in database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__assets'));
        $query->set($db->quoteName('rules') . ' = ' . $db->quote($acl));
        $query->where($db->quoteName('name') . ' = ' . $db->quote('com_vitabook'));
        $db->setQuery($query);
        $db->query();
    }
}
