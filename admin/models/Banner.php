<?php

namespace admin\models;
use \lithium\util\Validator;

class Banner extends Base {

	public $validates = array(
		'name' => array(
			array('notEmpty', 'required' => true, 'message' => 'Please add a banner name'),

		),
		'end_date' => array(
			array('notEmptyArray', 'required' => true, 'message' => 'Please enter an end date'),

		),
		'img' => array(
			array('notEmptyArray', 'required' => true, 'message' => 'Please upload an image'),

		)
	);

	public static function collection() {
		return static::_connection()->connection->banners;
	}

    public static function __init(array $options = array()) {
		parent::__init($options);

		Validator::add('notEmptyArray', function ($value) {
			return (empty($value)) ? false : true;
		});
	}
}

?>