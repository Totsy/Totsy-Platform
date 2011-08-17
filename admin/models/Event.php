<?php

namespace admin\models;
use MongoId;
use MongoDate;
use admin\models\Item;
use admin\extensions\util\String;
use admin\models\EventImage;

class Event extends \lithium\data\Model {

	public $validates = array();

	public static $tags = array(
		'holiday' => 'holiday',
		'special' => 'special',
		'toys' => 'toys'
	);

	/**
	 * Query for all the events within the next 24 hours.
	 *
	 * @return Object
	 */
	public static function open($params = null, array $options = array()) {
		$fields = $params['fields'];
		return Event::all(compact('fields') + array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array('$lte' => new MongoDate()),
				'end_date' => array('$gt' => new MongoDate())
			),
			'order' => array('start_date' => 'DESC')
		));
	}

	public static function collection() {
		return static::_connection()->connection->events;
	}

	protected $_booleans = array(
		'enabled',
		'viewlive',
		'clearance',
		'tangible'
		);

	public static function castData($event, array $options = array()) {

		foreach ($event as $key => $value) {
			if (in_array($key, static::_object()->_booleans)) {
				$event[$key] = (boolean) $value;
			}
		}
		return $event;
	}

	public static function removeItems($event) {
		return static::collection()->update(
			array('_id' => new MongoId($event)),
			array('$unset' => array('items' => true)
		));
	}

	/**
	 * Query for all events that are enabled and have a start date
	 * that is greater than "now".
	 *
	 * @return Object
	 */
	public static function pending() {
		return Event::all(array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array(
					'$gt' => new MongoDate())),
			'order' => array('start_date' => 'ASC')
		));
	}

	public static function poNumber($event) {
		$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr(String::asciiClean($event->name), 0, 3));
		$time = date('ymdis', $event->_id->getTimestamp());
		return 'TOT'.'-'.$vendorName.$time;
	}

	public static function makecell($content, $error = false) {
		if($error){
			return "<div class=xls_cell_error>$content</div>";
		}
		else{
			return "<div class=xls_cell>$content</div>";
		}
	}

	/**
	 * Takes copy/pasted XLS content and converts to multi-dimensional array
	 * @var returns array
	 */
	public static function convert_spreadsheet($val){
		$fullarray = array();

		$rows = explode("\n", $val);

		foreach($rows as $thisrows){
			$fields = explode("\t", $thisrows);

			if(($fields[0]=="")&&($fields[1]=="")&&($fields[2]=="")&&($fields[3]=="")&&($fields[4]=="")){
			}
			else{
				$fullarray[] = $fields;
			}
		}

		return $fullarray;
	}

	public static function convert_smart_quotes($string){
	    $search = array(chr(145),
	                    chr(146),
	                    chr(147),
	                    chr(148),
	                    chr(151));

	    $replace = array('"',
	    				 '"',
	    				 "'",
	                     "'",
	                     '"',
	                     '"',
	                     '-');

	    return str_replace($search, $replace, $string);
	}


	public static function check_spreadsheet($array){

		//arrays for datatypes to check uniqueness
		$output = "";
		$check_vendor_style = array();

		//arrays of header names to check stuff
		$check_required = array("vendor", "vendor_style", "description", "quantity", "department_1");
		$check_badchars = array("vendor", "vendor_style", "age", "category", "sub-category", "color", "no size");
		$check_decimals = array("msrp", "sale_retail", "percentage_off", "percent_off", "orig_wholesale", "orig_whol", "sale_whol", "sale_wholesale", "imu");
		$check_departments = array("Girls", "Boys", "Momsdads");
		$check_dept = array("department_1", "department_2", "department_3");
		$check_related = array("related_1", "related_2", "related_3", "related_4", "related_5");
		$highestRow = $array[0];
		$totalrows = count($array);
		$totalcols = count($highestRow);

		for ($row = 0; $row <= $totalrows-1; ++ $row ) {
			$output .= Event::makecell($row+1);
			for ($col = 0; $col < $totalcols; ++ $col) {
				$val = $array[$row][$col];
				$val = trim($val);
				$thiserror = "";

				if ($row == 0) {
					$heading[] = $val;
					if(strpos($val, "1/2")){
						$thiserror = "header error - $heading[$col] is illegal - has a half";
					}
				}
				else {
					if (isset($heading[$col])) {
						if ((in_array($heading[$col], $check_required))&&(empty($val))) {
							$thiserror = "$heading[$col] (required) is blank for row #$row";
						}
						else{
							if ((in_array($heading[$col], $check_badchars))&&(!empty($val))) {
								if((strpos($val, "&"))&&(!strpos($val, "\&"))){
									$thiserror = "$heading[$col] has an illegal character in row #$row";
								}
								if((strpos($val, "!"))&&(!strpos($val, "\!"))){
									$thiserror = "$heading[$col] has an illegal character in row #$row";
								}
								if((strpos($val, "'"))&&(!strpos($val, "\'"))){
									$thiserror = "$heading[$col] has an illegal character in row #$row";
								}
							}
							if ($heading[$col] == "vendor_style"){
								if(!strpos($val, "-")){
									if(in_array($val, $check_vendor_style)){
										$thiserror = "$heading[$col] is a duplicate in row #$row";
									}
									else{
										$check_vendor_style[] = $val;
									}
								}
								else{
									$thiserror = "$heading[$col] has a - in row #$row";
								}
							}
						}
					}
				}

				if($thiserror){
					$errors[] = $thiserror;
					$output .= Event::makecell($val, true);
				}
				else{
					$output .= Event::makecell($val);
				}
			}
			$output .= "<div style=\"clear:both;\"></div>";
		}

		//$errors[] = "you dont even know what youre doing!!!";
		if(count($errors)>0){
			$error_output="<h3>Spreadsheet Errors:</h3>";
			foreach($errors as $thiserror){
				$error_output .= $thiserror . "<br>";
			}
		}

		if($error_output){
			$output .= "<div>$error_output</div>";

			$output .= "<div style=\"clear:both;\"></div>";



			return "<div class=xls_holder_inner>$output</div>";
		}
		else{
			return "success";
		}

	}

	public static function getItems($eventId = null) {
		$items = null;
		if ($eventId) {
			$items = Item::find('all', array(
				'conditions' => array(
					'event' => array('$in' => array($eventId)
			))));
			$items = $items->data();
		}
		return $items;
	}
	/* Handling of attached images. */

	public function attachImage($entity, $name, $id) {
		$id = (string) $id;
		$type = EventImage::$types[$name];
		$images = $entity->images ? $entity->images->data() : array();
		$images[$type['field']] = $id;

		$entity->images = $images;
		return $entity;
	}

	public function detachImage($entity, $name, $id) {
		$id = (string) $id;
		$type = EventImage::$types[$name];

		$images = $entity->images->data();
		$images[$type['field']] = null;

		$entity->images = $images;

		return $entity;
	}

	public function images($entity) {
		$results = array();
		foreach (EventImage::$types as $name => $type) {
			$results[$name] = $type['multiple'] ? array() : null;

			if (!isset($entity->images[$type['field']])) {
				continue;
			}
			if ($type['multiple']) {
				foreach ($entity->images[$type['field']] as $key => $value) {
					$results[$name][$key] = EventImage::first(array(
						'conditions' => array('_id' => $value)
					));
				}
			} else {
				$results[$name] = EventImage::first(array(
					'conditions' => array('_id' => $entity->images[$type['field']])
				));
			}
		}
		return $results;
	}

	public function uploadNames($entity) {
		$results = array();

		foreach (EventImage::$types as $name => $type) {
			$results['form'][$name] = String::insert($type['uploadName']['form'], array(
				'url' => $entity->url,
				'name' => $name
			));
			$results['dav'][$name] = String::insert($type['uploadName']['dav'], array(
				'event' => $entity->url,
				'name' => $name,
				'file' => 'example',
				'month' => date('n', $entity->start_date->sec),
				'year' => date('Y', $entity->start_date->sec)
			));
		}
		return $results;
	}
}


?>
