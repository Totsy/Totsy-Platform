<?php

namespace app\controllers;

use app\models\Affiliate;
use app\models\User;
use MongoDate;
use lithium\storage\Session;

class AffiliatesController extends BaseController {

    /**
    * Affiliate registration from remote POST.
    * @params string $code
    * @return boolean $success
    **/
	public function registration($code = NULL) {
	    $success = false;
		if ($code) {
            if($this->request->data){
                $data = $this->request->data;
                if(isset($data['password'])) {
                    // New user, need to register here
                    $user['firstname'] = $data['fname'];
                    $user['lastname'] = $data['lname'];
                    $user['email'] = strtolower($data['email']);
                    $user['zip'] = $data['zip'];
                    $user['confirmemail'] = $data['email'];
                    $user['password'] = $data['password'];
                    $user['terms'] = "1";
                    $user['invited_by'] = $code;
                    extract(UsersController::registration($user));
                    $success = $saved;
                }
            }

             return compact('success');
		}
	}

	/**
	    Affiliate-user invite register
	    @params
	**/
	public function register($affiliate = NULL) {
	    $pdata = $this->request->data;
        $message = false;
        $user = User::create();

        if( ($affiliate)){
            if($affiliate == 'w4') {
               $gdata = $this->request->query;
                if( ($gdata) ){

                    $subaff = $gdata['subid'];
                    $conditions = array('invitation_codes' => 'w4');
                    $col = Affiliate::collection();
                    if($col->count($conditions) > 0) {
                        $col->update($conditions, array(
                                '$addToSet' => array(
                                    'sub_affiliates' => $subaff
                                )));
                    }
                    $affiliate = $subaff;
                }
            }
            if( ($pdata) ) {

                $data['email'] = strtolower($pdata['email']);
                $data['firstname'] = $pdata['firstname'];
                $data['lastname'] = $pdata['lastname'];
                $data['email'] = strtolower($pdata['email']);
                $data['zip'] = $pdata['zip'];
                $data['confirmemail'] = $pdata['email'];
                $data['password'] = $pdata['password'];
                $data['terms'] = "1";
                $data['invited_by'] = $affiliate;
                extract(UsersController::registration($data));
                if($saved) {
                     $message = $saved;
                    $userLogin = array(
                        '_id' => (string) $user->_id,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'zip' => $user->zip,
                        'email' => $user->email
                    );
                    Session::write('userLogin', $userLogin);
                   $ipaddress = $this->request->env('REMOTE_ADDR');
                    User::log($ipaddress);
                    $this->redirect('/');
                }
            }
        }

	    $this->_render['layout'] = 'login';
        return compact('message', 'user');
	}
}

?>