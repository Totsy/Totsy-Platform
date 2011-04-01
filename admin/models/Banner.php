<?php

namespace admin\models;

class Banner extends Base {

	public $validates = array(
		'name' => array(
			array('notEmpty', 'required' => true, 'message' => 'Please add a banner name'),

		),
		'end_date' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add an end date for the banner'
		)
	);

	public static function collection() {
		return static::_connection()->connection->banners;
	}
}

?>