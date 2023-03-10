<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


class UniteGalleryItems extends UniteElementsBaseUG{
		
	private $operations;
	
	public function __construct(){
		parent::__construct();
		$this->operations = new UGOperations();
	}
	
	
	/**
	 * 
	 * update item 
	 */
	private function update($itemID,$arrUpdate){
		
		$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$itemID));
	}
	
	
	
	
	/**
	 * 
	 * get items by id's
	 */
	private function getItemsByIDs($itemIDs){
		$strItems = implode(",", $itemIDs);
		$tableItems = GlobalsUG::$table_items;
		$sql = "select * from {$tableItems} where id in({$strItems})";
		$arrItems = $this->db->fetchSql($sql);
		
		return($arrItems);
	}
	
	
	/**
	 * get items from array of items
	 */
	public function getItemsFromArray($arrData){
		
		$arrItems = array();
		foreach($arrData as $data){
			
			$item = new UniteGalleryItem();
			$item->initByData($data);
			
			$arrItems[] = $item;
		}
		
		return($arrItems);
	}
	
	
	
	/**
	 * 
	 * get html of cate items
	 */
	private function getCatItemsHtml($catID){
		
		$items = $this->getCatItems($catID);
				
		$htmlItems = "";
		
		foreach($items as $item){
			$html = $item->getHtmlForAdmin();
			$htmlItems .= $html;
		}
		
		return($htmlItems);
	}
	
	
	/**
	 * 
	 * delete items
	 */
	private function deleteItems($arrItems){
		
		//sanitize
		foreach($arrItems as $key=>$itemID)
			$arrItems[$key] = (int)$itemID;
		
		$strItems = implode($arrItems,",");
		$this->db->delete(GlobalsUG::$table_items,"id in($strItems)");
	}
	
	/**
	 * 
	 * duplciate items within same category 
	 */
	private function duplicateItems($arrItemIDs, $catID){
				
		foreach($arrItemIDs as $itemID){
			$this->copyItem($itemID);
		}
	}
	
	
	/**
	 * 
	 * copy items to some category
	 */
	private function copyItems($arrItemIDs,$catID){
		$category = new UniteGalleryCategories();		
		$category->validateCatExist($catID);
		
		foreach($arrItemIDs as $itemID){
			$this->copyItem($itemID,$catID);
		}
	}
	
	/**
	 * 
	 * move items to some category by change category id	 
	 */
	private function moveItem($itemID,$catID){
		$itemID = (int)$itemID;
		$catID = (int)$catID;
				
		$arrUpdate = array();
		$arrUpdate["catid"] = $catID;
		$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$itemID));
	}
	
	/**
	 * 
	 * move multiple items to some category
	 */
	private function moveItems($arrItemIDs, $catID){
		$category = new UniteGalleryCategories();		
		$category->validateCatExist($catID);
		
		foreach($arrItemIDs as $itemID){
			$this->moveItem($itemID, $catID);
		}
	}
	
	
	/**
	 * 
	 * save items order
	 */
	private function saveItemsOrder($arrItemIDs){
		
		//get items assoc
		$arrItems = $this->getItemsByIDs($arrItemIDs);
		$arrItems = UniteFunctionsUG::arrayToAssoc($arrItems,"id");
				
		$order = 0;
		foreach($arrItemIDs as $itemID){
			$order++;
			
			$arrItem = UniteFunctionsUG::getVal($arrItems, $itemID);
			if(!empty($arrItem) && $arrItem["ordering"] == $order)
				continue;
			
			$arrUpdate = array();
			$arrUpdate["ordering"] = $order; 
			$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$itemID));
		}

	}
	
	/**
	 * 
	 * get html of categories and items.
	 */
	private function getCatsAndItemsHtml($catID){
		
		$htmlItems = $this->getCatItemsHtml($catID);
		$objCats = new UniteGalleryCategories();
		$htmlCatList = $objCats->getHtmlCatList($catID);
		
		$response = array();
		$response["htmlItems"] = $htmlItems;
		$response["htmlCats"] = $htmlCatList;
		
		return($response);
	}
	
	
	/**
	 * add image / images from data
	 * return items html
	 */
	private function addFromData_images($data){
	
		$catID = UniteFunctionsUG::getVal($data, "catID");
		
		$arrImages = UniteFunctionsUG::getVal($data, "urlImage");
		
		$isMultiple = false;
		if(is_array($arrImages) == true)
			$isMultiple = true;
		
		//add items, singe or multiple
		if($isMultiple == true){
		
			$itemHtml = "";
			foreach($arrImages as $item){
				$addData = array();
				$addData["catID"] = $catID;
				$urlImage = UniteFunctionsUG::getVal($item, "url");
				$urlImage = HelperUG::URLtoRelative($urlImage);
				$imageID = UniteFunctionsUG::getVal($item, "id");
		
				//make thumb and store thumb address
				$addData["urlImage"] = $urlImage;
				$addData["imageID"] = $imageID;
		
				if(empty($imageID)){
					$urlThumb = $this->operations->createThumbs($urlImage);
					$addData["urlThumb"] = $urlThumb;
				}else{
					$addData["urlThumb"] = UniteProviderFunctionsUG::getThumbUrlFromImageID($imageID);
				}
				
				$addData["type"] = UniteGalleryItem::TYPE_IMAGE;
				
				$objItem = new UniteGalleryItem();
				$objItem->add($addData);
				$itemHtml .= $objItem->getHtmlForAdmin();
			}
		}else{
			$item = new UniteGalleryItem();
			$item->add($data);
		
			//get item html
			$itemHtml = $item->getHtmlForAdmin();
		}
		
		
		return($itemHtml);
	}

	/**
	 * add image / images from data
	 * return items html
	 */
	private function addFromData_media($data){
	
		$item = new UniteGalleryItem();
		$item->add($data);
		$itemHtml = $item->getHtmlForAdmin();		
		
		return($itemHtml);
	}
	
	
	/**
	 * 
	 * get category items
	 */
	public function getCatItems($catID){
		$catID = (int)$catID;
		
		$records = $this->db->fetch(GlobalsUG::$table_items,"catid=$catID","ordering");

		$arrItems = array();
		foreach($records as $record){
			$objItem = new UniteGalleryItem();
			$objItem->initByDBRecord($record);
			$arrItems[] = $objItem;
		}
		
		return($arrItems);
	}
	
	
	/**
	 * 
	 * get max order from categories list
	 */
	public function getMaxOrder($catID){
				
		UniteFunctionsUG::validateNotEmpty($catID,"category id");
		
		$tableItems = GlobalsUG::$table_items;
		$query = "select MAX(ordering) as maxorder from {$tableItems} where catid={$catID}";
		
		///$query = "select * from ".self::TABLE_CATEGORIES;
		$rows = $this->db->fetchSql($query);
				
		$maxOrder = 0;
		if(count($rows)>0) $maxOrder = $rows[0]["maxorder"];
		
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
						
		return($maxOrder);
	}
	
	
	/**
	 * 
	 * copy item to same or different category
	 * if copy to same, then the item will be duplicated 
	 */
	public function copyItem($itemID,$newCatID = -1){
		$order = $this->getMaxOrder($newCatID);
		$newOrder = $order+1;
		
		$fields_item = GlobalsUG::FIELDS_ITEMS;
		$sqlSelect = "select ".$fields_item." from ".GlobalsUG::$table_items." where id={$itemID}";
		$sqlInsert = "insert into ".GlobalsUG::$table_items." (".$fields_item.") ($sqlSelect)";
		
		$this->db->runSql($sqlInsert);
		
		$newItemID = $this->db->getLastInsertID();
		
		//update the ordering:
		$arrUpdate = array();
		$arrUpdate["ordering"] = $newOrder;
		if($newCatID != -1 && !empty($newCatID))
			$arrUpdate["catid"] = $newCatID;
		
		$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$newItemID));
	}
	
	
	
	/**
	 * 
	 * add item from data
	 */
	public function addFromData($data){
		
		$type = UniteFunctionsUG::getVal($data, "type");
		
		$catID = UniteFunctionsUG::getVal($data, "catID");
		UniteFunctionsUG::validateNumeric($catID,"category id");
		
		switch($type){
			case "image":
				$itemHtml = $this->addFromData_images($data);
			break;
			default:		//add media
				$itemHtml = $this->addFromData_media($data);
			break;
		}
		
		//get categories html
		$objCats = new UniteGalleryCategories();		
		$htmlCatList = $objCats->getHtmlCatList($catID);
		
		//output html items and cats
		$output = array();
		$output["htmlItem"] = $itemHtml;
		$output["htmlCats"] = $htmlCatList;
		
		return($output);
	} 
	
	
	/**
	 * 
	 * get category items html
	 */
	public function getCatItemsHtmlFromData($data){
		$catID = UniteFunctionsUG::getVal($data, "catID");
		UniteFunctionsUG::validateNumeric($catID,"category id");
		$itemsHtml = $this->getCatItemsHtml($catID);
		
		$response = array("itemsHtml"=>$itemsHtml);
		
		return($response);
	}
	
	
	
	/**
	 * remove items from data
	 */
	public function removeItemsFromData($data){
				
		$catID = UniteFunctionsUG::getVal($data, "catid");
		
		$itemIDs = UniteFunctionsUG::getVal($data, "arrItemIDs");
		
		$this->deleteItems($itemIDs);
		
		$response = $this->getCatsAndItemsHtml($catID);
		
		return($response);
	}
	
	
	/**
	 * update item title
	 */
	public function updateItemTitleFromData($data){
		
		$itemID = $data["itemID"];
		$title = $data["title"];
		
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		$this->update($itemID,$arrUpdate);
	}
	
	
	/**
	 * 
	 * duplicate items
	 */
	public function duplicateItemsFromData($data){
		
		$catID = UniteFunctionsUG::getVal($data, "catID");
		
		$arrIDs = UniteFunctionsUG::getVal($data, "arrIDs");
		
		$this->duplicateItems($arrIDs, $catID);
		
		$response = $this->getCatsAndItemsHtml($catID);
		
		return($response);
	}
	
	/**
	 * 
	 * save items order from data
	 */
	public function saveOrderFromData($data){
		$itemsIDs = UniteFunctionsUG::getVal($data, "items_order");
		if(empty($itemsIDs))
			return(false);
		
		$this->saveItemsOrder($itemsIDs);
	}

	
	/**
	 * 
	 * copy / move items to some category 
	 * @param $data
	 */
	public function copyMoveItemsFromData($data){
		
		$targetCatID = UniteFunctionsUG::getVal($data, "targetCatID");
		$selectedCatID = UniteFunctionsUG::getVal($data, "selectedCatID");
		
		$arrItemIDs = UniteFunctionsUG::getVal($data, "arrItemIDs");
		
		UniteFunctionsUG::validateNotEmpty($targetCatID,"category id");
		UniteFunctionsUG::validateNotEmpty($arrItemIDs,"item id's");
		
		$operation = UniteFunctionsUG::getVal($data, "operation");
		
		switch($operation){
			case "copy":
				$this->copyItems($arrItemIDs, $targetCatID);
			break;
			case "move":
				$this->moveItems($arrItemIDs, $targetCatID);
			break;
			default:
				UniteFunctionsUG::throwError("Wrong operation: $operation");
			break;
		}
		
		$repsonse = $this->getCatsAndItemsHtml($selectedCatID);
		return($repsonse);
	}
	
	
	/**
	 * 
	 * get item data html for edit item
	 */
	private function getItemSettingsHtml($objItem){
		
		$settingsItem = $objItem->getObjSettings();
		
		$output = new UniteSettingsProductUG();
		$output->init($settingsItem);
		$output->setShowDescAsTips(true);
		$output->setShowSaps(false);
		
		ob_start();
		$output->draw("form_item_settings", true);
		$html = ob_get_contents();
		ob_clean();
		
		$response = array();
		$response["htmlSettings"] = $html;
		
		return($response);
	}
	
	
	/**
	 * for image, get settings html
	 * for media get data object
	 */
	public function getItemData($data){
		
		$itemID = UniteFunctionsUG::getVal($data, "itemid");
		$objItem = new UniteGalleryItem();
		$objItem->initByID($itemID);
		$itemType = $objItem->getType();
		
		switch($itemType){
			case UniteGalleryItem::TYPE_IMAGE:
				$response = $this->getItemSettingsHtml($objItem);				
			break;
			default:
				$response = $objItem->getData();
			break;
		}
		
		return($response);
	}
	
	
	/**
	 * 
	 * update item data
	 * get html item for admin response
	 */
	public function updateItemData($data){
		$itemID = UniteFunctionsUG::getVal($data, "itemID");
				
		UniteFunctionsUG::validateNotEmpty($itemID, "item params");
		
		$item = new UniteGalleryItem();
		$item->initByID($itemID);
				
		$item->updateItemData($data);
		
		$htmlItem = $item->getHtmlForAdmin();
		
		$response = array("html_item"=>$htmlItem);
		
		return($response);
	}
	
	
}

?>