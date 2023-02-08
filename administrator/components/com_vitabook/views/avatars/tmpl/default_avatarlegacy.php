<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// no direct access
defined('_JEXEC') or die;

// Add little style declaration to hide scrollbar in modal box
JFactory::getDocument()->addStyleDeclaration('html{overflow: hidden;}');

?>
<div class="width-100 fltlft">
    <h1><?php echo $this->avatar->name; ?></h1>
</div>
<div style="clear:both;"></div>
<div class="width-40 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_VITABOOK_AVATAR_CURRENT'); ?></legend>
        <img src="<?php echo $this->avatar->url; ?>" alt="" />
        <button class="button" onclick="location='<?php echo JRoute::_('index.php?option=com_vitabook&task=avatar.delete&id='.$this->avatar->id); ?>';"><?php echo JText::_('COM_VITABOOK_AVATAR_DELETE'); ?></button>
    </fieldset>
</div>   
<div class="width-60 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_VITABOOK_AVATAR_UPLOAD'); ?></legend>
        <form action="<?php echo JRoute::_('index.php?option=com_vitabook&task=avatar.upload'); ?>" method="post" enctype="multipart/form-data" name="UploadForm">
            <input type="file" id="image" name="image" accept="image/*" />
            <input type="hidden" name="jid" value="<?php echo $this->avatar->id; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
        <button class="button" onclick="if(document.getElementById('image').value != '')document.UploadForm.submit();"><?php echo JText::_('COM_VITABOOK_AVATAR_UPLOAD'); ?></button>
    </fieldset>
</div>
