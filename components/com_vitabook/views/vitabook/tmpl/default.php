<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// no direct access
defined('_JEXEC') or die;

// load primary stylesheet
JHtml::stylesheet('components/com_vitabook/assets/vitabook.css');
if($this->params->get('rounded_corners') == 1) {
    JHtml::stylesheet('components/com_vitabook/assets/vitabook_rounded.css');
}
if($this->params->get('editor_custom_css', 0) == 1) {
    JHtml::stylesheet('components/com_vitabook/assets/editor.css');
}


// configurable css settings
// Get values from parameters
$introtext_color = $this->params->get('vb_introtext_color');
$introtext_background = $this->params->get('vb_introtext_background');
$introtext_border = $this->params->get('vb_introtext_border');
$text_color = $this->params->get('vb_text_color');
$header_background = $this->params->get('vb_header_background');
$message_background = $this->params->get('vb_message_background');
$border_color = $this->params->get('vb_border_color');
// define and override css
$style = '
    .vbContainer, .vbMessageDate, .vbMessageLocation{color:#'.$text_color.';}
    .vbMessageHeader{background:#'.$header_background.';}
    .vbMessage{background:#'.$message_background.';}
    .vbMessage, .vbMessageForm, .vbLoadMoreMessages{border-color:#'.$border_color.';}
    .vbMessageUnpublished{background:#ffe7d7; border-color:#ffb39b;}
    .vbMessageUnActivated{ background-color: #FCF8E3; border-color: #FBEEE5; color:#c09853; }
    .vbActiveParent{background:#e0eab9; border-color:#90b203;}
    .vbActiveParent img, .vbMessageUnpublished img, .vbMessageUnActivated img{filter:alpha(opacity=40); opacity:0.4;}
    img.vbPublishControl{filter:alpha(opacity=100);	opacity:1;}
    .vbIntrotext{color:#'.$introtext_color.';background:#'.$introtext_background.';border-color:#'.$introtext_border.';}
    ';
if($message_background || $border_color)
    $style2 = '.vbMessage{margin:2px 0;}';
// add to html head
JFactory::getDocument()->addStyleDeclaration($style);
//JFactory::getDocument()->addStyleDeclaration($style2);

// include JS libraries
JHtml::_('behavior.framework', true);
    // hack to circumvent mootools-more block in meet_gavern template
    if(JFactory::getApplication()->getTemplate() == 'meet_gavern'){
        JHTML::script(JURI::base().'media/system/js/mootools-more.js?vitabook');
    }
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', '#vbAvatarLink');

JHtml::script('components/com_vitabook/assets/vitabook.js');

// populate dynamic javascript variables
JFactory::getDocument()->addScriptDeclaration("
    window.addEvent('domready', function(){     // initialize variables
        vitabook.url_base = '".JRoute::_('index.php?option=com_vitabook')."';
        vitabook.token = '".JSession::getFormToken()."';
    });
");

// client side form-validation not for IE8
$appWeb = new JApplicationWeb();
if($appWeb->client->browser == JWebClient::IE && $appWeb->client->browserVersion < 9){
    ?><script>document.formvalidator = { isValid: function(){ return true } };</script><?php
}
else {
    JHtml::_('behavior.formvalidation');

    ?><script>
        jQuery( document ).ready(function( $ ) {
            document.formvalidator.setHandler('name',
                function (value) {
                    regex = /[<|>|\"|%|;|(|)|&]/i;
                    return !regex.test(value);
            });
        });
    </script><?php
}
    ?><script>
        vitabook.validate = function(){
            var isValid = true;

            // prepend site with http:// if no scheme is present
            if(document.id('jform_site') && document.id('jform_site').value != '' && !document.id('jform_site').value.test('https?://','i'))
            {
                document.id('jform_site').value = 'http://'+document.id('jform_site').value;
            }

            if (!document.formvalidator.isValid(document.id('vitabookMessageForm')))
            {
                isValid = false;
            }
        return isValid;
        }
    </script><?php

// some loose JS ?>
<?php
    // if a new message form is requested
    if(JFactory::getApplication()->input->get('new', false, 'bool') == 'true')
    {
        JFactory::getDocument()->addScriptDeclaration("
            window.addEvent('domready', function(){     
                vitabook.fresh();
            });
        ");        
    }
?>

<div class="vbContainer">
    <?php
    // Show page header if set in menu item ?>
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h2 style="float:left;"><?php
            $page_heading = $this->params->get('page_heading');
            if(!empty($page_heading)) :
               echo $this->escape($page_heading);
            else :
                echo $this->escape(JFactory::getApplication()->getMenu()->getActive()->title); 
            endif; ?>
        </h2>
    <?php endif; ?>

    <?php
    // Show Vitabook avatar system
    if(($this->params->get('vbAvatar') == 1) && (JFactory::getUser()->get('id') != 0))
    { ?>
        <a id="vbAvatarLink" class="vbAvatarLink pull-right modal" href="<?php echo JRoute::_('index.php?option=com_vitabook&task=avatar.viewform') ?>" rel="{size:{x: 500, y: 250}, handler:'iframe'}">
            <?php echo JText::_('COM_VITABOOK_AVATAR'); ?>
        </a>
        <div class="clr"></div><br /><?php
    } ?>


    <?php
    // Show intro-text if set
    if($this->params->get('introtext')){ ?>
    <div class="clr"></div>
    <div class="vbIntrotext">
        <p><?php echo JText::_($this->params->get('introtext')); ?></p>
    </div><?php
    } ?>

    <?php
    // Show new message button, but only if user is allowed to create messages
    if(VitabookHelper::getActions()->get('vitabook.create.new')){ ?>
    <button class="vbPostButton pull-right btn" onclick="<?php if(JFactory::getApplication()->input->get('start', 0, 'int') > 0){ echo "document.location.href = '".JRoute::_('index.php?option=com_vitabook&new=true')."';"; } else { echo "vitabook.fresh();"; } ?>"><?php echo JText::_('COM_VITABOOK_NEW_MESSAGE'); ?></button>
    <div class="clr"></div><br /><?php
    } ?>

    <?php
    // Display message-form, but only if necessary
    if(VitabookHelper::getActions()->get('vitabook.create.new') || VitabookHelper::getActions()->get('vitabook.create.reply') || VitabookHelper::getActions()->get('core.edit') || VitabookHelper::getActions()->get('core.edit.own'))
    { ?>
    <div class="row-fluid">
        <div id="vbFormHolder"><?php //parking space for the form ?>
            <div class="well" id="vbMessageForm" style="display: none;">
                <form method="post" name="vitabookMessageForm" id="vitabookMessageForm" class="form-validate <?php if($this->params->get('vbEditorOutline', 1) == 1) { ?>form-horizontal<?php } ?>"><?php //-- this id is necessary for the front/back-end detection of the vitabookupload plugin for tinymce ?><?php
                    // form fields, configurable layout on one row, or two rows
                    // one row
                    if($this->params->get('vbEditorOutline', 1) == 1) { ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('name'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('name'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('email'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('email'); ?>
                            </div>
                        </div><?php
                        // site input field only if enabled
                        if($this->params->get('vbForm_site',1)) { ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('site'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('site'); ?>
                            </div>
                        </div><?php
                        }
                        // location input field only if enabled
                        if($this->params->get('vbForm_location',0)) { ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('location'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('location'); ?>
                            </div>
                        </div><?php
                        } ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('message'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('message'); ?>
                            </div>
                        </div><?php
                        // captcha field only if required
                        if($this->form->getField('captcha')){ ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('captcha'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('captcha'); ?>
                            </div>
                        </div><?php
                        }
                    }
                    // two rows
                    else {
                        echo $this->form->getLabel('name');
                        echo $this->form->getInput('name');
                        echo $this->form->getLabel('email');
                        echo $this->form->getInput('email');
                        // site input field only if enabled
                        if($this->params->get('vbForm_site',1)){
                            echo $this->form->getLabel('site');
                            echo $this->form->getInput('site');
                        }
                        // location input field only if enabled
                        if($this->params->get('vbForm_location',0)){
                            echo $this->form->getLabel('location');
                            echo $this->form->getInput('location');
                        }
                        echo $this->form->getLabel('message');
                        echo $this->form->getInput('message');
                        // captcha field only if required
                        if($this->form->getField('captcha')){
                            echo $this->form->getLabel('captcha');
                            echo $this->form->getInput('captcha');
                        }
                    }
                    // Secure form anti spam measure
                    echo $this->form->getInput('secureform');
                    // Buttons inside form submit the form: therefore cancel button returns false
                    if($this->params->get('vbEditorOutline', 1) == 1) { ?>
                    <div class="control-group"><div class="controls"><?php } else { echo "<br />"; }?>

                    <button type="button" class="btn button" onclick="vitabook.cancel();return false;"><?php echo JText::_('COM_VITABOOK_CANCEL'); ?></button>
                    <button type="button" class="btn button" onclick="vitabook.reset();return false;"><?php echo JText::_('COM_VITABOOK_FORM_RESET'); ?></button>
                    <button id="vbMessageFormSubmitButton" type="submit" class="btn btn-primary button" onclick="this.disabled = true;document.id('vbAjaxBusy').show('inline');vitabook.save();return false;"><?php echo JText::_('COM_VITABOOK_FORM_SUBMIT'); ?></button><span id="vbAjaxBusy"><img src="components/com_vitabook/assets/img/spinner.gif" /></span><?php
                    
                    if($this->params->get('vbEditorOutline', 1) == 1) { ?>
                    </div></div><?php }
                    // hidden fields
                    echo JHtml::_('form.token');
                    echo $this->form->getInput('id');
                    echo $this->form->getInput('parent_id'); ?>
                    <input type="hidden" name="task" value="message.save" />
                    <input type="hidden" name="format" value="raw" />
                </form>

                <div class="clr"></div>
            </div>
        </div>
    </div><?php
    }

    // Display the messages in the guestbook ?>
    <div class="clr"></div>
    <div class="row-fluid" id="vbMessages"><?php
        echo $this->loadTemplate('messages'); ?>
    </div>

    <?php
    // Show pagination ?>
    <div class="row-fluid">
        <div class="pagination pagination-centered"><?php
            echo $this->pagination->getPagesLinks(); ?>
        </div>
    </div>

    <?php
    // Show little bit of branding
    // Please do not remove the lines below ?>
    <div class="pull-right">
        Powered by <a href="http://www.joomvita.com" target="_blank" title="JoomVita">JoomVita</a> <a href="http://www.joomvita.com/vitabook" target="_blank" title="VitaBook">VitaBook</a>
        <!-- VitaBook version 2.2.2 -->
    </div>
    <div class="clr"></div>
</div>