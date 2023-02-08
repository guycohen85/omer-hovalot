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
$this->loadTemplate('header'); 

$data = array(); 
$data['id'] = 'test1'; 

$datastr = urlencode(json_encode($data)); 

$entity = JRequest::getVar('entity', ''); 

?>
<table>
 
<?php
foreach ($this->cats as $vmid => $cat)
 {
   ?><tr><?php
    echo '<td>'.$cat.'</td>'; 
	echo '<td>'; 
	
	
?><select class="vm-chzn-select" name="opt" onchange="updateCat(this)" ><?php

foreach ($this->data as $id=>$txt)
 {
   //$extoptions .= '<option value="'.$id.'">'.$txt.'</option>'; 
   //renderOption($entity, $vmCat, $refCat, $txt)
   echo $this->model->renderOption($entity, $vmid, $id, $txt); 
 }
 ?></select>
	
	</td>
	<td><div id="cat_id_<?php echo $vmid; ?>">&nbsp;</div>
	</td>
 
   </tr><?php
 }
 ?>
</table>


<?php

