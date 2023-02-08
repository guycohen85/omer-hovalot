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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JControllerOrders extends JControllerBase
{	
   function getViewName() 
	{ 
		return 'orders';		
	} 

   function getModelName() 
	{		
		return 'orders';
	}
	
	function installphpexcell()
	{
	   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
	   $data = OPCloader::fetchUrl('http://www.rupostel.com/gpl/phpexcell.zip'); 
	   if (!empty($data))
	    {
		   $zip = JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'.DS.'phpexcel.zip'; 
		   $dest = JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'; 
		   jimport( 'joomla.filesystem.file' );
		   jimport( 'joomla.filesystem.folder' );
		   jimport('joomla.filesystem.archive');
		   if (JFolder::create($dest) !== false)
		   if (JFile::write($zip, $data)!==false)
		   if (JArchive::extract($zip,$dest.DS)!==false)		   
			{
			   $msg = JText::_('COM_ONEPAGE_OK'); 
			}
		   
		   
		}
		if (empty($msg))
		$msg = 'Error'; 
		
		$data = JRequest::get('post');
    
     if (isset($data['limitstart'])) $limitstart=$data['limitstart'];
    else
     $limitstart  = JRequest::getVar('limitstart', 0, '', 'int');

    $mainframe = JFactory::getApplication(); 
     if (isset($data['limit'])) $limit=$data['limit'];
	 if (empty($limit))
   	  $limit   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 50, 'int');


    $link = 'index.php?option=com_onepage&view=orders&limitstart='.$limitstart.'&limit='.$limit;
    $this->setRedirect($link, $msg);
		
		
		
	   
	}
	
	function eexport()
	{
	 $startdate = JRequest::getVar('startdate', '');
	 $enddate = JRequest::getVar('enddate', '');  
	// $items = JRequest::getVar('items', ''); 
	
	 
	 $startid = JRequest::getVar('startid', ''); 
	 $endid = JRequest::getVar('endid', '');
	 
	 $this->setRedirect('index.php?option=com_onepage&view=order_excell&tmpl=component&startdate='.$startdate.'&enddate='.$enddate.'&items=yes&startid='.$startid.'&endid='.$endid);

	}
  function save()  // <-- edit, add, delete 
  {
	$mainframe = JFactory::getApplication(); 
	
    JRequest::setVar( 'view', '[ orders ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('orders');
    $reply = $model->save();
    if ($reply===true) {
    $msg = $_SESSION['msg'];
    } else { $msg = 'Error updating orders'; 
    }
    $data = JRequest::get('post');
    
     if (isset($data['limitstart'])) $limitstart=$data['limitstart'];
    else
     $limitstart  = JRequest::getVar('limitstart', 0, '', 'int');


     if (isset($data['limit'])) $limit=$data['limit'];
	 if (empty($limit))
   	  $limit   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 50, 'int');


    $link = 'index.php?option=com_onepage&view=orders&limitstart='.$limitstart.'&limit='.$limit;
    $this->setRedirect($link);
  }
  
  function runEucsv()
  {
    $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
	header('Content-Type: text/csv; charset=utf-8');
	header("Content-Disposition: attachment; filename=\"output.csv\";" );
	$eu = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'); 
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'opctracking.php'); 
	
	$array = array(); $order = new stdClass(); 
	$db = JFactory::getDBO(); 
	// all orders: 
	$q = 'select virtuemart_order_id from #__virtuemart_orders where 1 limit 999999'; 
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
	$header = false; 
	$ha = array(); 
	foreach ($res as $row)
	{
	 $order_id = $row['virtuemart_order_id']; 
	 $array = array(); $order = new stdClass(); 
	 //OPCtrackingHelper::getOrderVars(11, $array, $order); 
	 //var_dump($array); die(); 
	 OPCtrackingHelper::getOrderVars($order_id, $array, $order); 
	
	
	if (!$header)
	 {
	   foreach ($array as $k2=>$c2)
	    {
		  echo '"'.$k2.'",';
		  $ha[$k2] = ''; 
		}
		echo '"is_eu",'; 
		echo "\r\n"; 
		
		$ha['is_eu'] = ''; 
		$header = true; 
	 }
    //var_dump($ha); die(); 
	
	
	$ha2 = $ha; 
	foreach ($array as $k=>$c)
	 {
	 
	    //echo '"'.$c.'",'; //country_2_code
		if (array_key_exists($k, $ha))
		$ha2[$k] = '"'.$c.'",'; 
		
		if ($k == 'st_country_2_code')
		 {
		   if (in_array($c, $eu))
		   $ha2['is_eu'] =   '"X",'; 
		   else 
		   $ha2['is_eu'] = ','; 
		 }
		 
	 }
	 
	 foreach ($ha2 as $val) echo $val; 
	 
	 echo "\r\n"; 
	 }
	 die(); 
  }
  
   function xmlexport()
  {
  $eu = JRequest::getVar('export_eu_csv'); 
  if (!empty($eu))
  {
  
  $this->runEucsv(); 
  return;
  }
   $tid = JRequest::getInt('selected_tid'); 
   if (empty($tid)) return; 
   $startdate = JRequest::getVar('startdateo'); 
   $enddate = JRequest::getVar('enddateo'); 
   
   $startid = JRequest::getVar('startid'); 
   $endid = JRequest::getVar('endid'); 
   
   if (!empty($startdate)) $startdate = strtotime($startdate) == -1 ? '' :  strtotime($startdate);
	 if (!empty($enddate)) $enddate = strtotime($enddate) == -1 ? '' :  strtotime($enddate);
   
   $startdate = date("Y-m-d H:i:s", $startdate); 
  
   
if (!empty($startdate)) $where = ' where o.created_on >= "'.$startdate.'" ';
if (!empty($enddate)) 
{
$enddate =  $enddate+60*60*24-1;
$enddate = date("Y-m-d H:i:s", $enddate); 
   

if (!empty($where)) $where .= ' and ';
 else $where = ' where ';
$where .= ' o.created_on <= "'.$enddate.'" ';
}

$startid = JRequest::getVar('startid', ''); 
$endid = JRequest::getVar('endid', ''); 
if (!empty($startid)) $where = ' where o.virtuemart_order_id >= '.$startid.' ';
if (!empty($endid)) 
{
 if (!empty($where)) $where .= ' and ';
 else $where = ' where ';
 $where .= ' o.virtuemart_order_id <= "'.$endid.'" ';
}
   require_once ( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
   $ehelper = new OnepageTemplateHelper(); 
    
	$tt = $ehelper->getTemplate($tid);
	
   $q = 'select `virtuemart_order_id` from #__virtuemart_orders as o '.$where; 
   $db = JFactory::getDBO(); 
   $db->setQuery($q); 
   ob_start(); 
   $arr = $db->loadAssocList(); 
   $e=$db->getErrorMsg(); echo $e; 
   /*
   var_dump($q); 
   var_dump($arr); die(); 
   */
   foreach ($arr as $k=>$order_data)
   {
    $v = $order_data['virtuemart_order_id']; 
    $ra = $ehelper->getOrderDataEx($tid, $v);
    $localid = $v;
    $ehelper->processTxtTemplate($tid, $v, $ra);
   }
   $msg = ob_get_clean(); 
   $option = JRequest::getVar('option'); 
   $mainframe = JFactory::getApplication(); 
    $limit   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 50, 'int');
    $limitstart = $mainframe->getUserStateFromRequest("$option.limitstart", 'limitstart', 0, 'int');
	  
	  //var_dump($limit); var_dump($limitstart); die(); 

    $link = 'index.php?option=com_onepage&view=orders&limitstart='.$limitstart.'&limit='.$limit;
    $this->setRedirect($link);
   $this->setRedirect($link, $msg);
  }

}

