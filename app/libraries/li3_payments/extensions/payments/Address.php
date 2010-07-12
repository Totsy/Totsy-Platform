<?php

namespace li3_payments\extensions\payments;

class Address extends \lithium\core\Object {

	public $firstName;

	public $lastName;

	public $company;

	public $address;

	public $city;

	public $state;

	public $zip;

	public $country;

	public $phone;

	public $fax;

	protected function _init() {
		parent::_init();
		$valid = array_keys(get_object_vars($this));

		foreach ($this->_config as $key => $value) {
			if ($value && in_array($key, $valid)) {
				$this->{$key} = $value;
			}
		}
	}
}

?>