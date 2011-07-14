<?php

namespace app\models;

class CreditCard extends \lithium\data\Model {

	public $validates = array(
		'number' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a credit card number'
		),
		'name' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add the name of the credit card owner'
		),
		'year' => array(
			'notEmpty', 'required' => true, 'message' => 'Please select the expiration year'
		),
		'month' => array(
			'notEmpty', 'required' => true, 'message' => 'Please select the expiration month'
		),
		'code' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add the security code'
		)
	);
}