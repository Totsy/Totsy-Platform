<?php

namespace admin\models;

class Banner extends Base {

	public $validates = array(
		'name' => array(
			array('notEmpty', 'required' => true, 'message' => 'Please add a banner name'),

		)
	);
	
	public static function collection() {
		return static::_connection()->connection->banners;
	}
}

?>