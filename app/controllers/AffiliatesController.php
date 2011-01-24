<?php

namespace app\controllers;

use \app\models\Affiliate;

class AffiliatesController extends \lithium\action\Controller {

    /**
        Affiliate registration from remote POST.  Of course this need to
    **/
	public function registration($code=NULL) {
	    $success = false;
		if($code) {
            if($this->request->data){
                $data = $this->request->data;
                $data['date_created'] = new MongoDate();
                $data['invited_by'] = $code;

                if(isset($data['password'])){
                    // New user, need to register here
                    $user['firstname'] = $data['firstname'];
                    $user['lastname'] = $data['lastname'];
                    $user['email'] = $data['email'];
                    $user['confirmemail'] = $data['confirmemail'];
                    $user['password'] = $data['password'];
                    $user['terms'] = $data['terms'];
                    $user['invited_by'] = $data['invited_by'];
                    $success = UsersController::registration($user);
                }
            }
             die($success);
		}

	}

}

?>