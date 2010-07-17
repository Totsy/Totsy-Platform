<?php

namespace app\models;


class Address extends \lithium\data\Model {
	

	protected $_meta = array('title' => 'description');

	protected static function _findFilters() {
		return array('list' => function($self, $params, $chain) {
			$result = array();
			$meta = $self::meta();
			$name = $meta['key'];

			foreach ($chain->next($self, $params, $chain) as $entity) {
				$key = $entity->{$name};
				$result[is_scalar($key) ? $key : (string) $key] = $entity->{$meta['title']};
			}
			return $result;
		}) + parent::_findFilters();
	}

	/**
	 * Counts the number of Addresses based on search criteria
	 * Usage:
	 * $conditions = array('user_id' => Session::read('_id'))
	 */
	public function count($conditions = array()) {
		$collection = Address::_connection()->connection->totsy->addresses;
		return $collection->count($conditions);
	}
	
}

?>