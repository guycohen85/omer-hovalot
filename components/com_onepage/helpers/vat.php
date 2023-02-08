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

/**
 *  Some portions of this file are also credited to: 
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */


if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class OPCvat
{
  public static $european_states = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'HR', 'UK', 'EL');
  /**
	 * We cache the results of all time-consuming operations, e.g. vat validation, subscription membership calculation,
	 * tax calculations, etc into this array, saved in the user's session.
	 * @var array
	 */
	private $_cache = array();
	
  	public function isVIESValidVAT($country, $vat, $company='', &$err)
	{
		// Validate VAT number
		$vat = trim(strtoupper($vat));
		$country = $country == 'GR' ? 'EL' : $country;
		$country = $country == 'UK' ? 'GB' : $country;
		// (remove the country prefix if present)
		if(substr($vat,0,2) == $country) $vat = trim(substr($vat,2));
		
		$vat = preg_replace('/[^A-Z0-9]/', '', $vat); // Remove spaces, dots and stuff
		// Is the validation already cached?
		$key = $country.$vat;
		$ret = null;
		if(array_key_exists('vat', $this->_cache)) {
			if(array_key_exists($key, $this->_cache['vat'])) {
				$ret = $this->_cache['vat'][$key];
			}
		}
		$country = strtoupper($country); 
		if (!in_array($country, OPCvat::$european_states)) 
		{
		 $ret = false; 
		 $vat = ''; 
		}
		if(!is_null($ret)) return $ret;

		if(empty($vat)) {
			$ret = false;
		} else {
			if(!class_exists('SoapClient')) {
				$ret = false;
			} else {
				// Using the SOAP API
				// Code credits: Angel Melguiz / KMELWEBDESIGN SLNE (www.kmelwebdesign.com)
				try {
					$sOptions = array(
						'user_agent'		=> 'PHP'
					);
					$sClient = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', $sOptions);
					$params = array('countryCode'=>$country,'vatNumber'=>$vat);
					//if (!method_exists($sClient, 'checkVat')) die('123'); 
					$response = $sClient->checkVat($params);
					if ($response->valid) {
						$ret = true;
					}else{
						$ret = false;
					}
				} catch(SoapFault $ex) {
				    $err = $ex->faultcode.' '.$ex->faultstring.' '.$ex->faultactor.' '.$ex->detail.' '.$ex->_name.' '.$ex->headerfault; 
					
					$ret = false;
					return -1; 
				}
			}
		}

		// Cache the result
		if(!array_key_exists('vat', $this->_cache)) {
			$this->_cache['vat'] = array();
		}
		$this->_cache['vat'][$key] = $ret;
		$encodedCacheData = json_encode($this->_cache);

		$session = JFactory::getSession();
		$session->set('validation_cache_data', $encodedCacheData, 'com_onepage');

		// Return the result
		return $ret;
	}
	/**
	 * Sanitizes the VAT number and checks if it's valid for a specific country.
	 * Ref: http://ec.europa.eu/taxation_customs/vies/faq.html#item_8
	 *
	 * @param string $country Country code
	 * @param string $vatnumber VAT number to check
	 *
	 * @return array The VAT number and the validity check
	 */
	private function _checkVATFormat($country, $vatnumber)
	{
		$ret = (object)array(
			'prefix'		=> $country,
			'vatnumber'		=> $vatnumber,
			'valid'			=> true
		);

		$vatnumber = strtoupper($vatnumber); // All uppercase
		$vatnumber = preg_replace('/[^A-Z0-9]/', '', $vatnumber); // Remove spaces, dots and stuff
		$vat_country_prefix = $country; // Remove the country prefix, if it exists
		if($vat_country_prefix == 'GR') $vat_country_prefix = 'EL';
		if(substr($vatnumber, 0, strlen($vat_country_prefix)) == $vat_country_prefix) {
			$vatnumber = substr($vatnumber, 2);
		}
		$ret->prefix = $vat_country_prefix;
		$ret->vatnumber = $vatnumber;

		switch ($ret->prefix) {
			case 'AT':
				// AUSTRIA
				// VAT number is called: MWST.
				// Format: U + 8 numbers

				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					if(substr($vatnumber,0,1) != 'U') $ret->valid = false;
				}
				if($ret->valid) {
					$rest = substr($vatnumber, 1);
					if(preg_replace('/[0-9]/', '', $rest) != '') $ret->valid = false;
				}
				break;

			case 'BG':
				// BULGARIA
				// Format: 9 or 10 digits
				if((strlen($vatnumber) != 10) && (strlen($vatnumber) != 9)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'CY':
				// CYPRUS
				// Format: 8 digits and a trailing letter
				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					$check = substr($vatnumber, -1);
					if(preg_replace('/[0-9]/', '', $check) == '') $ret->valid = false;
				}
				if($ret->valid) {
					$check = substr($vatnumber, 0, -1);
					if(preg_replace('/[0-9]/', '', $check) != '') $ret->valid = false;
				}
				break;

			case 'CZ':
				// CZECH REPUBLIC
				// Format: 8, 9 or 10 digits
				$len = strlen($vatnumber);
				if(!in_array($len, array(8,9,10))) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'BE':
				// BELGIUM
				// VAT number is called: BYW.
				// Format: 9 digits
				if((strlen($vatnumber) == 10) && (substr($vatnumber,0,1) == '0')) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
					break;
				}
			case 'DE':
				// GERMANY
				// VAT number is called: MWST.
				// Format: 9 digits
			case 'GR':
			case 'EL':
				// GREECE
				// VAT number is called: ΑΦΜ.
				// Format: 9 digits
			case 'PT':
				// PORTUGAL
				// VAT number is called: IVA.
				// Format: 9 digits
			case 'EE':
				// ESTONIA
				// Format: 9 digits
				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'DK':
				// DENMARK
				// VAT number is called: MOMS.
				// Format: 8 digits
			case 'FI':
				// FINLAND
				// VAT number is called: ALV.
				// Format: 8 digits
			case 'LU':
				// LUXEMBURG
				// VAT number is called: TVA.
				// Format: 8 digits
			case 'HU':
				// HUNGARY
				// Format: 8 digits
			case 'MT':
				// MALTA
				// Format: 8 digits
			case 'SI':
				// SLOVENIA
				// Format: 8 digits
				if(strlen($vatnumber) != 8) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'FR':
				// FRANCE
				// VAT number is called: TVA.
				// Format: 11 digits; or 10 digits and a letter; or 9 digits and two letters
				// Eg: 12345678901 or X2345678901 or 1X345678901 or XX345678901
				if(strlen($vatnumber) != 11) $ret->valid = false;
				if($ret->valid) {
					// Letters O and I are forbidden
					if(strstr($vatnumber, 'O')) $ret->valid = false;
					if(strstr($vatnumber, 'I')) $ret->valid = false;
				}
				if($ret->valid) {
					$valid = false;
					// Case I: no letters
					if(preg_replace('/[0-9]/', '', $vatnumber) == '') $valid = true;

					// Case II: first character is letter, rest is numbers
					if(!$valid) {
						if(preg_replace('/[0-9]/', '', substr($vatnumber,1)) == '') $valid = true;
					}

					// Case III: second character is letter, rest is numbers
					if(!$valid) {
						$check = substr($vatnumber,0,1) . substr($vatnumber,2);
						if(preg_replace('/[0-9]/', '', $check) == '') $valid = true;
					}

					// Case IV: first two characters are letters, rest is numbers
					if(!$valid) {
						$check = substr($vatnumber,2);
						if(preg_replace('/[0-9]/', '', $check) == '') $valid = true;
					}

					$ret->valid = $valid;
				}
				break;

			case 'IE':
				// IRELAND
				// VAT number is called: VAT.
				// Format: seven digits and a letter; or six digits and two letters
				// Eg: 1234567X or 1X34567X
				if(strlen($vatnumber) != 8) $ret->valid = false;
				if($ret->valid) {
					// The last position must be a letter
					$check = substr($vatnumber,-1);
					if(preg_replace('/[0-9]/', '', $check) == '') $ret->valid = false;
				}
				if($ret->valid) {
					// Skip the second position (it's a number or letter, who cares), check the rest
					$check = substr($vatnumber,0,1) . substr($vatnumber,2,-1);
					if(preg_replace('/[0-9]/', '', $check) != '') $ret->valid = false;
				}
				break;

			case 'IT':
				// ITALY
				// VAT number is called: IVA.
				// Format: 11 digits
				if(strlen($vatnumber) != 11) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'LT':
				// LITUANIA
				// Format: 9 or 12 digits
				if((strlen($vatnumber) != 9) && (strlen($vatnumber) != 12)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'LV':
				// LATVIA
				// Format: 11 digits
				if((strlen($vatnumber) != 11)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'PL':
				// POLAND
				// Format: 10 digits
			case 'SK':
				// SLOVAKIA
				// Format: 10 digits
				if((strlen($vatnumber) != 10)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'RO':
				// ROMANIA
				// Format: 2 to 10 digits
				$len = strlen($vatnumber);
				if(($len < 2) || ($len > 10)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'NL':
				// NETHERLANDS
				// VAT number is called: BTW.
				// Format: 12 characters long, first 9 characters are numbers, last three characters are B01 to B99
				if(strlen($vatnumber) != 12) $ret->valid = false;
				if($ret->valid) {
					if((substr($vatnumber,9,1) != 'B')) {
						$ret->valid = false;
					}
				}
				if($ret->valid) {
					$check = substr($vatnumber,0,9) . substr($vatnumber,11);
					if(preg_replace('/[0-9]/', '', $check) == '') $valid = true;
				}
				break;

			case 'ES':
				// SPAIN
				// VAT number is called: IVA.
				// Format: Eight digits and one letter; or seven digits and two letters
				// E.g.: X12345678 or 12345678X or X1234567X
				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					// If first is number last must be letter
					$check = substr($vatnumber,0,1);
					if(preg_replace('/[0-9]/', '', $check) == '') {
						$check = substr($vatnumber,0);
						if(preg_replace('/[0-9]/', '', $check) == '') $ret->valid = false;
					}
				}
				if($ret->valid) {
					// If first is not a number, the  last can be anything; just check the middle
					$check = substr($vatnumber,1,-1);
					if(preg_replace('/[0-9]/', '', $check) != '') $ret->valid = false;
				}
				break;

			case 'SE':
				// SWEDEN
				// VAT number is called: MOMS.
				// Format: Twelve digits, last two must be 01
				if(strlen($vatnumber) != 12) $ret->valid = false;
				if($ret->valid) {
					if(substr($vatnumber,-2) != '01') $ret->valid = false;
				}
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'GB':
				// UNITED KINGDOM
				// VAT number is called: VAT.
				// Format: Nine or twelve digits; or 5 characters (alphanumeric)
				if(strlen($vatnumber) == 5) {
					break;
				}
				if((strlen($vatnumber) != 9) && (strlen($vatnumber) != 12)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			default:
				$ret->valid = false;
				break;
		}

		return $ret;
	}

  
}