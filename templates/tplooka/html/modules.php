<?php
/**
 * @version		$Id: modules.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a clean manner
 */
?>
<?php
function modChrome_neat($module, &$params, &$attribs) {
	if (!empty ($module->content)) : ?>
	<div id="<?php echo $module->position ?>" class="module">
		<?php if ($module->showtitle) : ?>
			<h6 class="module-title"><?php echo $module->title; ?></h6>
		<?php endif; ?>
	    <div class="module-body">
	        <?php echo $module->content; ?>
        </div>
	</div> <?php echo '<!-- /' . $module->position . ' -->';
	endif;
}
?>

<?php
function modChrome_neatwrap($module, &$params, &$attribs) {
	if (!empty ($module->content)) : ?>
	<div id="<?php echo $module->position ?>" class="module wrapper">
		<?php if ($module->showtitle) : ?>
			<h6 class="module-title"><?php echo $module->title; ?></h6>
		<?php endif; ?>
	    <div class="module-body">
	        <?php echo $module->content; ?>
        </div>
	</div> <?php echo '<!-- /' . $module->position . ' -->';
	endif;
}
?>

<?php
function modChrome_lean($module, &$params, &$attribs) {
	if (!empty ($module->content)) : ?>
	<div class="module">
		<?php if ($module->showtitle) : ?>
			<h6 class="module-title"><?php echo $module->title; ?></h6>
		<?php endif; ?>
	    <div class="module-body">
	        <?php echo $module->content; ?>
        </div>
	</div>
	<?php endif;
}
?>

<?php
function modChrome_leanwrap($module, &$params, &$attribs) {
	if (!empty ($module->content)) : ?>
	<div class="module wrapper">
		<?php if ($module->showtitle) : ?>
			<h6 class="module-title"><?php echo $module->title; ?></h6>
		<?php endif; ?>
	    <div class="module-body">
	        <?php echo $module->content; ?>
        </div>
	</div>
	<?php endif;
}
?>

<?php
function modChrome_html5plus($module, &$params, &$attribs){
	$moduleTag      = $params->get('module_tag', 'div');
	$headerTag      = htmlspecialchars($params->get('header_tag', 'h6'));
	$bootstrapSize  = (int) $params->get('bootstrap_size', 0);
	$moduleClass    = $bootstrapSize != 0 ? ' span' . $bootstrapSize : '';

	// Temporarily store header class in variable
	$headerClass	= $params->get('header_class');
	$headerClass	= ' class="module-title '. (!empty($headerClass) ? htmlspecialchars($headerClass).'"' : '"');

	if (!empty ($module->content)) : ?>
		<<?php echo $moduleTag; ?> class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')) . $moduleClass; ?>">

		<?php if ((bool) $module->showtitle) :?>
			<?php if ($module->showtitle != 0) {
				$titleTrim = explode(' ', trim($module->title), 2);
			} ?>
			<<?php echo $headerTag . $headerClass ?>>
				<span class='hfirst'><?php echo $titleTrim[0]; ?></span><span class='hnext'> <?php echo $titleTrim[1]; ?></span>
			</<?php echo $headerTag; ?>>
		<?php endif; ?>

			<?php echo $module->content; ?>

		</<?php echo $moduleTag; ?>>

	<?php endif;
}

?>