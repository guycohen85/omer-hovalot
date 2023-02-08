<?php
/**
 * @version		$Id: contact.php 21555 2011-06-17 14:39:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerUtils extends JControllerBase
{
     function getViewName() 
	{ 
		return 'utils';		
	} 

   function getModelName() 
	{		
		return 'utils';
	}
	function movemenu()
	{
	  $model = $this->getModel('utils');
	  $msg = $model->movemenu(); 
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	  
	}
	
	function errorlog()
	{
	    $error_log = @ini_get('error_log'); 
		$maxlines = 1000; 
		if (!empty($error_log))
		if (file_exists($error_log))
            {
			   if (function_exists('apache_setenv'))
	@apache_setenv('no-gzip', 1);
	$_ENV['no-gzip'] = 1; 
	header("Content-Type: text/html"); 
	@ignore_user_abort(false);
	if (!isset($timelimit)) $timelimit = 29; 
	@ini_set('ignore_user_abort', false); 
	@set_time_limit($timelimit);
	@ini_set('output_buffering', 0); 
    @ini_set('zlib.output_compression', 0);
	echo '<html><head></head><body>'; 
	// clear any possible buffers: 
	echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); 
	
	//$handle = fopen($error_log, "r") or die("Couldn't get handle");
	$time = time(); 
	echo JText::_('COM_ONEPAGE_VIEWPHPERRORLOG_NOTE')."<br />\n"; 
	echo JText::_('COM_ONEPAGE_VIEWPHPERRORLOG_READING').' '.$error_log."<br />\n"; 
	
	$fl = fopen($error_log, "r") or die('Not found');
	for($x_pos = 0, $ln = 0, $output = ''; fseek($fl, $x_pos, SEEK_END) !== -1; $x_pos--) {
    $char = fgetc($fl);
    if ($char === "\n") {
        // analyse completed line $output[$ln] if need be
       
		
		if (stripos($output, 'Fatal')!==false)
		 {
		   $output = '<b style="color: red;">'.$output.'</b><br />'; 
		 }
		 
		 if (stripos($output, 'Warning')!==false)
		 {
		   $output = '<b style="color: blue;">'.$output.'</b><br />'; 
		 }
		if (empty($output)) continue; 
		$ln++;
	    echo $ln.': '.$output."<br />\n"; 
		$output = ''; 
		flush(); 
        continue;
        }
	 if ($char === "\r") continue; 
     $output = $char . $output; 
    
	 $now = time(); 
     if (($now - $time) > $timelimit) 
	 {
	 fclose($fl);
	 die('Timeout'); 
	 }
	  
	 if ($ln >= $maxlines) 
	 {
	 
	 fclose($fl);
	 die('Max lines reached'); 
	 }
	
	}
   fclose($fl);
	
	
    echo '</body></html>'; 
	die("\nEOF\n"); 
			}
			
			die('Not found'); 
	}
	
	function searchtext()
	{
	  $model = $this->getModel('utils');
	  
	  $search = $model->searchtext(); 
	  $session = JFactory::getSession(); 
	  $session->set('opcsearch', $search); 
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	  
	}
	
	function ajax()
	{
	return;
		$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();
		ob_start(); 
		$model = $this->getModel('utils');
		
		$command = JRequest::getCmd('command'); 
		
		if ($command == 'editcss')
		{
			
			$model = $this->getModel('edittheme');
			$model->updateColors(); 
			
			$file = JRequest::getCmd('file'); 
			$files = $model->getCss(); 
			foreach ($files as $f)
			{
			 if (md5($f)==$file) 
			 {
			 $myfile = $f; 
			 break; 
			  }
			}
			if (!empty($myfile))
			{
			  $myfile2 = strtolower($myfile);
			  if (substr($myfile2, -4)!='.css') return; 
		      echo file_get_contents($myfile); 
			}

			
		}
		
		if ($command == 'savecss')
		{
			 
			$file = JRequest::getCmd('file'); 
			$files = $model->getCss(); 
			foreach ($files as $f)
			{
		     
			 if (md5($f)==$file) 
			 {
			 $myfile = $f; 
			 break; 
			  }
			}
			
			if (!empty($myfile))
			{
			  $myfile2 = strtolower($myfile);
			  if (substr($myfile2, -4)!='.css') return; 
			  {
				 
			     //echo file_get_contents($myfile); 
				  //$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
				 $css = JRequest::getVar('css', '', 'post', 'STRING', JREQUEST_ALLOWRAW);
				 if (!empty($css))
				 {
					 $css = str_replace("\r\r\n", "\r\n", $css); 
					 $css = str_replace("\xEF\xBB\xBF", "", $css); 
					 //echo $css; die(); 
					 JFile::write($myfile, $css); 
					 echo 'OPC_OK'; 
				 }
			  }
			}
		}
		
		if (($command == 'preview') || ($command == 'savepreview'))
		{
		
			$model = $this->getModel('edittheme');
			$model->updateColors(); 
		}
		
		if ($command == 'savepreview')
		{
			$model->createCustom(); 
			
		}
		JFactory::getApplication()->close(); 
		
	}

	
}
