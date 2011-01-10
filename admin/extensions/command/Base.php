<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;


/**
 * Li3 Import Command to load Order tracking information.
 *
 * 
 */
class Base extends \lithium\console\Command {

	public function sortArrayByArray($array, $orderArray) {
	    $ordered = array();
	    foreach($orderArray as $key) {
	        if(array_key_exists($key,$array)) {
	                $ordered[$key] = $array[$key];
	                unset($array[$key]);
	        }
	    }
	    return $ordered + $array;
	}
}