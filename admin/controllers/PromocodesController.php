<?php

namespace admin\controllers;
use admin\models\Promocode;
use admin\models\Promotion;
use admin\models\User;
use admin\models\Order;
use MongoId;
use MongoDate;
use MongoRegex;
use MongoCollection;
use lithium\util\Validator;
use li3_flash_message\extensions\storage\FlashMessage;

class PromocodesController extends \admin\controllers\BaseController {

	/**
	 * @todo Improve documentation
	 */
	public function index() {
		$promocodes = Promocode::find('all', array('conditions' => array('special' => array('$ne' => true))));
		$userCollection = User::collection();
		foreach ($promocodes as $promocode){
			$obj_data = $promocode->data();
			if (!empty($obj_data['start_date'])) {
				$promocode->start_date = date('m/d/Y', $obj_data['start_date']);
			}
			if (!empty( $obj_data['end_date'] )) {
				$promocode->end_date = date('m/d/Y', $obj_data['end_date']);
			}
			if (!empty( $obj_data['date_created'] )) {
				$promocode->date_created = date( 'm/d/Y', $obj_data['date_created']);
			}
			if (!empty( $obj_data['created_by'] )) {
				$conditions = array('conditions'=>array('_id'=>$obj_data['created_by']));
				$user = $userCollection->findOne( array('_id' => new MongoId($obj_data['created_by'])) );
				if (array_key_exists('firstname', $user)) {
					$promocode->created_by = $user['firstname'] . ' ' . $user['lastname'];
				} else {
					$promocode->created_by = $user['email'];
				}
			}
		}
		return compact('promocodes');
	}

	/**
	 * @todo Improve documentation
	 */
	public function view($code = null) {
		$promocodes = Promocode::find('all', array(
			'conditions' => array(
			    'special' => true,
				'$or' =>  array(
					array('code' => strtolower($code)),
					array( 'code' => strtoupper($code))
		))));
		$promocodes = $promocodes->data();
		return compact('promocodes');
	}

	/**
	 * @todo Improve documentation
	 */
    public function report() {
		$promocodes = Promocode::find('all', array('conditions' => array('special' => array('$ne' => true))));
		if ($this->request->data) {
			$data = $this->request->data;
			$search = $data['search'];
			if (!empty($data['search'])) {
				$search = $data['search'];
				$conditions = array('code' => new MongoRegex("/$search/i"));
				$promotions  = Promotion::all(compact('conditions'));
				$promocodeDetail = Promocode::all(compact('conditions'));
			}
			if (!empty($data['start_date']) && !empty($data['end_date'])) {
				$start_date = new MongoDate(strtotime($data['start_date']));
				$end_date = new MongoDate(strtotime($data['end_date']));
				$promotions = Promotion::find('all', array(
					'conditions'=> array(
						'date_created' => array(
							'$gt' => $start_date, '$lte' => $end_date
				))));
			}
			foreach ($promotions as $promotion) {
				if ($promotion->date_created) {
					$promotion->date_created = date('m/d/Y', $promotion->date_created->sec);
				}
			}
		}
        return compact('promotions', 'promocodes', 'promocodeDetail');
	}

	/**
	 * @todo Improve documentation
	 */
	public function add() {
       if (!empty($this->request->data)) {
			$code = $this->request->data;
			$col = Promocode::collection();
			$conditions = array('code' => $code['code']);

			if ($col->count($conditions) > 0) {
				$col->update($conditions, array(
					'$set' => array('enabled' => false)),
					array('multiple' => true)
				);
			}
			$promocode = Promocode::create();
			$data = $this->request->data;
			$result = $promocode->createCode($data);
			if ($result) {
				$this->redirect( array( 'Promocodes::index' ) );
			}
		}
	}

	/**
	 * @todo Improve documentation
	 */
	public function edit($id = NULL) {
		$promocode = Promocode::find($id);
		if (!$promocode) {
			$this->redirect('Promocodes::index');
		}
		$obj_data = $promocode->data();

		if (array_key_exists('start_date', $obj_data) && !empty($obj_data['start_date'])){
			$promocode->start_date = date('m/d/Y', $promocode->start_date->sec );
		}

		if (array_key_exists('end_date', $obj_data) && !empty($obj_data['end_date'])){
			$promocode->end_date = date('m/d/Y', $promocode->end_date->sec );
		}
		if ($promocode->parent){
		    $promocode->no_of_promos = Promocode::countChildren($promocode->_id);
		}

		if ($this->request->data) {
			$col = Promocode::collection();
			$conditions = array('code' => $obj_data['code']);
			if($col->count($conditions) > 0) {
				$col->update($conditions, array(
					'$set'=>array('enabled' => false)),
					array('multiple' => true)
				);
			}
			$data = $this->request->data;
			if ($promocode->parent){
			    $promocode->updateParent($data);
			} else {
			    $promocode->updateCode($data);
			}
			$this->redirect( array( 'Promocodes::index' ) );
		}
		return compact('promocode', 'admins');
	}

	/**
	* Produces unique promocodes.
	* POST
	**/
	public function generator(){
		$promoCode = Promocode::create($this->request->data);
		if ($this->request->data) {
			Validator::add('greaterThan2', function($value){
					return ($value > 2)? true:false;
			});
			$rules = array(
				'generate_amount' => array(
					array("notEmpty", "message" => "Please enter an amount"),
					array("numeric", "message" => "Please enter a numeric value eg. 1234"),
					array("greaterThan2", "message" =>"Please enter a value larger than 2")
				));
			$validate = Validator::check($this->request->data, $rules);
			$promoCode->errors( $promoCode->errors() + $validate);
			if (empty($validate)){
				$admins = User::all( array(
					'conditions' => array(
					'admin' => true
				)));
				/**
				* Creating Parent Code
				**/
				$parent = Promocode::create();
				$parent_id = $parent->createParent($this->request->data);
				$loop_number = (int)$this->request->data['generate_amount'];
				for($i=0; $i < $loop_number ; ++$i){
					$promoCode = Promocode::create();
					$col = Promocode::collection();
					do{
						$code = $this->request->data['code'];
						$rand = static::randomString(7, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
						$code .= $rand;
						$conditions = array('code' => $code, 'special' => true);
					}while($col->count($conditions) > 0);
					$data = $this->request->data;
					$data['code'] = $code;
					$promoCode->createChild($data, $parent_id);
					$codes[] = $promoCode->code;
				}//end of forloop
				if (!empty($codes)){
					$this->render(array('layout' => false, 'data' => compact('codes')));
				}
			}
		}
		return compact('promoCode');
	}
}

?>