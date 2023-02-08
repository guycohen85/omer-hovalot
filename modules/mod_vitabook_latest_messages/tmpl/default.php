<?php
/**
 * @version     2.2.1
 * @package     mod_vitabook_latest_messages
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;
?>

<div class="vb_lm_latest-messages">
    <?php foreach($messages as $message) : ?>
        <?php if($messagelink) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_vitabook&messageId='.$message->id).'#vb'.$message->id; ?>" title="<?php echo JText::_('MOD_VITABOOK_LM_NAMELINK_TITLE'); ?>">
        <?php endif; ?>        
            <div class="vb_lm_message <?php if(empty($message->activated)){ echo "vb_lm_messageUnactivated"; } elseif(empty($message->published)){ echo "vb_lm_messageUnpublished"; } ?>">
                <div class="vb_lm_name">
                    <?php echo $message->name; ?>
					<?php if(($showlocation) && !empty($message->location)) : ?>
                    <span class="vb_lm_location">
                        <?php echo " - " . $message->location; ?>
                    </span>
                <?php endif; ?>
                </div>
                <div class="vb_lm_text">
                    <p><?php echo $message->message; ?></p>
                </div>
                <?php if($showdate) : ?>
                    <div class="vb_lm_date">
                        <?php echo $message->date; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php if($messagelink) : ?>
            </a>
        <?php endif; ?>   
    <?php endforeach; ?>
    
    <?php if($readmore) : ?>
    <div class="vb_lm_readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_vitabook'); ?>">
            <?php echo JText::_('MOD_VITABOOK_LM_READMORE'); ?>
        </a>
    </div>
    <?php endif; ?>
</div>
