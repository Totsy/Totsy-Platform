<?php
namespace admin\extensions\command;

use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\data\Connections;
use admin\models\Order;
use admin\models\Item;
use admin\models\OrderShipped;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use li3_silverpop\extensions\Silverpop;


/**
 * Process payments from Authorize.net based on confirmed shipping log.
 */
class OrderShippedNotifications extends \lithium\console\Command  {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	
	public function run() {
		Logger::info('Order Shipped Processor');
		Environment::set($this->env);
		$this->emailNotificationSender();
		Logger::info('Order Shipped Finished');
	}
	
	protected function emailNotificationSender() {
	// collections
		$ordersCollection = Order::connection()->connection->orders;
		$usersCollection = Order::connection()->connection->users;
		$ordersShippedCollection = OrderShipped::collection();
		$itemsCollection = Item::collection();
		
		$time = strtotime('2011-05-17');
		$keys = array('OrderId' => true);
		$inital = array('totalItems' => 0, 'totalTracking' => 0, 'TrackNums' => array() );
		$reduce = new MongoCode("function(a,b){ 
			b.totalItems += 1;
			if (typeof(b.TrackNums[a['Tracking #']]) == 'undefined'){
				b.TrackNums[a['Tracking #']] = new Array();
				b.totalTracking += 1;
			}
			
			b.TrackNums[a['Tracking #']].push(a['ItemId']);
		}");
		//Conditions with date converted to the right timezone
		
		$conditions = array(
			'ShipDate' => array(
				'$gt' => new MongoDate(strtotime('-1 day',$time)),
				'$lte' => new MongoDate($time)),
			'OrderId' => array('$ne' => null),
			// validate tracking number
			'Tracking #' => new MongoRegex("/^[1Z]{2}[A-Za-z0-9]+/i"),
			// do not send notification if it already send
			'emailNotification' => array('$exists' => false)
		);
		/*
		$conditions = array(
			'ShipDate' => new MongoDate($time)
		);
		*/
		$results = $ordersShippedCollection->group($keys, $inital, $reduce, $conditions);
		$results = $results['retval'];
		//$this->out(print_r($results, true));
		
		foreach ($results as $result){
			if ($result['totalTracking']>0){
				$data = array();
				$data['order'] = Order::find('first', array('conditions' => array('_id' => $result['OrderId'])));
				$data['user'] = $usersCollection->find(array('_id' => $data['order']->user_id));
				//$data['email'] = $data['user']['email']; 
				$data['email'] = 'skoshelevskiy@totsy.com'; 
				$data['items'] = array();
				foreach($result['TrackNums'] as $trackNum => $items){
					foreach ($items as $item){
						$data['items'][$trackNum][ (string) $item ] = Item::find('first', array('conditions' => array('_id' => $item)))->data();
					}
				}
				$this->out(print_r($data, true));
				Silverpop::send('orderShipped', $data);
				unset($data);
				//SET send email flag
				foreach($result['TrackNums'] as $trackNum => $items){
					foreach ($items as $item){
						$conditions = array(
								'ItemId' =>  $item, 
								'OrderId' => $result['OrderId']
						);
						$ordersShippedCollection->update($conditions, array('$set' => array('emailNotificationSend' => new MongoDate())));
					}
				}
			}
			break;
		}
		
		
	}
}

?>