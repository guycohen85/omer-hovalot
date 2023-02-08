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

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

@ini_set("memory_limit","512M");
if (!file_exists(JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'.DS.'Classes'.DS.'PHPExcel.php')) die('Cannot find PHPExcel in '.JPATH_ROOT.DS.'libraries'.DS.'PHPExcel');
require_once ( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
require_once ( JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'.DS.'Classes'.DS.'PHPExcel.php');
require_once ( JPATH_ROOT.DS.'libraries'.DS.'PHPExcel'.DS.'Classes'.DS.'PHPExcel'.DS.'IOFactory.php');

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("RuposTel Systems")
							 ->setLastModifiedBy("RuposTel Systems")
							 ->setTitle("OPC Order Management")
							 ->setSubject("OPC Order Management")
							 ->setDescription("Order Management for VirtueMart")
							 ->setKeywords("orders, virtuemart, eshop")
							 ->setCategory("Orders");
$col = 0;
$row = 1;
$where = '';
$startdate = JRequest::getVar('startdate', '');
 
 
if (!empty($startdate)) $startdate = strtotime($startdate) == -1 ? '' :  strtotime($startdate);
$startdate = date("Y-m-d H:i:s", $startdate); 

$enddate = JRequest::getVar('enddate', '');
if (!empty($enddate)) $enddate = strtotime($enddate) == -1 ? '' :  strtotime($enddate);
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
 $where .= ' o.virtuemart_order_id <= '.$endid.' ';
}
$items = JRequest::getVar('items', ''); 
//echo $where ; die();
//if (empty($items))
$data =& $this->model->getOrderData($where);



if (empty($data)) 
{
 die('empty data!');
 $db =& JFactory::getDBO();
 echo $db->getErrorMsg();
}
else
{
while (@ob_end_clean() );
$keys = array_keys($data[0]);


foreach ($data[0] as $key=>$val)
{
 $objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow($col, $row, $key);
 $col++;
  
}
$row++;
$col=0;
foreach ($data as $k => &$value)
{
 foreach ($value as $k2 => $colval)
 {
 	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($col, $row, $colval);
    $col++;
            
 }
 unset($data[$k]);
 $row++;
 $col=0;
}
$objPHPExcel->getActiveSheet()->setTitle('Orders');
/*
$users =& $this->model->getList();
$keys = array_keys($users[0]);

foreach ($keys as $key)
{
 $objPHPExcel->setActiveSheetIndex(1)
            ->setCellValueByColumnAndRow($col, $row, $key);
 $col++;
  
}
$row++;
$col=0;
foreach($users as $kk => &$user)
{
 foreach ($user as $item)
 {
 $objPHPExcel->setActiveSheetIndex(1)
            ->setCellValueByColumnAndRow($col, $row, $item);
    $col++;
            
 }
 unset($users[$kk]);
 $row++;
 $col=0;
}

$objPHPExcel->getActiveSheet()->setTitle('Customers');

*/
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1);
unset($data); 
$data2 =& $this->model->getOrderDataWithItems($where);
$keys = array_keys($data2[0]);
$col = 0;
$row = 1;
foreach ($data2[0] as $key=>$val)
{
 $objPHPExcel->setActiveSheetIndex(1)
             ->setCellValueByColumnAndRow($col, $row, $key);
 $col++;
  
}
$row++;
$col=0;
foreach ($data2 as $k => $value)
{
 foreach ($value as $k2 => $colval)
 {
 	$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValueByColumnAndRow($col, $row, $colval);
    $col++;
            
 }
 unset($data2[$k]);
 $row++;
 $col=0;
}
$objPHPExcel->getActiveSheet()->setTitle('Order Items');

$objPHPExcel->setActiveSheetIndex(0);
unset($data2);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="export.xls"');
header('Cache-Control: max-age=0');


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output'); 

die();
}



?>