<?php
/**
 * Controller for the OPC ajax and checkout
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

	//require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
class OPCLang {
  public static function _($string, $jsSafe=false, $interpretBackSlashes=true,  $script = false)
  {
	$text = JText::_($string, $jsSafe, $interpretBackSlashes, $script); 
    switch ($string)
	{
		case  'COM_VIRTUEMART_CART_PRICE':
			return str_replace(':', '', JText::_('COM_VIRTUEMART_CART_PRICE')); 
		/*
		case 'COM_VIRTUEMART_COMMENT_CART': 
			return JText::_('COM_VIRTUEMART_COMMENT'); 
		*/
		default: 
			break; 
	}
	
	
	switch ($text)
	{
	    case 'COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS':
		 return JText::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS'); 
		case 'COM_VIRTUEMART_REGISTER_UNAME':
			return JText::_('COM_VIRTUEMART_USERNAME'); 
		case 'COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED':
		  return JText::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS'); 
		case 'COM_ONEPAGE_OTHER_DISCOUNT':
		  if (JText::_('COM_ONEPAGE_OTHER_DISCOUNT')=='COM_ONEPAGE_OTHER_DISCOUNT')
		  return JText::_('COM_VIRTUEMART_COUPON_DISCOUNT'); 
		  else return JText_('COM_ONEPAGE_OTHER_DISCOUNT'); 
		case 'COM_ONEPAGE_EMAIL_ALREADY_EXISTS':
		   return JText::_('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME');
		case 'COM_VIRTUEMART_FIELDS_NEWSLETTER':
		   return JText::_('COM_ONEPAGE_NEWSLETTER_SUBSCRIPTION'); 
		default: 
			return $text; 
	}
	return $text; 
  }
  public static function setGet($text, $text2)
  {
    if (!empty($text2)) return $text2; 
	else return JText::_($text); 
  }
  public static function getSet($text, $text2)
  {
    $t = JText::_($text); 
	if ($t == $text)
	return $text2; 
	
	return $t; 
  }
  public static function sprintf($text1, $text)
  {
   $text1 = OPCLang::_($text1); 
   return JText::sprintf($text1, $text); 
  }
  public static function loadLang()
  {
  
  
    $lang = JFactory::getLanguage();
    $lang->load('com_onepage', JPATH_SITE, 'en-GB', true);
	$lang->load('com_onepage', JPATH_SITE, null, true);
	
	if (class_exists('VmConfig'))
	{
	if (method_exists('VmConfig', 'loadJLang'))
	{
	  
	  VmConfig::loadJLang('com_virtuemart', TRUE);
	  VmConfig::loadJLang('com_virtuemart_shoppers', TRUE);
	  VmConfig::loadJLang('com_virtuemart_orders',TRUE);
	  VmConfig::loadJLang('com_onepage', TRUE);
	  return; 
	}
	}
	 
	$extension = 'com_virtuemart';
	$lang->load($extension, JPATH_SITE, 'en-GB');
	$tag = $lang->getTag();
	$lang->load('com_virtuemart_orders', JPATH_SITE, 'en-GB', false);
	$lang->load('com_virtuemart_shoppers', JPATH_SITE, 'en-GB', false);
	$lang->load($extension, JPATH_SITE, $tag, true, true);

	if (file_exists(JPATH_SITE.DS.'language'.DS.$tag.DS.$tag.'.com_virtuemart_orders.ini'))
	$x = $lang->load('com_virtuemart_orders', JPATH_SITE, $tag );
	if (file_exists(JPATH_SITE.DS.'language'.DS.$tag.DS.$tag.'.com_virtuemart_shoppers.ini'))
	$y = $lang->load('com_virtuemart_shoppers', JPATH_SITE, $tag);



  }
}