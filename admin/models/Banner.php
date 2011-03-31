<?php

namespace admin\models;

class Banner extends Base {

	public $validates = array();

	public static function collection() {
		return static::_connection()->connection->banners;
	}
}

?>