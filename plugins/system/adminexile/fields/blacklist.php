<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
// jimport('joomla.form.formfield');
// jimport('joomla.version');

class JFormFieldBlacklist extends JFormField
{
	protected $type = 'Blacklist';
	protected $app;
	protected $db;
	protected $formfields;
	protected function getLabel() {
            return '';
        }
	protected function getInput()
	{
	    $this->app = JFactory::getApplication();
            $this->db = JFactory::getDbo();
	    $this->formfields = $this->form->getFieldset();
            $blacklistnets = array();
            $blacklistaddresses = array();
            $blacklistinput = preg_replace(array('/\r\n$/','/\r$/'),"\n",$this->_getField('blacklist'));
            foreach(explode("\n",$blacklistinput) as $blacklistitem) {
		$blacklistitem = trim($blacklistitem);
                if(preg_match('/\//',$blacklistitem)) {
		    if(
                        function_exists('gmp_init') && 
                        function_exists('gmp_setbit') && 
                        function_exists('gmp_strval') && 
                        function_exists('gmp_testbit') && 
                        function_exists('gmp_cmp') && 
                        function_exists('gmp_intval') && 
                        function_exists('gmp_add') && 
                        function_exists('gmp_neg') && 
                        function_exists('gmp_mul') && 
                        function_exists('gmp_sub') && 
                        function_exists('gmp_pow') && 
                        function_exists('gmp_scan0') && 
                        function_exists('gmp_and') && 
                        function_exists('gmp_or')
                    ) {
                        require_once(JPATH_ROOT.'/plugins/system/adminexile/classes/IPv6Net.class.php');
                        $blacklistnets[$blacklistitem]=new IPv6Net($blacklistitem);
		    } else {
                        require_once(JPATH_ROOT.'/plugins/system/adminexile/classes/simplecidr.class.php');
                        $blacklistnets[$blacklistitem]=SimpleCIDR::getInstance($blacklistitem);		      
		    }
                } else {
                    $blacklistaddresses[trim($blacklistitem)]=$blacklistitem;
                }
            }
            $query = $this->db->getQuery(true);
            $query->select('*')->from('#__plg_system_adminexile')->where('penalty = 0')->order('lastattempt DESC');
            $this->db->setQuery($query);
            $blocked = $this->db->loadObjectList();
            $return=array();
	    if(function_exists('gmp_pow')) {
	      $return[]='<h3 style="float:left;clear:left;">'.JText::_('PLG_SYS_ADMINEXILE_IPV46').'</h3>';
	    } else {
	      $return[]='<h3 style="float:left;clear:left;">'.JText::_('PLG_SYS_ADMINEXILE_IPV4').'</h3>';
	    }
            if(!count($blocked)) return implode("\n",$return);
            $version = new JVersion;
            $shortversion = explode('.',$version->getShortVersion());
            $deletetext = '';
            if($shortversion[0] == 2) {
                $deletetext = JText::_('JACTION_DELETE');
            }
            $return[]='<table class="table table-condensed table-striped ipsecurity"><tr><th>IP</th><th>'.JText::_('PLG_SYS_ADMINEXILE_MATCH_RULE').'</th><th>'.JText::_('PLG_SYS_ADMINEXILE_LAST_ATTEMPT').'</th><th>'.JText::_('PLG_SYS_ADMINEXILE_ATTEMPTS').'</th><td></td></tr>';
            foreach($blocked as $match) {
                $buttons = '<button class="btn btn-mini removeblock hasTip" data-block="'.htmlentities(json_encode(array('ip'=>$match->ip,'firstattempt'=>$match->firstattempt))).'" data-toggle="tooltip" title="'.JText::_('JACTION_DELETE').'"><i class="icon-trash"></i>'.$deletetext.'</button>';
                if(in_array($match->ip,$blacklistaddresses)) {
                    $return[]='<td>'.$match->ip.'</td><td></td><td>'.$match->lastattempt.'</td><td>'.$match->attempts.'</td><td>'.$buttons.'</td></tr>';
                } else {
                    $matchnet=false;
                    foreach(array_keys($blacklistnets) as $key) {
                        if($matchnet) continue;
                        if($blacklistnets[$key]->contains(trim((string)$match->ip))) {
                            $matchnet = $key;
                            $return[]='<td>'.$match->ip.'</td><td>'.$key.'</td><td>'.$match->lastattempt.'</td><td>'.$match->attempts.'</td><td>'.$buttons.'</td></tr>';                           
                        }
                    }
                }
            }
            $return[]='</table>';
            return implode("\n",$return);
	}
        private function _getField($name) {
//             $fields = $this->form->getFieldset();
//             foreach($fields as $field) {
            foreach($this->formfields as $field) {
                if ( $field->name == 'jform[params]['.$name.']' || $field->name == 'jform[params]['.$name.'][]' ) {
                    return $field->value;
                }
            }               
        }
}
