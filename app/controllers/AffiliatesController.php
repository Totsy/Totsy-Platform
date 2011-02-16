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
	    $message = '';
	    $errors = 'Affiliate does not exists';

		if ($code) {
		    $count = Affiliate::count(array('conditions' => array('invitation_codes' => $code)));
            var_dump($count);
            if ( $count == 0 ){ return compact('success', 'errors'); }

            if($this->request->data){
                $data = $this->request->data;
                if(isset($data['password'])) {
                    // New user, need to register here
                    $user['firstname'] = $data['fname'];
                    $user['lastname'] = $data['lname'];
                    $user['email'] = strtolower($data['email']);
                    $user['zip'] = $data['zip'];
                    $user['confirmemail'] = strtolower($data['email']);
                    $user['password'] = $data['password'];
                    $user['terms'] = "1";
                    $user['invited_by'] = $code;
                    extract(UsersController::registration($user));
                    $success = $saved;
                    $errors = $user->errors();

                }
            }
             return compact('success','errors');
		}
	}

	/**
	    Affiliate-user invite register
	    @params $affiliate
	**/
	public function register($affiliate = NULL) {
	    $pdata = $this->request->data;
        $message = false;
        $user = User::create();
        $urlredirect = '';

        if ( ($affiliate)){
            $pixel = Affiliate::getPixels('after_reg', $affiliate);

           $gdata = $this->request->query;
            if( ($gdata) ){
                $affiliate = Affiliate::storeSubAffiliate($gdata, $affiliate);
                if (array_key_exists('url', $gdata)) {
                    $urlredirect = $gdata['url'];
                }
            }

            if( ($pdata) ) {

                $data['email'] = strtolower($pdata['email']);
                $data['firstname'] = $pdata['firstname'];
                $data['lastname'] = $pdata['lastname'];
                $data['email'] = htmlspecialchars_decode(strtolower($pdata['email']));
                $data['zip'] = $pdata['zip'];
                $data['confirmemail'] = htmlspecialchars_decode(strtolower($pdata['email']));
                $data['password'] = $pdata['password'];
                $data['terms'] = (boolean) $pdata['terms'];
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
                   Session::write('userLogin', $userLogin, array('name'=>'default'));
                   $ipaddress = $this->request->env('REMOTE_ADDR');
                    User::log($ipaddress);
                    if($affiliate == 'linkshare'){
                       if( array_key_exists('url', $gdata)){
                            $this->redirect(htmlspecialchars($gdata['url']));
                       }
                    }
                    Session::write('pixel',$pixel, array('name'=>'default'));
                    if(($urlredirect)) {
                        $this->redirect($urlredirect);
                    }else{
                        $this->redirect('/sales');
                    }
                }
            }
        }
	    $this->_render['layout'] = 'login';
        return compact('message', 'user');
	}
}

?>