<?php
/**
* @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
* @package		Joomla.Administrator
* @subpackage	com_cache
* @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

/**
* Cache Model
*
* @package		Joomla.Administrator
* @subpackage	com_cache
* @since		1.6
*/
class JModelUtils extends OPCModel
{
	function __construct() {
		parent::__construct();

	}
	
	function searchtext()
	{
	  jimport('joomla.filesystem.file');
	  jimport('joomla.filesystem.folder');
	  require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'cache.php'); 
	  
	  $search = JRequest::getVar('searchwhat', '', 'post','string', JREQUEST_ALLOWRAW);
	  if (empty($search)) return ''; 
	  $searchext = JRequest::getVar('ext'); 
	  if (empty($searchext)) return ''; 
	  
	  if ($searchext == '*') $searchext = '.'; 
	  else $searchext = '.'.$searchext; 
	  $xc = JRequest::getVar('excludecache', false); 
	   $cs = JRequest::getVar('casesensitive', false); 
	  
	  $ftest = OPCcache::getValue('opcsearch'.$searchext); 
	  
	  if (empty($ftest))
	  $files = JFolder::files(JPATH_SITE, $searchext, true, true); 
	  else $files = $ftest; 
	  
	  OPCcache::store($files, 'opcsearch'.$searchext); 
	  
	  $os = JRequest::getVar('onlysmall', false); 
	  
	  $resa = array(); 
	  foreach ($files as $f)
	  {
	     // exclude cache: 
		 if ($xc)
	     if (stripos($f, 'cache')!== false) continue; 
		 
		 if ($os)
		 if (filesize($f)>500000) continue; 
		 
		 $data = file_get_contents($f); 
		 if (!$cs)
		 {
		 if (stripos($f, $search)!==false)
		 $resa[] = $f; 
		 }
		 else
		 {
		  if (strpos($f, $search)!==false)
		  $resa[] = $f; 
		  
		 }
		 
		 
		 
	  }
	  
	  $ret = implode($resa, "<br />\n"); 
	  return $ret; 
	  
	  
	  
	  
	}
	
	function getCats()
	{
		if (!class_exists('VmConfig'))
		require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		
		VmConfig::loadConfig(true); 
		$langs = VmConfig::get('active_languages', array('en-GB')); 
		
		foreach ($langs as $lang)
		{
			$lang = str_replace('-', '_', $lang); 
			$lang = strtolower($lang); 
			$db = JFactory::getDBO(); 
			


			$vendorId = 1;

			$select = ' c.`virtuemart_category_id`, l.`category_description`, l.`category_name`, c.`ordering`, c.`published`, cx.`category_child_id`, cx.`category_parent_id`, c.`shared` ';

			$joinedTables = ' FROM `#__virtuemart_categories_'.$lang.'` l
				JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)
				LEFT JOIN `#__virtuemart_category_categories` AS cx
				ON l.`virtuemart_category_id` = cx.`category_child_id` ';

			$where = array();
			$where[] = " c.`published` = 1 ";
			$where[] = ' (c.`virtuemart_vendor_id` = "'. (int)$vendorId. '" OR c.`shared` = "1") ';
			$whereString = '';
			if (count($where) > 0){
				$whereString = ' WHERE '.implode(' AND ', $where) ;
			} else {
				$whereString = 'WHERE 1 ';
			}
			$orderBy = ''; 
			$groupBy = ''; 
			$filter_order_Dir = ''; 
			$joinedTables .= $whereString .$groupBy .$orderBy .$filter_order_Dir ;
			$q = 'SELECT '.$select.$joinedTables;
			$db->setQuery($q);
			$res = $db->loadAssocList(); 
			
			$mycats = $this->sortArray($res, 'virtuemart_category_id', 'category_parent_id', 0, false); 
			unset($mycats[0]); 
			foreach ($mycats as $key=>$val)
			 {
			   if (!isset($val['lft'])) { var_dump($val); die('138: empty lft for id '.$key); }
			   if (!isset($val['rgt'])) { var_dump($val); die('139: empty rgt for id'.$key); }
			 }
			

			
			$this->checkConsitency($mycats);
			// second round: 
			if (!$this->checkConsitency($mycats))
			 {
			   die('too many non existing categories'); 
			 }
			 $this->clearLftRgt($mycats);
//			var_dump($mycats[5]); die(); 
			$mycats = $this->sortArray($mycats, 'virtuemart_category_id', 'category_parent_id', 0, true); 
			unset($mycats[0]); 
			foreach ($mycats as $key=>$val)
			 {
			   if (!isset($val['lft'])) { var_dump($val); die('148: empty lft for id '.$key); }
			   if (!isset($val['rgt'])) { var_dump($val); die('148: empty rgt for id'.$key); }
			 }
			
			//var_dump($mycats[2]); 
			//var_dump($mycats[3]); 
			
			$cats[$lang] =  $mycats; 
			

		}
				
		$this->checkOdering($cats); 

		return $cats;
		

	}
	function checkConsitency(&$mycats, &$round=0)
	{
	   $problems = array(); 
	   foreach ($mycats as $key=>$val)
	     {
		   if (!empty($key))
		   if (!isset($val['virtuemart_category_id']))
		    {
			  //unset($mycats[$key]); continue; 
			  //var_dump($val); 
			  echo '168:category does not exists: '.$key."<br />\n"; 
			  echo 'new parent: '.var_export($val['category_parent_id'], true);
			  $problems[$key] = $key; 
			  if (!empty($val['category_parent_id'])) $newparent = $val['category_parent_id']; 
			  else $newparent = 0; 
			  
			  $mycats[$key]['category_parent_id'] = $newparent; 
			  foreach ($val as $nk =>$val2)
			    {
				   if (is_array($val2))
				   $this->merge($mycats[$newparent][$nk], $val2); 
				   else
				   {
				     if (!isset($mycats[$newparent][$nk]))
					 $mycats[$newparent][$nk] = $val2; 
				   }
				}
			  
			}
			
			if (!empty($val['category_parent_id']))
			if (!isset($mycats[$val['category_parent_id']]))
			{
			   echo 'missing parent: '.$val['category_parent_id']; 
			  //die('missing parent'); 
			   $mycats[$key]['category_parent_id'] = 0; 
			}
			
			
			
		 }
		 
		 foreach ($problems as $key=>$val)
		 {
		   unset($mycats[$key]); 
		 }
		 return empty($problems); 
	  	   
	  
	}
	static $cats; 
	function movemenu()
	{
	   
	   $cats = $this->getCats(); 
	   
	    
	   $lang = JRequest::getVar('vm_lang', 'en_gb'); 
	   $vmmenu = JRequest::getVar('vm_menu_'.$lang); 
	   $jmenu = JRequest::getVar('selected_menu'); 
	   $tomenu = JRequest::getVar('menu_'.$jmenu); 
	   $tolanguage = JRequest::getVar('tojlanguage', '*'); 
	   $config = array('vm_lang'=>$lang, 'vm_menu_'.$lang=>$vmmenu, 'selected_menu'=>$jmenu, 'menu_'.$jmenu=>$tomenu, 'tojlanguage'=>$tolanguage); 		
	   $session = JFactory::getSession(); 
	   $config = $session->set('opc_utils', $config); 

		
		if (empty($vmmenu))
		$copy = $cats[$lang]; 
		else $copy = $cats[$lang][$vmmenu]['children']; 
		
		//var_dump($copy); die(); 
		
	    $this->checkMinMax($copy); 
		
		$this->shiftLftRgt($cats[$lang], $copy); 

		
		
		
	    
		// pre-cache: 
		self::$cats = $cats[$lang]; 
		
		//JTable::addIncludePath(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'); 
		//JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'tables'); 
		//$table = JTable::getInstance('Menu', 'MenusTable', array());
		//$data = $table->load(300);
		
		
		
		$vmcid = $this->getVmComponentId(); 
		
		$count = 0; 
		//$this->sortforinsert($copy); 
		//$tomenulevel = $this->getToMenuLevel($tomenu); 
		
		$this->copyTable('menu', 'menu_working'); 
		$menu = $this->getWholeTable('menu'); 


		//$this->removeDeleted($menu); 
		
		
		$this->recalculate($menu, $copy, $jmenu, $tomenu, $vmmenu, $lang, $vmcid); 
		
		
		//debug_zval_dump($menu); 
		
		//die('menu'); 
		$this->checkDuplicities($menu);

	    
		$q = $this->createQuery($menu, 'menu_working'); 
		
		//debug_zval_dump($q); die(); 
		$this->flushTable('menu_working'); 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) { echo $e; die(); }
		$this->backupTable('menu'); 
		$this->copyTable('menu_working', 'menu'); 
		return; 
		
	
	}
	static $count; 
	function removeEntries(&$menu, $lft, $rgt)
	{
	  // requires first left to be 0
	  // calculate number of items being removed
	  // not including the currnet one: 
	  $diff = (($rgt-1)-$lft)/2;
	  //including the current one: 
	  
	  if ($diff<0) { return; } //die('ee'); 
	  foreach ($menu as &$item)
	   {
	     if (!isset($item['delete']))
	     if (($item['lft']>=$lft) && ($item['rgt']<=$rgt))
		   {
		     $item['delete'] = 1; 
			 self::$count++; 
			 echo self::$count."<br />\n"; 
		   }
		   
		   if (($item['lft']>$lft))
		   {
		     $item['lft'] -= $lft; //$diff; 
			 $item['rgt'] -= $lft;  // $diff;  
		   }
		   
		   if (($item['rgt']>$rgt)) 
		   $item['rgt'] -= $lft; //$diff; 
		   
	   }
	   
	   
		
	}
	function removeDeleted(&$menu)
	{
	   $this->checkDuplicities($menu, 'before delete'); 
	   $found = false; 
	   // in this round we mark it as deleted
	   foreach ($menu as $key=>&$item)
	   {
	     if (!isset($item['delete']))
	     if ($item['published']<0)
		   {
		     $left = $item['lft']; 
			 $right = $item['rgt']; 
			 $diff = $right-$left; 
			 if ($diff<0) 
			 {
			 //var_dump($item); 
			 //die('ee diff smaller zero'); 
			 }
			 
			 $this->removeEntries($menu, $left, $right); 
			 $found = true; 
		   }
	   }
	   if (!$found) return;
	   
	   //debug_zval_dump($menu); die(); 
	   // in this round we unset it
	   foreach ($menu as $key2=>$item2)
	    {
		  if (!empty($item2['delete'])) 
		  {
		  //echo $key2; die('delete'); 
		  unset($menu[$key2]); 
		  }
		}
		
	   echo count($menu); 
	 
	   $this->checkDuplicities($menu, 'from delete'); 
	}
	
	function tryAlias(&$menu, &$myitem, &$count)
	{
	   $arr = array();
	   foreach ($menu as $key=>&$item)
	    {
		   //if (!($myitem['id']==$item['id'])) continue; 
		   
		   $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
		   if (isset($arr[$str]))
		   {
		     $count++; 
			 $item['alias'] = $item['alias'].'-'.$count; 
		     $this->tryAlias($menu, $item, $count); 
			 $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
		     
		   }
		   
		   $arr[$str] = $key; 
		   
		}
		
		
	}
	function checkDuplicities(&$menu, $msg='')
	{
	 echo "<br />\n".$msg."<br />\n"; 
	  // joomla defines duplicity as client_id, parent_id, alias, language
	  $arr = array(); 
	  foreach ($menu as $key=>&$item)
	   {
	     $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
	     if (isset($arr[$str]))
		 {
		   
		   $count = 1; 
		   $item['alias'] = $item['alias'].'-'.$count; 
		   $this->tryAlias($menu, $item, $count); 
		   $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
		 }

		 $arr[$str] = $key; 
	   }
	  
	  $test = array(); 
	  $lftrgt = array(); 
	  
	  foreach ($menu as $m1)
	  {
	    //$menu[$m1['parent_id']] = $m1['rgt']; 
		//foreach ($menu as $m2)
		{
		// if (empty($m1['parent_id'])) continue; 
		
		 
		 
		 // skip for root
		 if (!empty($m1['parent_id']))
		 {
		  $left = $m1['lft']; 
		  $right = $menu[$m1['parent_id']]['rgt']; 
		  if ($right <= $left)
		   {
		     
			 $msg = "<br />\n".'parent id '.$m1['parent_id']."<br />\n"; 
			 $msg .= ' right for parent: '.$right."<br />\n"; 
			 $msg .= ' left for item: '.$left." right for item ".$m1['rgt']." <br />\n"; 
			 $msg .= ' for item id '.$m1['id']."<br />\n"; 
			 $msg .= ' error consistency right smaller left';
			 echo 'item:'; 
			 var_dump($m1);
			 echo 'parent:'; 
			 var_dump($menu[$m1['parent_id']]); 
			 
		     die('error consistency right smaller left'.$msg); 
		   }
		   }
		if (!isset($lftrgt[$m1['lft']]))
		{
		 $lftrgt[$m1['lft']] = $m1['id']; 
		 
		 //if ($m1['lft']===0) die('ok'); 
		}
		else
		{
		  echo 'id '.$m1['id'].' shares the same left with '.$lftrgt[$m1['lft']]."<br />\n"; 
		  
		  debug_zval_dump($menu); 
		  die('shares...'); 
		}
		if (!isset($lftrgt[$m1['rgt']]))
		$lftrgt[$m1['rgt']] = $m1['id'];
		else
		{
		echo '433:id '.$m1['id'].' shares the same right with '.$lftrgt[$m1['rgt']]."<br />\n"; 
		
		echo ' count '.count($menu); 
		//debug_zval_dump($menu); 
		die('shares the same right with.'); 
		}
		
		}
	  
	  }
	  //var_dump($menu[1]); 
	  // -1 because we start from 0
	  $c = (count($menu)*2)-1; 
	  for ($i=0; $i<$c; $i++)
	   {
	      if (!isset($lftrgt[$i]))
		   {
		     echo '484: missing value for left or right on position '.$i."<br />\n"; 
			 echo '485: count: '.count($menu); 
			 echo '486: before: '; 
			 var_dump($menu[$lftrgt[$i-1]]);
			 echo '488: next: '; 
			 if (!isset($menu[$lftrgt[$i+1]])) var_dump($menu[1]); 
			 var_dump($menu[$lftrgt[$i+1]]);
			 var_dump($menu[$lftrgt[$i+2]]);
			 //var_dump($menu[685]); 
			 die('493: missing'); 
			 
			 
		   }
	   }
	   
	}
	
	function shiftLftRgt(&$orig, &$copy)
	{
	  
	  $ca = count($orig); 
	  $cc = count($copy); 
	  $diff = $ca - $cc - 1; 
	  
	  
	  
	  
	  foreach ($copy as $key=>$item)
	   {
	     
	     if (!isset($item['virtuemart_category_id'])) continue; 
		 
	     $copy[$key]['lft'] = $item['lft'] - $diff; 
		 $copy[$key]['rgt'] = $item['rgt'] - $diff; 
		 if ((!isset($smallest_level)) || (((int)$item['level']<=$smallest_level)))
		 {
		 
		 $smallest_level = $item['level']; 
		 }
	   }
	   
	   // if not zero, we need to recalculate level as well: 
	   if (!empty($smallest_level))
	   foreach ($copy as $key=>$item)
	   {
	     $copy[$key]['level'] = (int)$copy[$key]['level'] - $smallest_level; 
	   }
	   
	   
	   
	   
	   
	   
	   
	   
	}
	function backupTable($table)
	{
	  
	  $this->copyTable($table, $table.'_backup'); 
	  return; 
	   if ($this->tableExists($table.'_backup'))
	   {
	     //$this->flushTable($table.'_backup'); 
		 $this->copyTable($table, $table.'_backup'); 
	   }
	  
	}
	function createQuery(&$menu, $table)
	{
	  
	  
	  $keys = $this->toKeys($menu);
	  $q = 'insert into `#__'.$table.'` ('.$keys.') values '."\n";  	
	  
	  $qi = ''; 
	  
	  foreach ($menu as &$val)
	  {
	  
	  $vals = $this->toVal($val); 
	  if (!empty($qi)) $qi .= ', '; 
	  $qi .= '('.$vals.')'."\n"; 
	  
	  }
	  $q .= $qi; 
	  
	  return $q; 
	
	}
	
	function toVal(&$val)
	{
	  $db = JFactory::getDBO(); 
	  
	  $q = ''; 
	  $nm = array('id', 'published', 'parent_id', 'level', 'component_id', 'ordering', 'checked_out', 'browserNav', 'access', 'template_style_id', 'lft', 'rgt', 'home', 'client_id'); 
	  foreach ($val as $key=>$value)
	    {
		  if (!empty($q))
		  $q .= ", ";
		  if (in_array($key, $nm)) $q .= $value; 
		  else
		  $q .= "'".$db->getEscaped($value)."'"; 
		  
		}
		return $q;
	
	}
	function toKeys(&$menu)
	{
	  $first = reset($menu); 
	  $q = ''; 
	  foreach ($first as $key=>$val)
	    {
		  if (!empty($q))
		  $q .= ', '.$key; 
		  else
		  $q .= $key; 
		}
		return $q; 
	}
	function insertTo($menu, $lft, $count)
	{
	  
	}
	//$this->recalculate($menu, $copy, $jmenu, $tomenu, $vmmenu); 
	function recalculate(&$menu, &$vmmenu, $jmenu, $tomenu, $vmmenu2, $lang, $vmcid)
	{
	 	//var_dump($vmmenu); die(); 
		$a = reset($vmmenu); 
		if ($a['lft'] == 2)
		$minus = 1; 
		else 
		$minus = 0; 
		
		
	   $found = false; 
	   $largest_left = 0;
	   $largest_right_for_largest_left = 0; 
       $largest_left_to = 0;
 	   
	   $startlevel = 1; 
	   $largest_id = 0; 
	   
	   foreach ($menu as $i)
	     {
		   // will get autoincrement from largest ID
		   if ($i['id']>$largest_id)
		   $largest_id = $i['id']; 
		   
		   if ($i['menutype'] == $jmenu)
		    {
			  $found_menu = true; 
			  if ($i['id'] == $tomenu)
			  {
			  $found = true; 
			  $startlevel = $i['level']+1; 
			  $largest_left_to = $i['lft'];
			  $found_right_to = $i['rgt']; 
			  $found_left_to = $i['lft']; 
			  }
			  if (!$found)
			  if (($i['lft']>=$largest_left_to) && ($startlevel<=$i['level']))
			  {
			  $largest_left_to = $i['lft'];
			  $right_to = $i['rgt']; 
			   
			  }
			  
			}
			if ($i['lft']>=$largest_left)
			 {
			 
			   $largest_left = $i['lft'];
			   if (!$found)
			   $right_to = $i['rgt'];
			   $largest_right_for_largest_left = $i['rgt']; 
			   $lid = $i['id']; 
			   
			   
			 }
		 }
		
	    $diff = count($vmmenu); 
		if (!$found)
		{
		  //$largest_left_to = $largest_right_for_largest_left
		}
		// original 
		//var_dump($largest_left_to); die(); 
		//var_dump($menu[550]); 
		//var_dump($menu[1]); 
		//var_dump($largest_right_for_largest_left); die(); 
		/*
		http://www.evanpetersen.com/item/nested-sets.html
		Deleting a node with children

		You will remove the node and promote all immediate children to be direct 
		descendants of the parent node of the node you are removing

		Decrement all left and right values by 1 if left value is greater than node 
		to delete’s left value and right value is less than node to delete’s right
		Decrement all left values by 2 if left value is greater than node to delete’s right value.
		Decrement all right values by 2 if right value is greater than node to delete’s right value.
		Remove node
		*/
		
		//var_dump($diff); die(); 
		//var_dump($menu[579]);
		//var_dump($menu[$tomenu]); die();
		
		// $largest_left_to = $i['lft'];
		// $found_right_to = $i['rgt']; 
		if (isset($found_right_to) && (isset($largest_left_to)))
		{
		  //check if we already have some items in the menu to which we are inserting
		  $count = ((($found_right_to-1)-$largest_left_to)/2); 
		}
		echo '697: menu1 rgt: '; var_dump($menu[1]['rgt']); 
		echo "<br />\n"; 
		echo 'count: '.$diff; 
		echo "<br />\n"; 
		echo 'largest left: '; var_dump($largest_left); 
		echo "<br />\n"; 
		echo 'largest right for largest left: '; var_dump($largest_right_for_largest_left); //die(); 
		echo "<br />\n"; 
		$sb = ($diff*2)+$menu[1]['rgt']; 
		echo 'largest right should be: '.$sb."<br />\n";
		
		foreach ($menu as &$m)
		{
		
		//var_dump($menu[1]); die(); 
		  if (false)
		  if ($m['rgt']>$largest_right_for_largest_left)
		  {
		  
		  // from 337 has to be 355, count 9, diff 18
		  // from 337 has to be 339, count 1, diff 2
		  // from 337, has to be 341,count 2, diff 4
		  // from 337, has to be 343, count 3, diff 6
		  // from 337, has to be 345, count 4, diff 8
		  // from 337, has to be 347, count 5, diff 10
		  //$diff * 2
		  $df = ($diff*2);
		  //var_dump($diff); 
		  //var_dump($m); 
		  // +2 because we are addding one to left and one to right, later
		  $up = $m['rgt'] + $df; 
		  //echo $m['id'].' is larger rgt: '.$m['rgt'].' updating to: '.$up."<br />\n"; 
		  $m['rgt'] = $up; 
		  
		  //die('hhh'); 
		  }
		  
		  // only if found: $largest_left_to
		  if (!empty($largest_left_to))
		   {
		   
		     if ($m['lft']>$largest_left_to)
			  {
			    $m['lft']+=$diff; 
			  }
			  //if ($m['rgt']>=$right_to)
			  //if (isset($right_to))
			  
			  if ($m['rgt']>=$largest_left_to)
			  {
			  
			    //$largest_left_to = $i['lft'];
				// $right_to = $i['rgt']; 
				//$count = ((($right_to-1)-$largest_left_to)/2); 
				$rgt = (($diff * 2)+$largest_left_to+1); 
				$m['rgt']+=$diff*2;
				/*
				echo 'right:'; 
				echo $found_right_to; 
				var_dump($rgt); 
				echo 'rgt to:'; 
			    var_dump($right_to); 
				echo 'curr:'; 
			    var_dump($m['rgt']); 
			     
				echo 'after: ';
				var_dump($m['rgt']); 
				echo 'largest left to: '; 
				var_dump($largest_left_to); 
				*/
			  }
		   }
		   else
		   {
		     // if not found: 
			 //it's the latest largest left
			 if (($m['lft']>$largest_left) && ($m['rgt']>=$largest_left))
			 {
			   $m['rgt']+=$diff*2;
			   //$largest_left
			 }
			 else
			 if ($m['rgt']>$largest_right_for_largest_left)
			 {
			   $m['rgt']+=$diff*2;
			 }
		   }
		}
		
	//var_dump($menu[1]); die(); 
	   if (!empty($found_menu))
	   {
	     $largest_left = $largest_left_to; 
		 
	   }
	   
	     {
		 
		 /*
		 foreach ($vmmenu as $ii)
		 {
		   if (isset($ii['lft']))
		   echo 'id: '.$ii['virtuemart_category_id'].' '.$ii['lft'].' '.$ii['rgt']."<br />\n"; 
		 }
		 */
		  $this->checkMinMax($vmmenu); 
		    foreach ($vmmenu as &$item)
			 {
			 
			   if (!isset($item['virtuemart_category_id'])) continue; 
			   
			  //var_dump($largest_left); die(); 
				// tu je problem: 
				$i = var_export($item, true); 
			   $item['lft'] += $largest_left; 
			   $item['rgt'] += $largest_left; 
			   $item['level'] += $startlevel; 
			   
			   if (($item['lft']>$sb) || ($item['rgt']>$sb))
			    {
				  echo ' before: '.$i.' after: '; 
				  var_dump($item); 
				  die('error - lft or rgt values are incorrect for vm menu'); 
				}
			   if ($item['level']>10)
			   {
			     
			   }
			   
			   $item['id_indexed'] += $largest_id+1; 
			   $arr = $this->converToMenu($vmmenu, $lang, $item, $jmenu, $tomenu, $largest_id, $vmcid, $found);
			   
			   $menu[$item['id_indexed']] = $arr; 
			   
			  
			  
			 }
			 /*
			 foreach ($vmmenu as $ii22)
		 {
		   if (isset($ii22['lft']))
		   echo 'id: '.$ii22['virtuemart_category_id'].' '.$ii22['lft'].' '.$ii22['rgt']."<br />\n"; 
		 }
		 */
			 
			 
			//var_dump($menu[1581]); die(); 
		 }
		 
		 
		 return true; 
		 
		 
		 
	}
	
	function checkMinMax($arr, $parent_id='parent_id_indexed', $id='id_indexed')
	{
	 $lftrgt = array(); 
	// var_dump($arr); die(); 
	  $max_left = $max_right = 0; 
	 $min_left = null;
	   foreach ($arr as $kj=>$m1)
	  {
	   if (empty($m1['virtuemart_category_id'])) continue;
	   
	   if ($m1['lft']>=$max_left) { $max_left = $m1['lft']; $mile = $m1; }
	   if (!isset($min_left)) { $min_left = $m1['lft']; $mire = $m1; }
	   if ($m1['lft']<=$min_left) { $min_left = $m1['lft']; $mle = $m1; }
	   if ($m1['rgt']>=$max_right) { $max_right = $m1['rgt']; $mre = $m1; }
	   
	    //$menu[$m1['parent_id']] = $m1['rgt']; 
		//foreach ($menu as $m2)
		/*
		{
		 if (empty($m1['parent_id'])) continue; 
		 $left = $m1['lft']; 
		 $right = $arr[$m1['parent_id']]['rgt']; 
		 if ($right <= $left)
		   {
		     
			 $msg = "<br />\n".'parent id '.$m1['parent_id']."<br />\n"; 
			 $msg .= ' right for parent: '.$right."<br />\n"; 
			 $msg .= ' left for item: '.$left." right for item ".$m1['rgt']." <br />\n"; 
			 $msg .= ' for item id '.$m1['id']."<br />\n"; 
			 echo 'item:'; 
			 var_dump($m1);
			 echo 'parent:'; 
			 var_dump($arr[$m1['parent_id']]); 
			 
		     die('error consistency right smaller left'.$msg); 
			 
		   }
		}
		*/
		if (!isset($lftrgt[$m1['lft']]))
		{
		 $lftrgt[$m1['lft']] = $kj; //$m1[$id]; 
		}
		else
		{
		  echo 'id '.$m1[$id].' shares the same left with '.$lftrgt[$m1['lft']]."<br />\n"; 
		  die(); 
		}
		if (!isset($lftrgt[$m1['rgt']]))
		{
		
		if (empty($m1['rgt'])) { echo 'empty rgt: '; var_dump($m1); die('empty rgt'); }
		
		$lftrgt[$m1['rgt']] = $kj; //$m1[$id];
		
		}
		else
		{
		echo '870: id '.$m1[$id].' shares the same right of value '.$lftrgt[$m1['rgt']]."<br />\n"; 
		var_dump($m1); 
		echo 'id '.$lftrgt[$m1['rgt']].':'; 
		var_dump($arr[$lftrgt[$m1['rgt']]]); 
		die('id shares..'); 
		}
	}
	
		
		// -1 because we start from 0
	  
	  //$starta = reset($arr); 
	  $start = $min_left;
	  
	  
	  $c = (count($arr)*2)-3+$start; 
	  //$c = (count($arr)*2)-4+$start; 
	  
	  if ($c != $max_right)
	  {
	  echo '835:max_right should be '.$c."<br />\n"; 
	  echo '836:max_right is: '.$max_right."<br />\n"; ; 
	 
	  
	  }
	  echo '838:count is: '.count($arr)."<br />\n"; ; 
	  echo '839:first_left is: '.$min_left."<br />\n"; ; 
	  
	  for ($i=$start; $i<=$c; $i++)
	   {
	      if (!isset($lftrgt[$i]))
		   {
		   
		     echo ' 1:missing value for left or right on position '.$i."<br />\n";
					//echo 'el: ';
					//var_dump($arr[$lftrgt[$i]]);
					echo '924:before: '; 
					var_dump($arr[$lftrgt[$i-1]]);
				    echo '926:after: '; 
					var_dump($arr[$lftrgt[$i+1]]); 
					
					 echo '947:max right is element: '; 
					var_dump($mre); 
					
			 die('1'); 
		   }
	   }
	   if (false)
	   if (($max_right-$min_left) > ((count($arr)*2)-$min_left))
		{
		  echo '953: count: '.count($arr); 
		  echo '954: max_right: '.$max_right."<br />\n"; 
		  echo '955: min_left: '.$min_left."<br />\n"; ; 
		  echo '956: max_left: '.$max_left."<br />\n"; ; 
		  die('957: max right is not correct'); 
		}
	   
	}
	
	function getImage($catId)
	{
		$db = JFactory::getDBO();  
		$q = "select m.file_title,m.file_url
			from `#__virtuemart_medias` as m 
			INNER JOIN #__virtuemart_category_medias as cat 
			ON m.virtuemart_media_id = cat.virtuemart_media_id 
			WHERE cat.virtuemart_category_id = '".$db->getEscaped($catId)."'limit 1"; 
					
		$db->setQuery($q); 
		$arr = $db->loadAssocList();
		$image = '';
		if ($arr[0]['file_url']){
			$path =  DS . "images" . DS ."com_opc_util_menu" .DS."19_19";
			$abspath = JPATH_SITE.$path;
			jimport( 'joomla.filesystem.file' );
			
			if ( !JFolder::exists($abspath) ) 	JFolder::create($abspath);
		
			$filename = JFile::makeSafe($arr[0]['file_title']);
			$filepath = $abspath . DS. $filename;
		
			$origFile = JPATH_ROOT. DS .$arr[0]['file_url'];
					
			list($width, $height, $type, $attr) = getimagesize($origFile);
		// resize image and copy to $filepath
			OPCimage::resizeImg($origFile, $filepath, 19, 19, $width, $height); 
					  
			$image = $path.DS.$filename ; 
	   } 
	   return $image;
	}
	
	function &converToMenu(&$cats, $lang, &$vmitem, $jmenu, $tomenu, $largest_id, $vmcid, $found)
	{
	 
			$arr = array(); 
			$key = $vmitem['virtuemart_category_id']; 
			$arr['id'] = (int)$vmitem['id_indexed']; 
			$arr['menutype'] = $jmenu; 
			$arr['title'] = $vmitem['category_name']; 
			$arr['alias'] = $this->getAlias($vmitem);
			$arr['note'] = 'virtuemart_category_id:'.$vmitem['virtuemart_category_id'];
			$arr['path'] = $this->getSefPath(self::$cats, $key, '', $arr['alias']); 
			$arr['link'] = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$key; 
			$arr['type'] = 'component'; 
			$arr['published'] = $vmitem['published']; 
			
			if (empty($vmitem['category_parent_id'])) 
			{
			// if the parent is top category, check if we have another top here: 
			if ($found)
			$arr['parent_id'] = (int)$tomenu; 
			else
			$arr['parent_id'] = 1; //$vmitem['id_indexed']; 
			}
			else
			{
			 // if the parent is outisde our scope, check if we have another top here: 
			 if (!isset($cats[$vmitem['category_parent_id']]['id_indexed']))
			 $arr['parent_id'] = (int)$tomenu; 
			 else
			 $arr['parent_id'] = $cats[$vmitem['category_parent_id']]['id_indexed'];//  $vmitem['parent_id_indexed']; // $this->getParent($vmmenu, $vmitem, $tomenu, $jmenu); 
			}
			
			
			$arr['level'] = $vmitem['level'];
			$arr['component_id'] = $vmcid; 
			
			$arr['ordering'] = '0'; //$vmitem['ordering']; 
			$arr['checked_out'] = '0'; 
			$arr['checked_out_time'] = '0000-00-00 00:00:00'; 
			$arr['browserNav'] = 0; 
			$arr['access'] = 1; 
			$arr['img'] = $this->getImage($key); 
			$arr['template_style_id'] = '0'; 
			$arr['params'] = '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}'; 
			$arr['lft'] = $vmitem['lft'];
			$arr['rgt'] = $vmitem['rgt'];
			$arr['home'] = 0; 
			$l = JRequest::getVar('tojlanguage', '*'); 
			$arr['language'] = $l; 
			$arr['client_id'] = 0; 
			
			if ($arr['level']>10)
			  {
			    var_dump($arr); die('level 10'); 
			  }
		return $arr; 
	}
	
	function &getWholeTable($table)
	{
	   $db = JFactory::getDBO(); 
	   $q = 'select * from `#__'.$table.'` where 1 limit 99999'; 
	   $db->setQuery($q); 
	   $arr = $db->loadAssocList(); 
	   $newa = array(); 
	   foreach ($arr as $key=>$val)
	    {
		  $newa[$val['id']] = $val; 
		}

		return $newa; 
	   
	   

	}
	
	
	function getToMenuLevel($Itemid)
	{
	  $db=JFactory::getDBO(); 
	  $q = 'select level from #__menu where id = "'.$Itemid.'" limit 0,1'; 
	  $db->setQuery($q); 
	  return $db->loadResult();
	}
	function sortforinsert(&$items)
	{
	  $copy = array(); 
	  // sort by level
	  $levels = array(); 
	  foreach ($items as $key=>$item)
	   {
	     $items[$key]['alias'] = $this->getAlias($items[$key]); 
	     $path = $this->getSefPath($items, $key, $vmmenu='', $items[$key]['alias']); 
		 $arr = explode('/', $path); 
		 //without root: 
		 $level = count($arr)-1; 
		 $items[$key]['level'] = $level; 
		 $levels[$level][$key] = $key; 
	   }
	   ksort($levels); 
	   foreach ($levels as $val)
	   foreach ($val as $cat_id=>$id)
	    $copy[$cat_id] = $items[$cat_id]; 
	   
	   $items = $copy; 
	}
	
	function checkOdering(&$items, $debug=false)
	 {
	    $parents = array();
		
		// group by parents and ordering: 
		if (empty($items)) return;
	    foreach ($items as &$item)
		 {
		   if (!isset($item['category_parent_id'])) continue; 
		   $co =& $item['ordering']; 
		   $cid =& $item['virtuemart_category_id']; 
		   //if (!isset($parents[$item['category_parent_id']])) $parents[$item['category_parent_id']] = array(); 
		   $parents[$item['category_parent_id']][$co][$cid] =& $item; 
		 }
		 
		
		 
		 
		 
	    //
		//foreach ($parent_i as $ordering=>$myitems)
		 foreach ($parents as $parent_id=>$parent_i)
		 {
		  
		   //$c = count($myitems); 
		   $i = 0; 
		   //if ($c != 1)
		   
		   {
		   $newa = $parents[$parent_id]; 
		   ksort($newa); 
		   foreach ($newa as $o2=>$item2)
		    {
			  
			   {
				  foreach ($item2 as $kk=>$val)
				  {
				    $i++;
			        $items[$kk]['ordering'] = $i; 
					
					//echo 'duplicity found for parent '.$parent_id.' and category '.$kk.'<br />'."\n"; 
				  }
			   }
			   
			}
			//break 1; 
			}
			if (false)
		   if (count($myitems)>1)
		    {
			  // reorder here: 
			  $c = 1; 
			  foreach ($parents[$parent_id][$ordering] as $cat_id=>$item)
			    {
				  // incremental:
				  $items[$cat_id]['ordering'] = $c; 
				  $c++; 
				}
			}
		 }
		 if ($debug)
		foreach ($parents as $p=>$k)
		  {
		    echo 'parent: '.$p.' has orderings of '; 
			{
			foreach ($k as $order=>$mitems)
			 foreach ($mitems as $cat_id=>$val)
			  echo $items[$cat_id]['ordering'].'(k:'.$order.'), '; 
			}
			echo "<br />\n"; 
		  }
		 return; 
	    $order = -1; 
		$ordering = array(); 
	    foreach ($items as $key=>$item)
		  {
		    $ordering[$item['category_parent_id']][$item['ordering']][$key] = $key;
		  }
		foreach ($ordering as $j=>$f)
		  foreach ($f as $order_x => $cat_id)
		  {
		     $num = count($ordering[$j][$order_x]);
		     if ($num>1)
			  {
			   $shift = 1; 
			   $shiftwhat = array(); 
			   for ($i=1; $i<=$num; $i++)
			    {
				  if (!empty($ordering[$j][$order_x+$i])) 
				   $shiftwhat[$order_x+$i] = $cat_id; 
				}
			   // we have a problem
			    $this->shiftOrdering($ordering, $items, $shiftwhat); 
			  }
		  }
	 }
	function shiftOrdering(&$arr)
	 {
	   foreach ($shiftwhat as $order_key=>$cat_id)
	     {
		   
		 }
	 }
	function getId($id, $menutype)
	{
	 if (!empty(self::$cats[$id]['Itemid'])) {
	 
	 return self::$cats[$id]['Itemid']; 
	 }
	 $db=JFactory::getDBO(); 
	 $q = "select id from #__menu where note LIKE 'virtuemart_category_id:".$id."' and menutype LIKE '".$menutype."' limit 0,1"; 
	 $db->setQuery($q); 
	 $r = $db->loadResult();
	 
	 if (!empty($r))
	 self::$cats[$id]['Itemid'] = $r; 
	 
	 return $r; 
	}
	
	function getParent($copy, $vmitem, $tomenu, $menutype)
	{
	 $parent = $vmitem['category_parent_id']; 
	 if (empty($copy[$parent])) 
	 {
	 if (!empty($tomenu)) return $tomenu; 
	 else
	 return 1; 
	 }
	 $id = $copy[$parent]['virtuemart_category_id']; 
	 
	 $r = $this->getId($id, $menutype); 
	 if (!empty($r)) return $r; 
	 // default for VM: 
	 if (!empty($tomenu)) return $tomenu; 
	 // else return top menu: 
	 return 1; 
	}
	
	function getSefPath(&$cats, $key, $vmmenu='', $alias)
	{
	  if (isset($cats[$key]['sefpath'])) return $cats[$key]['sefpath']; 
	  $arr = array(); 
	  $arr[] = $alias; 
	  $current = $cats[$key]; 
	  // max 10 recursions allowed, no more 
	  for ($i=0; $i<=10; $i++)
	   {
	     $parent = $current['category_parent_id']; 
		 if (!empty($parent))
		  {
		     $current = $cats[$parent]; 
			 $arr[] = $this->getAlias($current); 
			  
		  }
		  else
		  break; 
	   }
	  $path = ''; 
	 // will use full path to the category: 
	 foreach ($arr as $val)
	   {
	      //if (!empty($path))
		  $path = $val.'/'.$path; 
		  
	      //$path = $path.'/'.$val; 
		  //
	   }
	   //$path = 'root/'.$path; 
	   $cats[$key]['sefpath'] = $path; 
	   return $path; 
	}
	
	function getAlias($item, $unique=false)
	{
	// replace: 
	$vals = 'Á|A,Â|A,A|A,Ã|A,Ä|A,A|A,Æ|C,Ç|C,È|C,Ï|D,É|E,E|E,Ë|E,Ì|E,I|I,Í|I,Î|I,I|I,Å|L,¼|L,Ñ|N,Ò|N,N|N,O|O,Ó|O,Ô|O,O|O,Ö|O,À|R,Ø|R,Š|S,Œ|O,|T,Ù|U,Ú|U,Û|U,Ü|U,Ý|Y,Ž|Z,|Z,á|a,â|a,a|a,ä|a,a|a,æ|c,ç|c,è|c,ï|d,ð|d,é|e,ê|e,ë|e,ì|e,e|e,i|i,í|i,î|i,i|i,å|l,ñ|n,ò|n,n|n,o|o,ó|o,ô|o,õ|o,ö|o,š|s,œ|s,ø|r,à|r,|t,ù|u,ú|u,û|u,ü|u,ý|y,ž|z,Ÿ|z,ÿ|-,ß|ss,¥|A,µ|u,¥|A,µ|u,¹|a,¥|A,ê|e,Ê|E,œ|s,Œ|S,¿|z,¯|Z,Ÿ|z,|Z,æ|c,Æ|C,³|l,£|L,ó|o,Ó|O,ñ|n,Ñ|N,?|A,?|a,?|B,?|b,?|V,?|v,?|G,?|g,?|D,?|d,?|E,?|e,?|Zh,?|zh,?|Z,?|z,?|I,?|i,?|Y,?|y,?|K,?|k,?|L,?|l,?|M,?|m,?|N,?|n,?|O,?|o,?|P,?|p,?|R,?|r,?|S,?|s,?|T,?|t,?|U,?|u,?|F,?|f,?|Ch,?|ch,?|Ts,?|ts,?|Ch,?|ch,?|Sh,?|sh,?|Sch,?|sch,?|I,?|i,?|E,?|e,?|U,?|iu,?|Ya,?|ya,?| ,?| ,?| ,?| ,¾|l, |_,"|in,&|and,\'|_'; 
	$vala = explode(',', $vals); 
	$name = $item['category_name'];
	foreach ($vala as $s)
	{
	  $vv = explode('|', $s);
	  $search = $vv[0]; 
	  $rep = $vv[1]; 
	  $name = str_replace($search, $rep,  $name);
	  $name = str_replace(',', '_', $name); 
	  //$name = preg_replace("/[^A-Za-z0-9 ]/", '_', $name);
	}
	
	if ($unique)
	 {
	    //$q = "select * from #__menu where alias LIKE '".$name."' and menutype LIKE '".$menutype."' limit 0,1"; 
	 }
	return mb_substr($name, 0, 255); 
	
	
	}
	
	function getLevel($copy, $item)
	{
	  return $item['level']; 
	}
	function getVmComponentId()
	{
	 $db=JFactory::getDBO(); 
	 $q = "select extension_id from #__extensions where element LIKE 'com_virtuemart' and type LIKE 'component' limit 0,1"; 
	 $db->setQuery($q); 
	 $r = $db->loadResult();
	 if (!empty($r)) return $r; 
	 // default for VM: 
	 return 10000; 
	  
	}
	function clearLftRgt(&$arr)
	{
	  foreach ($arr as $key=>$val)
	   {
	     unset($arr[$key]['id_indexed']); 
		 unset($arr[$key]['parent_id_indexed']);
		 unset($arr[$key]['level']);
		 unset($arr[$key]['count']); 
	     $arr[$key]['lft'] = null; 
		 $arr[$key]['rgt'] = null; 
	   }
	}
	function sortArray(&$res, $index='virtuemart_category_id', $skey='category_parent_id', $top=0, $nd=false)
	{
			$mycats = array(); 
			// future ID:
			$int = 0; 
			foreach ($res as $c)
			{
			  $int++; 
			  $ind = $c[$index]; 
			  if ($ind == 3) 
			  {
			    //die('ok'); 
			  }
			  if (!isset($mycats[$ind])) $mycats[$ind] = array(); 
			  $this->merge($mycats[$ind], $c); 
			  //$mycats[$ind]['virtuemart_category_id'] = $ind; 
			  if (!isset($mycats[$ind]['children'])) 
			  {
			  $mycats[$ind]['lft'] = null; 
			  $mycats[$ind]['rgt'] = null; 
			  $mycats[$ind]['level'] = 0; 
			  $mycats[$ind]['id_indexed'] = $int; 
			  $mycats[$ind]['parent_id_indexed'] = 0; 
			  $mycats[$ind]['children'] = array(); 
			  
			  }
			  
			  if (!isset($mycats[$ind]['category_parent_id']))
			  $mycats[$ind]['category_parent_id'] = 0; 
			  $mycats[$mycats[$ind]['category_parent_id']][$ind] = $ind;
			  // is empty, or set (1), or equals to itself
			  if (!empty($c[$skey]) && ($c[$skey]!=$top) && ($c[$skey] != $ind))
			  {
			  // better: $mycats[$c['category_parent_id']]['children'][$ind] =& $c; 
			  // $mycats[$c[$skey]]['children'][$ind] =& $c; 
			  // reference back to me: 
			  if (!isset($mycats[$c[$skey]]['id_indexed'])) $mycats[$c[$skey]]['id_indexed'] = 0; 
			  $mycats[$ind]['parent_id_indexed'] =& $mycats[$c[$skey]]['id_indexed']; 
			  $mycats[$c[$skey]]['children'][$ind] =& $mycats[$ind];
			  
			  $mycats[$c[$skey]][$ind] = $ind;
			  
			  }
			  
			}
			
			
			$r = 1; 
			$l = 0; 
			$count = 0; 
			$level = 0; 
			$largest_id = 0; 
			$this->getLftRgt($mycats, true, $l, $r, $count, $level, $largest_id);
			
//var_dump($mycats[3]['virtuemart_category_id']); die(); 			
			return $mycats; 
	}
	
	function logTable($table)
	{

	$db = JFactory::getDBO(); 
	{
		$db->setQuery('SELECT * FROM `#__'.$table.'`');
		$result = $db->loadAssocList(); 
		$first = reset($result); 
		$num_fields = count($first); 
		
		
		$return.= 'DROP TABLE '.$table.';';
		$q = 'SHOW CREATE TABLE `#__'.$table.'`';
		$db->setQuery($q); 
		$row2 = $db->loadAssoc(); 
		
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);

	}
	
	function getLftRgt(&$mycats, $top=true, &$l, &$r, &$count, &$level, &$largest_id)
	{
	  //$mycats[0]['lft'] = 0; 
	  //$mycats[0]['rgt'] = 1; 
	  foreach ($mycats as $cat_id=>$cats)
			{
			   if (empty($cat_id)) continue; 
			  
			   // is a top category
			   if ($top)
			   {
			   
			    
			    
				
			   if (empty($mycats[$cat_id]['category_parent_id']))
			    {
				
				$count++; 

				
				if ($count==711)
				 {
				   
				 }
				  $mycats[$cat_id]['lft'] = $count; 
				  $mycats[$cat_id]['level'] = $level; 
				  $count_saved = $count; 
				  
				  
				  
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
					   
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
					
					{
					  $count++; 
					  $mycats[$cat_id]['rgt'] = $count; 
					  
					  

					  
					 
					}
				  $mycats[$cat_id]['count'] = $count - $count_saved; 
				}
			   }
			   else
			   {
			    $count++; 
				 

			      //if (!is_null($mycats[$cat_id]['lft'])) return; 
				  if (isset($mycats[$cat_id]['lft'])) return; 
			      
				  $mycats[$cat_id]['lft'] = $count; 
				  $mycats[$cat_id]['level'] = $level; 
				  $count_saved = $count;   
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
				
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
					
					{
					  $count++; 
					  $mycats[$cat_id]['rgt'] = $count; 
					}
				  $mycats[$cat_id]['count'] = $count - $count_saved; 
				  
			   }
			   
			   $largest_id = $cat_id; 
				
			}
			if ($top)
			{
			 
			 
			}
	}
	
	function getRght(&$mycats, $top=true, &$l, &$r, &$count, &$level, $largest_id)
	{
	   foreach ($mycats as $cat_id=>$cats)
			{
			   if (empty($cat_id)) continue; 
			  
			   // is a top category
			   if ($top)
			   {
			    $count++; 
			   if (empty($mycats[$cat_id]['category_parent_id']))
			    {
				  $mycats[$cat_id]['rgt'] = $count+$mycats[$cat_id]['count']+1;
				  
				 
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
					   
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
				 
				}
			   }
			   else
			   {
			    $count++; 
			      if (!is_null($mycats[$cat_id]['lft'])) return; 
			      
				  
				  $mycats[$cat_id]['rgt'] = $count+$mycats[$cat_id]['count']+1;
				  
				 
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
				
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
				  
				  
			   }
			   
			   $largest_id = $cat_id; 
				
			}
			if ($top)
			{
			 
			 
			}
	
	}
	
	function getMenusSorted()
	{
		$menus = $this->getMenus(); 
		$db = JFactory::getDBO(); 
		$ret = array(); 
		foreach ($menus as $m)
		 {
		   //$q = "select * from #__menu as m left join #__extensions as e on e.extension_id = m.component_id where menutype LIKE '".$db->getEscaped($m['menutype'])."' limit 9999"; 
		   $q = "select * from #__menu  as m, #__extensions as e where e.extension_id = m.component_id  and menutype LIKE '".$db->getEscaped($m['menutype'])."' limit 9999"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssocList(); 
		   
		   $ret[$m['menutype']] = $this->sortArray($res, 'id', 'parent_id', 1); 
		   
		   //$this->getItemName($ret[$m['menutype']]); 
		 
		 }
		 
		return $ret; 
	}
	function getMenus()
	{
	  $db = JFactory::getDBO(); 
	  $q = 'select * from #__menu_types where 1 limit 999'; 
	  $db->setQuery($q); 
	  return $db->loadAssocList(); 
	}
	function merge(&$arr1, $arr2)
	{
	  if (empty($arr1)) $arr1 = $arr2; 
	  else
	  foreach ($arr2 as $key=>$arr2v)
	  {
	    $arr1[$key] = $arr2v; 
		//if (!empty($c['element'])) $mycats[$c[$skey]]['componentname'] = $c['element']; 
		if ($key=='element') $arr1['componentname'] =& $arr2['element']; 
	  }
	  if (array_key_exists ('type', $arr1))
	   {
	     $this->getItemName($arr1); 
	   }
	}
	
	function printChildren($arr, $value, $title, $prefix='->')
	{
		  foreach ($arr as $line)
	   {
	     if (!isset($line[$value]))
		 {
		   
		 }
	     echo '<option value="'.$line[$value].'">'.$prefix.$line[$title].'</option>'; 
		 if (!empty($line['children'])) 
		  {
		  $prefix = '->'.$prefix; 
		  $this->printChildren($line['children'], $value, $title, $prefix); 
		  }
	   }
	}
	
	/**
	 *  get the menu name, orig from: \administrator\components\com_menus\views\items\view.html.php
	 */
	public function getItemName(&$item)
	{
		$lang 		= JFactory::getLanguage();

		//$this->ordering = array();

		//foreach ($items as $key=>&$item) 
		{
			//$this->ordering[$item['parent_id']][] = $item['id'];
			 $item['item_type'] = $item['title'];
			if (empty($item['type'])) {
			 
			
			 continue; 
			 }
			// item type text
			switch ($item['type']) {
				case 'url':
					$value = JText::_('COM_MENUS_TYPE_EXTERNAL_URL');
					break;

				case 'alias':
					$value = JText::_('COM_MENUS_TYPE_ALIAS');
					break;

				case 'separator':
					$value = JText::_('COM_MENUS_TYPE_SEPARATOR');
					break;

				case 'component':
				default:
					if (empty($item['type']) || (empty($item['componentname']))) 
					{
					 $value = $item['title']; 
					 break; 
					}
					// load language
						$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$item['componentname'], null, false, false)
					||	$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
					||	$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$item['componentname'], $lang->getDefault(), false, false);

					if (!empty($item['componentname'])) {
						$value	= JText::_($item['componentname']);
						$vars	= null;

						parse_str($item['link'], $vars);
						if (isset($vars['view'])) {
							// Attempt to load the view xml file.
							$file = JPATH_SITE.'/components/'.$item['componentname'].'/views/'.$vars['view'].'/metadata.xml';
							if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
								// Look for the first view node off of the root node.
								if ($view = $xml->xpath('view[1]')) {
									if (!empty($view[0]['title'])) {
										$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

										// Attempt to load the layout xml file.
										// If Alternative Menu Item, get template folder for layout file
										if (strpos($vars['layout'], ':') > 0)
										{
											// Use template folder for layout file
											$temp = explode(':', $vars['layout']);
											$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$item['componentname'].'/'.$vars['view'].'/'.$temp[1].'.xml';
											// Load template language file
											$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, null, false, false)
											||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], null, false, false)
											||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, $lang->getDefault(), false, false)
											||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], $lang->getDefault(), false, false);

										}
										else
										{
											// Get XML file from component folder for standard layouts
											$file = JPATH_SITE.'/components/'.$item['componentname'].'/views/'.$vars['view'].'/tmpl/'.$vars['layout'].'.xml';
										}
										if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
											// Look for the first view node off of the root node.
											if ($layout = $xml->xpath('layout[1]')) {
												if (!empty($layout[0]['title'])) {
													$value .= ' » ' . JText::_(trim((string) $layout[0]['title']));
												}
											}
											if (!empty($layout[0]->message[0])) {
												$item['item_type_desc'] = JText::_(trim((string) $layout[0]->message[0]));
											}
										}
									}
								}
								unset($xml);
							}
							else {
								// Special case for absent views
								$value .= ' » ' . JText::_($item['componentname'].'_'.$vars['view'].'_VIEW_DEFAULT_TITLE');
							}
						}
					}
					else {
						if (preg_match("/^index.php\?option=([a-zA-Z\-0-9_]*)/", $item['link'], $result)) {
							$value = JText::sprintf('COM_MENUS_TYPE_UNEXISTING', $result[1]);
						}
						else {
							$value = JText::_('COM_MENUS_TYPE_UNKNOWN');
						}
					}
					break;
			}
			if (!empty($value))
			$item['item_type'] = $value;
		}

		

		

		
		
	}

	function flushTable($table)
	{
	  $db = JFactory::getDBO(); 
	  $q = 'delete from `#__'.$table.'` where 1 limit 99999'; 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $e; die(); }
	}
  function copyTable($from, $to)
  {
  $dbj = JFactory::getDBO();

  $prefix = $dbj->getPrefix();
  
   if (OPCloader::tableExists($to))
   {
      $q = 'drop table `'.$prefix.$to.'`'; 
	  $dbj->setQuery($q); 
	  $dbj->query(); 
	  $e = $dbj->getError(); if (!empty($e)) { echo $e; die(); }
   }

  $Config = new JConfig();
  $db = $Config->db;
  
  $sql = '
 CREATE  TABLE  `'.$db.'`.`'.$prefix.$to.'` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `menutype` varchar( 24  )  NOT  NULL  COMMENT  \'The type of menu this item belongs to. FK to #__menu_types.menutype\',
 `title` varchar( 255  )  NOT  NULL  COMMENT  \'The display title of the menu item.\',
 `alias` varchar( 255  )  CHARACTER  SET utf8 COLLATE utf8_bin NOT  NULL  COMMENT  \'The SEF alias of the menu item.\',
 `note` varchar( 255  )  NOT  NULL DEFAULT  \'\',
 `path` varchar( 1024  )  NOT  NULL  COMMENT  \'The computed path of the menu item based on the alias field.\',
 `link` varchar( 1024  )  NOT  NULL  COMMENT  \'The actually link the menu item refers to.\',
 `type` varchar( 16  )  NOT  NULL  COMMENT  \'The type of link: Component, URL, Alias, Separator\',
 `published` tinyint( 4  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'The published state of the menu link.\',
 `parent_id` int( 10  )  unsigned NOT  NULL DEFAULT  \'1\' COMMENT  \'The parent menu item in the menu tree.\',
 `level` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'The relative level in the tree.\',
 `component_id` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'FK to #__extensions.id\',
 `ordering` int( 11  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'The relative ordering of the menu item in the tree.\',
 `checked_out` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'FK to #__users.id\',
 `checked_out_time` timestamp NOT  NULL DEFAULT  \'0000-00-00 00:00:00\' COMMENT  \'The time the menu item was checked out.\',
 `browserNav` tinyint( 4  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'The click behaviour of the link.\',
 `access` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'The access level required to view the menu item.\',
 `img` varchar( 255  )  NOT  NULL  COMMENT  \'The image of the menu item.\',
 `template_style_id` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\',
 `params` text NOT  NULL  COMMENT  \'JSON encoded data for the menu item.\',
 `lft` int( 11  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'Nested set lft.\',
 `rgt` int( 11  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'Nested set rgt.\',
 `home` tinyint( 3  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'Indicates if this menu item is the home or default page.\',
 `language` char( 7  )  NOT  NULL DEFAULT  \'\',
 `client_id` tinyint( 4  )  NOT  NULL DEFAULT  \'0\',
 PRIMARY  KEY (  `id`  ) ,
 UNIQUE  KEY  `idx_client_id_parent_id_alias_language` (  `client_id` ,  `parent_id` ,  `alias` ,  `language`  ) ,
 KEY  `idx_componentid` (  `component_id` ,  `menutype` ,  `published` ,  `access`  ) ,
 KEY  `idx_menutype` (  `menutype`  ) ,
 KEY  `idx_left_right` (  `lft` ,  `rgt`  ) ,
 KEY  `idx_alias` (  `alias`  ) ,
 KEY  `idx_path` (  `path` ( 333  )  ) ,
 KEY  `idx_language` (  `language`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8;'; 
  $dbj->setQuery($sql); 
  $dbj->query(); 
  $e = $dbj->getErrorMsg(); 
  if (!empty($e)) { echo $e; die(); }

  $sql = 'SET SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\'';
  $dbj->setQuery($sql); 
  $dbj->query(); 
  $e = $dbj->getErrorMsg(); 
  if (!empty($e)) { echo $e; die(); }


$sql = 'INSERT INTO `'.$db.'`.`'.$prefix.$to.'` SELECT * FROM `'.$db.'`.`'.$prefix.$from.'`;'; 

  $dbj->setQuery($sql); 
  $dbj->query(); 
  $e = $dbj->getErrorMsg(); 
  if (!empty($e)) { echo $e; die(); }
  }  
  

}

class nested {
  static $items; 
  var $item; 
  var $count; 
  function addChild(&$item, $idName='id', $parent_idName='parent_id')
  {
    self::$items[$item[$idName]] =& $this->toItem($item); 
	self::$items[$item[$parent_idName]] =& $this->toItem(self::$items[$item[$parent_idName]]); 
	if (!empty($item[$parent_idName]))
	if (isset(self::$items[$item[$parent_idName]]))
	self::$items[$item[$parent_idName]]->recalculate(); 
  }
  function &toItem(&$item)
  {
    if (!isset($item)) $item = array(); 
    $this->item = &$item; 
	return $this; 
  }
  function recalculate()
  {
    
  }
}
