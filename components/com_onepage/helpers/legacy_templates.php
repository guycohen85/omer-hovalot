<?php
/**
 * Legacy template loader for One Page Checkout 2 for VirtueMart 2
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

$document = JFactory::getDocument();

 ob_start();  
  echo '<div id="vmMainPageOPC">'; 
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 
  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
  $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
 
 if (OPCloader::checkOPCSecret())
 {
	 $selected_template .= '_preview'; 
 }
 
 $currentUser =& JFactory::getUser();
 $uid = $currentUser->get('id');
 if (!empty($uid)) 
 { 
 
 $no_login_in_template = true; 
 }
 
 
 {
 JHTMLOPC::stylesheet('onepage.css', 'components/com_onepage/themes/'.$selected_template.'/', array());
 //JHTML::_('behavior.formvalidation');
 JHTMLOPC::stylesheet('vmpanels.css', 'components/com_virtuemart/assets/css/', array());
 }
 
 if (!empty($load_min_bootstrap))
 {
 JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());
 }
 
 if (empty($this->cart) || (empty($this->cart->products)))
 {
   $continue_link = $tpla['continue_link']; 
   include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'empty_cart.tpl.php'); 
 }
 else
 {
 
 if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
 {
 $no_login_in_template = true; 
 }
 
 
 extract($tpla);
 
 if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
 
 if (method_exists('shopfunctionsF', 'getComUserOption'))
 $comUserOption=shopfunctionsF::getComUserOption();
 else $comUserOption= 'com_users'; 

 $VM_LANG = new op_languageHelper(); 
 $GLOBALS['VM_LANG'] = $VM_LANG; 
 $lang =& JFactory::getLanguage();
 $tag = $lang->getTag();
 $langcode = JRequest::getVar('lang', ''); 
 $no_jscheck = true;
 define("_MIN_POV_REACHED", '1');
 $no_jscheck = true;
 
 if (empty($langcode))
 {
 if (!empty($tag))
 {
 $arr = explode('-', $tag); 
 if (!empty($arr[0])) $langcode = $arr[0]; 
 }
 if (empty($langcode)) $langcode = 'en'; 
 }
 $GLOBALS['mosConfig_locale'] = $langcode; 

 // legacy vars to be deleted: 
 
 $op_disable_shipping = OPCloader::getShippingEnabled($this->cart); 
 
 
 if (empty($op_disable_shipping)) $op_disable_shipping = false;
 $no_shipping = $op_disable_shipping; 
 

 
$cart = $this->cart;

 
 if ((!empty($min_reached_text)) && (file_exists(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'onepage.min.tpl.php')))
 {
    echo '<div class="opc_minorder_wrapper" id="opc_minorder_wrapper" >'; 
    include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'onepage.min.tpl.php'); 
    echo '</div>'; 
 }
 else
 if (($this->logged($cart)))
 {
 // let's set the TOS config here
 echo '<div class="opc_logged_wrapper" id="opc_logged_wrapper" >'; 
 include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'onepage.logged.tpl.php'); 
 echo '</div>'; 
 }
 else
 {
 echo '<div class="opc_unlogged_wrapper" id="opc_unlogged_wrapper" >'; 
 include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'onepage.unlogged.tpl.php'); 
 echo '</div>'; 
 }
 }
 if (file_exists(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'include.php'))
 include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'include.php'); 
 echo '</div>';
 
 $output = ob_get_clean(); 
 //post process
 $output = str_replace('name="adminForm"', ' id="adminForm" name="adminForm" ', $output);

 //html5 spec for enter key: 
 $x1 = stripos($output, 'id="adminForm"'); 
 if ($x1 !== false)
  {
     $x2 = stripos($output, '>', $x1); 
	 $add = '<div style="display: none;"><input type="submit" onclick="return Onepage.formSubmit(event, this);" name="hidden_submit" value="hidden_submit" /></div>'; 
	 $output = substr($output, 0, $x2+1).$add.substr($output, $x2+1); 
  }
 
 if (!class_exists('OPCloadmodule'))
 {
   require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loadmodule.php'); 
 }
 OPCloadModule::onContentPrepare('text', $output); 
 /*
 jimport( 'joomla.plugin.helper' );
 $dispatcher = JDispatcher::getInstance();
 JPluginHelper::importPlugin('content', 'loadmodule', true, $dispatcher); // very important
 if (class_exists('plgContentLoadmodule'))
 {
  $params =& $mainframe->getParams('loadmodule'); 
  $cl = new plgContentLoadmodule($params); 
  $data = new stdClass(); 
  $data->text = $output; 
  
  $results = $dispatcher->trigger('onPrepareContent', array( &$data, &$params, 0)); 
  $results = $dispatcher->trigger('onContentPrepare', array( 'text', &$data, &$params, 0)); 
  if (!empty($data->text)) $output = $data->text; 
 }
 */
 // legacy support
 $output = str_replace('"showSA', '"Onepage.showSA', $output); 
 $output = str_replace('javascript: showSA', 'javascript: Onepage.showSA', $output); 
 $output = str_replace('javascript: return op_login', 'javascript: return Onepage.op_login', $output); 
 $output = str_replace('javascript: return op_login', 'javascript: return Onepage.op_login', $output); 
 $output = str_replace('return submitenter', 'return Onepage.submitenter', $output); 
 $output = str_replace('return op_openlink', 'return Onepage.op_openlink', $output); 
 $output = str_replace('return changeST', 'return Onepage.changeST', $output); 
 $output = str_replace('"showSA', '"Onepage.showSA', $output); 
 
 //return op_openlink
 $output = str_replace('return op_unhide(', 'return Onepage.op_unhide(', $output); 
 //$output = str_replace('"showFields(', '"Onepage.showFields(', $output); 
 $output = str_replace('onchange="showFields(', 'onclick="return Onepage.showFields(', $output); 
 //return submitenter
 //$output = str_replace('javascript: showSA', 'javascript: Onepage.showSA', $output); 
 //javascript: return op_login();
 echo $output; 
