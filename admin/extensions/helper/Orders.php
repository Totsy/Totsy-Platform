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
		'Error Date' => 'error_date',
		'Error Message' => 'auth_error',
		'Order Total' => 'total'
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
			    if ($payments) {
			        $heading = array_intersect($this->_standardHeading, array_keys($payments->getNext()));
			    } else {
			        $heading = $this->_standardHeading;
			    }
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
			if ($options['type'] == 'expired'){
				$html .=  "<th><input type='checkbox' id='capture_all'> Capture</th>";
			}
			$html .= '</tr></thead><tbody>';
			foreach ($payments as $payment) {
				if ($options['type'] == 'expired') {
					$payment['expire_date'] = new MongoDate(mktime(0,0,0,
						date('m',$payment['date_created']->sec),
						date('d',$payment['date_created']->sec) + 30,
						date('Y',$payment['date_created']->sec)
						));
				}
				$details = array_intersect_key($payment, array_flip(array_values($heading)));
				$orderedDetails = $this->sortArrayByArray($details, array_values($heading));
				$link = array_merge(array('Orders::view'), array('args' => $payment['_id']));
				$html .= "<tr id=$payment[_id]>";
				foreach ($orderedDetails as $key => $value) {
					if($key == 'order_id') {
						$value = $this->_context->html->link($value, $link, array('escape' => false));
					}
					if ($key == 'error_date' || $key == 'payment_date' || $key == 'date_created' || $key == 'expire_date') {

						$value = date('M-d-Y', $value->sec);
					}
					$html .= "<td>$value</td>";
				}
				if ($options['type'] == 'expired'){
						$html .=  "<td><input name='capture[]' type='checkbox' value=$payment[order_id] class='capture'/></td>";
					}
				$html .= '</tr>';
			}
			$html .= '</tbody>';
		}
		return $html;
	}
	public function sortArrayByArray($array,$orderArray) {
		$ordered = array();
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