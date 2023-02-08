<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class OPCparametersJForm
{
   function render($form)
   {
     $fieldSets = $form->getFieldsets();
	 $control_label_class = 'control'; 
	 $control_field_class = 'class'; 
	 $control_input_class = 'input'; 
	 $control_group_class = 'group'; 
	 ob_start(); 
		if (!empty($fieldSets)) {
			?>

				<?php
				foreach ($fieldSets as $name => $fieldSet) {
					?>
					<div class="<?php echo $control_field_class ?>">
						<?php
					$label = !empty($fieldSet->label) ? $fieldSet->label : strtoupper('VMPSPLUGIN_FIELDSET_' . $name);

						if (!empty($label)) {
							$class = isset($fieldSet->class) && !empty($fieldSet->class) ? "class=\"".$fieldSet->class."\"" : '';
							?>
							<h3> <span<?php echo $class  ?>><?php echo vmText::_($label) ?></span></h3>
							<?php
							if (isset($fieldSet->description) && trim($fieldSet->description)) {
								echo '<p class="tip">' . $this->escape(vmText::_($fieldSet->description)) . '</p>';
							}
						}
					?>

					<?php $i=0; ?>
					<?php foreach ($form->getFieldset($name) as $field) { ?>
						<?php if (!$field->hidden) {
							?>
						<div class="<?php echo $control_group_class ?>">
							<div class="<?php echo $control_label_class ?>">
									<?php echo $field->label; ?>
							</div>
							<div class="<?php echo $control_input_class ?>">
									<?php 
									
									echo $field->input; ?>
							</div>
						</div>
					<?php } ?>
					<?php } ?>

				</div>
				<?php

				}
				?>

		<?php


		}
		return ob_get_clean(); 
   }
}