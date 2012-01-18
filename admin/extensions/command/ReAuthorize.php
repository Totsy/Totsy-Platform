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
		$ordersCollection->ensureIndex(array(
			'date_created' => 1,
			'cc_payment' => 1
		));
		#Limit to X days Old Authkey
		$limitDate = mktime(23, 59, 59, date("m"), date("d") - $this->expiration, date("Y"));
		#Get All Orders with Auth Date >= 7days, Not Void Manually or Shipped
		$conditions = array('void_confirm' => array('$exists' => false),
							'auth_confirmation' => array('$exists' => false),
							'authKey' => array('$exists' => true),
							'cc_payment' => array('$exists' => true),
							'date_created' => array('$lte' => new MongoDate($limitDate)),
							'auth' => array('$exists' => true)
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
		#If Errors Send Email to Customer Service
		if(!empty($reportToSend['updated']) || !empty($reportToSend['errors']) ) {
			if (Environment::is('production')) {
				Mailer::send('ReAuth_Errors_CyberSource','authorization_errors@totsy.com', $reportToSend);
			}
			Mailer::send('ReAuth_Errors_CyberSource','troyer@totsy.com', $reportToSend);
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
				if(empty($order['authTotal']) || !empty($this->fullAmount)) {
					$total = $order['total'];
				} else {
					$total = $order['authTotal'];
				}
				if($order['processor'] == 'CyberSource') {
					$report = $this->reAuthCyberSource($order, $report, $total);
				} else {
					$report = $this->reAuthAuthorizeNet($order, $report, $total);
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
		Logger::debug('End Of Manage Reauth');
		return $report;
	}

	public function isReauth($order = null) {
		$reAuth = false;
		#Limit to X days Old Authkey
		$limitDate = mktime(23, 59, 59, date("m"), date("d") - $this->expiration, date("Y"));
		#Check If There were already ReAuthorization Records
		if(!empty($order['auth_records'])) {
			$lastDate = $order['date_created'];
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
		#If The Order has been already full authorize and Order send to Dotcom. Don't reauth
		if(!empty($this->fullAmount)) {
			if((!isset($order['authTotal'])) || ($order['authTotal'] >= $order['total'])) {
				$reAuth = false;
			}
		} else {
			#SPECIAL CONDITION - DONT REAUTHORIZE VISA/MC Transaction
			if($order['card_type'] != 'amex' && !$this->reauthVisaMC) {
				$reAuth = false;
			}
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
			if(isset($order['authTotal']) && $order['authTotal'] != $order['total']) {
				$reAuth = false;
			}
		}
		Logger::debug('Eligible for Reauth: ' . $reAuth);
		return $reAuth;
	}

	/*** REAUTHORIZE WITH VISA/MASTERCARD THROUGH AUTHORIZE.NET ***/
	public function reAuthAuthorizeNet($order = null, $report = null, $total) {
		Logger::debug('Reauthorizing Through Auth.net');
		$ordersCollection = Order::Collection();
		$usersCollection = User::Collection();
		#Decrypt Credit Card Infos
		if(!empty($order['cc_payment'])) {
			$creditCard = Order::getCCinfos($order);
			#If Credit Card Has Been well Decrypted
			if(!empty($creditCard)) {
				#Save Old AuthKey with Date
				$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
				#Cancel Previous Transaction
				// change 'authorizenet' to 'default' below when ready to reauth older Amex Authorize.net orders through CyberSource
/*
				$auth = Processor::void('authorizenet', $order['authKey'], array(
					'processor' => isset($order['processor']) ? $order['processor'] : null
				));
				if ($auth->success()) {
*/
					$userInfos = User::lookup($order['user_id']);
					#Create Card and Check Billing Infos
					$card = Processor::create('authorizenet', 'creditCard', $creditCard + array(
						'billing' => Processor::create('authorizenet', 'address', array(
							'firstName' => $order['billing']['firstname'],
							'lastName'  => $order['billing']['lastname'],
							'address'   => trim($order['billing']['address']),
							'city'      => $order['billing']['city'],
							'state'     => $order['billing']['state'],
							'zip'       => $order['billing']['zip'],
							'country'   => $order['billing']['country'] ?: 'US',
							'email'     => $userInfos['email']

					))));
					#Create a new Transaction and Get a new Authorization Key
					// change 'authorizenet' to 'default' below when ready to reauth older Amex Authorize.net orders through CyberSource
					$auth = Processor::authorize('default', $total, $card);
					if ($auth->success()) {
						Logger::debug("Authorization Succeeded");
						$customer = Processor::create('default', 'customer', array(
							'firstName' => $userInfos['firstname'],
							'lastName' => $userInfos['lastname'],
							'email' => $userInfos['email'],
							'payment' => $card
						));
						$result = $customer->save();
						$profileID = $result->response->paySubscriptionCreateReply->subscriptionID;
						$update = $usersCollection->update(
							array('_id' => new MongoId($user['_id'])),
							array('$push' => array('cyberSourceProfiles' => $profileID)), array( 'upsert' => true)
						);
						#Setup new AuthKey
						$update = $ordersCollection->update(
							array('_id' => $order['_id']),
							array('$set' => array(
								'cyberSourceProfileId' => $profileID
							)), array( 'upsert' => true)
						);
						#Setup new AuthKey
						$update = $ordersCollection->update(
								array('_id' => $order['_id']),
								array('$set' => array(
									'authKey' => $auth->key,
									'auth' => $auth->export(),
									'processor' => $auth->adapter,
									'authTotal' => $total
								)), array( 'upsert' => true)
						);
						#Add to Auth Records Array
						$update = $ordersCollection->update(
								array('_id' => $order['_id']),
								array('$push' => array('auth_records' => $newRecord)), array( 'upsert' => true)
						);
						$report['updated'][] = array(
							'error_message' => 'updated',
							'order_id' => $order['order_id'],
							'authKey' => $order['authKey'],
							'new_authKey' => $auth->key,
							'total' => $total
						);
					} else {
						$message  = "Authorize failed for order id `{$order['order_id']}`:";
						$message .= $error = implode('; ', $auth->errors);
						$update = $ordersCollection->update(
													array('_id' => $order['_id']),
													array('$set' => array('error_date' => new MongoDate(),
																			'auth_error' => $error
													)), array( 'upsert' => true)
						);
						$report['errors'][] = array(
							'error_message' => $message,
							'order_id' => $order['order_id'],
							'authKey' => $order['authKey'],
							'authKey_declined' => $auth->key,
							'total' => $total
						);
					}
/*
				} else {
					$message  = "Void failed for order id `{$order['order_id']}`:";
					$message .= $error = implode('; ', $auth->errors);
					$report['errors'][] = array(
							'error_message' => $message,
							'order_id' => $order['order_id'],
							'authKeyDeclined' => $order['authKey'],
							'total' => $total
					);
				}
*/
			}
		}
		return $report;
	}

	/*** REAUTHORIZE WITH VISA/MASTERCARD / AMEX THROUGH CYBERSOURCE ***/
	public function reAuthCyberSource($order, $report = null, $total) {
		Logger::debug('Reauthorizing Through CyberSource');
		$ordersCollection = Order::Collection();
		#Save Old AuthKey with Date
		$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
		#Cancel Previous Transaction
		if($order['card_type'] != 'amex' && (!empty($order['authTotal']))) {
			$auth = Processor::void('default', $order['auth'], array(
				'processor' => isset($order['processor']) ? $order['processor'] : null
			));
			if(!$auth->success()) {
				Logger::debug("Void failed for order id " . $order['order_id']);
				$message  = "Void failed for order id `{$order['order_id']}`:";
				$message .= $error = implode('; ', $auth->errors);
				$report['errors'][] = array(
						'error_message' => $message,
						'order_id' => $order['order_id'],
						'authKey' => $order['authKey'],
						'total' => $order['authTotal']
				);
			}
		}
		if (!empty($order['cyberSourceProfileId'])) {
			Logger::debug("Getting CyberSource Profile");
			$cybersource = new CyberSource(Processor::config('default'));
			$profile = $cybersource->profile($order['cyberSourceProfileId']);
			$auth = Processor::authorize('default', $total, $profile);
		} else {
			Logger::debug("Getting Credit Card Informations");
			$userInfos = User::lookup($order['user_id']);
			$creditCard = Order::getCCinfos($order);
			#Create Card and Check Billing Infos
			$card = Processor::create('default', 'creditCard', $creditCard + array(
				'billing' => Processor::create('default', 'address', array(
					'firstName' => $order['billing']['firstname'],
					'lastName'  => $order['billing']['lastname'],
					'address'   => trim($order['billing']['address']),
					'city'      => $order['billing']['city'],
					'state'     => $order['billing']['state'],
					'zip'       => $order['billing']['zip'],
					'country'   => $order['billing']['country'] ?: 'US',
					'email'     => $userInfos['email']

			))));
			#Create a new Transaction and Get a new Authorization Key
			$auth = Processor::authorize('default', $total, $card);
		}
		if($auth->success()) {
			Logger::debug("Authorization Succeeded");
			#Setup new AuthKey
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$set' => array(
						'authKey' => $auth->key,
						'auth' => $auth->export(),
						'processor' => $auth->adapter,
						'authTotal' => $total
					)), array( 'upsert' => true)
			);
			#Add to Auth Records Array
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$push' => array('auth_records' => $newRecord)), array( 'upsert' => true)
			);
			$report['updated'][] = array(
				'error_message' => 'updated',
				'order_id' => $order['order_id'],
				'authKey' => $order['authKey'],
				'new_authKey' => $auth->key,
				'total' => $total
			);
		} else {
			$message  = "Authorize failed for order id `{$order['order_id']}`:";
			$message .= $error = implode('; ', $auth->errors);
			Logger::debug($message);
			$update = $ordersCollection->update(
				array('_id' => $order['_id']),
				array('$set' => array('error_date' => new MongoDate(),
					'auth_error' => $error
				)), array( 'upsert' => true)
			);
			if($this->fullAmount) {
				$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array('processed' => false
					)), array( 'upsert' => true)
				);
			}
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