<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
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

// this feed is referenced by: https://support.google.com/merchants/answer/160589

// all classes should be named by <element>Xml per it's manifest with upper letter for the element name and the Xml
class Google_rssXml {
 function clear()
  {
  }
  function startHeaders()
  {
     $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n"; 
	 $xml .= '<rss version="2.0" '."\n";  
	 $xml .= 'xmlns:g="http://base.google.com/ns/1.0">'; 
	 $xml .= '<channel>'; 
	 $xml .= '<title><![CDATA['.$this->params->title.']]></title>'; 
	 $xml .= '<link><![CDATA['.$this->config->xml_live_site.']]></link>'; 
	 $xml .= '<description><![CDATA['.$this->params->description.']]></description>'; 
	 
	 return $xml; 
	 
  }
  function addItem($product, $vm1)
  {
          extract($vm1); 
  
     			// zaciatok shopitem
			$data = '<item>'."\n";
			
			$data .= '<g:id>'.$product_id.'</g:id>'."\n";
			$data .= '<g:condition>new</g:condition>'."\n"; 
			
			$data .= '<g:google_product_category>'.$product->paired_category_name.'</g:google_product_category>'; 
			
			$data .= '<title><![CDATA['.$product_name.']]></title>'."\n";
			
			$data .= '<g:image_link><![CDATA['.$thumb_url.']]></g:image_link>'."\n";
			//$data .= '<FULLIMAGE><![CDATA['.$fullimg.']]></FULLIMAGE>'."\n";
			$data .= '<description><![CDATA['.$desc.']]></description>'."\n";
			//$data .= '<FULLDESCRIPTION><![CDATA['.$fulldesc.']]></FULLDESCRIPTION>'."\n";
			$data .= '<link><![CDATA['.$link .']]></link>'."\n";
			
			$live_site = $this->config->xml_live_site; 
			
			if (empty($cena_s_dph)) return; 
			
			$data .= '<g:price>'.$cena_s_dph.'</g:price>'."\n";
			/*
			if (!empty($images))
			{
			$data .= '<IMAGES>'."\n";
			foreach ($images as $im)
			{
			
			$data .= "\t".'<IMAGE>'."\n"; 
			if (substr($im['file_name'], 0, 4) != 'http')
			$data .= "\t"."\t".'<PATH><![CDATA['.$live_site.$im['file_name'].']]></PATH>'."\n";
			else
			$data .= "\t"."\t".'<PATH><![CDATA['.$im['file_name'].']]></PATH>'."\n";
			
			$data .= "\t"."\t".'<TITLE><![CDATA['.$im['file_title'].']]></TITLE>'."\n";
			$data .= "\t".'</IMAGE>'."\n"; 
			}
			$data .= '</IMAGES>'."\n";
			}
			else $data .= '<IMAGES />'."\n"; 
			
			$data .= '<NONSEOLINK><![CDATA['.$node_link.']]></NONSEOLINK>'."\n";
			
			if (empty($cena_s_dph)) return ''; 
			$cena_s_dph = number_format ( $cena_s_dph, 2, ',', ''); 
			*/
			
			
			/*
			$data .= '<PRICE><![CDATA['.$cena_txt.']]></PRICE>'."\n";
			$tax_rate = number_format ( $tax_rate, 2, ',', ''); 
			$data .= '<VAT>'.$tax_rate.'</VAT>'."\n"; 
			$data .= '<ITEM_TYPE>NEW</ITEM_TYPE>'."\n";
			
			if (!empty($longest_cats))
			{
			//<CATEGORYTEXT>Obuv | Pánska | Bežecká | Nike</CATEGORYTEXT>
			 $lcats = implode(' | ', $longest_cats); 
			}
			else return; 
			
			
			
			if (!empty($cats))
			{
			  $data .= '<ALLCATEGORIES>'."\n"; 
			  foreach ($cats as $c)
			   {
			     $data .= "\t".'<CATEGORYPATH><![CDATA['.implode(' | ', $c).']]></CATEGORYPATH>'."\n"; 
			   }
			  $data .= '</ALLCATEGORIES>'."\n"; 
			}
			
			
			
    			$data .= '<CATEGORYTEXT><![CDATA['.$lcats.']]></CATEGORYTEXT>'."\n";
				//$data .= '<EAN />'."\n";
    			//$data .= '<CATEGORY_PATH><![CDATA['.$kat2.']]></CATEGORY_PATH>'."\n";
				if (empty($manufacturer))
				{
				$isset = false; 
				if (!empty($attribs))
				{
				  foreach ($attribs as $k=>$v)
				   {
				     if ($v['virtuemart_custom_id']==90)
					 {
					 $data .= '<MANUFACTURER><![CDATA['.$v['custom_value'].']]></MANUFACTURER>'."\n";
					 $isset = true; 
					
					 break; 
					 
					 
					 }
				   }
				}
				if (!$isset)
				$data .= '<MANUFACTURER />'."\n";
				}
				else
    			$data .= '<MANUFACTURER><![CDATA['.$manufacturer.']]></MANUFACTURER>'."\n";
				if (empty($manufacturer_id))
				$data .= '<MANUFACTURER_ID />'."\n";
				else
    			$data .= '<MANUFACTURER_ID>'.$manufacturer_id.'</MANUFACTURER_ID>'."\n";
    			$data .= '<AVAILABILITY>'.$avaitext.'</AVAILABILITY>'."\n";
				$data .= '<AVAILABILITY_DAYS><![CDATA['.$avaidays.']]></AVAILABILITY_DAYS>'."\n";
    			$data .= '<AVAILABILITY_OBR>'.$avai_obr.'</AVAILABILITY_OBR>'."\n";
    			//$data .= '<EAN><![CDATA['.$ean.']]></EAN>'."\n";
    			$data .= '<ATTRIBUTES><![CDATA['.$atr.']]></ATTRIBUTES>'."\n";
    			$data .= '<DELIVERY_DATE><![CDATA['.$avaidays.']]></DELIVERY_DATE>'."\n";
				if ($published)
    			$data .= '<PUBLISHED>Y</PUBLISHED>'."\n";
				else
				$data .= '<PUBLISHED>N</PUBLISHED>'."\n";
				
				if (!$published) return; 
				
				
				if (!empty($product->customfields))
				{
				   foreach ($product->customfields as $cf)
				    {
					  
					}
				}
				*/
		//	echo '<output>'.var_export($id, true).'</output>'."\n";
 			$data .= '</item>'."\n";
			
			
			return $data; 

  }
  function endHeaders()
  {
     $xml = '</channel>'."\n"; 
	 $xml .= '</rss>'."\n"; 
	 return $xml; 
  
  }
  function compress()
  {
  }
  
  
 
  
  function getPairingUrl()
  {
  
    $url = (string)$this->config->catlink; 
	
	$lang = JFactory::getLanguage()->getTag(); 
	return str_replace('{lang}', $lang, $url); 
  }
  
  function getPairingName()
  {
    $lang = JFactory::getLanguage()->getTag(); 
	$lang = str_replace('-', '_', $lang); 
	return 'googlerss_'.$lang; 
  }
  
  // either return an object or converted categories
  function processPairingData($xml, &$converted)
  {
  
  $lines = explode("\n", $xml); 
  
  
  $ret = array();  
  foreach ($lines as $k=>$line)
   {     
     //get rid of the rest characters
     $lines[$k] = str_replace("\r", '', $line); 
	 if (substr($line, 0, 1)==='#') continue; 
	 $hash = $k;  
	 $ret[$hash] = $line; 
   }
   
   
   
  
   $converted = $ret; 
   return; 


	   
  }
  
  
  
}
 