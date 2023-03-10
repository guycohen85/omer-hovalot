<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$document =& JFactory::getDocument();
JHTMLOPC::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
$video = OPCVideoHelp::get('COM_ONEPAGE_TRACKING_GENERAL');

$document->setTitle(JText::_('COM_ONEPAGE_TRACKING_PANEL')); 
JHTMLOPC::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
    window.addEvent('domready', function(){ 
       var JTooltips = new Tips($$('.hasTip'), 
       { maxTitleChars: 50, fixed: false}); 
    });
</script>
<?php

JToolBarHelper::Title(JText::_('COM_ONEPAGE_TRACKING_PANEL').$video , 'generic.png');
//	JToolBarHelper::install();
JToolBarHelper::apply();
jimport('joomla.html.pane');
jimport('joomla.utilities.utility');
jimport ('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
$document = JFactory::getDocument();
				$selectText = JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES');
				$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('Select All')."',select_some_options_text: '".JText::_($selectText)."'" ;
				


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
	.video_link {
	 margin-top: 0px !important; 
	 margin-left: 20px; 
	 vertical-align: middle !important;
	}
	.video_link span.videospan {
	 
	}
	'; 
	
	$document->addStyleDeclaration($css); 
	
	
	$document = JFactory::getDocument();
	$style = '
	fieldset {
	overflow: visible !important;
	}
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