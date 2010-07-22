<?php

namespace app\models;

use lithium\util\String;

class Address extends \lithium\data\Model {
	
	protected $_meta = array('title' => 'description');

	protected static function _findFilters() {
		return array('list' => function($self, $params, $chain) {
			$result = array();
			$meta = $self::meta();
			$name = $meta['key'];
			$format = '{:description} ({:address} {:city}, {:state})';

			foreach ($chain->next($self, $params, $chain) as $entity) {
				$key = $entity->{$name};
				$result[(string) $key] = String::insert($format, $entity->data());
			}
			return $result;
		}) + parent::_findFilters();
	}

	public static function menu($user, $type) {
		if (is_array($user) && isset($user['_id'])) {
			$user = $user['_id'];
		}
		return static::find('list', array(
			'conditions' => array('user_id' => $user) + compact('type'),
			'order' => array('default' => 'desc')
		));
	}

	/**
	 * Counts the number of Addresses based on search criteria
	 * Usage:
	 * $conditions = array('user_id' => Session::read('_id'))
	 */
	public static function count($conditions = array()) {
		$collection = Address::_connection()->connection->totsy->addresses;
		return $collection->count($conditions);
	}

	public static function changeDefault($user) {
		// I don't know what this is supposed to do, but there's a call to it in the Addresses
		// controller.
	}
}

?>