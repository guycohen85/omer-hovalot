<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

//-- No direct access
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo JRoute::_('index.php?option=com_vitabook&layout=edit&id='.(int) $this->message->id); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
    <div class="span12 form-horizontal">
            <?php foreach($this->form->getFieldset() as $field): 
                if($field->hidden):
					echo $field->input;
                else :            
            ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $field->label; ?>
                        </div>
                        <div class="controls">
                            <?php echo $field->input; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div>
                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
    </div>
</form>