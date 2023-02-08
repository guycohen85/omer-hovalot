<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport( 'joomla.application.component.model' );
	jimport( 'joomla.filesystem.file' );
	
	
    
  // Load the virtuemart main parse code
	
	
    require_once ( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
	
class JModelOrders extends OPCModel
{
 /**
   * Items total
   * @var integer
   */
  var $_total = null;
 
  /**
   * Pagination object
   * @var object
   */
  var $_pagination = null;
	var $_data = null;
	
    function __construct()
		{
			parent::__construct();

			 $mainframe = JFactory::getApplication(); 
 
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

		
		}
		function getTemplates()
		{
		  return array(); 
		}
		function _buildQuery() 
		{ 
    
      $ehelper = new OnepageTemplateHelper();
      if ($ehelper->columnExists('#__virtuemart_orders', 'track_num'))
      {
        $ups = 'o.track_num, ';
      }
      else 
       $ups = ''; 

	  $keyword = JRequest::getVar('keyword', '');
	  $show = JRequest::getVar('show', '');
	  
	  
      $list  = "SELECT o.virtuemart_order_id as order_id, o.order_status, o.order_pass, ".$ups." o.order_number, o.created_on, o.modified_on, o.order_total, o.order_currency, o.virtuemart_user_id as user_id,";
	  $list .= "u.first_name, u.last_name FROM #__virtuemart_orders as o LEFT JOIN #__virtuemart_order_userinfos as u ON o.virtuemart_order_id = u.virtuemart_order_id WHERE ";
	  $count = "SELECT count(*) as num_rows FROM #__virtuemart_orders, #__virtuemart_order_userinfos WHERE ";
	  $q = " u.address_type = 'BT' ";
	  if (!empty($keyword)) {
        $q  .= " AND (#__virtuemart_orders.order_id LIKE '%$keyword%' ";
        $q .= "OR #__virtuemart_orders.order_status LIKE '%$keyword%' ";
        $q .= "OR first_name LIKE '%$keyword%' ";
        $q .= "OR last_name LIKE '%$keyword%' ";
		$q .= "OR CONCAT(`first_name`, ' ', `last_name`) LIKE '%$keyword%' ";
        $q .= ") ";
	}
	if (!empty($show)) {
	 $q .= " AND order_status = '$show'  ";
	}
//	$q .= "(#__vm_orders.order_id=#__vm_order_user_info.order_id) ";
	//$q .= " o.vendor_id='".$_SESSION['ps_vendor_id']."' ";
	$q .= "ORDER BY o.created_on DESC ";
	$list .= $q;
	// . " LIMIT $limitstart, " . $limit;
	//$count .= $q;   
    $query = $list; //.$limit;
	return $query;
	/*
        $this->_db->setQuery($query); 

        $this->_data = $this->_db->loadObjectList(); 
        $this->_total = count( $this->_data ) ; 
   */
    
} 

	function datePicker($jsDateFormat, $name, $id, $date='', $placeholder='')
	{
		$display  = '<input class="datepicker-db" id="'.$id.'" type="hidden" name="'.$name.'" value="'.$date.'" />';
		$display .= '<input id="'.$id.'_text" class="datepicker" type="text" value="'.$formatedDate.'" placeholder="'.$placeholder.'" />';
		

		// If exist exit
		
		$front = 'components/com_virtuemart/assets/';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
//<![CDATA[
			jQuery(document).ready( function($) {
			$("#'.$id.'_text").live( "focus", function() {
				$( this ).datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat:"'.$jsDateFormat.'",
					altField: $(this).prev(),
					altFormat: "yy-mm-dd"
				});
			});
			$(".js-date-reset").click(function() {
				$(this).prev("input").val("'.$placeholder.'").prev("input").val("0");
			});
		});
//]]>
		');
		vmJsApi::js ('jquery.ui.core',FALSE,'',TRUE);
		vmJsApi::js ('jquery.ui.datepicker',FALSE,'',TRUE);

		vmJsApi::css ('jquery.ui.all',$front.'css/ui' ) ;
		$lg = JFactory::getLanguage();
		$lang = $lg->getTag();

		$existingLang = array("af","ar","ar-DZ","az","bg","bs","ca","cs","da","de","el","en-AU","en-GB","en-NZ","eo","es","et","eu","fa","fi","fo","fr","fr-CH","gl","he","hr","hu","hy","id","is","it","ja","ko","kz","lt","lv","ml","ms","nl","no","pl","pt","pt-BR","rm","ro","ru","sk","sl","sq","sr","sr-SR","sv","ta","th","tj","tr","uk","vi","zh-CN","zh-HK","zh-TW");
		if (!in_array ($lang, $existingLang)) {
			$lang = substr ($lang, 0, 2);
		}
		elseif (!in_array ($lang, $existingLang)) {
			$lang = "en-GB";
		}
		vmJsApi::js ('jquery.ui.datepicker-'.$lang, $front.'js/i18n' ) ;
		
		return $display; 
	}

	function loadVirtuemart()
	{
	   if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		


    $jlang =JFactory::getLanguage();
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, 'en-GB', true);
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
	
	$jlang->load('com_virtuemart_orders', JPATH_SITE, 'en-GB', true);
    $jlang->load('com_virtuemart_orders', JPATH_SITE, $jlang->getDefault(), true);
	
	$jlang->load('com_virtuemart_shoppers', JPATH_SITE, 'en-GB', true);
    $jlang->load('com_virtuemart_shoppers', JPATH_SITE, $jlang->getDefault(), true);

	
	
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, null, true);

	vmJsApi::jQuery();
		
		if (!class_exists('AdminUIHelper'))
		 {
		  // require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'adminui.php'); 
		 }
		 $front = JURI::root(true).'/components/com_virtuemart/assets/';
		$admin = JURI::root(true).'/administrator/components/com_virtuemart/assets/';
		$document = JFactory::getDocument();

		//loading defaut admin CSS
		$document->addStyleSheet($admin.'css/admin_ui.css');
		//$document->addStyleSheet($admin.'css/admin_menu.css');
		$document->addStyleSheet($admin.'css/admin.styles.css');
		$document->addStyleSheet($admin.'css/toolbar_images.css');
		$document->addStyleSheet($admin.'css/menu_images.css');
		$document->addStyleSheet($front.'css/chosen.css');
		$document->addStyleSheet($front.'css/vtip.css');
		$document->addStyleSheet($front.'css/jquery.fancybox-1.3.4.css');
		$document->addStyleSheet($front.'css/ui/jquery.ui.all.css');
		//$document->addStyleSheet($admin.'css/jqtransform.css');

		//loading defaut script

		$document->addScript($front.'js/fancybox/jquery.mousewheel-3.0.4.pack.js');
		$document->addScript($front.'js/fancybox/jquery.easing-1.3.pack.js');
		$document->addScript($front.'js/fancybox/jquery.fancybox-1.3.4.pack.js');
		$document->addScript($admin.'js/jquery.coookie.js');
		$document->addScript($front.'js/chosen.jquery.min.js');
		$document->addScript($admin.'js/vm2admin.js');

		$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('COM_VIRTUEMART_DRDOWN_SELALL')."',select_some_options_text: '".JText::_('COM_VIRTUEMART_DRDOWN_AVA2ALL')."'" ;
		$document->addScriptDeclaration ( "
//<![CDATA[
		var tip_image='".JURI::root(true)."/components/com_virtuemart/assets/js/images/vtip_arrow.png';
		var vm2string ={".$vm2string."} ;
		 jQuery( function($) {

			$('dl#system-message').hide().slideDown(400);
			$('.virtuemart-admin-area .toggler').vm2admin('toggle');
			$('#admin-ui-menu').vm2admin('accordeon');
			if ( $('#admin-ui-tabs').length  ) {

				$('#admin-ui-tabs').vm2admin('tabs',virtuemartcookie).find('select').chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text}); 
			}

			$('#content-box [title]').vm2admin('tips',tip_image);
			$('.modal').fancybox();
			$('.reset-value').click( function(e){
				e.preventDefault();
				none = '';
				jQuery(this).parent().find('.ui-autocomplete-input').val(none);
				
			});

		});
//]]>
		");
		 
		 
		 
	}
	
	function eexport()
	{
	
	}
		
	function getData() 
  {
	
        // if data hasn't already been obtained, load it
       if (empty($this->_data)) {
            $query = $this->_buildQuery();
			$db = JFactory::getDBO(); 
			$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $db->loadObjectList(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { echo $e; die(); }
            //$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
            //if (empty($this->_data)) {echo $this->_db->getErrorMsg(); return;}  

        }
        return $this->_data;
  }
 function getTotal()
  {
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
			//testing: 
			$db= JFactory::getDBO(); 
			$db->setQuery($query); 
			$db->loadAssoc(); 
			$e = $db->getError(); if (!empty($e)) { echo $e; die(); }
            $this->_total = $this->_getListCount($query);    
            
        }
        
        return $this->_total;
  }
function getPagination()
  {
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
       
        return $this->_pagination;
  }

	function save()
	{
	 $db = JFactory::getDBO();
	 $data = JRequest::get('post');
	 $e = '';
	 //var_dump($data); 
	 foreach ($data as $key => $d)
	 {
	  $arr = explode('_', $key);
	  if (count($arr)>1)
	  $order_id = (int)$arr[count($arr)-1];
	//   var_dump($arr); 

	  if (isset($order_id))
	  if (is_numeric($order_id))
	  {
	  
	    if (isset($data['changed_'.$order_id]) && ($data['changed_'.$order_id]=='1'))
	    {
	    
	    if (strpos($key, 'order_status')===0)
	    {
	     $vars = array();
	     if (isset($data['notify_customer_'.$order_id]))
	     {
	     $vars['notify_customer'] = 'Y';
	     //echo $data['notify_customer_'.$order_id]; die();
	     
	     }
	     else $vars['notify_customer'] = '';
	     $vars['order_status'] = $data['order_status_'.$order_id];
	     //echo $data['order_status_'.$order_id]; die();
	     $vars['curr_order_status'] = $data['current_order_status_'.$order_id];
	     $vars['order_item_id'] = '';
	     $vars['order_number'] = $data['order_number_'.$order_id];
	     $vars['order_comment'] = '';
	     $vars['order_id'] = $order_id;
	     $vars['include_comment'] = '';
	     $q = "select virtuemart_vendor_id from #__virtuemart_vendors where 1";
	     
	     $db->setQuery($q);
	     $vendor_id = $db->loadResult();
		 /*
	     $_SESSION['ps_vendor_id'] = $vendor_id;
	     $ps_order = new ps_order;
	     
	   	 ob_start();  
     	 if (!$ps_order->order_status_update($vars))  $e .= 'Error updating order status of order '.$order_id.'<br />';
		 */
		 die('order update'); 
  		 $e .= ob_get_clean();
  		
	    }
	    }
	  }
	  
	 }
	 //if (!empty($msg)) {echo $msg; die(); }
	 //die('stom');
	 //die();
	 $_SESSION['msg'] = $e;
	 return true;
	}


}