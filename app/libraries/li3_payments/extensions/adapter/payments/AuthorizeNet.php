<?php

namespace li3_payments\extensions\adapter\payments;

use MongoDate;
use SimpleXMLElement;
use app\models\AuthAttempt;
use lithium\util\Inflector;
use lithium\data\Collection;
use lithium\storage\Session;
use li3_payments\extensions\PaymentObject;
use li3_payments\extensions\payments\ECheck;
use li3_payments\extensions\payments\Profile;
use li3_payments\extensions\payments\Customer;
use li3_payments\extensions\payments\CreditCard;
use li3_payments\extensions\payments\Transaction;
use li3_payments\extensions\payments\exceptions\NetworkException;
use li3_payments\extensions\payments\exceptions\TransactionException;
// use li3_payments\payment\interfaces\UnlinkedInterface;
// use li3_payments\payment\interfaces\VisaVerifiedInterface;

class AuthorizeNet extends \lithium\core\Object {
	// implements VisaVerifiedInterface, UnlinkedInterface {

	protected $_transactionTypes = array(
		Transaction::TYPE_VOID    => 'VOID',
		Transaction::TYPE_AUTH    => 'AUTH_ONLY',
		Transaction::TYPE_CAPTURE => 'PRIOR_AUTH_CAPTURE',
		Transaction::TYPE_CREDIT  => 'CREDIT',
		Transaction::TYPE_AUTH_CAPTURE => 'AUTH_CAPTURE',
	);

	protected $_checkTypes = array(
		ECheck::TYPE_PERSONAL => 'checking',
		ECheck::TYPE_SAVINGS => 'savings',
		ECheck::TYPE_BUSINESS => 'businessChecking'
	);

	/**
	 * The list of gateway addresses, organized by environment (`'live'` or `'test'`), and
	 * transaction type.
	 *
	 * @var array
	 */
	protected $_gateways = array(
		'live' => array(
			'default' => 'https://secure.authorize.net:443/gateway/transact.dll',
			'profile' => 'https://api.authorize.net:443/xml/v1/request.api'
		),
		'test' => array(
			'default' => 'https://test.authorize.net:443/gateway/transact.dll',
			'profile' => 'https://apitest.authorize.net:443/xml/v1/request.api'
		),
		'schema' => array(
			'profile' => 'AnetApi/xml/v1/schema/AnetApiSchema.xsd'
		)
	);

	protected $_classes = array(
		'view' => 'lithium\template\View',
		'service' => 'lithium\net\http\Service',
		'response' => 'lithium\net\http\Response',
		'payments' => 'li3_payments\extensions\Payments'
	);

	protected $_connection = null;

	public function __construct(array $config = array()) {
		$defaults = array(
			'key' => null,
			'test' => false,
			'login' => null,
			'debug' => false,
			'gateway' => 'live',
			'version' => '3.1',
			'delimiter' => '|',
			'duplicateTimeLimit' => 300,
			'connection' => array(),
		);
		parent::__construct($config + $defaults);
	}

	public function process($amount, PaymentObject $payment, array $options = array()) {
		switch (true) {
			case ($payment instanceof Customer):
				$data = array('customer' => $payment, 'type' => 'profileTransAuthCapture');
				$request = $this->_renderCommand('process_profile', $data + compact('amount'));
				$parsed = explode(',', (string) $this->_send('profile', $request)->directResponse);
				return $parsed[6];
			break;
			case ($payment instanceof CreditCard):
				$type = Transaction::TYPE_AUTH_CAPTURE;
				$data = $this->_serialize($type, compact('amount', 'payment') + $options);
				$response = $this->_sendAim('default', $data);

				if (intval($response['Response Code']) == 1) {
					return $response['Transaction ID'];
				}
				throw new TransactionException($response['Response Reason Text']);
			break;
		}
		// $transaction = new Transaction(compact('amount') + array('payment' => $pmt) + $options);
	}

	public function authorize($amount, PaymentObject $payment, array $options = array()) {
		switch (true) {
			case ($payment instanceof Customer):
				$data = array('customer' => $payment, 'type' => 'profileTransAuthOnly');
				$request = $this->_renderCommand('process_profile', $data + compact('amount'));
				$result = $this->_send('profile', $request);
			break;
			case ($payment instanceof CreditCard):
				$user = Session::read('userLogin');
				
				$type = Transaction::TYPE_AUTH;
				$data = $this->_serialize($type, 
					compact('payment', 'amount') + 
					$options + 
					array('cust_id'=>$user['_id']));
				
				$auth_attempt = AuthAttempt::create();
				$auth_attempt->customer_ip = $user['lastip'];
				$auth_attempt->cust_id = $user['_id'];
				$auth_attempt->date_created = new MongoDate();
				$auth_attempt->card_number = substr($payment->number, -4);
				$auth_attempt->amount = $amount;
				$auth_attempt->save();
				
				$response = $this->_sendAim('default', $data);

				$auth_attempt->transaction_id = $response['Transaction ID'];
				$auth_attempt->response_code = $response['Response Code'];
				$auth_attempt->response_subcode = $response['Response Subcode'];
				$auth_attempt->response_reason_code = $response['Response Reason Code'];
				$auth_attempt->response_reason_text = $response['Response Reason Text'];
				$auth_attempt->ccv_response_code = $response['Card Code (CVV2/CVC2/CID) Response Code'];
				$auth_attempt->cavv_response_code = $response['Cardholder Authentication Verification Value (CAVV) Response Code'];
				$auth_attempt->save();
				if (intval($response['Response Code']) == 1) {
					return $response['Transaction ID'];
				}
				throw new TransactionException($response['Response Reason Text']);
			break;
		}
		return $result;
	}

	public function capture($transaction, $amount, array $options = array()) {
		$type = Transaction::TYPE_CAPTURE;
		$data = $this->_serialize($type, compact('transaction', 'amount') + $options);
		$response = $this->_sendAim('default', $data);

		if (intval($response['Response Code']) == 1) {
			return $response['Transaction ID'];
		}
		throw new TransactionException($response['Response Reason Text']);
	}

	public function credit($transaction, $amount = null, array $options = array()) {
	}

	public function void($transaction, array $options = array()) {
	}

	/**
	 * Creates a customer profile / payment profile / address.
	 *
	 * @param object $object
	 * @param array $options 
	 * @return void
	 */
	public function profile($profile, array $options = array()) {
		if (is_string($profile)) {
			return $this->_read($profile, $options);
		}
		if ($profile->key) {
			return $this->update($profile, $options);
		}
		$request = $this->_renderCommand('create_customer_profile', array('customer' => $profile));
		$response = $this->_send('profile', $request);
		$profile->key = (string) $response->result->customerProfileId;
		return true;
	}

	/**
	 * Delete customer profile / payment profile / address.
	 *
	 * @param mixed $object
	 * @param array $options
	 * @return void
	 */
	public function delete($object, array $options = array()) {
		$id = is_object($object) ? $object->key : $object;
		$request = $this->_renderCommand('delete_customer_profile', compact('id'));
		$result = $this->_send('profile', $request);
		$code = (string) $result->messages->message->code;
		return ($code === 'I00001');
	}

	public function read($query = null, array $options = array()) {
		$payments = $this->_classes['payments'];

		if ($query == null) {
			$request = $this->_renderCommand('get_customer_profiles');
			$result = $this->_send('profile', $request);
			$ids = array();

			foreach ($result->ids->numericString as $id) {
				$ids[] = (string) $id;
			}
			return $ids;
		}

		if (is_numeric($query)) {
			$request = $this->_renderCommand('get_customer_profile', array('id' => $query));
			$response = $this->_send('profile', $request);
			return $this->_customer($response);
		}
		// getCustomerPaymentProfileRequest - Single payment profile
		// getCustomerShippingAddressRequest - Single shipping address
	}

	public function update($object, $data, array $options = array()) {
		// updateCustomerProfileRequest
		// updateCustomerPaymentProfileRequest
		// updateCustomerShippingAddressRequest
	}

	public function _serialize($type, array $data) {
		$params = $data;
		$data = array(
			'login'            => $this->_config['login'],
			'tran_key'         => $this->_config['key'],
			'version'          => $this->_config['version'],
			'delim_data'       => 'TRUE',
			'test_request'     => $this->_config['debug'] ? 'TRUE' : 'FALSE',
			'type'             => $this->_transactionTypes[$type],
			'delim_char'       => $this->_config['delimiter'],
			'duplicate_window' => $this->_config['duplicateTimeLimit'],
			'relay_response'   => 'FALSE',
		);
		if (isset($params['payment'])) {
			$payment = $params['payment'];
			$data += array(
				'card_code'        => $payment->code,
				'exp_date'         => "{$payment->month}-{$payment->year}",
				'card_num'         => $payment->number
			);
			if (isset($payment->billing) && is_object($payment->billing)) {
				$billing = array(
					'firstName', 'lastName', 'address', 'city', 'state', 'zip', 'phone', 'company'
				);
				foreach ($billing as $key) {
					if (isset($payment->billing->{$key})) {
						$data[Inflector::underscore($key)] = $payment->billing->{$key};
					}
				}
			}
			unset($data['payment']);
		}
		$data['customer_ip'] = $_SERVER[REMOTE_ADDR];
		$data['cust_id'] = $params['cust_id'];
		foreach (array('amount' => 'amount', 'transaction' => 'trans_id') as $source => $dest) {
			if (isset($params[$source])) {
				$data[$dest] = $params[$source];
			}
		}
		$result = '';

		foreach($data as $key => $value) {
			if ($value === null) {
				continue;
			}
			$result .= 'x_' . $key . '=' . $value . '&';
		}
		return rtrim($result, "& ");
	}

	/**
	 * Creates a customer object from an XML API response.
	 *
	 * @param object $response Accepts a `SimpleXML` object representing a response from the
	 *               Authorize.net CIM API.
	 * @return object Returns an instance of `Customer`, populated with the information returned
	 *         from the API.
	 */
	protected function _customer($response) {
		$payments = $this->_classes['payments'];

		$customer = $payments::create($this, 'customer', array(
			'id' => (string) ($response->profile->merchantCustomerId ?: ''),
			'key' => (string) $response->profile->customerProfileId ?: '',
			'email' => (string) ($response->profile->email ?: ''),
			'payment' => $this->_payment($response->profile->paymentProfiles),
			'billing' => $payments::create($this, 'address', $this->_cast(
				$response->profile->paymentProfiles->billTo
			))
		));
		return $customer;
	}

	/**
	 * Creates a `PaymentObject` from the contents of a `paymentProfiles` response container.
	 *
	 * @param object $response 
	 * @return object
	 */
	protected function _payment($response) {
		$payments = $this->_classes['payments'];
		$key = (string) $response->customerPaymentProfileId;
		$list = get_object_vars($response->payment);
		$type = key($list);

		if ($type == 'creditCard') {
			$exp = str_split((string) $list[$type]->expirationDate);
			$data = array('month' => intval($exp[0]), 'year' => intval($exp[1]) + 2000);
			$data += array('number' => (string) $list[$type]->cardNumber);
		}
		return $payments::create($this, $type, compact('key') + $data);
	}

	/**
	 * Casts a `SimpleXML` object to an array.
	 *
	 * @param object $xml
	 * @return array
	 */
	protected function _cast($xml) {
		$convert = function($value) use (&$convert) {
			$value = is_object($value) ? get_object_vars($value) : $value;

			if (!is_array($value)) {
				return $value;
			}
			foreach ($value as $key => $val) {
				$value[$key] = $convert($val);
			}
			return $value;
		};
		return $convert($xml);
	}

	protected function _renderCommand($template, array $data = array()) {
		$data += array('config' => $this->_config, 'gateways' => $this->_gateways);
		return $this->_view()->render('template', $data, compact('template'));
	}

	protected function _send($service, $content) {
		$gateway = parse_url($this->_gateways[$this->_config['gateway']][$service]);
		$path = $gateway['path'];
		unset($gateway['path']);

		if (!$this->_connection) {
			$config = $gateway + $this->_config['connection'];
			$this->_connection = $this->_instance('service', $config);
		}
		$headers = array('Content-type' => 'text/xml');
		return $this->_response($this->_connection->post($path, $content, compact('headers')));
	}

	protected function _sendAim($service, $content) {
		$gateway = $this->_gateways[$this->_config['gateway']][$service];

		$ch = curl_init($gateway);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		$response = urldecode(curl_exec($ch));

		if (curl_errno($ch)) {
			throw new TransactionException(curl_error($ch));
		}
		curl_close($ch);

		$values = explode($this->_config['delimiter'], $response);
		$keys = array(
			"Response Code", "Response Subcode", "Response Reason Code", "Response Reason Text",
			"Approval Code", "AVS Result Code", "Transaction ID", "Invoice Number", "Description",
			"Amount", "Method", "Transaction Type", "Customer ID", "Cardholder First Name",
			"Cardholder Last Name", "Company", "Billing Address", "City", "State",
			"Zip", "Country", "Phone", "Fax", "Email", "Ship to First Name", "Ship to Last Name",
			"Ship to Company", "Ship to Address", "Ship to City", "Ship to State",
			"Ship to Zip", "Ship to Country", "Tax Amount", "Duty Amount", "Freight Amount",
			"Tax Exempt Flag", "PO Number", "MD5 Hash", "Card Code (CVV2/CVC2/CID) Response Code",
			"Cardholder Authentication Verification Value (CAVV) Response Code"
		);
		return array_combine($keys, array_slice($values, 0, count($keys)));
	}

	protected function _response($message) {
		if (!preg_match('/\?xml version/', $message)) {
			$error = 'There was a connection error, or the service returned an invalid response.';
			throw new TransactionException($error);
		}
		$message = $this->_instance('response', array('body' => $message))->body();
		$message = str_replace("xmlns=\"{$this->_gateways['schema']['profile']}\"", '', $message);
		$response = new SimpleXMLElement($message);
		$msg = isset($response->messages) ? $response->messages : null;

		if (!isset($msg->resultCode) || isset($msg->resultCode) && $msg->resultCode == 'Error') {
			$error = $msg->message->text;
			$code = intval(preg_replace('/[^0-9]/', '', $msg->message->code));
			throw new TransactionException($error, $code);
		}
		return $response;
	}

	protected function _view() {
		$template = __DIR__ . '/authorize_net/templates/{:template}.xml.php';
		return $this->_instance('view', array('paths' => compact('template')));
	}

	protected function _instance($class, array $options = array()) {
		if (!isset($this->_classes[$class])) {
			return null;
		}
		return parent::_instance($class, $options);
	}
}

?>