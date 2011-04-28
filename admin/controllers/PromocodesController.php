<?php

namespace admin\controllers;
use admin\models\Promocode;
use admin\models\Promotion;
use admin\models\User;
use admin\models\Order;
use MongoDate;
use MongoRegex;
use MongoCollection;
use lithium\util\Validator;

class PromocodesController extends \admin\controllers\BaseController {

	/**
	 * @todo Improve documentation
	 */
	public function index() {
		$promocodes = Promocode::find('all', array('conditions' => array('special' => array('$ne' => true))));
		foreach ($promocodes as $promocode){
			$obj_data = $promocode->data();
			if (!empty($obj_data['start_date'])) {
				$promocode->start_date = date('m/d/Y', $obj_data['start_date']['sec']);
			}
			if (!empty( $obj_data['end_date'] )) {
				$promocode->end_date = date('m/d/Y', $obj_data['end_date']['sec']);
			}
			if (!empty( $obj_data['date_created'] )) {
				$promocode->date_created = date( 'm/d/Y', $obj_data['date_created']['sec']);
			}
			if (!empty( $obj_data['created_by'] )) {
				$conditions = array('conditions'=>array('_id'=>$obj_data['created_by']));
				$user = User::find('all', $conditions);
				$user = $user[0]->data();
				$promocode->created_by = $user['firstname'] . '' . $user['lastname'];
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
		$promocodes = Promocode::all();
		if ($this->request->data) {
			$data = $this->request->data;
			$search = $data['search'];
			if (!empty($data['search'])) {
				$search = $data['search'];
				$conditions = array('code' => new MongoRegex("/$search/i"));
				$promotions  = Promotion::all(compact('conditions'));
				$promocodeDetail = Promocode::all(compact('conditions'));
			}
			if (!empty($data['start']) && !empty($data['end'])) {
				$start = new MongoDate(strtotime($data['start']));
				$end = new MongoDate(strtotime($data['end']));
				$promotions = Promotion::find('all', array(
					'conditions'=> array(
						'date_created' => array(
							'$gt' => $start, '$lte' => $end
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
			$promoCode = Promocode::create();
			$admins = User::all( array(
				'conditions' => array(
				'admin' => true
			)));
			$code = $this->request->data;
			$col = Promocode::collection();
			$conditions = array('code' => $code['code']);

			if ($col->count($conditions) > 0) {
				$col->update($conditions, array(
					'$set' => array('enabled' => false)),
					array('multiple' => true)
				);
			}
			$code['enabled'] = 	Promocode::setToBool($this->request->data['enabled']);
			$code['limited_use'] = Promocode::setToBool($this->request->data['limited_use']);
			$code['discount_amount'] = (float) $code['discount_amount'];
			$code['minimum_purchase'] = (int) $code['minimum_purchase'];
			$code['max_use'] = (int) $code['max_use'];
			$code['start_date'] = new MongoDate(strtotime($code['start_date']));
			$code['end_date'] = new MongoDate(strtotime($code['end_date']));
			$code['date_created'] = new MongoDate(strtotime(date('D M d Y')));
			$code['created_by'] = Promocode::createdBy();

			$result = $promoCode->save($code);
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
			$data['enabled'] = 	Promocode::setToBool($this->request->data['enabled']);
			//$data['limited_use'] = Promocode::setToBool($this->request->data['limited_use']);
			$data['discount_amount'] = (float) $data['discount_amount'];
			$data['minimum_purchase'] = (int) $data['minimum_purchase'];
			$data['max_use'] = (int) $data['max_use'];
			$data['start_date'] = new MongoDate( strtotime( $data['start_date'] ) );
			$data['end_date'] = new MongoDate( strtotime( $data['end_date'] ) );
			$data['date_created'] = new MongoDate( strtotime( date('D M d Y') ) );
			$data['creaeted_by'] = Promocode::createdBy();

			$promocode->save($data);
			$this->redirect( array( 'Promocodes::index' ) );
		}

		return compact('promocode', 'admins');
	}

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
            if(empty($validate)){
                $admins = User::all( array(
                    'conditions' => array(
                    'admin' => true
                )));
                $loop_number = (int)$this->request->data['generate_amount'];
                for($i=0; $i < $loop_number ; ++$i){
                    $promoCode = Promocode::create();
                    $col = Promocode::collection();
                    do{
                        $code = $this->request->data['code'];
                        $rand = rand(1,$loop_number);
                        $code .= $rand;
                        $conditions = array('code' => $code, 'special' => true);
                    }while($col->count($conditions) > 0);
                    $data['code'] = $code;
                    $data['type'] = $this->request->data['type'];
                    $data['enabled'] = 	Promocode::setToBool($this->request->data['enabled']);
                    $data['discount_amount'] = (float) $this->request->data['discount_amount'];
                    $data['minimum_purchase'] = (int) $this->request->data['minimum_purchase'];
                    $data['max_use'] = (int) $this->request->data['max_use'];
                    $data['start_date'] = new MongoDate(strtotime($this->request->data['start_date']));
                    $data['end_date'] = new MongoDate(strtotime($this->request->data['end_date']));
                    $data['date_created'] = new MongoDate(strtotime(date('D M d Y')));
                    $data['special'] = true;
                    $data['created_by'] = Promocode::createdBy();
                    $whitelist = array_keys($data);
                    $result = $promoCode->save($data, array('whitelist' => $whitelist));
                    $codes[] = $code;
                }//end of forloop
                if(!empty($codes)){
                    $this->render(array('layout' => false, 'data' => compact('codes')));
                }
            }
		}
		return compact('promoCode');
	}
}

?>