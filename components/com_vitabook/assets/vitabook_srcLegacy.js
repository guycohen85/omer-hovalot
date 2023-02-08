/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

var vitabook = {
    currentId: 0,
    currentType: 'none',
    reset: function (){
        // method to reset the form
        // cancel submit-button lock
        document.id('vbMessageFormSubmitButton').disabled = false;
        document.id('vbAjaxBusy').hide();
        // if current type: edit, restore form to original message contents
        if(vitabook.currentType == 'edit')
        {
            tinymce.get('jform_message').setContent(document.id('vbMessageMessage_'+vitabook.currentId).getFirst('div.vbMessageText').get('html'));
            var message = document.id('vbMessage_'+vitabook.currentId);
            document.id('jform_name').set('value', message.get('data-name'));
            document.id('jform_email').set('value', message.get('data-email'));
            if(document.id('jform_site'))
                document.id('jform_site').set('value', message.get('data-site'));
            if(document.id('jform_location'))
                document.id('jform_location').set('value', message.get('data-location'));
        }
        // else reset to onload defaults
        else
        {
            // reset de normal form fields
            document.id('vitabookMessageForm').reset();
            tinymce.get('jform_message').setContent('');
            // current type: reply, retain parent_id while resetting
            if(vitabook.currentType == 'reply')
            {
                document.id('jform_parent_id').value = vitabook.currentId;
            }
        }
    },
    fresh: function (){
        // do nothing if form is already in place
        if(vitabook.currentType == 'fresh')
        {
            return;
        }
        // if previous location is reply, retain form contents, only change parent_id and move form
        if(vitabook.currentType == 'reply')
        {
            document.id('jform_parent_id').value = 1;
            tinymce.execCommand('mceRemoveControl',true,'jform_message');
            document.id('vbMessageHeader_'+vitabook.currentId).removeClass('vbActiveParent');
            document.id('vbMessageMessage_'+vitabook.currentId).removeClass('vbActiveParent');
            document.id('vbFormHolder').grab(document.id('vbMessageForm'));
            vitabook.currentId = 0;
        }
        // cancel form if it's currently active elsewhere
        else if(vitabook.currentType != 'none')
        {
            vitabook.cancel(vitabook.fresh);
            return;
        }
        // reconnect tinymce
        tinymce.execCommand('mceAddControl',true,'jform_message');
        // show form
        document.id('vbMessageForm').reveal({ duration: 250 });
        // set status variables
        vitabook.currentType = 'fresh';
        // scroll to form
        var position = document.id('vbMessageForm').getPosition();
        window.scrollTo(position.x,position.y-100);
        // grab focus
        vitabook.grabFocus();
    },
    reply: function (parentId){
        if(vitabook.currentType == 'fresh')
        {
            // only have to disable editor before moving :-)
                tinymce.execCommand('mceRemoveControl',true,'jform_message');
        }
        else if (vitabook.currentType == 'reply')
        {
            tinymce.execCommand('mceRemoveControl',true,'jform_message');
            document.id('vbMessageHeader_'+vitabook.currentId).removeClass('vbActiveParent');
            document.id('vbMessageMessage_'+vitabook.currentId).removeClass('vbActiveParent');
        }
        // cancel form if it's currently active
        else if(vitabook.currentType != 'none')
        {
            vitabook.cancel(vitabook.reply.bind(this, parentId));
            return;
        }
        // move form
        document.id('jform_parent_id').value = parentId;
        document.id('vbMessageChildren_'+parentId).adopt(document.id('vbMessageForm'));
        // reconnect tinymce
        tinymce.execCommand('mceAddControl',true,'jform_message');
        // set active parent
        document.id('vbMessageHeader_'+parentId).addClass('vbActiveParent');
        document.id('vbMessageMessage_'+parentId).addClass('vbActiveParent');
        // show form
        document.id('vbMessageForm').reveal({ duration: 250 });
        // scroll to form
        var position = document.id('vbMessageForm').getPosition();
        window.scrollTo(position.x,position.y);
        // grab focus
        vitabook.grabFocus();
        // set status variables
        vitabook.currentId = parentId;
        vitabook.currentType = 'reply';
    },
    edit: function (messageId){
        // cancel form if it's currently active
        if(vitabook.currentType != 'none')
        {
            vitabook.cancel(vitabook.edit.bind(this, messageId));
            return;
        }
        // put content of the message in the formfield
        var messageContent = document.id('vbMessageMessage_'+messageId).getFirst('div.vbMessageText').get('html');
        document.id('jform_message').set('value', messageContent);
        var message = document.id('vbMessage_'+messageId);
        document.id('jform_name').set('value', message.get('data-name'));
        document.id('jform_email').set('value', message.get('data-email'));
        if(document.id('jform_site'))
            document.id('jform_site').set('value', message.get('data-site'));
        if(document.id('jform_location'))
            document.id('jform_location').set('value', message.get('data-location'));
        document.id('jform_parent_id').set('value', message.get('data-parent_id'));
        // move the editor to new location
        document.id('vbMessage_'+messageId).getParent().grab(document.id('vbMessageForm'),'top');
        // reconnect tinymce (with small delay so IE7&8 work also)
        (function(){tinymce.execCommand('mceAddControl',true,'jform_message');}).delay(50);
        // set correct message id in form
        document.id('jform_id').set('value', messageId);
        // add the message-id to the action of the form
        // hide original message
        document.id('vbMessage_'+messageId).hide();
        // show form
        document.id('vbMessageForm').reveal({ duration: 250 });
        // set status variables
        vitabook.currentId = messageId;
        vitabook.currentType = 'edit';
    },
    cancel: function (followUp){
        var delay = 0;
        // type specific cancel procedures
        switch (vitabook.currentType)
        {
            case 'edit':
                // hide form
                document.id('vbMessageForm').hide();
                // unhide original message
                document.id('vbMessage_'+vitabook.currentId).set('opacity',0.5);
                document.id('vbMessage_'+vitabook.currentId).show();
                if (Browser.ie && Browser.version < 9){
                    document.id('vbMessage_'+vitabook.currentId).set('opacity',1);
                }
                else{
                    document.id('vbMessage_'+vitabook.currentId).tween('opacity', 1);
                }
                document.id('vitabookMessageForm').reset();
                break;
            case 'reply':
                // hide form
                document.id('vbMessageForm').hide();
                // reset active parent
                document.id('vbMessageHeader_'+vitabook.currentId).removeClass('vbActiveParent');
                document.id('vbMessageMessage_'+vitabook.currentId).removeClass('vbActiveParent');
                // reset parent_id
                document.id('jform_parent_id').value = 1;
                break;
            case 'fresh':
                // hide form
                document.id('vbMessageForm').dissolve({ duration: 250 });
                delay = 200;
                break;
            default:
        }
        // delay execution of below to allow above to finish first
        (function(){
            // general
            tinymce.execCommand('mceRemoveControl',true,'jform_message');
            // reset values
            document.id('jform_id').value = 0;
            document.id('jform_message').set('value', '');
            document.id('vbMessageFormSubmitButton').disabled = false;
            document.id('vbAjaxBusy').hide();
            // move form to parking
            document.id('vbFormHolder').grab(document.id('vbMessageForm'));
            // set status variables
            vitabook.currentId = 0;
            vitabook.currentType = 'none';
            if(followUp)
            {
                followUp();
            }
            // reload captcha if present
            if(typeof( Recaptcha ) != 'undefined')
            {
                Recaptcha.reload();
            }
        }).delay(delay);
    },
    loadChildren: function(element,parent_id,start){
        //make the ajax call, insert child messages
        new Request.HTML({
            url: vitabook.url_base,
            onRequest: function(){
                // especially for IE7 not willing to coorporate, no references to element
                document.id('vbLoadMoreMessages_'+parent_id+'_'+start).set('opacity', 0.5); // basic indication that we're busy
                document.id('vbLoadMoreMessages_'+parent_id+'_'+start).set('onclick', '');  // prevent multiple clicks
            },
            onComplete: function(response) {
                // remove this load-more-messages button/div
                document.id('vbLoadMoreMessages_'+parent_id+'_'+start).destroy();
//TODO: get next siblings position, so we can scroll to that location :-)
                // put newly loaded messages in place
                var oud = document.id('vbMessageChildren_'+parent_id).getChildren('div');
                document.id('vbMessageChildren_'+parent_id).adopt(response);
                document.id('vbMessageChildren_'+parent_id).adopt(oud);
                vitabook.visualState(parent_id,document.id('vbMessageHeader_'+parent_id).hasClass('vbMessageUnpublished')? 0 : 1);
            }
        }).get('task=vitabook.getMessages&start='+start+'&format=raw&'+vitabook.token+'=1&parent_id='+parent_id);
    },
    state: function(element, id){
        var task = 'unpublish';
        if(document.id('vbMessageHeader_'+id).get('data-published')==0 || document.id('vbMessageHeader_'+id).hasClass('vbMessageUnActivated'))
            task = 'publish';
        //make the ajax call
        new Request.JSON({
            url: vitabook.url_base,
            onSuccess: function(response){
                if(response.success===1)
                {
                    // switch-controls and data attribute
                    document.id('vbMessageHeader_'+id).set('data-published', response.state);
                    document.id('vbMessageMessage_'+id).set('data-published', response.state);
                    document.id('vbMessageStateControl_'+id).src = response.state ? document.id('vbMessageStateControl_'+id).src.replace('offline','online') : document.id('vbMessageStateControl_'+id).src.replace('online','offline');
                    // actions for messages now unpublished
                    vitabook.visualState(id,response.state);
                }
                else
                {
                    // show errors
                    alert(response.state);
                }
            }
        }).get('task=message.'+task+'&messageId='+id+'&'+vitabook.token+'=1&format=raw');
    },
    remove: function(element, id){
        //make the ajax call
        new Request.JSON({
            url: vitabook.url_base,
            onSuccess: function(response) {
                if(response.success===1)
                {
                    // when success, remove this message and all possible children from DOM
                    document.id('vbMessage_'+id).getParent().dissolve({ duration: 250 });
                    (function(){document.id('vbMessage_'+id).getParent().destroy();}).delay(250);
                }
                else
                {
                    // show errors
                    alert(response.state);
                }
            }
        }).get('task=message.delete&messageId='+id+'&'+vitabook.token+'=1&format=raw');
    },
    save: function(element){
        // write tinymce content to textarea
        tinyMCE.triggerSave();
        if(vitabook.validate())
        {
            //make the ajax call
            new Request.JSON({
                url: vitabook.url_base,
                method: 'post',
                secure: false,
                data: document.id('vitabookMessageForm'),
                onSuccess: function(response) {
                    switch(response.state){
                        // routines for successful saves (published right away)
                        case 1:
                            if(vitabook.currentType == 'edit')
                            {
                                // edit was successfull, update message div with new content
                                new Request.HTML({
                                    url: vitabook.url_base,
                                    onComplete: function(message) {
                                        var place = document.id('vbMessage_'+response.result).getParent();
                                        var children = document.id('vbMessageChildren_'+response.result).getChildren('div');
                                        document.id('vbMessage_'+response.result).destroy();
                                        document.id('vbMessageChildren_'+response.result).destroy();
                                        place.adopt(message);
                                        document.id('vbMessageChildren_'+response.result).adopt(children);
                                    }
                                }).get('task=message.getMessage&format=raw&messageId='+response.result+'&'+vitabook.token+'=1');
                                // and reset form
                                vitabook.cancel();
                            }
                            else
                            {
                                if(vitabook.currentType == 'fresh')
                                {
                                // code for new messages
                                    new Request.HTML({
                                        url: vitabook.url_base,
                                        onComplete: function(message){
                                            var oud = document.id('vbMessages').getChildren('div');
                                            document.id('vbMessages').adopt(message);
                                            document.id('vbMessages').adopt(oud);
                                            vitabook.cancel();
                                            if(document.id('vbNoMessages'))
                                            {
                                                document.id('vbNoMessages').destroy();
                                            }
                                        }
                                    }).get('task=message.getMessage&format=raw&messageId='+response.result+'&'+vitabook.token+'=1');
                                }
                                if(vitabook.currentType == 'reply')
                                {
                                // code for new replies
                                    new Request.HTML({
                                        url: vitabook.url_base,
                                        onComplete: function(message) {
                                            document.id('vbMessageChildren_'+vitabook.currentId).adopt(message);
                                            vitabook.cancel();
                                            vitabook.visualState(document.id('vbMessageHeader_'+response.result).get('data-parent_id'),document.id('vbMessageHeader_'+document.id('vbMessageHeader_'+response.result).get('data-parent_id')).hasClass('vbMessageUnpublished')? 0 : 1);
                                        }
                                    }).get('task=message.getMessage&format=raw&messageId='+response.result+'&'+vitabook.token+'=1');
                                }
                            }
                            break;
                         // routine for saves awaiting moderation
                        case 2:
                            // remove form
                            vitabook.cancel();
                            // show errors/message
                            (function(){alert(response.result);}).delay(260);
                        break;
                        // routine for failed saves
                        case 0:
                            alert(response.result);
                            // reload captcha if present
                            if(typeof( Recaptcha ) != 'undefined'){
                                Recaptcha.reload();
                            }
                            document.id('vbMessageFormSubmitButton').disabled = false;
                            document.id('vbAjaxBusy').hide();
                        break;
                        default:
                    }
                }
            }).send();
        }
        else
        {
            document.id('vbMessageFormSubmitButton').disabled = false;
            document.id('vbAjaxBusy').hide();
        }
    return false;
    },
    grabFocus: function(){
        // set focus on name if empty
        if(document.id('jform_name').value == '')
            document.id('jform_name').focus();
        // else focus on editor, with slight delay giving tinymce time to initialize
        else
            (function(){tinyMCE.execCommand('mceFocus',false,'jform_message');}).delay(100);
    },
    // changes visual state of message with id and all it's children
    visualState: function(id, state){
        // actions for messages now unpublished
        if(state==0)
        {
            // change color (including all children)
            document.id('vbMessage_'+id).getParent().getElements('div.vbMessageHeader').each(function(el){el.addClass('vbMessageUnpublished');});
            document.id('vbMessage_'+id).getParent().getElements('div.vbMessageMessage').each(function(el){el.addClass('vbMessageUnpublished');});
            // change control of this message
        }
        // actions for messages now published
        else
        {
            // change color (including all children)
//TODO: this probably can be done more efficiently
            if(document.id('vbMessageHeader_'+id).hasClass('vbMessageUnActivated')){
                document.id('vbMessageHeader_'+id).removeClass('vbMessageUnActivated');
                document.id('vbMessageMessage_'+id).removeClass('vbMessageUnActivated');
            }
            else{
                document.id('vbMessage_'+id).getParent().getElements('div[class^=vbMessageHeader]').each(function(el){
                    if(el.get('data-published')==1 && (document.id('vbMessageHeader_'+el.get('data-parent_id')) === null || !(document.id('vbMessageHeader_'+el.get('data-parent_id')).hasClass('vbMessageUnpublished'))))
                        el.removeClass('vbMessageUnpublished');
                });
                document.id('vbMessage_'+id).getParent().getElements('div[class^=vbMessageMessage]').each(function(el){
                    if(el.get('data-published')==1 && (document.id('vbMessageMessage_'+el.get('data-parent_id')) === null || !(document.id('vbMessageMessage_'+el.get('data-parent_id')).hasClass('vbMessageUnpublished'))))
                        el.removeClass('vbMessageUnpublished');
                });
            }
        }
    }
};
