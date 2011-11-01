<?php
namespace admin\controllers;

use lithium\net\socket\Curl;

use MongoRegex;
use MongoDate;
use MongoCode;
//use admin\controllers\BaseController;
use admin\models\EmailsBounced;

class BouncedemailsController extends \lithium\action\Controller {
	
	public function index() {
		
		if (!empty($this->request->data)){
			
			
			if (!empty($this->request->data['search'])){
				$conditions['invited_by'] = $this->request->data['search'];
			}
			if (!empty($this->request->data['bounce'])){
				$conditions['engagement'] = $this->request->data['bounce'].'bounce';	
			}
			
			if (!empty($this->request->data['start_date']) && !empty($this->request->data['end_date'])) {
				$conditions['date']['$gt'] = new MongoDate(strtotime($this->request->data['start_date']));
				$conditions['date']['$lt'] = new MongoDate(strtotime($this->request->data['end_date']));
			} else if (!empty($this->request->data['todays'])) {
				$conditions['date']['$gt'] = new MongoDate(strtotime('-1 day'));
				$conditions['date']['$lt'] = new MongoDate(strtotime('+1 day'));
			} 
			
			$keys = array('invited_by' => true);
			$inital = array('total'=>0);
			$reduce = new MongoCode("function(a,b){
				if ((b['invited_by']) == b.invited_by){
					b.total++;
				}
			}");
			
			$cursor = EmailsBounced::collection()->group($keys, $inital, $reduce, $conditions);;

			$return = $this->request->data;
			$return['key'] = sha1(microtime().'-'.mt_rand());
			if ($cursor['count']>0){
				$return['retval'] = $cursor['retval'];
			}
			return $return;
		}

	}
	
}