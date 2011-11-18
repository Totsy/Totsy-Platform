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
	 * Set How Old Can Be the Auth.Key to be replaced
	 *
	 * @var string
	 */
	public $expiration = 8;
	
	public $orders = array();
	
	public $fullAmount = false;
	
	/**
	 * Instances
	 */
	public function run() {
		#SetUp Environment
		error_reporting(E_ERROR | E_PARSE);
		Environment::set($this->env);
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
		$this->sendReports($report);		
	}
	
	public function getOrders() {
		$ordersCollection = Order::Collection();
		$ordersCollection->ensureIndex(array(
			'date_created' => 1,
			'cc_payment' => 1
		));
		#Limit to X days Old Authkey
		$limitDate = mktime(0, 0, 0, date("m"), date("d") - $this->expiration, date("Y"));
		#Get All Orders with Auth Date >= 7days, Not Void Manually or Shipped
		$conditions = array('void_confirm' => array('$exists' => false),
							'auth_confirmation' => array('$exists' => false),
							'authKey' => array('$exists' => true),
							'card_type' => 'amex',
							'cc_payment' => array('$exists' => true),
							'date_created' => array('$lte' => new MongoDate($limitDate))
		);
		$orders = $ordersCollection->find($conditions);
		return $orders;
	}
	
	public function sendReports($report = null) {
		#If Errors Send Email to Customer Service
		if(!empty($report)) {
			//Mailer::send('ReAuth_Errors','searnest@totsy.com', $report);
			//Mailer::send('ReAuth_Errors','mruiz@totsy.com', $report);
			//Mailer::send('ReAuth_Errors','gene@totsy.com', $report);
			Mailer::send('ReAuth_Errors_Test','troyer@totsy.com', $report);
			var_dump($report);
		}
		echo 'ReAuth Script Runned, ' . count($report['updated']) . ' Orders Updated ' . count($report['errors']) . ' Errors Found.';
	}
	
	public function manageReauth($orders = null, $limitDate = null) {
		$report = array('updated', 'errors');
		foreach ($orders as $order) {
			#Check If Reauth Needed
			$reAuth = $this->isReauth($order);
			if($reAuth) {
				if(empty($order['authTotal']) || !empty($this->fullAmount)) {
					$total = $order['total'];
				} else {
					$total = $order['authTotal'];
				}
				if($order['processor'] == 'CyberSource') {
					echo 'CS';
					$report = $this->reAuthCyberSource($order, $report, $total);
				} else {
					echo 'Auth';
					$report = $this->reAuthAuthorizeNet($order, $report, $total);
				}
			}
		}
		return $report;
	}
	
	public function isReauth($order = null) {
		$reAuth = false;
		#Limit to X days Old Authkey
		$limitDate = mktime(0, 0, 0, date("m"), date("d") - $this->expiration, date("Y"));
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
			if((!isset($order['authTotal'])) || ($order['authTotal'] == $order['total'])) {
				$reAuth = false;
			}
		} else {
			#SPECIAL CONDITION - DONT REAUTHORIZE VISA/MC Transaction
			if($order['card_type'] != 'amex') {
				$reAuth = false;
			}
		}
		return $reAuth;
	}

	/*** REAUTHORIZE WITH VISA/MASTERCARD THROUGH AUTHORIZE.NET ***/
	public function reAuthAuthorizeNet($order = null, $report = null, $total) {
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
				$auth = Processor::void('default', $order['authKey'], array(
					'processor' => isset($order['processor']) ? $order['processor'] : null
				));
				if ($auth->success()) {
					$userInfos = $usersCollection->findOne(array('_id' => new MongoId($order['user_id'])));
					#Create Card and Check Billing Infos
					$card = Processor::create('default', 'creditCard', $creditCard + array(
						'billing' => Processor::create('default', 'address', array(
							'firstName' => $order['billing']['firstname'],
							'lastName'  => $order['billing']['lastname'],
							'address'   => trim($order['billing']['address'] . ' ' . $order['billing']['address2']),
							'city'      => $order['billing']['city'],
							'state'     => $order['billing']['state'],
							'zip'       => $order['billing']['zip'],
							'country'   => $order['billing']['country'] ?: 'US',
							'email'     => $userInfos['email']
			
					))));
					#Create a new Transaction and Get a new Authorization Key
					$auth = Processor::authorize('default', $total, $card);
					if ($auth->success()) {
						if($order['card_type'] == 'amex') {
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
						}
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
							'order_id' => $order['order_id'], 
							'authKey' => $order['authKey'], 
							'total' => $total
						);
					} else {
						$message  = "Authorize failed for order id `{$order['order_id']}`:";
						$message .= $error = implode('; ', $auth->errors);
						$report['errors'][] = array(
							'error_message' => $message, 
							'order_id' => $order['order_id'], 
							'authKey' => $order['authKey'], 
							'total' => $total
						);
					}
				} else {
					$message  = "Void failed for order id `{$order['order_id']}`:";
					$message .= $error = implode('; ', $auth->errors);
					$report['errors'][] = array(
							'error_message' => $message, 
							'order_id' => $order['order_id'], 
							'authKey' => $order['authKey'], 
							'total' => $total
					);
				}
			}
		}
		return $report;
	}
				
	/*** REAUTHORIZE WITH VISA/MASTERCARD / AMEX THROUGH CYBERSOURCE ***/
	public function reAuthCyberSource($order, $report = null, $total) {
		$ordersCollection = Order::Collection();
		$usersCollection = User::Collection();
		#Save Old AuthKey with Date
		$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
		#Cancel Previous Transaction
		if (!empty($order['cyberSourceProfileId'])) {
			if($order['card_type'] != 'amex') {
				$auth = Processor::void('default', $order['auth'], array(
					'processor' => isset($order['processor']) ? $order['processor'] : null
				));
				if(!$auth->success()) {
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
			$cybersource = new CyberSource(Processor::config('default'));
			$profile = $cybersource->profile($order['cyberSourceProfileId']);
			$result = $cybersource->authorize($total, $profile);
			if($result->responseCode == 100) {
				#Setup new AuthKey
				$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$set' => array(
							'authKey' => $result->key,
							'auth' => $result->export(),
							'processor' => $result->adapter,
							'authTotal' => $total
						)), array( 'upsert' => true)
				);
				#Add to Auth Records Array
				$update = $ordersCollection->update(
						array('_id' => $order['_id']),
						array('$push' => array('auth_records' => $newRecord)), array( 'upsert' => true)
				);
				$report['updated'][] = array(
					'order_id' => $order['order_id'], 
					'authKey' => $order['authKey'], 
					'total' => $total
				);
			} else {
				$report['errors'][] = array(
				'error_message' => "Authorize failed for order id ". $order['order_id'] . " " . $result->responseCode, 
				'order_id' => $order['order_id'], 
				'authKey' => $order['authKey'], 
				'total' => $order['authTotal']
				);
			}
		}
		return $report;
	}
	
	public function sendReport($report) {
		Mailer::send('ReAuth_Errors','searnest@totsy.com', $report);
		Mailer::send('ReAuth_Errors','mruiz@totsy.com', $report);
		Mailer::send('ReAuth_Errors','gene@totsy.com', $report);
		Mailer::send('ReAuth_Errors','troyer@totsy.com', $report);
	}
}