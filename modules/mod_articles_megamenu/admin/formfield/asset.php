<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField
{

    protected $type = 'Asset';

    protected function getInput()
	{
		JHTML::_('behavior.framework');	
		$document	= &JFactory::getDocument();		
		if (!version_compare(JVERSION, '3.0', 'ge')) {
			$checkJqueryLoaded = false;
			$header = $document->getHeadData();
			foreach($header['scripts'] as $scriptName => $scriptData)
			{
				if (substr_count($scriptName,'/jquery')) {
					$checkJqueryLoaded = true;
				}
			}
				
			//Add js
			if (!$checkJqueryLoaded) {
				$document->addScript(JURI::root().$this->element['path'].'js/jquery.min.js');
			}
			$document->addScript(JURI::root().$this->element['path'].'js/chosen.jquery.min.js');		
			$document->addScript(JURI::root().$this->element['path'].'js/admin.js');

			//Add css         
			$document->addStyleSheet(JURI::root().$this->element['path'].'css/admin.css');
			$document->addStyleSheet(JURI::root().$this->element['path'].'css/chosen.css');        
		}
		// Joomla 3
		else {
			//Add js
			$document->addScript(JURI::root().$this->element['path'].'js/admin.js');
			
			//Add css         
			$document->addStyleSheet(JURI::root().$this->element['path'].'css/admin.css');
		}
        
        return null;
    }
}
?>