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
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
$order_total = $this->order['details']['BT']->order_total;
$order_total = number_format($order_total, 2, '.', ''); 

?>
<!-- Google Code for Conversion OPC Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = <?php echo $this->params->google_conversion_id; ?>;
var google_conversion_language = "<?php echo $this->params->google_conversion_language; ?>";
var google_conversion_format = "<?php echo $this->params->google_conversion_format; ?>";
var google_conversion_color = "<?php echo $this->params->google_conversion_color; ?>";
var google_conversion_label = "<?php echo $this->params->google_conversion_label; ?>";
var google_conversion_value = <?php echo $order_total ?>;
<?php if (!empty($this->params->google_remarketing_only))
echo ' var google_remarketing_only = true; 
'; 
else
echo ' var google_remarketing_only = false; 
'; 
?>
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/<?php echo $this->params->google_conversion_id; ?>/?value=<?php echo $order_total ?>&amp;label=<?php echo $this->params->google_conversion_label; ?>&amp;guid=ON&amp;script=0"/>
</div>
</noscript>