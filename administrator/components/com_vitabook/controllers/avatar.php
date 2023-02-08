<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

//-- No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.image.image');

class VitabookControllerAvatar extends JControllerLegacy
{
    protected $view_list = 'avatars';
    protected $user;

	public function __construct($config = array())
	{
	    parent::__construct($config);

	    // Set the option as com_NameOfController.
	    if (empty($this->option))
	    {
	        $this->option = 'com_vitabook';
	    }

        $this->user = JFactory::getUser();
    }

	/**
	 * Method to display avatar-form in squeezbox.
	 * @return	JController		This object to support chaining.
	 */
    public function edit()
    {  
        //-- Get/Create the view
        $view = $this->getView('avatars','html');
        //-- Get/Create the models
        $view->setModel($this->getModel('avatars'), true);
        //-- We only want the form, no joomla
        $jinput = JFactory::getApplication()->input;
        $jinput->set('tmpl', 'component');

		//-- Display the view
		$view->display('avatar');
		return $this;
    }

   /**
    * Method to upload avatar
    * @return redirect to close modal box
    */
	public function upload()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        if(!$this->user->authorise('core.edit', 'com_vitabook')) {
			$this->closebox(JText::_('COM_VITABOOK_AVATAR_UPLOAD_NOT_AUTHORIZED'));
			return false;
        }

        //-- What is the user id
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->getInt('jid');

		//-- Check if image is uploaded
		if(isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"]) && isset($id))
		{
			//-- Check if file type is supported
			$types = array('image/gif', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png');
			if(!in_array($_FILES["image"]["type"], $types)) {
				//-- Unknown format
                $this->closebox(JText::_('COM_VITABOOK_UNKNOWN_FORMAT'));
                return false;
			}
			try {
				$fileInfo = JImage::getImageFileProperties($_FILES["image"]["tmp_name"]);
			}
			catch(Exception $e) {
				//-- Unknown format
				$this->closebox(JText::_('COM_VITABOOK_UNKNOWN_FORMAT'));
				return false;
			}

			//-- If image is uploaded, create JImage object and load image.
			$image = new JImage;
			try {
				$image->loadFile($_FILES["image"]["tmp_name"]);
			}
			//-- Loading image failed, stop!
			catch(Exception $e) {
				$this->closebox(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
				return false;
			}
			//-- Loading image failed, stop!
			if(!$image->isLoaded()) {
				$this->closebox(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
				return false;
			}
		}
		else
		{
			//-- Upload failed, stop!
            $this->closebox(JText::_('COM_VITABOOK_POST_FAILED'));
            return false;
		}

        //-- Check for memory
        if(!VitabookHelper::checkMemory($image)) {
            $this->closebox(JText::_('COM_VITABOOK_UPLOADING_FAILED_MEMORY'));
            return false;
        }

		//-- Where to store the avatar
		$dest = JPATH_SITE.'/media/com_vitabook/images/avatars/'.$id.'.png';

		//-- Check if destination folder exists
		if(!JFolder::exists(JPATH_SITE.'/media/com_vitabook/images/avatars/')) {
            $this->closebox(JText::_('COM_VITABOOK_NO_FOLDER'));
            return false;
		}
		//-- If user already has avatar. This image is renamed to old-filename.png
		if(JFile::exists($dest)) {
			rename($dest,JPATH_SITE.'/media/com_vitabook/images/avatars/old-'.$id.'.png');
		}

		//-- Resize and crop image to 100x100px
		try {
			$image->resize(100,100,false,1);
		}
		//-- Loading image failed, stop!
		catch(Exception $e) {
            $this->closebox(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
            return false;
		}

		//-- Copy the renamed file to media/com_vitabook/images.
		@$image->toFile($dest, 'IMAGETYPE_PNG');
		if(JFile::exists($dest)) {
			$this->closebox();
			return true;
		} else {
            $this->closebox(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
            return false;
		}
    }

   /**
    * Method to delete avatar
    * @return redirect to close modal box
    */
	public function delete()
	{        
        if($this->user->authorise('core.delete', 'com_vitabook')) {
            //-- Avatars location
            $jinput = JFactory::getApplication()->input;
            $id = $jinput->getInt('id');
            $dest = JPATH_SITE.'/media/com_vitabook/images/avatars/'.$id.'.png';

            //-- Avatars are not deleted completely, but renamed to old-filename.png
            if(JFile::exists($dest)) {
                rename($dest,JPATH_SITE.'/media/com_vitabook/images/avatars/old-'.$id.'.png');
            } else {
                $this->closebox(JText::_('COM_VITABOOK_AVATAR_DELETE_ERROR'));
                return false;
            }
        }

		$this->closebox();
		return true;
	}

    /**
    * Method to delete multiple avatars
    */
	public function deleteList()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        if($this->user->authorise('core.delete', 'com_vitabook')) {
            //-- Avatars location
            $jinput = JFactory::getApplication()->input;
            $cid = $jinput->get('cid', array(), 'array');

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);
            
            foreach($cid as $id) {
                $dest = JPATH_SITE.'/media/com_vitabook/images/avatars/'.$id.'.png';
                //-- Avatars are not deleted completely, but renamed to old-filename.png
                if(JFile::exists($dest)) {
                    rename($dest,JPATH_SITE.'/media/com_vitabook/images/avatars/old-'.$id.'.png');
                }
            }

            $message = false;
            $type = 'message';
        } else {
            $message = JText::_('COM_VITABOOK_AVATAR_DELETE_ERROR');
            $type = 'error';
        }

		$this->setRedirect(JRoute::_('index.php?option=com_vitabook&view=' . $this->view_list, $message, $type));
	}

   /**
    * Method to close a modal box and display error message
    */
    private function closebox($message=false)
    {
        if(!empty($message)) {
            echo "<script type='text/javascript'>alert('".$message."');</script>";
        }
        echo "<script type='text/javascript'>window.parent.location.href=window.parent.location.href;window.parent.SqueezeBox.close();</script>";
    }
}
