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

jimport('joomla.application.component.controllerform');

class VitabookControllerMessage extends JControllerForm
{
	protected $view_list = 'messages';
	
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_vitabook'))
		{
			return true;
		}
		
		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_vitabook'))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);
				if (empty($record))
				{
					return false;
				}
				$ownerId = $record->jid;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
		
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
}