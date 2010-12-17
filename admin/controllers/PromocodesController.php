<?php

namespace admin\controllers;

use \admin\models\Promocode;
use \admin\models\Promotion;
use \admin\models\User;
use li3_flash_message\extensions\storage\FlashMessage;
use MongoDate;
use MongoRegex;

class PromocodesController extends \lithium\action\Controller {

	public function index() {
        
        $promocodes = Promocode::all();
       // var_dump( get_class( $promocodes) );
       foreach($promocodes as $promocode){
          
          $obj_data = $promocode->data();
          
         // var_dump($obj_vars);
           if(!empty( $obj_data['start_date'] )) {
                $promocode->start_date = date('m/d/Y', $promocode->start_date->sec );
            }
            
            if(!empty( $obj_data['end_date'] )) {
                $promocode->end_date = date('m/d/Y', $promocode->end_date->sec );
            }
           
            if(!empty( $obj_data['date_created'] )) {
                $promocode->date_created= date( 'm/d/Y', $promocode->date_created->sec);
            }
            
        }
        
		return compact('promocodes');
	}

	public function view($code = null) {
       // var_dump($code);
		$promocodes = Promocode::find( 'all', array( 
                                                'conditions' =>array( 
                                                                '$or' =>  array( 
                                                                    array( 'code' => strtolower($code) ),
                                                                    array( 'code' => strtoupper($code) )
                                                    ) ) ) );
      //  var_dump($promocode->data());
        $promocodes= $promocodes->data();
		return compact('promocodes');
	}
    
    public function report() {
        FlashMessage::clear();
        $data = $this->request->data;
               
        if( empty($data) ){
           
            $promotions = Promotion::all();
            $promocodes= Promocode::all();
        
        }else{
           
            $search = $data['search'];
            if( !empty($data['search']) ) {
                     
                    $search = $data['search'];
                    $promotions  = Promotion::find(  'all', array( 
                                                    'conditions' =>array( 
                                                                    '$or' =>  array( 
                                                                        array( 'code' => strtolower($search) ),
                                                                        array( 'code' => strtoupper($search) )
                                                        ) ) ) );
                    $promocodes = Promocode::all( array( 
                                                    'conditions' =>array( 
                                                                    '$or' =>  array( 
                                                                        array( 'code' => strtolower($search) ),
                                                                        array( 'code' => strtoupper($search) )
                                                        ) ) ) );
            }
            
            if( !empty( $data['start'] ) && !empty($data['end']) ) {
                 
                $start = new MongoDate( strtotime($data['start']) );
                $end = new MongoDate( strtotime($data['end']) );
                $promotions  = Promotion::find(  'all', array( 'conditions'=>array('date_created' =>array( '$gt' => $start, '$lte' => $end ) ) ) );
            }
            
            if (!empty($promotions)) {
                FlashMessage::set('Results Found', array('class' => 'pass'));
            } else {
                FlashMessage::set('No Results Found', array('class' => 'warning'));
            }
            
        }
        
        foreach($promotions as $promotion){
          
          $obj_data = $promotion->data();
          
            if( !empty( $obj_data['date_created'] ) ) {
                $promotion->date_created= date( 'm/d/Y', $promotion->date_created->sec);
            }
        }
        
        return compact('promotions', 'promocodes');
	}

	public function add() {
        
		$promoCode= Promocode::create();
        $code = $this->request->data;
        $admins = User::all( array( 'conditional'=>array('admin' => true) ) );
        
        
        if( !empty($code) ) {
            
           $promocode = Promocode::all( array( 'conditions'=>array( 'code' => $code['code'] ) ) );
                
                if ($promocode->data() ) {
                    $message = 'Coupon Code Already Exists';
                    return compact('message');
                }   
                  
                  if( $this->request->data['enabled'] !== '1' ){
                      
                       $this->request->data['enabled'] = false;
                    
                    }else{
                        
                         $this->request->data['enabled'] = true;
                    
                    }
                
                   $code['start_date']= new MongoDate( strtotime( $code['start_date'] ) );
                   $code['end_date']= new MongoDate( strtotime( $code['end_date'] ) );
                   $code['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
                    
                   if( $promoCode->save($code) ){
                       
                        $this->redirect('Promocodes::index');
                        
                   }
                    
           }
           
           return compact('admins');
	}

	public function edit($id=NULL) {
		$promocode = Promocode::find($id);

		if (!$promocode) {
            
			$this->redirect('Promocodes::index');
            
		}
        
        $obj_data = $promocode->data();
        
        if(!empty( $obj_data['start_date'] )){
                $promocode->start_date = date('m/d/Y', $promocode->start_date->sec );
        }
            
        if(!empty( $obj_data['end_date'] )){
            $promocode->end_date = date('m/d/Y', $promocode->end_date->sec );
        }
        
        
		if ( $this->request->data ) {
            
               $data = $this->request->data;
               
               if( $data['enabled'] !== '1' ){
                          
                    $data['enabled'] = false;
            
                }else{
                
                    $data['enabled'] = true;
                
                }
           
                $admins = User::all( array( 'conditional'=>array('admin' => true) ) );
                           
               $data['start_date']= new MongoDate( strtotime( $data['start_date'] ) );
               $data['end_date']= new MongoDate( strtotime( $data['end_date'] ) );
               $data['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
                
                $promocode->save($data);
                
                $this->redirect( array( 'Promocodes::index' ) );
            
		}
        
		return compact('promocode', 'admins');
	}
}

?>