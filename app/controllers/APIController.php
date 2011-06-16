<?php

namespace app\controllers;

//use admin\controllers\EventsController;

//use app\controllers\EventsController;

use lithium\action\Request;
use \lithium\data\Connections;
use \lithium\util\Validator;
use app\models\Api;
use app\models\Item;
use app\models\Event;


class APIController extends  \lithium\action\Controller {
	
	protected static $_formats = array(
		'json', 'xml'
	);
	protected $_format = null;
	
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
		$format = array_pop( explode('.', $this->request->url) );
		var_dump ($format);
		echo '<br>';
		if (!is_null($format)) $format = strtolower($format);
		else $format = 'json';
		
		var_dump ($format);
		echo '<br>';
		
		// format validator
		if (!in_array($format, static::$_formats)){
			$this->format = 'json';
			$this->display( Api::errorCodes(415) );
		}
		/*
		echo '<pre>';
		print_r($this->request);
		print_r($format);
		echo '</pre>';
		exit(0);
		*/
		$params = $this->request->args;
		// set protocol HTTP OR HTTPS 
		Api::init($this->request);
		
		if (empty($params) || ( is_array($params) && count($params)==0)){
			$this->display( Api::errorCodes(405) );
		}
		
		$methods = get_class_methods(get_class($this));

		if (in_array($params[0].'Api',$methods)){
			$this->display( $this->{$params[0].'Api'}() );
		} else {
			$this->display( Api::errorCodes(405) );
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
			return Api::errorCodes(403);
		}
		// check request method
		if (Api::isPost() == false){
			return Api::errorCodes(400);
		}
		
		return Api::changePassword($token);
	}
	
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
			if ( !array_key_exists('items', $ev) || ( is_array($ev['item']) && count($ev['item']))) continue;
			
			$mItems  = array();
			foreach ($ev['items'] as $item){
				$mItems[] = new MonogoId($item);
			}
			unset($mItems);
			
			$itms = Item::all(array(
				'conditions' => array(
					'_id' => array( '$in' => $mItems  )
				)
			));
			
			foreach ($itms as $itm){
				$items[] = $itm->data();
			}
		}

		return $items;				
	}
	
	private function display ($data){
		switch ($this->_format){
			case 'json':
			default:
				echo json_encode($data);
			break; 
		}
		exit(0);
	}
}

?>