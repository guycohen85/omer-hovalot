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

<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

        <p><?php echo JText::plural('COM_VITABOOK_IMPORT_N_GUESTBOOKS', count($this->guestbooks)); ?></p>

        <form method="post" action="<?php echo JRoute::_('index.php?option=com_vitabook'); ?>">
            <div class="control-group">
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
            </div>

            <?php
            if($this->vbMessages)
            { ?>
                <div class="control-group">
                    <div class="control-label">
                        <label for="vitabookImport" title=""><?php echo JText::_('COM_VITABOOK_IMPORT_VBMESSAGES_EXISTING'); ?></label>		
                    </div>
                    <div class="controls">
                        <fieldset id="vitabookImport" class="radio btn-group">
                            <input id="vitabookImport0" name="vbImport" value="0" checked="checked" type="radio">
                            <label class="btn btn-danger" for="vitabookImport0"><?php echo JText::_('COM_VITABOOK_IMPORT_VBMESSAGES_DELETE'); ?></label>
                            <input id="vitabookImport1" name="vbImport" value="1" type="radio">
                            <label class="btn" for="vitabookImport1"><?php echo JText::_('COM_VITABOOK_IMPORT_VBMESSAGES_MERGE'); ?></label>                        
                        </fieldset>
                    </div>
                </div>

                <?php
                if($this->vbReplies)
                { ?>               
                    <div class="control-group">
                        <div class="control-label">
                            <label for="vitabookImportReplies" title=""><?php echo JText::_('COM_VITABOOK_IMPORT_VBREPLIES'); ?></label>		
                        </div>
                        <div class="controls">
                            <fieldset id="vitabookImportReplies" class="radio btn-group">
                                <input id="vbImportReplies0" name="vbImportReplies" value="0" checked="checked" type="radio">
                                <label class="btn btn-danger" for="vbImportReplies0"><?php echo JText::_('COM_VITABOOK_IMPORT_VBREPLIES_DELETE'); ?></label>
                                <input id="vbImportReplies1" name="vbImportReplies" value="1" type="radio">
                                <label class="btn" for="vbImportReplies1"><?php echo JText::_('COM_VITABOOK_IMPORT_VBREPLIES_CONVERT'); ?></label>                        
                            </fieldset>
                        </div>
                    </div>            
                <?php
                }
            }
            ?>
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" name="task" value="import.import" />
            
            <div style="margin-top:30px;">
                <button type="submit" class="button btn btn-primary"><?php echo JText::_('COM_VITABOOK_IMPORT_IMPORT'); ?></button>
            </div>
        </form>
    </div>
