<?php

namespace app\extensions\helper;

class DataFormat extends \lithium\template\Helper {
	public static function timeValue($value) {
		if (is_object($value)) {
			return $value->sec;
		} elseif (is_numeric($value)) {
			return $value;
		}
		return strtotime($value);
	}
}

?>
