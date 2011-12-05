<?php

namespace admin\tests\mocks\models;

class OrderMock extends \admin\models\Order {

	protected static $_classes = array(
		'tax' => 'admin\tests\mocks\extensions\AvaTaxMock',
		'payments' => 'admin\tests\mocks\extensions\PaymentsMock'
	);
}

?>