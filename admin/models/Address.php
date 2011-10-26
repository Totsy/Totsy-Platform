<?php

namespace admin\models;


class Address extends \lithium\data\Model {

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