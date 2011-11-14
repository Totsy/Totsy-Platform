<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;
use admin\models\Order;
use admin\extensions\Mailer;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;
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
	
	/**
	 * Instances
	 */
	public function run() {
		error_reporting(E_ERROR | E_PARSE);
		Environment::set($this->env);
		$ordersCollection = Order::Collection();
		$ordersCollection->ensureIndex(array(
			'date_created' => 1,
			'cc_payment' => 1
		));
		$errors = 0;
		$updated = 0;	
		#Limit to +7 days Old Authkey
		$limitDate = mktime(0, 0, 0, date("m"), date("d") - $this->expiration, date("Y"));
		#Get All Orders with Auth Date >= 7days, Not Void Manually or Shipped
		$conditions = array('void_confirm' => array('$exists' => false),
							'auth_confirmation' => array('$exists' => false),
							'authKey' => array('$exists' => true),
							'cc_payment' => array('$exists' => true),
							'date_created' => array('$lte' => new MongoDate($limitDate))
		);
		$orders = $ordersCollection->find($conditions);
		if(!empty($orders)) {
			foreach ($orders as $order) {
				$reAuth = false;
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
				if($reAuth) {
					#Decrypt Credit Card Infos
					if(!empty($order['cc_payment'])) {
						$cc_encrypt = $order['cc_payment'];
						$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
						$iv =  base64_decode($order['cc_payment']['vi']);
						$key = md5($order['user_id']);
						unset($cc_encrypt['vi']);
						foreach	($cc_encrypt as $k => $cc_info) {
							$crypt_info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key.sha1($k), base64_decode($cc_info), MCRYPT_MODE_CFB, $iv);
							$creditCard[$k] = $crypt_info;
						}
						#If Credit Card Has Been well Decrypted
						if(!empty($creditCard)) {
							try {
								#Save Old AuthKey with Date
								$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
								#Cancel Previous Transaction
								$authVoid = Payments::void('default', $order['authKey']);
								try {
									#Create Card and Check Billing Infos
									$card = Payments::create('default', 'creditCard', $creditCard + array(
										'billing' => Payments::create('default', 'address', array(
											'firstName' => $order['billing']['firstname'],
											'lastName'  => $order['billing']['lastname'],
											'address'   => trim($order['billing']['address'] . ' ' . $order['billing']['address2']),
											'city'      => $order['billing']['city'],
											'state'     => $order['billing']['state'],
											'zip'       => $order['billing']['zip'],
											'country'   => $order['billing']['country']
									))
									));
									#Create a new Transaction and Get a new Authorization Key
									$auth = Payments::authorize('default', $order['total'], $card);
									#Setup new AuthKey
									$update = $ordersCollection->update(
											array('_id' => $order['_id']),
											array('$set' => array('authKey' => $auth)), array( 'upsert' => true)
									);
									#Add to Auth Records Array
									$update = $ordersCollection->update(
											array('_id' => $order['_id']),
											array('$push' => array('auth_records' => $newRecord)), array( 'upsert' => true)
									);
									$updated++;
								} catch (TransactionException $e) {
									$errors++;
									$report['authorize_errors'][] = array(
										'error_message' => $e->getMessage(), 
										'order_id' => $order['order_id'], 
										'authKey' => $order['authKey'], 
										'total' => $order['total']
									);
								}
							} catch (TransactionException $e) {
								$errors++;
								$report['void_errors'][] = array(
										'error_message' => $e->getMessage(), 
										'order_id' => $order['order_id'], 
										'authKey' => $order['authKey'], 
										'total' => $order['total']
								);
							}
						}
					}
				}
			}
		}
		#If Errors Send Email to Customer Service
		$report['total_updated'] = $updated;
		$this->sendReport($report);
		echo 'ReAuth Script Runned, ' . $updated . ' Orders Updated ' . $errors . ' Errors Found. ';
	}
	
	public function sendReport($report) {
		Mailer::send('ReAuth_Errors','searnest@totsy.com', $report);
		Mailer::send('ReAuth_Errors','mruiz@totsy.com', $report);
		Mailer::send('ReAuth_Errors','gene@totsy.com', $report);
		Mailer::send('ReAuth_Errors','troyer@totsy.com', $report);
	}
}