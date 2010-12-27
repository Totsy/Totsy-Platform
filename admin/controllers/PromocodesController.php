<?php

namespace admin\controllers;

use \admin\models\Promocode;
use \admin\models\Promotion;
use \admin\models\User;
use li3_flash_message\extensions\storage\FlashMessage;
use MongoDate;
use MongoRegex;
use MongoCollection;

class PromocodesController extends \admin\controllers\BaseController {

	public function index() {

       $promocodes = Promocode::all();
	
       foreach($promocodes as $promocode){

          $obj_data = $promocode->data();

           if(!empty( $obj_data['start_date'] )) {
                $promocode->start_date = date('m/d/Y', $promocode->start_date->sec );
            }

            if(!empty( $obj_data['end_date'] )) {
                $promocode->end_date = date('m/d/Y', $promocode->end_date->sec );
            }

            if(!empty( $obj_data['date_created'] )) {
                $promocode->date_created= date( 'm/d/Y', $promocode->date_created->sec);
            }

			if(!empty( $obj_data['created_by'] )) {
				$conditions = array('conditions'=>array('_id'=>$obj_data['created_by']));
                $user = User::find('all', $conditions);
				$user=$user[0]->data();
				$promocode->created_by= $user['firstname'].' '.$user['lastname'];
            }

        }

		return compact('promocodes');
	}

	public function view($code = null) {
       // var_dump($code);
		$promocodes = Promocode::find( 'all', array(
									'conditions' =>array(
													'$or' =>  array(
														array( 'code' => strtolower($code) ),
														array( 'code' => strtoupper($code) )
										) ) ) );
      //  var_dump($promocode->data());
        $promocodes= $promocodes->data();
		return compact('promocodes');
	}

    public function report() {
        FlashMessage::clear();


        if( empty($this->request->data) ){

            $promotions = Promotion::all();
            $promocodes= Promocode::all();

        }else{

           $data = $this->request->data;
            $search = $data['search'];
            if( !empty($data['search']) ) {

                    $search = $data['search'];
                    $promotions  = Promotion::find(  'all', array(
                                                    'conditions' =>array(
                                                                    '$or' =>  array(
                                                                        array( 'code' => strtolower($search) ),
                                                                        array( 'code' => strtoupper($search) )
                                                        ) ) ) );
                    $promocodes = Promocode::all( array(
                                                    'conditions' =>array(
                                                                    '$or' =>  array(
                                                                        array( 'code' => strtolower($search) ),
                                                                        array( 'code' => strtoupper($search) )
                                                        ) ) ) );
            }

            if( !empty( $data['start'] ) && !empty($data['end']) ) {

                $start = new MongoDate( strtotime($data['start']) );
                $end = new MongoDate( strtotime($data['end']) );
                $promotions  = Promotion::find(  'all', array( 'conditions'=>array('date_created' =>array( '$gt' => $start, '$lte' => $end ) ) ) );
            }

            if (!empty($promotions)) {
                FlashMessage::set('Results Found', array('class' => 'pass'));
            } else {
                FlashMessage::set('No Results Found', array('class' => 'warning'));
            }

        }

        foreach($promotions as $promotion){

          $obj_data = $promotion->data();

            if( !empty( $obj_data['date_created'] ) ) {
                $promotion->date_created= date( 'm/d/Y', $promotion->date_created->sec);
            }
        }

        return compact('promotions', 'promocodes');
	}

	public function add() {
        FlashMessage::clear();

        if( !empty($this->request->data) ) {
			$promoCode= Promocode::create();
			$admins = User::all( array( 'conditional'=>array('admin' => true) ) );
			$code = $this->request->data;
			
			$col = Promocode::collection();
			$conditions = array('code' =>$code['code']);
			
			if($col->count( $conditions ) > 0) {
				$col->update($conditions, array('$set'=>array('enabled'=>false)), array('multiple'=>true));
			}
          

		  if( $this->request->data['enabled'] == '1' || $this->request->data['enabled'] == 'on' ){

			   $code['enabled'] = true;

			}else{

				$code['enabled'] = false;

			}
			$code['discount_amount'] = (float) $code['discount_amount'];
			$code['minimum_purchase'] = (int)$code['minimum_purchase'];
			$code['max_use'] = (int)$code['max_use'];
		   $code['start_date']= new MongoDate( strtotime( $code['start_date'] ) );
		   $code['end_date']= new MongoDate( strtotime( $code['end_date'] ) );
		   $code['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
		   $code = Promocode::createdBy($code);

			$result = $promoCode->save($code);
			if ($result) {
                $this->redirect( array( 'Promocodes::index' ) );
                FlashMessage::set('Promocode Created!', array('class' => 'pass'));
            } else {
                FlashMessage::set('Promocode not created.  Please check the form', array('class' => 'warning'));
            }

		}
	}

	public function edit($id=NULL) {
		$promocode = Promocode::find($id);

		if (!$promocode) {

			$this->redirect('Promocodes::index');

		}

        $obj_data = $promocode->data();

        if(!empty( $obj_data['start_date'] )){
                $promocode->start_date = date('m/d/Y', $promocode->start_date->sec );
        }

        if(!empty( $obj_data['end_date'] )){
            $promocode->end_date = date('m/d/Y', $promocode->end_date->sec );
        }

		if ( $this->request->data ) {

		   $data = $this->request->data;

		   if( $data['enabled'] == '1' || $data['enabled'] == 'on' ){
				
			   $data['enabled'] = true;

			}else{

				 $data['enabled'] = false;

			}
			$data['discount_amount'] = (float) $data['discount_amount'];
			$data['minimum_purchase'] = (int)$data['minimum_purchase'];
			$data['max_use'] = (int)$data['max_use'];
			$data['start_date']= new MongoDate( strtotime( $data['start_date'] ) );
			$data['end_date']= new MongoDate( strtotime( $data['end_date'] ) );
			$data['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
			$data= Promocode::createdBy($data);
			
		   $promocode->save($data);

			$this->redirect( array( 'Promocodes::index' ) );
		}

		return compact('promocode', 'admins');
	}
}

?>
