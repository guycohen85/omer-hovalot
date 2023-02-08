<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

/**
 * Supports a custom TinyMCE editor on a textarea
 */
class JFormFieldVitabookEditor extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'vitabookEditor';
	protected $canDo;
    protected $_legacy;

	/**
	 * Method to get the textarea field input markup.
	 * Use the rows and columns attributes to specify the dimensions of the area.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
        // Get Joomla version and set optional legacy mode
        $jversion = new JVersion();
        if(version_compare($jversion->getShortVersion(),'3.2.0','ge'))
        {
            // Joomla 3.2 or higher
            $this->_legacy = false;
        }
        else
        {
            // Joomla 3.1 or lower
            $this->_legacy = true;
        }

		//-- Get user permissions
		$this->canDo = VitabookHelper::getActions();
		//-- Get component parameters
		$params = JComponentHelper::getParams('com_vitabook');
		
		//-- Initialize some field attributes.
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        //-- Get editor width from component parameters
        $width = $params->get('editor_width');
        if($width == 0) {
            $width = '';
        } elseif($width == 500 && !$this->_legacy) {
            // Hack for Joomla 3.2 to make width of the editor standard responsive
            $width = '';
        }
        $height = $params->get('editor_height', 200);
		
		//-- Configure VitabookEditor settings
		$document = JFactory::getDocument();
		
		//-- Include external dependencies
        if($this->_legacy)
        {
            // Joomla 3.1 and lower
            $document->addScript( JURI::root().'media/editors/tinymce/jscripts/tiny_mce/tiny_mce.js');

            if($this->canDo->get('vitabook.insert.video'))
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/legacy/vitabookvideoLegacy/editor_plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"vitabookvideoLegacy\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/legacy/vitabookvideoLegacy/\");");
            }
            if($this->canDo->get('vitabook.insert.image') OR $this->canDo->get('vitabook.upload.image'))
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/legacy/vitabookuploadLegacy/editor_plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"vitabookuploadLegacy\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/legacy/vitabookuploadLegacy/\");");
            }
            if($params->get('editor_emoticons', 1))
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/legacy/vitabookemoticonsLegacy/editor_plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"vitabookemoticonsLegacy\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/legacy/vitabookemoticonsLegacy/\");");
            }
            if($params->get('editor_maxchars', 0) != 0)
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/legacy/maxcharsLegacy/editor_plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"maxcharsLegacy\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/legacy/maxcharsLegacy/\");");
            }
            
            // Get editor
            $document->addScriptDeclaration($this->VitabookEditorLegacy());
        }
        else
        {
            // Joomla 3.2 and higher
            $document->addScript( JURI::root().'media/editors/tinymce/tinymce.min.js');

            if($this->canDo->get('vitabook.insert.video'))
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/vitabookvideo/plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"vitabookvideo\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/vitabookvideo/\");");
            }
            if($this->canDo->get('vitabook.insert.image') OR $this->canDo->get('vitabook.upload.image'))
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/vitabookupload/plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"vitabookupload\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/vitabookupload/\");");
            }
            if($params->get('editor_emoticons', 1))
            {            
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/vitabookemoticons/plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"vitabookemoticons\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/vitabookemoticons/\");");
            }
            if($params->get('editor_maxchars', 0) != 0)
            {
                $document->addScript( JURI::root().'components/com_vitabook/assets/editor_plugins/maxchars/plugin.js');
                $document->addScriptDeclaration("tinymce.PluginManager.load(\"maxchars\", \"".JURI::root()."components/com_vitabook/assets/editor_plugins/maxchars/\");");
            }            
            
            // Get editor
            $document->addScriptDeclaration($this->VitabookEditor($height, $width));
        }

		//-- Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return '<textarea style="width:'.$width.'px; height:'.$height.'px;" name="' . $this->name . '" id="' . $this->id . '"' . $class . $disabled . $onchange . '>'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}

    
	/**
	 * Method to make custom tinyMCY editor from textarea for Joomla 3.1 and lower
	 * Only textareas with class="mceEditor" will be modified into an editor
	 **/
	protected function VitabookEditorLegacy()
	{
		//-- Get component parameters
		$params = JComponentHelper::getParams('com_vitabook');

		//-- General settings for editor
		$entity_encoding = 'raw';
		$language	= JFactory::getLanguage();
		
		if ($language->isRTL()) {
			$text_direction = 'rtl';
		} else {
			$text_direction = 'ltr';
		}
		
		$forcenewline = "force_br_newlines : false, force_p_newlines : true, forced_root_block : 'p',";
		$relative_urls = 'true';
		$invalid_elements = 'script,applet';
		//-- Toolbar settings
		$toolbar = 'top';
		$toolbar_align = 'left';
		$resizing = 'true';
		$resize_horizontal = 'false';
		$element_path = "theme_advanced_statusbar_location : \"none\", theme_advanced_path : false";
		
        //-- Use the custom css
        $custom_css = '';
        if($params->get('editor_custom_css', 0)) {
            $custom_css = Juri::root() ."components/com_vitabook/assets/editor.css";
        }
        
		$buttons 	= array();
		$plugins 	= array();
		$elements 	= array();
		
		//-- Initial values for buttons
		array_push($buttons, 'bold', 'italic', 'underline', 'bullist', 'numlist', 'separator');

		if($params->get('editor_html') == 1) {
			//-- Add code button
			$buttons[]	= 'code';
		}
		
		//-- Add links
		$buttons[]	= 'link, unlink, separator';		

		//-- Add Vitabook emoticons
        if($params->get('editor_emoticons', 1))
        {
            $plugins[]	= '-vitabookemoticonsLegacy';
            $buttons[]	= 'vitabookemoticonsLegacy';
        }

        //-- Check if uploading or inserting images is allowed
		if($this->canDo->get('vitabook.insert.image') OR $this->canDo->get('vitabook.upload.image'))
		{
			$plugins[]	= '-vitabookuploadLegacy';
			$buttons[]	= 'vitabookuploadLegacy';		
		}
		
		//-- Check if embedding videos is allowed
		if($this->canDo->get('vitabook.insert.video')) {
			$plugins[]	= '-vitabookvideoLegacy';
			$buttons[]	= 'vitabookvideoLegacy';
		}
		
		// load maxchars tinymce plugin only if limit is more than zero
		$maxchars = '';
		if($params->get('editor_maxchars', 0) != 0){
			$plugins[] = '-maxcharsLegacy';
			$maxchars = ',
					max_chars : '.$params->get('editor_maxchars', 0).',	
					max_chars_indicator : "vbMaxcharsIndicator"';
		}
		
		//-- Inline popups
		$plugins[]	= 'inlinepopups';
		$dialog_type = "dialog_type : \"modal\",";
		
		//-- Autolinks
		$plugins[]	= 'autolink';
		
		$buttons 	= implode(',', $buttons);
		$plugins 	= implode(',', $plugins);
		$elements 	= implode(',', $elements);		

        //-- set mode
        if(JFactory::getApplication()->isSite())
            $mode = "none";
        else
            $mode = "specific_textareas";

		//-- Build editor
		$editor = "tinyMCE.init({
					// General
					$dialog_type
					directionality: \"$text_direction\",
					editor_selector : \"mceEditor\",
					language : \"en\",
					mode : \"$mode\",
					plugins : \"$plugins\",
					skin : \"default\",
					theme : \"advanced\",
					// Cleanup/Output
					inline_styles : true,
					gecko_spellcheck : true,
					entity_encoding : \"$entity_encoding\",
					extended_valid_elements : \"$elements\",
					$forcenewline
					invalid_elements : \"$invalid_elements\",
					// URL
					relative_urls : $relative_urls,
					remove_script_host : true,
					document_base_url : \"". JURI::root() ."\",
					//Templates
                    content_css : \"$custom_css\",
					template_external_list_url :  \"". JURI::root() ."media/editors/tinymce/templates/template_list.js\",
					// Advanced theme
					theme_advanced_toolbar_location : \"$toolbar\",
					theme_advanced_toolbar_align : \"$toolbar_align\",
					theme_advanced_resizing : $resizing,
					theme_advanced_resize_horizontal : $resize_horizontal,
					$element_path,
					theme_advanced_buttons1 : \"$buttons\",
					theme_advanced_buttons2 : \"\",
					theme_advanced_buttons3 : \"\"
					".$maxchars."
				  });";

	
		return $editor;	
	}

	/**
	 * Method to make custom tinyMCY editor from textarea for Joomla 3.2 and higher
	 * Only textareas with class="mceEditor" will be modified into an editor
	 **/
	protected function VitabookEditor($height, $width)
	{
		//-- Get component parameters
		$params = JComponentHelper::getParams('com_vitabook');

		//-- General settings for editor
		$language	= JFactory::getLanguage();
		if ($language->isRTL()) {
			$text_direction = 'rtl';
		} else {
			$text_direction = 'ltr';
		}
		$invalid_elements = 'script,applet';
        
        $height = $height . 'px';
        $width = $width . 'px';

        //-- Use the custom css
        $custom_css = '';
        if($params->get('editor_custom_css', 0)) {
            $custom_css = Juri::root() ."components/com_vitabook/assets/editor.css";
        }
        
        //-- No statusbar
        $statusbar  = 'false';
        
		$buttons 	= array();
		$plugins 	= array();
		
		//-- Initial values for buttons
		array_push($buttons, 'bold', 'italic', 'underline', '|', 'bullist', 'numlist', '|');

		if($params->get('editor_html') == 1) {
			//-- Add code button
			$buttons[]	= 'code';
            $plugins[]  = 'code';
		}
		
		//-- Add links
		$buttons[]	= 'link, |';
        $plugins[]  = 'link';

		//-- Add Vitabook emoticons
        if($params->get('editor_emoticons', 1))
        {
            $plugins[]	= 'vitabookemoticons';
            $buttons[]	= 'vitabookemoticons';
        }


        //-- Check if uploading or inserting images is allowed
		if($this->canDo->get('vitabook.insert.image') OR $this->canDo->get('vitabook.upload.image'))
		{
			$plugins[]	= 'vitabookupload';
			$buttons[]	= 'vitabookupload';		
		}

		//-- Check if embedding videos is allowed
		if($this->canDo->get('vitabook.insert.video'))
        {
			$plugins[]	= 'vitabookvideo';
			$buttons[]	= 'vitabookvideo';
		}
		
		// load maxchars tinymce plugin only if limit is more than zero
		$maxchars = '';
		if($params->get('editor_maxchars', 0) != 0){
            $statusbar = 'true';
			$plugins[] = 'maxchars';
			$maxchars = '
                max_chars : '.$params->get('editor_maxchars', 0).',
                max_chars_text : "'.JText::_('COM_VITABOOK_FORM_MAX_CHARS').'"';
		}
	
		//-- Autolinks
		$plugins[]	= 'autolink';

		$buttons 	= implode(',', $buttons);
		$plugins 	= implode(',', $plugins);	

        //-- set mode
        if(JFactory::getApplication()->isSite())
            $mode = "none";
        else
            $mode = "specific_textareas";

		//-- Build editor
		$editor = "tinyMCE.init({
					directionality: \"$text_direction\",
					selector : \"textarea.mceEditor\",
					language : \"en\",
					mode : \"$mode\",
					skin : \"lightgray\",
					theme : \"modern\",
                    schema : \"html5\",
					inline : false,
					gecko_spellcheck : true,
					entity_encoding : \"raw\",
                    relative_urls : true,
					document_base_url : \"". JUri::root() ."\",
                    menubar : false,
                    statusbar : $statusbar,
                    toolbar_items_size: \"small\",
                    content_css : \"$custom_css\",
					toolbar1 : \"$buttons\",
                    plugins : \"$plugins\",
                    height : \"$height\",
                    width : \"$width\",
                    resize : \"both\",
                    ".$maxchars."
				  });";
	
		return $editor;	
	}
}