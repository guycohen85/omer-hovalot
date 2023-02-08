<?php


defined('_JEXEC') or die('Restricted access');

				$classSettings = "";
				$classItems = "";
				$classPreview = "";
				
				if(isset($selectedGalleryTab)){
					switch($selectedGalleryTab){
						default:
						case "settings":
							$classSettings = "class='selected'";
						break;
						case "items":
							$classItems = "class='selected'";							
						break;
						case "preview":
							$classPreview = "class='selected'";
						break;
					}
				}
			?>
			
			<div class='settings_tabs'>
				<ul class="list-tabs-settings">
					<li <?php echo $classSettings?>>
						<a href="<?php echo HelperGalleryUG::getUrlViewCurrentGallery()?>"><?php _e("Settings", UNITEGALLERY_TEXTDOMAIN)?></a>
					</li>
					<li <?php echo $classItems?>>
						<a href="<?php echo HelperGalleryUG::getUrlViewItems()?>"><?php _e("Items", UNITEGALLERY_TEXTDOMAIN)?></a>
					</li>
					<li <?php echo $classPreview?>>
						<a href="<?php echo HelperGalleryUG::getUrlViewPreview()?>"><?php _e("Preview", UNITEGALLERY_TEXTDOMAIN)?></a>
					</li>
				</ul>
				<div class="unite-clear"></div>
			</div>	
