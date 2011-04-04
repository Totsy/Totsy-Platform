<?php

namespace app\models;

class Banner extends Base {

	public static function collection() {
		return static::_connection()->connection->banners;
	}
}

?>