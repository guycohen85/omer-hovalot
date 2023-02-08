<?php


defined( '_JEXEC' ) or die( 'Restricted access' );
?>



<iframe src="http://www.zbozi.cz/action/<?php echo $this->params->zbozi_cislo; ?>/conversion?chsum=-<?php echo $this->params->zbozi_kod; ?>==&price=<?php echo number_format($this->order['details']['BT']->order_total, 2, ',', ''); ?>&uniqueId=<?php echo $this->order['details']['BT']->order_number;?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="position:absolute; top:-3000px; left:-3000px; width:1px; height:1px; overflow:hidden;"></iframe> 