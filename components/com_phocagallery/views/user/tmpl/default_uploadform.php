<table>
	<tr>
		<td><?php echo JText::_('COM_PHOCAGALLERY_FILENAME');?>:</td>
		<td>
		
		<input type="file" id="file-upload" name="Filedata" />
			<button class="btn btn-primary" id="file-upload-submit"><i class="icon-upload icon-white"></i> <?php echo JText::_('COM_PHOCAGALLERY_START_UPLOAD') ?></button>
			<span id="upload-clear"></span>
		</td>
	</tr>

	<tr>
		<td><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_TITLE' ); ?>:</td>
			<td>
				<input type="text" id="phocagallery-upload-title" name="phocagalleryuploadtitle" value=""  maxlength="255" class="comment-input" /></td>
		</tr>
		
		<tr>
			<td><?php echo JText::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</td>
			<td><textarea id="phocagallery-upload-description" name="phocagalleryuploaddescription" onkeyup="countCharsUpload('<?php echo $this->tmpl['upload_form_id']; ?>');" cols="30" rows="10" class="comment-input"></textarea></td>
		</tr>
			
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagalleryuploadcountin" value="0" readonly="readonly" class="comment-input2" /> <?php echo JText::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocagalleryuploadcountleft" value="<?php echo $this->tmpl['maxuploadchar'];?>" readonly="readonly" class="comment-input2" />
			</td>
		</tr>
</table>

<input type="hidden" name="controller" value="user" />
<input type="hidden" name="viewback" value="user" />
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->tmpl['currenttab']['images'];?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="filter_order_image" value="<?php echo $this->listsimage['order']; ?>" />
<input type="hidden" name="filter_order_Dir_image" value="" />
<input type="hidden" name="catid" value="<?php echo $this->tmpl['catidimage'] ?>"/>

<?php


if ($this->tmpl['upload_form_id'] == 'phocaGalleryUploadFormU') {
	echo '<div id="loading-label" style="text-align:center">'
	. JHtml::_('image', 'media/com_phocagallery/images/icon-switch.gif', '') 
	. '  '.JText::_('COM_PHOCAGALLERY_LOADING').'</div>';
}
?>