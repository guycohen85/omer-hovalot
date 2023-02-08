<?php
?>
<select name="pobocky" id="pobocky" class="inputbox" vmid="<?php echo $viewData['virtuemart_shipmentmethod_id']; ?>" onChange="changeUlozenka(this.options[this.selectedIndex].value, true);" style="min-width: 200px;">
<option value="0"><?php echo JText::_($viewData['method']->vyberte_pobocku_label); ?></option>
<?php
				foreach ($viewData['pobocky_options'] as $ppp)
				 {
				    $option = '<option '; 
					if ($viewData['sind'] == $ppp->id) $option .= ' selected="selected" '; 
					$option .= ' ismulti="true" data-json=\''.json_encode(array('ulozenka_pobocka'=>$ppp->id)).'\' multi_id="shipment_id_'.$viewData['virtuemart_shipmentmethod_id'].'_'.$ppp->id.'" value="'.$ppp->id.'">'.$ppp->nazev.'</option>'; 
					echo $option; 
				 }
				 ?>
</select>