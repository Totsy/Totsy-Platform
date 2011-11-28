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
	    'Affiliates::register' => 'landing page',
	    'after_reg' => 'after registering',
	    'Users::login' => 'login',
	    'Events::index' => 'sales',
	    'product' => 'product page',
	    'event' => 'event page',
	    'Orders::view' => 'orders confirmation',
	    'order' =>'order confirmation(spinback)',
        'invite' => 'invite page(spinback)'
	    );

	public $packages = array(
	    'regular' => 'regular',
	    'super' => 'super',
	);

	public function index() {

	   $affiliates = Affiliate::collection()->find(array('affiliate'=>true), array(
       'date_created' => true,
       'created_by' => true,
       'active' => true,
       'name' => true,
       'active_pixel' => true,
       'level' => true
       ));

	   $userCollection = User::collection();
	   $afs = array();

       foreach($affiliates as $affiliate){
            $obj_data = $affiliate;
            if(!empty( $obj_data['date_created'] )) {
				$obj_data['date_created'] = date( 'm/d/Y', $affiliate['date_created']->sec);
            }

            if(!empty( $obj_data['created_by'] )) {
              if (strlen($obj_data['created_by']) > 10) {
                     $user = $userCollection->findOne( array('_id' => new MongoId($obj_data['created_by'])) );
                } else {
                    $user = $userCollection->findOne( array('_id' => $obj_data['created_by']) );
               }

                if (array_key_exists('firstname', $user)) {
                    $obj_data['created_by'] = $user['firstname'] . ' ' . $user['lastname'];
                } else {
                    $obj_data['created_by'] = $user['email'];
                }
            }
            $afs[] = $obj_data;
            unset($obj_data);
        }
        $affiliates = $afs;
        return compact('affiliates');
	}

	/**
	* Adds a new affiliate in the collection.  Admin user can only create one landing page when
	* creating affiliate.
	* @return if the a landing page was created for the affiliate, the admin user is redirected
	* to the edit page of that affiliate.  Otherwise, the user is redirected to the index page.
	* @see admin\models\Affiliate::pixelFormating()
	*/
	public function add() {
        $info = array();
        $landing = array();
       	$data = $this->request->data;
       	if (!array_key_exists('affiliate_id', $data) && empty($data['affiliate_id'])) {
			$prospective_id = new MongoId();
			$affiliate = Affiliate::create();
			$affiliate->_id = $prospective_id;
		} else {
			$affiliate = Affiliate::create();
			$affiliate->_id = $data['affiliate_id'];
		}

		if ($data) {
            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;
            $info['name'] = $data['affiliate_name'];

            $info['level'] = $data['level'];
            $info['invitation_codes'] = array_values($data['invitation_codes']);

            if ($info['level'] != 'regular') {
                $info['active_pixel'] = (boolean) $data['active_pixel'];

                if ($info['active_pixel']) {
			        $info['pixel'] = Affiliate::pixelFormating($data['pixel'],
			                                                $info['invitation_codes'],
			                                                $info['category']
			                                                );
			    }

			    $info['active_landing'] = (($data['active_landing'] == '1' || $data['active_landing'] == 'on')) ? true : false;
			}

			$info['created_by'] = Affiliate::createdBy();
			$info['date_created'] = new MongoDate();

       		if($affiliate->save($info)) {
                $this->redirect(array('Affiliates::edit', 'args' => array($affiliate->_id)));
            }
		}

		$sitePages = $this->sitePages;
		$packages = $this->packages;

        return compact('affiliate','sitePages','packages', 'prospective_id');
	}
	/**
	* Edits Affiliate information
	* @param string $id - document id
	*/
	public function edit($id = NULL) {
        $affiliate = Affiliate::find($id);
       	$affiliateCategories = Affiliate::getCategories();
       	$info = array();

        if(!$affiliate) {
            $this->redirect( array('Affiliates::index') );
        }
        $data = $this->request->data;
        if( ($data) ) {
            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;
            $info['name'] = $data['affiliate_name'];
            $info['level'] = $data['level'];
            $info['invitation_codes'] = array_values( $data['invitation_codes'] );
            if($info['level'] != 'regular'){
                $info['active_pixel'] = (($data['active_pixel'] == '1' || $data['active_pixel'] == 'on')) ? true : false;
                $info['active_landing'] = (($data['active_landing'] == '1' || $data['active_landing'] == 'on')) ? true : false;
				if ($info['active_landing']) {
					$info['category'] = Affiliate::landingPageFormating($data);
				}
                if($info['active_pixel']){
			        $info['pixel'] = Affiliate::pixelFormating($data['pixel'],
			                            $info['invitation_codes'],
			                            $info['category']
			                            );
			    }
			}
			$info['created_by'] = $affiliate->createdBy();
			$info['date_created'] = new MongoDate(strtotime(date('D M d Y')));

			if( ($affiliate->save($info)) ) {
				$this->redirect( array( 'Affiliates::index' ) );
			}
        }

        $sitePages = $this->sitePages;
		$packages = $this->packages;
        return compact('sitePages', 'packages','affiliate', 'affiliateCategories');
	}
	/**
	* Ajax call to dynamically retrieve all images taged as background
	* @see \admin\models\Affiliate::retrieveBackgrounds()
	*/

	/*
	public function background() {
        $data = Affiliate::retrieveBackgrounds();
        $this->render(array('layout' => false));

        foreach($data as $value){
            $backgrounds[] = $value['_id'];
        }
        if(empty($data)){
            $backgrounds = array("success"=>"false");
        }
        echo json_encode($backgrounds);
	}
	*/

    /**
	* Ajax call to dynamicall retrieve landing page information
	* Expected post data: affiliate id and the name of the landing page
	**/

	/*
	public function retrieveLanding(){
	    $this->render(array('layout' => false));
	    if($this->request->data){
	        $affiliate_id = $this->request->data['affiliate'];
	        $landing_page = $this->request->data['name'];
            $affiliate = Affiliate::find($affiliate_id);
            $data = $affiliate->data();
            $landing = $data['landing'];
            $page = array();
            foreach ($landing as $key => $value) {
                if ($value['name'] == $landing_page) {
                    $value['key'] = $key;
                    $page = $value;
                    break;
                }
            }
            echo json_encode($page);
	    }
	}*/

	/**
	* Ajax call to dynamically save a new or edited landing page
	*/

	/*
	public function saveLanding(){
	    $this->render(array('layout' => false));
	    if ($this->request->data) {
            $affiliate = Affiliate::find($this->request->data['aid']);
            $data = $this->request->data;
            if ($data['active_landing']) {
                $landing['name'] = $data['name'];
                $landing['template'] = $data['template_type'];
                $landing['enabled'] = (bool) $data['landing_enable'];
                $landing['background_img'] = $data['background_img'];
                $landing['url'] = $data['url'];
                $landing['headline'] = array(
                    $data['headline_1'],
                    $data['headline_2']
                    );
                $landing['bullet'] = array(
                    $data['bullet_1'],
                    $data['bullet_2'],
                    $data['bullet_3'],
                    $data['bullet_4']
                    );
                // If the landing page is edited or new
                if ($this->request->data['index'] != "new") {
                    $key = $data['index'];
                    $pages = $affiliate->landing->data();
                    $pages[$key] = $landing;
                    $affiliate->landing = $pages;
                } else {
                    //
                    $affiliate->landing[] = $landing;
                }
                if ($affiliate->save()){
                    echo json_encode(true);
                } else {
                    echo json_encode(false);
                }
            }
	    }
	}*/
}

?>
