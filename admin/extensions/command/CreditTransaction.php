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
 * This Script credits all orders that has been accidently captured
 * 
 */
class CreditTransaction extends \lithium\console\Command {
		
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
	public $ordersIdFile = "orders_id_to_credit.csv";
	
	
	public $unitTest = false;
	/**
	 * Instances
	 */
	public function run() {
		#SetUp Environment
		error_reporting(E_ERROR | E_PARSE);
		Environment::set($this->env);
		Logger::debug('Starting Credit Script');
		#Get Orders that has to be void depending of their last auth date
		$orderIds = $this->parseOrderIdsFromCSV();
		#Reauth depending of the Type of Transaction
		if(!empty($orderIds)) {
			$this->issueCreditForOrders($orderIds);
		}
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
	
	public function issueCreditForOrders($orderIds = null) {
		$ordersCollection = Order::Collection();
		$report = array();
		$idx = 0;
		foreach($orderIds as $orderId) {
			Logger::debug('Processing Order Id : ' . $orderId);
			$conditions = array('order_id' => $orderId,
								'total' => array('$gt' => 1)
			);
			$order = $ordersCollection->findOne($conditions);
			if(!empty($order)) {				
				$report[$idx] = $this->creditOrder($order, $report);
				$idx++;
			} else {
				Logger::debug('Order Not Found' . $orderId);
			}
		}
		$this->logReport($report);
	}
	
	public function creditOrder($order = null, $report) {
		Logger::debug('Credit');
		$ordersCollection = Order::Collection();
		$auth_credited = Processor::credit('default', $order['authKey'], $order['total']);
		if ($auth_credited->success()) {
			Logger::debug('Credit Succeeded: ' . $auth_credited->key);
			$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('credit_authKey' => $auth_credited->key,
           									  'credited' => true
						)), array( 'upsert' => true)
			);
			$report[] = 'credit_succeeded';
			$report[] = '';
			$report[] = $order['order_id'];
			$report[] = $auth_credited->key;
			$report[] = $order['total'];
			Logger::debug('Order Document Updated!');
		} else {
			#Record errors in DB
			$error = implode('; ', $auth_credited->errors);
			Logger::debug('Credit Error: ' .$error);
			$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('credit_auth_error_date' => new MongoDate(),
											  'credit_auth_error' => $error,
											  'credit_auth_error_key' => $auth_credited->key
						)), array( 'upsert' => true)
			);
			#Include errors in Report
			$report[] = 'credit_error';
			$report[] = $error;
			$report[] = $order['order_id'];
			$report[] = $auth_credited->key;
			$report[] = $order['total'];
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
		$myFile =  "Log_Credit_" . $month .'_'. $day . "_" . $year . "_" . $now["0"] . ".csv";
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
?>