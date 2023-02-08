<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/ 
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );
class OPCcontroller extends JControllerLegacy {

}
jimport( 'joomla.application.component.model' );
class OPCmodel extends JModelLegacy {

}
jimport('joomla.application.component.view');
class OPCView extends JViewLegacy {

}
define('OPCJ3', true); 
class OPCPane 
{
  var $options; 
  var $type; 
  var $name; 
  function getInstance($type='TabSet', $options, $name='myTabs')
  {
    JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   jimport ('joomla.html.html.bootstrap');
	$arr = array('active'=>$options['active']); 
    JHtml::_('jquery.framework');
	JHtml::_('jquery.ui');
    $this->options = $arr; 
	$pane = new OPCPane(); 
	$pane->type = $type; 
	$pane->name = $name; 
	$pane->options = $options; 
	return $pane; 
  }
  function startPane($type)
   {
     //return JHtml::_('tabs.start', 'tab_group_id', $this->options);
	 $this->name = $type; 
	 return JHtml::_('bootstrap.startTabSet', $this->name, $this->options);
	 
   }
  function startPanel($name, $id)
   { 
     return JHtml::_('bootstrap.addTab', $this->name, $id, $name, true);
     //return JHtml::_('tabs.panel', $name, $id);
   }
  function endPanel()
  {
    return JHtml::_('bootstrap.endTab');
    return ''; 
  }
  
  function endPane()
  {
   return JHtml::_('bootstrap.endTabSet');
   return JHtml::_('tabs.end');
  }
}

class OPCUtility {
 function getToken()
   {
      return JSession::getFormToken();
   }
}

class JHTMLOPC
{
 public static function stylesheet($file, $path, $option=array())
  {
    $base = Juri::base(true); 
	$path = str_replace('administrator/', '', $path); 
	//if (stripos($path, $base)===0) $base = str_replace($base, '', $path); 
    JHTML::stylesheet(Juri::base().$path.$file);
  }
  public static function script($file, $path, $mootools=false)
  {
   $base = Juri::base(true); 
   $path = str_replace('administrator/', '', $path); 
	//if (stripos($path, $base)===0) $base = str_replace($base, '', $path); 
   JHTML::script(Juri::base().$path.$file, $mootools);
  }
}

class OPCObj {
  private $data; 
  function __construct($data)
   {
      $this->data =& $data; 
   }
  function get($key, $val=null)
  {
    if (method_exists($this->data, 'get'))
	$this->data->get($key, $val); 
	else
	$this->data->getValue($key, $val); 
  }
  function getValue($key, $val=null)
  {
    if (method_exists($this->data, 'get'))
	$this->data->get($key, $val); 
	else
	$this->data->getValue($key, $val); 
  }
  
}

class OPCParameter {
  private $data; 
  function __construct($json)
    {
	   $this->data = json_decode($json, true); 
	}
  public function get($key, $default)
    {
	   if (isset($this->data[$key])) return $this->data[$key]; 
	   return $default; 
	}
}


if (!defined('DS'))
define('DS', DIRECTORY_SEPARATOR); 