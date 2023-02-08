<?php
defined ('_JEXEC') or die();

class JElementPobocky extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'pobocky';

	function fetchElement ($name, $value, &$node, $control_name) {
		
		$db = JFactory::getDBO(); 
		$cid = JRequest::getVar('cid'); 
		$q = 'select shipment_params from #__virtuemart_shipmentmethods where shipment_element = \'ulozenka\' '; 
		if (!empty($cid))
		 {
		   $cid = (int)$cid[0]; 
		   
		   $q .= ' and virtuemart_shipmentmethod_id = '.$cid; 
		 }
		$db->setQuery($q); 
		
		$params = $db->loadResult(); 
		$e = $db->getErrorMsg(); 
		if (!empty($e)) die($e); 
		$err = true; 
		if (empty($params)) $err = true; 
		else
		{
		$a = explode('|', $params); 
		$obj = new stdClass(); 
		foreach ($a as $p)
		 {
		    $a2 = explode('=', $p); 
			if (!empty($a2) && (count($a2)==2))
			 {
			   $obj->$a2[0] = json_decode($a2[1]); 
			 }
		 }
		 
		require_once(JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'ulozenka'.DS.'helper.php'); 
		
		
		$xml = UlozenkaHelper::getPobocky($obj, false); 
		
		if (!empty($xml->error)) return $xml->error; 
		
		if (!empty($xml))
		 {
		 if (isset($xml->body)) {
					$html = '
					<tr>
						<td colspan="4">
							<center><span style="color:red;font-weight:bold;">'.$xml->body->div.'</span></center>
						</td>
					</tr>'; 
					
				}
		   $k=1;
		   foreach ($xml->pobocky as $p)
		     {
			   $err = false; 
			   $enabled_const = 'enabled'.$p->id; 
			   $parcel_price = 'parcelprice'.$p->id; 
			   $dobierka_price = 'codprice'.$p->id; 
			   
			   if (empty($obj->pobocky->$parcel_price))
			   {
			     $obj->pobocky->$parcel_price = $p->prices->parcel; 
			   }
			   if (empty($obj->pobocky->$dobierka_price))
			   {
			    $obj->pobocky->$dobierka_price = $p->prices->cashOnDelivery; 
			   }
			   
			   //var_dump($obj->pobocky->enabled6); die(); 
			   
			   //'ULOZENKA_'.strtoupper((string)$p->zkratka).'_ENABLED';
							$price_const = 'ULOZENKA_'.strtoupper((string)$p->zkratka).'_PRICE';
							if (!empty($obj->pobocky->$enabled_const)) {
								
								
								
									$enabled = "checked=\"checked\""; 
								} else {
									$enabled = ""; 
								}
							
							
							
							$price = defined($price_const)?constant($price_const):'0';
							$html .= "<tr class=\"row$k\"><td colspan=\3\" width=\"25%\"><b>".$p->nazev; 
							if (!empty($p->partner)) {
							$html .= ' (Partner) '; 
							}
							$html .= "</b>"
								.'</td><td width="25%" rowspan="2">'
								.$p->provoz
								.'</td><td width="25%" rowspan="2">'; 
								
								if (empty($p->aktiv)) { $enabled = ' disabled="disabled" '; }
								if (isset($obj->partners))
								if (empty($obj->partners) && (!empty($p->partner)))
								{
								
								 $enabled = ' disabled="disabled" '; 
								}
								
								
								
								
								
								
								$html .= 'Povolit: <input class="inputbox" type="checkbox" name="params['.$name.']['.$enabled_const.']" '.$enabled.' value="1" />'
								.'</td><td width="30%"  rowspan="2">'
								.'Cena za dopravu (parcel) ('.$p->prices->parcel.' '.$p->prices->currency.'): '
								.'<input class="inputbox" type="text" name="params['.$name.']['.$parcel_price.']"  value="'.$obj->pobocky->$parcel_price.'" />'
								.'Priplatek za dopravu (dobirka) ('.$p->prices->cashOnDelivery.' '.$p->prices->currency.'): '
								.'<input class="inputbox" type="text" name="params['.$name.']['.$dobierka_price.']"  value="'.$obj->pobocky->$dobierka_price.'" />'
								."</td></tr>\n";
							$html .= "<tr class=\"row$k\"><td colspan=\3\" width=\"30%\">"
								.$p->ulice."<br />\n".$p->obec."<br />\n".$p->psc
								."</td></tr>\n";
							$pobocky_zkratky[]=strtoupper((string)$p->zkratka);
							$k=abs($k-1);
			 }
		 }
		if (!$err)
		if (!empty($html)) return $html; 
		//return '<input type="text" name="params[' . $name . ']" id="params' . $name . '" value="' . $value . '" class="text_area" size="50">';
		}
		if ($err)
		 {
		    return 'Nastavte kluc, a ID obchodu kliknite ulozit a nasledne sa zobrazia pobocky pre ktore je mozne nastavit cenu.';
		 }
	}

}