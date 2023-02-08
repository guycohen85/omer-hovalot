<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.version');

class JFormFieldJavascript extends JFormField
{
	protected $type = 'Javascript';
        protected function getLabel(){
            return '';
        }
	protected function getInput()
	{
                $options = array();
                $messages = array('INVALIDASCII','INVALIDCHAR','NOTNUMERIC');
                $chars = array('DOLLAR','AMPERSAND','PLUS','COMMA','FORWARDSLASH','COLON','SEMICOLON','EQUALS','QUESTION','AT','SPACE','QUOTE','LESSTHAN','GREATERTHAN','POUND','PERCENT','LEFTCURLY','RIGHTCURLY','PIPE','BACKSLASH','CARAT','TILDE','LEFTBRACKET','RIGHTBRACKET','GRAVE');
                foreach($messages as $string) JText::script('PLG_SYS_ADMINEXILE_MESSAGE_'.$string);
                foreach($chars as $string) JText::script('PLG_SYS_ADMINEXILE_CHAR_'.$string);
                $version = new JVersion;
                $shortversion = explode('.',$version->getShortVersion());
                $options['version']=$shortversion[0].'.'.$shortversion[1];
                JHtml::_('behavior.framework',true);
		$doc = JFactory::getDocument();
                $doc->addScriptDeclaration("\n".'window.plg_sys_adminexile_config = '.json_encode($options).';'."\n");
                $doc->addScript(JURI::root(true).'/media/plg_system_adminexile/admin.js');
                $doc->addStyleDeclaration($this->_getCSS($shortversion[0]));
		return;
	}
        function _getCSS($version) {
            $return = array();
            switch($version) {
                case 2:
                    $return[]='table.ipsecurity, table.bruteforce {';
                        $return[]='clear:left;';
                        $return[]='width:100%;';
                    $return[]='}';
                    $return[]='table.ipsecurity th, table.bruteforce th,';
                    $return[]='table.ipsecurity td, table.bruteforce td {';
                        $return[]='border-bottom:1px solid #ccc;';
                    $return[]='}';
                    break;
                default:
                    break;
            }
            return implode("\n",$return);
        }
}
