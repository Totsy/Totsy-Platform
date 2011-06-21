<?php

/*
 * 2011-05-16 updates
 * 	- output format checker fixed APIController::index()
 */

namespace app\controllers;

//use admin\controllers\EventsController;

//use app\controllers\EventsController;

use app\extensions\helper\ApiHelper;

use lithium\action\Request;
use \lithium\data\Connections;
use \lithium\util\Validator;
use app\models\Api;
use app\models\Item;
use app\models\Event;
use MongoId;


class APIController extends  \lithium\action\Controller {
	
	
	protected static $_formats = array(
		'json', 'xml'
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
			if (!is_null($format)) $format = strtolower($format);
		}		
		// format validator
		if (!is_null($format) && !in_array($format, static::$_formats)){
			$this->format = 'json';
			$this->display( ApiHelper::errorCodes(415) );
		}
		
		// set protocol HTTP OR HTTPS 
		Api::init($this->request);
		
		if (empty($params) || ( is_array($params) && count($params)==0)){
			$this->display( ApiHelper::errorCodes(405) );
		}
		
		$methods = get_class_methods(get_class($this));

		if (in_array($params[0].'Api',$methods)){
			$this->_method = $params[0];
			$this->display( $this->{$params[0].'Api'}() );
		} else {
			$this->display( ApiHelper::errorCodes(405) );
		}		
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
		return compact('token','data');		
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
	protected function salesApi(){
		
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
	 * @protocol HTTPS
	 * @method POST
	 * 
	 */
	protected function chnagePasswordApi (){
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
				 $items[] = $it;
			}
		}
		$this->setView(1);
		return $items;				
	}
	
	
	/**
	 * Mwthod to handle templates aka views.
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
		
		switch ($this->_format){
			
			case 'xml':
				if (!is_null($this->_view)){
					$path = $this->_format.'/'.$this->_method.'/'.$this->_view.'.php';
					if (!file_exists(LITHIUM_APP_PATH . '/views/api/'.$path)) {
						echo ApiHelper::converter(ApiHelper::errorCodes(415),$this->_format);
					}
					require_once LITHIUM_APP_PATH . '/views/api/'.$path;
				} else {
					echo ApiHelper::converter(ApiHelper::errorCodes(415),$this->_format);
				}
			break;
			
			case 'json':
			default:
				echo json_encode($data);
			break;

		}
		exit(0);
	}
	
	private function setView($param){
		$params = $this->request->args;
		if (is_array($params) && count($params)>2){
			$pc = count($params);
			if ($pc>$param){
				$this->view = str_replace('.'.$this->_format, '', $params[$param]);
			} 
		}
	}
}

?>