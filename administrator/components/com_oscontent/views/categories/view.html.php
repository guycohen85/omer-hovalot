<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.1
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/views/view.php';

/**
 * Mass Category View
 *
 * @since  1.0.0
 */
class OSContentViewCategories extends OSView
{
    /**
     * Method to display the view
     *
     * @param   string $tpl Template file
     *
     * @access    public
     * @return  void
     */
    public function display($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_OSCONTENT_MASS_CATEGORIES'), 'oscontent.png');
        JToolBarHelper::apply("categories.save");
        JToolbarHelper::cancel('categories.cancel');
        JToolBarHelper::divider();
        JToolBarHelper::spacer();
        JToolBarHelper::preferences('com_oscontent');

        // Get component params
        $params = JComponentHelper::getParams('com_oscontent');

        // Get data
        $lists = $this->get('Data');

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $this->assignRef('params', $params);
            $this->assignRef('lists', $lists);
        } else {
            $this->params = $params;
            $this->lists  = $lists;
        }

        parent::display($tpl);
    }
}
