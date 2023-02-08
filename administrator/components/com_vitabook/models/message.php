<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Vitabook model.
 */
class VitabookModelMessage extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_VITABOOK';

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.7
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_vitabook.message.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Message', $prefix = 'VitabookTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
        $jinput = JFactory::getApplication()->input;
        $parentId = $jinput->getInt('parent_id');
		$this->setState('message.parent_id', $parentId);

		// Load the User state.
        $pk = $jinput->getInt('id');
		$this->setState($this->getName() . '.id', $pk);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_vitabook');
		$this->setState('params', $params);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_vitabook.message', 'message', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		//-- Check if user can edit message state
		$user = JFactory::getUser();
		if(!$user->authorise('core.edit.state', 'com_vitabook'))
		{
			$form->removeField('published');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_vitabook.edit.message.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
			$user = JFactory::getUser();

			//-- Auto fill some fields of new messages
			if(empty($data->id))
			{
                //-- Current date/time
                $config         = JFactory::getConfig();
                $data->date 	= JFactory::getDate('',$config->get('config.offset'))->toSQL(true);
				$data->ip 		= $_SERVER['REMOTE_ADDR'];

				if(!$data->get('guest'))
				{
					$data->jid 		= (int) $user->id;
					$data->name 	= $user->name;
					$data->email 	= $user->email;
				}
			}
		}
		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   11.1
	 */
	public function save($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			// Set the new parent id if parent id not matched OR while New/Save as Copy .
			if ($table->parent_id != $data['parent_id'] || $data['id'] == 0)
			{
				$table->setLocation($data['parent_id'], 'last-child');
			}

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}

    /**
     * Method to validate the form data. Override JModelForm to preserve youtube and vimeo iframes/div-objects
     *
     * @param   object  $form   The form to validate against.
     * @param   array   $data   The data to validate.
     * @param   string  $group  The name of the field group to validate.
     *
     * @return  mixed  Array of filtered data if valid, false otherwise.
     *
     * @see     JFormRule
     * @see     JFilterInput
     * @since   11.1
     */
    public function validate($form, $data, $group = null)
    {
        
   
        // detect embedded youtube/vimeo iframes
       if(JFactory::getUser()->authorise('vitabook.insert.video', 'com_vitabook')){
            if(strpos($data['message'],'<iframe src="http://www.youtube.com/embed/') !== false || strpos($data['message'],'<iframe src="http://player.vimeo.com/video/') !== false)
            {
                // initialize variables
                $youtube = false; 
                $vimeo = false;
                // store youtube/vimeo video ids
                preg_match_all('#(?<=youtube.com\/embed\/)[a-zA-Z0-9_-]+(?=\?wmode)#', $data['message'], $youtube);
                preg_match_all('#(?<=player.vimeo.com\/video\/)[\d]+(?=" width)#', $data['message'], $vimeo);

                // replace iframes in message with temporary markers
                if(!empty($youtube))
                {
                    foreach ($youtube[0] as $key => $videoId):
                        $pattern = '/<iframe src="http:\/\/www.youtube.com\/embed\/'.$videoId.'\?wmode=opaque" width="350(px)?" height="300(px)?"><\/iframe>/';
                        $data['message'] = preg_replace($pattern, '##youtube_'.$key, $data['message']);
                    endforeach;
                }
                if(!empty($vimeo))
                {
                    foreach ($vimeo[0] as $key => $videoId):
                        $pattern = '/<iframe src="http:\/\/player.vimeo.com\/video\/'.$videoId.'" width="350(px)?" height="300(px)?"><\/iframe>/';
                        $data['message'] = preg_replace($pattern, '##vimeo_'.$key, $data['message']);
                    endforeach;
                }
            }
        }

//TODO: This ugly hack has to go, someday ;-)
        // (1/2) ugly hack to make possible that site is edited to be empty. Override for JTable store method ($updateNulls=true) would be preferable, but imposes an unnecessary db-scheme change
        if(JComponentHelper::getParams('com_vitabook')->get('vbForm_site') && $data['site'] == '')
        {
            $data['site'] = "http://clear.clr";
        }

 		// Get the data cleaned and validated by proxying the task to JModelForm
		$data = parent::validate($form, $data, $group);

        // (2/2) ugly hack to make possible that site is edited to be empty. Override for JTable store method ($updateNulls=true) would be preferable, but imposes an unnecessary db-scheme change
        if(JComponentHelper::getParams('com_vitabook')->get('vbForm_site') && $data['site'] == 'http://clear.clr')
        {
            $data['site'] = "";
        }

        // if necessary, restore iframes at the temporary markers
        if(!empty($youtube))
        {
            foreach ($youtube[0] as $key => $videoId):
                $pattern = '/(##youtube_'.$key.')/';
                $data['message'] = preg_replace($pattern, '<iframe src="http://www.youtube.com/embed/'.$videoId.'?wmode=opaque" width="350px" height="300px"></iframe>', $data['message']);
            endforeach;
        }
        if(!empty($vimeo))
        {
            foreach ($vimeo[0] as $key => $videoId):
                $pattern = '/(##vimeo_'.$key.')/';
                $data['message'] = preg_replace($pattern, '<iframe src="http://player.vimeo.com/video/'.$videoId.'" width="350px" height="300px"></iframe>', $data['message']);
            endforeach;
        }

        return $data;
    }

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   11.1
	 */
	public function delete(&$pks)
	{
		$pks = (array) $pks;
		// sort message ids from high to low to prevent parents being deleted before children
		rsort($pks);
		return parent::delete($pks);
	}


    
	/**
	 * Method to activate messages.
	 *
	 * @param   array    &$pks   The ids of the items to publish.
	 * @param   integer  $value  The value of the published state
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function activate(&$pks, $value = 1)
	{
		$user		= JFactory::getUser();

		$table		= $this->getTable();
		$pks		= (array) $pks;

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$table->activated = (int) $value;
                // Allow an exception to be thrown.
                try
                {
                    if (!$table->check())
                    {
                        $this->setError($table->getError());
                        return false;
                    }

                    // Store the table.
                    if (!$table->store())
                    {
                        $this->setError($table->getError());
                        return false;
                    }
                }
                catch (Exception $e)
                {
                    $this->setError($e->getMessage());

                    return false;
                }
            }
            else
            {
                // Prune items that you can't change.
                unset($pks[$i]);
                JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
            }
		}

		return true;
	}
}
