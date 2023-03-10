<?php

/**
 * virtuemart table class, with some additional behaviours.
 *
 * @version $Id$
 * @package    VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 -2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('_JEXEC') or die();

/**
 * Replaces JTable with some more advanced functions and fitting to the nooku conventions
 *
 * checked_out = locked_by,checked_time = locked_on
 *
 * Enter description here ...
 * @author Milbo
 *
 */
class VmTable extends JTable {

	protected $_pkey = '';
	protected $_pkeyForm = '';
	protected $_obkeys = array();
	protected $_unique = false;
	protected $_unique_name = array();
	protected $_orderingKey = 'ordering';

	protected $_slugAutoName = '';
	protected $_slugName = '';
	protected $_loggable = false;
	public $_xParams = 0;
	public $_varsToPushParam = array();
	var $_translatable = false;
	protected $_translatableFields = array();
	public $_cryptedFields = false;
	protected $_langTag = null;
	protected $_ltmp = false;
	protected $_tbl_lang = null;
	protected $_updateNulls = false;

	private static $_cache = array();

	/**
	 * @param string $table
	 * @param string $key
	 * @param JDatabase $db
	 */
	function __construct($table, $key, &$db) {

		$this->_tbl = $table;
		$this->_db =& $db;
		$this->_pkey = $key;
		$this->_pkeyForm = 'cid';

		if(JVM_VERSION<3){
			$this->_tbl_key = $key;
		} else {
			// Set the key to be an array.
			if (is_string($key)){
				$key = array($key);
			} elseif (is_object($key)){
				$key = (array) $key;
			}

			$this->_tbl_keys = $key;

			if (count($key) == 1) {
				$this->_autoincrement = true;
			} else {
				$this->_autoincrement = false;
			}

			// Set the singular table key for backwards compatibility.
			$this->_tbl_key = $this->getKeyName();
		}

		// If we are tracking assets, make sure an access field exists and initially set the default.
		if (property_exists($this, 'asset_id')){
			$this->_trackAssets = true;
		}

		// If the access property exists, set the default.
		if (property_exists($this, 'access')){
			$this->access = (int) JFactory::getConfig()->get('access');
		}

		if(JVM_VERSION>2){
			// Implement JObservableInterface:
			// Create observer updater and attaches all observers interested by $this class:
			$this->_observers = new JObserverUpdater($this);
			JObserverMapper::attachAllObservers($this);
		}

	}

	public function setPrimaryKey($key, $keyForm = 0) {

		$error = vmText::sprintf('COM_VIRTUEMART_STRING_ERROR_PRIMARY_KEY', vmText::_('COM_VIRTUEMART_' . strtoupper($key)));
		$this->setObligatoryKeys('_pkey', $error);
		$this->_pkey = $key;
		$this->_pkeyForm = empty($keyForm) ? $key : $keyForm;
		$this->$key = 0;
	}

	public function getPKey(){
		return $this->_pkey;
	}

	public function setObligatoryKeys($key) {

		$this->_obkeys[$key] = 1;
	}

	public function setUniqueName($name) {
		$this->_unique = true;
		$this->_obkeys[$name] = 1;
		$this->_unique_name[$name] = 1;
	}

	public function setLoggable() {

		$this->_loggable = true;
		$this->created_on = false;
		$this->created_by = 0;
		$this->modified_on = '';
		$this->modified_by = 0;
	}

	/**
	 *
	 * @author Patrick Kohl,
	 * @author Max Milbers
	 */
	public function setTranslatable($langFields) {

		$this->_translatableFields = $langFields;
		$this->_translatableFields['slug'] = 'slug';
		$this->_translatable = true;

		if (!class_exists('VmConfig')) require(JPATH_COMPONENT_ADMINISTRATOR .'helpers/config.php');
		VmConfig::loadConfig();

		$this->_langTag = VmConfig::$vmlang;
		$this->_tbl_lang = $this->_tbl . '_' . $this->_langTag;
	}

	public function getTranslatableFields() {

		return $this->_translatableFields;
	}

	public function setLockable() {

		$this->locked_on = '';
		$this->locked_by = 0;
	}

	function setOrderable($key = 'ordering', $auto = true) {

		$this->_orderingKey = $key;
		$this->_orderable = 1;
		$this->_autoOrdering = $auto;
		$this->$key = 0;
	}

	function setSlug($slugAutoName, $key = 'slug') {

		// 		$this->_useSlug = true;
		$this->_slugAutoName = $slugAutoName;
		$this->_slugName = $key;
		$this->$key = '';
		$this->setUniqueName($key);

	}

	var $_tablePreFix = '';

	function setTableShortCut($prefix) {

		$this->_tablePreFix = $prefix . '.';
	}

	public function emptyCache(){
		self::$_cache = array();
	}

	/**
	 * This function defines a database field as parameter field, which means that some values get injected there
	 * As delimiters are used | for the pair and = for key, value
	 *
	 * @author Max Milbers
	 * @param string $paramsFieldName
	 * @param string $varsToPushParam
	 * @param boolean $overwrite
	 */
	function setParameterable($paramsFieldName, $varsToPushParam, $overwrite = false) {

		//if($this->_xParams===0)
		$this->_xParams = $paramsFieldName;

		if ($overwrite) {
			$this->_varsToPushParam = $varsToPushParam;
		} else {
			$this->_varsToPushParam = array_merge((array)$varsToPushParam, (array)$this->_varsToPushParam);
		}

		foreach ($this->_varsToPushParam as $k => $v) {
			if (!isset($this->$k)) $this->$k = $v[0];
		}
		//vmdebug('setParameterable called '.$this->_xParams,$this->_varsToPushParam);
	}


	/**
	 * This function must be
	 * Takes the bounded values at obj of the field $xParams
	 * and adds them as attributs of obj
	 * @param $obj
	 * @param $xParams
	 * @param $varsToPushParam
	 */
	static function bindParameterable(&$obj, $xParams, $varsToPushParam) {

		if(empty($varsToPushParam)) return;
		//$paramFields = $obj->$xParams;
		//vmdebug('$obj->_xParams '.$xParams.' $varsToPushParam ',$varsToPushParam);
		if(is_object($obj)){
			if (!empty($obj->$xParams)) {

				$params = explode('|', $obj->$xParams);
				foreach ($params as $item) {

					$item = explode('=', $item);
					$key = $item[0];
					unset($item[0]);

					$item = implode('=', $item);

					if (!empty($item) && isset($varsToPushParam[$key][1])) {
						$obj->$key = json_decode($item);
					}
				}

			} else {
				if (empty($xParams)) {
					//vmError('There are bindParameterables, but $xParams is empty, this is a programmers error ',$varsToPushParam);
					vmdebug('There are bindParameterables, but $xParams is empty, this is a programmers error ', $obj);
					vmTrace('$xParams is empty');
				}
				if(!isset($obj->$xParams)){
					//vmError('There are bindParameterables, but $obj->$xParams is empty, this is a programmers error '.$xParams);
					vmdebug('There are bindParameterables, but $obj->$xParams is empty, this is a programmers error ',$xParams , $obj);
					vmTrace('$obj->$xParams is empty');
				}
			}

			foreach ($varsToPushParam as $key => $v) {
				if (!isset($obj->$key)) {
					$obj->$key = $v[0];
				}
			}
		} else {
			if (!empty($obj[$xParams])) {

				$params = explode('|', $obj[$xParams]);
				foreach ($params as $item) {

					$item = explode('=', $item);
					$key = $item[0];
					unset($item[0]);

					$item = implode('=', $item);

					if (!empty($item) && isset($varsToPushParam[$key][1])) {
						$obj[$key] = json_decode($item);
					}
				}
			} else {
				if (empty($xParams)) {
					//vmError('There are bindParameterables, but $xParams is empty, this is a programmers error ',$varsToPushParam);
					vmdebug('There are bindParameterables, but $xParams is empty, this is a programmers error ', $obj);
					vmTrace('$xParams is empty');
				}
				if(!isset($obj[$xParams])){
					//vmError('There are bindParameterables, but $obj->$xParams is empty, this is a programmers error '.$xParams);
					vmdebug('There are bindParameterables, but $obj->$xParams is empty, this is a programmers error ',$xParams , $obj);
					vmTrace('$obj->$xParams is empty');
				}
			}

			foreach ($varsToPushParam as $key => $v) {
				if (!isset($obj[$key])) {
					$obj[$key] = $v[0];
				}
			}
		}

	}

	/**
	 * Sets fields encrypted
	 * @author Max Milbers
	 * @param $fieldNames
	 */
	public function setCryptedFields($fieldNames){
		if(!$fieldNames){
			vmTrace('setEncrytped fields false not catched');
			return;
		}
		if(!is_array($fieldNames)) $fieldNames = array($fieldNames);
		if(isset($fieldNames[$this->_pkey])){
			unset($fieldNames[$this->_pkey]);
		}
		$this->_cryptedFields = $fieldNames;
	}

	/**
	 *
	 */
	public function getCryptedFields(){
		return $this->_cryptedFields;
	}


	/**
	 * Gives Back the columns of the current table, sets the properties on the table.
	 *
	 * @author Max Milbers
	 * @param int $typeKey use "Field" to get the effect of getTableColumns
	 * @param int $typeValue use "Type" to get the effect of getTableColumns
	 * @param bool $properties disable setting of columns as table properties
	 */
	public function showFullColumns($typeKey=0,$typeValue=0,$properties=true){

		$hash = 'SFL'.$this->_tbl.$typeKey.$typeValue;
		if (!isset(self::$_cache[$hash])) {
			$this->_db->setQuery('SHOW FULL COLUMNS  FROM `'.$this->_tbl.'` ') ;
			self::$_cache[$hash] = $this->_db->loadAssocList();
		}

		if ($properties and count(self::$_cache[$hash]) > 0) {
			foreach (self::$_cache[$hash] as $key => $_f) {
				$_fieldlist[$_f['Field']] = $_f['Default'];
			}
			$this->setProperties($_fieldlist);
		}

		if ($typeKey or $typeValue){
			foreach (self::$_cache[$hash] as $field){
				if(empty($typeValue)){
					$value = $field;
				} else {
					$value = $field[$typeValue];
				}
				if($typeKey){
					$result[$field[$typeKey]] = $value;
				} else {
					$result[] = $value;
				}
			}
		} else {
			$result = self::$_cache[$hash];
		}

		return $result;
	}

	public function loadFields(){
		return $this->showFullColumns();
	}

	function loadFieldValues($array=true){

		$tmp = get_object_vars($this);
		if($array){
			$return = array();
			foreach ($tmp as $k => $v){
				// Do not process internal variables
				if ('_' != substr($k, 0, 1)){
					$return[$k] = $v;
				}
			}
		} else {
			$return = new stdClass();
			foreach ($tmp as $k => $v){
				// Do not process internal variables
				if ('_' != substr($k, 0, 1)){
					$return->$k = $v;
				}
			}
		}

		return $return;
	}

	function checkDataContainsTableFields($from, $ignore = array()) {

		if (empty($from))
			return false;
		$fromArray = is_array($from);
		$fromObject = is_object($from);

		if (!$fromArray && !$fromObject) {
			vmError(get_class($this) . '::check if data contains table fields failed. Invalid from argument <pre>' . print_r($from, 1) . '</pre>');
			return false;
		}
		if (!is_array($ignore)) {
			$ignore = explode(' ', $ignore);
		}
		$properties = $this->getProperties();
		foreach ($properties as $k => $v) {
			// internal attributes of an object are ignored
			if (!in_array($k, $ignore)) {

				if ($fromArray && isset($from[$k])) {
					return true;
				} else if ($fromObject && isset($from->$k)) {
					return true;
				}
			}
		}
		vmdebug('VmTable developer notice, table ' . get_class($this) . ' means that there is no data to store. When you experience that something does not get stored as expected, please write in the forum.virtuemart.net',$properties);
		return false;
	}

	/**
	 * Function setting the loggable data hack procted
	 * In case you want to override the value for administrators, just set the created_on to "0000-00-00 00:00:00"
	 *
	 * @author Max Milbers
	 */
	function setLoggableFieldsForStore() {

		if ($this->_loggable) {

			// set default values always used

			//We store in UTC time, dont touch it!
			$date = JFactory::getDate();
			$today = $date->toSQL();
			//vmdebug('my today ',$date);
			$user = JFactory::getUser();

			$pkey = $this->_pkey;
			//Lets check if the user is admin or the mainvendor
			$admin = JFactory::getUser()->authorise('core.admin', 'com_virtuemart');
			$adminSessionID = JFactory::getSession()->get('vmAdminID');
			if ($admin || JFactory::getUser($adminSessionID)->authorise('core.admin', 'com_virtuemart')) {
//				vmdebug('setLoggableFieldsForStore ', $this->created_on);
				if (empty($this->$pkey) and empty($this->created_on)) {
					$this->created_on = $today;
				} else if (empty($this->created_on)) {
					//If nothing is there, dont update it
					unset($this->created_on);
				} else //ADDED BY P2 PETER
					if ($this->created_on == "0000-00-00 00:00:00") {
						$this->created_on = $today;
						$this->created_by = $user->id;
					}
				//END ADD

				if (empty($this->$pkey) and empty($this->created_by)) {
					$this->created_by = $user->id;
				} else if (empty($this->created_by)) {
					//If nothing is there, dont update it
					unset($this->created_by);
				}


			} else {

				if (empty($this->$pkey)) {
					$this->created_on = $today;
					$this->created_by = $user->id;
				} else {
					//If nothing is there, dont update it
					unset($this->created_on);
					unset($this->created_by);
				}
			}

			$this->modified_on = $today;
			$this->modified_by = $user->id;
		}

		if (isset($this->locked_on)) {
			//Check if user is allowed to store, then disable or prevent storing
			$this->locked_on = 0;
		}
	}

	/**
	 * Technic to inject params as table attributes
	 * @author Max Milbers
	 * $TableJoins array of table names to add and left join to find ID
	 */
	function load($oid = null, $overWriteLoadName = 0, $andWhere = 0, $tableJoins = array(), $joinKey = 0) {

		if( $overWriteLoadName!==0 ){
			$k = $overWriteLoadName;
		} else {
			$k = $this->_pkey;
		}

		if ($oid !== null) {
			$this->$k = $oid;
		} else {
			$oid = $this->$k;
		}

		if (empty($oid)) {
			if (!empty($this->_xParams)) {
				if(!empty($this->_varsToPushParam)){
					foreach ($this->_varsToPushParam as $key => $v) {
						if (!isset($this->$key)) {
							$this->$key = $v[0];
						}
					}
				} else {
					//vmdebug('_varsToPushParam empty ',$this);
				}
			}
			//vmdebug('vmtable load empty $oid return proto',$this);
			return $this;
		}

		//Version load the tables using JOIN
		if ($this->_translatable) {
			$mainTable =  $this->_tbl;
			$langTable = $this->_tbl . '_' . $this->_langTag;

			$select = 'SELECT `' . $mainTable . '`.* ,`' . $langTable . '`.* ';
			$from = ' FROM `' . $mainTable . '` INNER JOIN `' . $langTable . '` using (`' . $this->_tbl_key . '`)';
		} else {
			$mainTable = $this->_tbl;
			$select = 'SELECT `' . $mainTable . '`.* ';
			$from = ' FROM `' . $mainTable . '` ';
		}

		if (count($tableJoins)) {
			if (!$joinKey) $joinKey = $this->_tbl_key;
			foreach ($tableJoins as $tableId => $table) {
				$select .= ',`' . $table . '`.`' . $tableId . '` ';
				$from .= ' LEFT JOIN `' . $table . '` on `' . $table . '`.`' . $joinKey . '`=`' . $mainTable . '`.`' . $joinKey . '`';
			}
		}
		//the cast to int here destroyed the query for keys like virtuemart_userinfo_id, so no cast on $oid
		// $query = $select.$from.' WHERE '. $mainTable .'.`'.$this->_tbl_key.'` = "'.$oid.'"';
		if ($andWhere === 0) $andWhere = '';
		$query = $select . $from . ' WHERE `' . $mainTable . '`.`' . $k . '` = "' . $oid . '" ' . $andWhere;

		$hashVarsToPush = '';
		if (!empty($this->_xParams)) {
			$hashVarsToPush = serialize($this->_varsToPushParam);
		}
		$hash = md5($oid. $select . $k . $andWhere . $hashVarsToPush);

		if (isset (self::$_cache['l'][$hash])) {
			//vmdebug('Resturn cached '.$this->_pkey.' '.$this->_slugAutoName.' '.$oid);
			$this->bind(self::$_cache['l'][$hash]);
			return $this;
		} else {
			//vmdebug('loading '.$this->_pkey.' '.$this->_slugAutoName.' '.$oid);
		}

		$db = $this->getDBO();
		$db->setQuery($query);

		$result = $db->loadAssoc();

		if ($result) {
			$this->bind($result);
			if (!empty($this->_xParams)) {
				//Maybe better to use for $this an &
				self::bindParameterable($this, $this->_xParams, $this->_varsToPushParam);
			}

			if (count($tableJoins)) {
				foreach ($tableJoins as $tableId => $table) {
					if (isset($result[$tableId])) $this->$tableId = $result[$tableId];
				}
			}
		} else {
			$params = JComponentHelper::getParams('com_languages');
			$defaultLang = $params->get('site', 'en-GB');//use default joomla
			$defaultLang= strtolower(strtr($defaultLang,'-','_'));

			if($defaultLang!=$this->_langTag and Vmconfig::$langCount>1){
				$this->_ltmp = $this->_langTag;
				$this->_langTag = $defaultLang;
				$this->load($oid, $overWriteLoadName, $andWhere, $tableJoins, $joinKey) ;
			}
		}

		if($this->_cryptedFields){
			if(!class_exists('vmCrypt')){
				require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcrypt.php');
			}
			if(isset($this->modified_on)){
				$timestamp = strtotime($this->modified_on);
				$date = $timestamp;
			} else {
				$date = 0;
			}

			if($this->_cryptedFields){
				if(!class_exists('vmCrypt')){
					require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcrypt.php');
				}
				if(isset($this->modified_on)){
					$timestamp = strtotime($this->modified_on);
					$date = $timestamp;
				} else {
					$date = 0;
				}

				foreach($this->_cryptedFields as $field){
					if(isset($this->$field)){
						$this->$field = vmCrypt::decrypt($this->$field,$date);
					}

				}
			}
		}

		if($this->_ltmp){
			$this->_langTag = $this->_ltmp;
			$this->_ltmp = false;
		}

		self::$_cache['l'][$hash] = $this->loadFieldValues(false);
		return $this;
	}


	/**
	 * Technic to inject params as table attributes
	 * @author Max Milbers
	 */
	function store($updateNulls = false) {

		$this->setLoggableFieldsForStore();

		if($this->_cryptedFields){
			if(!class_exists('vmCrypt')){
				require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcrypt.php');
			}
			vmdebug('my crytped fields in store '.get_class($this),$this->_cryptedFields);
			foreach($this->_cryptedFields as $field){
				if(isset($this->$field)){
					$this->$field = vmCrypt::encrypt($this->$field);
				} else {
					vmdebug('Store vmtable empty property for '.$field);
				}
			}
		}

		$this->storeParams();

		return parent::store($updateNulls);

	}


	function storeParams() {

		if (!empty($this->_xParams)) {
			$paramFieldName = $this->_xParams;
			$this->$paramFieldName = '';
			foreach ($this->_varsToPushParam as $key => $v) {

				if (isset($this->$key)) {
					$this->$paramFieldName .= $key . '=' . json_encode($this->$key) . '|';
				} else {
					$this->$paramFieldName .= $key . '=' . json_encode($v[0]) . '|';
				}
				unset($this->$key);
			}
		}
		return true;
	}


	function checkCreateUnique($tbl_name, $name) {

		$i = 0;

		while ($i < 20) {

			$tbl_key = $this->_tbl_key;
			$q = 'SELECT `' . $name . '` FROM `' . $tbl_name . '` WHERE `' . $name . '` =  "' . $this->$name . '" ';
			if(!empty($this->$tbl_key)){
				$q .= '  AND `' . $this->_tbl_key . '`!=' . $this->$tbl_key.' ';
			}
			$this->_db->setQuery($q);
			$existingSlugName = $this->_db->loadResult();

			if (!empty($existingSlugName)) {

				if($posNbr = strrpos($this->$name,'-')){
					$existingNbr = substr($this->$name,$posNbr+1);

					if(is_numeric($existingNbr)){
						$existingNbr++;
						if($i>10){
							$existingNbr = $existingNbr +  rand (1, 9);
						}
						$this->$name = substr($this->$name,0,$posNbr+1) . $existingNbr;
					} else{
						$this->$name = $this->$name . '-1';
					}
				} else {
					$this->$name = $this->$name . '-1';
				}

			} else {
				return true;
			}
			$i++;
		}

		return false;

	}


	/**
	 * @author Max Milbers
	 * @param
	 */
	function check() {

		if (!empty($this->_slugAutoName)) {

			$slugAutoName = $this->_slugAutoName;
			$slugName = $this->_slugName;

			if (in_array($slugAutoName, $this->_translatableFields)) {
				$checkTable = $this->_tbl . '_' . VmConfig::$vmlang;
			} else {
				$checkTable = $this->_tbl;
			}

			if (empty($this->$slugName)) {
				// 				vmdebug('table check use _slugAutoName '.$slugAutoName.' '.$slugName);
				if (!empty($this->$slugAutoName)) {
					$this->$slugName = $this->$slugAutoName;
				} else {
					vmError('VmTable ' . $checkTable . ' Check not passed. Neither slug nor obligatory value at ' . $slugAutoName . ' for auto slug creation is given');
					return false;
				}

			}

			//if (JVM_VERSION === 1) $this->$slugName = JFilterOutput::stringURLSafe($this->$slugName);
			//else $this->$slugName = JApplication::stringURLSafe($this->$slugName);
			//pro+#'!"??$%&/()=?duct-w-| ||cu|st|omfield-|str<ing>
			//vmdebug('my slugName '.$slugName,$this->$slugName);
			$this->$slugName = str_replace('-', ' ', $this->$slugName);

			//$config =& JFactory::getConfig();
			//$transliterate = $config->get('unicodeslugs');
			$unicodeslugs = VmConfig::get('transliterateSlugs',false);
			if($unicodeslugs){
				$lang = JFactory::getLanguage();
				$this->$slugName = $lang->transliterate($this->$slugName);
			}

			// Trim white spaces at beginning and end of alias and make lowercase
			$this->$slugName = trim(JString::strtolower($this->$slugName));
			$this->$slugName = str_replace(array('`','??',"'"),'',$this->$slugName);

			$this->$slugName = vRequest::filterUword($this->$slugName,'-,_,.,|','-');
			while(strpos($this->$slugName,'--')){
				$this->$slugName = str_replace('--','-',$this->$slugName);
			}
			// Trim dashes at beginning and end of alias
			$this->$slugName = trim($this->$slugName, '-');

			if($unicodeslugs)$this->$slugName = rawurlencode($this->$slugName);

			$valid = $this->checkCreateUnique($checkTable, $slugName);
			vmdebug('my Final slugName '.$slugName,$this->slugName);
			if (!$valid) {
				return false;
			}

		}

		foreach ($this->_obkeys as $obkeys => $error) {
			if (empty($this->$obkeys)) {
				$error = get_class($this) . ' ' .vmText::sprintf('COM_VIRTUEMART_STRING_ERROR_OBLIGATORY_KEY', 'COM_VIRTUEMART_' . strtoupper($obkeys) );
				vmError($error);
				return false;
			}
		}

		if ($this->_unique) {
			if (empty($this->_db)) $this->_db = JFactory::getDBO();
			foreach ($this->_unique_name as $obkeys => $error) {

				if (empty($this->$obkeys)) {
					$error = vmText::sprintf('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME', 'COM_VIRTUEMART_' . strtoupper($obkeys));
					vmError('Non unique ' . $this->_unique_name . ' ' . $error);
					return false;
				} else {

					$valid = $this->checkCreateUnique($this->_tbl, $obkeys);
					if (!$valid) {
						return false;
					}
				}
			}
		}


		if (isset($this->virtuemart_vendor_id) ) {

			if(empty($this->virtuemart_vendor_id) and $this->_pkey=='virtuemart_vendor_id'){
				$this->virtuemart_vendor_id = $this->_pvalue;
			}

			$multix = Vmconfig::get('multix', 'none');
			//Lets check if the user is admin or the mainvendor
			$virtuemart_vendor_id = false;
			if ($multix == 'none' and get_class($this) !== 'TableVmusers') {

				$this->virtuemart_vendor_id = 1;
				return true;
			} else {
				$loggedVendorId = VmConfig::isSuperVendor();
				$user = JFactory::getUser();
				$admin = $user->authorise('core.admin','com_virtuemart');

				$tbl_key = $this->_tbl_key;
				$className = get_class($this);
				if (strpos($this->_tbl,'virtuemart_vmusers')===FALSE) {
					$q = 'SELECT `virtuemart_vendor_id` FROM `' . $this->_tbl . '` WHERE `' . $this->_tbl_key . '`="' . $this->$tbl_key . '" ';
					if (!isset(self::$_cache[md5($q)])) {
						$this->_db->setQuery($q);
						$virtuemart_vendor_id = $this->_db->loadResult();
					} else $virtuemart_vendor_id = self::$_cache[md5($q)];
				} else {
					$q = 'SELECT `virtuemart_vendor_id`,`user_is_vendor` FROM `' . $this->_tbl . '` WHERE `' . $this->_tbl_key . '`="' . $this->$tbl_key . '" ';
					if (!isset(self::$_cache[md5($q)])) {
						$this->_db->setQuery($q);
						$vmuser = $this->_db->loadRow();
					} else $vmuser = self::$_cache[md5($q)];

					if ($vmuser and count($vmuser) === 2) {
						$virtuemart_vendor_id = $vmuser[0];
						$user_is_vendor = $vmuser[1];

						if ($multix == 'none') {
							if (empty($user_is_vendor)) {
								$this->virtuemart_vendor_id = 0;
							} else {
								$this->virtuemart_vendor_id = 1;
							}
							return true;
						} else {
							if (!$admin) {
								$this->virtuemart_vendor_id = $loggedVendorId;
								return true;
							}
						}
					} else {
						//New User
						//vmInfo('We run in multivendor mode and you did not set any vendor for '.$className.' and '.$this->_tbl);//, Set to mainvendor '.$this->virtuemart_vendor_id
					}
				}

				if (!$admin and !empty($virtuemart_vendor_id) and !empty($loggedVendorId) and $loggedVendorId != $virtuemart_vendor_id) {

					//vmWarn('COM_VIRTUEMART_NOT_SAME_VENDOR',$loggedVendorId,$virtuemart_vendor_id
					//vmWarn('Stop try to hack this store, you got logged');
					vmdebug('Hacking attempt stopped, logged vendor ' . $loggedVendorId . ' but data belongs to ' . $virtuemart_vendor_id);
					return false;
				} else if (!$admin) {
					if ($virtuemart_vendor_id) {
						$this->virtuemart_vendor_id = $virtuemart_vendor_id;
						vmdebug('Non admin is storing using loaded vendor_id');
					} else {
						if(empty($this->virtuemart_vendor_id)){
							$this->virtuemart_vendor_id = $loggedVendorId;
						}
						//No id is stored, even users are allowed to use for the storage and vendorId, no change
					}

				} else {
					//Admins are allowed to do anything. We just trhow some messages
					if (!empty($virtuemart_vendor_id) and $loggedVendorId != $virtuemart_vendor_id) {
						vmdebug('Admin with vendor id ' . $loggedVendorId . ' is using for storing vendor id ' . $this->virtuemart_vendor_id);
					}
					else if (empty($virtuemart_vendor_id)) {
						if(strpos($this->_tbl,'virtuemart_vendors')===FALSE and strpos($this->_tbl,'virtuemart_vmusers')===FALSE){
							vmdebug('Fallback to '.$this->virtuemart_vendor_id.': We run in multivendor mode and you did not set any vendor for '.$className.' and '.$this->_tbl);
							$this->virtuemart_vendor_id = $this->virtuemart_vendor_id;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * As shortcat, Important the & MUST be there, even in php5.3
	 *
	 * @author Max Milbers
	 * @param array/obj $data input data as assoc array or obj
	 * @param boolean $preload You can preload the data here too preserve not updated data
	 * @return array/obj $data the updated data
	 */
	public function bindChecknStore(&$data, $preload = false) {

		$tblKey = $this->_tbl_key;
		$ok = true;
		if ($this->_translatable) {
			if (!class_exists('VmTableData')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtabledata.php');
			$db = JFactory::getDBO();

			$langTable = new VmTableData($this->_tbl_lang, $tblKey, $db);
			$langTable->setPrimaryKey($tblKey);
			$langData = array();
			$langObKeys = array();
			$langUniqueKeys = array();

			if (is_object($data)) {

				foreach ($this->_translatableFields as $name) {
					if (isset($data->$name)) {
						//We directly store language stuff "escaped"
						$langData[$name] = htmlentities($data->$name, ENT_QUOTES, "UTF-8");
					} else {
					//	$langData[$name] = '';
					}
					unset($this->$name);

					if (!empty($this->_unique_name[$name])) {
						$langUniqueKeys[$name] = 1;
						unset($this->_unique_name[$name]);
						$langObKeys[$name] = 1;
						unset($this->_obkeys[$name]);
					}

					if (!empty($this->_obkeys[$name])) {
						$langObKeys[$name] = 1;
						unset($this->_obkeys[$name]);
					}

				}
				// 				$langTable->$tblKey = $data->$tblKey;
			} else {
				foreach ($this->_translatableFields as $name) {
					if (isset($data[$name])) {
						//$langData[$name] = $data[$name];
						$langData[$name] = htmlentities($data[$name], ENT_QUOTES, "UTF-8");
					} else {
					//	$langData[$name] = '';
					}
					unset($this->$name);

					if (!empty($this->_unique_name[$name])) {
						$langUniqueKeys[$name] = 1;
						unset($this->_unique_name[$name]);
						$langObKeys[$name] = 1;
						unset($this->_obkeys[$name]);
					}

					if (!empty($this->_obkeys[$name])) {
						$langObKeys[$name] = 1;
						unset($this->_obkeys[$name]);
					}

				}
				// 				$langTable->$tblKey = $data[$tblKey];
			}

			$langTable->_unique_name = $langUniqueKeys;
			$langTable->_obkeys = $langObKeys;

			$langTable->_slugAutoName = $this->_slugAutoName;
			unset($this->_slugAutoName);

			$langTable->_slugName = 'slug';
			unset($this->_slugName);

			$langTable->setProperties($langData);
			$langTable->_translatable = false;
			//We must check the langtable BEFORE we store the normal table, cause the langtable is often defining if there are enough data to store it (for exmple the name)

			if ($ok) {
				//vmdebug('my langtable before bind',$langTable->id);
				if (!$langTable->bind($data)) {
					$ok = false;
					$msg = 'bind';
					// 			vmdebug('Problem in bind '.get_class($this).' '.$this->_db->getErrorMsg());
					vmdebug('Problem in bind ' . get_class($this) . ' ');
				}
			}

			if ($ok) {
				if (!$langTable->check()) {
					$ok = false;
					vmdebug('Check returned false ' . get_class($langTable) . ' ' . $this->_tbl . ' ' . $langTable->_db->getErrorMsg());
				}
			}

			if ($ok) {

				$this->bindChecknStoreNoLang($data, $preload);

				$langTable->$tblKey = !empty($this->$tblKey) ? $this->$tblKey : 0;
				//vmdebug('bindChecknStoreNoLang my $tblKey '.$tblKey.' '.$langTable->$tblKey);
				if ($ok and $preload) {
					if (!empty($langTable->$tblKey)) {
						$id = $langTable->$tblKey;
						if (!$langTable->load($id)) {
							$ok = false;
							vmdebug('Preloading of language table failed, no id given, cannot store ' . $this->_tbl);
						}
					} else {
						if ($ok) {
							if (!$langTable->bind($data)) {
								$ok = false;
								vmdebug('Problem in bind ' . get_class($this) . ' ');
							}
						}

						if ($ok) {
							if (!$langTable->check()) {
								$ok = false;
								vmdebug('Check returned false ' . get_class($langTable) . ' ' . $this->_tbl . ' ' . $langTable->_db->getErrorMsg());
							}
						}
					}
				}

				if ($ok) {
					if (!$langTable->store()) {
						$ok = false;
						// $msg .= ' store';
						vmdebug('Problem in store with langtable ' . get_class($langTable) . ' with ' . $tblKey . ' = ' . $this->$tblKey . ' ' . $langTable->_db->getErrorMsg());
					}
				}
			}


		} else {

			if (!$this->bindChecknStoreNoLang($data, $preload)) {
				$ok = false;
			}
		}

		return $ok;
	}


	function bindChecknStoreNoLang(&$data, $preload = false) {

		$tblKey = $this->_tbl_key;

		if ($preload) {
			if (is_object($data)) {
				if (!empty($data->$tblKey)) {
					$this->load($data->$tblKey);
				}
			} else {
				if (!empty($data[$tblKey])) {
					$this->load($data[$tblKey]);
				}
			}

			if ($this->_translatable) {
				foreach ($this->_translatableFields as $name) {
					unset($this->$name);
				}
			}
			//vmdebug('bindChecknStoreNoLang language unloaded, why?');
		}

		$ok = true;
		$msg = '';

		if (!$this->bind($data)) {
			$ok = false;
			$msg = 'bind';
			// 			vmdebug('Problem in bind '.get_class($this).' '.$this->_db->getErrorMsg());
			vmdebug('Problem in bind ' . get_class($this) . ' ');
		}

		if ($ok) {
			if (!$this->checkDataContainsTableFields($data)) {
				$ok = false;
				//    			$msg .= ' developer notice:: checkDataContainsTableFields';
			}
		}

		if ($ok) {
			if (!$this->check()) {
				$ok = false;
				$msg .= ' check';
				vmdebug('Check returned false ' . get_class($this) . ' ' . $this->_db->getErrorMsg());
				return false;
			}
		}

		if ($ok) {
			if (!$this->store($this->_updateNulls)) {
				$ok = false;
				$msg .= ' store';
				vmdebug('Problem in store ' . get_class($this) . ' ' . $this->_db->getErrorMsg());
				return false;
			}
		}


		if (is_object($data)) {
			$data->$tblKey = !empty($this->$tblKey) ? $this->$tblKey : 0;
		} else {
			$data[$tblKey] = !empty($this->$tblKey) ? $this->$tblKey : 0;
		}

		// 		vmdebug('bindChecknStore '.get_class($this).' '.$this->_db->getErrorMsg());
		//This should return $ok and not the data, because it is already updated due use of reference
		return $data;
	}

	/**
	 * Description
	 * will make sure that all items in the table are not using the same ordering values
	 * @author stAn
	 * @access public
	 * $where -> limits the categories if a child category of another one
	 */
	function fixOrdering($where = '') {

		$where = $where ? ' WHERE ' . $where : '';
		// fast check for duplicities
		$q = 'SELECT `' . $this->_tbl_key . '` FROM `' . $this->_tbl . '` GROUP BY `' . $this->_orderingKey . '` HAVING COUNT(*) >= 2 ' . $where . ' LIMIT 1';
		$this->_db->setQuery($q);
		$res = $this->_db->loadAssocList();
		if (empty($res)) return true;

		$q = ' SELECT `' . $this->_tbl_key . '` FROM `' . $this->_tbl . '` ' . $where . ' ORDER BY `' . $this->_orderingKey . '` ASC';
		$this->_db->setQuery($q, 0, 999999);
		$res = $this->_db->loadAssocList();
		$e = $this->_db->getErrorMsg();
		if (!empty($e)) {
			vmError(get_class($this) . $e);
		}
		echo $q . "<br />\n";
		// no data in the table
		if (empty($res)) return true;
		// we will set ordering to 5,10,15,20,25 so there is enough space in between for manual editing

		$start = 5;
		// it is not really optimized to load full table into array, a while loop would be better especially when having thousands of categories
		foreach ($res as $row) {
			$q = 'UPDATE  `' . $this->_tbl . '` SET `' . $this->_orderingKey . '` = ' . (int)$start . ' WHERE `' . $this->_tbl_key . '`= ' . $row[$this->_tbl_key] . ' LIMIT 1';

			$this->_db->setQuery($q);
			$r = $this->_db->execute($q);
			$start = $start + 5;
		}

	}

	/**
	 * Description
	 *
	 * @author Joomla Team, Max Milbers
	 * @access public
	 * @param $dirn
	 * @param $where
	 */
	function move($dirn, $where = '', $orderingkey = 0) {

		// for some reason this function is not used from categories
		$this->fixOrdering();

		$k = $this->_tbl_key;
		// problem here was that $this->$k returned (0)

		$cid = vRequest::getInt($this->_pkeyForm);
		if (!empty($cid) && (is_array($cid))) {
			$cid = reset($cid);
		} else {
			// either we fix custom fields or fix it here:
			/*$cid = vRequest::getVar($this->_pkeyForm);
			if (!empty($cid) && (is_array($cid))) {
				$cid = reset($cid);
			} else {*/
				vmError(get_class($this) . ' is missing cid information !');
				return false;
			//}
		}		// stAn: if somebody knows how to get current `ordering` of selected cid (i.e. virtuemart_userinfo_id or virtuemart_category_id from defined vars, you can review the code below)
		$q = "SELECT `" . $this->_orderingKey . '` FROM `' . $this->_tbl . '` WHERE `' . $this->_tbl_key . "` = '" . (int)$cid . "' limit 0,1";

		if (!isset(self::$_cache[md5($q)])) {
			$this->_db->setQuery($q);
			$c_order = $this->_db->loadResult(); // current ordering value of cid
		} else {
			$c_order = self::$_cache[md5($q)];
		}

		$this->$orderingkey = $c_order;

		$e = $this->_db->getErrorMsg();
		if (!empty($e)) {
			vmError(get_class($this) . $e);
		}
		// stAn addition:
		$where .= ' `' . $this->_tbl_key . '` <> ' . (int)$cid . ' ';
		// explanation:
		// select one above or under which is not cid and update/set it's ordering of the original cid
		// could be done with one complex query... but this is more straitforward and the speed is not that much needed in this one

		if (!empty($orderingkey))
			$this->_orderingKey = $orderingkey;

		if (!in_array($this->_orderingKey, array_keys($this->getProperties()))) {
			vmError(get_class($this) . ' does not support ordering');
			return false;
		}

		$k = $this->_tbl_key; // virtuemart_userfield_id column name

		$orderingKey = $this->_orderingKey; // ordering column name

		$sql = 'SELECT `' . $this->_tbl_key . '`, `' . $this->_orderingKey . '` FROM ' . $this->_tbl;

		if ($dirn < 0) {
			$sql .= ' WHERE `' . $this->_orderingKey . '` <= ' . (int)$c_order;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= ' ORDER BY `' . $this->_orderingKey . '` DESC';
		} else if ($dirn > 0) {
			$sql .= ' WHERE `' . $this->_orderingKey . '` >= ' . (int)$c_order;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= ' ORDER BY `' . $this->_orderingKey . '`';
		} else {
			$sql .= ' WHERE `' . $this->_orderingKey . '` = ' . (int)$c_order;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= ' ORDER BY `' . $this->_orderingKey . '`';
		}


		if (!isset(self::$_cache[md5($sql)])) {
			$this->_db->setQuery($sql, 0, 1);


			$row = null;
			$row = $this->_db->loadObject();
		} else $row = self::$_cache[md5($sql)];


		if (isset($row)) {

			// ok, we have a problem here - previous or next item has the same ordering as the current one
			// we need to fix the ordering be reordering it all
			if ((int)$row->$orderingKey == $c_order) {
				// if we fix this while loading the ordering, it will slow down FE
			}

			// update the next or previous to have the same ordering as the selected
			$query = 'UPDATE ' . $this->_tbl
				. ' SET `' . $this->_orderingKey . '` = ' . (int)$c_order
				. ' WHERE ' . $this->_tbl_key . ' = ' . (int)$row->$k . ' LIMIT 1';

			$this->_db->setQuery($query);
			echo "\n" . $query . '<br />';

			if (!$this->_db->execute()) {
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, get_class($this) . ':: move isset row $row->$k' . $err);
			}

			// update the currently selected to have the same ordering as the next or previous
			$query = 'UPDATE ' . $this->_tbl
				. ' SET `' . $this->_orderingKey . '` = ' . (int)$row->$orderingKey
				. ' WHERE ' . $this->_tbl_key . ' = "' . (int)$cid . '" LIMIT 1';
			$this->_db->setQuery($query);
			//echo $query.'<br />'; die();
			if (!$this->_db->execute()) {
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, get_class($this) . ':: move isset row $row->$k' . $err);
			}

			// stAn, what for is this?
			$this->ordering = $row->$orderingKey;


		} else {
			// stAn: why should we update the same line with the same information when no next or previous found (?)

			$query = 'UPDATE ' . $this->_tbl
				. ' SET `' . $this->_orderingKey . '` = ' . (int)$this->$orderingKey
				. ' WHERE ' . $this->_tbl_key . ' = "' . $this->_db->escape($this->$k) . '" LIMIT 1';
			$this->_db->setQuery($query);

			if (!$this->_db->execute()) {
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, get_class($this) . ':: move update $this->$k' . $err);
			}
		}
		return true;
	}

	/**
	 * Returns the ordering value to place a new item last in its group
	 *
	 * @access public
	 * @param string query WHERE clause for selecting MAX(ordering).
	 */
	function getNextOrder($where = '', $orderingkey = 0) {

		$where = $this->_db->escape($where);
		$orderingkey = $this->_db->escape($orderingkey);

		if (!empty($orderingkey))
			$this->_orderingKey = $orderingkey;
		if (!in_array($this->_orderingKey, array_keys($this->getProperties()))) {
			vmError(get_class($this) . ' does not support ordering');
			return false;
		}

		$query = 'SELECT MAX(`' . $this->_orderingKey . '`)' .
			' FROM ' . $this->_tbl .
			($where ? ' WHERE ' . $where : '');
		if (!isset(self::$_cache[md5($query)])) {
			$this->_db->setQuery($query);
			$maxord = $this->_db->loadResult();
		} else $maxord = self::$_cache[md5($query)];

		if ($this->_db->getErrorNum()) {
			vmError(get_class($this) . ' getNextOrder ' . $this->_db->getErrorMsg());
			return false;
		}
		return $maxord + 1;
	}

	/**
	 * Compacts the ordering sequence of the selected records
	 *
	 * @access public
	 * @param string Additional where query to limit ordering to a particular subset of records
	 */
	function reorder($where = '', $orderingkey = 0) {

		$where = $this->_db->escape($where);
		$orderingkey = $this->_db->escape($orderingkey);

		if (!empty($orderingkey))
			$this->_orderingKey = $orderingkey;
		$k = $this->_tbl_key;

		if (!in_array($this->_orderingKey, array_keys($this->getProperties()))) {
			vmError(get_class($this) . ' does not support ordering');
			return false;
		}

		if ($this->_tbl == '#__content_frontpage') {
			$order2 = ", content_id DESC";
		} else {
			$order2 = "";
		}

		$query = 'SELECT ' . $this->_tbl_key . ', ' . $this->_orderingKey
			. ' FROM ' . $this->_tbl
			. ' WHERE `' . $this->_orderingKey . '` >= 0' . ($where ? ' AND ' . $where : '')
			. ' ORDER BY `' . $this->_orderingKey . '` ' . $order2;
		$this->_db->setQuery($query);
		if (!($orders = $this->_db->loadObjectList())) {
			vmError(get_class($this) . ' reorder ' . $this->_db->getErrorMsg());
			return false;
		}
		$orderingKey = $this->_orderingKey;
		// compact the ordering numbers
		for ($i = 0, $n = count($orders); $i < $n; $i++) {
			if ($orders[$i]->$orderingKey >= 0) {
				if ($orders[$i]->$orderingKey != $i + 1) {
					$orders[$i]->$orderingKey = $i + 1;
					$query = 'UPDATE ' . $this->_tbl
						. ' SET `' . $this->_orderingKey . '` = "' . $this->_db->escape($orders[$i]->$orderingKey) . '"
					 WHERE ' . $k . ' = "' . $this->_db->escape($orders[$i]->$k) . '"';
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
			}
		}

		return true;
	}

	/**
	 * Checks out a row
	 *
	 * @access public
	 * @param    integer    The id of the user
	 * @param    mixed    The primary key value for the row
	 * @return    boolean    True if successful, or if checkout is not supported
	 */
	function checkout($who, $oid = null) {

		if (!in_array('locked_by', array_keys($this->getProperties()))) {
			return true;
		}

		$k = $this->_tbl_key;
		if ($oid !== null) {
			$this->$k = $oid;
		}

		$config = JFactory::getConfig();
		$siteOffset = $config->get('offset');
		$date = JFactory::getDate('now', $siteOffset);

		$time = $date->toSql();

		$query = 'UPDATE ' . $this->_db->quoteName($this->_tbl) .
			' SET locked_by = ' . (int)$who . ', locked_on = "' . $this->_db->escape($time) . '"
			 WHERE ' . $this->_tbl_key . ' = "' . $this->_db->escape($this->$k) . '"';
		$this->_db->setQuery($query);

		$this->locked_by = $who;
		$this->locked_on = $time;

		return $this->_db->execute();
	}

	/**
	 * Checks in a row
	 *
	 * @access    public
	 * @param    mixed    The primary key value for the row
	 * @return    boolean    True if successful, or if checkout is not supported
	 */
	function checkin($oid = null) {

		if (!(
			in_array('locked_by', array_keys($this->getProperties())) ||
				in_array('locked_on', array_keys($this->getProperties()))
		)
		) {
			return true;
		}

		$k = $this->_tbl_key;

		if ($oid !== null) {
			$this->$k = $oid;
		}

		if ($this->$k == NULL) {
			return false;
		}

		$query = 'UPDATE ' . $this->_db->quoteName($this->_tbl) .
			' SET locked_by = 0, locked_on = "' . $this->_db->escape($this->_db->getNullDate()) . '"
				 WHERE ' . $this->_tbl_key . ' = "' . $this->_db->escape($this->$k) . '"';
		$this->_db->setQuery($query);

		$this->locked_by = 0;
		$this->locked_on = '';

		return $this->_db->execute();
	}

	/**
	 * Check if an item is checked out
	 *
	 * This function can be used as a static function too, when you do so you need to also provide the
	 * a value for the $against parameter.
	 *
	 * @static
	 * @access public
	 * @param integer $with    The userid to preform the match with, if an item is checked out
	 *                            by this user the function will return false
	 * @param integer $against    The userid to perform the match against when the function is used as
	 *                            a static function.
	 * @return boolean
	 */
	function isCheckedOut($with = 0, $against = null) {

		if (isset($this) && is_a($this, 'JTable') && is_null($against)) {
			$against = $this->get('locked_by');
		}

		//item is not checked out, or being checked out by the same user
		if (!$against || $against == $with) {
			return false;
		}

		$session = JTable::getInstance('session');
		return $session->exists($against);
	}

	/**
	 * toggle (0/1) a field
	 * or invert by $val
	 * @author impleri
	 * @author Max Milbers
	 * @param string $field the field to toggle
	 * @param boolean $val field value (0/1)
	 * @todo could make this multi-id as well...
	 */
	function toggle($field, $val = NULL) {

		if ($val === NULL) {
			$this->$field = !$this->$field;
		} else {
			$this->$field = $val;
		}
		$k = $this->_tbl_key;
		$q = 'UPDATE `' . $this->_tbl . '` SET `' . $field . '` = "' . $this->$field . '" WHERE `' . $k . '` = "' . $this->$k . '" ';
		$this->_db->setQuery($q);
		if (!$res = $this->_db->execute()) {
			vmError('There was an error toggling ' . $field, $this->_db->getErrorMsg());
		} else {
			vmdebug('Toggled '.$q );
		}

		return $res;
	}

	public function resetErrors() {

		$this->_errors = array();
	}


	function delete($oid = null, $where = 0) {

		$k = $this->_tbl_key;

		if ($oid) {
			$this->$k = intval($oid);
		}

		$mainTableError = $this->checkAndDelete($this->_tbl, $where);

		if ($this->_translatable) {

			$langs = VmConfig::get('active_languages', array());
			if (!$langs) $langs[] = VmConfig::$vmlang;
			if (!class_exists('VmTableData')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtabledata.php');
			foreach ($langs as $lang) {
				$lang = strtolower(strtr($lang, '-', '_'));
				$langError = $this->checkAndDelete($this->_tbl . '_' . $lang);
				$mainTableError = min($mainTableError, $langError);
			}
		}

		return $mainTableError;
	}

	// author stAn
	// returns true when mysql version is larger than 5.0
	function isMysql51Plus() {

		$r = $this->getMysqlVersion();
		return version_compare($r, '5.1.0', '>=');
	}

	// author: stan, added in 2.0.16+
	// returns mysql version for query optimalization
	function getMysqlVersion() {

		$q = 'select version()';
		if (!isset(self::$_cache[md5($q)])) {
			$this->_db->setQuery($q);
			return $this->_db->loadResult();
		} else return self::$_cache[md5($q)];

	}

	function checkAndDelete($table, $whereField = 0, $andWhere = '') {

		$ok = 1;
		$k = $this->_tbl_key;

		if ($whereField !== 0) {
			$whereKey = $whereField;
		} else {
			$whereKey = $this->_pkey;
		}

		$query = 'SELECT `' . $this->_tbl_key . '` FROM `' . $table . '` WHERE `' . $whereKey . '` = "' . $this->$k . '" '.$andWhere;
		$this->_db->setQuery($query);
		// 		vmdebug('checkAndDelete',$query);
		$list = $this->_db->loadColumn();
		// 		vmdebug('checkAndDelete',$list);


		if ($list) {

			foreach ($list as $row) {
				$ok = $row;
				$query = 'DELETE FROM `' . $table . '` WHERE ' . $this->_tbl_key . ' = "' . $row . '"';
				$this->_db->setQuery($query);

				if (!$this->_db->execute()) {
					$this->setError($this->_db->getErrorMsg());
					vmError('checkAndDelete ' . $this->_db->getErrorMsg());
					$ok = 0;
				}
			}

		}
		return $ok;
	}

	/**
	 * Add, change or drop userfields
	 *
	 * @param string $_act Action: ADD, DROP or CHANGE (synonyms available, see the switch cases)
	 * @param string $_col Column name
	 * @param string $_type fieldtype
	 * @param string $_col2 Second Column name
	 * @return boolean True on success
	 * @author Oscar van Eijk
	 *
	 * stAn - note: i disabled deleting of user data when a column (shopper field) is deleted. If a deletion of specific user or order is needed, it can be done separatedly
	 * The column if not set with $_col2 will be renamed to ORIGINALNAME_DELETED_{timestamp()} and depending on mysql version it's definition will change
	 */
	function _modifyColumn($_act, $_col, $_type = '', $_col2 = '') {

		$_sql = 'ALTER TABLE `' . $this->_tbl . '` ';


		$_check_act = strtoupper(substr($_act, 0, 3));
		//Check if a column is there

		//$columns = $this->_db->getTableColumns($this->_tbl);
		$columns = $this->showFullColumns('Field','Type',false);

		$res = array_key_exists($_col, $columns);

		if ($_check_act != 'ADD' and $_check_act != 'CRE') {
			if (!$res) {
				vmdebug('_modifyColumn Command was ' . $_check_act . ' column does not exist, changed to ADD');
				$_check_act = 'ADD';

			}
		} else {
			if ($res) {
				vmdebug('_modifyColumn Command was ' . $_check_act . ' column already exists, changed to MOD');
				$_check_act = 'UPD';

			}
		}

		switch ($_check_act) {
			case 'ADD':
			case 'CRE': // Create
				$_sql .= "ADD $_col $_type ";
				break;
			case 'DRO': // Drop
			case 'DEL': // Delete
				//stAn, i strongly do not recommend to delete customer information only because a field was deleted
				if (empty($_col2)){
					$_col2 = $_col . '_DELETED_' . time();
					vmInfo('Be aware the column of table '.$this->_tbl.' is not deleted, only renamed to '.$_col2);
				}

				if (!$this->isMysql51Plus()) {
					if (empty($_type)) $_type = 'TEXT CHARACTER SET utf8';
				}

				// NOT NULL not allowed for deleted columns
				//$t_type = str_ireplace(' NOT ', '', $_type);
				$_sql .= "CHANGE $_col $_col2 $_type ";
				//was: $_sql .= "DROP $_col ";
				break;
			case 'MOD': // Modify
			case 'UPD': // Update
			case 'CHA': // Change
				if (empty($col2)) $_col2 = $_col; // change type only
				$_sql .= "CHANGE $_col $_col2 $_type ";
				break;
		}

		$this->_db->setQuery($_sql);

		$this->_db->execute();
		if ($this->_db->getErrorNum() != 0) {
			vmError(get_class($this) . '::modify table - ' . $this->_db->getErrorMsg() . '<br /> values: action ' . $_act . ', columname: ' . $_col . ', type: ' . $_type . ', columname2: ' . $_col2);
			return false;
		}
		vmdebug('_modifyColumn executed successfully ' . $_sql);
		return true;
	}

}
