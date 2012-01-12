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
	    'after_reg' => 'after registering',
	    '/' => 'login',
	    '/sales' => 'sales',
	    'product' => 'product page',
	    'event' => 'event page',
	    '/shopping/checkout' => 'checkout',
	    '/shopping/process' => 'checkout process',
	    '/orders/view' => 'orders confirmation',
	    'order' =>'order confirmation(spinback)',
        'invite' => 'invite page(spinback)'
	    );

	public $packages = array(
	    'regular' => 'regular',
	    'super' => 'super',
	);
	public $templates = array(
        'temp_1' => 'Template One',
        'temp_2' => 'Template Two'
    );

	public function index() {

	  $affiliates = Affiliate::collection()->find(array('affiliate'=>true), array(
	    'date_created' => true,
	    'created_by' => true,
	    'active' => true,
	    'name' => true,
	    'active_pixel' => true,
	    'level' =>true
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

	/* Gets all unique categories from the affiliates collection */
	public function getCategories() {

		$affiliateCategories = array();

		$temp = Affiliate::collection()->find( array('affiliate'=>true), array(
    	'date_created' => true,
    	'created_by' => true,
    	'active' => true,
    	'name' => true,
    	'category' => true,
    	'level' => true
    	));

    	foreach($temp as $affCat) {
    		if(array_key_exists('category', $affCat)) {
    			if(is_array($affCat['category'])) {
    				foreach($affCat['category'] as $cat) {
    				    if(!in_array($cat['name'], $affiliateCategories)) {
    				    	$affiliateCategories[] = $cat['name'];
    				    }
    				}
    			}
    		}
    	}

    	return $affiliateCategories;
	}

	/**
	* Adds a new affiliate in the collection.  Admin user can only create one landing page when
	* creating affiliate.
	* @return if the a landing page was created for the affiliate, the admin user is redirected
	* to the edit page of that affiliate.  Otherwise, the user is redirected to the index page.
	* @see admin\models\Affiliate::pixelFormating()
	*/
	public function add() {

       	$affiliate = Affiliate::create();
       	$affiliateCategories = $this->getCategories();

        $info = array();
        $landing = array();
       	$data = $this->request->data;

		if ($data) {
            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;
            $info['name'] = $data['affiliate_name'];

            if(isset($data['affiliate_category']) && isset($data['background_image'])) {
            	$info['category'][] = array('name' => $data['affiliate_category'], 'background_image' => $data['background_image']
);
            } else {
            	$info['category'] = "";
            }

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

                $info['landing'] = array();
                $landing['name'] = $data['name'];

                $landing['enabled'] = (bool) $data['landing_enable'];
                //$landing['url'] = $data['url'];
                $info['landing'][] = $landing;
			}

			$info['created_by'] = $affiliate->createdBy();
			$info['date_created'] = new MongoDate( strtotime( date('D M d Y') ) );

       		if (isset($info['name']) && isset($info['category'])) {
       			$getAff = Affiliate::find('first',
					array('conditions' => array(
						'name'=> $info['name'], $info['category'])
				));

				if ($getAff) {
					print "This category already exists for this affiliate code, please choose a different category";
				} else {
					$affiliate->save($info);
				}
			}
		}

		$sitePages = $this->sitePages;
		$packages = $this->packages;
		$templates = $this->templates;

        return compact('sitePages', 'affiliateCategories' ,'packages', 'templates', 'template');
	}
	/**
	* Edits Affiliate information
	* @param string $id - document id
	*/
	public function edit($id = NULL) {
        $affiliate = Affiliate::find($id);
       	$affiliateCategories = $this->getCategories();
       	$info = array();

        if(!$affiliate) {
            $this->redirect( array('Affiliates::index') );
        }
        $data = $this->request->data;

        if($data) {
        	$i = 0;

			foreach($data as $record=>$val) {

				if(strrpos($record, "_category_name") > 0 ) {
					$i++;
					$info['category'][$i]['name'] = $val;
				}

				if(strrpos($record, "_category_background") > 0 ) {
					$info['category'][$i]['background_image'] = $val;
				}
			}

            $info['active'] = (($data['active'] == '1' || $data['active'] == 'on')) ? true : false;

            $info['name'] = $data['affiliate_name'];
            $info['level'] = $data['level'];
            $info['invitation_codes'] = array_values( $data['invitation_codes'] );
            if($info['level'] != 'regular'){
                $info['active_pixel'] = (boolean) $data['active_pixel'];
                $info['active_landing'] = (boolean) $data['active_landing'];
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
		//checks if certain keys exists
		if ($affiliate->key('active_landing')) {
		    $affiliate->active_landing = false;
		}
		if ($affiliate->key('active_pixel')) {
		    $affiliate->active_pixel = false;
		}
		$affiliate = $affiliate->data();
		$landing = array();
		/*
		* Just retrieving name and url of landing pages connected to the affiliate, if applicable
		*/
		if (array_key_exists('landing', $affiliate)){
            foreach ($affiliate['landing'] as $values) {
                $temp = array();
                $temp['name'] = $values['name'];
                $temp['url'] = $values['url'];
                $temp['background_image'] = $values['background_image'];
                $landing[] = $temp;
            }
		}
		$affiliate['landing'] = $landing;
        return compact('sitePages', 'packages','affiliate', 'affiliateCategories');
	}
}

?>