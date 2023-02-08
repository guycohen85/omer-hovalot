<?php

/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
class OPCimage {
    public static function op_image_info_array($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0)
	{
	 return OPCimage::op_image_tag($image, $args, $resize, $path_appendix, $thumb_width, $thumb_height, true );
	}
	
	function op_image_tag($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0, $retA = false ) 
	{

	$oi = $image; 
	if (empty($image)) 
	 {
		  $image = VmConfig::get('vm_themeurl', JURI::root().'components/com_virtuemart/').'assets/images/vmgeneral/'.VmConfig::get('no_image_set'); 
	 }
	if (strpos($image, 'http')===0)
	{
	     // if the image starts with http
	     $imga = array();
		 $imga['width'] = $thumb_width;
		 $imga['height'] = $thumb_height;
		 $imga['iurl'] = $image;
	}
	if (!file_exists($image) || (!is_file($image)))
	{
	  $image = VmConfig::get('vm_themeurl', JURI::root().'components/com_virtuemart/').'assets/images/vmgeneral/'.VmConfig::get('no_image_set'); 
	  $imga = array();
	  $imga['width'] = $thumb_width;
	  $imga['height'] = $thumb_height;
	  $imga['iurl'] = $image;
	}
	
	
		$height = $width = 0;
		
		$ow = $thumb_width; 
		$oh = $thumb_height; 
		
		if ($image != "") {
			$fi = pathinfo($image);
			
			//
			// to resize we need to know if to keep height or width
			
			$arr = getimagesize( $image );
			$width = $arr[0]; $height = $arr[1];
			
			if (empty($thumb_width) && (!empty($thumb_height)))
			{
			  $rate = $height / $thumb_height; // 1.5
			  $thumb_width = round($width / $rate);
			  // if width<height do nothing
			  //if ($width>$height && ())
			}
			else
			if (empty($thumb_height))
			{
			 $rate = $width / $thumb_width; 
			 $thumb_height = round($height / $rate); 
			}
			else
			if (empty($thumb_height) && (empty($thumb_width)))
			{
			  $thumb_height = $height;
			  $thumb_width = $width;
			}
			
			// check ratio: 
			$r1 = round($thumb_height / $thumb_width, 3); 
			$r2 = round($height / $width, 3); 
			
			if ($r1 != $r2)
			 {
			   // the ratio got changed
			   $thumb_height = $thumb_height * $r2; 
			   if ($thumb_height > $oh)
			   {
			   // reverse
			   $thumb_height = $thumb_height / $r2; 
			   $thumb_width = $thumb_width / $r2; 
			   }
			   //$thumb_width = $thumb_width * $r2; 
			 }
			
			if (!empty($fi['extension']))
			{
			$basename = str_replace('.'.$fi['extension'], '', $fi['basename']); 
			$u = VmConfig::get('media_product_path'); 
			$u = str_replace('/', DS, $u); 
			
			$filename = JPATH_SITE.DS.$u.$ow.'x'.$oh.DS.$fi['basename']; 
			$dirname = JPATH_SITE.DS.$u.$ow.'x'.$oh; 
			
			jimport( 'joomla.filesystem.file' );
			
			if (file_exists($filename)) 
			 { 
			   $arr = getimagesize( $filename );
			   if ($arr === false)
			    {
				
				  // we've got a corrupted image here
				  JFile::delete($filename); 
				}
			 }

			if (($width > $thumb_width) || ($height > $thumb_height) || (!(file_exists($filename))))
			 {
			 
			   if (!file_exists($dirname)) 
			    {
				 				  jimport( 'joomla.filesystem.folder' );
				  jimport( 'joomla.filesystem.file' );
				  
				  if (@JFolder::create($dirname)===false)
				   {
				 
				     // we can't create a directory and we don't want to get into a loop
				     return "&nbsp;"; 
				   }
				  $x = ' '; 
				   if (@JFile::write($dirname.DS.'index.html', $x)===false)
				   {
				     // we can't create a directory and we don't want to get into a loop
				     return "&nbsp;"; 
				   }

				}
				if (file_exists($dirname) && (!file_exists($filename)))
				{
				
				
				OPCimage::resizeImg($image, $filename, $thumb_width, $thumb_height, $width, $height); 
			    $arr = @getimagesize( $filename );
				if ($arr === false) return array(); 
			    $width = $arr[0]; $height = $arr[1];
				
				}
				else
				if (file_exists($dirname) && (file_exists($filename)))
				{
					 $arr = @getimagesize( $filename );
					if ($arr === false) return array(); 
					$width = $arr[0]; $height = $arr[1];
				}
				else
				{


					if (!empty($oi))
					return OPCimage::op_image_tag("", $args, 0, 'product', $thumb_width, $thumb_height, $retA);
					else 
					if ($retA===true)
					{
					  return array(); 
					  return "&nbsp;"; 
					 
					}
					else
					{
					
					}

				}
				
			   // we need to create it
			   // should be here:
			   //
			   
			 }
			}

			

		}
		
		if ($retA===true)
		{
		 if (!file_exists($filename)) return array(); 
		 $imga = array();
		 $imga['width'] = $width;
		 $imga['height'] = $height;
		 $imga['iurl'] = OPCimage::path2url($filename);
		 
		 
		 return $imga;
		}
		else 
		{
		if (empty($url)) return "&nbsp;"; 
		return '<img src="'.$url.'" />'; 
		}
		//return vmCommonHTML::imageTag( $url, '', '', $height, $width, '', '', $args.' '.$border );

	}
public function resizeImg($orig, $new,  $new_width, $new_height, $ow, $oh)
{


// What sort of image?
$info = GetImageSize($orig);

if(empty($info))
{
  return false;
}


$width = $info[0];
$height = $info[1];
$mime = $info['mime'];

$type = substr(strrchr($mime, '/'), 1);

switch ($type)
{
case 'jpeg':
    $image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
    break;

case 'png':
    $image_create_func = 'ImageCreateFromPNG';
    $image_save_func = 'ImagePNG';
	$new_image_ext = 'png';
    break;

case 'bmp':
    $image_create_func = 'ImageCreateFromBMP';
    $image_save_func = 'ImageBMP';
	$new_image_ext = 'bmp';
    break;

case 'gif':
    $image_create_func = 'ImageCreateFromGIF';
    $image_save_func = 'ImageGIF';
	$new_image_ext = 'gif';
    break;

case 'vnd.wap.wbmp':
    $image_create_func = 'ImageCreateFromWBMP';
    $image_save_func = 'ImageWBMP';
	$new_image_ext = 'bmp';
    break;

case 'xbm':
    $image_create_func = 'ImageCreateFromXBM';
    $image_save_func = 'ImageXBM';
	$new_image_ext = 'xbm';
    break;

default:
	$image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
}

	// New Image
	
	$image_c = ImageCreateTrueColor($new_width, $new_height);
	$new_image = $image_create_func($orig);
	
	 if($type == "gif" or $type == "png"){
    imagecolortransparent($image_c, imagecolorallocatealpha($image_c, 0, 0, 0, 127));
    imagealphablending($image_c, false);
    imagesavealpha($image_c, true);
	}
	
	ImageCopyResampled($image_c, $new_image, 0, 0, 0, 0, $new_width, $new_height, $ow, $oh);
	// clean, debug:
	//@ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); 
	//header('Content-Type: image/jpeg');
		ob_start(); 
	//$process = $image_save_func($image_c, $new);
	$process = $image_save_func($image_c);
	$data = ob_get_clean(); 
	jimport( 'joomla.filesystem.file' );
	 @JFile::write($new, $data); 

	//	$process = $image_save_func($image_c, $new);
	
	}
	
	
 	public static function op_show_image(&$image, $extra, $width, $height, $type)
	{
    $showimg = VmConfig::get('oncheckout_show_images', true); 
	if (empty($showimg)) return '&nbsp;'; 
	
	if (empty($image))
	{
	  if (!empty($width)) $w = 'width: '.$width.';'; else $w = ''; 
	  if (!empty($height)) $h = 'height: '.$height.';'; else $h = ''; 
	  return '<div style="'.$w.' '.$h.' ">&nbsp;</div>';
	}
	

		$class = '';
	   $alt = ''; 
	       $img = OPCimage::op_image_info_array($image, 'class="'.$class.'" border="0" title="'.$alt.'" alt="'.$alt.'"', 1, $type, $width, $height);
           
          if (!empty($img))
		    {
			  $real_height = $img['height'];
              $real_width =  $img['width']; 
			}
			else
			{
			  $real_height = 0;
              $real_width =  0;
			  $href = ''; 
			}
		   
		   $width = (int)$width; 
		   $height = (int)$height;
		   $real_width = (int)$real_width;
		   $real_height = (int)$real_height; 
		   if (empty($width)) $width = $real_width;
		   if (empty($height)) $height = $real_height;
           $w1 = floor((abs($real_width-$width))/2);
		   
           $w2 = $width-floor((abs($real_width-$width))/2);
           
           $h1 = floor((abs($real_height-$height))/2);
           $h2 = $height-floor((abs($real_height-$height))/2);
           
           $w3 = $width-$w1;
           $ret = '<div style="height: '.$height.'px; width: '.$width.'px; ">
           <div style="float: left; width: '.$w1.'px; height: 100%;"></div>
		   <div style="float: left; width: '.$w3.'px; height: '.$h1.'px;"></div>
           <div style="float: left; width: '.$w3.'px; height: '.$h2.'px;">';
		   if (!empty($img))
		   {
           if (!empty($href)) $ret .= '<a href="'.$href.'" title="'.$alt.'">';
			$ret .= '<img src="'.$img['iurl'].'" width="'.$img['width'].'" height="'.$img['height'].'" />'; 
           if (!empty($href)) $ret .= '</a>';
		   }
		   else $ret .= "&nbsp;"; 
           $ret .= '
           </div>
           </div>';
           
           return $ret; 

	  
	  
	}
	
    function path2url($path)
	{
	
	
		$path = str_replace(JPATH_SITE, '', $path); 
		$path = str_replace(DS, '/', $path); 
		
		if (substr($path, 0, 1) != '/') $path = '/'.$path; 
		
		$base = JURI::root(true);
		
		
		if (substr($base, -1)=='/') $base = substr($base, 0, -1);

		$path = $base.$path; 
		
		return $path; 
	}
	
	public static function getMediaData($id)
	{
	   if (empty($id)) return;
   if (is_array($id)) $id = reset($id);
   
   $db = JFactory::getDBO(); 
   $id = (int)$id; 
   $q = "select * from #__virtuemart_medias where virtuemart_media_id = ".$id." limit 0,1"; 
   $db->setQuery($q); 
   $res = $db->loadAssoc(); 
   
   $err = $db->getErrorMsg(); 
   
   return $res; 
	}
	
	public static function getImageFile($id, $w=0, $h=0)
	{
	   $img = OPCImage::getMediaData($id);
  
   if (!empty($img['file_url_thumb']))
    {
	
	  $th = $img['file_url_thumb']; 
	  if (!empty($w) && (!empty($h)))
	  {
	  $th2 = str_replace('/resized/', '/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $thf;
	  }
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th); 
	  
	  
	  if (file_exists($thf)) 
	  {
	  
	  $tocreate = true; 
	  return $thf;
	  }
	  
	  $imgp = JPATH_SITE.DS.str_replace('/', DS, $img['file_url_thumb']); 
	  
	  if (file_exists($imgp) && (!is_dir($imgp)))
	   {
	      
	      return $imgp; 
	   }
	 
	  
	}
    
    {
	  $th = $img['file_url']; 
	 
	  if (!empty($w) && (!empty($h)))
	  {
	  $th2 = str_replace('/virtuemart/', '/virtuemart/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $thf;
	  }
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th); 
	  if (file_exists($thf)) 
	  	{
	    $tocreate = true; 
		
		return $thf;
		}
		
	  $imgp = JPATH_SITE.DS.str_replace('/', DS, $img['file_url']); 
	  if (file_exists($imgp) && (!is_dir($imgp)))
	   {
	   
	      return $imgp; 
	   }
	  
	
	}
	}
	
	public static  function getImageUrl($id, &$tocreate, $w=0, $h=0) 
	{
	   $img = OPCImage::getMediaData($id);
   if (!empty($img['file_url_thumb']))
    {
	  $th = $img['file_url_thumb']; 
	  $th2 = str_replace('/resized/', '/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $th2;
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th); 
	  if (file_exists($thf)) 
	  {
	  $tocreate = true; 
	  return $th;
	  }
	}
   else
    {
	  $th = $img['file_url']; 
	  $th2 = str_replace('/virtuemart/', '/virtuemart/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $th2;
	  $thf = JPATH_SITE.DS.str_replace('/', DS, $th); 
	  if (file_exists($thf)) 
	  	{
	    $tocreate = true; 
		return $th;
		}
	}
	}



}