<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
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
/*
$x = file_get_contents(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.'sandwitch'.DS.'blue.css'); 
$m = array(); 
preg_match_all('((#([0-9A-Fa-f]{3,6})\b)|(aqua)|(black)|(blue)|(fuchsia)|(gray)|(green)|(lime)|(maroon)|(navy)|(olive)|(orange)|(purple)|(red)|(silver)|(teal)|(white)|(yellow)|(rgb\(\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*\))|(rgb\(\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*\)))', $x, $m, PREG_OFFSET_CAPTURE); 
var_dump($m); die(); 
*/

$version = ''; 
if (!defined('OPCVERSION'))
{
if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'version.php'))
{
  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'version.php'); 
}
}

jimport ('joomla.html.html.bootstrap');



JHtml::_('behavior.keepalive');

//JHtml::_('formbehavior.chosen', 'select');

if (OPCVERSION != '{OPCVERSION}')
$version = ' ('.OPCVERSION.')'; 

	
	ob_start();
	JToolBarHelper::Title(JText::_('COM_ONEPAGE_CONFIGURATION_TITLE').$version , 'generic.png');
//	JToolBarHelper::install();
	JToolBarHelper::apply();
/*	JToolBarHelper::apply(); */
	//JToolBarHelper::cancel();
$document = JFactory::getDocument();
				$selectText = JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES');
				$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('Select All')."',select_some_options_text: '".JText::_($selectText)."'" ;
				


	if (!OPCJ3)
	{
	  //JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());
	}
				
	if (!class_exists('VmConfig'))
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
	    VmConfig::loadConfig(); 
		if (method_exists('vmJsApi', 'js'))
		{
		$app = JFactory::getApplication(); 
		$jq = $app->get('jquery'); 
		if (empty($jq) && (!OPCJ3))
		{
		vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
		vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
		}
		if (OPCJ3)
		 {
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 else
		 {
		vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
		$document->addScriptDeclaration ( '
//<![CDATA[
		var vm2string ={'.$vm2string.'} ;
		 jQuery( function($) {
			$(".vm-chzn-select").chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text});
		});
//]]>
				');
		
		
		}
		else
		{
		vmJsApi::jQuery(); 
		}
		$document->addScript(JURI::base().'components/com_virtuemart/assets/js/jquery.noConflict.js');
		
		
	$css = ' .chzn-container-multi .chzn-choices .search-field input {
	 height: 25px; 
	} 
	iframe {
	  width: 95%; 
	  height: 300px;
	  border: 1px solid #ddd; 
	}
	
	'; 
	
	$document->addStyleDeclaration($css); 
	$docj = JFactory::getDocument();
	$url = JURI::base(true); 
	if (substr($url, strlen($url))!= '/') $url .= '/'; 
	$javascript =  "\n".' var op_ajaxurl = "'.$url.'"; '."\n";
	/*
    $javascript .= 'if(window.addEventListener){ // Mozilla, Netscape, Firefox' . "\n";
    $javascript .= '    window.addEventListener("load", function(){ op_runAjax(); }, false);' . "\n";
    $javascript .= '} else { // IE' . "\n";
    $javascript .= '    window.attachEvent("onload", function(){ op_runAjax(); });' . "\n";
    $javascript .= '}';
    */
	$docj = JFactory::getDocument();
	$docj->addScriptDeclaration( $javascript );	
	
	$c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	if ($test != 10)
	 {
	   $is_admin =false; 
	 }
	 else $is_admin = true; 
	
      $session = JFactory::getSession();
      
        jimport('joomla.html.pane');
        jimport('joomla.utilities.utility');
	JHTML::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
    
		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		  VmConfig::loadConfig(true); 

	



$document = JFactory::getDocument();
//$document->addScript('/administrator/includes/js/joomla.javascript.js');
	$is_admin = true; 
    include(JPATH_SITE.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php");
   	$document = JFactory::getDocument();
	$style = '
	
	div.current {
	 float: left;
	 
	 width: 98%;
	}
	div {
	 text-indent: 0;
	}
	dl {
	 margin-left: 0 !important;
	 padding: 0 !important;
	}
	dd {
	 margin-left: 0 !important;
	 padding: 0 !important;
	 width: 100%;
	 
	}
	dd div {
	 margin-left: 0 !important;
	 padding-left: 0 !important;
	 text-indent: 0 !important;
	 
	 
	}
	div.current dd {
	 display: block;
	 padding-left:1px;
     padding-right:1px;
     margin-left:1px;
     margin-right:1px;
     text-indent:1px;
     float: left;
	}
	input[type="button"]:hover, input[type="button"]:active {
	  background-color: #ddd; 
	}
	
	';
	if (!OPCJ3)
   $document->addStyleDeclaration($style);

//include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');

// set default variables:
if (!isset($disable_onepage)) $disable_onepage = false;
if (!isset($must_have_valid_vat)) $must_have_valid_vat = true;
if (!isset($unlog_all_shoppers)) $unlog_all_shoppers = false;
if (!isset($allow_duplicit)) $allow_duplicit = true;
if (!isset($tpl_logged)) $tpl_logged = '';
if (!isset($tpl_unlogged)) $tpl_unlogged = '';
if (!isset($css_logged)) $css_logged = '';
if (!isset($css_unlogged)) $css_unlogged = '';
if (!isset($show_full_tos)) $show_full_tos = false;
if (!isset($payment_default)) $payment_default = 'default';
if (!empty($this->default_country))
if (!isset($default_shipping_country)) $default_shipping_country = $this->default_country;

$userConfig = JComponentHelper::getParams('com_users');
$regA = $userConfig->get('allowUserRegistration');
$regB = $userConfig->get('useractivation');

if (!empty($_SESSION['onepage_err'])) $msg = unserialize($_SESSION['onepage_err']).'<br />';
else $msg = ''; 

if (isset($_SESSION['onepage_err']))
{
    $msg = @unserialize($_SESSION['onepage_err']);
	if (!empty($msg))
	{
	    echo '<div style="width = 100%; border: 2px solid red;">';
	    echo $msg;
	    unset($_SESSION['onepage_err']);
	    echo '</div>';
	}
}	
	if (isset($payments_to_hide))
	{
	 $payments_to_hide = str_replace(' ', '',  $payments_to_hide);
	 $pth = explode(',', $payments_to_hide);
	}
	if (!isset($pth)) $pth = array();

?>
	
	<form action="<?php echo JURI::base(); ?>index.php?option=com_onepage&amp;controller=config&amp;<?php echo $session->getName().'='.$session->getId(); ?>&amp;<?php 
	if (method_exists('JUtility', 'getToken'))
	echo JUtility::getToken();
	else echo JSession::getFormToken();
	?>=1" method="post" name="adminForm" id="adminForm">
	<?php 
	$x = VmConfig::get('active_languages', array('en-GB')); 
	
	$selected = $opclang = JRequest::getVar('opclang', ''); 
	$flag = ''; 
	if (count($x)>1)
	{
	$a1 = explode('-', $opclang); 
	if (isset($a1[0]))
	{
	 $cl = strtolower($a1[0]); 
	 if (file_exists(JPATH_SITE.DS.'media'.DS.'mod_languages'.DS.'images'.DS.$cl.'.gif'))
	  {
	    $root = Juri::root().'/'; 
		$root = str_replace('/administrator/', '', $root); 
		
	    $flag = '<br style="clear:both;"><img src="'.$root.'/media/mod_languages/images/'.$cl.'.gif" alt="'.$opclang.'"/>'; 
		
	  }
	}
	?>
    <div class="langtab" style="clear: both; " >
	<label for="opclang"><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></label>
	<select name="opclang" id="opclang" onchange="submitbutton('changelang');">
	 <?php 
	  
	  
	  echo '<option '; 
	  if (empty($selected)) echo 'selected="selected" '; 
	  echo ' value="">'.JText::_('JALL_LANGUAGE').'</option>'; 
	  foreach ($x as $l)
	   {
	     echo '<option '; 
		 if ($selected == $l) echo ' selected="selected" '; 
		 echo ' value="'.$l.'">'.$l.'</option>'; 
	   }
	   
	 ?>
	</select>
	
	
	</div>
	
	<?php
	}
	?>
	<input type="hidden" name="opc_lang_orig" value="<?php echo $opclang; ?>" />
	<?php
	
	
        $pane = OPCPane::getInstance('tabs', array('active'=>'panel01id', 'startOffset'=>0));
        echo $pane->startPane('pane');
        
		echo $pane->startPanel(JText::_('COM_ONEPAGE_VERSION_PANEL'), 'panel01id');
		?>
		<div id="opc_new_version" style="display: none; width: 100%; background-color: green; color: white; font-weight: bold; padding:5px;"><?php echo JText::_('COM_ONEPAGE_UPDATE_AVAILABLE'); ?></div>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_VERSION_INFO'); ?></legend>
        <table class="admintable" style="width: 100%;">
		<tr>
	    <td class="key">
	     <label for="installed_version"><?php echo JText::_('COM_ONEPAGE_INSTALLED_VERSION'); ?></label> 
	    </td>
	    <td  >
		<?php echo OPCVERSION; 
		$document = JFactory::getDocument();
		$document->addScriptDeclaration(' var opc_current_version = "'.OPCVERSION.'"; ');
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'api.php'))
		{
		  $api_key = $api_stamp = 0; 
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'api.php'); 
		}
		if (empty($disable_check))
		$document->addScript('//cdn.rupostel.com/rupostel.js?opcversion='.OPCVERSION.'&api_key='.$api_key.'&api_stamp='.$api_stamp); 
		//JHtml::script('//cdn.rupostel.com/rupostel.js');

		?>
		</td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="latest_version"><?php echo JText::_('COM_ONEPAGE_AVAILABLE_VERSION'); ?></label> 
	    </td>
	    <td  ><div id="opc_latest_version_wrapper"><div id="opc_latest_version">&nbsp;<?php if (empty($disable_check)) { ?><img src="../media/system/images/mootree_loader.gif" /><?php } if (!empty($disable_check)) echo JText::_('COM_ONEPAGE_VERSION_CHECK_DISABLED'); ?> </div></div>
		</td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="change_log"><?php echo JText::_('COM_ONEPAGE_CHANGELOG'); ?></label> 
	    </td>
	    <td  >
		<div id="opc_iframe_here">&nbsp;<?php if (!empty($disable_check)) echo JText::_('COM_ONEPAGE_VERSION_CHECK_DISABLED'); ?></div>
		  
		</td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="rupostel_email"><?php echo JText::_('COM_ONEPAGE_RUPOSTEL_EMAIL'); ?></label> 
	    </td>
	    <td  >
		<?php echo JText::_('COM_ONEPAGE_RUPOSTEL_EMAIL_DESC'); ?><br />
		  <input type="text" style="width: 300px;" name="rupostel_email" value="<?php if (!empty($rupostel_email)) echo $rupostel_email; ?>" />
		</td>
		
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="disable_check2"><?php echo JText::_('COM_ONEPAGE_DISABLE_VERSION_CHECK'); ?></label> 
	    </td>
	    <td  >
		
		  <input type="checkbox" id="disable_check"  style="float: left; text-align: left;" name="disable_check" <?php if (!empty($disable_check)) echo ' checked="checked" '; ?> value="1" />
		  <label for="disable_check"><?php echo JText::_('COM_ONEPAGE_DISABLE_VERSION_CHECK_DESC'); ?></label>
		</td>
		
		</tr>
		
		
		</table>
		</fieldset>
		<?php
		echo $pane->endPanel(); 
			   
        echo $pane->startPanel(JText::_('COM_ONEPAGE_GENERAL_PANEL'), 'panel1');
?>
<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_GENERAL'); ?></legend>
        <table class="admintable" style="width: 100%;">
	<tr>
	    <td class="key">
	     <label for="disable_op"><?php echo JText::_('COM_ONEPAGE_GENERAL_DISABLEOPC_LABEL'); ?></label> 
	    </td>
	    <td  >
	    <input id="disable_op" type="checkbox" name="disable_op" value="disable" <?php if ($this->disable_onepage === true) echo 'checked="checked"'; ?>/> 

		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="config" />
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="task2" id="task2" value="" />
		<input type="hidden" name="delete_ht" id="delete_ht" value="0" />
		<input type="hidden" name="backview" id="backview" value="panel1" />


	    </td><td><?php echo JText::_('COM_ONEPAGE_GENERAL_DISABLEOPC_DESC'); ?></td>
	</tr>

	
	
	<tr>
	    <td class="key">
	     <label for="agreed_notchecked" ><?php echo JText::_('COM_ONEPAGE_GENERAL_AGREEMENTCHECKBOX_LABEL'); ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="agreed_notchecked" id="agreed_notchecked" value="agreed_notchecked" <?php if (isset($agreed_notchecked)) if ($agreed_notchecked==true) echo 'checked="checked"';?> />
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_GENERAL_AGREEMENTCHECKBOX_DESC'); ?>  
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_link_type" ><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_LABEL'); ?></label>
	    </td>
	    <td>
	     <select  name="opc_link_type" id="opc_link_type">
		   <option <?php if (empty($opc_link_type)) echo ' selected="selected" '; ?> value="0"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_NOTENABLED'); ?></option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '1')) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_DELCARTSETLINK'); ?></option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '2')) echo ' selected="selected" '; ?> value="2"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_NOTINCREMENTQUANT'); ?></option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '3')) echo ' selected="selected" '; ?> value="3"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_INCREMENTQUANT'); ?></option>
		   
		 </select>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_DESC1'); ?><a href="https://www.rupostel.com/one-page-checkout-component/features/add-to-cart-as-a-link"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_DESC2'); ?></a>. 
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_link_type" ><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKAUTO_COUPON_LABEL'); ?></label>
	    </td>
	    <td>
		<input type="text" value="<?php if (!empty($opc_auto_coupon)) echo $opc_auto_coupon; ?>" name="opc_auto_coupon" placeholder="<?php echo addslashes(JText::_('COM_ONEPAGE_GENERAL_OPCLINKAUTO_COUPON_PLACEHOLDER')); ?>" />
		</td>
		<td>
	     <?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKAUTO_COUPON_DESC'); ?>
		 
	    </td>

	</tr>
<tr>
	    <td class="key">
	     <label for="adc_op_articleid"><?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL').JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL_FOR_ADDTOCARTASLINK'); echo $flag; ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL'); ?>
	    </td>
	    <td>
		
	     <?php echo $this->articleselector3; ?>
		 <input type="button" onclick="javascript: return clearArticle('adc_op_articleid');" value="<?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_VALUE'); ?>" />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_DESC'); ?>  
	    </td>
		</tr>
	
	<?php if ($is_admin)
	{
	?>
	<tr>
	    <td class="key">
	     <label for="use_ssl" ><?php echo JText::_('COM_ONEPAGE_GENERAL_USESSL_LABEL') ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="use_ssl" id="use_ssl" value="use_ssl" <?php 
		 $useSSL = VmConfig::get('useSSL', 0);
		 if (!empty($useSSL))  echo 'checked="checked"'; ?> />
	    </td>
	    <td> 
	    </td>
	</tr>
	
	<?php 
	}
   ?>
		
<?php 
// disabled in v 210+
/*
if (false)
{
?>
	<tr>
	    <td class="key">
	     <label for="g_analytics" ><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="g_analytics" id="g_analytics">
		<option <?php if (($g_analytics==true) || (!isset($g_analytics))) echo 'selected="selected"'; ?> value="1"><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_SELECT_YES'); ?></option>
		<option <?php if ($g_analytics===false) echo 'selected="selected"'; ?>value="0"><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_SELECT_NO'); ?></option>
		</select> 
		
	    </td>
		<td>
		<?php if (false) { ?>
		<label for="google_id"><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ID_LABEL'); ?></label>
		<input type="text" id="google_id" name="google_id" value="<?php if (!empty($google_id)) echo $google_id; ?>" />
		<br style="clear: both;"/>
		<?php 
		}
		?>
		
		<?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_DESC'); ?> 
	    </td>
	</tr>
<?php 

}
*/
?>	
	<tr>
	    <td class="key">
	     <label for="opc_memory" ><?php echo JText::_('COM_ONEPAGE_OPC_MEMORY_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="opc_memory" id="opc_memory">
		<option <?php if (empty($opc_memory) || ($opc_memory=='128M')) echo 'selected="selected"'; ?> value="128M">128M</option>
		<option <?php if (!empty($opc_memory) && ($opc_memory=='256M')) echo 'selected="selected"'; ?> value="256M">256M</option>
		<option <?php if (!empty($opc_memory) && ($opc_memory=='64M')) echo 'selected="selected"'; ?> value="64M">64M (<?php echo JText::_('COM_ONEPAGE_OPC_NOT_RECOMMENDED'); ?>)</option>
		</select> 
		
	    </td>
		<td>
		
		<?php 
		$x = @ini_get("memory_limit"); 
		echo JText::_('COM_ONEPAGE_MEMORY_DESC').$x; 
		
			$x1 = @ini_set('memory_limit', '256M'); 
			$x2 = @ini_get('memory_limit'); 
			
			if (($x2 == '128M') || ($x2 == -1))
			{
			echo JText::_('COM_ONEPAGE_ERROR_SETTING_MEMORY_LIMIT'); 
			}
			else
			if (($x2 != '256M') || ($x1 === false)) echo ' <b style="color:red;">'.JText::_('COM_ONEPAGE_ERROR_SETTING_MEMORY_LIMIT').'</b>'; 
		
		
		?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_plugin_order" ><?php echo JText::_('COM_ONEPAGE_PLUGIN_ORDER_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="text" name="opc_plugin_order" id="opc_plugin_order" value="<?php 
				
				if (!isset($opc_plugin_order)) echo '-9999'; else echo $opc_plugin_order; 
				
				?>"  />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_PLUGIN_ORDER_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_disable_for_mobiles" ><?php echo JText::_('COM_ONEPAGE_DISABLE_FOR_MOBILES_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_disable_for_mobiles" id="opc_disable_for_mobiles" value="1" <?php if (!empty($opc_disable_for_mobiles)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_DISABLE_FOR_MOBILES_DESCRIPTION'); 
		
		
		
		?> 
	    </td>
	</tr>

	
		<tr>
	    <td class="key">
	     <label for="opc_debug" ><?php echo JText::_('COM_ONEPAGE_DEBUG_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_debug" id="opc_debug" value="1" <?php if (!empty($opc_debug)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_DEBUG_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_async" ><?php echo JText::_('COM_ONEPAGE_OPC_ASYNC_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_async" id="opc_async" value="1" <?php if (!empty($opc_async)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_OPC_ASYNC_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_php_js2" ><?php echo JText::_('COM_ONEPAGE_LOAD_OPC_CONFIG'); ?></label>
	    </td>
	    <td>
				<input type="hidden" name="opc_php_js" id="opc_php_js" value="0" /><input type="checkbox" <?php if (!empty($opc_php_js2)) echo ' checked="checked" '; if (!isset($opc_php_js2)) echo ' checked="checked" '; ?> value="1" name="opc_php_js2" id="opc_php_js2" />
				
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_LOAD_OPC_CONFIG_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>



	

	
	


        </table>
    </fieldset>    
	
	<script type="text/javascript">
//<![CDATA[
//http://www.codeproject.com/Tips/585663/Communication-with-Cross-Domain-IFrame-A-Cross-Bro
// Here "addEventListener" is for standards-compliant web browsers and "attachEvent" is for IE Browsers.
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];

// Now...
// if 
//    "attachEvent", then we need to select "onmessage" as the event. 
// if 
//    "addEventListener", then we need to select "message" as the event

var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

// Listen to message from child IFrame window
eventer(messageEvent, function (e) {
	
	if ((e.origin == 'https://cdn.rupostel.com') || (e.origin == 'http://cdn.rupostel.com') || (e.origin == '//cdn.rupostel.com')) {
		setVersion(e.data); 
		// Do whatever you want to do with the data got from IFrame in Parent form.
	}
}, false);    



	var op_next = 0;
	<?php 
	if (false) 
	{
	?>
	var html1 = '<tr><td class="key"><label for="hidep_';
	var html2 = '" >Payment configuration: </label></td><td colspan="3" > For this shipping method <select style="max-width: 100px;"  id="hidepsid_';
	var html21 = '" name="hidepsid_';
	var html3 = '"><option value="del" selected="selected">NOT CONFIGURED/DELETE</option><?php
		  if (!empty($this->sids))
		  foreach ($this->sids as $k => &$sid)
		  {
		  ?><option value="<?php echo addslashes($k); ?>"><?php echo $sid ?></option><?php
		  }
		  ?></select> 	disable these payment payments methods (use CTRL)		<select style="max-width: 100px;" multiple="multiple" size="5" id="hidep_';
	var html31 = '" name="hidep_';	
	var html4 = '[]">	<?php
		if (!empty($this->pms))
		foreach($this->pms as $p)
		{
		 ?> <option value=<?php echo '"'.addslashes($p['payment_method_id']).'" '; ?>><?php echo addslashes($p['payment_method_name']);?></option><?php
		}
		?></select>and make default this one	<select style="max-width: 100px;" id="hidepdef_';
	var html41 = '"  name="hidepdef_';	
	var html5 = '">	<?php
	    if (!empty($this->pms))
		foreach($this->pms as $p)
		{
		 ?> <option value=<?php echo '"'.$p['payment_method_id'].'" ';  ?>><?php echo addslashes($p['payment_method_name']);?></option><?php
		}
		?></select><a href="#" onclick="javascript: return(addnew());"> Click here to ADD MORE ... </a>	    </td>	</tr>';
    <?php } ?>


		




	
//]]>
	</script>

	
<?php    

        echo $pane->endPanel();
    echo $pane->startPanel(JText::_('COM_ONEPAGE_SHIPPING_PANEL'), 'panel77');
?>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_SHIPPING'); ?></legend>
        <table class="admintable" id="comeshere" style="width: 100%;">
	<tr>
	    <td class="key">
	     <label for="op_disable_shipping" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_LABEL'); ?> </label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DISABLE_LABEL'); ?>
		 
	    </td>
	    <td  >
		 <?php $sa = VmConfig::get('automatic_shipment', 0); 
		 
		 ?>
	     <input type="checkbox" name="op_disable_shipping" id="op_disable_shipping" <?php 
		 //if (VmConfig::get('automatic_shipment', 0)==1) echo ' disabled="disabled" '; 
		 //else 
		 if (!empty($op_disable_shipping))echo 'checked="checked"'; ?> value="op_disable_shipping"  />
	    </td>
	    <td>
	     <?php if (VmConfig::get('automatic_shipment', 1)) echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_DESC').' '.JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION'); ?>
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_disable_shipto" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_LABEL'); ?>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_disable_shipto" id="op_disable_shipto" value="op_disable_shipto" <?php if (!empty($op_disable_shipto))echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_DESC'); ?>
	    </td>
	</tr>
<tr>
	    <td class="key" >
	     <label for="only_one_shipping_address" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_LABEL'); ?>
	    </td>
	    <td>
	    <input type="checkbox" id="only_one_shipping_address" name="only_one_shipping_address" value="only_one_shipping_address" <?php if (!empty($only_one_shipping_address)) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_DESC') ?>
		<br /><div>
		<table class="adminList">
		<tr  class="row0">
		<td>
		<input type="checkbox" id="only_one_shipping_address_hidden" name="only_one_shipping_address_hidden" value="only_one_shipping_address_hidden" <?php if (!empty($only_one_shipping_address_hidden)) echo 'checked="checked"'; ?> /> 		
		</td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_ONLY_ONE_ST_HIDDEN'); ?>
		</td>
		</tr>
		</table>
		</div>
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_dontloadajax" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DONTLOADAJAX_LABEL'); ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_dontloadajax" id="op_dontloadajax" value="op_dontloadajax" <?php if (isset($op_dontloadajax)) if ($op_dontloadajax==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_SHIPPING_DONTLOADAJAX_DESC'); ?>	
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_loader" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_LOADER_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_loader" id="op_loader" value="op_loader" <?php if (!empty($op_loader)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	       <?php echo JText::_('COM_ONEPAGE_SHIPPING_LOADER_DESC'); ?>
	    </td>
	</tr>

		<tr style="display: none;">
	    <td class="key">
	     <label for="shipping_error_override"><?php echo JText::_('COM_ONEPAGE_SOON_AVAILABLE'); ?><br /><?php echo JText::_('COM_ONEPAGE_SHIPPING_ERROR_OVERRIDE_LABEL'); ?></label>
	    </td>
	    <td colspan="2">
	    <?php echo JText::_('COM_ONEPAGE_SHIPPING_ERROR_OVERRIDE_DESC'); ?><br />
		<?php echo JText::_('COM_ONEPAGE_SHIPPING_ERROR_OVERRIDE_SEARCH_FOR'); ?> <input type="text" name="shipping_error_override" id="shipping_error_override" value="<?php if (!empty($shipping_error_override)) echo urldecode($shipping_error_override); else echo 'ERROR'; ?>"/>
		</td><td>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_zero_weight_override" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_LABEL'); ?>
	    </td>
	    <td>
	     <input type="checkbox" name="op_zero_weight_override" id="op_zero_weight_override" value="op_free_shipping" <?php if (isset($op_zero_weight_override)) if ($op_zero_weight_override==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_DESC'); ?><br />
		 <table>
		 <tr>
		 <td>
		 <input type="checkbox" name="disable_ship_to_on_zero_weight" value="1" <?php if (!empty($disable_ship_to_on_zero_weight)) echo ' checked="checked" '; ?> id="disable_ship_to_on_zero_weight" />
		 </td>
		 <td>
		   <?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_DISABLESHIP_DESC'); ?>
		  </td>
		 </tr>
		 </table>
	    </td>
	</tr>
		

	<tr>
	    <td class="key">
	     <label for="op_delay_ship" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELAY_SHIP_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DELAY_SHIP_LABEL'); ?>
	    </td>
	    <td>
	     <input type="checkbox" name="op_delay_ship" id="op_delay_ship" value="op_delay_ship" <?php if (!empty($op_delay_ship)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      <?php echo JText::_('COM_ONEPAGE_SHIPPING_DELAY_SHIP_DESC'); ?> 
	    </td>
	</tr>
<?php if (false) { ?>
	<tr>
	    <td class="key">
	     <label for="op_last_field" ><?php echo JText::_('COM_ONEPAGE_SOON_AVAILABLE'); ?><br /><?php echo JText::_('COM_ONEPAGE_SHIPPING_LAST_FIELD_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_last_field" id="op_last_field" value="op_last_field" <?php if (!empty($op_last_field)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      <?php echo JText::_('COM_ONEPAGE_SHIPPING_LAST_FIELD_DESC'); ?>
	    </td>
	</tr>
<?php } ?>
	<tr>
	    <td class="key">
	     <label for="op_customer_shipping" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_CUSTOM_INIT_SHIPPING_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_customer_shipping" id="op_customer_shipping" value="op_customer_shipping" <?php if (!empty($op_customer_shipping)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      <?php echo JText::_('COM_ONEPAGE_SHIPPING_CUSTOM_INIT_SHIPPING_DESC'); ?>
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="shipping_inside_basket" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_BASKET_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_INSIDE_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input  class="shipping_inside_basket" type="checkbox" name="shipping_inside_basket" id="shipping_inside_basket" <?php if (!empty($shipping_inside_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_BASKET_DESC') ?>
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="shipping_inside" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_LABEL'); ?>
	    </td>
	    <td>
	     <input class="shipping_inside" type="checkbox" name="shipping_inside" id="shipping_inside" <?php if (!empty($shipping_inside)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td colspan="2"><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_NOTICE'); 
		// Removed in 2.0.196
		if (false)
		{
		?><br /><input class="shipping_inside_choose" type="checkbox" name="shipping_inside_choose" id="shipping_inside_choose" <?php if (!empty($shipping_inside_choose)) echo 'checked="checked"'; ?>/> <?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_DESC'); 
		}
		?>
		
	    </td>
	</tr>

	
	<tr>
	    <td class="key">
	     <label for="shipping_template" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_TEMPLATE_LABEL'); ?> </label>
	    </td>
	    <td>
	     <input class="shipping_template" type="checkbox" name="shipping_template" id="shipping_template" <?php if (!empty($shipping_template)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td colspan="2"><?php echo JText::_('COM_ONEPAGE_SHIPPING_TEMPLATE_DESC'); ?>  
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label  ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_LABEL'); ?>
	    </td>
	    <td colspan="2">
	     <label for="disable_payment_per_shipping"><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_ENABLE_LABEL'); ?></label>
		 <input value="ok" class="disable_payment_per_shipping" type="checkbox" name="disable_payment_per_shipping" id="disable_payment_per_shipping" <?php if (!empty($disable_payment_per_shipping)) echo 'checked="checked"'; ?>/> 
		 <br />
		 <?php
		 $iq = 0; 
		 if (empty($dpps_default)) $dpps_default[0] = ''; 
		 if (empty($dpps_search)) $dpps_search[0] = ''; 
		 if (empty($dpps_disable)) $dpps_disable[0] = ''; 
		 
		 $a = $dpps_search; 
		 foreach ($a as $iq=>$v)
		 {
		 $html = '
		 <div id="dpps_section_'.$iq.'">
		 <label for="">'.JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_SEARCH_LABEL').'</label><input id="dpps_search_'.$iq.'" name="dpps_search['.$iq.']" type="text" value="';
		 if (!empty($dpps_search[$iq])) $html .= urldecode($dpps_search[$iq]); 
		 $html .= '" />
		 <br />
		 <label for="dpps_disable_'.$iq.'">'.JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_DISABLE_LABEL').'</label>
		 <select id="dpps_disable_'.$iq.'" name="dpps_disable['.$iq.']">
		<option value="" ';
		if (empty($dpps_disable[$iq])) $html .= ' selected="selected" '; 
		$html.= '		>'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> '; 
		foreach($this->pms as $p)
		{
		$html .= '<option value="'.$p['payment_method_id'].'" '; 
		if ((!empty($dpps_disable[$iq])) && $p['payment_method_id']==$dpps_disable[$iq]) $html .= ' selected="selected" '; 
		$html .= '>'.$p['payment_method_name'].'</option>'; 
		 
		}
		$html .= '
		
		</select>
		 <br />
		 
		 <label for="dpps_default_'.$iq.'">'.JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_DISABLE_DEFAULT').'</label>
		  <select id="dpps_default_'.$iq.'" name="dpps_default['.$iq.']">
		<option value="" '; 
		if (empty($dpps_default[$iq])) $html .= ' selected="selected" '; 
		$html .= '>'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>'; 
		foreach($this->pms as $p)
		{
		 $html .= '<option value='; 
		 $html .= '"'.$p['payment_method_id'].'" '; 
		 if ((!empty($dpps_default[$iq])) && $p['payment_method_id']==$dpps_default[$iq]) $html .= ' selected="selected" '; 
		 $html .= '>'; 
		 $html .= $p['payment_method_name'].'</option>'; 
		 
		}
		$html .='
		</select>
		 <br />
		<div id="dpps_addhere_'.$iq.'">&nbsp;</div>		 
		 </div>'; 
		 echo $html; 
		 //if ($iq == (count($dpps_search)-1)) echo ''; 
		 }
		 ?>
		
		 <script type="text/javascript">
//<![CDATA[			 
		  var opc_last_dpps = <?php 
		  //echo count($dpps_search)-1; 
		  echo $iq
		  ?>;

//]]>		   
		 </script>
		 <br />
		 <div style="clear: both">
		 <a href="#" onclick="javascript: return add_dpps()" ><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?></a>
		 </div>
	    </td>
	    
	</tr>
	<tr>
	    <td class="key">
	     <label for="opc_default_shipping"><?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DEFAULT_SHIPPING'); ?>
	    </td>
	    <td colspan="2">
		 <select name="opc_default_shipping" id="opc_default_shipping">
		  <option <?php if (empty($opc_default_shipping) && (empty($op_default_shipping_zero))) echo ' selected="selected" '; ?> value="0"><?php echo JText::_('COM_ONEPAGE_SELECT_NOT_ZERO'); ?></option>
		  <option <?php 
		  
		  if (empty($opc_default_shipping) && (!empty($op_default_shipping_zero))) echo ' selected="selected" '; 
		  else
		  if (!empty($opc_default_shipping)) if ($opc_default_shipping === 1) echo ' selected="selected" '; 
		  
		  ?> value="1"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_PRICE_LABEL'); ?></option>
		  <option <?php if (!empty($opc_default_shipping)) if ($opc_default_shipping == 2) echo ' selected="selected" '; ?> value="2"><?php echo JText::_('COM_ONEPAGE_SELECT_THE_MOST_EXPENSIVE'); ?></option>
		  <option <?php 
		  
		  if (!empty($opc_default_shipping)) 
		  {
		  if ($opc_default_shipping == 3) echo ' selected="selected" '; 
		  }
		  else 
		  if (!empty($shipping_inside_choose))
		  echo ' selected="selected" '; 
		  
		  ?> value="3"><?php echo JText::_('COM_ONEPAGE_SELECT_NONE'); ?></option>
		 </select>
	    <?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_DESC'); ?> 
	    </td>
	    
	</tr>
<?php
// stAn removed in 2.0.196
if (false) { 
?>
	<tr>
	    <td class="key">
	     <label for="op_default_shipping_zero"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_PRICE_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_default_shipping_zero" id="op_default_shipping_zero" <?php if (!empty($op_default_shipping_zero)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_PRICE_DESC'); ?> 
		</td>
	</tr>
<?php } 
?>
		<tr >
	    <td class="key">
	     <label for="op_default_shipping_search"><?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_LABEL'); ?>
	    </td>
		
		
	    <td id="op_default_shipping_tr" colspan="2">
		<?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_DESC'); ?> 
		<br />
		<a href="#" onclick="return addMore(ssearch, 'op_default_shipping_tr');"><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?></a><br />
		<?php 
		$html = '<input placeholder="'.addslashes(JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_PLACEHOLDER')).'" type="text" name="op_default_shipping_search[{key}]" id="op_default_shipping_search_{key}" size="40" value="{val}" />'; 
		
		$c = 0; 
		
		if (empty($op_default_shipping_search))
		{
		?>
	     <input placeholder="<?php echo addslashes(JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_PLACEHOLDER')); ?>" type="text" name="op_default_shipping_search[0]" size="40" id="op_default_shipping_search_0" value="" />
		 <?php 
		 $c = 1; 
		}
		else
		{
		  foreach ($op_default_shipping_search as $key=>$val)
		  {
		     $html2 = str_replace('{key}', $key, $html); 
			 $html2 = str_replace('{val}', $val, $html2); 
			 echo $html2; 
			 $c++; 
		  }
		}
		 
		$document = JFactory::getDocument(); 
$document->addScriptDeclaration ( '
//<![CDATA[		 
		   var ssearch = \''.str_replace("'", "\'", $html).'\'; 
		   keycount = '.(int)$c.';
		  
//]]>		
');   
?>
		 
	    </td>
		
	   
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="use_free_text"><?php echo JText::_('COM_ONEPAGE_USE_FREE_TEXT_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="use_free_text" id="use_free_text" <?php if (!empty($use_free_text)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_USE_FREE_TEXT_DESC'); ?> 
		</td>
	</tr>

	
	
        </table>
        </fieldset>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHIPPING_DEFAULT_ADDRESS_LEGEND'); ?></legend>
		<p><?php echo JText::_('COM_ONEPAGE_SHIPPING_DEFAULT_ADDRESS_DESC'); ?></p>
		<table class="admintable" style="width: 100%;">
		
		   <?php 
   
   if (!empty($this->countries))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_country" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_LABEL1'); ?><br/><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_LABEL2'); ?></label>
	    </td>
	    <td>
		<select name="default_country" id="default_country">
		<option value="default"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_SELECT'); ?></option>
		<?php
		$sel = false;
		foreach($this->countries as $p)
		{
		 
		 ?>
		 <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		 if ($p['virtuemart_country_id']==$default_shipping_country) { echo ' selected="selected" '; $sel = true;}
		 if (empty($default_shipping_country) || ($default_shipping_country == 'default'))
		 if ($p['virtuemart_country_id']==$this->default_country) echo ' selected="selected" ';
		 ?>><?php echo $p['country_name']; ?></option>
		 <?php
		}
		
		?>
		</select></td><td> <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_DESC'); ?>
	    </td>
	    
	</tr>
	
	
	
	<?php 
	}
	?>
	
		<tr>
	 <td class="key"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ADVANCED_COUNTRY_LABEL'); ?>
	 </td>
	 <td colspan="2">
	   <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ADVANCED_COUNTRY_DESC'); ?><br />
	   <?php
	   	 $larr = array();
	     $num = 0;
	   
	   if (!empty($this->codes))
	   {
	   foreach ($this->codes as $uu)
	   {
	   ?>
	   <div style="width: 100%; clear: both;">
	   <select name="op_lang_code_<?php echo $num; ?>">
	    <option <?php if (empty($default_country_array[$uu['code']])) echo ' selected="selected" '; ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <option  <?php if (!empty($default_country_array[$uu['code']])) echo ' selected="selected" '; ?> value="<?php echo $uu['code']; ?>"><?php echo $uu['code'] ?></option>
	   </select>
	   <select name="op_selc_<?php echo $num; ?>">
	    <?php 
		
		foreach ($this->countries as $p)  { 
		$ua = explode('-', $uu['code']); 
		$uc = $ua[1]; 
		$uc = strtoupper($uc); 
		
		?>
		 <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		  if ((!empty($default_country_array[$uu['code']])) &&
		   ($default_country_array[$uu['code']]==$p['virtuemart_country_id'])) echo ' selected="selected" '; 
		  else
		  if ((empty($default_country_array[$uu['code']])))
		   {
		     if ($uc == $p['country_2_code']) echo ' selected="selected" '; 
		   }
		   ?>><?php echo $p['country_name']; ?></option>
	    <?php } ?>
	   </select>

	 
	   <br />
	   <?php 
	   $num++;
	   $larr[] = $uu;
	   echo '</div>'; 
	   }
	   }
	   else
	   {
	    echo JText::_('COM_ONEPAGE_JOS_LANG');
	   } ?>
	 </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_use_geolocator"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_USE_GEOLOCATOR_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" value="1" name="op_use_geolocator" id="op_use_geolocator" <?php if (!empty($op_use_geolocator)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_USE_GEOLOCATOR_DESC'); ?>
		</td>
	</tr>
	
<tr>
	    <td class="key">
	     <label for="op_default_zip"><br /><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ZIP_CODE_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="text" name="op_default_zip" id="op_default_zip" value="<?php if (!empty($op_default_zip)) echo urldecode($op_default_zip); else 
		 if ($op_default_zip === 0) echo '0';
		 else
		 echo '99999'; ?>"/>
	    </td>
	    <td>
	    <b><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ZIP_CODE_DESC_BOLD'); ?></b> <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ZIP_CODE_DESC'); ?><br />
		</td>
	</tr>
	

		
		
		</table>
		</fieldset>
<?php
    echo $pane->endPanel(); 
    echo $pane->startPanel(JText::_('COM_ONEPAGE_PAYMENT_PANEL'), 'panel799');
    ?>
    <fieldset class="adminform">
    <legend><?php echo JText::_('COM_ONEPAGE_PAYMENT'); ?></legend>
     <table class="admintable" style="width: 100%;">
   <?php 
   
   if (!empty($this->pms))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_payment" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_DEFAULT_OPTION_LABEL'); ?></label>
	    </td>
	    <td colspan="3" >
		<select id="default_payment" name="default_payment">
		<option value="default"><?php echo JText::_('COM_ONEPAGE_PAYMENT_DEFAULT_OPTION'); ?></option>
		<?php
		
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if ($p['payment_method_id']==$payment_default) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		</select>
	    </td>
	</tr>

   <?php 
   }
   ?>

	<tr>
	    <td class="key">
	     <label for="hide_payment_if_one" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_LABEL'); ?>
	    </td>
	    <td>
	     <input name="hide_payment_if_one" type="checkbox"  id="hide_payment_if_one" <?php if (!empty($hide_payment_if_one)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="hide_advertise" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_ADVERTISEMENT_LABEL'); ?></label>
	    </td>
	    <td>
	     <input name="hide_advertise" type="checkbox"  id="hide_advertise" <?php if (!empty($hide_advertise)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_ADVERTISEMENT_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="payment_inside_basket" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input  class="payment_inside_basket" type="checkbox" name="payment_inside_basket" id="payment_inside_basket" <?php if (!empty($payment_inside_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_DESC2'); ?> 
	    </td>
	</tr>

	

	<tr>
	    <td class="key">
	     <label for="payment_inside" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_LABEL'); ?></label>
		 <?php OPCVideoHelp::show('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input  class="payment_inside" type="checkbox" name="payment_inside" id="payment_inside" <?php if (!empty($payment_inside)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_DESC2'); ?> 
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="klarna_se_get_address" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_KLARNA_GET_ADDRESS_LABEL'); ?></label>
	    </td>
	    <td>
	     <input  class="klarna_se_get_address" type="checkbox" name="klarna_se_get_address" id="klarna_se_get_address" <?php if (!empty($klarna_se_get_address)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_KLARNA_GET_ADDRESS_DESC'); ?>
	    </td>
	</tr>

	
	
     </table>
    </fieldset>
    <?php 
    if (false) {
    ?>
    <fieldset class="adminform">
    <legend><?php echo JText::_('COM_ONEPAGE_PAYMENT_ADVANCED'); ?></legend>
        <?php
        jimport( 'joomla.filesystem.folder' );
        $editor = JFactory::getEditor();
        $mce = true; 
         $ofolder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'payment'.DS.'onepage';
         if (!file_exists($ofolder))
         {
          if (!JFolder::create($ofolder))
           echo '<span style="color: red;">Cannot create directory for this feature: '.$ofolder.'</span>';
         }
		?>
        <table class="admintable" style="width: 100%;">
        <tr>
	    <td class="key">
	     <label for="payment_advanced"><?php echo JText::_('COM_ONEPAGE_PAYMENT_ADVANCED_PAYMENT_LABEL'); ?></label>
	    </td>
	    <td >
	     <input type="checkbox" name="payment_advanced" id="payment_advanced" value="payment_advanced" <?php if (!empty($payment_advanced)) echo 'checked="checked"' ?> />
	    </td>
	    <td>
	     [ADVANCED_PAYMENT] Enable <b>overriding of ps_checkout::list_payment_methods, ps_payment::list_payment_radio</b> and appropriate payment template files. All the features below need this feature to be enabled. If your payment method modifies VirtueMart's list_payment_method function, these features might not work correctly for you. Please let us know and we might create a custom OnePage extension to support your payment module.  
	    </td>
		</tr>
		<tr>
		<td colspan="3">
		<a href="index.php?option=com_onepage&amp;view=payment">Edit HTML per payment with content editor (SAVE CONFIGURATION FIRST!)</a>
		</td>
		</tr>
		<tr>
		<td colspan="3">
		
		and if joomfish available configure per language settings:
		<?php
		 if (!empty($this->codes))
	   {
	   ?>
	   <select name="payment_per_lang">
	   <?php
	   	   foreach ($this->codes as $uu)
	    {
		?>
	     <option value="<?php echo $uu['code']; ?>"><?php echo $uu['code']; ?></option>
		<?php 
		}
		?>
	   </select><input type="button" value="Edit..." onclick="javascript: submitbutton('perlangedit');" /><br />
	   If content editor cannot save your payment information please edit the files directly in /administrator/components/com_virtuemart/classes/payment/onepage/{lang2code}/{payment_id}.part.html On some systems there are problems with relative image paths when using SEO and JCE.
	   <?php 
	   $num++;
	   
	   
	   }
	   else
	   {
	    echo 'jos_languages not found. JoomFISH not installed.';
	   }
	   ?>
		</td>

		</tr>
        <?php
		if (!empty($this->pms))
        foreach($this->pms as $p)
        {
        ?>
        <tr>
        <td class="key">
        Set text for<br />
         <?php echo $p['payment_method_name'];
         ?>
        </td>
        <td colspan="2">
        <?php
         $id = $p['payment_method_id'];
         if (file_exists($ofolder.DS.$id.'.part.html')) 
         $html = file_get_contents($ofolder.DS.$id.'.part.html');
         else $html = ''; 
         
         $id = $p['payment_method_id']; 
         echo 'You can use {payment_discount} to insert payment fee or discount at a specific location. If not used, it will be automatically appended at the end.<br />';
		 if (!$mce)
		 echo $editor->display('payment_content_'.$id, $html, '550', '400', '60', '20', true);
		 else echo '<textarea id="payment_content_'.$id.'" style="width: 550px; height: 400px;" cols="60" rows="20">'.$html.'</textarea>';
		 echo '<input type="hidden" name="payment_contentid_'.$id.'"/>';
        ?>
        </td>
        </tr>
        <?php
        }
        ?>
        </table>
        
    </fieldset>
    
    <?php
    }
    echo $pane->endPanel(); 
	if (false)
	{
	echo $pane->startPanel('Coupons', 'panel7');
			?>
			 <fieldset class="adminform">
        <legend>Coupon Products configuration</legend>
        <table class="admintable" style="width: 100%;">
		<tr>
		 <h2>Experimental !</h2>This feature is built for K2 + Virtuemart coupon selling features. <br />
		 You need to set up available date for coupon products and optionally end date in attribute of the product. 
		</tr>
        <tr>
	    <td class="key">
	     <label for="fix_encoding">Coupon Products </label>
	    </td>
	    <td >
	     <input type="text" name="coupon_products" style="width: 200px;" id="coupon_products" value="<?php if (!empty($coupon_products)) echo $coupon_products; ?>" />
	    </td>
		<td>
		 Please enter product IDs separated by comma for which coupon code should be automatically generated on purchase and activated on order status change to confirmed. 
		</td>
		</tr>
        <tr>
		<tr>
	    <td class="key">
	     <label for="all_products">All products</label>
	    </td>
	    <td>
	     <input type="checkbox" name="all_products" style="width: 200px;" id="all_products" value="<?php if (!empty($coupon_products)) echo $coupon_products; ?>" />
	    </td>
		<td>
		 Please enter product IDs separated by comma for which coupon code should be automatically generated on purchase and activated on order status change to confirmed. 
		</td>
		</tr>
        <tr>
		</tr>
		</table>
		</fieldset>
		<?php
			echo $pane->endPanel(); 
			}
            echo $pane->startPanel(JText::_('COM_ONEPAGE_DISPLAY_PANEL'), 'panel7');
?>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_DISPLAY'); ?></legend>
        <table class="admintable" style="width: 100%;">
	
		

        	
	   <tr> 
	    <td class="key">
	     <label for="selected_template"><?php echo JText::_('COM_ONEPAGE_DISPLAY_SELECTED_TEMPLATE_LABEL'); ?></label>
	    </td>
		
	    <td colspan="1" >
		<?php echo JText::_('COM_ONEPAGE_DESKTOP_THEME'); ?><br />
	     <select style="float: left; max-width: 200px; "  name="selected_template" id="selected_template">
	     <?php
		 
	     if (!empty($this->templates)) 
	     foreach($this->templates as $t)
	     {
		  if ($t == 'extra') continue; 
	      ?>
	      <option value="<?php echo $t; ?>" <?php if ((empty($selected_template) && ($t=='default')) || ($selected_template == $t)) echo ' selected="selected" '; ?>><?php echo $t; ?></option>
	      <?php
	     }
	     ?>
	     </select>
		<?php 
		
		echo JText::_('COM_ONEPAGE_MOBILE_THEME'); ?><br />
		<select style="float: left; max-width: 200px;"  name="mobile_template" id="mobile_template">
	     <option value=""><?php echo JText::_('COM_ONEPAGE_THE_SAME_AS_DESKTOP'); ?></option>
		 <?php
		 
	     if (!empty($this->templates)) 
	     foreach($this->templates as $t)
	     {
		  if ($t == 'extra') continue; 
	      ?>
	      <option value="<?php echo $t; ?>" <?php 
		  if (!empty($mobile_template)) 
		  if (($mobile_template == $t)) 
		  echo ' selected="selected" '; ?>><?php echo $t; ?></option>
	      <?php
	     }
	     ?>
	     </select>
		 </td>
		<td colspan="1">
		
		 <input style="float: left;"type="checkbox" name="load_min_bootstrap" value="1" <?php if (!empty($load_min_bootstrap)) echo ' checked="checked" '; ?> id="load_min_bootstrap" /><label style="float: left; clear: right; margin: 0;" for="load_min_bootstrap"><?php echo JText::_('COM_ONEPAGE_DISPLAY_LOAD_MIN_BOOTSTRAP'); ?></label>
		 <?php if (false) { ?>
		 <input style="float: left;"type="checkbox" name="opc_rtl" value="1" <?php if (!empty($opc_rtl)) echo ' checked="checked" '; ?> id="opc_rtl" /><label style="float: left; clear: right; margin: 0;" for="opc_rtl"><?php echo JText::_('COM_ONEPAGE_DISPLAY_OPC_RTL'); ?></label>
		 <?php } ?>
		  <br style="clear: both;"/><a href="index.php?option=com_onepage&amp;view=edittheme"><?php echo JText::_('COM_ONEPAGE_OPC_THEME_EDITOR'); ?>...</a>
		 <br />
	     <input class="text_area" type="hidden" name="override_css_by_class" id="override_css_by_class" size="60" value=""/>
	     <input class="text_area" type="hidden" name="override_css_by_id" id="override_css_by_id" size="60" value="<?php if (!empty($op_ids)) echo $op_ids ?>"/>
		 <input type="hidden" name="php_logged" value="onepage.logged.tpl.php" />
		 <input type="hidden" name="css_logged" value="onepage.css" />
 		 <input type="hidden" name="php_unlogged" value="onepage.unlogged.tpl.php" />
		 <input type="hidden" name="css_unlogged" value="onepage.css" />

	    </td>
		</tr>
		<tr>
	    <td class="key"><label><?php echo JText::_('COM_ONEPAGE_RENAME_THEME'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_RENAME_THEME'); ?></td>
		<td>
				 <input type="button" name="rename_theme" value="<?php echo JText::_('COM_ONEPAGE_RENAME_TO_CUSTOM');  ?>" id="rename_theme" onclick="javascript: submitbutton('rename_theme');"/>
		</td>
		<td>
				<label for="rename_theme"><?php echo JText::_('COM_ONEPAGE_RENAME_THEME_DESC'); ?> 
				</label>
		</td>
		</tr>
        <tr style="display: none;">
	    <td class="key">
	     <label for="op_numrelated">[SOON AVAILABLE]<br /><?php echo JText::_('COM_ONEPAGE_DISPLAY_NUM_RELATED_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="text" name="op_numrelated" id="op_numrelated" value="<?php if (empty($op_numrelated) || (!is_numeric($op_numrelated))) echo '0'; else echo $op_numrelated; ?>" />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_NUM_RELATED_DESC'); ?>
	    </td>
		</tr>
		
        <tr>
	    <td class="key">
	     <label for="op_customitemid"><?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_LABEL'); echo $flag; ?></label>
	    </td>
	    <td>
	     <input type="text" id="op_customitemid" value="<?php 
		 
		 if (!isset($newitemid)) $newitemid = ''; 
		 $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, $newitemid, $opclang); 
		 //var_dump($newitemid); die(); 
		 if (!empty($newitemid)) echo $newitemid; ?>" name="newitemid" />
	     
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_DESC'); ?>
	    </td>
		</tr>

		        <tr>
	    <td class="key">
	     <label for="op_customitemidty"><?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_LABEL_TY'); echo $flag; ?></label>
	    </td>
	    <td>
	     <input type="text" id="op_customitemidty" value="<?php 
		 
		  if (!isset($op_customitemidty)) $op_customitemidty = ''; 
	  $op_customitemidty = OPCconfig::getValue('opc_config', 'op_customitemidty', 0, $op_customitemidty, $opclang); 
		 
		 if (!empty($op_customitemidty)) echo $op_customitemidty; ?>" name="op_customitemidty" />
	     
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_DESC_TY'); ?>
	    </td>
		</tr>

		
		
        <tr>
	    <td class="key">
	     <label for="op_articleid"><?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL'); echo $flag; ?></label>
	    </td>
	    <td>
		 <?php if (false) { ?>
	     <input type="text" id="op_articleid" value="<?php if (!empty($op_articleid)) echo $op_articleid; ?>" name="op_articleid" /> <?php } ?>
	     <?php echo $this->articleselector2; ?>
		 <input type="button" onclick="return clearArticle('op_articleid');" value="<?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_VALUE'); ?>" />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_DESC'); ?>  
	    </td>
		</tr>
		
	<tr>
	
	    <td class="key">
	     <label for="show_full_tos" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_LABEL'); ?></label>
	    </td>
	    <td  colspan="2">
		
		<?php
		$d = VmConfig::get('agree_to_tos_onorder', '1'); 
		$vmtos = (int)VmConfig::get('agree_to_tos_onorder', '1'); 
		//var_dump($vmtos); var_dump($tos_unlogged); var_dump($tos_logged); 
		?>
		
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="full_tos_logged"  <?php if (!empty($full_tos_logged)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_LOGGED') ?><br style="clear: both;"/>
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="full_tos_unlogged" <?php if (VmConfig::get('oncheckout_show_legal_info', '1')) echo ' checked="checked" '; else if (!empty($full_tos_unlogged)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'); ?>
		 <br style="clear: both;" />
		 <?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'); ?>
		 <br style="clear: both;" />
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="tos_logged" <?php if ($vmtos) echo ' checked="checked"  '; else if (!empty($tos_logged)) echo ' checked="checked" '; if (!isset($tos_unlogged)) echo ' checked="checked" '; ?>/> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_LOGGED').' '; echo JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION');  ?><br style="clear: both;" />
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="tos_unlogged" <?php if ($vmtos) echo ' checked="checked"  '; else if (!empty($tos_unlogged)) echo ' checked="checked" '; if (!isset($tos_unlogged)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_UNLOGGED').' '; echo JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION');  ?><br style="clear: both;" />
	    
		 <?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_SHOW_TOS_UNLOGGED'); ?>
		 <br style="clear: both;" />
		 <input type="checkbox" value="1" name="tos_scrollable" <?php if (!empty($tos_scrollable)) echo ' checked="checked" '; ?> /><?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_SCROLLABLE'); ?><br style="clear: both;" />
		</td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="tos_config" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_CONFIG'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'); ?>
	    </td>
	    <td>
		<?php echo $this->articleselector; ?>
	     <?php
		 if (false) { ?><input class="text_area" type="text" name="tos_config" id="tos_config" size="10" value="<?php if (!empty($tos_config)) echo $tos_config; ?>"/>
		 <?php } ?>
		 </td>
		 <td>
		 <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_CONFIG_DESC'); ?>
		 </td>
	</tr>
    <tr>
	  <td></td>
	  <td>
		 <?php echo JText::_('COM_ONEPAGE_DISPLAY_TOS_ITEM_ID_DESC'); echo $flag; ?>
	  </td>
	  <td>
	  <input type="text" name="tos_itemid" value="<?php 
	  
	  if (!isset($tos_itemid)) $tos_itemid = ''; 
	  $tos_itemid = OPCconfig::getValue('opc_config', 'tos_itemid', 0, $tos_itemid, $opclang); 
	  
	  if (!empty($tos_itemid)) echo $tos_itemid; ?>"/>  
		 
	  </td>
	</tr>
	<tr>
	    <td></td>
	   
		<td>
		<?php echo JText::_('COM_ONEPAGE_DISPLAY_TOS_RESET'); ?>
	    </td>
		 <td >
		<input type="button" onclick="javascript: return clearArticle('tos_config');" value="<?php echo JText::_('COM_ONEPAGE_DISPLAY_TOS_RESET_VALUE'); ?>" />
		</td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="op_no_basket" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_BASKET_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_NO_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input class="op_no_basket" type="checkbox" name="op_no_basket" id="op_no_basket" <?php if (!empty($op_no_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_BASKET_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_login_in_template" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_LOGIN_TEMPLATE_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_login_in_template" type="checkbox" name="no_login_in_template" id="no_login_in_template" <?php if (!empty($no_login_in_template)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_LOGIN_TEMPLATE_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_continue_link" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_CONTINUE_LINK_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_continue_link" type="checkbox" name="no_continue_link" id="no_continue_link" <?php if (!empty($no_continue_link)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_CONTINUE_LINK_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_extra_product_info" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_EXTRA_PRODUCT_INFO_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_extra_product_info" type="checkbox" name="no_extra_product_info" id="no_extra_product_info" <?php if (!empty($no_extra_product_info)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_EXTRA_PRODUCT_INFO_DESC'); ?> 
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_alerts" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_ALERTS_LABEL') ?></label>
	    </td>
	    <td>
	     <input class="no_alerts" type="checkbox" name="no_alerts" id="no_alerts" <?php if (!empty($no_alerts)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_ALERTS_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_coupon_ajax" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_COUPON_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_coupon_ajax" type="checkbox" name="no_coupon_ajax" id="no_coupon_ajax" <?php if (!empty($no_coupon_ajax)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_COUPON_DESC'); ?>
	    </td>
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="ajaxify_cart" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_AJAXIFY_CART_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="ajaxify_cart" type="checkbox" name="ajaxify_cart" id="ajaxify_cart" <?php if (!empty($ajaxify_cart)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_AJAXIFY_CART_DESC'); ?> 
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="use_original_basket" ><?php echo JText::_('COM_ONEPAGE_USE_ORIGINAL_BASKET_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="use_original_basket" type="checkbox" name="use_original_basket" id="use_original_basket" <?php if (!empty($use_original_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_USE_ORIGINAL_BASKET_DESC'); ?> 
	    </td>
	</tr>

		<tr>
	    <td class="key">
	     <label for="opc_editable_attributes" ><?php echo JText::_('COM_ONEPAGE_EDITABLE_ATTRIBUTES_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_editable_attributes" type="checkbox" name="opc_editable_attributes" id="opc_editable_attributes" <?php if (!empty($opc_editable_attributes)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_EDITABLE_ATTRIBUTES_DESC'); ?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_show_weight" ><?php echo JText::_('COM_ONEPAGE_SHOW_WEIGHT_BASKET_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_show_weight" type="checkbox" name="opc_show_weight" id="opc_show_weight" <?php if (!empty($opc_show_weight)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHOW_WEIGHT_BASKET_DESC'); ?> 
	    </td>
	</tr>

		<tr>
	    <td class="key">
	     <label for="opc_only_parent_links" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_ALWAYS_LINK_PARENT_PRODUCTS_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_only_parent_links" type="checkbox" name="opc_only_parent_links" id="opc_only_parent_links" <?php if (!empty($opc_only_parent_links)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_ALWAYS_LINK_PARENT_PRODUCTS_DESC'); ?> 
	    </td>
	</tr>

	
        
        </table>
        </fieldset>
        <?php 
         echo $pane->endPanel();
		 




                    echo $pane->startPanel(JText::_('COM_ONEPAGE_REGISTRATION_PANEL'), 'panel8');
					
					?>
					<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_REGISTRATION'); ?></legend>
		 <table class="admintable" id="comeshere2" style="width: 100%;">
		 <tr>
	    <td class="key">
	     <label for="op_redirect_joomla_to_vm" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_LABEL'); ?>
	    </td>
	    <td>
	     <input class="op_redirect_joomla_to_vm" value="1" type="checkbox" name="op_redirect_joomla_to_vm" id="op_redirect_joomla_to_vm" <?php if (!empty($op_redirect_joomla_to_vm)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_DESC'); ?>
	    </td>
		</tr>
		<tr>
	    <td class="key">
	     <label for="opc_override_registration" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_OVERRIDE_VMREGISTRATION_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_override_registration" value="1" type="checkbox" name="opc_override_registration" id="opc_override_registration" <?php

		  $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opcregistration' and type='plugin' and folder='system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled) || (empty($isInstalled['enabled'])))
		  {
		  $opc_override_registration = false; 
		  }
		  else
		  $opc_override_registration = true; 
		  
		 if (!empty($opc_override_registration)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_OVERRIDE_VMREGISTRATION_DESC'); ?>
	    </td>
		</tr>

		
		
				 <tr>
	    <td class="key">
	     <label for="op_never_log_in" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NEVER_LOGIN_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="op_never_log_in" value="1" type="checkbox" name="op_never_log_in" id="op_never_log_in" <?php if (!empty($op_never_log_in)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NEVER_LOGIN_DESC'); ?> 
	    </td>
		</tr>
		 	<tr>
	    <td class="key">
	     <label for="op_usernameisemail" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_LABEL'); ?>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_usernameisemail" id="op_usernameisemail" value="op_usernameisemail" <?php if (isset($op_usernameisemail)) if ($op_usernameisemail==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_DESC'); ?>
	    </td>
		</tr>
		
	    <tr>
		 <td class="key">
		 <label for="opc_check_username" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_CHECK_LABEL'); ?></label>
		</td>
		<td>
		  <input type="checkbox" name="opc_check_username" value="1" <?php if (!empty($opc_check_username)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		<table>
		<tr>
		 <td colspan="2">
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_CHECK_DESC'); ?>
		 </td>
		</tr>
		<tr>
		<td>
		 <input type="checkbox" name="opc_no_duplicit_username" value="1" <?php if (!empty($opc_no_duplicit_username)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_NO_DUPLICIT'); ?>
		</td>
		</tr>
		</table>
		</td>
		</tr>

	    <tr>
		 <td class="key">
		 <label for="opc_check_email" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_CHECK_LABEL') ?></label>
		</td>
		<td>
		  <input type="checkbox" name="opc_check_email" value="1" <?php if (!empty($opc_check_email)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		
		
				<table>
		<tr>
		 <td colspan="2">
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_CHECK_DESC'); ?> 
		 </td>
		</tr>
		<tr>
		<td>
		 <input type="checkbox" name="opc_no_duplicit_email" value="1" <?php if (!empty($opc_no_duplicit_email)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_CHECK_NO_DUPLICIT'); ?>
		</td>
		</tr>
		</table>
		
		
		 
		</td>
		</tr>

		
		
	    <tr>
		 <td class="key">
		 <label for="opc_email_in_bt" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_IN_BT_LABEL'); ?></label>
		  
		  
		</td>
		<td>
		  <input type="checkbox" name="opc_email_in_bt" value="1" <?php if (!empty($opc_email_in_bt)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_IN_BT_DESC'); ?>
		</td>
		</tr>
	    <tr>
		 <td class="key">
		 <label for="double_email" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_DOUBLE_LABEL'); ?></label>
		  
		</td>
		<td>
		  <input type="checkbox" name="double_email" value="1" <?php if (!empty($double_email)) echo ' checked="checked" '; ?>/> 
		</td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_DOUBLE_DESC'); ?>
		</td>
		</tr>
			<tr>
	    <td class="key">
	     <label for="unlog_all_shoppers"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_UNLOG_ALL_SHOPPERS_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="unlog_all_shoppers" name="unlog_all_shoppers" value="unlog_all_shoppers" <?php if ($unlog_all_shoppers==true) echo 'checked="checked"'; ?> /> 
	    </td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_REGISTRATION_UNLOG_ALL_SHOPPERS_DESC'); ?>
		</td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_no_display_name" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NO_DISPLAY_NAME_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="op_no_display_name" type="checkbox" name="op_no_display_name" id="op_no_display_name" <?php if (!empty($op_no_display_name)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NO_DISPLAY_NAME_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_create_account_unchecked" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_CREATE_ACCOUNT_UNCHECKED_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="op_create_account_unchecked" type="checkbox" name="op_create_account_unchecked" id="op_create_account_unchecked" <?php if (!empty($op_create_account_unchecked)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_CREATE_ACCOUNT_UNCHECKED_DESC'); ?>
	    </td>
	</tr>        
	<tr>
	    <td class="key" >
	     <label for="allow_duplicit" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_DUPLICIT_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_duplicit" name="allow_duplicit" value="allow_duplicit" <?php if ($allow_duplicit==true) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_DUPLICIT_DESC'); ?>
	    </td>
	</tr>
	
	<tr>
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ENABLE_CAPTCHA_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  <table style="border: none;">
	   <tr style="border: none;">
	    <td><input type="checkbox" id="enable_captcha_unlogged" name="enable_captcha_unlogged" value="enable_captcha_unlogged" <?php if (!empty($enable_captcha_unlogged)) echo ' checked="checked" '; ?> /> 
		</td>
	    <td>
		<label for="enable_captcha_unlogged" >
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_ENABLE_CAPTCHA_UNLOGGED'); ?></label>
			
			
		 </td>
		</tr>
		<tr style="border: none;">
		  <td>
		  <input type="checkbox" id="enable_captcha_logged" name="enable_captcha_logged" value="enable_captcha_logged" <?php if (!empty($enable_captcha_logged)) echo ' checked="checked" '; ?> /> 
		  </td>
		  <td>
		    <label for="enable_captcha_logged"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ENABLE_CAPTCHA_LOGGED'); ?>
			</label>
		   </td>
		   
		 </tr>
		</table>
	    </td>
	   
	</tr>
	<tr>
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_USER_ACTIVATION_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_do_not_alter_registration" name="opc_do_not_alter_registration" value="opc_do_not_alter_registration" <?php if (!empty($opc_do_not_alter_registration)) echo ' checked="checked" '; ?> /> 
		 <?php echo JText::_('COM_ONEPAGE_USER_ACTIVATION_DESCRIPTION'); ?>
	    </td>
	   
	</tr>

	
	
	<tr>
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_USER_NOACTIVATION_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_no_activation" name="opc_no_activation" value="opc_no_activation" <?php if (!empty($opc_no_activation)) echo ' checked="checked" '; ?> /> 
		 <?php echo JText::_('COM_ONEPAGE_USER_NOACTIVATION_DESC'); ?>
	    </td>
	   
	</tr>

	<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_ACYMAILING_CHECKBOX_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_acymailing_checkbox" name="opc_acymailing_checkbox" value="opc_acymailing_checkbox" <?php if (!empty($opc_acymailing_checkbox)) echo ' checked="checked" '; ?> /> <input type="text" value="<?php if (isset($opc_acy_id)) echo $opc_acy_id; else echo "2"; ?>" name="opc_acy_id" />
		 <?php echo JText::_('COM_ONEPAGE_ACYMAILING_CHECKBOX_DESC'); 
		 ?>
	    </td>
	   
	</tr>

		<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_ITALIAN_CHECKBOX_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_italian_checkbox" name="opc_italian_checkbox" value="opc_italian_checkbox" <?php if (!empty($opc_italian_checkbox)) echo ' checked="checked" '; ?> /> 
		 <?php echo JText::_('COM_ONEPAGE_ITALIAN_CHECKBOX_DESC'); ?>
	    </td>
	   
	</tr>

	
	<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_REGISTRATION_FIELDS'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<select id="bt_fields_from" name="bt_fields_from">
			<?php
			$f = array(0,1,2); 
			$f = array(0,1); 
			foreach ($f as $opt)
			{
			  echo '<option value="'.$opt.'"'; 
			  if (isset($bt_fields_from))
			  if ($bt_fields_from == $opt) echo ' selected="selected" '; 
			  //default
			  if (!isset($bt_fields_from))
			  if ($opt == 0) echo ' selected="selected" '; 
			  echo '>'.JText::_('COM_ONEPAGE_REGISTRATION_FIELDS_OPT'.$opt).'</option>'; 
			}
			
			?>
			</select>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_FIELDS_DESC'); ?>
	    </td>
	   
	</tr>
	

	
		</table>
		</fieldset>
					<fieldset class="adminform">
					<legend><?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART'); ?></legend>
		<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_TAX_CONFIG'); ?><br />
					<?php
jimport( 'joomla.html.html.behavior' );
JHtml::_('behavior.modal', 'a.modal'); 
					
		 if (!defined('VM_REGISTRATION_TYPE'))
   {
    if (VmConfig::get('oncheckout_only_registered', 0))
	{
	  if (VmConfig::get('oncheckout_show_register', 0))
	  define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
	  else 
	  define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
	}
	else
	{
	if (VmConfig::get('oncheckout_show_register', 0))
    define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
	else 
	define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
	}
   }
					?>
<a class="modal" href="index.php?option=com_config&amp;view=component&amp;component=com_users&amp;path=&amp;tmpl=component" rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}">
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_ACTIVATION'); ?> 
</a>
<br />

<p>
<label for="opc_registraton_type">
<?php echo JText::_('COM_ONEPAGE_SELECT_REGISTRATION_TYPE'); echo JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION'); ?>
</label><br style="clear: both;"/>
<?php OPCVideoHelp::show('COM_ONEPAGE_SELECT_REGISTRATION_TYPE'); ?>
<br style="clear: both;"/>
<select <?php if (!$is_admin) echo ' disabled="disabled" '; ?> name="opc_registraton_type" id="opc_registraton_type">
 <?php 
 echo '<option value="NO_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='NO_REGISTRATOIN') 
 echo ' selected="selected"'; 
 echo '>'; 
 echo JText::_("COM_ONEPAGE_NO_REGISTRATION").'</option>'; 
 
 echo '<option value="OPTIONAL_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='OPTIONAL_REGISTRATION') 
 echo ' selected="selected"'; 
echo '>'; 
 echo JText::_("COM_ONEPAGE_OPTIONAL_REGISTRATION").'</option>'; 
 
 echo '<option value="SILENT_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='SILENT_REGISTRATION') 
 echo ' selected="selected"'; 
echo '>'; 
 echo JText::_("COM_ONEPAGE_SILENT_REGISTRATION").'</option>'; 
 
 
 echo '<option value="NORMAL_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='NORMAL_REGISTRATION') 
 echo ' selected="selected"'; 
 echo '>'; 
 echo JText::_("COM_ONEPAGE_NORMAL_REGISTRATION").'</option>'; 
 ?>
</select>
<?php  echo JHtml::_('form.token'); 
/*
?>
<br style="clear: both;"/>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_REGISTRATION_OPTION'); ?>

<?php
*/
?></p><?php
 $usersConfig = JComponentHelper::getParams( 'com_users' );
 $reg = $usersConfig->get('allowUserRegistration'); 
 if (empty($reg) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION'))
 {
   echo '<p style="color: red;">'.JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_NO_REGISTRATION').'</p>'; 
 }
 /*
?>
<?php if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') echo '<span style="color: green; font-weight: bold; font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_IS_B_NOT'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION') echo '<span style="color: green; font-weight: bold; font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_NOT_B_IS'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION') echo '<span style="color: green; font-weight: bold;font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_NOT_B_NOT'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION') echo '<span style="color: green; font-weight: bold;font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_IS_B_IS'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION') echo '</span>'; ?>
<br />
<br />
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_NOTE'); 
*/
?>
</fieldset>
<?php
					echo $pane->endPanel();
					?>
   
		<?php
		echo $pane->startPanel(JText::_('COM_ONEPAGE_SHOPPERFIELDS_PANEL'), 'panel82'); ?>
		<fieldset class="adminform">
		 <?php
		   if (empty($this->ulist)) echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_INFO'); 
		   else
		   {
		   ?>
		  <p><h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_HEAD'); ?></h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_DESC'); ?></p>
		  <p><h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER'); ?></h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_DESC'); ?></p>
		  <div>
		  <h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL'); ?></h3>
		  <p><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_DESC'); ?></p>
		  <select name="opc_cr_type">
		  <option value="save_all" <?php if (!empty($opc_cr_type) && ($opc_cr_type=="save_all")) echo ' selected="selected" '; ?>><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_SAVE_ALL'); ?></option>
		  <option value="save_order" <?php if (!empty($opc_cr_type) && ($opc_cr_type=="save_order")) echo ' selected="selected" '; ?>><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_SAVE_ORDER'); ?></option>
		  <option value="save_none" <?php if (!empty($opc_cr_type) && ($opc_cr_type=="save_none")) echo ' selected="selected" '; ?>><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_SAVE_NONE'); ?></option>
		  </select>
		  </div>
		  <br style="clear: both;"/>
		  <table class="admintable" style="width: 50%;">
		  <tr>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_FIELDNAME'); ?>
		   </th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_CUSTOM'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_SHIPPING'); ?></th>
		  </tr>
		  

		   <?php
		   //var_dump($this->ulist); die(); 
		   foreach ($this->ulist as $key=>$row)
		    {
			 if (!$row->published) continue; 
			 
			 // the next line will filter core fields
			 //if (in_array($row->name, $this->clist)) continue;
			  //if ($row->published)

			   ?>
			   		<tr>
	    <td class="key">
	     <label><?php 
		 $title = $row->title; 
		 $title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) echo $title2.' ('.JText::_($row->name).')'; 
		 else echo $row->name; 
		 if ($row->name == 'register_account')
		  {
		    echo '<br /><small>'.JText::_('COM_ONEPAGE_SHOPPERFIELDS_REGISTER_NOTE').'</small>';
		  }
		  if ($row->name == 'password2')
		  {
		    echo '<br /><small>'.JText::_('COM_ONEPAGE_SHOPPERFIELDS_PASSW2_NOTE').'</small><br /><input type="checkbox" name="password_clear_text" value="1" ';
			if (!empty($password_clear_text)) echo ' checked="checked" ';
			echo ' /> '.JText::_('COM_ONEPAGE_SHOPPERFIELDS_PASS_CLEAR'); 
		  }

		 ?></label> 
	    </td>
	    <td><input type="checkbox" name="business_fields[]" value="<?php echo $row->name; ?>"
		<?php 
		switch ($row->name)
		{
		 
		  case 'password':
		  case 'password2':
		    echo ' disabled="disabled" '; 
			
			break; 
		  default: 
		     if (!empty($business_fields))
		     if (in_array($row->name, $business_fields)) echo ' checked="checked'; 
			break; 
		}
		
		
		?>" />
		</td>

	    <td><input type="checkbox" name="custom_rendering_fields[]" value="<?php echo $row->name; 
		// case 'password2':
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		 
		  case 'register_account':
		    echo '" disabled="disabled" checked="checked" readyonly="readonly'; 
		
		
		}
		
		if (!empty($custom_rendering_fields))
		if (in_array($row->name, $custom_rendering_fields)) echo '" checked="checked'; 
		
		?>" />
		</td>
		 <td><input type="checkbox" name="shipping_obligatory_fields[]" value="<?php echo $row->name; 
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'password2':
		  case 'register_account':
		    echo '" disabled="disabled" checked="checked" readyonly="readonly'; 
			
		
		}
		if ($row->shipment != '1')
		    echo '" disabled="disabled" readyonly="readonly'; 
			
		if (!empty($shipping_obligatory_fields))
		if (in_array($row->name, $shipping_obligatory_fields)) echo '" checked="checked'; 
		
		?>" />
		</td>

		
		</tr>
		<?php
			}
			echo '</table>'; 
		   }
		 ?>
		</fieldset>
		<?php echo $pane->endPanel();
		echo $pane->startPanel(JText::_('COM_ONEPAGE_SHOPPERGROUP_PANEL'), 'panel8');
		?>
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP'); ?></legend>
		  <table>
		    <tr>
	    <td class="key" >
	     <label for="allow_sg_update" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATE_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_sg_update" name="allow_sg_update" value="allow_sg_update" <?php if (!empty($allow_sg_update)) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATE_DESC'); ?>
	    </td>
	</tr>
	
	 <tr>
	    <td class="key" >
	     <label for="allow_sg_update_logged" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATELOGGED_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_sg_update_logged" name="allow_sg_update_logged" value="allow_sg_update_logged" <?php if (!empty($allow_sg_update_logged)) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATELOGGED_DESC'); ?>
	    </td>
	</tr>
	
	
		  </table>
		 </fieldset>
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP'); ?></legend>
		 <p><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_DESC'); ?></p>
		
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_NOTALTER'); ?></legend>
		<input type="radio" id="option_sgroup3" name="option_sgroup" value="0" <?php if (empty($option_sgroup)) echo ' checked="checked" '; ?> /><label style="float: left;clear: none; margin: 0; padding:0;" for="option_sgroup3"><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_NOTALTER_DESC'); ?></label>
		
        </fieldset>
		
		
		<fieldset class="adminform"><legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_GLOBALLY'); ?></legend>	
		<input type="radio" id="option_sgroup1" name="option_sgroup" value="1" <?php if (!empty($option_sgroup) && ($option_sgroup===1)) echo ' checked="checked" '; ?> />
		<label style="float: left;clear: none; margin: 0; padding:0;" for="option_sgroup1"><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_GLOBALLY_DESC'); ?></label>
		<br style="clear: both;" />
		<table>
		 <tr>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_LANGGROUP'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_SHOPGROUP'); ?></th>
		 </tr>
		 
		
	   <?php
	   	 $larr = array();
	     $num = 0;
	   
	   if (!empty($this->codes))
	   {
	   foreach ($this->codes as $uu)
	   {
	   ?>
	    <tr>
		 
		 <td>
		     
			 
	   <div style="width: 100%; clear: both;">
	   <select name="op_lang_code2_<?php echo $num; ?>">
	    <option <?php if (empty($lang_shopper_group[$uu['code']])) echo ' selected="selected" '; ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <option  <?php //if (!empty($default_country_array[$uu['code']])) echo ' selected="selected" '; 
		if (!empty($lang_shopper_group[$uu['code']]))
		{
		 echo ' selected="selected" '; 
		 }
		?> value="<?php echo $uu['code']; ?>"><?php echo $uu['code'] ?></option>
	   </select>
	   </div>
	    </td>
		<td>

	   <select name="op_group_<?php echo $num; ?>">
	      <option <?php 
		  if (empty($lang_shopper_group[$uu['code']])) echo ' selected="selected" '; 
		  ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		  
		  
		  <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($lang_shopper_group[$uu['code']]))
			if ($g['virtuemart_shoppergroup_id'] == $lang_shopper_group[$uu['code']]) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.$g['shopper_group_name'].'</option>'; 
		  }
		 ?>

	   </select>
	   
	   
	   
	   <?php 
	   $num++;
	   $larr[] = $uu;
	   echo '</td></tr>'; 
	   }
	   }
	   else
	   {
	    echo JText::_('COM_ONEPAGE_JOS_LANG');
	   } ?>
		
		
		
		
		</table>
		</fieldset>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP'); ?></legend>
		<input type="radio" id="option_sgroup2" name="option_sgroup" value="2" <?php if (!empty($option_sgroup) && ($option_sgroup==2)) echo ' checked="checked" '; ?> /><label style="float: left;clear: none; margin: 0; padding:0;" for="option_sgroup2"><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_DESC'); ?></label></legend>
		<br style="clear: both;" />
		<table id="ip_shopper_group">
		 <tr>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_SEARCH'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_COUNTRY'); ?></th>
		   <th colspan="2"><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_SGROUP'); ?></th>
		   
		 </tr>
		 <?php
		  //echo $num; 
		 $num = 0; 
		 
		 if (empty($lang_shopper_group_ip)) 
		 $lang_shopper_group_ip[0] = ''; 
		 
		 foreach ($lang_shopper_group_ip as $key=>$uu)
		 {
		 
		 if (strpos($key, '-')>0) continue; 
		 
		 $code ='
		
		  <td><input type="text" name="search" onkeyup="javascript: return opc_search(this, \'op_selc2_{num}\')" placeholder="'.addslashes(JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_SEARCH_PLACEHOLDER')). '" value="" />
		  </td>
		  
		  <td>
		  <select style="margin: 0;" id="op_selc2_{num}" name="op_selc2_{num}">
	      <option value="0">'.JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_NOT').'</option>
		';
		
		foreach ($this->countries as $p)  { 
		
		$uc = $key; 
		$uc = strtoupper($uc); 
		
		$code .= '
		 <option value="'.$p['virtuemart_country_id'].'"';
		  if  ($key==$p['virtuemart_country_id']) $code .= ' selected="selected" '; 
		  
		   $code .= '>'.$p['country_name'].'</option>';
	    }
      $code .= '		
	   </select>
	       </td>
		   <td>
		    <select style="margin: 0;" name="op_group_ip_{num}">
	      <option ';
		  if (empty($lang_shopper_group_ip[$key])) $code .= ' selected="selected" '; 
$code .= ' value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> '; 
		   foreach ($this->groups as $g)
		  {
		    $code .= '<option '; 
			if (!empty($lang_shopper_group_ip[$key]))
			if ($g['virtuemart_shoppergroup_id'] == $lang_shopper_group_ip[$key]) $code .= ' selected="selected" '; 
			$code .= ' value="'.$g['virtuemart_shoppergroup_id'].'">'.$g['shopper_group_name'].'</option>'; 
		  }
		$code .= '

	   </select>
	   </td>
		   <td>
		   <a href="#" onclick="javascript: return op_new_line(opc_line, \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_ADD_MORE').'</a>
		   <a style="margin-left: 50px;" href="#" onclick="javascript: return op_remove_line(\'{num}\', \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_REMOVE').'</a>
		   </td>
		  '; 
		  $jscode = $code; 
		  
		  $code_sg = '<tr id="rowid_'.$num.'">'.str_replace('{num}', $num, $code).'</tr>'; 
		 $num++;
		  unset($code); 
		  echo $code_sg; 
		  }
		  //echo $code_sg; 
		  
		  $code_sg = $jscode; 
		  $code_sg = trim($code_sg); 
		  $code_sg = str_replace("\r\r\n", "", $code_sg); 
		  $code_sg = str_replace("\r\n", "", $code_sg); 
		  $code_sg = str_replace("\n", "", $code_sg); 
		  $document->addScriptDeclaration(' 
//<![CDATA[		  
var line_iter = '.$num.'; 
var opc_line = \''.str_replace("'", "\'", $code_sg).'\';
//]]>
'); 

		  
		  //unset($code); 
		  $last_num = $num; 
		  ?>
		  
		  </table>
		  </fieldset>
		   

		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_PER_REGISTRATION'); ?></legend>
		<table>
		
	<tr>
	    <td class="key" >
	     <label for="business_shopper_group" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_BUSINESS_SHOPPER_GROUP_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="business_shopper_group" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($business_shopper_group))
			if ($g['virtuemart_shoppergroup_id'] == $business_shopper_group) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.$g['shopper_group_name'].'</option>'; 
		  }
		 ?>
		 </select>
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_BUSINESS_SHOPPER_GROUP_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key" >
	     <label for="visitor_shopper_group" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_VISITOR_SHOPPER_GROUP_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="visitor_shopper_group" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($visitor_shopper_group))
			if ($g['virtuemart_shoppergroup_id'] == $visitor_shopper_group) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.$g['shopper_group_name'].'</option>'; 
		  }
		 ?>
		 </select>
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VISITOR_SHOPPER_GROUP_DESC'); ?>
	    </td>
	</tr>
	
	</table>
		</fieldset>
	<fieldset class="adminform"><legend><?php echo JText::_('COM_ONEPAGE_EUVAT_SECTION'); ?></legend>
		<table>
	<tr>
	    <td class="key" >
	     <label for="euvat_shopper_group" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_SHOPPER_GROUP_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="euvat_shopper_group" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($euvat_shopper_group))
			if ($g['virtuemart_shoppergroup_id'] == $euvat_shopper_group) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.$g['shopper_group_name'].'</option>'; 
		  }
		 ?>
		 </select>
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_PER_EUVAT_DESC'); ?> 
	    </td>
	</tr>
	<tr>
	    <td class="key" >
	     <label for="home_vat_countries" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_LABEL'); ?></label>
	    </td>
	    <td>
		 <input type="text" placeholder="<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_PLACEHOLDER'); ?>" name="home_vat_countries" value="<?php if (!empty($home_vat_countries)) echo $home_vat_countries; ?>" />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_DESC'); ?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key" >
	     <label for="opc_euvat" ><?php echo JText::_('COM_ONEPAGE_EUVAT_LABEL'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat" value="1" <?php if (!empty($opc_euvat)) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_DESC'); ?> 
	    </td>
	</tr>
	<tr >
	    <td class="key" >
	     <label for="opc_euvat_button" ><?php echo JText::_('COM_ONEPAGE_EUVAT_USEBUTTON'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat_button" value="1" <?php if (!empty($opc_euvat_button)) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_USEBUTTON'); ?> 
	    </td>
	</tr>
	<tr >
	    <td class="key" >
	     <label for="opc_euvat_contrymatch" ><?php echo JText::_('COM_ONEPAGE_EUVAT_COUNTRYMATCH'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat_contrymatch" value="1" <?php if ((!isset($opc_euvat_contrymatch)) || (!empty($opc_euvat_contrymatch))) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_COUNTRYMATCH'); ?> 
	    </td>
	</tr>
	
	
	
		</table>
		</fieldset>
		
		<?php
		echo $pane->endPanel(); 
							
                    echo $pane->startPanel(JText::_('COM_ONEPAGE_TAXES_PANEL'), 'panel86');
?>
		<fieldset class="adminform">
		<?php echo JText::_('COM_ONEPAGE_TAXES_DESC'); ?>
		 <table class="admintable" id="comeshere4" style="width: 100%;">
	    <tr>
	    <td class="key">
	     <label for="american"><?php echo JText::_('COM_ONEPAGE_TAXES_AMERICA'); ?></label> 
	    </td>
	    <td>
			<input type="checkbox" value="1" name="opc_usmode" <?php if (!empty($opc_usmode)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_TAXES_AMERICA_DESC'); ?>
	    </td>
		</tr>

		 <tr>
	    <td class="key">
	     <label for="product_price_display"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS'); ?></label> 
	    </td>
	    <td>
	    <select name="product_price_display">
		
		
		<option <?php if (!empty($product_price_display) && ($product_price_display == 'discountedPriceWithoutTax')) echo ' selected="selected" '; ?>value="discountedPriceWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_AFTERDISCOUNT'); ?></option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'basePriceWithTax')) echo ' selected="selected" '; ?>value="basePriceWithTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_WITHTAX'); ?></option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'basePrice')) echo ' selected="selected" '; ?>value="basePrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_BASEPRICE'); ?></option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'priceWithoutTax')) echo ' selected="selected" '; ?>value="priceWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_NOTAX'); ?></option>
		 <option <?php if (empty($product_price_display) || (!empty($product_price_display) && ($product_price_display == 'salesPrice'))) echo ' selected="selected" '; ?>value="salesPrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_PRICE'); ?></option>
		</select>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="id_subtotal_display"><?php echo JText::_('COM_ONEPAGE_TAXES_SUBTOTAL'); ?></label> 
	    </td>
	    <td>
	    <select name="subtotal_price_display" id="id_subtotal_display">
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'basePriceWithTax')) echo ' selected="selected" '; ?>value="basePriceWithTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_WITHTAX'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'diffTotals')) echo ' selected="selected" '; ?>value="diffTotals"><?php echo JText::_('COM_ONEPAGE_TAXES_DIFFERENCE_TOTAL'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'basePrice')) echo ' selected="selected" '; ?>value="basePrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_BASEPRICE'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'billSub')) echo ' selected="selected" '; ?>value="billSub"><?php echo JText::_('COM_ONEPAGE_TAXES_BILLSUB'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'priceWithoutTax')) echo ' selected="selected" '; ?>value="priceWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_NOTAX'); ?></option>
		 <option <?php if (empty($subtotal_price_display) || (!empty($subtotal_price_display) && ($subtotal_price_display == 'salesPrice'))) echo ' selected="selected" '; ?>value="salesPrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_PRICE'); ?></option>
		</select>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="id_coupon_display"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON'); ?></label> 
	    </td>
	    <td>
	    <select name="coupon_price_display" id="id_coupon_display">
		 <option <?php if ((!empty($coupon_price_display) && ($coupon_price_display == 'billDiscountAmount'))) echo ' selected="selected" '; ?>value="billDiscountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_BILLDISCOUNT'); ?></option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'discountAmount')) echo ' selected="selected" '; ?>value="discountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_DISCOUNT'); ?></option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'couponValue')) echo ' selected="selected" '; ?>value="couponValue"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_COUPONVALUE'); ?></option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'salesWithoutTax')) echo ' selected="selected" '; ?>value="salesWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_NOTAX'); ?></option>
		 <option <?php if  (empty($coupon_price_display) || (!empty($coupon_price_display) && ($coupon_price_display == 'salesPriceCoupon'))) echo ' selected="selected" '; ?>value="salesPriceCoupon"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_PRICE'); ?></option>
		</select>
	    </td>
		</tr>
		
		
			<tr>
		<td class="key">
	     <label for="payment_discount_before2"><?php echo JText::_('COM_ONEPAGE_TAXES_PAYMENT'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="payment_discount_before" name="payment_discount_before" <?php if (!empty($payment_discount_before)) echo ' checked="checked" '; ?> value="1" /> <label for="payment_discount_before"><?php echo JText::_('COM_ONEPAGE_TAXES_PAYMENT_DESC'); ?></label> 
		 
		 <select name="other_discount_display" id="id_coupon_display">
		 <option <?php if (empty($other_discount_display) ||((!empty($other_discount_display) && ($other_discount_display == 'billDiscountAmount')))) echo ' selected="selected" '; ?>value="billDiscountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_BILLDISCOUNT'); ?></option>
		 <option <?php if (!empty($other_discount_display) && ($other_discount_display == 'discountAmount')) echo ' selected="selected" '; ?>value="discountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_DISCOUNT'); ?></option>
		 <option <?php if (!empty($other_discount_display) && ($other_discount_display == 'minus')) echo ' selected="selected" '; ?>value="minus"><?php echo JText::_('COM_ONEPAGE_OTHER_DISCOUNT_MINUS'); ?></option>
		
		 <option <?php if (!empty($other_discount_display) && ($other_discount_display == 'sum')) echo ' selected="selected" '; ?>value="sum"><?php echo JText::_('COM_ONEPAGE_OTHER_DISCOUNT_SUM'); ?></option>
		</select>
		 
		 
		 
		</td>
		</tr>
		<tr>
			    <td class="key">
	     <label for="id_coupon_display"><?php echo JText::_('COM_ONEPAGE_TAXES_ZEROTOTAL'); ?></label> 
	    </td>
	    <td>
	    <select name="zero_total_status" id="zero_total_status">
		  <?php 
		  foreach ($this->statuses as $k=>$s)
		   {
		      echo '<option '; 
		   if (empty($zero_total_status) && ($s['order_status_code'] == 'C')) echo ' selected="selected" '; 
		   else if ((!empty($zero_total_status)) && ($zero_total_status == $s['order_status_code'])) echo ' selected="selected" '; 
			  
			  echo ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }
		  ?>
		</select>
	    </td>
		</tr>
		
		
		<tr>
		<?php 
		if (!isset($show_single_tax)) $show_single_tax = true; 
		?>
		<td class="key">
	     <label for="show_single_tax"><?php echo JText::_('COM_ONEPAGE_TAXES_SINGLETAX'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="show_single_tax" name="show_single_tax" <?php if (!empty($show_single_tax)) echo ' checked="checked" '; ?> value="1" /> 
		 <?php echo JText::_('COM_ONEPAGE_TAXES_SINGLETAX_DESC'); ?>
		</td>
		</tr>


		<tr>
		<?php 
		
		?>
		<td class="key">
	     <label for="opc_dynamic_lines"><?php echo JText::_('COM_ONEPAGE_TAXES_DYNAMIC_LINES').'<br />'.JText::_('COM_ONEPAGE_NEW'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="opc_dynamic_lines" name="opc_dynamic_lines" <?php if (!empty($opc_dynamic_lines)) echo ' checked="checked" '; ?> value="1" /> 
		 <?php echo JText::_('COM_ONEPAGE_TAXES_DYNAMIC_LINES_DESC'); ?>
		</td>
		</tr>

		

		
	  </table>
		</fieldset> 
		<?php 
		if (!empty($this->currencies) && (count($this->currencies) > 1)) {  ?> 
		
		<fieldset class="adminform">
		  <legend><?php echo JText::_('COM_ONEPAGE_CURRENCY_SETTINGS'); ?></legend>
		  <?php
		  
		   if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php'))
		    {
			  echo '<span style="color: red;">'.JText::_('COM_ONEPAGE_GEO_COUNTRY_CURRENCY_GEOLOCATOR_NOT_FOUND').'</span><br /><a href="http://www.rupostel.com/com_geolocator.zip">'.JText::_('COM_ONEPAGE_DOWNLOAD_GEOLOCATOR').'</a>'; 
			}
			else
			{
		  ?>
		  
		  <table class="admintable adminlist">
		  <tr>
		    <td class="key"><?php echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN'); ?>
			</td>
			<td>
			   <?php $enabled = JPluginHelper::isEnabled('system', 'opc_currency'); 
			   
			   
			   ?>
			   <input type="checkbox" name="currency_plg" value="1" <?php if (!empty($enabled)) echo ' checked="checked" '; ?>" id="currency_plg" />
			</td>
			  <td colspan="2">
			   <label for="currency_plg"><span style="color: <?php 
			   if (!$enabled) echo 'red'; 
			   else echo 'green'; ?>;"><?php
			   echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN_DESC'); ?></span></label>
			  </td>
		  </tr>
		   <tr>
		    <td class="key"><?php echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN_DISABLE_CHANGE'); ?>
			</td>
			<td>
			   <?php 
			   $default = false; 
			   $enabled = OPCconfig::getValue('currency_config', 'can_change', 0, $default);
			    
			   
			   ?>
			   <input type="checkbox" name="currency_plg_can_change" value="1" <?php if (!empty($enabled)) echo ' checked="checked" '; ?>" id="currency_plg" />
			</td>
			  <td colspan="2">
			   <label for="currency_plg_can_change"><span> 
			   <?php
			   echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN_CAN_CHANGE_DESC'); ?></span></label>
			  </td>
		  </tr>
		  
		  
		  
		  <?php foreach ($this->currencies as $c)
		  {
		  ?>
		  
		  <tr>
		    <td class="key">
			 <?php echo JText::_('COM_ONEPAGE_ASSOCIATE_A_COUNTRY_TO_A_CURRENCY'); ?>
			</td>
		    <td>
			<?php
			     echo $c->currency_name.' ('.$c->currency_code_3.')'; 
			   
			   ?>
			 
			</td>
			
			<td>
			
			<select style="min-width: 150px; " data-placeholder="<?php echo JText::_('COM_VIRTUEMART_COUNTRY_S'); ?>" multiple="multiple" name="country_currency[<?php echo $c->virtuemart_currency_id; ?>][]" class="vm-chzn-select"  id="country_currency_<?php echo $c->virtuemart_currency_id; ?>">
			<?php 
			$default = 0; 
			if (!empty($this->countries))
			 {
			    foreach($this->countries as $p)
				 {
				 
				 $c_int = (int)OPCconfig::getValue('currency_config', $p['country_2_code'], 0, $default); 
				 
				 
				 
				 $p['virtuemart_country_id'] = (int)$p['virtuemart_country_id']; 
				 
				 
				 
				 ?>
				    <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		 if ($c_int == $c->virtuemart_currency_id) 
		 {
			
		   echo ' selected="selected" '; 
		 }
		 ?>><?php echo $p['country_name']; ?></option>
				 
				 <?php
				 }
			 }
			?>
			</select>
			</td>
		  </tr>
		 <?php 
		 }
		 ?>
		 </table>
		 <?php 
		 } 
		 ?>
		</fieldset>
		
		<?php
		}
         echo $pane->endPanel();
        echo $pane->startPanel(JText::_('COM_ONEPAGE_AFTER_CHECKOUT_PANEL'), 'panel19');
		?>
		<fieldset class="adminform" style="overflow: visible;">
		
		<legend><?php echo JText::_('COM_ONEPAGE_THIRDPART_SUPPORT'); ?></legend>
          <table class="admintable" id="comeshere5" style="width: 100%;">
		<tr>
		<?php 
		if (!isset($do_not_allow_gift_deletion)) $do_not_allow_gift_deletion = false; 
		?>
		<td class="key">
	     <label for="do_not_allow_gift_deletion"><?php echo JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS'); ?></label> 
	    </td>
	    <td>
		
		 <input style="float:left;" type="checkbox" id="do_not_allow_gift_deletion" name="do_not_allow_gift_deletion" <?php if (!empty($do_not_allow_gift_deletion)) echo ' checked="checked" '; ?> value="1" /> 
		 </td>
		 <td>
		 <label for="do_not_allow_gift_deletion" style="float:left;">
		 <?php echo JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_DESC'); ?>
		 </label>
		 
		 <div style="">
		 <select name="gift_order_statuses[]" multiple="multiple" style="width: 150px;" class="vm-chzn-select" >
		 <?php 
		  		  foreach ($this->statuses as $k=>$s)
		   {
		      echo '<option '; 
			  
			  if (!empty($gift_order_statuses))
		      if (in_array($s['order_status_code'], $gift_order_statuses)) echo ' selected="selected" '; 
		   
			  
			  echo ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }

		  
		  ?>
		 </select>
		 </div>
		 
		</td>
		</tr>
		<tr>
		<td class="key"><label for="theme_fix1"><?php echo JText::_('COM_ONEPAGE_THEME_FIX1_LABEL'); ?></label></td>
		<td colspan="2"><input type="checkbox" id="theme_fix1" name="theme_fix1" <?php if (!empty($theme_fix1)) echo ' checked="checked" '; ?> value="1" /><label for="theme_fix1"> <?php echo JText::_('COM_ONEPAGE_THEME_FIX1_DESC'); ?></label></td>
		</tr>
		
		</table>
		</fieldset>
		<fieldset class="adminform">
		
        <legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT'); ?></legend>
          <table class="admintable" id="comeshere6" style="width: 100%;">
	   
		
	   <tr>
	    <td class="key">
	     <label for="tr_ext_id"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_SENDEMAIL'); ?></label> 
	    </td>
		  
		<td>
		<input type="checkbox" name="send_pending_mail" id="send_pending_mail" <?php if (!empty($send_pending_mail)) echo ' checked="checked" '; ?> value="1" />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_SENDEMAIL_DESC'); ?>
		</td>
		</tr>
		</table>
        </fieldset>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU'); ?></legend>
          <table class="admintable" id="comeshere7" style="width: 100%;">
	    
		 <tr>
		 
		 <td class="key">
	   <label for="adwords_enabled_0"><span style="color: <?php if (!empty($this->isEnabled)) echo 'green'; else echo 'red'; ?>;"><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_ENABLE'); ?></span></label> 
	    </td>
		 
	    <td >
		 <input id="adwords_enabled_0" type="checkbox" name="adwords_enabled_0" <?php if (!empty($this->isEnabled)) echo 'checked="checked" '; ?>/>
	     
	    </td>
	    
		<td>
		</td>
		</tr>
		
		
		
		<tr>
	    <td class="key">
	     <label for="append_details"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_APPEND'); ?></label> 
	    </td>
		  
		<td>
		<input type="checkbox" name="append_details" id="append_details" <?php if (!empty($append_details)) echo ' checked="checked" '; ?> value="1" />
		</td>
		<td>
		  <?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_APPEND_DESC'); ?>
		</td>
		</tr>
		</table>
		<p><?php echo JText::_('COM_ONEPAGE_TY_DESC'); ?></p>
		<table id="table_thankyou_config">
		<tr>
		   <th><?php echo JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES'); ?></th>
		   <th><?php echo JText::_('COM_VIRTUEMART_PAYMENTMETHOD'); ?></th>
		   <th colspan="2"><?php echo JText::_('COM_CONTENT_SELECT_AN_ARTICLE'); ?></th>
		    
		 </tr>

		<tr>
		<?php
		//stAn thank you page articla start
		
		  //echo $num; 
		 $num = 0; 
		 
		 if (empty($this->lang_thank_youpage)) 
		 $this->lang_thank_youpage[0] = ''; 
		 
		
		 
		 
		 foreach ($this->lang_thank_youpage as $key=>$uu2)
		 {
		 
		 if (strpos($key, '-')>0) continue; 
		 $uu = (array)$uu2; 
		 $code ='
		
		  
		  
		  <td>
		  
		  <select style="margin: 0;" id="op_ostatus_{num}" name="op_ostatus_{num}">
	      
		';
		  if (empty($uu['order_status'])) $uu['order_status'] = ''; 
		  foreach ($this->statuses as $k=>$s)
		   {
		      $code .= '<option '; 
		   if (empty($uu['order_status']) && ($s['order_status_code'] == 'C')) 
		   $code .= ' selected="selected" '; 
		   else if ((!empty($uu['order_status'])) && ($uu['order_status'] == $s['order_status_code'])) 
		   $code .= ' selected="selected" '; 
			  
			  $code .= ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }
      $code .= '		
	   </select>
	       </td>
		   <td>
		    <select style="margin: 0;" name="op_opayment_{num}">
	      <option ';
		  if (empty($uu['payment_id'])) 
		  {
		  $code .= ' selected="selected" '; 
		  $uu['payment_id'] = 0; 
		  }
$code .= ' value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> '; 
		foreach($this->pms as $p)
		{
		 $code .= '
		 <option value="'.$p['payment_method_id'].'" '; 
		 if ($p['payment_method_id']==$uu['payment_id']) 
		 $code .=		 'selected="selected" '; 
		 $code .= '>'; 
		 $code .= $p['payment_method_name'].'</option>'; 
		 
		}
		$code .= '

	   </select></td><td>'; 
	   if (empty($uu['article_id'])) $uu['article_id'] = 0; 
	   $artc = $this->model->getArticleSelector('op_oarticle_{num}', $uu['article_id']); 
	   $code .= $artc; 
	  
		  
		 $code .=' <td>
		    <select style="margin: 0;" name="op_olang_{num}">
	      <option ';
		  if (empty($uu['language'])) 
		  {
		  $code .= ' selected="selected" '; 
		  $uu['language'] = null; 
		  }
$code .= ' value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> '; 
		foreach($this->codes as $p)
		{
		 $code .= '
		 <option value="'.$p['lang_code'].'" '; 
		 if ($p['lang_code']==$uu['language'])
		 {		 	
		 
		 $code .=		 'selected="selected" '; 
		 }
		 $code .= '>'; 
		 $code .= $p['lang_code'].'</option>'; 
		 
		}
		$code .= '

	   </select></td>';
	   
	    $code .=' <td>
		    <select style="margin: 0;" name="op_omode_{num}">';
		  if (empty($uu['mode'])) 
		  {

		  $uu['mode'] = 0; 
		  }

		$modes = array(0,1,2); 
		foreach($modes as $p)
		{
		 $code .= '
		 <option value="'.$p.'" '; 
		 if ($p==$uu['mode']) 
		 $code .=		 'selected="selected" '; 
		 $code .= '>'; 
		 $code .= JText::_('COM_ONEPAGE_TY_MODE_'.$p).'</option>'; 
		 
		}
		$code .= '

	   </select></td>';
	   
		  
		   $code .='</td>
		   <td>
		   <a href="#" onclick="javascript: return op_new_line2(opc_line_ty, \'table_thankyou_config\' );" >'.JText::_('COM_ONEPAGE_ADD_MORE').'</a>
		   <a style="margin-left: 50px;" href="#" onclick="javascript: return op_remove_line2(\'{num}\', \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_REMOVE').'</a>
		   </td>
		  '; 
		  $jscode = $code; 
		  
		  $code_sg = '<tr id="rowid2_'.$num.'">'.str_replace('{num}', $num, $code).'</tr>'; 
		 $num++;
		  unset($code); 
		  echo $code_sg; 
		  }
		  
		  $code_sg = $jscode; 
		  $code_sg = trim($code_sg); 
		  $code_sg = str_replace("\r\r\n", "", $code_sg); 
		  $code_sg = str_replace("\r\n", "", $code_sg); 
		  $code_sg = str_replace("\n", "{br}", $code_sg); 
		  $code_sg = str_replace("<", '&lt;', $code_sg); 
		  $code_sg = str_replace(">", '&lg;', $code_sg); 
		  $document->addScriptDeclaration(' 
//<![CDATA[		  
var line_iter2 = '.$num.'; 
var opc_line_ty = \''.str_replace("'", "\'", $code_sg).'\';
//]]>
'); 

		  
		  //unset($code); 
		  $last_num = $num; 
		  
		
		// stAn thank page article end
		?>
		</table>
        </fieldset>

        <?php 
         echo $pane->endPanel();
		
        echo $pane->startPanel(JText::_('COM_ONEPAGE_LANGUAGE_PANEL'), 'panel94'); ?>
        
        <fieldset class="adminform" style="width: 100%;">
        <legend><?php echo JText::_('COM_ONEPAGE_LANGUAGE'); ?></legend>
		<div id="opc_language_editor">
		  <table class="admintable" id="comeshere8" style="width: 100%;">
	    
		<tr>
	    <td class="key">
	     <label for="tr_type"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TYPE'); ?></label> 
	    </td>
		<td>
		  <select name="tr_type" id="tr_ext_id" onchange="javascript: return ext_chageList(this);">
			<option value="site"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TYPE_SITE'); ?></option>
			<option value="administrator"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TYPE_ADMIN'); ?></option>
		  </select>
		 </td>
		 </tr>
		<tr>
	    <td class="key">
	     <label for="tr_ext_id"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_EXT'); ?></label> 
	    </td>
		<td>
		  <select name="tr_ext_site" id="tr_ext_site">
		  <?php
		    foreach($this->exts as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'com_onepage.ini')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		  
		   <select name="tr_ext_administrator" id="tr_ext_administrator" style="display: none;">
		  <?php
		    foreach($this->adminxts as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'com_onepage.ini')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		  
		 </td>
		 </tr>
		 <tr>
		 <td class="key">
	     <label for="tr_fromlang_id"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_FROMLANG'); ?></label> 
	     </td>
		 <td>
		  <select name="tr_fromlang" id="tr_fromlang_id">
		  <?php
		    foreach($this->extlangs as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'en-GB')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		 </td>
		 </tr>
		 
		 <tr>
		 <td class="key">
	     <label for="tr_tolang_id"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TOLANG'); ?></label> 
	     </td>
		 <td>
		  
		  <select name="tr_tolang" id="tr_tolang_id">
		  <?php
		  
			$config =& JFactory::getConfig();
			if (method_exists($config, 'getValue'))
			$flang = $config->getValue('config.language');
			else 
			$flang = $config->get('language');
			
		    foreach($this->extlangs as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (stripos($key, $flang )!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		</td>
		</tr>
		<tr>
		 <td>
		   <input type="button" value="<?php echo JText::_('COM_ONEPAGE_LANGUAGE_BTN_VALUE'); ?>" onclick="javascript: submitbutton('langedit');" />
		 </td>
		</tr>
		<?php if (!empty($this->langerr))
		{
		 ?>
		  <tr>
		  <td>
		  <?php echo JText::_('COM_ONEPAGE_LANGUAGE_CREATE_COPY'); ?><br />
		  <?php
		  foreach ($this->langerr as $mi)
		   {
		     $orig = str_replace(VMLANG, 'en_gb', $mi); 
		     echo 'Copy table <b>'.$orig.'</b> to <b>'.$mi.'</b><br />'; 
		   }
		  ?>
		  </td>
		  </tr>
		  <tr>
		 <td>
		   <input type="button" value="YES, make the copy of the tables above" onclick="javascript: submitbutton('langcopy');" />
		 </td>
		</tr>
		 <?php
		}
		?>
		</table>
		  
		  
		  
		</div>
    
	
	</fieldset>
        <?php 
        echo $pane->endPanel();
		
   
		
	echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_EXTENSIONS_PANEL'), 'panel69');
?>
<fieldset><legend><?php echo JText::_('COM_ONEPAGE_OPC_EXTENSIONS'); ?></legend>
	<?php 
	/*
	if (empty($this->exthtml)) echo JText::_('COM_ONEPAGE_OPC_EXTENSIONS_NOEXT'); 
	else
	echo $this->exthtml; 
	*/
	?><table class="admintable" id="extension_list">
	<?php
	if (!empty($this->opcexts))
	 {
	   foreach ($this->opcexts as $ext)
	    {
		 
		  echo '<tr>'; 
		  echo '<td>'.$ext['name'].'</td>';  
		  echo '<td>'.$ext['description'].'</td>'; 
		  if (!empty($ext['data']))
		  {
		  if (!empty($ext['link']))
		  echo '<td><div style="color: green;" href="'.$ext['link'].'">'.JText::_('COM_ONEPAGE_INSTALLED').'</div></td>'; 
		  else
		  echo '<td style="color: green;">'.JText::_('COM_ONEPAGE_INSTALLED').'</td>'; 
		  //
		  }
		  else
		   {
		   echo '<td><input type="button" onclick="javascript: submitbutton(\'installext\');" value="'.JText::_('COM_ONEPAGE_INSTALL_OPCEXTENSION').'" /></td>'; 
		   }
		  echo '</tr>'; 
		}
	 }
	 
	?></table>
</fieldset>

<?php
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('COM_ONEPAGE_NOTES_PANEL'), 'panel68');
	?>
	<fieldset><legend><?php echo JText::_('COM_ONEPAGE_NOTES'); ?></legend>
	<h3><?php echo JText::_('COM_ONEPAGE_NOTES_COMMON'); ?></h3> 
	<p><?php echo JText::_('COM_ONEPAGE_NOTES_COMMON_DESC'); ?></p>
	<h3><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED'); ?></h3>
	<p><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_DESC'); ?></p>
	<ul>
	 <li><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_OPT1'); ?></li>
	 <li><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_OPT2'); ?></li>
	 <li><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_OPT3'); ?></li>
	 
	</ul>
	
	</fieldset>
	<?php
	echo $pane->endPanel(); 
	echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_CACHING_PANEL'), 'panel60');
	?>
		<fieldset><legend><?php echo JText::_('COM_ONEPAGE_OPC_CACHING'); ?></legend>
	<h3><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_HEAD'); ?></h3> 
	<p><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_DESC'); ?></p>
	
	<br />
	<table class="admintable" style="width: 100%;">
	<?php 
	$file = JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'alatak_usps'.DS.'alatak_usps.php'; 
    if (file_exists($file))
	{
    $x = file_get_contents($file); 
	if (stripos($x, 'self::$uspsCache')===false)
	if (file_exists($file))
	{
		?>
		<tr>
	    <td class="key">
	     <label for="usps_cache"><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS'); ?></label> 
	    </td>
	    <td>

	<input type="button" id="usps_cache" name="usps_cache"  onclick="javascript: submitbutton('<?php
		$file = JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'alatak_usps'.DS.'alatak_usps.php'; 

		$file2 = str_replace('.php', '_opc_backup.php', $file); 			
		
		if (file_exists($file2))
		{
			echo 'removepatchusps'; 
			$uspspatch = true; 
		}
		else
			echo 'patchusps';
	
	?>');" value="<?php if (empty($uspspatch)) echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS_PATCH'); else echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS_REMOVE'); ?>" /></td><td><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS_DESC'); ?></td>
		
		</tr>
		<?php 
		}
		}
		?>

	<tr>
	    <td class="key">
	     <label for="opc_calc_cache"><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_CALC'); ?></label> 
	    </td>
	    <td>

	<input type="checkbox" id="opc_request_cache" name="opc_request_cache" <?php if (!empty($opc_request_cache)) echo ' checked="checked" '; ?> /></td><td><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_CALC_DESC'); ?></td>
		
		</tr>

	  <tr>
	    <td class="key">
	     <label for="opc_calc_cache"><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_PERMAMENT'); ?></label> 
	    </td>
	    <td>

	<input type="checkbox" id="opc_calc_cache" name="opc_calc_cache" <?php if (!empty($opc_calc_cache)) echo ' checked="checked" '; ?> /></td><td><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_PERMAMENT_DESC'); ?></td>
		
		</tr>
	</table> 
	</fieldset>
	<?php 
    echo $pane->endPanel(); 
	echo $pane->endPane();
		?>
  </form>

<?php
echo ob_get_clean();
function checkFile($file, $file2=null)
{
 $pi = pathinfo($file);
 if (!empty($pi['extension']))
  $name = str_replace('.'.$pi['extension'], '', $pi['basename']);
 else $name = $pi['basename']; 

 $orig = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'vm_files'.DS.$pi['basename'];
 if (!empty($file2)) $orig = $file2;
 
 if (!file_exists($orig)) return 'Cannot Check';
 if (!file_exists($orig) && (file_exists($file))) return 'OK';
 if (file_exists($file))
 {
  
  $d1 = filemtime($file);
  $d2 = filemtime($orig);
  if ($d2>$d1)
  { 
  $d1 = hash_file('md5',$file);
  $d2 = hash_file('md5',$orig);
  if ($d1 != $d2 )
  {
   if (strpos($file, 'templates')!==false)
   return 'Template will not be overwritten'.retI($name, 'template');
   else
   return 'Upgrade'.retI($name, 'install');
  }
  else return 'OK'.retI($name, 'ok');
  
  }
  
  else
  return 'OK'.retI($name, 'ok');; 
 }
 else return 'File not found'.retI($name, 'install');;
}

function retI($name, $task)
{
 return '<input type="hidden" name="'.$name.'" value="'.$task.'" />';
}

// functions to parse variables
function parseP($hidep)
{
 $hidep = str_replace(' ', '', $hidep);
 $arr = explode (',', $hidep);
 return $arr;
}
// returns true if an payment id is there
function isThere($id, $hidep)
{
 //var_dump($id); 
 //var_dump($hidep);
 
 $hidep = ','.$hidep.',';
 if (strpos($hidep, ','.$id.',') !== false) return true;
 if (strpos($hidep, ','.$id.'/') !== false) return true;
 return false;
}
// for an payment id get a default payment id 
function getDefP($id, $hidep)
{
 $hidep = ','.$hidep.',';
 if (strpos($hidep, '/'.$id.',') !== false) return true;
 return false;
 
}
$_SESSION['endmem'] = memory_get_usage(true); 
$mem =  $_SESSION['endmem'] - $_SESSION['startmem'];
//echo 'Cm: '.$mem.' All:'.$_SESSION['endmem'];
$document = JFactory::getDocument();
 
// Add Javascript
$js = '
//<![CDATA[
		if ((typeof window != \'undefined\') && (typeof window.addEvent != \'undefined\'))
			   {
			   window.addEvent(\'domready\', function() {
			      ';
				  if (!OPCJ3)
				  $js .= '
			      initRows(); 
				  op_checkHt();
				  '; 
				  
$js .= '				  
				   '; 
if (empty($disable_check))
$js .= '
				if (typeof getOPCExts != \'undefined\')
				  getOPCExts(); 
'; 
$js .= '				  
			    });
			   }
			   else
			   {
			     if(window.addEventListener){ // Mozilla, Netscape, Firefox
			window.addEventListener("load", function(){ 
			initRows();  
			op_checkHt(); 
			}, false);
			 } else { // IE
			window.attachEvent("onload", function(){ 
			op_checkHt(); 
			initRows();  
			});
			 }
			   }
			 
    
//]]>
';
$document->addScriptDeclaration($js); 

