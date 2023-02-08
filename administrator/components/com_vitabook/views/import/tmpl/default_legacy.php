<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */


//-- No direct access
defined('_JEXEC') or die; ?>

<div class="width-60 fltlft">

    <?php echo JText::plural('COM_VITABOOK_IMPORT_N_GUESTBOOKS', count($this->guestbooks)); ?>

    <form method="post" action="<?php echo JRoute::_('index.php?option=com_vitabook'); ?>">
        <select name="import">
            <option value="99" selected="selected">-</option><?php 
            foreach ($this->guestbooks as $key => $gb): ?>
                <option value="<?php echo $key; ?>">
                    <?php echo $gb->title; ?>
                    (<?php echo JText::plural('COM_VITABOOK_IMPORT_N_MESSAGES', $gb->number_of_messages); ?>)
                </option>
            <?php    
            endforeach;
            ?>
        </select>

        <?php
        if($this->vbMessages)
        { ?>
            <div style="margin-top:20px;">
                <?php echo JText::_('COM_VITABOOK_IMPORT_VBMESSAGES_EXISTING'); ?><br />
                <input id="vitabookImport0" type="radio" name="vbImport" value="0" checked="checked">
                <label for="vitabookImport0"><?php echo JText::_('COM_VITABOOK_IMPORT_VBMESSAGES_DELETE'); ?></label>
                <br />
                <input id="vitabookImport1" type="radio" name="vbImport" value="1">
                <label for="vitabookImport1"><?php echo JText::_('COM_VITABOOK_IMPORT_VBMESSAGES_MERGE'); ?></label> 
            </div>

            <?php
            if($this->vbReplies)
            { ?>
                <div style="margin-top:20px;">
                    <?php echo JText::_('COM_VITABOOK_IMPORT_VBREPLIES'); ?><br />
                    <input id="vbImportReplies0" type="radio" name="vbImportReplies" value="0" checked="checked">
                    <label for="vbImportReplies0"><?php echo JText::_('COM_VITABOOK_IMPORT_VBREPLIES_DELETE'); ?></label>
                    <br />
                    <input id="vbImportReplies1" type="radio" name="vbImportReplies" value="1">
                    <label for="vbImportReplies1"><?php echo JText::_('COM_VITABOOK_IMPORT_VBREPLIES_CONVERT'); ?></label> 
                </div>
                <?php
            }
        }
        ?>
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="task" value="import.import" />

        <div style="margin-top:20px;">
            <button type="submit" class="button btn btn-primary"><?php echo JText::_('COM_VITABOOK_IMPORT_IMPORT'); ?></button>
        </div>
    </form>
</div>
