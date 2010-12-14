<?php

namespace admin\controllers;

use \admin\models\Promocode;
use \admin\models\Promotion;
use MongoDate;

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
		$promocode = Promocode::all( array( 'conditions' => array( 'code'=> strtolower($code) ) ) );
        //var_dump($promocode);
		return compact('promocode');
	}
    
    public function report() {
		$promotions = Promotion::all();
        
        foreach($promotions as $promotion){
          
          $obj_data = $promotion->data();
          
         // var_dump($obj_vars);
                     
            if( !empty( $obj_data['date_created'] ) ) {
                $promotion->date_created= date( 'm/d/Y', $promotion->date_created->sec);
            }
            
        }
        
		return compact('promotions');
	}

	public function add() {
        
		$promoCode= Promocode::create();
        $code = $this->request->data;
         
              
          if( !empty($code) ) {
            
           $promocode = Promocode::all( array( 'conditions'=>array( 'code' => $code['code'] ) ) );
                
           // var_dump($promocode->data());
                
                
                if ($promocode->data() ) {
                    $message = 'Coupon Code Already Exists'  ;
                    return compact('message');
                    
                }   
                 
                  
                  if( $this->request->data['enabled'] !== '1' ){
                      
                       $this->request->data['enabled'] = false;
                    
                    }else{
                    
                         $this->request->data['enabled'] = true;
                    
                    }
                   
                   var_dump( strtotime( $code['start_date'] )  );
                   
                   $code['start_date']= new MongoDate( strtotime( $code['start_date'] ) );
                   $code['end_date']= new MongoDate( strtotime( $code['end_date'] ) );
                   $code['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
                    
                   if( $promoCode->save($code) ){
                       
                        $this->redirect('Promocodes::index');
                        
                   }
                    
           }
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
           
           
               $data['start_date']= new MongoDate( strtotime( $data['start_date'] ) );
               $data['end_date']= new MongoDate( strtotime( $data['end_date'] ) );
               $data['date_created']= new MongoDate( strtotime( date('D M d Y') ) );
                
                $promocode->save($data);
                
                $this->redirect( array( 'Promocodes::index' ) );
            
		}
        
		return compact('promocode');
	}
}

?>