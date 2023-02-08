<?php
/**
 * @package 	mod_universal_menu
 * @version		1.7.3
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements. Use class .nofollow for attribute rel="nofollow"!
$class ='';// $item->anchor_css ? (stripos($item->anchor_css, "nofollow") !== FALSE ? 'rel="nofollow" ' : '') . 'class="'.$item->anchor_css.'" ' : '';
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';

// Get a HTML code of the item's link
$linktype = modUniversalMenuHelper::htmlLinkType( $item, $imageTitle, $arrowPosition );

$flink = $item->flink;
$flink = JFilterOutput::ampReplace(htmlspecialchars($flink));

switch ($item->browserNav) :
	default:
	case 0:
?><a <?php echo $class; ?>href="<?php echo $flink; ?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
?><a <?php echo $class; ?>href="<?php echo $flink; ?>" target="_blank" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
		// window.open
		$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');
			?><a <?php echo $class; ?>href="<?php echo $flink; ?>" onclick="window.open(this.href,'targetWindow','<?php echo $options;?>');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
endswitch;
