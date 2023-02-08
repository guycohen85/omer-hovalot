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
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCAddToCartAsLink {
  public static function addtocartaslink(&$ref)
 {
   
   
	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 

	$rememberhtml = ''; 
 
    $rp = JRequest::getVar('randomproduct', 0); 
	if (!empty($rp))
	 {
		if (OPCloader::checkOPCSecret())
		{
	      $opc_link_type = 1; 
	      $q = 'select virtuemart_product_id from #__virtuemart_products where published=1 limit 1'; 
		  $db=JFactory::getDBO(); 
		  $db->setQuery($q); 
		  $temp_id = $db->loadResult();
		  JRequest::setVar('add_id', $temp_id);
		  
		}
	 }
	if (empty($opc_link_type)) return; 
    $p_id = JRequest::getVar('add_id', '');
    if (empty($p_id)) return;

	
   if (!isset($ref->cart->order_number)) $ref->cart->order_number = ''; 
if (!empty($p_id))
{

$qq = array(); 

if (is_array($p_id))
{

foreach ($p_id as $i=>$item)
{
if (!is_numeric($p_id[$i])) break;

$q = JRequest::getVar('qadd_'.$p_id[$i], 1); 

if (!is_numeric($q)) break;

$rememberhtml .= '<input type="hidden" name="qadd_'.$p_id[$i].'" value="'.$q.'" />'; 
$rememberhtml .= '<input type="hidden" name="add_id['.$i.']" value="'.$p_id[$i].'" />'; 

$q = (float)$q;
$qq[$p_id[$i]] = $q;

}

}
else
{
// you can use /index.php?option=com_virtuemart&page=shop.cart&add_id=10&quadd=1;
// to add two products (ids: 10 and 11) of two quantity each (quadd_11=2 for product id 11 set quantity 2)
// OR /index.php?option=com_virtuemart&page=shop.cart&add_id[]=10&quadd_10=2&add_id[]=11&qadd_11=2

$q = JRequest::getVar('qadd_'.$p_id, 1); 
$rememberhtml .= '<input type="hidden" name="qadd_'.$p_id.'" value="'.$q.'" />'; 
$rememberhtml .= '<input type="hidden" name="add_id" value="'.$p_id.'" />'; 

$q = (float)$q;
$q2 = JRequest::getVar('qadd', 1);
//$rememberhtml .= '<input type="hidden" name="qadd" value="'.$q2.'" />'; 
if (!is_numeric($p_id)) return;


$qq[$p_id] = $q;

$a = array(); 
$a[$p_id] = $p_id; 
$p_id = $a; 

}
   
   

}
else return;

    $post = JRequest::get('default');
	/*
	if (!class_exists('VirtueMartModelProduct'))
	 require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'product.php');
	 */
	 require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'mini.php'); 
	$productClass = OPCmini::getModel('product'); //new VirtueMartModelProduct(); 
	
	//$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array'); //is sanitized then
	$newp = array(); 
$rr2 = array(); 



	foreach ($p_id as $pid)
	 {
	   $newp[$pid] = $pid; 
 $product = $productClass->getProductSingle($pid, true, true, true); 
	   $rr = OPCAddToCartAsLink::getProductCustomsFieldCart($product); 
	   $rr2[] = $rr; 
	 }
	 
    if (($opc_link_type == 2) || ($opc_link_type == 1))
	{
	 if (!empty($ref->cart->products))
	  {
	    $p = $ref->cart->products;
		foreach ($p as $key=>$pr) 
		 {
		   $id = $pr->virtuemart_product_id; 
		   
		   // delete cart content
		   if ($opc_link_type == 1)
		   {
		  
		   if (isset($ref->cart->products[$key]))
		   $ref->cart->removeProductCart($key); 
		   else 
		   if (isset($ref->cart->product[$id]))
		   $ref->cart->removeProductCart($id); 
 continue; 
		   }
		   // do not increment quantity: 
		   if ($opc_link_type == 2)
		   if (in_array($id, $newp)) return ; 
		   
		  
		 }
		 
	  }
    }	 
	 
	 
	 
	$virtuemart_product_ids = JRequest::setVar('virtuemart_product_id', $newp); //is sanitized then
	$virtuemart_product_ids = JRequest::setVar('quantity', $qq); //is sanitized then

	
	if (!empty($rr2))
	foreach ($rr2 as $rr1)
	 foreach ($rr1 as $post)
	 {
	    
	    $x = JRequest::getVar($post['name']); 
		if (empty($x))
		{
		 $test = array(); 
		 if (strpos($post['name'], ']')!==false)
		 {
		 $post['name'] = parse_str($post['name'].'='.$post['value'], $test); 
		
		 $firstkey = 0; 
		 if (!empty($test))
		  foreach ($test as $key=>$val)
		   {
		     $firstkey = $key; break; 
		   }
		     
		 $name = $firstkey; 
		 $value = $test[$name]; 
		 JRequest::setVar($name, $value); 
		 
		 }
		 else
	     JRequest::setVar($post['name'], $post['value']); 
		}
	 }
	if (!empty($opc_auto_coupon))
	{
	 $ref->cart->couponCode = $opc_auto_coupon; 
	}
	$ref->cart->add();
	JRequest::setVar('virtuemart_product_id', ''); 
	JRequest::setVar('add_id', ''); 
	JRequest::setVar('opc_adc', 1); 
	//$quantityPost = (int) $post['quantity'][$p_key];
	return $rememberhtml; 

  }
  
  
		/**
	 * Original function from customFields.php 
	 * We need to update custom attributes when using add to cart as link
	 *
	 * @author Patrick Kohl
	 * @param obj $product product object
	 * @return html code
	 */
	public static function getProductCustomsFieldCart ($product) {
		
		if (OPCJ3) return array(); 
		$db = JFactory::getDBO(); 
		
		// group by virtuemart_custom_id
		$query = 'SELECT C.`virtuemart_custom_id`, `custom_title`, C.`custom_value`,`custom_field_desc` ,`custom_tip`,`field_type`,field.`virtuemart_customfield_id`,`is_hidden`
				FROM `#__virtuemart_customs` AS C
				LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
				Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id . ' and `field_type` != "G" and `field_type` != "R" and `field_type` != "Z"';
		$query .= ' and is_cart_attribute = 1 group by virtuemart_custom_id';

		$db->setQuery ($query);
		$groups = $db->loadObjectList ();

		if (!class_exists ('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		$row = 0;
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();

		if (!class_exists ('calculationHelper')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		}
		$calculator = calculationHelper::getInstance ();
		if (!class_exists ('vmCustomPlugin')) {
			require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
		}
		
		$reta = array(); 
		
		$free = OPCLang::_ ('COM_VIRTUEMART_CART_PRICE_FREE');
		// render select list
		foreach ($groups as $group) {

			//				$query='SELECT  field.`virtuemart_customfield_id` as value ,concat(field.`custom_value`," :bu ", field.`custom_price`) AS text
			$query = 'SELECT field.`virtuemart_product_id`, `custom_params`,`custom_element`, field.`virtuemart_custom_id`,
							field.`virtuemart_customfield_id`,field.`custom_value`, field.`custom_price`, field.`custom_param`
					FROM `#__virtuemart_customs` AS C
					LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
					Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id;
			$query .= ' and is_cart_attribute = 1 and C.`virtuemart_custom_id`=' . (int)$group->virtuemart_custom_id;

			// We want the field to be ordered as the user defined
			$query .= ' ORDER BY field.`ordering`';

			$db->setQuery ($query);
			$options = $db->loadObjectList ();
			//vmdebug('getProductCustomsFieldCart options',$options);
			$group->options = array();
			foreach ($options as $option) {
				$group->options[$option->virtuemart_customfield_id] = $option;
			}
			
			
			if ($group->field_type == 'V') {
				$default = current ($group->options);
				foreach ($group->options as $productCustom) {
					if ((float)$productCustom->custom_price) {
						$price = strip_tags ($currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price)));
					}
					else {
						$price = ($productCustom->custom_price === '') ? '' : $free;
					}
					$productCustom->text = $productCustom->custom_value . ' ' . $price;

				}
				$r = array(); 
				$r['name'] = 'customPrice[' . $row . '][' . $group->virtuemart_custom_id . ']'; 
				$r['value'] = $default->custom_value;
				$reta[] = $r; 
				
				//$group->display = VmHTML::select ('customPrice[' . $row . '][' . $group->virtuemart_custom_id . ']', $group->options, $default->custom_value, '', 'virtuemart_customfield_id', 'text', FALSE);
			}
			else {
				if ($group->field_type == 'G') {
					$group->display .= ''; // no direct display done by plugin;
				}
				else {
					if ($group->field_type == 'E') {
						$group->display = '';

						foreach ($group->options as $k=> $productCustom) {
							if ((float)$productCustom->custom_price) {
								$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
							}
							else {
								$price = ($productCustom->custom_price === '') ? '' : $free;
							}
							$productCustom->text = $productCustom->custom_value . ' ' . $price;
							$productCustom->virtuemart_customfield_id = $k;
							if (!class_exists ('vmCustomPlugin')) {
								require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
							}

							//legacy, it will be removed 2.2
							$productCustom->value = $productCustom->virtuemart_customfield_id;
							JPluginHelper::importPlugin ('vmcustom');
							$dispatcher = JDispatcher::getInstance ();
							$fieldsToShow = $dispatcher->trigger ('plgVmOnDisplayProductVariantFE', array($productCustom, &$row, &$group));

						
							$group->display .= '<input type="hidden" value="' . $productCustom->virtuemart_customfield_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_custom_id . ']" /> ';
							
							$r = array(); 
							$r['name'] = 'customPrice[' . $row . '][' . $productCustom->virtuemart_custom_id . ']';
							$r['value'] = $productCustom->virtuemart_customfield_id;
							$reta[] = $r; 
							
							
							
							if (!empty($currency->_priceConfig['variantModification'][0]) and $price !== '') {
								$group->display .= '<div class="price-plugin">' . OPCLang::_ ('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
							}
							$row++;
						}
						$row--;
					}
					else {
						if ($group->field_type == 'U') {
							foreach ($group->options as $productCustom) {
								if ((float)$productCustom->custom_price) {
									$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
								}
								else {
									$price = ($productCustom->custom_price === '') ? '' : $free;
								}
								$productCustom->text = $productCustom->custom_value . ' ' . $price;

								$group->display .= '<input type="text" value="' . OPCLang::_ ($productCustom->custom_value) . '" name="customPrice[' . $row . '][' . $group->virtuemart_custom_id . '][' . $productCustom->value . ']" /> ';
								
								$r = array(); 
								
								$r['name'] = 'customPrice[' . $row . '][' . $group->virtuemart_custom_id . '][' . $productCustom->value . ']';
								$r['value'] = OPCLang::_ ($productCustom->custom_value);
								$reta[] = $r; 
								// only the first is used here
								//continue; 
								
								if (false)
								if (!empty($currency->_priceConfig['variantModification'][0]) and $price !== '') {
									$group->display .= '<div class="price-plugin">' . OPCLang::_ ('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
								}
							}
						}
						else {
							if ($group->field_type == 'A') {
								$group->display = '';
								foreach ($group->options as $productCustom) {
								/*	if ((float)$productCustom->custom_price) {
										$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
									}
									else {
										$price = ($productCustom->custom_price === '') ? '' : $free;
									}*/
									$productCustom->field_type = $group->field_type;
									$productCustom->is_cart = 1;
								
								
								// only the first is used here
								continue; 


									
									$checked = '';
								}
							}
							else {

								$group->display = '';
								$checked = 'checked="checked"';
								foreach ($group->options as $productCustom) {
									//vmdebug('getProductCustomsFieldCart',$productCustom);
									if (false)
									if ((float)$productCustom->custom_price) {
										$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
									}
									else {
										$price = ($productCustom->custom_price === '') ? '' : $free;
									}
									$productCustom->field_type = $group->field_type;
									$productCustom->is_cart = 1;
								//	$group->display .= '<input id="' . $productCustom->virtuemart_custom_id . '" ' . $checked . ' type="radio" value="' .
								//		$productCustom->virtuemart_custom_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_customfield_id . ']" /><label
								//		for="' . $productCustom->virtuemart_custom_id . '">' . $this->displayProductCustomfieldFE ($productCustom, $row) . ' ' . $price . '</label>';
								//MarkerVarMods
									$r['name'] = 'customPrice[' . $row . '][' . $group->virtuemart_customfield_id . ']';
								   $r['value'] = $productCustom->virtuemart_custom_id;
								   $reta[] = $r; 
								   //only the first here
								   continue; 
									$checked = '';
								}
							}
						}
					}
				}
			}
			$row++;
		}

		return $reta;

	}
  
  
}