<?php
/*
*
* @copyright Copyright (C) 2007 - 2014 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
	
	$ajax = JRequest::getVar('ajax','0');

   if ($ajax === 'yes')
   {
    require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_onepage'.DS.'views'.DS.'order_details'.DS.'tmpl'.DS.'ajax'.DS.'onepage_ajax.php');
    die();
   }
	
	JHTMLOPC::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
	JHTMLOPC::script('order_edit.js', 'administrator/components/com_onepage/views/order_details/tmpl/ajax/', false);
	JHTMLOPC::script('onepage_ajax.js', 'administrator/components/com_onepage/views/order_details/tmpl/ajax/', false);
	JHTMLOPC::stylesheet('order_edit.css', 'administrator/components/com_onepage/views/order_details/tmpl/ajax/', false);
  
   JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());