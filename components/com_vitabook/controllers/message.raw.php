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

jimport('joomla.application.component.controllerform');

/**
 * Message controller class.
 */
class VitabookControllerMessage extends JControllerForm
{

    private $canDo;

    public function __construct($config = array())
    {
        parent::__construct($config);
        // Define standard task mappings.
        $this->registerTask('unpublish', 'publish'); // value = 0

        // Set the option as com_NameOfController.
        if (empty($this->option))
        {
            $this->option = 'com_vitabook';
        }

        // get users actions
        $this->canDo = VitabookHelper::userActions();
    }

    /**
     * Method to retrieve a message.
     * @return      JController             This object to support chaining.
     */
    public function getMessage()
    {
        // Check for request forgeries.
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
        // Get/Create the view
        $view = $this->getView('Vitabook', 'html');
        // Get/Create the models
        $view->setModel($this->getModel('message'), true);
        // we only want messages, no joomla
        JFactory::getApplication()->input->set('tmpl', 'component');
        // Display the view
        $view->display('message');
        return $this;
    }

    /**
     * Method to save a message
     *
     * @return  json array
     *   state   int     0 if unsuccessfull, 1 if successfull, 2 if moderation is required
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(json_encode(array("state"=>0, "result" => JText::_('JINVALID_TOKEN'))));

        // Initialise variables.
        $app = JFactory::getApplication();
        $lang = JFactory::getLanguage();
        $model = $this->getModel('message');
        $table = $model->getTable();
        $data = JFactory::getApplication()->input->get('jform', null, 'array');
        $context = "$this->option.edit.$this->context";
        $task = $this->getTask();

        // Set the MIME type for JSON output.
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // Populate the row id from the session.
        $recordId = $data[$key];
        // for messages which are being edited
        if(empty($recordId))
        {
            if(!$this->allowAdd($data, $key))
            {
                $state = JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED');
                // notify user their not allowed to edit this message
                echo json_encode(array("state"=>0, "result" => $state));
                return true;
            }
        }
        else
        {
            // Access check.
            if (!$this->allowEdit($data, $key))
            {
                $state = JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED');
                // notify user their not allowed to edit this message
                echo json_encode(array("state"=>0, "result" => $state));
                return true;
            }
            // tell joomla we'll be editing this record
            $this->holdEditId($context, $recordId);
        }

        // Access check.
        if (!$this->allowSave($data, $key))
        {
            $result = JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED');
            // notify user their not allowed to save
            echo json_encode(array("state"=>0, "result" => $result));
            return true;
        }

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);
        if (!$form)
        {
            $result = $model->getError();
            // notify user of our failure
            echo json_encode(array("state"=>0, "result" => $result));
            return true;
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up one validation error to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 1; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $result = $errors[$i]->getMessage();
                }
                else
                {
                    $result = $errors[$i];
                }
            }

            // Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // notify user of their failure
            echo json_encode(array("state"=>0, "result"=>$result));
            return true;
        }

        // Attempt to save the data.
        if (!$model->save($validData))
        {
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // get error message.
            $result = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError());

            // notify user of our failure
            echo json_encode(array("state"=>0, "result" => $result));
            return true;
        }

        // Clear the record id and data from the session.
        $this->releaseEditId($context, $recordId);
        $app->setUserState($context . '.data', null);

        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHook($model, $validData);

        // if user is guest and moderation/activation is enabled
        if (JFactory::getUser()->get('guest'))
        {
            if (JComponentHelper::getParams('com_vitabook')->get('guest_email_activation')){
                echo json_encode(array("state"=>2, "result" => JText::_( 'COM_VITABOOK_MESSAGE_WAIT_ACTIVATE' ) ));
                return true;
            }            
            elseif (JComponentHelper::getParams('com_vitabook')->get('guest_post_state') == 0){
                echo json_encode(array("state"=>2, "result" => JText::_( 'COM_VITABOOK_MESSAGE_WAIT' ) ));
                return true;
            }

        }

        // get id of newly created message
        $result = (int) $model->getItem()->get('id');

        // success! return id of (new) message
        echo json_encode(array("state"=>1, "result" => $result));
        return true;
    }

    /**
     * Method to delete a message (and it's children).
     */
    public function delete()
    {
        // Set the MIME type for JSON output.
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        // Check for request forgeries.
        $messageId = JFactory::getApplication()->input->get('messageId', null, 'int');
        if (JSession::checkToken('request') === false && VitabookHelperMail::checkMailHash($messageId,'delete') === false) {
            jexit(json_encode(array("success"=>0, "state" => JText::_('JINVALID_TOKEN'))));
        } 

        // Initialise variables.
        $model  = $this->getModel('Message');
        $messageId = JFactory::getApplication()->input->get('messageId', null, 'int');

        if (!$model->delete($messageId))
        {
            $return = json_encode(array("success"=>0, "state" => $model->getError()));
        }
        else
        {
            $return = json_encode(array("success"=>1, "state" => ''));
        }
        
        if(JFactory::getApplication()->input->get('code', null, 'cmd')){
            $this->setRedirect(JRoute::_('index.php?option=com_vitabook'), JText::_('COM_VITABOOK_MESSAGE_DELETED'));
            $this->redirect();
        }

        echo $return;
        return true;
    }


    /**
     * FROM: JControllerAdmin class
     *
     * Method to publish a list of items
     *
     * @return  void
     *
     * @since   11.1
     */
    public function publish()
    {
        // Set the MIME type for JSON output.
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        $messageId = JFactory::getApplication()->input->get('messageId', null, 'int');

        // Check for request forgeries.
        if(JSession::checkToken('request') === false && VitabookHelperMail::checkMailHash($messageId,'publish') === false) {
            echo json_encode(array("success"=>0, "state" => JText::_('JINVALID_TOKEN')));
            return true;
        }
        // Get items to publish from the request.
        $cid = array($messageId);
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            $return = json_encode(array("success"=>0, "state" => JText::_($this->text_prefix . '_NO_ITEM_SELECTED')));
        }
        else
        {
            // Get the model.
            $model = $this->getModel('message');
            
            // Publish the items.
            if (!$model->publish($cid, $value))
            {
                $return = json_encode(array("success"=>0, "state" => $model->getError()));
            }
            else
            {
                // (un)publishing succeeded, also activate this message (if required)
                // if($task == 'publish' && JComponentHelper::getParams('com_vitabook')->get('guest_email_activation')){
                if($task == 'publish'){
                    $model->activate($cid[0]);
                }
                                
                $return = json_encode(array("success"=>1, "state" => $value));
            }
        }
        
        $code = JFactory::getApplication()->input->get('code', null, 'cmd');
        if(!empty($code)){
            switch ($value) {
                case 0:
                    $this->setRedirect(JRoute::_('index.php?option=com_vitabook&messageId='.$messageId, true, -1).'#vb'.$messageId, JText::_('COM_VITABOOK_MESSAGE_UNPUBLISHED'));
                    break;
                case 1:
                default:
                    $this->setRedirect(JRoute::_('index.php?option=com_vitabook&messageId='.$messageId, true, -1).'#vb'.$messageId);
                    break;
            }
            $this->redirect();
        }

        echo $return;
        return true;
    }

    /**
     * Method to activate a message
     *
     */
    public function activate()
    {
        // Set the MIME type for JSON output.
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        $messageId = JFactory::getApplication()->input->get('messageId', null, 'int');

        // Check for request forgeries.
        if(VitabookHelperMail::checkMailHash($messageId,'activate') === false){
            echo json_encode(array("success"=>0, "state" => JText::_('JINVALID_TOKEN'))); // nog een redirect met invalid token waarschuwing
            return true;
        }

        $model = $this->getModel();

        if (!$model->activate($messageId))
        {
            JError::raiseWarning(500, $model->getError());
        }
        else
        {
            $message = $model->getItem($messageId);

            if($message->published){
                $this->setRedirect(JRoute::_('index.php?option=com_vitabook&messageId='.$messageId, true, -1).'#vb'.$messageId, JText::_('COM_VITABOOK_MESSAGE_ACTIVATED_PUBLISHED'));
            }
            else{
                $this->setRedirect(JRoute::_('index.php?option=com_vitabook&messageId='.$messageId, true, -1).'#vb'.$messageId, JText::_('COM_VITABOOK_MESSAGE_ACTIVATED_UNPUBLISHED'));
            }
        }
    }

    /**
     * Method to check if you can add a new record.
     *
     * Extended classes can override this if necessary.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   11.1
     */
    protected function allowAdd($data = array())
    {
        $user = JFactory::getUser();
        if($data['parent_id'] == 1)
        {
            return $this->canDo->get('vitabook.create.new');
        }
        else
        {
            return $user->authorise('vitabook.create.reply', $this->option);
        }
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * Checks if user has global edit rights, else checks for edit.own rights, returns false by default.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key; default is id.
     *
     * @return  boolean
     *
     * @since   11.1
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $messageId = (int) isset($data[$key]) ? $data[$key] : 0;

        if(!empty($messageId)){
            $user = JFactory::getUser();

            if ($user->authorise('core.edit', 'com_vitabook'))
            {
                return true;
            }

            if ($user->authorise('core.edit.own', 'com_vitabook'))
            {
                $message = $this->getModel('message')->getItem($messageId);
                return $message->actions->edit;
            }
        }
        return false;
    }

   /**
     * Function that allows child controller access to model data
     * after the data has been saved.
     *
     * @param   JModel  &$model     The data model object.
     * @param   array   $validData  The validated data.
     *
     * @return  void
     *
     * @since   11.1
     */
    // TODO: make this function compatible with  JControllerForm::postSaveHook() from joomla 3.x as soon as we drop joomla 2.5 as main target platform
    protected function postSaveHook($model, $validData = Array())
    {
        // get admin mail param
        $admin_mail = JComponentHelper::getParams('com_vitabook')->get('admin_mail');
        
        // are we supposed to send e-mail notifications?
        if( empty($validData['id']) && ( ($admin_mail == 2) || ($admin_mail == 1 && empty($validData['jid'])) ) )
        {
            $item = $model->getItem();
            $data = $validData;
            $data['id'] = $item->get('id');
            VitabookHelperMail::sendAdminMail($data);
        }
        // do we need to send activation emails to guests?
        if(JFactory::getUser()->get('guest') && JComponentHelper::getParams('com_vitabook')->get('guest_email_activation'))
        {
            $item = $model->getItem();
            $data = $validData;
            $data['id'] = $item->get('id');
            VitabookHelperMail::sendGuestActivationMail($data);
        }
    }
    

}
