<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class JViewOrders extends OPCView
	{
		function display($tpl = null)
		{	
			// load language: 
			
			
			$model = $this->getModel();
			$model->loadVirtuemart(); 
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'models'.DS.'config.php'); 
			$config = new JModelConfig(); 
			$config->loadVmConfig(); 
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'listfactory.php'); 
			$this->statuses = $config->getOrderStatuses();
			// Get data from the model
			$pagination = $this->get('Pagination');
            $items = $this->get('Data');   
            $total = $this->get('Total');   
            
			$ehelper = new OnepageTemplateHelper();
			$templates = $ehelper->getExportTemplates('ALL');

			$templates = $model->getTemplates();
			//$order_data = $model->getOrderData();
		    
		    //$ehelper = new OnepageTemplateHelper($order_id);
			
			$this->assignRef('ehelper', $ehelper);
			$this->assignRef('templates', $templates);
			$this->assignRef('model', $model); 
            // push data into the template
            $this->assignRef('items', $items);     
            $this->assignRef('total', $total);     
            $this->assignRef('pagination', $pagination);

			
			parent::display($tpl); 
		}
	}
