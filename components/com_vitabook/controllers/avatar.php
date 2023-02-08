<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerlegacy');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.image.image');

/**
 * Avatar controller class.
 */
class VitabookControllerAvatar extends JControllerLegacy
{
	/**
	 * Method to display avatar-form in squeezbox.
	 * @return	JController		This object to support chaining.
	 */
    public function viewform()
    {
        //-- Get/Create the view
        $view = $this->getView('vitabook','html');
        //-- Get/Create the models
        $view->setModel($this->getModel('vitabook'), true);
        //-- We only want the form, no joomla
        JFactory::getApplication()->input->set('tmpl', 'component');

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
		//-- Guests cannot upload avatars
		if(JFactory::getUser()->get('id') == 0) {
			$this->closebox(JText::_('COM_VITABOOK_AVATAR_UPLOAD_ERROR_GUEST'));
			return false;
		}

		//-- Get avatar parameters
		$params = JComponentHelper::getParams('com_vitabook');
		$source = $params->get('vbAvatar');

		if($source != 1) {
            $this->closebox(JText::_('COM_VITABOOK_AVATAR_UPLOAD_ERROR_NOTACTIVATED'));
            return false;
		}

		//-- Check if image is uploaded
		if(isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"]))
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

		//-- Create filename
		$fileName = JFactory::getUser()->get('id');
		//-- Where to store the avatar
		$dest = JPATH_BASE.'/media/com_vitabook/images/avatars/'.$fileName.'.png';
		$loc = JUri::root().'media/com_vitabook/images/avatars/'.$fileName.'.png';

		//-- Check if destination folder exists
		if(!JFolder::exists(JPATH_BASE.'/media/com_vitabook/images/avatars/')) {
            $this->closebox(JText::_('COM_VITABOOK_NO_FOLDER'));
            return false;
		}
		//-- If user already has avatar. This image is renamed to old-filename.png
		if(JFile::exists($dest)) {
			rename($dest,JPATH_BASE.'/media/com_vitabook/images/avatars/old-'.$fileName.'.png');
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
		//-- Avatars location
		$fileName = JFactory::getUser()->get('id');
		$dest = JPATH_BASE.'/media/com_vitabook/images/avatars/'.$fileName.'.png';

		//-- Avatars are not deleted completely, but renamed to old-filename.png
		if(JFile::exists($dest)) {
			rename($dest,JPATH_BASE.'/media/com_vitabook/images/avatars/old-'.$fileName.'.png');
		} else {
            $this->closebox(JText::_('COM_VITABOOK_AVATAR_DELETE_ERROR'));
            return false;
		}

		$this->closebox();
		return true;
	}

   /**
    * Method to close a modal box and display error message
    */
    private function closebox($message)
    {
        if(isset($message)) {
            echo "<script type='text/javascript'>alert('".$message."');</script>";
        }
        echo "<script type='text/javascript'>window.parent.location.href=window.parent.location.href;window.parent.SqueezeBox.close();</script>";
    }
}
