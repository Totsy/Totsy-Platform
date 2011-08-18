<?php

namespace admin\extensions\adapter\security\auth;

class Token extends \lithium\core\Object {

	public function __construct(array $config = array()) {
		$defaults = array(
			'check' => function($token) {
				return false;
			}
		);
		parent::__construct($config + $defaults);
	}

	public function check($request, array $options = array()) {
		if (!$request->token) {
			return false;
		}
		$token = $request->token;

		return $this->_config['check']($token);
	}

	public function set($data, array $options = array()) {
		return $data;
	}

	public function clear(array $options = array()) {}
}

?>