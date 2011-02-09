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
	    '/a/' => 'landing page',
	    '/' => 'login',
	    '/sales' => 'sales',
	    'product' => 'product page',
	    'event' => 'event page ',
	    '/shopping/checkout' => 'checkout',
	    '/shopping/process' => 'checkout process',
	    '/orders/view' => 'orders confirmation'

	    );

	public $packages = array(
	    'regular' => 'regular',
	    'super' => 'super',
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

            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;
            $info['name'] = $data['affiliate_name'];
            $info['level'] = $data['level'];
            $info['invitation_codes'] = array_values( $data['invitation_codes'] );
            if($info['level'] != 'regular'){
                $info['active_pixel'] = (boolean) $data['active_pixel'];
			    $info['pixel'] = Affiliate::pixelFormating($data['pixel'],
			                                                $info['invitation_codes']
			                                                );
			}

			$info['created_by'] = $affiliate->createdBy();
			$info['date_created'] = new MongoDate( strtotime( date('D M d Y') ) );

			if( ($affiliate->save($info)) ) {
				$this->redirect( array( 'Affiliates::index' ) );
			}
		}
		$sitePages = $this->sitePages;
		$packages = $this->packages;
        return compact('sitePages', 'packages');
	}

	public function edit($id = NULL) {
        $affiliate = Affiliate::find($id);

        if( !$affiliate ) {
            $this->redirect( array('Affiliates::index') );
        }

        $data = $this->request->data;

        if( ($data) ) {

            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;
            $info['name'] = $data['affiliate_name'];
            $info['level'] = $data['level'];
            $info['invitation_codes'] = array_values( $data['invitation_codes'] );
            if($info['level'] != 'regular'){
                $info['active_pixel'] = (boolean) $data['active_pixel'];
			    $info['pixel'] = Affiliate::pixelFormating($data['pixel'],
			                            $info['invitation_codes']
			                            );
			}
			$info['created_by'] = $affiliate->createdBy();
			$info['date_created'] = new MongoDate( strtotime( date('D M d Y') ) );

			if( ($affiliate->save($info)) ) {
				$this->redirect( array( 'Affiliates::index' ) );
			}
        }
        $sitePages = $this->sitePages;
		$packages = $this->packages;
        return compact('sitePages', 'packages','affiliate');
	}
}

?>