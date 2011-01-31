<?php

namespace admin\controllers;

use admin\models\Affiliate;
use admin\models\User;
use MongoDate;
use MongoId;
use MongoRegex;
use MongoCollection;

class AffiliatesController extends \admin\controllers\BaseController {

	public $sitePages = array(
	    '/register' => 'registration',
	    '/' => 'login',
	    '/sales' => 'sales',
	    '/shopping/checkout' => 'checkout',
	    '/shopping/process' => 'checkout process',
	    '/orders/view' => 'orders confirmation',
	    '/join/' => 'landing page'
	    );

	public function index() {
	   $affiliates = Affiliate::find('all',array('conditions'=>array('affiliate'=>true)));

        foreach($affiliates as $affiliate){
            $obj_data = $affiliate->data();
            if(!empty( $obj_data['date_created'] )) {
             $affiliate->date_created = date( 'm/d/Y', $affiliate->date_created->sec);
            }

            if(!empty( $obj_data['created_by'] )) {
                $conditions = array('conditions'=>array('_id' => $obj_data['created_by']));
                $user = User::find( 'all', $conditions );
                $user = $user[0]->data();
                $affiliate->created_by = $user['firstname'] . ' ' . $user['lastname'];
            }
        }
        return compact('affiliates');
	}

	public function add() {

		$affiliate = Affiliate::create();
        $info = array();

		$data = $this->request->data;
		if ( ($data) ) {
			$info['active'] = (( $data['active'] == '1' || $data['active'] == 'on')) ? true : false;
			$info['name'] = $data['affiliate_name'];
			$info['invitation_codes'] = array_values( $data['invitation_codes'] );
			if($data['active_pixel'] == '1' || $data['active_pixel'] == 'on'){
                $info['active_pixel'] = true;
			    $info['pixel'] = Affiliate::pixelFormating( $data['pixel'], $info['invitation_codes'] );
			}else{
			    $info['active_pixel'] = false;
			}
			$info['created_by'] = $affiliate->createdBy();
			$info['date_created'] = new MongoDate( strtotime( date('D M d Y') ) );
			if( ($affiliate->save($info)) ){
				$this->redirect( array( 'Affiliates::index' ) );
			}
		}
		$sitePages = $this->sitePages;
        return compact('sitePages');
	}

	public function edit($id = NULL) {
        $affiliate = Affiliate::find($id);

        if( !$affiliate ) {
            $this->redirect( array('Affiliates::index') );
        }

        $data = $this->request->data;

        if( ($data) ) {
            //die(var_dump($data));
            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;
            $info['name'] = $data['affiliate_name'];
            $info['invitation_codes'] = array_values( $data['invitation_codes'] );
            if($data['active_pixel'] == '1' || $data['active_pixel'] == 'on'){
                $info['active_pixel'] = true;
			    $info['pixel'] = Affiliate::pixelFormating($data['pixel'], $info['invitation_codes']);
			   // die(var_dump($info['pixel']));
			}else{
			    $info['active_pixel'] = false;
			}
			$info['created_by'] = $affiliate->createdBy();
			$info['date_created'] = new MongoDate( strtotime( date('D M d Y') ) );

			if( ($affiliate->save($info)) ) {
				$this->redirect( array( 'Affiliates::index' ) );
			}
        }
        $sitePages = $this->sitePages;
        return compact('sitePages','affiliate');
	}
}

?>