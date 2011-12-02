<?php

namespace app\models;
use MongoDate;

class Base extends \lithium\data\Model {
	protected $_dates = array(
		'now' => 0,
		'-1min' => -60,
		'-3min' => -180,
		'-5min' => -300,
		'3min' => 180,
		'5min' => 300,
		'15min' => 900
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	public static function timeValue($value) {
		if (is_object($value)) {
			return $value->sec;
		} elseif (is_numeric($value)) {
			return $value;
		}
		return strtotime($value);
	}

	/**
	 * This method gives direct access to the MongoDB collection object from any
	 * model that extends the Base Model.
	 * @return object
	 */
	public static function collection() {
		return static::_connection()->connection->{static::_object()->_meta['source']};
	}

    public static function generateToken() {
        return substr(md5(uniqid(rand(),1)), 1, 10);
    }

    public static function randomString($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
        $chars_length = (strlen($chars) - 1);
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < $length; $i = strlen($string)) {
            $r = $chars{rand(0, $chars_length)};
            if ($r != $string{$i - 1}) $string .=  $r;
        }
        return $string;
    }
}

?>
