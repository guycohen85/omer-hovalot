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
* loaded from: \components\com_onepage\controllers\opc.php
*
* 
*/
defined('_JEXEC') or die('Restricted access');


$disablarray[] = 'USPS price updated'; 
$disablarray[] = 'Erro no Webservice Correios Correios';
$disablarray[] = trim(JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', '')); 
$disablarray[] = JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', ''); 
$disablarray[] = 'Canada Post Destination Postal Code'; 