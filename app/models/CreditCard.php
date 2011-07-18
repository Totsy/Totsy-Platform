<?php

namespace app\models;

class CreditCard extends \lithium\data\Model {

	public $validates = array(
		'description' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a credit card number'
		),
		'firstname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a first name'
		),
		'lastname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a last name'
		),
		'address' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add an address'
		),
		'city' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a city'
		),
		'state' => array(
			'state', 'required' => true, 'message' => 'Please select a state or province'
		),
		'zip' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a zip code'
		)
	);
}