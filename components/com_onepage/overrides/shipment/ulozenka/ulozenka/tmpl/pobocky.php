<?php
?>
<div class="sectiontableentry1_null" style="clear: both; padding-top: 10px;">
 <div >
	<input type="hidden" name="ulozenka_pobocka" id="ulozenka_pobocka" value="<?php echo $viewData['first_opt'] ?>" />
	<input type="radio" name="virtuemart_shipmentmethod_id" value="<?php echo $viewData['virtuemart_shipmentmethod_id']; ?>" id="shipment_id_<?php 
	echo $viewData['virtuemart_shipmentmethod_id'] ?>" multielement="pobocky" multielementgetjs="getUlozenka()" multielementgetphp="plgListMultipleOPC" />
 

 <label for="shipment_id_<?php echo $viewData['virtuemart_shipmentmethod_id'] ?>"><?php echo $viewData['method']->shipment_name; ?></label>

</div>
<div width="33%">
	<label>{combobox}</label>
</div>
	<div width="33%"><div><span id="ulozenka_cena">&nbsp;</span></div></div>
</div>

