<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */
 
// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.image.image');

/**
 * Upload controller class.
 */
class VitabookControllerEditor extends JControllerLegacy
{
	/**
	 * Show the VitabookUpload plugin content for Joomla 3.2 and higher
	 **/    
    public function showVitabookUpload()
    {
    ?>
		<!DOCTYPE HTML>
		<html>
		<head>
            <title><?php echo JText::_('COM_VITABOOK_IMAGE_UPLOAD_TITLE'); ?></title>
            <link rel="stylesheet" href="<?php echo JURI::root() . "media/jui/css/bootstrap.min.css"; ?>" type="text/css" />

            <script type="text/javascript">
					//-- Upload or embed image?
					function insert()
					{
						<?php if (JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook')) : ?>
							if (document.getElementById('url').value != '') {
								startInsert();
							} else if (document.getElementById('image').value != '') {
								startUpload();
                            } else if ((document.getElementById('image').value == '') && (document.getElementById('url').value == '')) {
								alert('<?php echo JText::_('COM_VITABOOK_UPLOAD_NOT_SELECTED'); ?>');
								return false;
                            }
						<?php else : ?>
							if (document.getElementById('url').value!= '') {
								startInsert();
							} else {
                                alert('<?php echo JText::_('COM_VITABOOK_UPLOAD_NOT_SELECTED'); ?>');
                                return false;
                            }
						<?php endif; ?>
					}
					
				    //-- Upload image
					<?php if (JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook')) : ?>
						function startUpload()
						{
							if(document.getElementById('image').value != '') {
								document.getElementById('upload_process').innerHTML = '<img src="<?php echo JURI::root() . "components/com_vitabook/assets/editor_plugins/vitabookupload/img/loader.gif"; ?>" alt="loading" />';
								document.getElementById('insert-button').disabled=true;
								document.getElementById('input_field').style.visibility = 'hidden';
								document.getElementById('UploadForm').submit();
								return true;
							} else {
								alert('<?php echo JText::_('COM_VITABOOK_UPLOAD_NOT_SELECTED'); ?>');
								return false;
							}
						}
					
						//-- If upload error, return error and close
						function uploadError(response)
						{
							document.getElementById('upload_process').innerHTML = '';
							document.getElementById('input_field').style.visibility = '';
					
							//-- Return error and close
							alert(response);
							top.tinymce.activeEditor.windowManager.close();
						}
						
						//-- If upload was succesfull, insert <img> tag
						function stopUpload(response)
						{
							document.getElementById('upload_process').innerHTML = '';
							document.getElementById('input_field').style.visibility = '';
							
							//-- Insert image and close
                            top.tinymce.activeEditor.insertContent(response);
                            top.tinymce.activeEditor.windowManager.close();
						}
					<?php endif; ?>
					
                    //-- Insert image form url
				    function startInsert()
				    {
						if(document.getElementById('url').value != '') {
							document.getElementById('insert_process').innerHTML = '<img src="<?php echo JURI::root() . "components/com_vitabook/assets/editor_plugins/vitabookupload/img/loader.gif"; ?>" alt="loading" />';
							document.getElementById('insert-button').disabled=true;
							document.getElementById('insert_field').style.visibility = 'hidden';
							document.getElementById('InsertForm').submit();
							return true;
						}
				    }
					
					//-- If insert was succesfull, insert <img> tag
					function stopInsert(response)
					{
    					document.getElementById('insert_process').innerHTML = '';
    					document.getElementById('insert_field').style.visibility = '';
    					
						//-- Insert image and close
                        top.tinymce.activeEditor.insertContent(response);
                        top.tinymce.activeEditor.windowManager.close();
					}

					//-- If upload error, return error and close
					function insertError(response)
					{
						document.getElementById('insert_process').innerHTML = '';
						document.getElementById('insert_field').style.visibility = '';
				
						//-- Return error and close
						alert(response);
				        top.tinymce.activeEditor.windowManager.close();
					}
            
            </script>
        </head>
        <body>
            <div class="modal-body">
                <?php if (JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook') AND JFactory::getUser()->authorise('vitabook.insert.image', 'com_vitabook')) : ?>
                    <fieldset>
                        <legend><?php echo JText::_('COM_VITABOOK_UPLOAD_LEGEND'); ?></legend>
                        <form action="<?php echo JRoute::_('index.php?option=com_vitabook&task=editor.upload'); ?>" method="post" enctype="multipart/form-data" id="UploadForm" target="upload_target">
                            <span id="input_field">
                                <input style="display: none;" type="file" id="image" name="image" accept="image/*" onchange="document.getElementById('vbUploadImage').value = this.value;" />
                                <div class="input-append">
                                    <input id="vbUploadImage" class="input-medium" type="text" readonly="readonly" onclick="document.getElementById('image').click();" />
                                    <a class="btn" onclick="document.getElementById('image').click();">Browse</a>
                                </div>
                            </span>
                            <span id="upload_process"></span>
                        </form>
                    </fieldset>
                <?php endif; ?>

	            <iframe id="upload_target" name="upload_target" src="" style="visibility:hidden;width:0;height:0;border:0px solid #fff;"></iframe>

                <fieldset>
                    <legend><?php echo JText::_('COM_VITABOOK_INSERT_LEGEND'); ?></legend>
                    <form action="<?php echo JRoute::_('index.php?option=com_vitabook&task=editor.insert'); ?>" method="post" id="InsertForm" target="upload_target">
                        <span id="insert_field">
                            <input type="text" class="input-xlarge" name="url" id="url" placeholder="<?php echo JText::_('COM_VITABOOK_UPLOAD_URL'); ?>" />
                        </span>
                        <span id="insert_process"></span>
                    </form>
                </fieldset>                
            </div>
            <div class="modal-footer">                
                <button class="btn btn-primary" id="insert-button" name="insert-button" onclick="insert();"><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_BUTTON_INSERT'); ?></button>
                <button class="btn" id="cancel" name="cancel" onclick="top.tinymce.activeEditor.windowManager.close();"><?php echo JText::_('COM_VITABOOK_UPLOAD_BUTTON_CLOSE'); ?></button>
            </div>
        </body>
        </html>
        <?php
		$app = JFactory::getApplication();
		$app->close();
    }

	/**
	 * Show the plugin content
	 *
	 * Displays upload and insert form, depending on ACL
	 */
    public function showVitabookUploadLegacy()
    {
	   ?> 
		<!DOCTYPE HTML>
		<html>
			<head>
				<title><?php echo JText::_('COM_VITABOOK_IMAGE_UPLOAD_TITLE'); ?></title>
				<link rel="stylesheet" href="<?php echo JURI::root() . "components/com_vitabook/assets/editor_plugins/legacy/vitabookuploadLegacy/css/vitabookupload.css"; ?>" type="text/css" />
				<script type="text/javascript" src="<?php echo JURI::root() . "media/editors/tinymce/jscripts/tiny_mce/tiny_mce_popup.js"; ?>"></script>
				
				<script type="text/javascript">
				
					//-- Upload or embed image?
					function insert()
					{
						<?php if (JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook')) { ?>
							if ((document.getElementById('image').value == '') && (document.getElementById('url').value != '')) {
								startInsert();
							} else if ((document.getElementById('image').value != '') && (document.getElementById('url').value == '')) {
								startUpload();
							}
						<?php } else { ?>
							if (document.getElementById('url').value!= '') {
								startInsert();
							}
						<?php } ?>
					}
					
				    //-- Upload image
					<?php if (JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook')) { ?>
						function startUpload()
						{
							if(document.getElementById('image').value != '') {
								document.getElementById('upload_process').innerHTML = '<img src="<?php echo JURI::root() . "components/com_vitabook/assets/editor_plugins/legacy/vitabookuploadLegacy/img/loader.gif"; ?>" alt="loading" />';
								document.getElementById('insert-button').disabled=true;
								document.getElementById('input_field').style.visibility = 'hidden';
								document.getElementById('UploadForm').submit();
								return true;
							} else {
								alert('<?php echo JText::_('COM_VITABOOK_UPLOAD_NOT_SELECTED'); ?>');
								return false;
							}
						}
					
						//-- If upload error, return error and close
						function uploadError(response)
						{
							document.getElementById('upload_process').innerHTML = '';
							document.getElementById('input_field').style.visibility = '';
					
							//-- Return error and close
							alert(response);
							tinyMCEPopup.close();
						}
						
						//-- If upload was succesfull, insert <img> tag
						function stopUpload(response)
						{
							document.getElementById('upload_process').innerHTML = '';
							document.getElementById('input_field').style.visibility = '';
							
							//-- Insert image and close
							tinyMCEPopup.editor.execCommand('mceInsertContent', false, response);
							tinyMCEPopup.close();
						}
					<?php } ?>
					
                    //-- Insert image form url
				    function startInsert()
				    {
						if(document.getElementById('url').value != '') {
							document.getElementById('insert_process').innerHTML = '<img src="<?php echo JURI::root() . "components/com_vitabook/assets/editor_plugins/legacy/vitabookuploadLegacy/img/loader.gif"; ?>" alt="loading" />';
							document.getElementById('insert-button').disabled=true;
							document.getElementById('insert_field').style.visibility = 'hidden';
							document.getElementById('InsertForm').submit();
							return true;
						}
				    }
					
					//-- If insert was succesfull, insert <img> tag
					function stopInsert(response)
					{
    					document.getElementById('insert_process').innerHTML = '';
    					document.getElementById('insert_field').style.visibility = '';
    					
						//-- Insert image and close
    			        tinyMCEPopup.editor.execCommand('mceInsertContent', false, response);
    					tinyMCEPopup.close();
					}

					//-- If upload error, return error and close
					function insertError(response)
					{
						document.getElementById('insert_process').innerHTML = '';
						document.getElementById('insert_field').style.visibility = '';
				
						//-- Return error and close
						alert(response);
				        tinyMCEPopup.close();
					}			
				</script>
				
			</head>
			<body>
				<?php if (JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook') AND JFactory::getUser()->authorise('vitabook.insert.image', 'com_vitabook')) { ?>
					<form action="index.php?option=com_vitabook&task=editor.upload" method="post" enctype="multipart/form-data" id="UploadForm" target="upload_target">
						<div class="tabs"></div>
						<div class="panel_wrapper">
							<h2><?php echo JText::_('COM_VITABOOK_UPLOAD_LEGEND'); ?></h2>
							<span id="input_field"><input type="file" size="50" id="image" name="image" accept="image/*" /></span>
							<span id="upload_process"></span>
						</div>
					</form>
				<?php } ?>
				
				<iframe id="upload_target" name="upload_target" src="" style="visibility:hidden;width:0;height:0;border:0px solid #fff;"></iframe>
				
				<form action="index.php?option=com_vitabook&task=editor.insert" method="post" id="InsertForm" target="upload_target">
					<div class="tabs"></div>
    				<div class="panel_wrapper">
				        <h2><?php echo JText::_('COM_VITABOOK_INSERT_LEGEND'); ?></h2>
				        <span id="insert_field"><input name="url" type="url" id="url" size="50" class="mceFocus" /></span>
						<span id="insert_process"></span>
   				    </div>
				</form>
				<div class="mceActionPanel">
					<button id="insert-button" class="upload-button" onclick="insert();"><?php echo JText::_('COM_VITABOOK_UPLOAD_BUTTON_INSERT'); ?></button>
					<button id="cancel" onclick="tinyMCEPopup.close();" class="close-button"><?php echo JText::_('COM_VITABOOK_UPLOAD_BUTTON_CLOSE'); ?></button>
				</div>				        				    
			</body>
		</html>

		<?php    
		$app = JFactory::getApplication();
		$app->close();
    }
	
	/**
	 * Show the VitabookVideo plugin content for Joomla 3.2 and higher
	 **/    
    public function showVitabookVideo()
    {
    ?>
		<!DOCTYPE HTML>
		<html>
		<head>
            <title><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_TITLE'); ?></title>
            <link rel="stylesheet" href="<?php echo JURI::root() . "media/jui/css/bootstrap.min.css"; ?>" type="text/css" />
            
            <script type="text/javascript">
                function insertVitabookVideo()
                {
					//-- Read values from dialog box and insert into editor
					videoUrl = document.getElementById("video_url").value;
                    link = parseUrl(videoUrl);

                    if(link !== false)
                    {
                        // Embed video and close modalbox
                        top.tinymce.activeEditor.insertContent(link);
                        top.tinymce.activeEditor.windowManager.close();
                    }
                }
            
				/**
				 * Function to parse Youtube or Vimeo url
				 * Input: youtube or vimeo url
				 * Output: code for youtube or vimeo player
				 */
				function parseUrl(url)
				{
                    var videoSize = 'width="350px" height="300px"';

					if(url.match(/http:\/\/(www.)?youtube|youtu\.be/i))
					{
						//-- Extract and return youtube id
						var regExp = /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|player_embedded\&v=)([^#\&\?]*).*/;
						var match = url.match(regExp);
						if (match&&match[1].length==11) {
							//-- Make youtube url
							var youtubeUrl = "http://www.youtube.com/embed/"+match[1];							
							//-- Insert video into document
							var	objectCode = '<iframe src="'+youtubeUrl+'?wmode=opaque" '+videoSize+'></iframe>';

							return objectCode;
						} else {
							alert("<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_INCORRECT_URL'); ?>");
						}
					}
					else if(url.match(/http:\/\/(player.)?vimeo\.com/i))
					{
						//-- Extract and return vimeo id
						var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
						var match = url.match(regExp);
						if (match) {
							//-- Make vimeo url
							var vimeoUrl = "http://player.vimeo.com/video/"+match[2];
							var	objectCode = '<iframe src="'+vimeoUrl+'" '+videoSize+'></iframe>';

							return objectCode;
							
						} else {
							alert("<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_INCORRECT_URL'); ?>");
						}
					}
					else
					{
						alert("<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_UNKNOWN_URL'); ?>");
					}
					return false;
				}            
            </script>
        </head>
        <body>
            <div class="modal-body">
                <fieldset>
                    <legend><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_HEADING'); ?></legend>
                    <input type="text" class="input-xlarge" maxlength="300" name="video_url" id="video_url" placeholder="<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_URL'); ?>" />
                </fieldset>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="insert" name="insert" onclick="insertVitabookVideo();"><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_BUTTON_INSERT'); ?></button>
                <button class="btn" id="cancel" name="cancel" onclick="top.tinymce.activeEditor.windowManager.close();"><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_BUTTON_CLOSE'); ?></button>
            </div>
        </body>
        </html>
        <?php
		$app = JFactory::getApplication();
		$app->close();    
    }

	/**
	 * Show the VitabookVideo plugin content for Joomla 3.1 and lower
	 **/
	public function showVitabookVideoLegacy()
	{
	?>
		<!DOCTYPE HTML>
		<html>
		<head>

			<title><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_TITLE'); ?></title>
			<script type="text/javascript" src="<?php echo JURI::root() . "media/editors/tinymce/jscripts/tiny_mce/tiny_mce_popup.js"; ?>"></script>			
		
			<script type="text/javascript">
				function insertVitabookVideo()
				{
					//-- Read values from dialog box and insert into editor
					var videoURL     = document.forms[0].video_url.value;
					
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, parseUrl(videoURL));
					tinyMCEPopup.close();
				}
				
				/**
				 * Function to parse Youtube or Vimeo url
				 * Input: youtube or vimeo url
				 * Output: code for youtube or vimeo player
				 */
                function parseUrl(url)
                {
                    var videoSize = 'width="350px" height="300px"';

                    if(url.match(/http:\/\/(www.)?youtube|youtu\.be/i))
                    {
                        //-- Extract and return youtube id
                        var regExp = /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|player_embedded\&v=)([^#\&\?]*).*/;
                        var match = url.match(regExp);
                        if (match&&match[1].length==11) {
                            //-- Make youtube url
                            var youtubeUrl = "http://www.youtube.com/embed/"+match[1];
                            //-- Insert video into document
                            var	objectCode = '<iframe src="'+youtubeUrl+'?wmode=opaque" '+videoSize+'></iframe>';

                            return objectCode;
                        } else {
                            alert("<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_INCORRECT_URL'); ?>");
                        }
                    }
                    else if(url.match(/http:\/\/(player.)?vimeo\.com/i))
                    {
                        //-- Extract and return vimeo id
                        var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
                        var match = url.match(regExp);
                        if (match) {
                            //-- Make vimeo url
                            var vimeoUrl = "http://player.vimeo.com/video/"+match[2];
                            var	objectCode = '<iframe src="'+vimeoUrl+'" '+videoSize+'></iframe>';

                            return objectCode;

                        } else {
                            alert("<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_INCORRECT_URL'); ?>");
                        }
                    }
                    else
                    {
                        alert("<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_UNKNOWN_URL'); ?>");
                    }
                    return false;
                }
            </script>
		</head>
		<body>
			<form onsubmit="insertVitabookVideo();return false;" action="#">
				<div class="tabs"></div>
				<div class="panel_wrapper">
					<h2><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_HEADING'); ?></h2>
					<br />
					<table>
						<tr>
							<td><?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_URL'); ?>:</td>
							<td>
								<input type="text" maxlength="300" size="30" name="video_url" id="video_url" class="mceFocus" />
							</td>
						</tr>
					</table>
				</div>
				<div class="mceActionPanel">
					<input type="button" id="insert" name="insert" value="<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_BUTTON_INSERT'); ?>" onclick="insertVitabookVideo();" />
					<input type="button" id="cancel" name="cancel" value="<?php echo JText::_('COM_VITABOOK_VITABOOKVIDEO_BUTTON_CLOSE'); ?>" onclick="tinyMCEPopup.close();" />
				</div>
			</form>
		</body>
		</html>
	<?php
		$app = JFactory::getApplication();
		$app->close();
	}

	/**
	 * Upload image
	 *
	 * Image is uploaded, renamed and resized
	 */
	public function upload()
	{
		//-- Check if user is authorised to upload images
		if (!JFactory::getUser()->authorise('vitabook.upload.image', 'com_vitabook')) {
            $this->closeboxError(JText::_('COM_VITABOOK_UPLOAD_NOTAUTHORISED'));
            return false;
		}
		
		//-- Get parameters for image uploading
		$params = JComponentHelper::getParams('com_vitabook');
		$imageQuality = $params->get('upload_image_quality');
		$imageWidth = $params->get('upload_image_width');
		
		$succes = 0;

		//-- Check if image is uploaded
		if(isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"]))
		{
			//-- Check if file type is supported
			$types = array('image/gif', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png');
			if(!in_array($_FILES["image"]["type"], $types)) {
				//-- Unknown format
                $this->closeboxError(JText::_('COM_VITABOOK_UNKNOWN_FORMAT'));
                return false;
			}
			try {
				$fileInfo = JImage::getImageFileProperties($_FILES["image"]["tmp_name"]);
			}
			catch(Exception $e) {
                $this->closeboxError(JText::_('COM_VITABOOK_UNKNOWN_FORMAT'));
                return false;		
			}
            
			//-- If image is uploaded, create JImage object and load image.
			$image = new JImage;
			try {
				$image->loadFile($_FILES["image"]["tmp_name"]);
			}
			//-- Loading image failed, stop!
			catch(Exception $e) {
                $this->closeboxError(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
                return false;		
			}
			//-- Loading image failed, stop!
			if(!$image->isLoaded()) {
                $this->closeboxError(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
                return false;	
			}
		}
		else 
		{
			//-- Upload failed, stop!
            $this->closeboxError(JText::_('COM_VITABOOK_POST_FAILED'));
            return false;
		}
        
        //-- Check for memory
        if(!VitabookHelper::checkMemory($image)) {
            $this->closeboxError(JText::_('COM_VITABOOK_UPLOADING_FAILED_MEMORY'));
            return false;      
        }
		
		//-- Generate random filename
		$fileName = md5($image->getPath() + (rand()*1000));
		//-- Get correct extension
		switch (JImage::getImageFileProperties($image->getPath())->mime) {
			case 'image/gif':
				$extension = 'gif';
				$img_type = 'IMAGETYPE_GIF';
				break;
			case 'image/png':
				$extension = 'png';
				$img_type = 'IMAGETYPE_PNG';
				break;
			case 'image/x-png':
				$extension = 'png';
				$img_type = 'IMAGETYPE_PNG';
				break;				
			case 'image/jpeg':
				$extension = 'jpg';
				$img_type = 'IMAGETYPE_JPEG';
				break;
			case 'image/pjpeg':
				$extension = 'jpg';
				$img_type = 'IMAGETYPE_JPEG';
				break;			
			default :
				//-- Error, unknown image type
                $this->closeboxError(JText::_('COM_VITABOOK_UNKNOWN_FORMAT'));
                return false;				
		}
		
		//-- Where to store the image
		$dest = JPATH_BASE.'/media/com_vitabook/images/uploaded/'.$fileName.'.'.$extension;
		$loc = JURI::root().'media/com_vitabook/images/uploaded/'.$fileName.'.'.$extension;
		
		//-- Check if destination folder exists
		if(!JFolder::exists(JPATH_BASE.'/media/com_vitabook/images/uploaded/')) {
            $this->closeboxError(JText::_('COM_VITABOOK_NO_FOLDER'));
            return false;   		
		}
		
		//-- Resize image proportional to its original size
		try {
			$image->resize($imageWidth,'100%',false);
		}
		//-- Loading image failed, stop!
		catch(Exception $e) {
            $this->closeboxError(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
            return false; 			
		}
		
		//-- Copy the renamed file to media/com_vitabook/images.
		@$image->toFile($dest, $img_type, array('quality' => $imageQuality));
		
		//-- Check if uploading was successful 
		if(JFile::exists($dest)) {
			$succes = 1;
		}

		//-- If no errors during the upload process return image code
		if($succes === 1) {
			//-- Sent output back
			?><script type="text/javascript">
				window.parent.stopUpload('<?php echo $this->imgHtml($loc); ?>');
			</script><?php
			return true;
		} else {
			//-- Upload failed, stop!
            $this->closeboxError(JText::_('COM_VITABOOK_UPLOADING_FAILED'));
		}
		return false;
	}

	/**
	 * Insert image
	 */
	public function insert()
	{
		//-- Check if user is authorised to insert images
		if (!JFactory::getUser()->authorise('vitabook.insert.image', 'com_vitabook')) {
            $this->closeboxError(JText::_('COM_VITABOOK_INSERT_NOTAUTHORISED'));
            return false;
		}

        $url = JFactory::getApplication()->input->get('url', null, 'string');
		if(!VitabookControllerEditor::isImage($url))
		{
			$this->closeboxError(JText::_('COM_VITABOOK_UNKNOWN_FORMAT'));
			return false;
		}

		//-- Sent output back
		?><script type="text/javascript">
			window.parent.stopInsert('<?php echo $this->imgHtml($url); ?>');
		</script><?php
		return true;		
	}

	/**
	 * Function to check if insert url is an image
	 * http://stackoverflow.com/questions/676949/best-way-to-determine-if-a-url-is-an-image-in-php
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	public static function isImage($url)
	{
		$params = array('http' => array(
			'method' => 'HEAD'
		));
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp)
			return false;  // Problem with url

		$meta = stream_get_meta_data($fp);
		if ($meta === false)
		{
			fclose($fp);
			return false;  // Problem reading data from url
		}

		$wrapper_data = $meta["wrapper_data"];
		if(is_array($wrapper_data)){
			foreach(array_keys($wrapper_data) as $hh){
				if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") // strlen("Content-Type: image") == 19
				{
					fclose($fp);
					return true;
				}
			}
		}
		fclose($fp);

		return false;
	}


	/**
	 * Create html for img tag
	 */
	private function imgHtml($url, $width = '', $height = '', $alt = '')
	{
		//-- Get maximal image width from parameters
		$params = JFactory::getApplication()->getParams();
		$width = $params->get('upload_image_width');
			
		//-- If necessary change img width/height 
		list($img_width, $img_height, $img_type, $img_attr) = getimagesize($url);
		if($img_width < $width) {
			$width = $img_width;
		}
		
		if($width != '') {$width = 'width="'.$width.'px"';}
		if($height != '') {$height = 'height="'.$height.'px"';}
		$html = '<img src="'.$url.'" '.$width . $height .' alt="'.$alt.'" class="vitabook_image" />';

		return $html;
	}
    
   /**
    * Method to close a modal box and display error message
    */		    
    private function closeboxError($message)
    {
        echo "<script type='text/javascript'>window.parent.insertError('".$message."');</script>";
    }
}