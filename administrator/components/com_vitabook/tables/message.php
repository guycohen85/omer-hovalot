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
jimport('joomla.database.tablenested');

/**
 * item Table class
 */
class VitabookTableMessage extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param JDatabaseDriver A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__vitabook_messages', 'id', $db);
	}



/**
     * FROM: JTable class
     *
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTable/publish
     * @since   11.1
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('published = ' . (int) $state);

        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time'))
        {
            $query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
            $checkin = true;
        }
        else
        {
            $checkin = false;
        }

        // Build the WHERE clause for the primary keys.
        $query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->query())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);

            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks))
        {
            $this->published = $state;
        }

        $this->setError('');
        return true;
    }

    /**
     * Override for JTable store method to update nulls
     */
    //public function store($updateNulls = true)
    //{
    //    parent::store($updateNulls);
    //}

}
