<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.viewlegacy');

/**
 * HTML View class for the Vitabook component
 */
class VitabookViewVitabook extends JViewLegacy
{
    protected $messages;
    protected $params;
    protected $pagination;

    function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $this->params = $app->getParams('com_vitabook');

        // only messages (AJAX)
        if($tpl=='messages')
        {
            $model = $this->getModel();
            $parent_id = JFactory::getApplication()->input->get('parent_id', null, 'int');
            $start = JFactory::getApplication()->input->get('start', null, 'int');
            $this->messages = $model->getChildren(array($parent_id), $start);
            $this->messages = $model->sortChildMessages();
        }
        // only message (AJAX)
        elseif($tpl=='message')
        {
            $model = $this->getModel();
            $this->messages = array();
            $this->messages[] = $model->getItem(JFactory::getApplication()->input->get('messageId', null, 'int'));
            $tpl = 'messages';
        }
        // If necessary, load avatar layout
        elseif(($tpl == 'avatar') && ($this->params->get('vbAvatar') == 1) && (JFactory::getUser()->get('id') != 0))
        {
            $this->avatar = VitabookHelperAvatar::messageAvatar((object)array('jid'=>JFactory::getUser()->get('id')));
        }
        // display the guestbook
        else
        {
            $this->pagination = $this->get('Pagination');
            // Get the data from the vitabook model
            $this->messages = $this->get('Items','vitabook');
            // Get the form from the message model
            $this->form     = $this->get('Form','message');
        }

        // load legacy templates if joomla version < 3.0.0
        $jversion = new JVersion();
        if( version_compare( $jversion->getShortVersion(), '3.0.0', 'lt' ) ) {
                $tpl .= "Legacy";
        }

        parent::display($tpl);
    }
}
