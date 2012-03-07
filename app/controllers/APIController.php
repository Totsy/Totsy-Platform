<?php


namespace app\controllers;

use app\extensions\helper\ApiHelper;
use lithium\action\Request;
use lithium\data\Connections;
use lithium\util\Validator;
use app\models\Api;
use app\models\Item;
use app\models\Event;
use app\models\User;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;

// TODO: needs better doccumentation
class APIController extends  \lithium\action\Controller {
	
	
	protected static $_formats = array(
		'json', 'xml', 'rss'
	);
	protected $_method = null;
	protected $_format = null;
	protected $_view = null;
	
	/**
	 * Index functon to handle api actions and errors.
	 * Scans for all methods in this class and filter by Api
	 * at the and of the method name. Like we have sales action,
	 * so it'll be salesApi in this class. Checks output data format
	 * if none then by assigned by default 'json'. In case of
	 * unsupported format return error and output in default json.  
	 * 
	 *  @param  void
	 *  @return array combined data
	 */
	// TODO: add request method handeling
	public function index(){
		// check the format of the output data
		$format = null;
		$params = $this->request->args;
		
		if (!preg_match("/[\.]{1}/", $this->request->url)){
			$format = 'json';
		} else { 
			$format = array_pop( explode('.', $this->request->url) );
			if (!is_null($format)) $this->_format = strtolower($format);
		}		
		// format validator
		if (!is_null($format) && !in_array($format, static::$_formats)){
			$this->_format = 'json';
			$this->display( ApiHelper::errorCodes(415) );
		} else {
			$this->_format = $format;
		}

		/* set protocol HTTP OR HTTPS 
		 *
		 * remember to set proper virtual host nginx config
		 * @see Api::setProtocol() comments
		 */
		Api::init($this->request);
		
		if (empty($params) || ( is_array($params) && count($params)==0)){
			$this->display( ApiHelper::errorCodes(405) );
		}
		
		// in case we have only method anf format specified in url
		// so it means to remove form form the method name
		if (is_array($params) && count($params)==1){
			$params[0] = str_replace('.'.$this->_format,'',$params[0]);
		}
		$methods = get_class_methods(get_class($this));
		if (in_array($params[0].'Api',$methods)){
			$this->_method = $params[0];
			$this->display( $this->{$params[0].'Api'}() );
		} else {
			$this->display( ApiHelper::errorCodes(405) );
		}		
	}	
	
	public function help() {
		$all_methods = get_class_methods(get_class($this));
		$methods = array();
		if (array_key_exists('args', $this->request->params) && is_array($this->request->params['args']) && count($this->request->params['args'])>1){
			$current_method = strtolower($this->request->params['args'][1]);
		} else {
			$current_method = 'intro';
		}
		
		foreach($all_methods as $method){
			if(preg_match('/Api/',$method) ){
				$clear = str_replace('Api','',$method);
				$methods[] = array(
					'name'=>strtoupper(substr($clear,0,1)).substr($clear,1),
					'clear' => $clear
				);
			}
		}
		if (file_exists(LITHIUM_APP_PATH . '/views/api/help/'.$current_method.'.php')) {
			ob_start();
			require LITHIUM_APP_PATH . '/views/api/help/'.$current_method.'.php';
			$content = ob_get_clean();
		} else {
			$current_method = 'intro';
			ob_start();
			require LITHIUM_APP_PATH . '/views/api/help/intro.php';
			$content = ob_get_clean();
		} 
		return compact('methods', 'current_method', 'content');
	}
	
	/**
	 * Authorize function
	 * 
	 * Used api to authorize customer
	 * Requires params FOR HTTP in a query string such as
	 * 	@req string auth_key (auth_key)
	 *  @req int	time	(unix time stamp)
	 *  @req string sig		(md5 hash of: private_key,parameters (in alpahbetical order) ) 
	 * 
	 * When doig request via HTTPS required pram is auth_token
	 *  
	 * @return stirng token
	 */
	protected function authorizeApi() {
		
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		return compact('token');		
	}
	
	/**
	 * Retreive current event
	 * almost mimics (for the api) the EventsController::index
	 * except athurization_key OR token_key
	 * 
	 * @protocol HTTP
	 * @method GET
	 * @return sales_list
	 */
	protected function sales(){
		
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		$controller = new EventsController();
		$data = $controller->index();
		return compact('token','data');
	}
	
	/**
	 * Resets password for authorithed user
	 * Beside regular auth, first checks if 
	 * protocol is secure,then request method.
	 * 
	 * SK: is not ready yet.
	 * 
	 * @protocol HTTPS
	 * @method POST
	 * 
	 */
	protected function chnagePassword (){
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		// do not allow requests via HTTP
		// HTTPS only
		if(Api::isSecure() == false){
			return ApiHelper::errorCodes(403);
		}
		// check request method
		if (Api::isPost() == false){
			return ApiHelper::errorCodes(400);
		}
		
		return Api::changePassword($token);
	}
	
	/**
	 * Method to show available active items by active 
	 * event for current date. 
	 * 
	 * min protocol HTTP
	 * method GET
	 */
	protected function itemsApi() {
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		$openEvents = Event::open();
		
		$base_url = 'http://'.$_SERVER['HTTP_HOST'].'/';
		$items = array();
		foreach ($openEvents as $event){
			$ev = $event->data();
			
			if (is_object($ev['start_date'])){
				$ev['start_date'] = array( 'sec' => $ev['start_date']->sec );
			}
			if (!is_array($ev['start_date'])){
				$ev['start_date'] = array( 'sec' => $ev['start_date']);
			}
			
			if (is_object($ev['end_date'])){
				$ev['end_date'] = array( 'sec' => $ev['end_date']->sec );
			}
			if (!is_array($ev['end_date'])){
				$ev['end_date'] = array( 'sec' => $ev['end_date'] );
			}
			
			if ( !array_key_exists('items', $ev) || ( is_array($ev['items']) && count($ev['items'])==0)) continue;
			
			$mItems  = array();
			foreach ($ev['items'] as $item){
				$mItems[] = new MongoId($item);
			}
			
			
			$itms = Item::all(array(
				'conditions' => array(
					'_id' => array( '$in' => $mItems  )
				)
			));
			unset($mItems);
			foreach ($itms as $itm){
				$it = $itm->data();
				$it['base_url'] = $base_url;
				$it['event_url'] = $ev['url'];
				
				if (!isset($it['percent_off'])){
					$it['percent_off'] = 0;
				}
				if (preg_match('/[\%]+/',$it['percent_off'])){
					$it['percent_off'] = substr($it['percent_off'],0,-1);
					if (is_float($it['percent_off']) ) $it['percent_off'] = round($it['percent_off'],2);
				} else if (is_float($it['percent_off'])) {
					$it['percent_off'] = round($it['percent_off']*100,2);
				} else {
					$it['percent_off'] = preg_replace('/[\D]+/','',$it['percent_off']);
					if ($it['percent_off']>74) $it['percent_off'] = 0;	
				} 
				$it['start_date'] = $ev['start_date'];
				$it['end_date'] = $ev['end_date']; 
				$items[] = $it;
			}
		}
		$this->setView(1);
		return (compact('items', 'token'));
	}
	
	/**
	 * Method to show available(active) events 
	 * for current date. 
	 * 
	 * min protocol HTTPS
	 * method GET
	 */
	protected function eventsApi() {
		
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		
		if (is_array($this->request->query) && array_key_exists ('start_date', $this->request->query)){
			if (preg_match('/[[\d]{4}[\-][\d]{2}[\-][\d]{2}]/',$this->request->query['start_date'])){
				$start_date = strtotime($this->request->query['start_date']);
			} else if ($this->request->query['start_date'] == 'today'){
				$start_date = strtotime(date('Y-m-d'));
			}
		}
		
		$openEvents = Event::open();
		
		$base_url = 'http://'.$_SERVER['HTTP_HOST'].'/';
		$events = array();
		$closing = array();
		$maxOff = 0;
		
		foreach ($openEvents as $event){
			
			$data =  $event->data();
			
			if (is_object($data['start_date'])){
				$data['start_date'] = array( 'sec' => $data['start_date']->sec );
			}
			if (!is_array($data['start_date'])){
				$data['start_date'] = array( 'sec' => $data['start_date']);
			}

			if (is_object($data['end_date'])){
				$data['end_date'] = array( 'sec' => $data['end_date']->sec );
			}
			if (!is_array($data['end_date'])){
				$data['end_date'] = array( 'sec' => $data['end_date'] );
			}
		
			if ($data['end_date']['sec'] <= strtotime(date('d-m-Y 23:59:59',strtotime('+1 day',$start_date))) && 
				$data['end_date']['sec'] > strtotime(date('d-m-Y 23:59:59',$start_date)) ){
				$closing[] = $data;
			}
			
			if (isset($start_date) && $start_date > $data['start_date']['sec'] ) { 
				continue; 
			}
			$data['available_items'] = false;
			$data['maxDiscount'] = 0;
			$data['vendor'] = '';
			$data['groups'] = array(
				'category' => array(),
				'age' => array()
			);
			$data['tags'] = array(
				'category' => array(),
				'age' => array()
			);
			
			if (!array_key_exists('event_image',$data)) { $data['event_image'] = $base_url.'img/no-image-small.jpeg'; }
			else { $data['event_image'] = $base_url.'image/'.$data['event_image'].'.jpg'; }

			if (!array_key_exists('splash_small_image',$data)) {
				$data['event_image_small'] = $base_url.'img/no-image-small.jpeg';
			}
			else { $data['event_image_small'] = $base_url.'image/'.$data['splash_small_image'].'.jpg';
			}
			
			if ( isset($data['items']) && count($data['items'])>0){

				$mItems  = array();
				foreach ($data['items'] as $item){
					$mItems[] = new MongoId($item);
				}
				
				$itms = Item::all(array(
					'conditions' => array(
					'_id' => array( '$in' => $mItems  )
					)
				));
				unset($mItems);
				
				foreach ($itms as $itm){
					$it = $itm->data();
					
					if (preg_match('/[\%]+/',$it['percent_off'])){
						$it['percent_off'] = substr($it['percent_off'],0,-1);
						if (is_float($it['percent_off']) ) $it['percent_off'] = round($it['percent_off'],2);
					} else if (is_float($it['percent_off'])) {
						$it['percent_off'] = round($it['percent_off']*100,2);
					} else {
						$it['percent_off'] = preg_replace('/[\D]+/','',$it['percent_off']);
						if ($it['percent_off']>74) $it['percent_off'] = 0;	
					}
					if ($it['percent_off'] > $maxOff) { $maxOff = $it['percent_off']; }
					if ( isset($it['vendor']) && (is_null($data['vendor']) || strlen(trim($data['vendor']))==0)){ 
						$data['vendor'] = $it['vendor']; 
					}
					
					if ($it['percent_off'] > $data['maxDiscount']) { $data['maxDiscount'] = $it['percent_off']; }
					if ($it['total_quantity']>0 && $data['available_items'] === false) { $data['available_items'] = true; }
					
					if (!empty($it['ages'])){ 
						$data['groups']['age'] = array_merge($data['groups']['age'],$it['ages']); 
					}
					if (!empty($it['categories'])){
						$data['groups']['category'] = array_merge($data['groups']['category'],$it['categories']);
					}
				}
				
			}
			
			$data['groups']['age'] = array_unique( $data['groups']['age'] );
			$data['groups']['category'] = array_unique($data['groups']['category']);
			foreach ($data['groups'] as $csK => $cs){
				foreach ($cs as $c){
					$data['tags'][$csK][] = Event::mapCat2Url($csK,$c);
				}
			}

			$data['groups']['ages'] = $data['groups']['age'];
			$data['groups']['categories'] = $data['groups']['category'];
			$data['tags']['ages'] = $data['tags']['age'];
			$data['tags']['categories'] = $data['tags']['category'];
			
			unset($data['tags']['age'], $data['tags']['category']);
			unset($data['groups']['category'], $data['groups']['age'] );
			
			$events[] = $data;
		}
		
		$pendingEvents = Event::pending();
		$pending = array();
		foreach ($pendingEvents as $pendingEvent){
			$pending[] = $pendingEvent->data();
		}
		
		$this->setView(1);
		return (compact('events','pending','closing','base_url','maxOff'));
	}	
	
	/**
	 * Method to review future available(active) events 
	 * for given date. 
	 * 
	 * min protocol HTTPS
	 * method GET
	 */
	protected function eventsReviewApi() {
		
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		
		$token = "testing";
		
		$start_date = strtotime(date('Y-m-d'));
		$start_time = '19:00:00';

		if (is_array($this->request->query) && array_key_exists ('order', $this->request->query)){			
			if (strtolower($this->request->query['order']) == 'desc'){
				$order_desc = true; //DESC
			} 
		} 
		
		if (is_array($this->request->query) && array_key_exists ('start_date', $this->request->query)){			
			if (preg_match('/[\d]{4}[\-][\d]{2}[\-][\d]{2}/i',$this->request->query['start_date'])){
				$start_date = strtotime($this->request->query['start_date']);
			} 
		} 
		
		if (is_array($this->request->query) && array_key_exists ('start_time', $this->request->query)){
			if (strtolower($this->request->query['start_time']) == 'am'){
				$start_time = '08:00:00';
			} 
		}
				
		$eventCollection = Event::connection()->connection->events;
		$openEvents = Event::directQuery(
			array(
				'enabled' => true,
				'start_date' => array(
					'$gte' => new MongoDate( strtotime(date('Y-m-d',$start_date).' '.$start_time) ),
					'$lte' => new MongoDate( strtotime(date('Y-m-d',$start_date).' 23:59:59') )
				)
		));

		$base_url = 'http://'.$_SERVER['HTTP_HOST'].'/';
		$events = array();
		$closing = array();
		$maxOff = 0;
		foreach ($openEvents as $data){

			$data['available_items'] = false;
			$data['maxDiscount'] = 0;
			$data['vendor'] = '';
			$data['groups'] = array(
				'category' => array(),
				'age' => array()
			);
			$data['tags'] = array(
				'category' => array(),
				'age' => array()
			);
			
			if (!array_key_exists('event_image',$data)) { $data['event_image'] = $base_url.'img/no-image-small.jpeg'; }
			else { $data['event_image'] = $base_url.'image/'.$data['event_image'].'.jpg'; }
			
			if (!array_key_exists('splash_small_image',$data)) {
				$data['event_image_small'] = $base_url.'img/no-image-small.jpeg';
			}
			else { $data['event_image_small'] = $base_url.'image/'.$data['splash_small_image'].'.jpg';
			}
			
			if ( isset($data['items']) && count($data['items'])>0){

				$mItems  = array();
				foreach ($data['items'] as $item){
					$mItems[] = new MongoId($item);
				}
				
				$itms = Item::all(array(
					'conditions' => array(
					'_id' => array( '$in' => $mItems  )
					)
				));
				unset($mItems);
				
				foreach ($itms as $itm){
					$it = $itm->data();
					
					if (preg_match('/[\%]+/',$it['percent_off'])){
						$it['percent_off'] = substr($it['percent_off'],0,-1);
						if (is_float($it['percent_off']) ) $it['percent_off'] = round($it['percent_off'],2);
					} else if (is_float($it['percent_off'])) {
						$it['percent_off'] = round($it['percent_off']*100,2);
					} else {
						$it['percent_off'] = preg_replace('/[\D]+/','',$it['percent_off']);
						if ($it['percent_off']>74) $it['percent_off'] = 0;	
					}
					if ($it['percent_off'] > $maxOff) { $maxOff = $it['percent_off']; }
					if ( isset($it['vendor']) && (is_null($data['vendor']) || strlen(trim($data['vendor']))==0)){ 
						$data['vendor'] = $it['vendor']; 
					}
					
					if ($it['percent_off'] > $data['maxDiscount']) { $data['maxDiscount'] = $it['percent_off']; }
					if ($it['total_quantity']>0 && $data['available_items'] === false) { $data['available_items'] = true; }
			
					if (!empty($it['ages'])){ 
						$data['groups']['age'] = array_merge($data['groups']['age'],$it['ages']); 
					}
					if (!empty($it['categories'])){
						$data['groups']['category'] = array_merge($data['groups']['category'],$it['categories']);
					}

				}
				
			}
			
			$data['groups']['age'] = array_unique( $data['groups']['age'] );
			$data['groups']['category'] = array_unique($data['groups']['category']);
			foreach ($data['groups'] as $csK => $cs){
				foreach ($cs as $c){
					$data['tags'][$csK][] = Event::mapCat2Url($csK,$c);
				}
			}

			$data['groups']['ages'] = $data['groups']['age'];
			$data['groups']['categories'] = $data['groups']['category'];
			$data['tags']['ages'] = $data['tags']['age'];
			$data['tags']['categories'] = $data['tags']['category'];
			
			unset($data['tags']['age'], $data['tags']['category']);
			unset($data['groups']['category'], $data['groups']['age'] );
			$events[] = $data;
		}
		
		$closing = Event::directQuery(array(
				'enabled' => true,
				'end_date' => array(
					'$gt' => new MongoDate( strtotime(date('Y-m-d',$start_date).' 00:00:00') ),
					'$lte' => new MongoDate( strtotime(date('Y-m-d',strtotime('+1 day',$start_date)).' 23:59:59') )
				)
		));
		
		$pending = Event::directQuery(array(
				'enabled' => true,
				'start_date' => array(
					'$gt' => new MongoDate( strtotime(date('Y-m-d',$start_date).' 23:59:59') ),
					'$lt' => new MongoDate( strtotime('+2 days',$start_date) )
				)
		));
		
		if (isset($order_desc) && $order_desc){
			if (is_array($events) && count($events)>0) {
				$events = array_reverse($events);
			}
			if (is_array($closing) && count($closing)>0) {
				$$closing = array_reverse($closing);
			}			
		} 
		
		$this->setView(1);
		return (compact('events','pending','closing','base_url','maxOff'));
	}	
	
	
	/**
	 * Subscriber list, provided by date
	 *
	 */
	
	protected function signupsApi (){
		
		$token = Api::authorizeTokenize($this->request->query);
		if (is_array($token) && array_key_exists('error', $token)) {
			return $token;
		}
		$from = $to = null;
		if (array_key_exists('from',$this->request->query)){
			$from = $this->request->query['from'];
			if (preg_match("/[\d]{4}-[\d]{2}-[\d]{2}/",$from)){
				$from = strtotime($from.' 0:00:00');
			} 
		}
		if (array_key_exists('to',$this->request->query)){
			$to = $this->request->query['to'];
			if (preg_match("/[\d]{4}-[\d]{2}-[\d]{2}/",$to)){
				$to = strtotime($to. ' 23:59:59');
			}
		}
		$code = 'keyade';
		if (array_key_exists('code',$this->request->query)){
			$code = $this->request->query['code'];
		}
		if ((!isset($from) || empty($from)) && (!isset($to) || empty($to))){
			return ApiHelper::errorCodes(416);
		}
		
		$options = array(
			'invited_by' => $code,
			'keyade_user_id' => array( '$exists' => true ),
			'created_date' =>  array(
			 '$gte' => new MongoDate($from),
             '$lte' => new MongoDate($to)
			)
		);		
		// Run that sucker!
		$cursor = User::collection()->find( $options );
		$this->setView(1);
		
		return compact('token','cursor');
	}
	
	/**
	 * Signups by Referral list
	 *
	 */
	protected function signupsByReferralApi (){
		$data = $this->signupsApi();
		if (is_array($data) && array_key_exists('error', data)) {
			return $data;
		}
		$code = 'keyade';
		if (array_key_exists('code',$this->request->query)){
			$code = $this->request->query['code'];
		}
		$from = $to = null;
		if (array_key_exists('from',$this->request->query)){
			$from = $this->request->query['from'];
			if (preg_match("/[\d]{4}-[\d]{2}-[\d]{2}/",$from)){
				$from = strtotime($from.' 0:00:00');
			} 
		}
		if (array_key_exists('to',$this->request->query)){
			$to = $this->request->query['to'];
			if (preg_match("/[\d]{4}-[\d]{2}-[\d]{2}/",$to)){
				$to = strtotime($to. ' 23:59:00');
			}
		}

		if ((!isset($from) || empty($from)) && (!isset($to) || empty($to))){
			return ApiHelper::errorCodes(416);
		}
		// Run that sucker!
		$cursor = User::collection()->find( array(
			'keyade_referral_user_id' => array('$exists' => true),
			'keyade_referral_user_id' => array('$ne' => null),
			'created_date' =>  array(
			 '$gte' => new MongoDate($from),
             '$lte' => new MongoDate($to)
			)
		));
		$data['cursor'] = $cursor;
		return $data;
	}
	
	/**
	 * Method to handle templates aka views.
	 * 
	 * template structure :
	 * {{{
	 * views => (
	 * 		api => (
	 * 			xml => (
	 * 				sales => (
	 * 					criteo.php
	 * 					facebook.com
	 * 				)
	 * 				items => (
	 * 					criteo.php
	 * 					facebook.php	
	 * 				)
	 * 			)
	 * 		)
	 * )
	 * }}}
	 */
	private function display ($data){

		if (isset($data['error']['code']) && $data['error']['code'] >0){
			if($this->_firmat == 'xml'){ echo ApiHelper::converter($data,$this->_format); }
			else { echo json_encode($data); }
			exit(0);
		}
		
		switch ($this->_format){
			
			case 'xml':
			case 'rss':
				header("Content-type: text/xml");
				if (!is_null($this->_view)){
					$path = $this->_format.'/'.$this->_method.'/'.$this->_view.'.php';
					if (!file_exists(LITHIUM_APP_PATH . '/views/api/'.$path)) {
						echo ApiHelper::converter(ApiHelper::errorCodes(415),$this->_format);
					}
					extract($data);
					require_once LITHIUM_APP_PATH . '/views/api/'.$path;
				} else if ($this->_method == 'authorize'){
					ApiHelper::converter($data,'xml');
				} else {
					$this->_view = 'default';
					$this->display($data);
				}
			break;
			
			case 'json':
			default:
				header("Content-type: text/javascript");
				if (!is_null($this->_view)){
					$path = $this->_format.'/'.$this->_method.'/'.$this->_view.'.php';
					if (!file_exists(LITHIUM_APP_PATH . '/views/api/'.$path)) {
						echo ApiHelper::converter(ApiHelper::errorCodes(415),$this->_format);
					}
					extract($data);
					require_once LITHIUM_APP_PATH . '/views/api/'.$path;
				} else if ($this->_method == 'authorize'){
					echo json_encode($data);
				} else {
					$this->_view = 'default';
					$this->display($data);
				}
			break;

		}
		exit(0);
	}
	
	private function setView($param){
		$params = $this->request->args;
		if (is_array($params) && count($params)>=2){
			$pc = count($params);
			if ($pc>$param){
				$this->_view = str_replace('.'.$this->_format, '', $params[$param]);
			} 
		}
	}
	
	private function purefiedArray(&$array){
		if (is_array($array) && count($array)>0){
			foreach ($array  as $k=>$a){
				if (is_object($a)){
					$array[$k] = self::obj2array($a);
				}
			}
		} else if (is_object($array)){
			$array = self::obj2array($array);
		}
	}
	
	private static function obj2array(&$obj){
		$return = array();
		if (is_object($obj)){
			$properties = get_object_vars($obj);
			if (is_array($properties) && count($properties)>0){
				foreach ($properties as $property){
					$return[$property] = $obj->{$property};
				}
			}
		}
		return $return;
	}
}

?>
