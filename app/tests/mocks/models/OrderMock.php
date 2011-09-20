<?php

namespace app\tests\mocks\models;

class OrderMock extends \app\models\Order {

	protected static $_classes = array(
		'tax' => 'app\tests\mocks\extensions\AvaTaxMock'
	);
}

?>