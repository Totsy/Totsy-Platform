<?php

namespace app\controllers;

use totsy_common\models\Menu;
use lithium\storage\Session;
use app\controllers\BaseController;
use app\models\User;
use Mongo;
use MongoDate;
use MongoId;
use app\models\Base;
use app\extensions\Mailer;


class PagesController extends BaseController {

	/**
	 * Sets up the Menu element for the page
	 */
	protected function _init() {
		parent::_init();
	}

	public function view() {
		$path = func_get_args();
		
		if (empty($path)) {
			$path = array('home');
		}
		
		if ($path[0] == 'blog') {
			$this->redirect('http://totsyblog.blogspot.com', array('target' => '_blank'));
		} elseif ($path[0] == 'btrendie' || $path[0] == "living_social"){
			$this->_render['layout'] = 'blank';
		}
		$allowed = array('earthday', 'terms', 'faq', 'contact', 'privacy', 'aboutus', 'btrendie', 'moms', 'testimonials', 'being_green', 'press','affiliates','living_social', 'careers');
				
		$userCheck = Session::read('userLogin');
						
		if (empty($userCheck) && !in_array($path[0], $allowed)) {
			$this->redirect('/');
		}
				
		if($path[0] == "earthday"){
			if($_POST['earthdaybtn']){ 
				$data['earthdayemail'] = $_POST['email'];
				$data['postcomment'] = $_POST['comment'];
				
				//$insertdata = array(array('email'=> $postemail), array('comment' => $postcomment));
				
				//$m = new Mongo();
				//$totsy = $m->totsy;
				//$earthday = $totsy->earthday;
				//$earthday->save($insertdata);
				
				Mailer::send('zzzadmin', "jwidro@totsy.com", $data);
					
			
			}

		}
				
		if (in_array($path[0], $allowed) && $path[0] == "living_social") {
		    $today = date('m/d/Y');
		    if (!(($today >= '06/27/2011') && ($today <= '08/01/2011'))) {
		        $this->redirect('/');
		    }
		}
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia') {
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->render(array('template' => 'mobile_'.$path[0]));
		} else {
			$this->render(array('template' => $path[0]));
		}
	}
}

?>