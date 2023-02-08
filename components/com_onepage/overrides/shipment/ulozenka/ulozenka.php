<?php

defined('_JEXEC') or die('Restricted access');

if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
if (!class_exists ('calculationHelper')) {
  require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
}
if (!class_exists ('CurrencyDisplay')) {
  require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
}
if (!class_exists ('VirtueMartModelVendor')) {
  require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
}


class plgVmShipmentUlozenka extends vmPSPlugin
{
    // instance of class
    public static $_this = false;
    
    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
        $this->_loggable   = true;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		
        $this->tableFields = array_keys($this->getTableSQLFields());
        $varsToPush        = $this->getVarsToPush();
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);        
        
		//JHTML::_('behavior.modal', 'a.ulozenkamodal'); 
        
    }    

    /**
     * Create the table for this plugin if it does not yet exist.
     * @author Valérie Isaksen
     */
    public function getVmPluginCreateTableSQL()
    {
        return $this->createTableSQL('ulozenka');
    }
    
    function getTableSQLFields()
    {
        $SQLfields = array(
            'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED',
            'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
            'order_number' => 'char(32)',
            'ulozenka_packet_id' => 'decimal(10)',
            'ulozenka_packet_price' => 'decimal(15,2)',
            'branch_id' => 'decimal(10)',
            'branch_currency' => 'char(5)',
            'branch_name_street' => 'varchar(500)',
            'email' => 'varchar(255)', 
            'phone' => 'varchar(255)', 
            'first_name' => 'varchar(255)',
            'last_name' => 'varchar(255)',
            'address' => 'varchar(255)',
            'city' => 'varchar(255)',
            'zip_code' => 'varchar(255)',            
            'virtuemart_country_id' => 'varchar(255)',            
            'adult_content' => 'smallint(1) DEFAULT 0', 
            'is_cod' => 'smallint(1)',            
            'exported' => 'smallint(1)',
            'printed_label' => 'smallint(1) DEFAULT 0',            
            'shipment_name' => 'varchar(5000)',            
            'shipment_cost' => 'decimal(10,2)',
            'shipment_package_fee' => 'decimal(10,2)',
            'tax_id' => 'smallint(1)'
        );
        return $SQLfields;
    }
    
    /**
     * This method is fired when showing the order details in the frontend.
     * It displays the shipment-specific data.
     *
     * @param integer $order_number The order Number
     * @return mixed Null for shipments that aren't active, text (HTML) otherwise
     * @author Valérie Isaksen
     * @author Max Milbers
     */
    public function plgVmOnShowOrderFEShipment($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name)
    {
        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
    }
    
    /**
     * This event is fired after the order has been stored; it gets the shipment method-
     * specific data.
     *
     * @param int $order_id The order_id being processed
     * @param object $cart  the cart
     * @param array $priceData Price information for this order
     * @return mixed Null when this method was not selected, otherwise true
     * @author Valerie Isaksen
     */
    
    function plgVmConfirmedOrder(VirtueMartCart $cart, $order)
    {
        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_shipmentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->shipment_element)) {
            return false;
        }
		
		
		
        if (!$this->OnSelectCheck($cart)) {
            return false;
        }
		$values['id'] = null; 
        $values['virtuemart_order_id']          = $order['details']['BT']->virtuemart_order_id;
        $values['virtuemart_shipmentmethod_id'] = $order['details']['BT']->virtuemart_shipmentmethod_id;
        $values['order_number']                 = $order['details']['BT']->order_number;
        $values['ulozenka_packet_id']         = 0;
        $values['ulozenka_packet_price']      = $order['details']['BT']->order_total;
		$session = JFactory::getSession(); 
        $values['branch_id']                    = JRequest::getVar('ulozenka_pobocka', $session->get('ulozenka_pobocka', '')); 
        $values['branch_currency']              = 'Kč'; 
		
		require_once(dirname(__FILE__).DS.'helper.php'); 
		$pobocka = UlozenkaHelper::getDataPobocky($method, $values['branch_id']); 
		
		$ptext = $pobocka->nazev.', '.$pobocka->ulice.', '.$pobocka->obec.', '.$pobocka->psc.' ('.$pobocka->provoz.')'; 
		
        $values['branch_name_street']           = $ptext;
        $values['email']                        = $cart->BT['email'];
        $values['phone']                        = $cart->BT['phone_1'];
        $values['adult_content']                = 0;
        $values['is_cod']                       = -1; //depends on actual settings of COD payments until its set manually in administration
        $values['exported']                = 0;        
        $values['shipment_name']                = $method->shipment_name.': '.$ptext;             
        $values['shipment_cost']                = $this->getCosts ($cart, $method, "");                
        $values['tax_id']                       = $method->tax_id;
        $ret = $this->storePSPluginInternalData($values);
		
        return true;
    }
    
    
  /**
   * calculateSalesPrice
   * overrides default function to remove currency conversion
   * @author Zasilkovna
   */

  function calculateSalesPrice ($cart, $method, $cart_prices) {    
      $value = $this->getCosts ($cart, $method, $cart_prices);

      $tax_id = @$method->tax_id;

	  $vendor_id = $cart->vendorId; 
	  if (empty($vendor_id))
      $vendor_id = 1;
	  
      $vendor_currency = VirtueMartModelVendor::getVendorCurrency ($vendor_id);

      $db = JFactory::getDBO ();
      $calculator = calculationHelper::getInstance ();
      $currency = CurrencyDisplay::getInstance ();    

      $taxrules = array();
      if (!empty($tax_id)) {
        $q = 'SELECT * FROM #__virtuemart_calcs WHERE `virtuemart_calc_id`="' . $tax_id . '" ';
        $db->setQuery ($q);
        $taxrules = $db->loadAssocList ();
      }

      if (count ($taxrules) > 0) {
        $salesPrice = $calculator->roundInternal ($calculator->executeCalculation ($taxrules, $value));
      } else {
        $salesPrice = $value;
      }      
      return $salesPrice;       
  }

    /**    
     * @return delivery cost for the shipping method instance
     * @author Zasilkovna
     */
    
    function getCosts(VirtueMartCart $cart, $method, $cart_prices){                   
        //get actual display currency. in $cart->pricesCurrency its not updated immediately.. but if we dont use change currency,   getUserStateFromRequest doesnt return anything.. so then use cart pricesCurrency
        $currency = CurrencyDisplay::getInstance ();            
        $pricesCurrencyId = $currency->_app->getUserStateFromRequest( 'virtuemart_currency_id', 'virtuemart_currency_id',$currency->_vendorCurrency );          
        if(empty($pricesCurrencyId)){//this is pretty weird
          $pricesCurrencyId=$cart->pricesCurrency;
        }        
        
		$pobocka = $this->getSelectedPobocka(); 
		
		if (empty($pobocka)) return 0; 
		
	    require_once(dirname(__FILE__).DS.'helper.php'); 
		$pobockaObj = UlozenkaHelper::getDataPobocky($method, $pobocka); 

		
		$parcel_price = 'parcelprice'.$pobocka;
	    $dobierka_price = 'codprice'.$pobocka; 
		
		$price = $method->pobocky->$parcel_price;  

        
		if (!class_exists('ShopFunctions'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		
		$cid = ShopFunctions::getCurrencyIDByName("CZK"); 
		
		

        /*
          Set free shipping
        */
		
		$total = $cart->pricesUnformatted['billTotal']; 
		if (!empty($cart->pricesUnformatted['salesPriceShipment']))
		$total -= (float)$cart->pricesUnformatted['salesPriceShipment']; 
		
		if (!empty($cart->pricesUnformatted['salesPricePayment']))
		$total -= (float)$cart->pricesUnformatted['salesPricePayment']; 
		
		if (!empty($pobockaObj->sk))
		{
		  $method->free_start_sk = (float)$method->free_start_sk; 
		  if (!empty($method->free_start_sk))
		  if ($total >= $method->free_start_sk)
		    {
			
			  return 0;    
			}
		}
		else
		{
		  if (!empty($pobockaObj->partner))
		   {
		      if (!empty($method->free_start_partner))
		      {
			     $fs = (float)$method->free_start_partner; 
				 if ($total >= $fs) 
				 {
				 
				 return 0; 
				 }
			  }
		   }
		   else
		   {
		      if (!empty($method->free_start_ulozenka))
		      {
			     $fs = (float)$method->free_start_ulozenka; 
				 if ($total >= $fs) 
				 {
				 
				 return 0; 
				 }
			  }
		   
		   }
		   
		}
		
		if (!empty($method->cod_payments))
		if (!empty($cart->virtuemart_paymentmethod_id))
		{
		   if (in_array($cart->virtuemart_paymentmethod_id, $method->cod_payments))
		   if (!empty($method->pobocky->$dobierka_price))
		   $price += $method->pobocky->$dobierka_price; 
		}
		
		$orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
		if (!empty($method->weight_stop))
		if ($orderWeight > $method->weight_stop)
		{
		   $w = (float)$method->weight_stop; 
		   $times = $orderWeight / $w; 
		   $c = ceil($times); 
		   $price = $price * $c; 
		}
		
		return $price;  
		
    }
    
    /** TODO
    * Here can add check if user has filled in valid phone number or mail so he is reachable by zasilkovna
    */
    protected function checkConditions($cart, $method, $cart_prices)
    {
	   	$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
	    $is_cz = $this->isCz($address['virtuemart_country_id']); 
		$is_sk = $this->isSk($address['virtuemart_country_id']); 

	   if ((!$is_sk) && (!$is_cz)) return false; 
		
	  $wstart = (float)$method->weight_start;
	  
      $ws = (float)$method->weight_stop;
      $orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
	  
	  if (!empty($wstart))
	  if ($orderWeight < $wstart) return false; 
	  
	  // do not allow oversize packages
	  if (!empty($ws))
      if (empty($method->strategy))
	  if ($orderWeight > $ws) return false; 
	  
	  if (!empty($ws))
      if (!empty($method->strategy))
	  {
	  

		if(count($cart->products)>0) {
			foreach ($cart->products as $product) {

				$weight = ShopFunctions::convertWeightUnit ($product->product_weight, $product->product_weight_uom, $method->weight_unit) ;
				// single product weight is larger then x: 
				if ($weight > $ws) return false; 
			}
		}

	  
	  }
	  
	  
	  $total = (int)$cart->pricesUnformatted['billTotal']; 
	  
	  if (!empty($method->orderamount_start))
	   {
	     $st = (float)$method->orderamount_start; 
		 if (($total < $st)) return false; 
	   }

	   if (!empty($method->orderamount_stop))
	   {
	     $st = (float)$method->orderamount_stop; 
		 if (($total < $st)) return false; 
	   }

	   
      return true;
    }      
    
    /*
     * We must reimplement this triggers for joomla 1.7
     */
    
    /**
     * Create the table for this plugin if it does not yet exist.
     * This functions checks if the called plugin is active one.
     * When yes it is calling the standard method to create the tables
     * @author Valérie Isaksen
     *
     */
    function plgVmOnStoreInstallShipmentPluginTable($jplugin_id)
    {
        return $this->onStoreInstallPluginTable($jplugin_id);
    }
    
    /**
     * This event is fired after the shipment method has been selected. It can be used to store
     * additional payment info in the cart.
     *
     * @author Max Milbers
     * @author Valérie isaksen
     *
     * @param VirtueMartCart $cart: the actual cart
     * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
     *
     */
    // public function plgVmOnSelectCheck($psType, VirtueMartCart $cart) {
    // return $this->OnSelectCheck($psType, $cart);
    // }
    public function plgVmOnSelectCheckShipment(VirtueMartCart &$cart)
    {        
		if (!$this->selectedThisByMethodId($cart->virtuemart_shipmentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}

		if (!$this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}


		
		$saved = JRequest::getVar('ulozenka_saved'); 
		if (!empty($saved)) JRequest::setVar('ulozenka_pobocka', $saved); 


		
	    $pobocka = JRequest::getVar('ulozenka_pobocka', ''); 

		
		
        if ($this->OnSelectCheck($cart)) {
            //session_start();
			if (empty($pobocka)) 
			  {
			  
			    return false; 
			    
			  }
			$session = JFactory::getSession(); 
			$session->set('ulozenka_pobocka', (int)$pobocka); 
        }else{
			
        }
        
        $ret = $this->OnSelectCheck($cart);
		
		return $ret; 
    }
	public function isSk($country_id)
	{
	    if (empty($country_id)) return true; 
	    $countryModel = VmModel::getModel ('country');
		$sk_id = $countryModel->getCountryByCode('SVK');
		if (empty($sk_id) && (!empty($country_id))) return false; 
		if (empty($sk_id) && (empty($country_id))) return true; 
		if (empty($country_id) || ($country_id == $sk_id->virtuemart_country_id)) return true; 
		return false; 
	}
	
	public function isCz($country_id)
	{
	    if (empty($country_id)) return true; 
	    $countryModel = VmModel::getModel ('country');
		$sk_id = $countryModel->getCountryByCode('CZE'); 
		if (empty($sk_id) && (!empty($country_id))) return false; 
		if (empty($sk_id) && (empty($country_id))) return true; 
		if (empty($country_id) || ($country_id == $sk_id->virtuemart_country_id)) return true; 
		return false; 
	}
	
    /**
     * plgVmDisplayListFE
     * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
     *
     * @param object $cart Cart object
     * @param integer $selected ID of the method selected
     * @return boolean True on succes, false on failures, null when this plugin was not selected.
     * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
     *
     * @author Valerie Isaksen
     * @author Max Milbers
     */
    public function plgVmDisplayListFEShipment(VirtueMartCart $cart, $selected = 0, &$htmlIn)
    {
	
	
        $js_html = '';
        if ($this->getPluginMethods($cart->vendorId) === 0) {
                return FALSE;            
        }        

        require_once(dirname(__FILE__).DS.'helper.php'); 
		
	    $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
      
		$is_cz = $this->isCz($address['virtuemart_country_id']); 
		$is_sk = $this->isSk($address['virtuemart_country_id']); 
		
		$html        = array();
        $method_name = $this->_psType . '_name';
        $document = JFactory::getDocument(); 
		
		
		
        foreach ($this->methods as $key => $method) { 
			if (!$this->checkConditions ($cart, $method, $cart->pricesUnformatted)) continue; 
			$mymethod = $method; 
			$cache_d = JPATH_CACHE.DS.'ulozenka'.DS.'ulozenka_html_'.$method->partners.'_'.$is_cz.'_'.$is_sk.'_'.$method->virtuemart_shipmentmethod_id.'.html'; 
			if (file_exists($cache_d))
			 {
			   $html[$key] .= file_get_contents($cache_d); 
			 }
			 else
			 {
			
		    $xml = UlozenkaHelper::getPobocky($method); 	
			
            $html[$key] = '';
			
			if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
				//JHTML::_('behavior.modal', 'a.modal');
				$html[$key] .= '';
				$k=1;
				$first=true;
				$pobocky_options = array();
				$js_adresa="\n\nvar adresa=new Array();";
				$js_oteviraci_doba="\n\nvar oteviraci_doba=new Array();";
				$js_cena="\n\nvar cena=new Array();";
				$js_values="\n\nvar values=new Array();";
//				$js_mapa="\n\nvar mapa=new Array();";
				$first_opt = 0; 
				
				$ind = 0; 
				$pobocky_html = ''; 
				foreach ($xml->pobocky as $p) {
				    
					if ($is_sk)
					if ($p->country != 'SVK') continue; 

					if ($is_cz)
					if ($p->country != 'CZE') continue; 

					if (empty($method->partners))
					if (!empty($p->parner))
					continue; 
					
					$enabled_const = 'ULOZENKA_'.strtoupper((string)$p->zkratka).'_ENABLED';
					if (defined($enabled_const)) {
						if (constant($enabled_const)==1) {
							$enabled = true;
						} else {
							$enabled = false;
						}
					} else {
						$enabled = true;
					}
					
					$enabled_const = 'enabled'.$p->id; 
					
					if ((isset($method->pobocky->$enabled_const)) && ($method->pobocky->$enabled_const)) $enabled = true; 
					else $enabled = false; 
					
					if ($enabled) {
						if ((string)$p->aktiv) {
							//$price_const = 'ULOZENKA_'.strtoupper((string)$p->zkratka).'_PRICE';

							
							
							$session = JFactory::getSession(); 
							$first_opt = $session->get('ulozenka_pobocka', $first_opt); 
							$mk = 0; 
							
							if (empty($first_opt)) $first_opt = $p->id; 
							if ($first_opt == $p->id) $sind = $ind; 
							$ind++; 
							
							
							
							
							
							
							//$pobocky_options[] = JHTML::_('select.option',  $p->id, $p->nazev );
							$opobocka = new stdClass(); 
							$opobocka->id = $p->id; 
							$opobocka->nazev = $p->nazev; 
							$pobocky_options[] = $opobocka; 
							//$pobocky_options[] = JHTML::_('select.option',  $p->id, $p->nazev );
							$js_adresa.="\nadresa[".$p->id."]='<b>".$p->nazev.'</b><br />'.$p->ulice.'<br />'.$p->obec.'<br />'.$p->psc."';";
							$p->provoz = str_replace("\n\r", '', $p->provoz); 
							$p->provoz = str_replace("\n", '', $p->provoz); 
							$js_oteviraci_doba.="\noteviraci_doba[".$p->id."]='".$p->provoz."';";
							
							$pobocky_html .= $this->renderByLayout('pobocka', array(
									'pobocka' => $p, 
									'sind' => $sind, 
							)); 
							
							//$js_mapa.="\nmapa[".$p->id."]='".$p->mapa."';";
						}
					}
				}
				
				
				if ($first) {
							$cena = ''; 
								$output1 = $this->renderByLayout('pobocky', array(
								  'virtuemart_shipmentmethod_id'=>$method->virtuemart_shipmentmethod_id, 
								  'first_opt'=>$first_opt,
								  'method'=>$method,
								)); 
								$detail_url = JURI::root().'plugins/vmshipment/ulozenka/detail_pobocky.php?id='.(string)$p->id;
								
								$first = false;

							}
				
				 $pobocky = $this->renderByLayout('pobocky_select', array(
				    'pobocky_options' => $pobocky_options,
					'virtuemart_shipmentmethod_id' => $method->virtuemart_shipmentmethod_id,
					'xml' => $xml,
					'sind' => $sind,
					'method'=>$method,
				 )); 
				 
				 
				

				$html[$key] .= str_replace('{combobox}', $pobocky, $output1);
				$html[$key] .= $pobocky_html; 
				$html[$key] .= "\n";
			
				JFile::write($cache_d, $html[$key]);
			
			}  // pobocky not empty
			
			} // isset pobocky
			
		} // end of ... if not cache ... 
			
			
           
        } // end of foreach 
	
		if (isset($mymethod))
		if (!defined('ulozenka_javascript'))
				{
				$document->addScriptDeclaration(
				"\n".'//<![CDATA['."\n" 
				."\n\nvar detail_url='".JURI::root()."plugins/vmshipment/ulozenka/detail_pobocky.php?id=';\n"	
				."\n\n
				function changeUlozenka(id, update) {
				    if (typeof jQuery != 'undefined')
					 jQuery('.zasielka_div1').not('#ulozenka_branch_' + id).hide();
					document.getElementById('ulozenka_pobocka').value=id;
					
					var d = document.getElementById('ulozenka_saved'); 
					if (d != null)
					d.value = id; 
					if (update)
					{
					document.getElementById('ulozenka_branch_'+id).style.display='block';
					if (typeof jQuery != 'undefined')
					{
					  jQuery('#shipment_id_".$method->virtuemart_shipmentmethod_id."').click(); 
					}
					else
					document.getElementById('shipment_id_".$method->virtuemart_shipmentmethod_id."').onclick();
					}
					if (typeof Onepage != 'undefined')
					Onepage.changeTextOnePage3();
					
				};\n".'//]]>'."\n"); 
				define('ulozenka_javascript', true); 
				}
		
		

        if (empty($html)) {
            return FALSE;
        }
        
		
		
        $htmlIn[] = $html;
        return TRUE;
    }
	
	
  
  public function setOPCbeforeSelect($cart, $shipmentid, $shipping_method, $id, &$html)
   {
      if (!($this->selectedThisByMethodId ($id))) {
      return NULL;
      }
	  $a = explode('_', $shipmentid); 
	  //from: <option ismulti="true" multi_id="shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$ppp->id.'" 
	  if (!empty($a[3]))
	   {
	     $pobocka = $a[3]; 
		
		 JRequest::setVar('ulozenka_pobocka', $pobocka); 
		 
		 
	   }
	   if (empty($pobocka)) $pobocka = ''; 
	   if (!defined('ulozenka_saved'))
	   {
	     $html .= '<input type="hidden" name="ulozenka_saved" value="'.$pobocka.'" id="ulozenka_saved" />'; 
		 define('ulozenka_saved', true); 
	   }
	  
   }
  /**
   * This method is fired when showing the order details in the backend.
   * It displays the shipment-specific data.
   * NOTE, this plugin should NOT be used to display form fields, since it's called outside
   * a form! Use plgVmOnUpdateOrderBE() instead!
   *
   * @param integer $virtuemart_order_id The order ID
   * @param integer $virtuemart_shipmentmethod_id The order shipment method ID
   * @param object  $_shipInfo Object with the properties 'shipment' and 'name'
   * @return mixed Null for shipments that aren't active, text (HTML) otherwise
   * @author Valerie Isaksen
   */
  public function plgVmOnShowOrderBEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id) {

    if (!($this->selectedThisByMethodId ($virtuemart_shipmentmethod_id))) {
      return NULL;
    }
    $html = $this->getOrderShipmentHtml ($virtuemart_order_id);
    return $html;
  }

  /**
   * @param $virtuemart_order_id
   * @return string
   * @author zasilkovna
   */
  function getOrderShipmentHtml ($virtuemart_order_id) {

    $db = JFactory::getDBO ();
    $q = 'SELECT * FROM `' . $this->_tablename . '` '
      . 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
    $db->setQuery ($q);
    if (!($shipinfo = $db->loadObject ())) {
      vmWarn (500, $q . " " . $db->getErrorMsg ());
      return '';
    }

    if (!class_exists ('CurrencyDisplay')) {
      require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
    }

    $currency = CurrencyDisplay::getInstance ();
    $tax = ShopFunctions::getTaxByID ($shipinfo->tax_id);
    $taxDisplay = is_array ($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipinfo->tax_id;
    $taxDisplay = ($taxDisplay == -1) ? JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;

    $html = '<table class="adminlist">' . "\n";
    $html .= $this->getHtmlHeaderBE ();
    $html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_SHIPPING_NAME', $shipinfo->shipment_name);   
    $html .= $this->getHtmlRowBE ('BRANCH', $shipinfo->branch_name_street);   
    $html .= $this->getHtmlRowBE ('CURRENCY', $shipinfo->branch_currency);       

    $html .= '</table>' . "\n";

    return $html;
  }    
    private function getSelectedPobockaObj($id)
	{
	  
	}
    private function getSelectedPobocka()
	 {
	   $session = JFactory::getSession(); 
	   return JRequest::getVar('ulozenka_pobocka', $session->get('ulozenka_pobocka', '')); 
	 }
    public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
    {        
		
		
		if (!($method = $this->selectedThisByMethodId ($cart->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}

		if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return NULL;
		}
		
		$pobocka = $this->getSelectedPobocka(); 
		
		
		$parcel_price = 'parcelprice'.$pobocka;
	    $dobierka_price = 'codprice'.$pobocka; 
		
		$price = $method->pobocky->$parcel_price;  
		
		$cart_prices_name = '';
		$cart_prices['cost'] = $this->getCosts($cart, $method, $cart_prices); 

		if (!$this->checkConditions ($cart, $method, $cart_prices)) {
			return FALSE;
		}

		$cart_prices_name = $this->renderPluginName ($method);
		$this->setCartPrices ($cart, $cart_prices, $method);

		return TRUE;
        //return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }
    
    /**
     * plgVmOnCheckAutomaticSelected
     * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
     * The plugin must check first if it is the correct type
     * @author Valerie Isaksen
     * @param VirtueMartCart cart: the cart object
     * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
     *
     */
    function plgVmOnCheckAutomaticSelectedShipment(VirtueMartCart $cart, array $cart_prices = array())
    {
        return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }
    
    /**
     * This event is fired during the checkout process. It can be used to validate the
     * method data as entered by the user.
     *
     * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
     * @author Max Milbers
     
     public function plgVmOnCheckoutCheckData($psType, VirtueMartCart $cart) {
     return null;
     }
     */
    
    /**
     * This method is fired when showing when priting an Order
     * It displays the the payment method-specific data.
     *
     * @param integer $_virtuemart_order_id The order ID
     * @param integer $method_id  method used for this order
     * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
     * @author Valerie Isaksen
     */
    function plgVmonShowOrderPrint($order_number, $method_id)
    {
        return $this->onShowOrderPrint($order_number, $method_id);
    }
    
    /**
     * Save updated order data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     
     public function plgVmOnUpdateOrder($psType, $_formData) {
     return null;
     }
     */
    /**
     * Save updated orderline data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     
     public function plgVmOnUpdateOrderLine($psType, $_formData) {
     return null;
     }
     */
    /**
     * plgVmOnEditOrderLineBE
     * This method is fired when editing the order line details in the backend.
     * It can be used to add line specific package codes
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     
     public function plgVmOnEditOrderLineBE($psType, $_orderId, $_lineId) {
     return null;
     }
     */
    /**
     * This method is fired when showing the order details in the frontend, for every orderline.
     * It can be used to display line specific package codes, e.g. with a link to external tracking and
     * tracing systems
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     
     public function plgVmOnShowOrderLineFE($psType, $_orderId, $_lineId) {
     return null;
     }
     */
    
    /**
     * plgVmOnResponseReceived
     * This event is fired when the  method returns to the shop after the transaction
     *
     *  the method itself should send in the URL the parameters needed
     * NOTE for Plugin developers:
     *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
     *
     * @param int $virtuemart_order_id : should return the virtuemart_order_id
     * @param text $html: the html to display
     * @return mixed Null when this method was not selected, otherwise the true or false
     *
     * @author Valerie Isaksen
     *
     
     function plgVmOnResponseReceived($psType, &$virtuemart_order_id, &$html) {
     return null;
     }
     */
    function plgVmDeclarePluginParamsShipment($name, $id, &$data)
    {
        return $this->declarePluginParams('shipment', $name, $id, $data);
    }
    
    function plgVmSetOnTablePluginParamsShipment($name, $id, &$table)
    {
        return $this->setOnTablePluginParams($name, $id, $table);
    }
    
}

// No closing tag
