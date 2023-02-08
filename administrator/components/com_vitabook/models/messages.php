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

/**
 * Methods supporting a list of Vitabook records.
 */
class VitabookModelMessages extends JModelList
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
				'email',
				'message',
				'published',
                'activated',
				'date',
				'site',
                'location'
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
		$messages = parent::getItems();

		foreach ($messages as &$message) {
            $message->message = substr(strip_tags($message->message), 0,35); // Shorten messages to 35 characters
            $message->url = JRoute::_('index.php?option=com_vitabook&task=message.edit&id=' . (int) $message->id); // Create edit link
            $message->date = VitabookHelper::formatDate($message); // format date according to settings
		}
		
		return $messages;
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

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('rgt', 'DESC');
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

		$query->select('*');
		$query->from('#__vitabook_messages');

		//-- We don't want the root
		$query->where('(parent_id > 0)');

		//-- Filter state
		$published = $this->getState('filter.published');

		if ($published == '') {
			//$query->where('(published = 1 OR published = 0)');
		} else if ($published != '*') {
			$published = (int) $published;
			$query->where("published = '{$published}'");
		}

		//-- Search
		$search = $this->getState('filter.search');

		$db = $this->getDbo();

		if (!empty($search)) {
			$search = '%' . $db->escape($search, true) . '%';

			$field_searches =
				"(message LIKE '{$search}' OR " .
				"name LIKE '{$search}' OR " .
				"email LIKE '{$search}')";

			$query->where($field_searches);
		}

		//-- Column ordering
		$listOrdering = $this->getState('list.ordering');
		$listDirn = $db->escape($this->getState('list.direction'));

		//-- Standard order is 'rgt desc', as stated in the populateState method.
        $query->order($db->escape($listOrdering.' '.$listDirn));
        $query->order('rgt');

		return $query;
	}
}
