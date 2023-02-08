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

	require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php' );
	class JModelOrder_excell extends OPCModel
   {
    function __construct()
		{
			parent::__construct();
		
		}

    // vracia pole so vsetkymi beznymi hodnotami objednavky
    function getOrderData($where='')
    {
     $db = JFactory::getDBO();
     $q = 'select * from #__virtuemart_orders as o ';
	 $q .= ' left join (select * from #__virtuemart_order_histories order by virtuemart_order_history_id desc ) as h on o.virtuemart_order_id = h.virtuemart_order_id '; 
	 $q .= ' left join (select * from #__virtuemart_order_userinfos) as ui on (ui.virtuemart_order_id = o.virtuemart_order_id and ui.address_type="BT") ';
	 $q .= $where;
	 $q .= ' group by o.virtuemart_order_id order by o.created_on desc limit 99999 ';
	 $db->setQuery($q);
     $res = $db->loadAssocList();
     
     if (empty($res)) 
		{
		echo $q; 
 		echo $db->getErrorMsg();
 		die('No data !');
		}
     
     
     
     
	 return $res;
     echo $db->getErrorMsg();
     var_dump($res); die();
     if (empty($res)) return array();
     return $res;
     $data = array();
     $ehelper = new OnepageTemplateHelper;
     if (!empty($res))
     foreach ($res as $row)
     {
       //$data[] = $ehelper->getOrderData($row['order_id'], '');
     }
     
		
	 return $data;
    }

    // vracia pole so vsetkymi beznymi hodnotami objednavky
    function getOrderDataWithItems($where='')
    {
     $db = JFactory::getDBO();
     $q = 'select * from #__virtuemart_order_items as o '; 
	 //$q .= ' left join (select * from #__vm_order_history order by order_status_history_id desc limit 0,1) as h on o.order_id = h.order_id '; 
	 $q .= ' left join (select * from #__virtuemart_order_userinfos) as ui on (ui.virtuemart_order_id = o.virtuemart_order_id and ui.address_type="BT") ';
	 //$q .= ' left outer join #__vm_order_item as oi on (oi.order_id = o.order_id) ';
	 $q .= $where;
	 $q .= ' limit 99999 ';
	 $db->setQuery($q);
     $res = $db->loadAssocList();
     if (empty($res)) 
		{
  		 echo $db->getErrorMsg();
 		 die('No data !');
		}
     
     
     
     
	 return $res;
     echo $db->getErrorMsg();
     var_dump($res); die();
     if (empty($res)) return array();
     return $res;
     $data = array();
     $ehelper = new OnepageTemplateHelper;
     if (!empty($res))
     foreach ($res as $row)
     {
       //$data[] = $ehelper->getOrderData($row['order_id'], '');
     }
     
		
	 return $data;
    }

    
    
function getList()
{
$db =& JFactory::getDBO();

$list  = "SELECT * FROM #__users AS u LEFT JOIN jos_vm_user_info AS ui ON u.id=ui.user_id"
  ." WHERE "
  ." ui.perms = 'shopper' "
  ." ORDER BY user_id ASC ";

$db->setQuery($list);
//echo $list;
$arr = $db->loadAssocList();
$users = array();
foreach ($arr as $row)
{
  $user = array();
  foreach ($row as $key=>$val)
  {
   $user[$key] = $val;
  } 
 $users[] = $user;
 
}
//var_dump($users);
//die();
//$i = 0;
return $users;


}

    
}