<?php

namespace app\models;

use lithium\util\String;

class Address extends \lithium\data\Model {

	public $validates = array(
		'description' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a description'
		),
		'firstname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a first name'
		),
		'lastname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a last name'
		),
		'address' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add an address'
		),
		'telephone' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a telephone number'
		),
		'city' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a city'
		),
		'state' => array(
			'state', 'required' => true, 'message' => 'Please select a state or province'
		),
		'zip' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a zip code'
		)
	);

	public static $states = array(
		'United States' => array(
			"AL" => 'Alabama',
			"AK" => 'Alaska',
			"AZ" => 'Arizona',
			"AR" => 'Arkansas',
			"CA" => 'California',
			"CO" => 'Colorado',
			"CT" => 'Connecticut',
			"DE" => 'Delaware',
			"DC" => 'District of Columbia',
			"FL" => 'Florida',
			"GA" => 'Georgia',
			"GU" => 'Guam ',
			"HI" => 'Hawaii',
			"ID" => 'Idaho',
			"IL" => 'Illinois',
			"IN" => 'Indiana',
			"IA" => 'Iowa',
			"KS" => 'Kansas',
			"KY" => 'Kentucky',
			"LA" => 'Louisiana',
			"ME" => 'Maine',
			"MD" => 'Maryland',
			"MA" => 'Massachusetts',
			"MI" => 'Michigan',
			"MN" => 'Minnesota',
			"MS" => 'Mississippi',
			"MO" => 'Missouri',
			"MT" => 'Montana',
			"NE" => 'Nebraska',
			"NV" => 'Nevada',
			"NH" => 'New Hampshire',
			"NJ" => 'New Jersey',
			"NM" => 'New Mexico',
			"NY" => 'New York',
			"NC" => 'North Carolina',
			"ND" => 'North Dakota',
			"OH" => 'Ohio',
			"OK" => 'Oklahoma',
			"OR" => 'Oregon',
			"PA" => 'Pennyslvania',
			"PR" => 'Puerto Rico',
			"RI" => 'Rhode Island',
			"SC" => 'South Carolina',
			"SD" => 'South Dakota',
			"TN" => 'Tennessee',
			"TX" => 'Texas',
			"UT" => 'Utah',
			"VT" => 'Vermont',
			"VA" => 'Virginia',
			"VI" => 'Virgin Islands',
			"WA" => 'Washington',
			"WV" => 'West Virginia',
			"WI" => 'Wisconsin',
			"WY" => 'Wyoming'
		)
		// 'Canada' => array(
		// 	"AB" => 'Alberta',
		// 	"BC" => 'British Columbia',
		// 	"MB" => 'Manitoba',
		// 	"NB" => 'New Brunswick',
		// 	"NL" => 'Newfoundland and Labrador',
		// 	"NT" => 'Northwest Territories',
		// 	"NS" => 'Nova Scotia',
		// 	"NU" => 'Nunavut',
		// 	"PE" => 'Prince Edward Island',
		// 	"SK" => 'Saskatchewan',
		// 	"ON" => 'Ontario',
		// 	"QC" => 'Quebec',
		// 	"YT" => 'Yukon'
		// )
	);

	protected $_meta = array('title' => 'description');

	public static function __init() {
		parent::__init();
		$validator = static::$_classes['validator'];
		$validator::add('state', '[A-Z]{2}', array('contains' => false));
	}

	protected static function _findFilters() {
		return array('list' => function($self, $params, $chain) {
			$result = array();
			$meta = $self::meta();
			$name = $meta['key'];
			$format = '{:description} ({:address} {:city}, {:state})';

			foreach ($chain->next($self, $params, $chain) as $entity) {
				$key = $entity->{$name};
				$result[(string) $key] = String::insert($format, $entity->data());
			}
			return $result;
		}) + parent::_findFilters();
	}

	public static function menu($user) {
		if (is_array($user) && isset($user['_id'])) {
			$user = (string) $user['_id'];
		}
		return static::find('list', array(
			'conditions' => array('user_id' => $user),
			'order' => array('default' => 'desc')
		));
	}

	/**
	 * Counts the number of Addresses based on search criteria
	 * Usage:
	 * $conditions = array('user_id' => Session::read('_id'))
	 */
	public static function count($conditions = array()) {
		$collection = Address::_connection()->connection->addresses;
		return $collection->count($conditions);
	}

	public static function changeDefault($user) {
		// I don't know what this is supposed to do, but there's a call to it in the Addresses
		// controller.
	}
}

?>