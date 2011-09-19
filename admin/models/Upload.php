<?php

namespace admin\models;

use admin\models\Event;
use admin\models\User;
use admin\models\Item;
use admin\models\Upload;
use lithium\storage\Session;
use MongoDate;
use MongoId;
use Mongo;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;

class Upload extends \lithium\data\Model {


	/**
	 * This method parses the item file that is uploaded in the Events Edit View.
	 *
	 * @todo Move this method to the Items controller and make it a static method.
	 * @todo Add event to the header information for spreadsheet (event - this needs to replace vendor)
	 * @todo Add vendor_description
	 */
	public static function parseItems($_FILES, $_id, $enableFinalSale = 1, $enabled = false) {
		$items = array();
		$itemIds = array();
		$relatedItems = array();
		$rowToItemIdMap = "";
		$errors = array();

		// Default column headers from csv file
		$standardHeader = array(
			'vendor',
			'vendor_style',
			'age',
			'departments',
			'category',
			'sub_category',
			'description',
			'color',
			'total_quantity',
			'msrp',
			'sale_retail',
			'percent_off',
			'orig_whol',
			'sale_whol',
			'imu',
			'product_weight',
			'product_dimensions',
			'shipping_weight',
			'shipping_dimensions',
			'related_items'
		);

		
		if ($_FILES['upload_file']['error'] == 0) {
			$file = $_FILES['upload_file']['tmp_name'];
			$objReader = PHPExcel_IOFactory::createReaderForFile("$file");
			$objPHPExcel = $objReader->load("$file");
			
			//arrays for datatypes to check uniqueness
			$check_vendor = array();
			$check_vendor_style = array();
			$check_age = array();
			$check_category = array();
			$check_subcategory = array();
			$check_description = array();
			$check_color = array();
			$check_nosize = array();
			
			
			//arrays of header names to check stuff
			$check_required = array("vendor", "vendor_style", "category", "sub-category", "description", "color", "quantity");
			$check_badchars = array("vendor", "vendor_style", "age", "category", "sub-category", "description", "color", "no size");
			$check_decimals = array("msrp", "sale_retail", "percentage_off", "orig_wholesale", "sale_wholesale", "imu");
			$check_departments = array("Girls", "Boys", "Momsdads");

			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				for ($row = 1; $row <= $highestRow; ++ $row ) {
					for ($col = 0; $col < $highestColumnIndex; ++ $col) {
						$cell = $worksheet->getCellByColumnAndRow($col, $row);
						$val = $cell->getCalculatedValue();
						if ($row == 1) {
							$heading[] = $val;
						} else {
							if (isset($heading[$col])) {
							
								//checking formulas in each row, checks to see if = is first char
								if(substr($val, 0, 1)=="="){
									$errors[] = "$heading[$col] has a formula in row #$row";
								}
							
								//check required fields
								if (in_array($heading[$col], $check_required)) {
									if (empty($val)) {
										$errors[] = "$heading[$col] (required) is blank for row #$row";
									}
								}
								
								//check for bad chars				
								if (in_array($heading[$col], $check_badchars)) {
							
									if (!empty($val)) {
										if(strpos($val, "&")){
											$errors[] = "$heading[$col] has an illegal character in row #$row";
										}
										if(strpos($val, "!")){
											$errors[] = "$heading[$col] has an illegal character in row #$row";
										}
									}
								}
							
								if(($heading[$col] === "department_1") ||
									($heading[$col] === "department_2") ||
									($heading[$col] === "department_3")) {
									if (!empty($val)) {
										$eventItems[$row - 1]['departments'][] = ucfirst(strtolower(trim($val)));
										$eventItems[$row - 1]['departments'] = array_unique($eventItems[$row - 1]['departments']);

										if (!in_array($val, $check_departments)) {
											$errors[] = "$heading[$col] is incorrect in row #$row";
										}

									}
									
								} else if (($heading[$col] === "related_1") ||
										($heading[$col] === "related_2") ||
										($heading[$col] === "related_3") ||
										($heading[$col] === "related_4") ||
										($heading[$col] === "related_5")) {
										if (!empty($val)) {
											$eventItems[$row - 1]['related_items'][] = trim($val);
											$eventItems[$row - 1]['related_items'] = array_unique($eventItems[$row - 1]['related_items']);
										}

								//check if vendor style is unique
								} else if ($heading[$col] === "vendor_style"){
									if (empty($val)) {
										$errors[] = "$heading[$col] is blank for row #$row";
									}
									if(in_array(trim($val), $check_vendor_style)){
										//check if color/description is unique
									
									
										$errors[] = "$heading[$col] is a duplicate in row #$row";
									}else{
										$check_vendor_style[] = trim($val);
									}
								
								//check decimals here
								} elseif (in_array($heading[$col], $check_decimals)) {
									if (!empty($val)) {
										if(is_numeric($val)){
											$val = number_format($val, 2, '.', '');
										}
									}

								} else {
									if (!empty($val)) {
										$eventItems[$row - 1][$heading[$col]] = $val;
									}
								}
						}
					}
				}
			}
		}
		//$errors[] = "you dont even know what youre doing!!!";
		if(count($errors)>0){
			$error_output="<h3>Spreadsheet Errors:</h3>";
			foreach($errors as $thiserror){
				$error_output .= $thiserror . "<br>";
			}
			return $error_output;
		}else{


				foreach ($eventItems as $itemDetail) {
					$i=0;
					$itemAttributes = array_diff_key($itemDetail, array_flip($standardHeader));
					
          			//check if final sale radio box was checked or not
          			if($enableFinalsale==0){
          			  $blurb = "<p><strong>Final Sale</strong></p>";
          			}
          			//if not make blurb var blank for good form
          			else{
          			  $blurb = "";
          			}
					$itemCleanAttributes = null;
					foreach ($itemAttributes as $key => $value) {
						unset($itemDetail[$key]);

						if($key!=="color_description_style") {
							$itemCleanAttributes[trim($key)] = $value;
						}
					}
					$item = Item::create();
					$date = new MongoDate();
					//$url = $this->cleanUrl($itemDetail['description']." ".$itemDetail['color']);

					$details = array(
						'enabled' => (bool) $enabled,
						'created_date' => $date,
						'details' => $itemCleanAttributes,
						'event' => array((string) $_id),
						'url' => $url,
						'blurb' => $blurb,
						'taxable' => true
					);

					$newItem = array_merge(Item::castData($itemDetail), Item::castData($details));
					$newItem['vendor_style'] = (string) $newItem['vendor_style'];
					
					if ((array_sum($newItem['details']) > 0) && $item->save($newItem)) {
						$items[] = (string) $item->_id;

						//related items will be added later, after ihe items in this event actually HAVE unique ID's
						//each related item will momentarily be a string made of the color, description and style separated by pipes
						if( !empty($itemDetail['related_items']) ) {

							$k=0;

							foreach( $itemDetail['related_items'] as $key=>$value ) {
								//build array of related items using color, description and style
								//the color and the description are for the buyer to see, but we use the style number
								//here to persist the related items
								//and later update each item using it and the event hash to query and get the id
								$fields = explode("|", $value);

								$related_items[(string) $item->_id][$k]['vendor_style'] = $fields[2];
								$related_items[(string) $item->_id][$k]['event'] = (string) $_id;

								$k++;
							}
						}
					}
					$i++;
				}

				$itemsCollection = Item::Collection();

				foreach ( $related_items as $key => $value ) {

					$rel_items = array();

					//aggregate related item id's
					for ($i=0; $i<count($related_items[$key]); $i++) {

						$style = trim($related_items[$key][$i]['vendor_style']);
						$event = trim($related_items[$key][$i]['event']);

						//query for this item
						$rel_item = Item::find('first', array('conditions' => array(
									'event' => array($event),
									'vendor_style' => $style
								)));

						$rel_items[] = (string) $rel_item['_id'];

					}

					$itemsCollection->update(array("_id" => new MongoId($key)), array('$set' => array('related_items' => $rel_items)));
				}

			}

			return $items;
		}
	}









}
?>