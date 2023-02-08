<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document =& JFactory::getDocument();
JHTML::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
$document->setTitle(JText::_('COM_ONEPAGE_UTILS')); 
$document->addStyleDeclaration('
#toolbar-box { display: none; } ');

$default_config = array('vm_lang'=>0, 'vm_menu_en_gb'=>0, 'selected_menu'=>0, 'menu_'=>0, 'tojlanguage'=>'*'); 
$session = JFactory::getSession(); 
$config = $session->get('opc_utils', $default_config); 

if (!version_compare(JVERSION,'2.5.0','ge'))
{
  $j15 = true; 
  
}


?>
<form action="index.php">
<fieldset <?php if (!empty($j15)) echo ' disabled="disabled" '; ?> >
<legend><?php echo JText::_('COM_ONEPAGE_UTILS_VM_TO_J_LABEL'); ?></legend>
<?php if (!empty($j15)) echo '<div>'.JText::_('COM_ONEPAGE_ONLY_J25').'</div>'; ?>
<div><?php echo JText::_('COM_ONEPAGE_UTILS_DESC'); ?></div>
<div><?php echo '<div style="color: red;">'.JText::_('COM_ONEPAGE_UTILS_NOTE').' </div>'; echo JText::_('COM_ONEPAGE_UTILS_NOTE2'); ?>
</div><br />
<table <?php if (empty($this->cats)) echo ' style="display: none;'; ?> >
<tr>
<td>
<?php 

echo JText::_('COM_ONEPAGE_UTILS_SELECT_VM_CHILD'); ?><br /><?php echo JText::_('COM_ONEPAGE_UTILS_SELECT_VM_CHILD_DESC'); ?>
</td>
<td>
<select name="vm_lang" onchange="return op_unhideMenuVM(this);">
<?php
if (empty($this->cats)) $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
{
  echo '<option '; 
  if (!empty($config['vm_lang']) && ($lang==$config['vm_lang'])) 
  {
  $first_lang = $config['vm_lang']; 
  echo ' selected="selected" '; 
  }
  if (empty($config['vm_lang']))
  if (!isset($first_lang))
  $first_lang = $lang; 
  
  echo ' value="'.$lang.'">'.$lang.'</option>'; 
}
?>
</select>
<?php
if (empty($this->cats))  $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
{
 if (!empty($config['vm_menu_'.$first_lang])) $first_vm = $config['vm_menu_'.$first_lang];  
 
 if (!isset($first_vm))
 $first_vm = $lang;
?><select <?php if ($lang != $first_lang) echo ' style="display: none;" ';  ?> name="vm_menu_<?php echo $lang; ?>" id="vm_menu_<?php echo $lang; ?>"  >
<option value="0">--- <?php echo JText::_('COM_ONEPAGE_UTILS_ALL'); ?> ---</option>
<?php

foreach ($arr as $key2=>$mymenu)
{
?>

<?php
//debug_zval_dump($m); die(); 

   if (!isset($mymenu['virtuemart_category_id'])) continue; 
   echo '<option '; 
   if (!empty($config['vm_menu_'.$first_lang]) && ($mymenu['virtuemart_category_id'] == $config['vm_menu_'.$first_lang]))
   echo ' selected="selected" '; 
   echo ' value="'.$mymenu['virtuemart_category_id'].'">'.$mymenu['category_name'].'</option>'; 
   // recursion here: 
   if (!empty($mymenu['children']))
   $this->printChildren($mymenu['children'], 'virtuemart_category_id', 'category_name', '->');
 
}
?>
</select>

<?php
}
?>
</td>
</tr>
<tr>
<td>


<?php echo JText::_('COM_ONEPAGE_UTILS_TO_MOVE_JOOMLA_MENU'); ?>
</td>
<td>
<select name="selected_menu" onchange="return op_unhideMenu(this);">
<option value="0">--- <?php echo JText::_('COM_ONEPAGE_UTILS_NEW'); ?> ---</option>

<?php 
if (empty($config['selected_menu'])) $first = 0; 
else $first = $config['selected_menu']; 
foreach ($this->menus as $menu)
{
 //$first = $menu['menutype']; 
 echo '<option value="'.$menu['menutype'].'" '; 
 if ($menu['menutype'] == $config['selected_menu']) echo ' selected="selected" '; 
 echo '>'.$menu['title'].'</option>'; 
}
?>
</select>
</td>
</tr>
<tr>
<td>

<script type="text/javascript">
 var last_menu = 'menu_<?php echo $first; ?>'; 
 var last_menu_vm = 'vm_menu_<?php echo $first_lang; ?>'; 
</script>
<?php echo JText::_('COM_ONEPAGE_UTILS_WITH_PARENT_MENU_ITEM'); ?>
</td>
<td>
<select name="menu_0" id="menu_0" <?php if (!empty($first)) echo ' style="display: none;" '; ?> disabled="disabled" ><option value="">-</option></select>
<?php
foreach ($this->sortedmenu as $key2=>$m)
{
?>
<select name="menu_<?php echo $key2; ?>" id="menu_<?php echo $key2; ?>" <?php if ($key2 !== $first) echo ' style="display: none;" '; ?> >
<option value="1">--- <?php echo JText::_('COM_ONEPAGE_UTILS_TOP'); ?> ---</option>
<?php
//debug_zval_dump($m); die(); 
foreach ($m as $key=>$mymenu)
 {
   if (empty($key)) continue; 
   if ($mymenu['published']<0) continue; 
   if (!isset($mymenu['id'])) { 
   continue; 
   var_dump($key2);  var_dump($key); var_dump($menu[103]); 
   echo 'mymenu: '; 
   var_dump($mymenu);  die('hh'); }
   
   echo '<option '; 
if ((!empty($config['menu_'.$key2])) && ($config['menu_'.$key2]==$mymenu['id'])) echo ' selected="selected" ';    
   echo ' value="'.$mymenu['id'].'">'.$mymenu['item_type'].'</option>'; 
   // recursion here: 
   if (!empty($mymenu['children']))
   $this->printChildren($mymenu['children'], 'id', 'item_type', '->');
 }
?>
</select>
<?php

}
?>
</td>
</tr>
<tr>
<td>
<?php
$lang =  JFactory::getLanguage(); 
$langs = $lang->getKnownLanguages(); 
echo ''.JText::_('COM_ONEPAGE_UTILS_INS_TO_LANG'); 
?>
</td>
<td>
<?php
echo '<select name="tojlanguage">'; 
echo '<option value="*">All</option>'; 
foreach ($langs as $key=>$l)
{
  echo '<option ';
  if (!empty($config['tojlanguage']) && ($config['tojlanguage']==$key)) echo ' selected="selected" '; 
  echo ' value="'.$key.'">'.$l['name'].'</option>'; 
}
echo '</select>'; 
?>
</td>
</tr>
</table>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="movemenu" />
<input type="hidden" name="view" value="utils" />
<?php if (!empty($this->cats)) { ?>
<input type="submit" name="Proceed" />
<?php } ?>
</fieldset>
</form> 
<form action="index.php" method="post">
<fieldset <?php if (!empty($j15)) echo ' disabled="disabled" '; ?> >
<legend><?php echo JText::_('COM_ONEPAGE_UTILS_SEARCH_FULLTEXT'); ?></legend>
<div><?php echo JText::_('COM_ONEPAGE_UTILS_SEARCH_DESC'); ?></div>

<table>
<tr>
<td>Search for text in your joomla installation: 
</td>
<td><input type="text" value="" name="searchwhat" />
</td>
<td>
<select name="ext">
 <option value="*">*.*</option>
 <option value="css">*.css</option>
 <option value="php">*.php</option>
 <option value="gif">*.gif</option>
</select>
</td>
</tr>
<tr><td>
<input type="checkbox" name="excludecache" value="1" checked="checked" id="excludecache" />
</td>
<td>
<label for="excludecache">Exclude cache</label>
</td>
</tr>
<tr><td><input type="checkbox" name="casesensitive" value="1" checked="checked" id="casesensitive" /></td><td><label for="casesensitive">Case sensitive</label></td></tr>
<tr><td><input type="checkbox" name="onlysmall" value="1" checked="checked" id="onlysmall" /></td><td><label for="onlysmall">Only smaller than 500kb</label></td></tr>
<tr>
<td><input type="submit" /></td></tr></table>
</fieldset>


<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="searchtext" />
<input type="hidden" name="view" value="utils" />

</form>
<?php
$error_log = @ini_get('error_log'); 
if (!empty($error_log))
if (file_exists($error_log))
{
?>
<fieldset><legend><?php echo JText::_('COM_ONEPAGE_PHPERRORlOG'); ?></legend>
 <a href="index.php?option=com_onepage&view=utils&task=errorlog&format=raw&tmpl=component"><?php echo JText::_('COM_ONEPAGE_VIEWPHPERRORLOG'); ?></a>
</fieldset>
<?php 
}
?>
<div><?php echo $this->results; ?></div>