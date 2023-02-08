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

class JControllerConfig extends JControllerBase
{	
   function getViewName() 
	{ 
		return 'config';		
	} 

   function getModelName() 
	{		
		return 'config';
	}
	
	function installopcext()
	{
	  $link = 'index.php?option=com_onepage'; 
	  $msg = 'Not implemented'; 
	  $this->setRedirect($link, $msg);
	
	  $task2 = JRequest::getVar('task2', ''); 
	  die($task2); 
	}
   
	
	function installext()
	{
	  $link = 'index.php?option=com_onepage'; 
	  $msg = 'Not implemented'; 
	  $this->setRedirect($link, $msg);
	}
	
    function changelang()
	{
  
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->store();
    if ($reply===true) {
    $msg = JText::_('COM_ONEPAGE_CONFIGURATION_SAVED');
    } else { $msg = JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
    }
    $link = 'index.php?option=com_onepage';
	
	$opc_lang = JRequest::getVar('opclang', ''); 
	$link .= '&opclang='.$opc_lang; 
    $this->setRedirect($link, $msg); 	      
	
	}
	
	function perlangedit()
	{
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  $l = JRequest::getVar('payment_per_lang', ''); 
	  $url = 'index.php?option=com_onepage&view=payment&langcode='.$l; 
	  $this->setRedirect($url, $reply);
	}
	
		
	function removepatchusps()
	{
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  
	  $reply = $model->removepatchusps();
	  
	  $url = 'index.php?option=com_onepage'; 
	  $this->setRedirect($url, $reply);
	}
	function patchusps()
	{
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  $reply = $model->patchusps();
	  $url = 'index.php?option=com_onepage'; 
	  $this->setRedirect($url, $reply);
	}
	
	function langcopy()
	{
	   $model = $this->getModel('config');
	   $link = 'index.php?option=com_onepage';
	   $model->copylang(); 
	   $msg = 'OK, tables copied.';
       $this->setRedirect($link, $msg);
	  
	}
		
	function langedit()
	{
	  $from = JRequest::getVar('tr_fromlang', 'en-GB'); 
	  $to = JRequest::getVar('tr_tolang', 'en-GB'); 
	   $tr_type = JRequest::getVar('tr_type', 'site'); 
	  $xt = JRequest::getVar('tr_ext_'.$tr_type, 'com_onepage.ini'); 
	  $tr_type = JRequest::getVar('tr_type', 'site'); 
	  
	  $url = 'index.php?option=com_onepage&view=edit&tr_fromlang='.$from.'&tr_tolang='.$to.'&tr_ext='.$xt.'&tr_type='.$tr_type; 
	  
	  $this->setRedirect($url);
	  /*
	  die('ok'); 
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  $l = JRequest::getVar('payment_per_lang', ''); 
	  */
	  
	}
	
    
    
    function cleanupdb()
    {
     
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->cleanupdb();
    if ($reply===true) {
    $msg = JText::_('Clean Up sucessfull');
    } else { $msg = 'Error'; 
    }
    $link = 'index.php?option=com_onepage';
    //$msg = unserialize($_SESSION['onepage_err']);
    if (empty($msg)) $msg = 'Clean Up O.K.';
    $this->setRedirect($link, $msg);

    }

    function restorebasket()
    {
     JRequest::setVar( 'view', '[ config ]' );
     JRequest::setVar( 'layout', 'default'  );  
    
     $model = $this->getModel('config');
     $reply = $model->restorebasket();
     if ($reply===true) {
     $msg = JText::_('Basket.php restored sucessfully');
     } else { $msg = 'Error'; 
     }
     $link = 'index.php?option=com_onepage';
     $this->setRedirect($link, $msg);

    }


    function template_update_upload()
    {
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    $model = $this->getModel('config');
    $reply = $model->template_update_upload();
    $link = 'index.php?option=com_onepage';
    $this->setRedirect($link, $reply);
    }
    
    function template_upload()
    {
    
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    $model = $this->getModel('config');
    $reply = $model->template_upload();
    $link = 'index.php?option=com_onepage';
    $this->setRedirect($link, $reply);
    }

    function install_ps_order()
    {
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->install_ps_order();
    if ($reply===true) {
    $msg = JText::_('Installation sucessfull');
    } else { $msg = 'Error while installation.'; 
    }
    $link = 'index.php?option=com_onepage';
    //$msg = unserialize($_SESSION['onepage_err']);
    if (empty($msg)) $msg = 'Installtion O.K.';
    $this->setRedirect($link, $msg);

    }
    function install()
    {

    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->install();
    if ($reply===true) {
    $msg = JText::_('Installation sucessfull');
    } else { $msg = 'Error while installation.'; 
    }
    $link = 'index.php?option=com_onepage';
	
	//remove message queue from VM: 
		  
    //$msg = $_SESSION['onepage_err'];
    //if (empty($msg)) $msg = 'Installtion O.K.';
    $this->setRedirect($link, $msg);


    }  
	function apply()
	{
		return $this->save(); 
	}
	
	function rename_theme()
	{
	  JRequest::setVar('orig_selected_template', JRequest::getVar('selected_template')); 
	  JRequest::setVar('selected_template', JRequest::getVar('selected_template').'_custom'); 
	  JRequest::setVar('rename_to_custom', true); 
	  return $this->save(); 
	}
   function save()  // <-- edit, add, delete 
  {
    
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->store();
    if ($reply===true) {
    $msg = JText::_('COM_ONEPAGE_CONFIGURATION_SAVED');
    } else { $msg = JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
    }
    $link = 'index.php?option=com_onepage';
	
	$opc_lang = JRequest::getVar('opclang', ''); 
	$link .= '&opclang='.$opc_lang; 
	
	/*
	$y = JFactory::getApplication()->get('_messageQueue', array());
	
	

	
	  $x = JFactory::getApplication()->set('messageQueue', array()); 
			$x = JFactory::getApplication()->set('_messageQueue', array()); 
			$session = JFactory::getSession();
            $sessionQueue = $session->set('application.queue', array());
	*/
	
    $this->setRedirect($link, $msg); 
  }

}
