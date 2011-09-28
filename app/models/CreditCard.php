<?php

namespace app\models;

class CreditCard extends \lithium\data\Model {

	protected $_meta = array('source' => 'credit_cards');

	public $validates = array(
		'number' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a credit card number'
		),
		'year' => array(
			'notEmpty', 'required' => true, 'message' => 'Please select the expiration year'
		),
		'month' => array(
			'notEmpty', 'required' => true, 'message' => 'Please select the expiration month'
		),
		'code' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add the security code'
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


	public static function __init() {
		parent::__init();
		$validator = static::$_classes['validator'];
		$validator::add('state', '[A-Z]{2}', array('contains' => false));
	}
	

}