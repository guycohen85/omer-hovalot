<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class OPCtrackingHelper {
  
  // will create cookie hash and register it in the database
  public static function registerCart($hash)
  {
    $res = OPCtrackingHelper::getEmptyLine(0, 0, $hash); 
	$user = JFactory::getUser(); 
	$user_id = (int)$user->get('id', 0); 
	$db = JFactory::getDBO(); 
	
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 
	
	   jimport( 'joomla.utilities.date' );
	   
	   $date = new JDate('now');
	   
	   if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s"); 
	   

	
	if (empty($res))
	 {
	  if (defined('OPCJ3') && (!OPCJ3))
	  {
	   $q = "insert into #__virtuemart_plg_opctracking (virtuemart_order_id, hash, shown, created, created_by, modified, modified_by) values ('0', '".$db->getEscaped($hash)."', '', '".$dd."', '".(int)$user_id."', '".$dd."', '".(int)$user_id."' )"; 
	  }
	  else
	  {
	   $q = "insert into #__virtuemart_plg_opctracking (virtuemart_order_id, hash, shown, created, created_by, modified, modified_by) values ('0', '".$db->escape($hash)."', '', '".$dd."', '".(int)$user_id."', '".$dd."', '".(int)$user_id."' )"; 
	  }
	  
			   $db->setQuery($q); 
			   $db->query(); 
			   
	 }
	 else
	 {
	  
	   self::updateLine($res['id'], $res['virtuemart_order_id'], $hash, $res['shown']); 
	 }
	

  } 
  
  // will associate cookie hash with order
  public static function orderCreated($hash, &$data, $order_last_state)
  {
    
	
		
		if (is_object($data))
		 {
		
		   
		   if (!isset($data->virtuemart_order_id)) return;   
		   $order_id = $data->virtuemart_order_id; 
		   
		   if (!isset($data->order_status)) return;   
		   $status = $data->order_status; 
		   $tracking = OPCtrackingHelper::getEmptyLine(0, $order_id, $hash); 
		   if (!empty($tracking))
		   {
		   
		   
		   if ($tracking['virtuemart_order_id'] != $order_id)
		    {
			  OPCtrackingHelper::updateLine($tracking['id'], $order_id, $hash); 
			}
		   }
		   else
		   {
		     // will do insert: 
		     OPCtrackingHelper::registerCart($hash); 
			 // will update the order_id 
			 OPCtrackingHelper::updateLine(0, $order_id, $hash); 
			 
		   }
			
		
		   
		 }
		 else
		 {
		   
		 }
		
  }
  // sets cookie (general)
  public static function setCookie($hash, $timeout=0)
  {
    
    if (empty($timeout)) $timeout = time()+60*60*24*30; 
	else $timeout += time(); 
	if ($timeout<0) $timeout = 1; 
	
	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
	$config =  JFactory::getConfig(); 
	 if (!OPCJ3)
	 {
	 $domain = $config->getValue('config.cookie_domain'); 
	 $path = $config->getValue('config.cookie_path'); 
	 }
	 else
	 {
	 $domain = $config->get('cookie_domain'); 
	 $path = $config->get('cookie_path'); 
	 }
	 if (empty($path)) $path = '/'; 

	 if (empty($domain))
	 setcookie($hashn, $hash, $timeout, $path);
	 else
	 setcookie($hashn, $hash, $timeout, $path, $domain);
	
	
  }
  
  public static function getUserHash()
   {
   return;
      if (method_exists('JApplication', 'getHash'))
	  $hashn = JApplication::getHash('opctracking'); 
	  else $hashn = JUtility::getHash('opctracking'); 
	  $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	  if (empty($opchash))
	   {
		  OPCtrackingHelper::setCookie($opchash); 
		  $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	   }
	  return $opchash; 
   }
  
  // 
  public static function getHTML()
  {
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 

	 
	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
	 
     $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	   if (empty($opchash)) return; 
	   $db = JFactory::getDBO(); 
	   if (defined('OPCJ3') && (!OPCJ3))
	   {
	   $q = "select o.order_status from #__virtuemart_plg_opctracking as t, #__virtuemart_plg_opctracking as o  where t.hash = '".$db->getEscaped($opchash)."' and t.virtuemart_order_id = o.virtuemart_order_id limit 0,1"; 
	   }
	   else
	   {
	   $q = "select o.order_status from #__virtuemart_plg_opctracking as t, #__virtuemart_plg_opctracking as o  where t.hash = '".$db->escape($opchash)."' and t.virtuemart_order_id = o.virtuemart_order_id limit 0,1"; 
	   }
	   $db->setQuery($q); 
	   $state = $db->loadResult(); 
	   if (empty($state)) return;
	   if ($state == 'C')
	    {
		
		$bodyp = stripos($buffer, '</body'); 
		 $html .= '
		 <script type="text/javascript">alert(\'gotch ya, testing tracking\');</script>
		 '; 
		$buffer = substr($buffer, 0, $bodyp).$html.substr($buffer, $bodyp); 
		
		}
  }
  public static $config; 
  public static function checkStatus()
  {
  
     // ALTER TABLE  `#__virtuemart_order_histories` ADD INDEX (  `virtuemart_order_id` )
    if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
	 $hash = JRequest::getVar($hashn, false, 'COOKIE'); 
	 if (empty($hash)) return false; 
	 OPCtrackingHelper::$html = '';  	 
	 $tracking_s = OPCtrackingHelper::getLines(0, 0, $hash); 
	 
	 
	 
	 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'tracking.php'); 
	 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'config.php'); 
	 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	 jimport('joomla.filesystem.file');
	 jimport( 'joomla.utilities.date' );
	 
	 if (empty($tracking_s)) return; 
	 foreach ($tracking_s as $tracking)
	 {
	 self::$actions = array(); 
	 $order_id = (int)$tracking['virtuemart_order_id']; 
	 
	 if (empty($order_id)) continue; 
	 
	 //
	 	   
	   
	   //only take order less than one month
	   $time = time()-(24*60*60*30); 
	   $date = new JDate($time);
	   
	   if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s", $time); 

	 $q = 'select * from #__virtuemart_order_histories where virtuemart_order_id = '.$order_id.' and created_on > \''.$dd.'\' order by virtuemart_order_history_id desc'; 
	 $db = JFactory::getDBO(); 
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 
	 
	 
	 
	 if (empty($res)) continue; // this should not happen
	 
	
	 $config = new JModelConfig(); 
	 $config->loadVmConfig(); 
	 //$files = $config->getPhpTrackingThemes();
	 $statuses = $config->getOrderStatuses();
     $trackingModel = new JModelTracking(); 
	 self::$config = $config = $trackingModel->getStatusConfig($statuses); 
	
	
	 $ind = 0; 
	 
	 foreach ($res as $state)
	 {
	 
	     if (empty($config[$state['order_status_code']])) continue; 
		  else $lc = $config[$state['order_status_code']]; 
		  

		 
		 if (!empty($lc->only_when))
		 {
		 
		    $newa = array_slice($res, $ind+1); 
			
			foreach ($newa as $ns)
			 {
			 
			 
			   if ($ns['order_status_code']==$lc->only_when)
			    {
				  //OK, do an action
				  if (!empty($config[$state['order_status_code']]->code))
				  self::prepareAction($state['order_status_code'], 'code', $config[$state['order_status_code']]->code);   
				  foreach ($lc as $key=>$file)
					{
						// inbuilt limitations
						if ($file == 'only_when') continue; 
						if ($file == 'code') continue; 
						
						$file = JFile::makeSafe($file);

						if (empty($lc->$file)) continue; 
			
		    
						self::prepareAction($state['order_status_code'], $file);   
					}
				
				
				
				}
			 }
		 }
		 else
		 {
			if (!empty($config[$state['order_status_code']]->code))
				  {
				  self::prepareAction($state['order_status_code'], 'code', $config[$state['order_status_code']]->code);   
				  
				  }

		   // only when is not set: 
		   foreach ($lc as $key=>$file)
		   {
		     // inbuilt limitations
			 if (stripos($file, 'since')===0) continue; 
		     if ($file == 'only_when') continue; 
			 if ($file == 'code') continue; 
			 if (empty($lc->$file)) continue; 
			
			
		    
		     self::prepareAction($state['order_status_code'], $file);   
		   }
		   
		 }
	
		 
		 $ind++; 
		  
	 }
	 
	 
	 self::checkActions($tracking); 
	
	 self::doActions($tracking); 
	  }
	 if (!empty(OPCtrackingHelper::$html)) return true; 
	 
	 return false; 
  }
  
  // will check if they were already shown to the users
  public function checkActions($tracking)
  {
    if (empty(self::$actions)) return; 
	
	
    $shown = $tracking['shown']; 
	if (!empty($shown))
	 {
	   $so = @json_decode($shown, true); 
	 }
	
	if (empty($so)) return; 
	
	// obj to array: 
	$so2 = array(); 
	foreach ($so as $key=>$val)
	{
	  $so2[$key] = $val; 
	}
	
    
	
    foreach (self::$actions as $status=>$data)
	 if (!empty($so2[$status]))
	 foreach ($data as $name=>$extra)
	 {
	   
	    
		  // do not perform the action once it was done
		  
		  {
		  if (!empty($so2[$status][$name])) {
		   $sostat = (int)$so2[$status][$name]; 
		   if ($sostat === 2)
		   {
		   unset(self::$actions[$status][$name]);
		   
		   }
		   
		    }
		   }
		
	 }
	 
	 foreach (self::$actions as $status=>$data)
	 if (empty($data)) unset(self::$actions[$status]); 
	
	
  
  }
  
  private function getReplaceVars($html, $tracking)
  {
    
	$order_id = $tracking['virtuemart_order_id']; 
	if (empty($order_id)) return ''; 
	$array = array(); $order = new stdClass(); 
	self::getOrderVars($order_id, $array, $order); 
	foreach ($array as $key => $val)
	 {
	   $html = str_replace('{'.$key.'}', $val, $html); 
	 }
	 return $html; 
	
	
  }
  
  private function doActions($tracking)
  {
    
	if (empty(self::$actions)) return;
	 
	//debug: 
	
	
    foreach (self::$actions as $status=>$data)
	{
	 if (empty($data)) continue; 
	 foreach ($data as $name=>$extra)
	   {
	     
	     if ($name != 'code')
		  {
		    $html = self::getFileRendered($tracking, $name, $status); 
			OPCtrackingHelper::$html .= $html; 
			
			
		  }
		  else
		  {
		  
		    $html = ''; 
			require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
			$default = new stdClass(); 
			$params = OPCconfig::getValue('tracking_config', $name, 0, $default); 
			$order_id = $tracking['virtuemart_order_id']; 
			$trackingrenderer = new OPCtrackingview($order_id, 'check_js', $status, $params, $name); 
			
			if ($trackingrenderer->error) continue;
			$trackingrenderer->params = $params; 
		    $trackingrenderer->pingData .= '&file='.str_replace('&', '&amp;', $name); 
			$html = ''; 
			$html .= $trackingrenderer->fetchFile('check_js'); 
			$html .= self::getReplaceVars($data['code'], $tracking); 
			$trackingrenderer->pingData .= '&end=2'; 
			$html .= $trackingrenderer->fetchFile('check_js'); 
			OPCtrackingHelper::$html .= $html; 
		  }
	     
	   }
	  }
	   
	   
	   
  }
  
  public static $html = ''; 
  
    // returns 
  function getOrderVars($order_id=0, &$order_array, &$order_object, $show=false, &$named_obj=array())
  {
     $db = JFactory::getDBO(); 
    if (empty($order_id))
	{
	 $app = JFactory::getApplication(); 
	 // do not allow random order for FE
	 if (!$app->isAdmin()) return; 
	 $order_id = JRequest::getInt('order_id', 0); 
	 if (empty($order_id))
	 {
     
      $q = 'select virtuemart_order_id from #__virtuemart_orders where 1 order by rand() limit 0,1';
	  $db->setQuery($q); 
	  $order_id = $db->loadResult($q); 
	 }
	}
    //require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
    $modelOrder = OPCmini::getModel('orders');
	$order_id = (int)$order_id; 
	
	$order = $modelOrder->getOrder($order_id);

	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	if (OPCmini::tableExists('virtuemart_invoices')) 
	if (empty($order['invoice']))
	{
	//
	$q = 'SELECT * FROM `#__virtuemart_invoices` WHERE `virtuemart_order_id` = '.$order_id.' limit 0,1';
	 $db->setQuery($q); 

  $invoice_data = $db->loadAssoc();
   if (empty($invoice_data)) {
     $invoice_data = array(); 
   }   
  $order['invoice'] = $invoice_data; 
  
    }
	
	
	
	if (isset($order['details']['']))
	{
	$order['details']['BT'] = $order['details']['']; 
	$order['details']['ST'] = $order['details']['BT']; 
	}
	
	OPCtrackingHelper::getTextFields($order); 
	$ret = array(); 
	$named_obj = array(); 
	if (!empty($order['details']['BT']))
	foreach ($order['details']['BT'] as $key=>$val)
	 {
	   if (empty($val)) $val = ''; 
	   $ret['bt_'.$key] = $val; 
	   $named_obj['bt_'.$key] = '$order[\'details\'][\'BT\']->'.$key; 
	   if ($show)
	    {
		   echo '$order[\'details\'][\'BT\']->'.$key.' = "'.$val."\";<br />\n"; 
		}
		
		
		
	 }
	
	if (!empty($order['details']['ST']))
	foreach ($order['details']['ST'] as $key=>$val)
	 {
	   
	   

	   if (empty($val)) $val = ''; 
	   $ret['st_'.$key] = $val; 
	   $named_obj['st_'.$key] = '$order[\'details\'][\'ST\']->'.$key; 
	   
	    if ($show)
	    {
		   echo '$order[\'details\'][\'ST\']->'.$key.' = "'.$val."\";<br />\n"; 
		}
		
		
	   
	 }
	$i =0; 
	
	
	
	
	foreach ($order['history'] as $key=>$val)
	{
	  foreach ($val as $key2=>$val2)
	   {
	     if (empty($val2)) $val2 = ''; 
	     $ret['history_'.$key.'_'.$key2] = $val2; 
		 $named_obj['history_'.$key.'_'.$key2] = '$order[\'history\'][\''.$key.'\']->'.$key2;
		if ($show)
	    {
		   echo '$order[\'history\']['.$key.']->'.$key2.' = "'.$val2."\";<br />\n"; 
		}

		 
	   }
	}
	
	
	if (!empty($order['items']))
	foreach ($order['items'] as $key=>$val)
	foreach ($val as $key2=>$val2)
	{
	  $ret['items_'.$key.'_'.$key2] = $val2; 
	  $named_obj['items_'.$key.'_'.$key2] = '$order[\'items\'][\''.$key.'\']->'.$key2; 
	  	if ($show)
	    {
		   echo '$order[\'items\']['.$key.']->'.$key2.' = "'.$val2."\";<br />\n"; 
		}
	 
	}
    

	
	 $order_array = $ret; 
	 $order_object = $order; 
	 return $order; 
  }

  
  
  public static function ping()
  {
     $file = JRequest::getVar('file', ''); 
	 jimport('joomla.filesystem.file');
	 $file = JFile::makeSafe($file);
	 $hash = JRequest::getVar('hash', ''); 
	 $order_id = (int)JRequest::getVar('order_id', 0);
	 $res = OPCtrackingHelper::getLine(0, $order_id, $hash); 
	 
	 $order_status = JRequest::getVar('order_status', ' '); 
	 
	 $end = JRequest::getVar('end', 1); 
	 
	 if (!empty($res['shown']))
	  {
	    
	    $data = @json_decode($res['shown'], true); 
		if (empty($data)) $data = array(); 
		if (empty($data[$order_status])) $data[$order_status] = array(); 
		
		if (!empty($data[$order_status][$file]))
		if ((int)$data[$order_status][$file] >= (int)$end) return;
		
		$data[$order_status][$file] = $end; 
		$new = json_encode($data); 
		
	  }
	 else
	  {
	    $newa = array(); 
		$newa[$order_status] = array();
		$newa[$order_status][$file] = $end; 
		
		$new = json_encode($newa); 
	  }
	  
	 OPCtrackingHelper::updateLine($res['id'], $res['virtuemart_order_id'], $hash, $new); 
	$app  = JFactory::getApplication(); 
    $app->close(); 
    die(); 
	 
  }
  
  
  public static function getCheckJs($tracking, $file, $status, $overridename='')
  {
  
  }
  public static function getFileRendered($tracking, $file, $status, $overridename='')
   {
  
     require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	 $default = new stdClass(); 
     $data = OPCconfig::getValue('tracking_config', $file, 0, $default); 
	 $order_id = $tracking['virtuemart_order_id']; 
	 $trackingrenderer = new OPCtrackingview($order_id, $file, $status, $data); 
	 
	 
	 
	 if ($trackingrenderer->error) return '';
	 $trackingrenderer->params = $data; 
	 
	 
	 //OPCtrackingHelper::$html .= 
	 if (!empty($overridename))
	 $trackingrenderer->pingData .= '&file='.str_replace('&', '&amp;', $overridename); 
	 else
	 $trackingrenderer->pingData .= '&file='.str_replace('&', '&amp;', $file); 
	 $html = ''; 
	 
	 if ($file != 'check_js')
	 $html .= $trackingrenderer->fetchFile('check_js'); 
	 
	 $html .= $trackingrenderer->fetchFile($file); 
	 
	 
	 
	 
	 $trackingrenderer->pingData .= '&end=2'; 
	 $html .= $trackingrenderer->fetchFile('check_js'); 
	 
	 return $html; 
	 
   }
   
  static $actions; 
  function prepareAction($state, $what, $extra='')
  {
  
  
    if (empty($what)) return; 
    if (empty(self::$actions)) self::$actions = array(); 
	
	if (empty(self::$actions[$state])) self::$actions[$state] = array(); 
	self::$actions[$state][$what] = $extra ; 
	
	
	//debug: 
	//echo $state.' '.$what."<br />\n"; 
	//echo $state.' '.$what."<br />\n"; 
	
  }
  
  function getEmptyLine($id=0, $order_id=0, $hash=0)
  {
    
    require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 

	
    if (empty($id))
	{
     $db = JFactory::getDBO(); 
	 if (defined('OPCJ3') && (!OPCJ3))
	 {
     $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->getEscaped($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc limit 0,1"; 
	 }
	 else
	 {
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc limit 0,1"; 
	 }
	 $db->setQuery($q); 
	 return $db->loadAssoc(); 
	}
  }
  
  function getLines($id=0, $order_id=0, $hash=0)
  {
  
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 
  
     if (empty($id))
	{
     $db = JFactory::getDBO(); 
	 if (defined('OPCJ3') && (!OPCJ3))
     $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->getEscaped($hash)."' "; 
	 else
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' "; 
	 
	 if (!empty($order_id))
	 $q .= ' and virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
	 else
	 $q .= ' order by virtuemart_order_id desc limit 0,2'; 
	 
	 $db->setQuery($q); 
	 $ret = $db->loadAssocList(); 
	 
	 return $ret; 
	}
	else
	{
      $db = JFactory::getDBO(); 
      $q = "select * from #__virtuemart_plg_opctracking where id='".(int)$id."' limit 0,1"; 
	  $db->setQuery($q); 
	  return $db->loadAssocList(); 
	}
	if (!empty($order_id))
	{
      $db = JFactory::getDBO(); 
      $q = "select * from #__virtuemart_plg_opctracking where virtuemart_order_id='".(int)$order_id."' limit 0,5"; 
	  $db->setQuery($q); 
	  return $db->loadAssocList(); 
	}
	return array(); 
  }
  function getLine($id=0, $order_id=0, $hash=0)
  {
    if (empty($id))
	{
     $db = JFactory::getDBO(); 
     if (defined('OPCJ3') && (!OPCJ3))
	 {
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->getEscaped($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc"; 
	 }
	 else
	 {
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc"; 
	 }
	 //if (!empty($order_id))
	 //$q .= ' and virtuemart_order_id = '.(int)$order_id.' '; 
	 $q .= " limit 0,1"; 
	 $db->setQuery($q); 
	 return $db->loadAssoc(); 
	}
	else
	{
      $db = JFactory::getDBO(); 
      $q = "select * from #__virtuemart_plg_opctracking where id='".(int)$id."' limit 0,1"; 
	  $db->setQuery($q); 
	  return $db->loadAssoc(); 
	}
	if (!empty($order_id))
	{
      $db = JFactory::getDBO(); 
	  if (defined('OPCJ3') && (!OPCJ3))
	  {
      $q = "select * from #__virtuemart_plg_opctracking where virtuemart_order_id='".(int)$order_id."' and hash='".$db->getEscaped($hash)."' limit 0,1"; 
	  }
	  else
	  {
	  $q = "select * from #__virtuemart_plg_opctracking where virtuemart_order_id='".(int)$order_id."' and hash='".$db->escape($hash)."' limit 0,1"; 
	  }
	  $db->setQuery($q); 
	  return $db->loadAssoc(); 
	}
	return false; 
	
  }
  
  function updateLine($id=0, $order_id=0, $hash=0, $shown='')
   {
      $db = JFactory::getDBO(); 
	  
	   jimport( 'joomla.utilities.date' );
	   $date = new JDate('now');
	   if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s"); 

	   
	  $user = JFactory::getUser(); 
	  $user_id = (int)$user->get('id', 0); 

	  
	  if (empty($id))
	  {
	  if (defined('OPCJ3') && (!OPCJ3))
	  {
	  $q2 = "select id from #__virtuemart_plg_opctracking where (virtuemart_order_id = '".(int)$order_id."' or virtuemart_order_id = 0) and hash = '".$db->getEscaped($hash)."' order by virtuemart_order_id desc limit 0,1"; 
	  }
	  else
	  {
	  $q2 = "select id from #__virtuemart_plg_opctracking where (virtuemart_order_id = '".(int)$order_id."' or virtuemart_order_id = 0) and hash = '".$db->escape($hash)."' order by virtuemart_order_id desc limit 0,1"; 
	  }
	  $db->setQuery($q2);
	  $id = $db->loadResult(); 
	  //echo '...id:'.$id;
	  
	  }
	  //else { $q=$z; echo $id.'....:'; }
	
	  
	  if (empty($id))
	   {
	   if (defined('OPCJ3') && (!OPCJ3))
	   {
        $q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', shown='".$db->getEscaped($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where hash = '".$db->getEscaped($hash)."' ";  	  
		}
		else
		{
		$q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', shown='".$db->escape($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where hash = '".$db->escape($hash)."' ";  	  
		}
	    $db->setQuery($q); 
	    $db->query(); 
		
	   }
	   else
	   {
	     if (defined('OPCJ3') && (!OPCJ3))
		 {
		  $q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', hash = '".$db->getEscaped($hash)."', shown='".$db->getEscaped($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where id = ".(int)$id." ";  	     
		  }
		  else
		  {
		  $q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', hash = '".$db->escape($hash)."', shown='".$db->escape($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where id = ".(int)$id." ";  	     
		  }
		  $db->setQuery($q); 
	      $db->query(); 

	   }
	  
		
   }
   
  function &getVendorInfo($vendorid)
  {
   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
   if (!OPCmini::tableExists('virtuemart_userinfos')) return array(); 
   if (empty($vendorid)) $vendorid = 1; 
   

   $dbj = JFactory::getDBO(); 

   $q = "SELECT * FROM `#__virtuemart_userinfos` as ui, #__virtuemart_vmusers as uu WHERE ui.virtuemart_user_id = uu.virtuemart_user_id and uu.virtuemart_vendor_id = '".(int)$vendorid."' limit 0,1";
   $dbj->setQuery($q);
	
    $vendorinfo = $dbj->loadAssoc();
	return $vendorinfo;  
  }
  

   public function getTextFields(&$order)
  {
    // setes default language: 
	
	$lang = JFactory::getLanguage(); 
	$lang = $lang->getDefault(); 
	
   if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
	  VmConfig::loadConfig(); 
	 }

	
	if (!empty($order['details']['BT']))
	if (!empty($order['details']['BT']->order_language))
	 {
	   $lang = $order['details']['BT']->order_language; 
	 }
	 else
	 {
	   $langs = VmConfig::get('active_languages', array($lang)); 
	   foreach ($langs as $lang2)
	    {
		  $lang = $lang2; 
		  break; 
		}
	 } 
	 
	$vmlang = strtolower($lang); 
	$vmlang = str_replace('-', '_', $vmlang); 
    
	if (defined('VMLANG'))
	{
	$vmlang_c = VMLANG; 
    if (empty($vmlang) && (!empty($vmlang_c))) $vmlang = VMLANG; 
	}
	
	if (empty($order['details']['BT'])) return;
	
	$currency = (int)$order['details']['BT']->user_currency_id; 
	if	(empty($currency)) $currency = (int)$order['details']['BT']->order_currency; 
	
	if (!empty($currency))
	{
	  $db = JFactory::getDBO(); 
	  $q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.$currency.' limit 0,1'; 
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	   foreach ($res as $key5=>$val5)
	   {
	    $order['details']['BT']->$key5 = $val5; 
	   }
	}
	$paymenttext = ''; 
	$payment_id = (int)$order['details']['BT']->virtuemart_paymentmethod_id; 
	if (!empty($payment_id))
	 {
		
		$orig1 = JFactory::getApplication()->get('messageQueue', array()); 
		$orig2 = JFactory::getApplication()->get('_messageQueue', array()); 
		
	    		JPluginHelper::importPlugin('vmpayment');
		$_dispatcher = JDispatcher::getInstance();
		$ordercopy = $order; 
		ob_start();  
		$_returnValues = $_dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $order['details']['BT']->virtuemart_order_id,$order['details']['BT']->virtuemart_paymentmethod_id, &$ordercopy));
		
		foreach ($_returnValues as $_returnValue) {
			if ($_returnValue !== null) {
				$paymenttext .= $_returnValue;
			}
		}
		
		JFactory::getApplication()->set('messageQueue', $orig1); 
		JFactory::getApplication()->set('_messageQueue', $orig2); 
		
		$delete = ob_get_clean(); 
	 }
	 
	 $order['details']['BT']->payment_name = $paymenttext; 
	 $shipment = ''; 
	 $shipment_id = $order['details']['BT']->virtuemart_shipmentmethod_id; 
	 if (!empty($shipment_id))
	 {
		ob_start(); 
		$orig1 = JFactory::getApplication()->get('messageQueue', array()); 
		$orig2 = JFactory::getApplication()->get('_messageQueue', array());
		
	   		JPluginHelper::importPlugin('vmshipment');
		$_dispatcher = JDispatcher::getInstance();
		$returnValues = $_dispatcher->trigger('plgVmOnShowOrderFEShipment',array( $order['details']['BT']->virtuemart_order_id,$order['details']['BT']->virtuemart_shipmentmethod_id, $order));
		
		foreach ($returnValues as $returnValue) {
			if ($returnValue !== null) {
			   $shipment .= $returnValue;
				
			}
		}
	   
	   JFactory::getApplication()->set('messageQueue', $orig1); 
	   JFactory::getApplication()->set('_messageQueue', $orig2); 
	   $delete = ob_get_clean(); 

	 }
	$order['details']['BT']->shipment_name = $shipment; 
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	if (OPCmini::tableExists('virtuemart_categories_'.$vmlang))
    if (!empty($order['items']))
    foreach ($order['items'] as $key=>$item)
	{
	  if (empty($item->virtuemart_category_id)) 
	  {
	   $order['items'][$key]->virtuemart_category_id = 0; 
	   $order['items'][$key]->virtuemart_category_name = 0; 
	  
	   continue; 
	  }
	  $db = JFactory::getDBO(); 
	  
	  $cat_id = (int)$item->virtuemart_category_id; 
	  $q = 'select * from `#__virtuemart_categories_'.$vmlang.'` where virtuemart_category_id = '.$cat_id.' limit 0,1'; 
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  if (!empty($res))
	  foreach ($res as $key5=>$val5)
	   {
	    $order['items'][$key]->$key5 = $val5; 
	   }
	}
  
    if (!empty($order['details']['BT']->virtuemart_country_id))
		{
		   $val = (int)$order['details']['BT']->virtuemart_country_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_countries where virtuemart_country_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   if (!empty($res))
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['BT']->$key5 = $val5; 
			}
		}
		
		$order['details']['BT']->state_name = ''; 
		
	
	if (!empty($order['details']['BT']->virtuemart_state_id))
		{
		   $val = (int)$order['details']['BT']->virtuemart_state_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_states where virtuemart_state_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   
		   if (!empty($res))
		   {
		    $emptystate = array(); 
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['BT']->$key5 = $val5; 
			   $emptystate[$key5] = '';   
			}
		   }
		   else
		   {
		     $q = "select * from #__virtuemart_states where 1 limit 0,1"; 
		     $db->setQuery($q); 
		     $res = $db->loadAssoc(); 
			 $emptystate = array(); 
			  foreach ($res as $key5=>$val5)
		      {
			   $order['details']['BT']->$key5 = ''; 
			   $emptystate[$key5] = '';   
			  }
		   }
		}
	$order['details']['bt'] =& $order['details']['BT']; 
	
	$history_sorted = array(); 
	
	if (!empty($order['history']))
	foreach ($order['history'] as $key=>$val)
	{
	  $time=(int)strtotime($order['history'][$key]->modified_on); 
	  if (empty($val)) $val = ''; 
	  if (isset($history_sorted[$time]))
	  $time++; 
	  $history_sorted[$time] = $order['history'][$key]; 
	  //['history_'.$i.'_'.$key] = $val; 
	}
	
	ksort($history_sorted, SORT_NUMERIC); 
	$history_sorted = array_reverse($history_sorted);

	unset($order['history']); 
	$order['history'] = $history_sorted; 
	
	if (empty($order['details']['ST'])) return; 
	
		
		if (empty($order['details']['ST']->email))
		 {
		   $order['details']['ST']->email =  $order['details']['BT']->email; 
		 }
	
    if (!empty($order['details']['ST']->virtuemart_country_id))
		{
		   $val = (int)$order['details']['ST']->virtuemart_country_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_countries where virtuemart_country_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   if (!empty($res))
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['ST']->$key5 = $val5; 
			}
		}
	if (!empty($order['details']['ST']->virtuemart_state_id))
		{
		   $val = (int)$order['details']['ST']->virtuemart_state_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_states where virtuemart_state_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   if (empty($res)) $res = $emptystate;
		   
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['ST']->$key5 = $val5; 
			   
			}
		  
		}		
		$order['details']['st'] =& $order['details']['ST']; 
  }
  
   // returns the domain url ending with slash
 function getUrl($rel = false)
 {
   $url = JURI::root(); 
   if ($rel) $url = JURI::root(true);
   if (empty($url)) return '/';    
   if (substr($url, strlen($url)-1)!='/')
   $url .= '/'; 
   return $url; 
 }

  public static function getNegativeOrder(&$order)
   {
      $totals = array('order_total', 'order_salesPrice', 'order_billTaxAmount', 'order_billTax', 'order_billDiscountAmount', 'order_discountAmount', 'order_subtotal', 'order_tax', 'order_shipment', 'order_shipment_tax', 'order_payment', 'order_payment_tax', 'coupon_discount', 'coupon_code', 'order_discount', 'product_quantity', 'product_subtotal_with_tax'); 
	$product = 	 array ('product_item_price', 'product_final_price', 'product_basePriceWithTax', 'product_discountedPriceWithoutTax', 'product_priceWithoutTax', 'product_subtotal_with_tax', 'product_subtotal_discount', 'product_tax'); 
   
   if (!empty($order['details']['BT']))
   foreach ($order['details']['BT'] as $key=>$val)
     {
	    if (in_array($key, $totals))
		 {
		   $val2 = floatval($val)*(-1); 
		   $order['details']['BT']->$key = $val2; 
		 }
	 }
	if (!empty($order['details']['ST']))
   foreach ($order['details']['ST'] as $key=>$val)
     {
	    if (in_array($key, $totals))
		 {
		   $val2 = floatval($val)*(-1); 
		   $order['details']['ST']->$key = $val2; 
		 }
	 }
	
	if (!empty($order['items']))
   foreach ($order['items'] as $key2=>$item)
   foreach ($item as $key=>$val)
     {
	    if (in_array($key, $totals))
		 {
		   $val2 = floatval($val)*(-1); 
		   $order['items'][$key2]->$key = $val2; 
		 }
	 }
	 
	
	 
	}
}
class OPCtrackingview {
  var $params; 
  var $order; 
  var $vendor; 
  var $pingUrl;  
  var $pingData;
  var $cookieHash; 
  var $error; 
  public function __construct($orderID, $params2, $status, $params, $file='')
  {
    $this->params = new stdClass();
    if (!empty($params))
	$this->params = $params; 
	
	//$this->pingUrl = JRoute::_('index.php?option=com_onepage&task=ping&nosef=1&format=raw&tmpl=component', false); 
	
	$this->pingUrl = OPCtrackingHelper::getUrl().'index.php?option=com_onepage&task=ping&nosef=1&format=raw&tmpl=component'; 
	
	
	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
     $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	$this->cookieHash = $opchash; 
	$this->pingData = 'hash='.$this->escapeSingle(str_replace('&', '&amp;', $opchash)); 
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	$orderModel = OPCmini::getModel('orders');
	$this->order = $orderModel->getOrder($orderID);
	
	if (empty($this->order)) 
	{
	 $this->error = true; 
	 return;
	}
	
	if ((empty($this->order['items'])) || (!is_array($this->order['items']))) 
	{
	$this->error = true; 
	return; 
	}
	
	foreach ($this->order['items'] as $key=>$order_item)
	 {
	    if (empty($order_item->order_item_sku))
		$this->order['items'][$key]->order_item_sku = $this->order['items'][$key]->virtuemart_product_id; 
	 }
	
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
	$negative_statuses = OPCconfig::getValue('tracking_negative', 'negative_statuses', 0, array()); 
	
	if (!empty($negative_statuses))
	{
	$copy_negative_statuses = array(); 
	foreach ($negative_statuses as $key=>$ng)
	 {
	   $copy_negative_statuses[$key] = $ng; 
	 }
	
	$negative_statuses = $copy_negative_statuses; 
	
	
	if (is_array($negative_statuses))
	{
	   if (isset($this->order['details']['BT']))
	   if (in_array($this->order['details']['BT']->order_status, $negative_statuses))
	    {
		   OPCtrackingHelper::getNegativeOrder($this->order); 
		}
	   
	}
	}
	if (empty($this->order['details'])) 
	{
	 
	 $this->error = true; 
	 return;
	}
	
	// check if the tracking was enabled before or after the order was created
	if (is_array($this->order))
	if (!empty($this->order['details']['BT']))
	{
	 $c = $this->order['details']['BT']->created_on; 
	 $time = strtotime($c);
	
	if (!empty(OPCtrackingHelper::$config))
	 if (!empty(OPCtrackingHelper::$config[$status]))
	 {
	   $key = 'since'.$file; 
	   if (!empty(OPCtrackingHelper::$config[$status]->$key))
	    {
		    $since = OPCtrackingHelper::$config[$status]->$key;
			
			if ($since > $time) 
			{
			 $this->error = true; 
			 return;	  
			}
		}
	 }
	 
	
	}
	$this->error = false; 
	$this->pingData .= '&order_status='.$status; 
	$this->pingData .= '&order_id='.$orderID; 
	
	OPCtrackingHelper::getTextFields($this->order); 
	

	$this->vendor = OPCtrackingHelper::getVendorInfo($this->order['details']['BT']->virtuemart_vendor_id); 
	
  }
 
  public function fetchFile($file)
   {
     
     jimport('joomla.filesystem.file');
	 $file = JFile::makeSafe($file);
	 $filei = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'php'.DS.$file.'.php'; 

	 if (!file_exists($filei)) return; 
	 ob_start(); 
     include($filei); 
	 return ob_get_clean(); 
   }
  public function escapeSingle($string)
   {
     $string = str_replace("'", "\'", $string); 
	 // MacOS: 
	 $string =  str_replace("\r\r\n", '\r\n', $string); 
	 return $string; 
   }
  public function escapeDouble($string)
   {
     // in double quotes the end line is not supported
     $string =  str_replace('"', '\"', $string); 
	 $string =  str_replace("\r\r\n", '\r\n', $string); 
	 $string =  str_replace("\r\n", '\n', $string); 
	 $string =  str_replace("\n", ' ', $string); 
	 return $string; 
   }

}

