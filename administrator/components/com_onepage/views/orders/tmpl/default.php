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
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

	JToolBarHelper::Title('OPC Order Management' , 'generic.png');
	JToolBarHelper::save();
	JToolBarHelper::cancel();
	jimport('joomla.html.pane');
	$pane =& JPane::getInstance('tabs', array('startOffset'=>0));
	
	
	
if (!empty($_SESSION['msg']))
{

?>
<div style="width:100%; border: solid 1px;">
<?php
$txt = $_SESSION['msg'];
unset($_SESSION['msg']);
$txt = str_replace('<div class="shop_info">', '', $txt);
$txt = str_replace('</div>', '', $txt);
$txt = str_replace('<div >', '', $txt);
echo $txt;
//$txt = str_replace('<br>', '', $txt);
?>
</div>
<?php
}
	
	
     
        
	JHTML::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
    JHTML::script('onepage_ajax.js', 'administrator/components/com_onepage/views/order_details/tmpl/ajax/', false);
    
// Load the virtuemart main parse code
	
	require_once ( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
    
  	
  	echo $pane->startPane('order_general');
	echo $pane->startPanel(JText::_('COM_VIRTUEMART_ORDERS'), 'px');

// missing variables:
	//$limitstart = JRequest::getVar('limitstart', 0);
	$limitstart  = $this->pagination->limitstart;
	$limit = $this->pagination->limit;
	$num_rows = $this->total;
	//$limit   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 50, 'int');
	//$limit = JRequest::getVar('limit', 50);
	$keyword = JRequest::getVar('keyword', '');
	$modulename = 'order';	
	//$ps_vendor_id = 1;
	//$ps_order_status = new ps_order_status;
	//$ps_html = new ps_html;
	//$GLOBALS('ps_order_status') = $ps_order_status;
	//$VM_LANG->load('order');
	//$db = new ps_DB;
	$db = JFactory::getDBO(); 
	$show = JRequest::getVar('show', '');


   // check if we have to load order list or order details:
/*
   $order_id = JRequest::getVar('order_id', 0);
   
   if ($order_id===0)
*/
   	$document =& JFactory::getDocument();
	$style = '
	
	div.current {
	 float: left;
	 padding: 5 !important;
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
	 
	 padding-left:1px;
     padding-right:1px;
     margin-left:1px;
     margin-right:1px;
     text-indent:1px;
     float: left;
	}';
   $document->addStyleDeclaration($style);

/**
* Rest of this file is a modified copy of order.order_list.php of virtuemart page file
*
* @version $Id: order.order_list.php 1958 2009-10-08 20:09:57Z soeren_nb $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
//mm_showMyFileName( __FILE__ );
//global $page;
//, $ps_order_status;

$show = JRequest::getVar('show', ''); 
//$pageNav = new JPagination( $this->total, $limit_start, $limit );
$pageNav = $this->pagination;
//require_once( CLASSPATH . "htmlTools.class.php" );

$ehelper = new OnepageTemplateHelper();
$templates = $ehelper->getExportTemplates('ALL');
if (!empty($templates))
{
?><a href="#" style='float: right;' onclick="javascript:return opShow('mytmps');"><img src="/images/M_images/pdf_button.png" alt='Create' title='Create' /></a>
<div style="position: absolute; right: 20px; text-align: left; background-color: #CCCCCC; border: 1px solid; margin-top:30px; z-index: 99; display: none; clear: both;" id="mytmps">
<?php
foreach ($templates as $t)
{
 //if (empty($t['tid_special'] || (!empty($t['tid_ai']) && ($t['tid_special']=='1') && ($t['tid_
 echo "<a style='float: left;' href='#' onclick='javascript:return op_runCmd(\"sendXmlMulti\", this);' id='createpdf_".$t['tid']."' ><img src='/images/M_images/pdf_button.png' alt='Create ".$t['tid_name']."' title='Create ".$t['tid_name']."' />".$t['tid_name']."</a><br style='clear: both;'/>";
}
?></div><?php
}
$listObj = new listFactory( $this->pagination );
// end template export part
?>
<div style="text-align: center; margin-left: auto; margin-right: auto;">

<?php
//echo $this->pagination->getPagesLinks();
echo $this->pagination->getPagesCounter();
 
foreach ($this->statuses as $k=>$s) {
?> 
  <a href="index.php?view=orders&amp;option=com_onepage&amp;show=<?php echo $s['order_status_code']; ?>">
  <b><?php echo JText::_($s['order_status_name']); ?></b></a>
      | 
<?php 
} 
?>
    <a href="index.php?view=orders&amp;option=com_onepage&amp;show="><b>
    <?php echo JText::_('COM_VIRTUEMART_ALL') ?></b></a>
</div>
<br />
<?php 
echo '	
<form method="post" action="index.php" name="adminForm">
<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
		<input type="hidden" name="cmd" id="cmd" value="" />
	
';
$form_code = '';
$listObj->startTable();


$upsi = false; 
 

// these are the columns in the table
$checklimit = ($num_rows < $limit) ? $this->total : $limit;

$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".count($this->items).")\" />" => "width=\"20\"",
					JText::_('COM_VIRTUEMART_ORDER_LIST_ID') => '',
					JText::_('COM_VIRTUEMART_NAME') => '',
					
					JText::_('COM_VIRTUEMART_PRINT') => '',
					JText::_('COM_VIRTUEMART_ORDER_CDATE') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_MDATE') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_NOTIFY') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_TOTAL') => '',
					'Referal' => "width=\"5%\""
				);

$listObj->writeTableHeader( $columns );
// so we can determine if shipping labels can be printed
$dbl = JFactory::getDBO();

//$db->query($list);
$i = 0;
//var_dump($this->items);

//while ($db->next_record()) 
foreach ($this->items as $item)
{ 
    
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->getRowOffset( $i ) );
		
	// The Checkbox
	$html_o = '<input type="checkbox" id="cb'.$i.'" name="order_id[]" value="'.$item->order_id.'" onclick="isChecked(this.checked);">'; 
	$listObj->addCell( $html_o );

	//$url = $_SERVER['PHP_SELF']."?option=com_onepage&amp;view=order_detailspage=$modulename.order_print&amp;limitstart=$limitstart&amp;keyword=".urlencode($keyword)."&amp;order_id=". $db->f("order_id");
	$order_id = $item->order_id;
	$url = 'index.php?option=com_onepage&amp;view=order_details&amp;order_id='.$order_id;
	$tmp_cell = '<a href="'.$url.'">'.sprintf("%08d", $order_id).'<br />'.$item->order_number."</a><br />";
	

	
	$listObj->addCell( $tmp_cell );

		
	$tmp_cell = $item->first_name.' '.$item->last_name;
	//if( $perm->check('admin') && defined('_VM_IS_BACKEND')) 
	{
		$url = $_SERVER['PHP_SELF']."?page=admin.user_form&amp;user_id=". $item->user_id;
		$tmp_cell = '<a href="'. $url .'">'.$tmp_cell.'</a>';
	}
	
	$listObj->addCell( $tmp_cell );
	
	
	
	
	
	
	
	


	$print_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $item->order_id . '&order_number=' . $item->order_number . '&order_pass=' . $item->order_pass;
	$print_link = "<a  href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
	$print_link .= '<span  class="hasTip print_32" title="' . JText::_ ('COM_VIRTUEMART_PRINT') . '">&nbsp;</span>';
	$print_link .= '</a>'; 
	
	
    $listObj->addCell( $print_link );
	// Creation Date
	$listObj->addCell( $item->created_on);
	// Last Modified Date
    $listObj->addCell( $item->modified_on);
	$order_id = $item->order_id;
    // Order Status Drop Down List
	$html = '
	 <select name="order_status_'.$order_id.'"  style="width: 150px;" class="vm-chzn-select" onchange="document.adminForm.changed_'.$order_id.'.value=\'1\';" >'; 
		 
		   foreach ($this->statuses as $k=>$s)
		   {
		      $html .= '<option '; 
			  
			  
		      if ($s['order_status_code']== $item->order_status) $html .= ' selected="selected" '; 
		   
			  
			  $html .= ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }

		  
		 $html .= '
		 </select>'; 
		 
	
    //$html = $ps_order_status->getOrderStatusList($item->order_status, "onchange=\"document.adminForm.changed_$order_id.value='1';\"");
    //$html = str_replace('name="order_status"', 'name="order_status_'.$order_id.'"', $html);
	$listObj->addCell( $html );
		
	// Notify Customer checkbox
	$listObj->addCell( '<input type="checkbox" class="inputbox" name="notify_customer_'.$order_id.'" />' 
				."" );
	
	$listObj->addCell($item->order_total);
	$ref = getRefOrders($order_id);
	$listObj->addCell($ref);
	$i++; 
}

$listObj->writeTable();
$listObj->endTable();
echo '<br style="clear: both;" />';
echo '<table style="width: 100%;">';
echo '<tr><td>';
echo $this->pagination->getListFooter(  );
echo '</td></tr></table>';
echo $form_code.'</form>';

 

echo $pane->endPanel();
echo $pane->startPanel('Exported Items', 'ei');
$ehelper->listExports();
echo $pane->endPanel();


 echo $pane->startPanel('Excell Export', 'ei');
 JHTML::_('behavior.calendar');
 if (file_exists(JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'.DS.'Classes'.DS.'PHPExcel.php'))
 {
 ?>
 <h3>Filter Order Export</h3>
 <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" name="adminForm2">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="eexport" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
 <table>
 <tr>
  <th>From</th>
  <th>To</th>
  <th>Export</th>
 </tr>
 <tr>
 <td>
 <?php
 $cal = $this->model->datePicker('mm-dd-yy', 'startdate', 'startdate', '', 'From...'); 
 echo $cal; 
 ?>
 

</td>
<td>


<?php
 $cal = $this->model->datePicker('mm-dd-yy', 'enddate', 'enddate', '', 'From...'); 
 echo $cal; 
 ?>
 

</td>
<td>
 <input type="submit" value="Export Order Items by date" />
</td>
</tr>
<tr>
<td>
 <input class="inputbox" type="text" name="startid" placeholder="From order id"
	id="startid" size="25" maxlength="25"
value="" />
</td>
<td>
 <input class="inputbox" type="text" name="endid" placeholder="to order id"
	id="endid" size="25" maxlength="25"
value="" />
</td>
<td>
 <input type="submit" value="Export Order Items by Order Id" />
</td>
</tr>
</table>
</form>

 <?php
 echo '<a href="index.php?option=com_onepage&amp;view=order_excell" style="float: left;">Export all orders to Excell withoud order details</a>';
 }
 else 
 {
 ?>
 <p>To see Excell export, you must upload phpExcell to your <?php echo JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'.DS.'Classes'.DS.'PHPExcel.php' ?></p>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" name="adminForm2">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="installphpexcell" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
		<input type="submit" name="submit" id="submit" value="Download &amp; Install..." />
		
  </form>
 
 <?php
 }
 
 echo $pane->endPanel();



echo $pane->startPanel('XML Export', 'xmkl');
?>

<h3>XML Order Export</h3>

 <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" name="adminForm3">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="xmlexport" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />

 <?php
 $tids = ' <select name="selected_tid" >'; 
 $ci = 0; 
 foreach ($templates as $t)
 {
 //if (empty($t['tid_special'] || (!empty($t['tid_ai']) && ($t['tid_special']=='1') && ($t['tid_
  if (!empty($t['tid_type']))
  if ( $t['tid_type']=='ORDER_DATA_TXT')
  {
  $tids .= '<option value="'.$t['tid'].'">'.$t['tid_name'].'</option>';
  $ci++; 
  }
 }
 $tids .= '
 </select>'; 
 ?>
 <table>
 <tr>
  <th>From</th>
  <th>To</th>
  <th>Export</th>
 </tr>
 <?php if (!empty($ci)) { ?>
 <tr>
 <td colspan="3"><?php echo $tids; ?></td>
 </tr>
 
 <?php } ?>
 
 <tr class="row1" style="margin-top: 10px;"><td colspan="3">Export by Order Date</td></tr>
 <tr>
 <td>
 <?php
 //$cal = vmJsApi::jDate('', 'startdateo', 'startdateo', true, '');  
 
 $cal = $this->model->datePicker('mm-dd-yy', 'startdateo', 'startdateo', '', 'From...'); 
					  
					  
					  
echo $cal; 
 ?>
 
</td>
<td>
 
<?php 
					  
$cal = $this->model->datePicker('mm-dd-yy', 'enddateo', 'enddateo', '', 'To...'); 
echo $cal; 
?>

</td>
<td>
 <div  <?php //if (empty($ci)) echo ' style="display: none;" '; ?>><input type="submit" value="Export Order Items by date" /></div>
</td>
</tr>
<tr class="row1" style="margin-top: 10px;"><td colspan="3">Export by Order ID</td></tr>
<tr>
<td>
 <input class="inputbox" type="text" name="startid" 
	id="startid" size="25" maxlength="25" placeholder="from order id"
value="" />
</td>
<td>
 <input class="inputbox" type="text" name="endid" placeholder="to order id"
	id="endid" size="25" maxlength="25"
value="" />
</td>
<td>
 <input   <?php //if (empty($tcount)) echo ' style="display: none;" '; ?> type="submit" value="Export Order Items by Order Id" />
</td>
</tr>
<tr class="row1" style="margin-top: 10px;">
<td colspan="3">
<input type="hidden" name="export_eu_csv" value="0" id="export_eu_csv" />

 Export CSV (invoice id, order id, country_2_code, country_3_code, company, first name, last name, eu vat id, is_eu
 <br />
 <input type="submit" value="Export simple CSV" onclick="javascript: adminForm3.export_eu_csv.value=1" />
 <?php
 //SELECT distinct e.localid, o.order_id, c.country_2_code, u.country, u.company, u.first_name, u.last_name , u.vm_eu_vat FROM `jos_vm_orders` as o, jos_vm_order_user_info as u, jos_onepage_exported as e, jos_vm_country as c WHERE e.localid = o.order_id and u.order_id = o.order_id and u.address_type = 'BT' and c.country_3_code = u.country order by e.localid asc
 // is EU $eu = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'); 
 //SELECT distinct e.localid, o.order_id, c.country_2_code, u.country, u.company, u.first_name, u.last_name , u.vm_eu_vat FROM `jos_vm_orders` as o, jos_vm_order_user_info as u, jos_onepage_exported as e, jos_vm_country as c WHERE e.localid = o.order_id and u.order_id = o.order_id and u.address_type = 'BT' and c.country_3_code = u.country order by e.localid asc
 ?>
</td>
 </tr>
</table>
</form>
<?php
echo $pane->endPanel();

echo $pane->endPane();


function getRefOrders($order_id, $tree=false)
	{
	 if (defined('PARTNERS_INSTALLED') && (PARTNERS_INSTALLED=='0')) return "";
	 
	
	 
	 global $mainframe;

	 $db =& JFactory::getDBO();
	 $q = "SHOW TABLES LIKE '".$db->getPrefix()."partners_orders'";
	 $db->setQuery($q);
	 $r = $db->loadResult();
	 if (empty($r)) 
	 {
	 define('PARTNERS_INSTALLED', '0');
	 return "";
	 }
	   
	   
	 $q = "SELECT * FROM #__partners_orders where order_id = ".$order_id." order by id desc LIMIT 0,100";
	 $db->setQuery($q);
	 $res = $db->loadAssocList();
	 $msg = $db->getErrorMsg();
	 if (!defined('PARTNERS_INSTALLED'))
	 {
	 if (!empty($msg)) 
	 {
	  define('PARTNERS_INSTALLED', '0');
	  return "";
	 }
	 else 
	  define('PARTNERS_INSTALLED', '1');
	 }
	 
	 $orders = array();
	 
	 foreach($res as $val)
	 {
	  
	  $order_id = $val['order_id'];
	  $orders[$order_id] = array();
	  
	  $q = "select first_name, last_name from #__vm_order_user_info where order_id = '".$order_id."' and address_type = 'BT' limit 0,1 ";
	  $db->setQuery($q);
	  $row = $db->loadAssoc();
	  $orders[$order_id]['first_name'] = $row['first_name'];
	  $orders[$order_id]['last_name'] = $row['last_name'];
	  $orders[$order_id]['order_total'] = $val['order_total'];
	  $q = "select * from #__partners_ref where order_ref_id = '".$val['id']."' order by start asc";
	  $db->setQuery($q); 
	  $data = $db->loadAssocList();
	  foreach ($data as $k)
	  {
	   $arr = array();
	   
	   $arr['title'] = $k['title'];
	   $arr['url'] = $k['url'];
	   $arr['ref'] = $k['ref'];
	   if (!$tree)
	   {
	    if (empty($arr['ref'])) return '';
	    else
	    {
	     $ref = urldecode(urldecode($arr['ref']));
	     $p1 = strpos($ref, '//');
	     $p2 = strpos($ref, '/', $p1+3);
	     if ($p1 !== false && $p2 !== false)
	     return substr($ref, $p1+2, $p2-$p1-2);
	     else return $arr['ref'];
	    }
	   }
	   
	   
	   $start = $k['start'];
	   $end = $k['end'];
	   if (!empty($start) && (!empty($end)))
	   {
	    $time = $end-$start;
	    $time = number_format($time, 0).' sec ';
	    $arr['time'] = $time;
	   }
	   else $arr['time'] = 'Unknown';
	   if (empty($orders[$order_id]['ref']))
	   {
	    $orders[$order_id]['ref'] = array();
	    $orders[$order_id]['ref'][] = $arr;
	   }
	   else
	     $orders[$order_id]['ref'][] = $arr;
	  }
	 }
	 if (!$tree) return "";
	 return $orders;
	}
$processing_html = "<img id='status_img' src='/media/system/images/mootree_loader.gif' alt='' title='' />";
$error_html = "<img id='status_img' src='/administrator/components/com_media/images/remove.png' alt='' title='' />";
$created_html = "<img id='status_img' src='/images/M_images/pdf_button.png' alt='' title='' />";
$order_id = 0;
echo '<div id="debug_window" style="position: fixed; bottom: 0px; right: 0px; width: 30%; overflow:auto; height: 30%; background-color: transparent; color: black; font-size: 10px; text-align: right;"></div>';
echo '<script language="javascript" type="text/javascript">//<![CDATA[
		          		var opTimer = null;
		          		var opStop = false;
		          		var opTemplates = [];
						var focusedE = null;
						var timeOut = null;
						var tmpElement = null;
						var deb = document.getElementById("debug_window");							
						var op_params = "option=com_onepage&view=order_details&task=ajax&ajax=yes&order_number=0"; '."\n".'
						var op_url = "'.$this->ehelper->getUrl().'/administrator/index.php";
						var op_localid = null;
						var multiOrders = true;
							//]]></script>';


function printWrapper($field, $start = false)
	{
	 if ($start == true)
	 {
	  
	 }
	 else
	 {
	 $html = '<div id="buttons_'.$field.'" style="display: none; ">'
	       .'<input type="button" id="update_'.$field.'" value="Update" size="10" class="ipt" onclick="javascript:op_update(this);" />'
       			 .'<input type="button" id="cancel_'.$field.'" value="Cancel" size="10" class="ipt" onclick="javascript:op_cancel(this);" />'
	          			.'</div>';
	 return $html;
	 }
	}
	return '';




