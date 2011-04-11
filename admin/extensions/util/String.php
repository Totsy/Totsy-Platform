<?php

namespace admin\extensions\util;

/**
 * Extending string manipulation class with some regularly used cleanup methods.
 *
 */
class String extends \lithium\util\String {

	public static function asciiClean($description) {
		return preg_replace('/[^(\x20-\x7F)]*/','', $description);
	}
}