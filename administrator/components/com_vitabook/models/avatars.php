<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

//Import filesystem libraries.
jimport('joomla.filesystem.file');

/**
 * Methods supporting a list of Vitabook records.
 */
class VitabookModelAvatars extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'name',
                'username',
				'email'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   11.1
     */
	public function getItems()
	{
		$avatars = parent::getItems();
		foreach ($avatars as &$avatar) {
            //-- Create avatar link       
            $avatar->avatar = VitabookHelperAvatar::getAvatarUrl((object) array('jid'=>$avatar->id));
            
            //-- Create edit url
            $avatar->url = JRoute::_('index.php?option=com_vitabook&task=avatar.edit&id=' . (int) $avatar->id);
		}
		return $avatars;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('name', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		//-- Create a new query object.
		$query = parent::getListQuery();

		$query->select('id,name,username,email');
		$query->from('#__users');

		//-- Search
		$search = $this->getState('filter.search');

		$db = $this->getDbo();

		if (!empty($search)) {
			$search = '%' . $db->escape($search, true) . '%';

			$field_searches =
				"(name LIKE '{$search}' OR " .
				"username LIKE '{$search}' OR " .
				"email LIKE '{$search}')";

			$query->where($field_searches);
		}

		//-- Column ordering
		$listOrdering = $this->getState('list.ordering');
		$listDirn = $db->escape($this->getState('list.direction'));

		//-- Standard order is 'name asc', as stated in the populateState method.
		$query->order($db->escape($listOrdering.' '.$listDirn));
		$query->order('name');

		return $query;
	}

    /**
     * Method to get the vitabook-avatar link for this user
     * @return link to avatar
     */
	public function getAvatar()
	{
        $jinput = JFactory::getApplication()->input;
        $avatar = new stdClass;
        $avatar->id = $jinput->getInt('id');

        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__users');
        $query->where('id = '.$avatar->id);
        $db->setQuery((string)$query);
        $avatar->name = $db->loadResult();

		$path = JPATH_SITE.'/media/com_vitabook/images/avatars/'.$avatar->id.'.png';
        $avatar->url = VitabookHelperAvatar::getAvatarUrl((object) array('jid'=>$avatar->id));
        
        return $avatar;
	}
}
