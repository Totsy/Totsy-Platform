<?php
namespace admin\extensions\helper;
use \MongoDate;

class Orders extends \lithium\template\Helper {

	protected $_errorHeading = array(
		'Order ID' => 'order_id',
		'Order Date' => 'date_created',
		'Error Date' => 'error_date',
		'AuthKey' => 'authKey',
		'Error Message' => 'auth_error',
		'Order Total' => 'total'
	);

	protected $_successHeading = array(
		'Order ID' => 'order_id',
		'Order Date' => 'date_created',
		'Auth Confirm Key' => 'auth_confirmation',
		'Payment Date' => 'payment_date',
		'Order Total' => 'total'
	);
	protected $_overdueHeading = array(
		'Order ID' => 'order_id',
		'Order Date' => 'date_created',
		'AuthKey' => 'authKey',
		'Expiration Date' => 'expire_date',
		'Order Total' => 'total'
	);
	protected $_standardHeading = array(
		'Order ID' => 'order_id',
		'Order Date' => 'date_created',
		'AuthKey' => 'authKey',
		'Auth Confirm Key' => 'auth_confirmation',
		'Payment Date' => 'payment_date',
		'Order Total' => 'total',
		'Error Date' => 'error_date',
		'Error Message' => 'auth_error'

	);

	public function build($payments = null, $options = array('type' => null, 'search' => null)){
		switch ($options['type']) {
			case 'error':
				$heading = $this->_errorHeading;
				break;
			case 'processed':
			    $heading = $this->_successHeading;
				break;
			case 'expired':
			    $heading = $this->_overdueHeading;
				break;
			default:
			    $heading = $this->_standardHeading;
				break;
		}
		if (!empty($payments)) {

			$html = '';
			$html .= '<table id="paymentTable" class="datatable" border="1">';
			$html .=  '<thead>';
			$html .= '<tr>';
			foreach ($heading as $key => $value){
				$html .=  "<th>$key</th>";
			}
			$html .= '</tr></thead><tbody>';
			foreach ($payments as $payment) {
				$details = array_intersect_key($payment, array_flip(array_values($heading)));
				var_dump();
				$orderedDetails = $this->sortArrayByArray($details, array_values($heading));
				$link = array_merge($action, array('args' => $event['_id']));
				$html .= "<tr id=$payment[_id]>";
				foreach ($orderedDetails as $key => $value) {
					if ($key == 'error_date' || $key == 'payment_date' || $key == 'date_created') {

						$value = date('M-d-Y', $value->sec);
					}
					$html .= "<td>$value</td>";
				}
				$html .= '</tr>';
			}
			$html .= '</tbody>';
		}
		return $html;
	}
	public function sortArrayByArray($array,$orderArray) {
		$ordered = array();
		var_dump($orderArray);
		var_dump($array);
		foreach($orderArray as $key => $value) {
			if(array_key_exists($value, $array)) {
				$ordered[$value] = $array[$value];
				unset($array[$value]);
			}
		}
	    return $ordered + $array;
	}
}

?>