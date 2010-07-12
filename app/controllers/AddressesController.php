<?php

namespace app\controllers;
use app\controllers\BaseController;
use app\models\Address;
use \lithium\storage\Session;
use app\models\Menu;
use app\models\User;
use \MongoId;
use \lithium\data\Connections;
use \lithium\analysis\Logger;




class AddressesController extends BaseController {
	/**
	 * The maximum number of addresses a user can have stored
	 * @var int
	 */
	private $_maxAddresses = 10; 
	
	
	public function view(){

		$this->_render['layout'] = 'main';

		$addressList = Address::find('all', array('conditions' => array('user_id' => Session::read('_id'))))->data();
		
		return compact("addressList");
	}
	
	private function getUser($fields = array()) {

		$id = new MongoID(Session::read('_id'));
		return User::find('first', array('conditions' => array('_id' => $id), $fields))->data();	
	}
	
	/**
	 * Adds an address
	 */
	public function add() {
		$this->log();
		//Set some defaults
		$status = '';
		$message = '';
		$this->_render['layout'] = 'main';
		$data = array();
					
		if($this->request->data){
			$data = $this->cleanAddressData();
			$count = Address::count(array('user_id' => Session::read('_id')));
			
			if($count >= $this->_maxAddresses) {
				$message = 'There are already 10 addresses registered. Please remove one first.';
			} else {
				$Address = Address::create($data);
				//Check Li3 on Model->save to cleanup array_merge above
				$status = $Address->save($data);
				$message = 'Address Saved';
			}
			
		}
		
		return compact('status', 'message', 'data');
	}
		
	/**
	 * Sets up the Menu element for the page
	 */
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$menu = Menu::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$self->set(compact('menu'));
			return $chain->next($self, $params, $chain);
		});
	}
	
	public function edit() {
		$this->log();
		$message = '';
		$dataObject = null;
		//Use the add template and main layout
		$this->_render['layout'] = 'main';
		$this->_render['template'] = 'add';
		
		//Get the arg address _id
		$addressId = func_get_args();

		// Check that we got	 address _id
		if($addressId){
			//Find address using user_id and address_id
			$dataObject = Address::find('first', array('conditions' => array('_id' => $addressId[0])));
			var_dump($dataObject);
			if($dataObject) {
				//Get the Data from record object
				$addressRecord = $dataObject->data();
				
				//If we don't have data redirect to add view for security
				if(!$addressRecord) {
					$this->redirect('/addresses/add');
				}				
			}
		}
		//Check if we got form data from $POST
		if($this->request->data) {
			var_dump($this->request->data);
			$data = $this->cleanAddressData();
			//Save the updated data		
			$status = $dataObject->save($data);
			//TODO: Remove the old information from the record
			if($status) {
				$message = 'Address Updated';
				$addressRecord = $data;
			} else {
				//If this doesn't get updated we have database issues. 
				$message = 'Error';
			}			
		}

		return compact('message', 'addressRecord');
	}
	
	private function getSession(){
		return array('user_id' => Session::read('_id'));
	}
	
	private function cleanAddressData()
	{
		//Remove the submit data
		unset($this->request->data['submit']);
		//Merge session info from user to data	
		return array_merge($this->getSession(), $this->request->data);
	}
	
	private function log() {
		Logger::config(array(
			'default' => array('adapter' => 'File')
		));
		
		$MongoDb = Connections::get('default');
		$MongoDb->applyFilter('read', function($self, $params, $chain) use (&$MongoDb) {
			$result = $chain->next($self, $params, $chain);

			if (method_exists($result, 'data')) {
				Logger::write('info',
					json_encode($params['query']->export($MongoDb) + array('result' => $result->data()))
				);
			}
			return $result;
		});
	}
	
	private function checkDefault() {
		//Change all the default addresses
	}	
}
?>