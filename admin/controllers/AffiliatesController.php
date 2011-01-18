<?php

namespace admin\controllers;

use \admin\models\Affiliate;
use \admin\models\User;
use li3_flash_message\extensions\storage\FlashMessage;
use MongoDate;
use MongoCollection;

class AffiliatesController extends \admin\controllers\BaseController {

	public function index() {
		$affiliates = Affiliate::find('all',array('conditions'=>array('affiliate'=>true)));


		foreach($affiliates as $affiliate){
			$obj_data= $affiliate->data();
			if(!empty( $obj_data['date_created'] )) {
				$affiliate->date_created= date( 'm/d/Y', $affiliate->date_created->sec);
			}

			if(!empty( $obj_data['created_by'] )) {
				$conditions = array('conditions'=>array('_id'=>$obj_data['created_by']));
				$user = User::find('all', $conditions);
				$user=$user[0]->data();
				$affiliate->created_by= $user['firstname'].' '.$user['lastname'];
			}
		}
		return compact('affiliates');
	}

	public function add() {
		FlashMessage::clear();
		$affiliate = Affiliate::create();

		$data= $this->request->data;
		if (($data) ) {

			$data['active']= (($data['active']=='1' || $data['active']=='on'))? true:false;
			$data['invitation_codes']= explode(" ", trim($data['invitation_codes']));
			$data['created_by']= $affiliate->createdBy();
			$data['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
			if( ($affiliate->save($data))){
				$this->redirect( array( 'Affiliates::index' ) );
			}else{
				FlashMessage::set('Failed to create Affiliate', array('class' => 'pass'));
			}

		}

	}

	public function edit($id=NULL) {
       FlashMessage::clear();
        $affiliate= User::find($id);

        if(!($affiliate)) {
            $this->redirect( array('Affiliates::index'));
            FlashMessage::set('This Affiliate does not exist', array('class'=>'pass'));
        }

        $data = $this->request->data;
        if( ($data) ) {
            $data['active']= (($data['active']=='1' || $data['active']=='on'))? true:false;
			$data['invitation_codes']= explode(" ", trim($data['invitation_codes']));
			$data['created_by']= $affiliate->createdBy();
			$data['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
			if( ($affiliate->save($data))){
				$this->redirect( array( 'Affiliates::index' ) );
				FlashMessage::set('Affiliate Edit was successful', array('class' => 'pass'));
			}else{
				FlashMessage::set('Failed to edit Affiliate', array('class' => 'pass'));
			}
        }
        return compact('affiliate');
	}
}

?>