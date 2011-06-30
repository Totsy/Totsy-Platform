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
use admin\extensions\command\Pid;

/**
 * Process email notifications for orders shipped.
 *
 * Since 06-29-2011 supports command line params.
 * (only for public or protected variables)
 */
class OrderShippedNotifications extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * Directory of tmp files.
	 *
	 * @var string
	 */
	public $tmp = '/resources/totsy/tmp/';
	/**
	 *
	 * Flag for debug mode and email address -
	 * if non-null, we're debugging and sending
	 * info to the specified email address.
	 *
	 * Example for debug:
	 * protected $debugemail = "skosh@totsy.com";
	 * Example for production:
	 * protected $debugemail = null;
	 *
	 * Example for debug specefied in cli:
	 * li3 order-shipped-notifications --debugemail=skosh@totsy.com
	 */
	protected $debugemail = null;
	public function run() {
		Logger::info('Order Shipped Processor');
		Environment::set($this->env);
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$pid = new Pid($this->tmp,  'OrderShippedNotification');
		if ($pid->already_running == false) {
			$this->getCommandLineParams();
			$this->emailNotificationSender();
		} else {
			Logger::info("Already Running! Stoping Execution");
		}
		Logger::info('Order Shipped Finished');
	}

	protected function emailNotificationSender() {
	// collections;
	    $ordersCollection = Order::collection();
		$usersCollection = User::collection();
		$ordersShippedCollection = OrderShipped::collection();
		$keys = array('OrderId' => true);
		$inital = array('totalItems' => 0, 'Tracking' => 0, 'TrackNums' => array());
		$reduce = new MongoCode("function(a,b){
			b.totalItems += 1;
			if (b.Tracking == 0 && typeof(a['Tracking #']) != 'undefined'){
				b.Tracking = a['Tracking #'];
			}

			if (typeof(b.TrackNums[b.Tracking]) == 'undefined'){
				b.TrackNums[b.Tracking] = new Array();
			}
			b.TrackNums[b.Tracking].push({ 'id': a['ItemId'], 'sku': a['SKU'] });
		}");
		//Conditions with date converted to the right timezone
		$conditions = array(
			'ShipDate' => array(
				'$gte' => new MongoDate(mktime(0, 0, 0, date("m"), date("d")-2, date("Y"))),
				'$lt' => new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")))
			),
			'OrderId' => array('$ne' => null),
		    // don't validate TRCK # because sometimes there could shipped item without tracking #
			// validate tracking number
			//'Tracking #' => new MongoRegex("/^[1Z]{2}[A-Za-z0-9]+/i"),
			// do not send notification if it already send
			'emailNotification' => array('$exists' => false)
		);

		$results = $ordersShippedCollection->group($keys, $inital, $reduce, $conditions);

		if (is_object($results) && get_class_name($results)=='MongoCursor'){
			Logger::info('Found "'.$results::count().'" orders');
		} else if ( is_array($results)){
			if (array_key_exists('errmsg',$results)){
				Logger::info('ERROR: "'.$results['errmsg'].'"');
				// to make shure that process closes correctly
				if (!isset($results['retval']) || count($results['retval'])==0){
					return false;
				}
			}
			$results = $results['retval'];
			Logger::info('Found "'.count($results).'" orders');
		}
		$skipped = array();
		$c = 0;
		foreach ($results as $result){

			if (count($result['TrackNums'])>0){
				$do_break = false;
				$data = array();
				$data['order'] = $ordersCollection->findOne(  array('_id' => $result['OrderId'] ));
				$data['user'] = $usersCollection->findOne(array('_id' => $this->getUserId($data['order']['user_id']) ));
				if (is_null($this->debugemail)) {
					$data['email'] = $data['user']['email'];
				} else {
					$data['email'] = $this->debugemail;
				}
				$data['items'] = array();
				$itemSkus = $this->getSkus($data['order']['items']);
				$problem = '';
				foreach($result['TrackNums'] as $trackNum => $items){
					if ( $trackNum==0 || (strlen($trackNum)<15 && $data['order']['auth_confirmation'] < 0) ){
						$problem = 'No tracking number and payment auth confirmation error';
						$do_break = true;
						break;
					}
					if ( $do_break===false ){
						$itemCount = 0;
						foreach ($items as $item){
							if (!array_key_exists($item['sku'],$itemSkus)){
								Logger::info('Items don\'t match ['.$data['order']['order_id'].']');
								$problem = 'Some items don\'t match';
								$do_break = true;
								break;
							}
							$data['items'][$trackNum][ (string) $item['id'] ] = $itemSkus[ $item['sku'] ];
							$itemCount++;
						}
					}
				}

				if ($do_break===true){
					Logger::info('skip ['.$data['order']['order_id'].']');
					$do_break = false;
					$skipped[] = array('OrderId'=>$data['order']['order_id'], 'MongoId'=>$result['OrderId'], 'problem' => $problem);
					continue;
				}
				unset($itemSkus);
				unset($do_break);
				Logger::info('Trying to send email for order #'.$data['order']['order_id'].'('.$result['OrderId'].' to '.$data['email'].' (tottal items: '.$itemCount.')');
				Silverpop::send('orderShipped', $data);
				unset($data);
				if(is_null($this->debugemail)) {
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
			}

			// don't send more than 10 emails in debug mode
			if (!is_null($this->debugemail)) {
				if ($c==10){
					break;
				}
				$c++;
			}

		}

		if (count($skipped)>0){
			$data['skipped'] = $skipped;

			if(is_null($this->debugemail)) {
				$data['email'] = 'email-notifiations@totsy.com';
			}
			else {
				$data['email'] = $this->debugemail;
			}
			Silverpop::send('ordersSkipped', $data);
			unset($data);
		}
	}

	private function getUserId($id) {
		if (strlen($id)<10){
			return $id;
		} else {
			return new MongoId($id);
		}
	}

	/**
	 * Method to get array of skus out of the array of shipped items for a particular order
	 *
	 * @param array $itms
	 */
	private function getSkus ($itms){
		$itemsCollection = Item::collection();

		$ids = array();
		$items = array();
		$itemSkus = array();

		foreach($itms as $itm){
			$items[$itm['item_id']] = $itm;
			$ids[] = new MongoId($itm['item_id']);
		}
		$iSkus = $itemsCollection->find(array('_id' => array( '$in' => $ids )));
		unset($ids);
		$iSs = array();
		foreach ($iSkus as $i){
			$iSs[ (string) $i['_id'] ] = $i;
		}

		foreach ($itms as $itm){
			$sku = $iSs[ $itm['item_id'] ]['sku_details'][ $itm['size'] ];
			$itemSkus[ $sku ] = $itm;
		}
		unset($iSs);
		unset($items);
		return $itemSkus;
	}

	private function getCommandLineParams(){
		$params = $this->request->params;
		$vars = get_class_vars(get_class($this));
		foreach ($vars as $var=>$value){
			if (array_key_exists($var,$params)){
				$this->{$var} = $params[$var];
			}
		}
	}
}

?>