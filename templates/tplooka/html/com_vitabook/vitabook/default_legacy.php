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

JHtml::stylesheet('components/com_vitabook/assets/vitabookLegacy.css');
if($this->params->get('rounded_corners') == 1) {
    JHtml::stylesheet('components/com_vitabook/assets/vitabook_roundedLegacy.css');
}
if($this->params->get('editor_custom_css', 0) == 1) {
    JHtml::stylesheet('components/com_vitabook/assets/editor.css');
}

// include JS libraries
JHtml::_('behavior.framework', true);
    // hack to circumvent mootools-more block in meet_gavern template
    if(JFactory::getApplication()->getTemplate() == 'meet_gavern'){
        JHTML::script(JURI::base().'media/system/js/mootools-more.js?vitabook');
    }
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');

JHtml::script('components/com_vitabook/assets/vitabook_Legacy.js');

// populate dynamic javascript variables
JFactory::getDocument()->addScriptDeclaration("
    window.addEvent('domready', function(){     // initialize variables
        vitabook.url_base = '".JRoute::_('index.php?option=com_vitabook')."';
        vitabook.token = '".JUtility::getToken()."';
    });
");

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
    .vbContainer{color:#'.$text_color.';}
    .vbMessageHeader{background:#'.$header_background.';}
    .vbMessageMessage{background:#'.$message_background.';}
    .vbMessageHeader, .vbMessageMessage, .vbMessageForm, .vbLoadMoreMessages{border-color:#'.$border_color.';}
    .vbMessageUnpublished{background:#ffe7d7; border-color:#ffb39b;}
    .vbMessageUnActivated{ background-color: #FCF8E3; border-color: #FBEEE5; color:#c09853; }
    .vbActiveParent{background:#e0eab9; border-color:#90b203;}
    .vbActiveParent img, .vbMessageUnpublished img, .vbMessageUnActivated img{filter:alpha(opacity=40); opacity:0.4;}
    img.vbPublishControl{filter:alpha(opacity=100);	opacity:1;}
    #vbMessageForm ul{list-style-type:none;list-style:none;}
    #vbMessageForm ul li, #vbMessageForm ul > li, .vbForm li, ul.vbForm li{list-style-type:none;list-style:none;background:none;background-image:none;}
    .vbIntrotext{color:#'.$introtext_color.';background:#'.$introtext_background.';border-color:#'.$introtext_border.';}
    ';
// add to html head
JFactory::getDocument()->addStyleDeclaration( $style );

// client side form-validation not for IE8
$appWeb = new JApplicationWeb();
if($appWeb->client->browser == JWebClient::IE && $appWeb->client->browserVersion < 9){
    ?><script>document.formvalidator = { isValid: function(){ return true } };</script><?php
}
else {
    JHtml::_('behavior.formvalidation');

    ?><script>
        window.addEvent('domready', function() { // validation handler for name
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
    // Open Vitabook avatar system
    if(($this->params->get('vbAvatar') == 1) && (JFactory::getUser()->get('id') != 0))
    { ?>
        <a class="vbAvatarLink modal" href="<?php echo JRoute::_('index.php?option=com_vitabook&task=avatar.viewform') ?>" rel="{size:{x: 550, y: 250}, handler:'iframe'}">
            <?php echo JText::_('COM_VITABOOK_AVATAR'); ?>
        </a>
        <div class="clr"></div><?php
    } ?>

    <?php
    // Show intro-text if set
    if($this->params->get('introtext'))
    {
        echo '<div class="clr"></div>';
        echo '<div class="vbIntrotext"><p>'.$this->params->get('introtext').'</p></div>';
    }
    ?>

    <?php
    // new message button only if user is allowed to create messages
    if(VitabookHelper::getActions()->get('vitabook.create.new'))
    { ?>
        <div id="vbReplyButton">
            <button class="button" onclick="<?php if(JFactory::getApplication()->input->get('start', 0, 'int') > 0){ echo "document.location.href = '".JRoute::_('index.php?option=com_vitabook&new=true')."';"; } else { echo "vitabook.fresh();"; } ?>"><?php echo JText::_('COM_VITABOOK_NEW_MESSAGE'); ?></button>
        </div>
        <?php
    }

    ?>
    <div class="clr"></div>

    <?php
    // message-form only if necessary
    if(VitabookHelper::getActions()->get('vitabook.create.new') || VitabookHelper::getActions()->get('vitabook.create.reply') || VitabookHelper::getActions()->get('core.edit') || VitabookHelper::getActions()->get('core.edit.own'))
    { ?>
        <div id="vbFormHolder">
            <div class="vbMessageForm" id="vbMessageForm" style="display: none;">
                <form action="<?php echo JRoute::_('index.php?option=com_vitabook'); ?>" method="post" name="vitabookMessageForm" id="vitabookMessageForm" class="form-validate"><?php //-- this id is necessary for the front/back-end detection of the vitabookupload plugin for tinymce ?>
                    <ul class="vbForm">
                    <?php // form fields, configurable layout on one row, or two rows ?>
                    <?php if($this->params->get('vbEditorOutline', 1) == 1) { ?>
                        <li>
                        <table id="vbFormTable">
                            <tr>
                                <td><?php echo $this->form->getLabel('name'); ?></td>
                                <td><?php echo $this->form->getInput('name'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->form->getLabel('email'); ?></td>
                                <td><?php echo $this->form->getInput('email'); ?></td>
                            </tr>
                            <?php if($this->params->get('vbForm_site',1)) { ?>
                            <tr>
                                <td><?php echo $this->form->getLabel('site'); ?></td>
                                <td><?php echo $this->form->getInput('site'); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if($this->params->get('vbForm_location',0)) { ?>
                            <tr>
                                <td><?php echo $this->form->getLabel('location'); ?></td>
                                <td><?php echo $this->form->getInput('location'); ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                        </li>
                    <?php } else { ?>    
                        <li><?php echo $this->form->getLabel('name'); ?></li>
                        <li><?php echo $this->form->getInput('name'); ?></li>
                        <li><?php echo $this->form->getLabel('email'); ?></li>
                        <li><?php echo $this->form->getInput('email'); ?></li>
                        <?php if($this->params->get('vbForm_site',1)) { ?>
                            <li><?php echo $this->form->getLabel('site'); ?></li>
                            <li><?php echo $this->form->getInput('site'); ?></li>
                        <?php } ?>
                        <?php if($this->params->get('vbForm_location',0)) { ?>
                            <li><?php echo $this->form->getLabel('location'); ?></li>
                            <li><?php echo $this->form->getInput('location'); ?></li>                       
                        <?php } ?>
                    <?php }
                        // vitabook editor on two rows ?>
                        <li><?php echo $this->form->getLabel('message'); if($this->params->get('editor_maxchars', 0) != 0) { ?><span id="vbMaxcharsIndicatorContainer">(<?php echo JText::_('COM_VITABOOK_FORM_MAX_CHARS'); ?><span id="vbMaxcharsIndicator"> <?php echo $this->params->get('editor_maxchars', 0); ?></span>)</span><?php } ?></li>
                        <li><?php echo $this->form->getInput('message'); ?></li> <?php
                        // Secure form
                        echo $this->form->getInput('secureform');
                        // captcha only if necessary
                        if($this->form->getField('captcha')){ ?>
                            <li><?php echo $this->form->getLabel('captcha'); ?></li>
                            <li><?php echo $this->form->getInput('captcha'); ?></li><?php
                        } ?>
                        <li id="vbMessageFormListButton">
                            <?php // Buttons inside form submit the form: therefore cancel button returns false ?>
                            <button type="button" class="button" onclick="vitabook.cancel();return false;"><?php echo JText::_('COM_VITABOOK_CANCEL'); ?></button>
                            <button type="button" class="button" onclick="vitabook.reset();return false;"><?php echo JText::_('COM_VITABOOK_FORM_RESET'); ?></button>
                            <button id="vbMessageFormSubmitButton" type="submit" class="button" onclick="this.disabled = true;document.id('vbAjaxBusy').show('inline');vitabook.save();return false;"><?php echo JText::_('COM_VITABOOK_FORM_SUBMIT'); ?></button><span id="vbAjaxBusy"><img src="components/com_vitabook/assets/img/spinner.gif" /></span>
                        </li>
                    </ul><?php
                    // hidden fields
                    echo JHtml::_('form.token');
                    echo $this->form->getInput('id');
                    echo $this->form->getInput('parent_id'); ?>
                    <input type="hidden" name="task" value="message.save" />
                    <input type="hidden" name="format" value="raw" />
                </form>

                <div class="clr"></div>
            </div>
        </div><?php
    }

    // show available messages ?>
    <div id="vbMessages"><?php
        echo $this->loadTemplate('messageslegacy'); ?>
    </div><?php

    // add pagination ?>
    <div class="pagination"><?php
        echo $this->pagination->getPagesLinks(); ?>
        <!-- Please do not remove the lines below -->
        <?php/*<div style="text-align: right;">
            Powered by <a href="http://www.joomvita.com" target="_blank" title="JoomVita">JoomVita</a> <a href="http://www.joomvita.com/vitabook" target="_blank" title="VitaBook">VitaBook</a>
            <!-- VitaBook version 2.2.2 -->
        </div>*/?>
    </div>
</div>
