<?php
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JControllerOrder_export extends JControllerBase
{	
   function getViewName() 
	{ 
		return 'order_export';		
	} 

   function getModelName() 
	{		
		return 'order_export';
	}
	function apply()
	{
	 return $this->save(); 
	}
 function save()  // <-- edit, add, delete 
  {
    
    
    $model = $this->getModel('order_export');
    $reply = $model->store();
    if ($reply===true) {
    $msg = JText::_('COM_ONEPAGE_CONFIGURATION_SAVED');
    } else { $msg = JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
    }
    $link = 'index.php?option=com_onepage&view=order_export';
	
	  $x = JFactory::getApplication()->set('messageQueue', array()); 
			$x = JFactory::getApplication()->set('_messageQueue', array()); 
			$session = JFactory::getSession();
            $sessionQueue = $session->set('application.queue', array());
			
	 $link = 'index.php?option=com_onepage&view=order_export';
    $this->setRedirect($link, $msg); 
  }	
  
function template_update_upload()
{
 require_once(JPATH_COMPONENT.DS.'assets'.DS.'export_helper.php');
 jimport('joomla.filesystem.file');
 $file = "";
 $msg = '';
 foreach ($_FILES as $k=>$v)
 {
 // $msg .= 'key: '.$k.'<br />';
 // $msg .= 'val: '.$v.'<br />';
  if ((strpos($k, 'uploadedupdatefile_')!==false) && (!empty($_FILES[$k]['name'])))
  $file = $k;
 }
 
 $arr = explode('_', $file);
 if (count($arr)>1)
 {
 $tid = $arr[1];
 if (!is_numeric($tid)) return "Error!";
 // get previous file
 $ehelper = new OnepageTemplateHelper();
 $tt = $ehelper->getTemplate($tid);
 $target_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS.'templates'.DS;
 $newname = JFile::makesafe(basename( $_FILES['uploadedupdatefile_'.$tid]['name']));
 if (file_exists($target_path.$newname) && ($tt['file'] != $newname))
 {
   $msg = 'Another theme is using the same filename'; 
 }
 else
 {
 if (file_exists($target_path.$tt['file']))
 {
  if (!JFile::delete($target_path.$tt['file']))
   $msg .= 'Could not remove old template file: '.$tt['file'].'<br />';
 }
 
 $msg .= $ehelper->updateFileName($tid, $newname);
 if (!empty($msg))
 {
 //$userfile = JRequest::getVar('uploadedupdatefile_'.$tid, null, 'files');
 //var_dump($userfile); die();
 $target_path = $target_path . $newname; 
 //echo $target_path.'<br />'; var_dump($_FILES); die();
 if(JFile::upload($_FILES[$file]['tmp_name'], $target_path)) {
    $msg .=  "The template file ".  $newname. 
    " has been uploaded";
	} else{
    $msg .= "There was an error uploading the file, please try again! file: ".$newname;
	}
 }
 }
  }
  if (empty($msg)) $msg = 'O.K.'; 
  //@JFile::delete($_FILES[$file]['tmp_name']); 
  $link = 'index.php?option=com_onepage&view=order_export';
  $this->setRedirect($link, $msg); 
return $msg;
 //die('som tu');
}


function template_upload()
{
 require_once(JPATH_COMPONENT.DS.'assets'.DS.'export_helper.php');
 $target_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS.'templates'.DS;
 
 $userfile = JRequest::getVar('uploadedfile', null, 'files');
 //var_dump($userfile); die();
 jimport('joomla.filesystem.file'); 
 $file = JRequest::getVar('uploadedfile', null, 'files', 'array'); 
 $filename = JFile::makeSafe($file['name']); 
 $src = $file['tmp_name']; 
 
 // $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
 $target_path .= $filename; 
 // if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) 
 //echo $target_path.'<br />'; var_dump($_FILES); die();
 if (JFile::upload($src, $target_path))
 {
    $msg =  "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
    
    
} else{
    $msg = "There was an error uploading the file, please try again!";
}
 @JFile::delete($_FILES[$file]['tmp_name']); 
 $link = 'index.php?option=com_onepage&view=order_export';
  $this->setRedirect($link, $msg); 
return $msg;
 //die('som tu');
}

	
   

}
