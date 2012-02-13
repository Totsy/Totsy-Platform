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
 * This Script Recapture all orders that has been shipped and that got Capture Errors
 * 
 */
class ReCapture extends \lithium\console\Command {
		
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
	 * File that contains orders id to be captured
	 *
	 * @var string
	 */
	public $ordersIdFile = "capture_errors.csv";
	
	/**
	 * Creating new auth during recapture process
	 *
	 * @var string
	 */
	public $createNewAuth = true;
	
	/**
	 * Creating only a reauth during recapture process
	 *
	 * @var string
	 */
	public $onlyReauth = false;

	/**
	 * Instances
	 */
	public function run() {
		error_reporting(E_ERROR | E_PARSE);
		Environment::set($this->env);
		Logger::debug('\n\n Recapture Script Starts');
		/**** GET CC INFOS FOR SPECIFIC ORDERDS ****/
		$ordersCollection = Order::Collection();
		#Setup Output File Headers
		$report = array();
		$report[0] = array('error_type', 'error_message', 'order_id','authKey','total');
		$reportCounter = 1;
		$orderIds = $this->parseOrderIdsFromCSV();
		if(!empty($orderIds)) {
			foreach($orderIds as $orderId) {
				Logger::debug('Processing Order Id : ' . $orderId);
				$conditions = array('order_id' => $orderId,
									'total' => array('$ne' => 0),
									'cancel' => array('$ne' => true),
									'payment_captured' => array('$exists' => false),
									'cancel' => array('$ne' => true),
									'isOnlyDigital' => array('$ne' => true)
									);
				$order = $ordersCollection->findOne($conditions);
				if(!empty($order)) {		
					if(!empty($order['cyberSourceProfileId']) && !empty($this->createNewAuth)) {
						$authKeyAndReport = $this->authorize($order);
						if(!empty($authKeyAndReport['reportAuthorize'])) {
							$report[$reportCounter] = $authKeyAndReport['reportAuthorize'];
							$reportCounter++;
						}
						Logger::debug('New Authorize Key : ' . $authKeyAndReport['authKey']);
					} else {
						if($order['authKey']) {
							$authKeyAndReport['authKey'] = $order['authKey'];
						}
					}
					if(!empty($authKeyAndReport['authKey']) && empty($this->onlyReauth)) {
						$reportCapture = $this->capture($authKeyAndReport['authKey'], $order);
						if(!empty($reportCapture)) {
							$report[$reportCounter] = $reportCapture;
							$reportCounter++;
						}
					}			
				} else {
					Logger::debug('Order Not Found or Already Captured : ' . $orderId);
				}
			}
		}
		$this->logReport($report);
	}
	
	public function parseOrderIdsFromCSV() {
		Logger::debug('Parse Order Ids From CSV');
		$orderIds = null;
		if (($handle = fopen(LITHIUM_APP_PATH . $this->source . $this->ordersIdFile, "r")) !== FALSE) {
		    while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$orderIds[] = $line[0];
		    }
		    fclose($handle);
		}
		return $orderIds;
	}

	public function authorize($order = null) {
		Logger::debug('Authorize');
		$ordersCollection = Order::Collection();
		$report = null;
		$authKey = null;
		$userInfos = User::lookup($order['user_id']);
		#Retrieve Profile using CyberSourceProfile ID		
		$cybersource = new CyberSource(Processor::config('default'));
		$profile = $cybersource->profile($order['cyberSourceProfileId']);
		#If Digital Items, Calculate correct Amount
		$amountToAuthorize = Order::getAmountNotCaptured($order);
		#Create a new Transaction and Get a new Authorization Key
		$auth = Processor::authorize('default', $amountToAuthorize, $profile, array('orderID' => $order['order_id']));
		if ($auth->success()) {
			Logger::debug('Authorize Complete: ' . $auth->key);
			$authKey = $auth->key;
			if(!empty($this->onlyReauth)) {
				$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('authKey' => $auth->key,
											  'auth' => $auth->export(),
											  'authTotal' => $amountToAuthorize,
											  'processor' => $auth->adapter
						)), array( 'upsert' => true)
				);
			}
		} else {
			#Record errors in DB
			$error = implode('; ', $auth->errors);
			Logger::debug('Authorize Error: ' . $error);
			$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('error_date' => new MongoDate(),
											  'auth_error' => $error
						)), array( 'upsert' => true)
			);
			#Include errors in Report
			$reportAuthorize[] = 'authorize_error';
			$reportAuthorize[] = $error;
			$reportAuthorize[] = $order['order_id'];
			$reportAuthorize[] = $order['authKey'];
			$reportAuthorize[] = $amountToAuthorize;
		}
		return compact('authKey', 'reportAuthorize');
	}
	
	public function capture($authKey = null, $order = null) {
		Logger::debug('Capture');
		$ordersCollection = Order::Collection();
		$report = null;
		#If Digital Items, Calculate correct Amount
		$amountToCapture = Order::getAmountNotCaptured($order);
		$auth_capture = Processor::capture(
				'default',
				$authKey,
				floor($amountToCapture * 100) / 100,
				array(
					'processor' => isset($order['processor']) ? $order['processor'] : null,
					'orderID' => $order['order_id']
				)
		);
		if ($auth_capture->success()) {
			Logger::debug('Capture Succeeded: ' . $auth_capture->key);
			$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('authKey' => $auth_capture->key,
											  'auth' => $auth_capture->export(),
											  'authTotal' => $amountToCapture,
											  'processor' => $auth_capture->adapter,
											  'payment_date' => new MongoDate(),
           									  'auth_confirmation' => $auth_capture->key,
           									  'payment_captured' => true
						)), array( 'upsert' => true)
			);
			#Save Capture in Transactions Logs
			$transation['authKey'] = $auth_capture->key;
			$transation['amount'] = $amountToCapture;
			$transation['date_captured'] = new MongoDate();
			$update = $ordersCollection->update(
				array('_id' => $order['_id']),
				array(
					'$push' => array(
					'capture_records' => $transation
					)
				)
			);
			#Unset Old Errors fields
			$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$unset' => array('error_date' => 1,
												'auth_error' => 1
			)));
			$report[] = 'capture_succeeded';
			$report[] = '';
			$report[] = $order['order_id'];
			$report[] = $authKey;
			$report[] = $amountToCapture;
			Logger::debug('Order Document Updated!');
		} else {
			#Record errors in DB
			$error = implode('; ', $auth_capture->errors);
			Logger::debug('Capture Error: ' .$error);
			$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('error_date' => new MongoDate(),
											  'auth_error' => $error,
											  'auth_confirmation' => -1
						)), array( 'upsert' => true)
			);
			#Include errors in Report
			$report[] = 'capture_error';
			$report[] = $error;
			$report[] = $order['order_id'];
			$report[] = $order['authKey'];
			$report[] = $amountToCapture;
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
		$myFile =  "Log_Recapture_" . $month .'_'. $day . "_" . $year . "_" . $now["0"] . ".csv";
		$myFilePath = LITHIUM_APP_PATH . $this->source . $myFile;
		$fh = fopen($myFilePath, 'wb');
		if(!empty($report)) {
			foreach ($report as $line) {
				fputcsv($fh, $line);
				$idx++;
			}
		}
		fclose($fh);
		Logger::debug('Finish Writing Report, ' . $idx . ' lines has been written');
	}
}