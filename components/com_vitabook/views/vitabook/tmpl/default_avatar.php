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

JHtml::_('behavior.framework');
JHTML::stylesheet('components/com_vitabook/assets/vitabook.css');
// Add little style declaration to hide scrollbar in modal box
//JFactory::getDocument()->addStyleDeclaration('body{overflow: hidden;}');

?>

<div class="vbAvatarContainer">
	<div class="vbAvatarCurrent">
            <h3><?php echo JText::_('COM_VITABOOK_AVATAR_CURRENT'); ?></h3>
            <img src="<?php echo $this->avatar; ?>" alt="" />
	</div>
	<div class="vbAvatarUpload">
        <h3><?php echo JText::_('COM_VITABOOK_AVATAR_UPLOAD'); ?></h3>
        <form action="<?php echo JRoute::_('index.php?option=com_vitabook&task=avatar.upload'); ?>" method="post" enctype="multipart/form-data" name="UploadForm">
            <input style="display: none;" type="file" id="image" name="image" accept="image/*" onchange="$('avatar').value = $(this).value;" />
            <div class="input-append">
                <input id="avatar" class="input-medium" type="text" readonly="readonly" onclick="$('image').click();" />
                <a class="btn" onclick="$('image').click();">Browse</a>
            </div>
        </form>
        <button class="btn avatar-button" onclick="location='<?php echo JRoute::_('index.php?option=com_vitabook&task=avatar.delete'); ?>';"><?php echo JText::_('COM_VITABOOK_AVATAR_DELETE'); ?></button>
        <button class="btn avatar-button" onclick="if(document.getElementById('image').value != '')document.UploadForm.submit();"><?php echo JText::_('COM_VITABOOK_AVATAR_UPLOAD'); ?></button>
    </div>
</div>
