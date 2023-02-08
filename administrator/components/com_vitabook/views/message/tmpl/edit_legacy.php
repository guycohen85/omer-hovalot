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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>

<form action="<?php echo JRoute::_('index.php?option=com_vitabook&layout=edit&id='.(int) $this->message->id); ?>" method="post" name="adminForm" id="adminForm">
	<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_VITABOOK_MESSAGE_FIELDSET_LEGEND'); ?></legend>
				<ul class="adminformlist">
					<?php
					foreach($this->form->getFieldset() as $field):
						//-- If the field is hidden, only use the input.
						if ($field->hidden):
							echo $field->input;
						//-- If field is editor, change layout
						elseif ($field->type == 'vitabookEditor'):
							echo '<div class="clr"></div>';
							echo $field->label;
							echo '<div class="clr"></div>';
							echo $field->input;
						else:
						?>
						<li>
							<?php echo $field->label; ?>
							<?php echo $field->input; ?>
						</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</fieldset>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>