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
class VoidTransaction extends \lithium\console\Command {

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
	public $expirationVoid = 7;

	public $expirationAuth = 8;

	public $voidVisaMC = true;

	public $unitTest = false;
	/**
	 * Instances
	 */
	public function run() {
		#SetUp Environment
		error_reporting(E_ERROR | E_PARSE);
		Environment::set($this->env);
		Logger::debug('Starting Void Script');
		#Get Orders that has to be void depending of their last auth date
		$orders = $this->getOrders();
		#Reauth depending of the Type of Transaction
		if(!empty($orders)) {
			$report = $this->manageVoid($orders);
		}
		#Send Email containing informations about the Reauth Process
		if(!$this->unitTest) {
			$this->sendReports($report);
			$this->logReport($report);
		}
	}

	public function getOrders() {
		Logger::debug('Getting Orders to be Reauth');
		$ordersCollection = Order::Collection();
		$ordersCollection->ensureIndex(array(
			'date_created' => 1
		));
		#Limit to X days Old Authkey
		$limitDate = mktime(23, 59, 59, date("m"), date("d") - $this->expirationVoid, date("Y"));
		#Get All Orders with Auth Date >= 7days, Not Void Manually or Shipped
		$conditions = array('void_confirm' => array('$exists' => false),
							'auth_confirmation' => array('$exists' => false),
							'authKey' => array('$exists' => true),
							'date_created' => array('$lte' => new MongoDate($limitDate)),
							'auth' => array('$exists' => true),
							'cancel' => array('$ne' => true),
							'total' => array('$ne' => 0),
							'authTotal' => array('$exists' => true),
							'isOnlyDigital' => array('$ne' => true)
		);
		if($this->unitTest) {
			$conditions['test'] = true;
		}
		if($this->voidVisaMC) {
			$conditions['card_type'] =  array('$ne' => 'amex');
		}
		$orders = $ordersCollection->find($conditions);
		Logger::debug('End of Getting Orders to be Void');
		return $orders;
	}

	public function sendReports($report = null) {
		Logger::debug('Sending Report');
		#If Errors Send Email to Customer Service
		if(!empty($report['updated']) || !empty($report['errors']) ) {
			if (Environment::is('production')) {
				Mailer::send('Void_Errors_CyberSource','authorization_errors@totsy.com', $report);
			}
			Mailer::send('Void_Errors_CyberSource','troyer@totsy.com', $report);
		}
	}

	public function manageVoid($orders = null, $limitDate = null) {
		Logger::debug('Managing Void Type');
		$report = array('updated' => null, 'errors' => null, 'skipped' => null);
		foreach ($orders as $order) {
			Logger::debug('Processing Order : ' . $order['order_id']);
			#Check If Reauth Needed
			$toVoid = $this->isVoid($order);
			if($toVoid) {
				if($order['processor'] == 'CyberSource') {
					$report = $this->voidTransaction($order, $report);
				}
			} else {
				$report['skipped'][] = array(
					'error_message' => 'skipped',
					'order_id' => $order['order_id'],
					'authKey' => $order['authKey'],
					'total' => $order['total']
				);
			}
		}
		return $report;
	}

	public function isVoid($order = null) {
		$toVoid = false;
		#Limit to X days Old Authkey
		$limitDate = mktime(23, 59, 59, date("m"), date("d") - $this->expirationAuth, date("Y"));
		$limitDateVoid = mktime(23, 59, 59, date("m"), date("d") - $this->expirationVoid, date("Y"));
		#Check If There were already ReAuthorization Records
		$lastDate = $order['date_created'];
		if(!empty($order['auth_records'])) {			
			foreach($order['auth_records'] as $record) {
				if($lastDate->sec < $record['date_saved']->sec) {
					$lastDate = $record['date_saved'];
				}
			}
			if($lastDate->sec <= $limitDate) {
				$toVoid = true;
			}
		} else {
			$toVoid = true;
		}
		#Don't Void if the last Auth is an error
		if(!empty($order['error_date'])) {
			if($lastDate->sec < $order['error_date']->sec) {
				$toVoid = false;
			}	
		}
		if(!empty($order['void_records'])) {
			$lastDateVoid = $order['date_created'];
			foreach($order['void_records'] as $record) {
				if($lastDateVoid->sec < $record['date_saved']->sec) {
					$lastDateVoid = $record['date_saved'];
				}
			}
			if($lastDateVoid->sec >= $limitDateVoid) {
				$toVoid = false;
			}
		}
		
		#Check The Amount to Authorize
		$amountToAuthorize = Order::getAmountNotCaptured($order);
		if($order['authTotal'] != $amountToAuthorize) {
			$toVoid = false;
		}
		Logger::debug('Eligible for Void: ' . $toVoid);
		return $toVoid;
	}

	/*** VOID TRANSACTION WITH VISA/MASTERCARD THROUGH CYBERSOURCE ***/
	public function voidTransaction($order, $report = null) {
		Logger::debug('Void Through CyberSource');
		$ordersCollection = Order::Collection();
		#Save Old AuthKey with Date
		$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
		#Cancel Previous Transaction
		$auth = Processor::void('default', $order['auth'], array(
			'processor' => isset($order['processor']) ? $order['processor'] : null,
			'orderID' => $order['order_id']
		));
		if(!$auth->success()) {
			Logger::debug("Void failed for order id " . $order['order_id']);
			$message  = "Void failed for order id `{$order['order_id']}`:";
			$message .= $error = implode('; ', $auth->errors);
			$datasToSet['error_void_date'] = new MongoDate();
			$datasToSet['error_void_message'] = $error;
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
		} else {
			Logger::debug("Void Succeeded");
			#Setup new AuthKey
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$set' => array(
						'authKey' => $auth->key,
						'auth' => $auth->export(),
						'processor' => $auth->adapter
					)), array( 'upsert' => true)
			);
			#Add to Auth Records Array
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$push' => array('void_records' => $newRecord)), array( 'upsert' => true)
			);
			$report['updated'][] = array(
				'error_message' => 'voided',
				'order_id' => $order['order_id'],
				'authKey' => $order['authKey'],
				'new_authKey' => $auth->key,
				'total' => $order['total']
			);
		}
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
		$myFile =  "Log_Void_" . $month .'_'. $day . "_" . $year . "_" . $now["0"] . ".csv";
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
