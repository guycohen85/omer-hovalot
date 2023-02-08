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

// function to recursively render messages with their children
function vbRenderMessage($message,$params,$parentState){ ?>
    <div class="vbMessage">
    <div id="vbMessageTop_<?php echo $message->id; ?>" class="vbMessageTop row-fluid <?php if(!isset($message->load_more) && empty($message->activated)){ echo "vbMessageUnActivated"; } elseif(!isset($message->load_more) && (empty($message->published) || empty($parentState)) ) { echo "vbMessageUnpublished"; } ?>"><?php
    if(is_object($message) && !isset($message->load_more))
    { ?>
        <a id="vb<?php echo $message->id; ?>"></a>
        <div class="vbMessageInner" id="vbMessage_<?php echo $message->id; if($message->actions->edit){ ?>" data-name="<?php echo $message->name; ?>" data-email="<?php echo $message->email; ?>" data-site="<?php echo $message->site; ?>" data-location="<?php if(!empty($message->location)) echo $message->location; } ?>" data-parent_id="<?php echo $message->parent_id; ?>" data-published="<?php echo $message->published; ?>">
            <?php if($params->get('vbAvatar') != 0) { ?>
            <img class="vbMessageAvatar" src="<?php echo $message->avatar; ?>" alt="" />
            <?php } ?>
            <div class="vbMessageControls">
                <?php
                if($message->actions->manage)
                {
                    if(!empty($message->ipblock))
                    { ?>
                        <img id="vbMessageIpblock_<?php echo $message->id; ?>" title="<?php echo JText::_('COM_VITABOOK_MESSAGE_IPBLOCK_LABEL') .': '.$message->ip; ?>" src="components/com_vitabook/assets/img/ip.png" class="vbPublishControl" onclick="window.location.href = '<?php echo JRoute::_('index.php?option=com_vitabook&task=vitabook.blockip&format=raw&ip='.$message->ip.'&messageId='.$message->id); ?>';" />
                        <?php
                    }
                    else
                    { ?>
                         <img id="vbMessageIpblock_<?php echo $message->id; ?>" title="<?php echo JText::_('COM_VITABOOK_MESSAGE_IPBLOCK_LABEL') .': '.$message->ip; ?>" src="components/com_vitabook/assets/img/ip-block.png" class="vbPublishControl" onclick="window.location.href = '<?php echo JRoute::_('index.php?option=com_vitabook&task=vitabook.blockip&format=raw&ip='.$message->ip.'&messageId='.$message->id); ?>';" />
                         <?php
                    }
                }                
                ?>
                <?php if($message->actions->delete){ ?><img title="<?php echo JText::_('COM_VITABOOK_MESSAGE_DELETE_LABEL'); ?>" src="components/com_vitabook/assets/img/delete.png" onclick="vitabook.remove(this,'<?php echo $message->id; ?>');" class="vbPublishControl" /> <?php } ?>
                <?php
                if($message->actions->state)
                {
                    if(empty($message->activated) || empty($message->published))
                    { ?>
                        <img id="vbMessageStateControl_<?php echo $message->id; ?>" title="<?php echo JText::_('COM_VITABOOK_MESSAGE_PUBLISH_LABEL'); ?>" src="components/com_vitabook/assets/img/offline.png" onclick="vitabook.state(this,<?php echo $message->id; ?>);" class="vbPublishControl" /> <?php
                    }
                    else
                    { ?>
                        <img id="vbMessageStateControl_<?php echo $message->id; ?>" title="<?php echo JText::_('COM_VITABOOK_MESSAGE_UNPUBLISH_LABEL'); ?>" src="components/com_vitabook/assets/img/online.png" onclick="vitabook.state(this,<?php echo $message->id; ?>);" class="vbPublishControl" /> <?php
                    }
                }
                if($message->actions->edit)
                { ?>
                    <img title="<?php echo JText::_('COM_VITABOOK_MESSAGE_EDIT_LABEL'); ?>" src="components/com_vitabook/assets/img/edit.png" onclick="vitabook.edit(<?php echo $message->id; ?>);" /> <?php
                }
                if($message->actions->reply)
                { ?>
                    <img title="<?php echo JText::_('COM_VITABOOK_MESSAGE_REPLY_LABEL'); ?>" src="components/com_vitabook/assets/img/reply.png" onclick="vitabook.reply(<?php echo $message->id; ?>);" /> <?php
                } ?>
            </div>
            
            <div class="vbMessageTitle">
                <?php if(!empty($message->site)){ ?><a target="_blank" href="<?php echo $message->site; ?>"><?php } ?><strong><?php echo $message->name; ?></strong><?php if(!empty($message->site)){ ?></a><?php } ?>
                <?php if(!empty($message->location) && $params->get('vbForm_location', 0) == 1){ echo " - <span class=\"vbMessageLocation\">" . $message->location . "</span>"; } ?>
            </div>

            <div class="vbMessageText">
                <?php echo $message->message;?>
            </div>

            <div class="vbMessageDate"><?php // echo $message->date; ?></div>
            <div class="clr"></div>
        </div>
        <div class="vbMessageChildren" id="vbMessageChildren_<?php echo $message->id; ?>"><?php
            if(!empty($message->children))
            {
                foreach($message->children as $child):
                vbRenderMessage($child,$params,(!$message->published || !$parentState) ? 0 : 1);
                endforeach;
            } ?>
        </div><?php
    } // end if $message is object
    elseif(is_object($message) && isset($message->load_more))
    { ?>
        <div id="vbLoadMoreMessages_<?php echo $message->parent_id; ?>_<?php echo $message->start; ?>" class="vbLoadMoreMessages" onclick="vitabook.loadChildren(this,<?php echo $message->parent_id; ?>,<?php echo $message->start; ?>);"><strong><?php echo JText::_('COM_VITABOOK_LOAD_MORE_MESSAGES'); ?></strong></div><?php
    } ?>
    </div>
    </div>

<?php
} // end function render message

// render available messages
if(!empty($this->messages))
{
    foreach ($this->messages as $message):
        vbRenderMessage($message,$this->params,1);
    endforeach;
}
// if no messages are present, say so
else
{ ?>
    <div id="vbNoMessages">
        <p>
            <?php echo JText::_('COM_VITABOOK_NO_MESSAGES'); ?>
        </p>
    </div><?php
}
