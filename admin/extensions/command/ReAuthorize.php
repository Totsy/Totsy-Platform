<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;
use admin\models\Order;
use admin\models\User;
use admin\extensions\Mailer;
use li3_payments\payments\Processor;
use li3_payments\extensions\adapter\payment\CyberSource;
use MongoId;
use MongoDate;

/**
 * This Script Reauthorize all orders that has not been shipped and that got 7 days old AuthKey
 *
 */
class ReAuthorize extends \lithium\console\Command {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * Directory of files holding the files
	 *
	 * @var string
	 */
	public $source = '/resources/totsy/tmp/';

	/**
	 * Set How Old Can Be the Auth.Key to be replaced
	 *
	 * @var string
	 */
	public $expiration = 8;

	public $orders = array();

	public $fullAmount = false;

	public $reauthVisaMC = true;

	public $unitTest = false;
	/**
	 * Instances
	 */
	public function run() {
		#SetUp Environment
		error_reporting(E_ERROR | E_PARSE);
		Environment::set($this->env);
		Logger::debug('Starting Reauthorization Script');
		#Get Orders that has to be Reauthorize depending of their last auth date
		if(empty($this->orders)) {
			$orders = $this->getOrders();
		} else {
			$orders = $this->orders;
		}
		#Reauth depending of the Type of Transaction
		if(!empty($orders)) {
			$report = $this->manageReauth($orders);
		}
		#Send Email containing informations about the Reauth Process
		if(!$this->unitTest) {
			Logger::debug('Reports Processing...');
			$this->sendReports($report);
			$this->logReport($report);
		}
		#In Case Of Request by OrderExport, return orders to be processed
		if(!empty($this->fullAmount)) {
			$ordersToBeProcessed = $this->getOrdersToShipped($report);
			return $ordersToBeProcessed;
		}
	}

	public function getOrdersToShipped($report) {
		$ordersCollection = Order::Collection();
		$ordersUpdated = null;
		if($report['updated']) {
			foreach($report['updated'] as $value) {
				$order_ids[] = $value['order_id'];
			}
		}
		if($report['skipped']) {
			foreach($report['skipped'] as $value) {
				$order_ids[] = $value['order_id'];
			}
		}
		if($report['errors']) {
			foreach($report['errors'] as $value) {
				$order_ids_errors[] = $value['order_id'];
			}
		}
		if(!empty($order_ids_errors)) {
			$order_ids = array_diff($order_ids, $order_ids_errors);
		}
		if($order_ids) {
			$conditions = array('order_id' => array('$in' => $order_ids));
			$fields = array(
			    '_id' => true,
			    'billing' => true,
			    'shipping' => true,
			    'date_created' => true,
			    'ship_date' => true,
			    'items' => true,
			    'order_id' => true,
			    'shippingMethod' => true,
			    'user_id' => true
			);
			$ordersUpdated = $ordersCollection->find($conditions,$fields);
		}
		return $ordersUpdated;
	}

	public function getOrders() {
		Logger::debug('Getting Orders to be Reauth');
		$ordersCollection = Order::Collection();
		#Limit to X days Old Authkey
		$limitDate = mktime(23, 59, 59, date("m"), date("d") - $this->expiration, date("Y"));
		#Get All Orders with Auth Date >= 7days, Not Void Manually or Shipped
		$conditions = array('void_confirm' => array('$exists' => false),
							'auth_confirmation' => array('$exists' => false),
							'authKey' => array('$exists' => true),
							'date_created' => array('$lte' => new MongoDate($limitDate)),
							'auth' => array('$exists' => true),
							'cancel' => array('$ne' => true),
							'total' => array('$ne' => 0),
							'cyberSourceProfileId' => array('$exists' => true),
							'authTotal' => array('$exists' => true),
							'processor' => 'CyberSource',
							'isOnlyDigital' => array('$ne' => true)
		);
		if($this->unitTest) {
			$conditions['test'] = true;
		}
		if(!$this->reauthVisaMC) {
			$conditions['card_type'] = 'amex';
		}
		$orders = $ordersCollection->find($conditions);
		Logger::debug('End of Getting Orders to be Reauth');
		return $orders;
	}

	public function sendReports($report = null) {
		$reportToSend = $report;
		unset($reportToSend['skipped']);
		Logger::debug('Sending Report');
		if($this->fullAmount) {
			$template = 'Auth_Initial_Errors_CyberSource';
		} else {
			$template = 'ReAuth_Errors_CyberSource';
		}
		#If Errors Send Email to Customer Service
		if(!empty($reportToSend['updated']) || !empty($reportToSend['errors']) ) {
			if (Environment::is('production')) {
				Mailer::send($template,'authorization_errors@totsy.com', $reportToSend);
			}
			Mailer::send($template,'troyer@totsy.com', $reportToSend);
			Logger::debug('Report Sent!');
		}
	}

	public function manageReauth($orders = null, $limitDate = null) {
		Logger::debug('Managing Reauth Type');
		$report = array('updated' => null, 'errors' => null, 'skipped' => null);
		foreach ($orders as $order) {
			Logger::debug('Processing Order : ' . $order['order_id']);
			#Check If Reauth Needed
			$reAuth = $this->isReauth($order);
			if($reAuth) {
				$report = $this->reAuthCyberSource($order, $report);
			} else {
				Logger::debug('Order Skipped');
				$report['skipped'][] = array(
					'error_message' => 'skipped',
					'order_id' => $order['order_id'],
					'authKey' => $order['authKey'],
					'total' => $order['total']
				);
			}
		}
		Logger::debug('End Of Manage Reauth');
		return $report;
	}

	public function isReauth($order = null) {
		$reAuth = false;
		#Limit to X days Old Authkey
		$limitDate = mktime(23, 59, 59, date("m"), date("d") - $this->expiration, date("Y"));
		#Check If There were already ReAuthorization Records
		$lastDate = $order['date_created'];
		if(!empty($order['auth_records'])) {
			foreach($order['auth_records'] as $record) {
				if($lastDate->sec < $record['date_saved']->sec) {
					$lastDate = $record['date_saved'];
				}
			}
			if($lastDate->sec <= $limitDate) {
				$reAuth = true;
			}
		} else {
			$reAuth = true;
		}
		#Don't Reauthorize if the last Auth is an error
		if(!empty($order['error_date'])) {
			if($lastDate->sec < $order['error_date']->sec) {
				$reAuth = false;
			}	
		}
		#If The Order has been already full authorize and Order send to Dotcom. Don't reauth
		if(!empty($this->fullAmount)) {
			#Check The Amount to Authorize
			$amountToAuthorize = Order::getAmountNotCaptured($order);
			if($order['authTotal'] >= $amountToAuthorize) {
				$reAuth = false;
			}
		} else {
			if($order['card_type'] != 'amex' && $this->reauthVisaMC) {
				if(!empty($order['void_records'])) {
					$limitDate = mktime(23, 59, 59, date("m"), date("d") - 1, date("Y"));
					$lastDateVoid = $order['date_created'];
					foreach($order['void_records'] as $record) {
						if($lastDateVoid->sec < $record['date_saved']->sec) {
							$lastDateVoid = $record['date_saved'];
						}
					}
					if($lastDateVoid->sec >= $limitDate) {
						$reAuth = false;
					}
				} else {
					$reAuth = false;
				}
			}
		}
		if(!isset($order['cyberSourceProfileId'])) {
			$reAuth = false;
		}
		if(!empty($order['cancel'])) {
			$reAuth = false;
		}
		if(!empty($order['isOnlyDigital'])) {
			$reAuth = false;
		}
		if($reAuth) {
			Logger::debug('Eligible for Reauth');
		}
		return $reAuth;
	}
				
	/*** REAUTHORIZE WITH VISA/MASTERCARD / AMEX THROUGH CYBERSOURCE ***/
	public function reAuthCyberSource($order, $report = null) {
		Logger::debug('Reauthorizing Through CyberSource');
		$ordersCollection = Order::Collection();
		#Save Old AuthKey with Date
		$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
		#Cancel Previous Transaction
		if($order['card_type'] != 'amex' && (!empty($order['authTotal'])) && $this->fullAmount) {
			$auth = Processor::void('default', $order['auth'], array(
				'processor' => isset($order['processor']) ? $order['processor'] : null,
				'orderID' => $order['order_id']
			));
		}
		Logger::debug("Getting CyberSource Profile");
		$cybersource = new CyberSource(Processor::config('default'));
		$profile = $cybersource->profile($order['cyberSourceProfileId']);
		Logger::debug("Authorizing...");
		#If Digital Items, Calculate correct Amount
		$amountToAuthorize = Order::getAmountNotCaptured($order);
		$auth = Processor::authorize('default', $amountToAuthorize, $profile, array('orderID' => $order['order_id']));
		if($auth->success()) {
			Logger::debug("Authorization Succeeded");
			#Setup new AuthKey
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array(
					'$set' => array(
						'authKey' => $auth->key,
						'auth' => $auth->export(),
						'processor' => $auth->adapter,
						'authTotal' => $amountToAuthorize
					),
					'$push' => array(
						'auth_records' => $newRecord
					)), 
					array('upsert' => true)
			);
			$report['updated'][] = array(
				'error_message' => 'updated',
				'order_id' => $order['order_id'],
				'authKey' => $order['authKey'],
				'new_authKey' => $auth->key,
				'total' => $amountToAuthorize
			);
		} else {
			#Reverse Transaction that Failed
			Processor::void('default', $auth, array(
				'processor' => $auth->adapter,
				'orderID' => $order['order_id']
			));
			$message  = "Authorize failed for order id `{$order['order_id']}`:";
			$message .= $error = implode('; ', $auth->errors);
			Logger::debug($message);
			$datasToSet['error_date'] = new MongoDate();
			$datasToSet['auth_error'] = $error;
			if($this->fullAmount) {
				$datasToSet['processed'] = false;
			}
			#Record Errors in DB
			$update = $ordersCollection->update(
				array('_id' => $order['_id']),
				array('$set' => $datasToSet),
				array( 'upsert' => true)
			);
			$report['errors'][] = array(
				'error_message' => $message,
				'order_id' => $order['order_id'],
				'authKey' => $order['authKey'],
				'authKeyDeclined' => $auth->key,
				'total' => $order['authTotal']
			);
		}
		Logger::debug('End of Order Process: ' . $order['order_id']);
		return $report;
	}

	public function logReport($report = null) {
		Logger::debug('Writing Report : ');
		/**** RECORD DATAS IN FILE ****/
		$idx = 0;
		$now = getdate();
		$month = date("m",$now["0"]);
		$day = date("d",$now["0"]);
		$year = date("Y",$now["0"]);
		$myFile =  "Log_Reauthorize_" . $month .'_'. $day . "_" . $year . "_" . $now["0"] . ".csv";
		$myFilePath = LITHIUM_APP_PATH . $this->source . $myFile;
		$fh = fopen($myFilePath, 'wb');
		if(!empty($report)) {
			foreach ($report as $reporType) {
				if(!empty($reporType)) {
					foreach($reporType as $reportCase) {
						$line = null;
						foreach($reportCase as $value){
							$line[] = $value;
						}
						fputcsv($fh, $line);
						$idx++;
					}
				}
			}
		}
		fclose($fh);
		Logger::debug('Finish Writing Report, ' . $idx . ' lines has been written');
	}
}
