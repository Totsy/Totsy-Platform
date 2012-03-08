<?php

namespace app\models;

use MongoDate;
use app\models\Promocode;
use app\models\Cart;
use lithium\storage\Session;

class Promotion extends Base {

	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * The confirm method checks the total number of times the promotion code
	 * has been used by an individual user
	 * @param $code
	 * @param $user
	 */
	public static function confirmCount($code_id, $user) {
		return static::count(array(
			'conditions' => array(
				'code_id' => (string) $code_id,
				'user_id' => $user
		)));
	}
	/**
	 * The confirm no of uses method checks the TOTAL number of times the promotion code
	 * has been used
	 * @param $code
	 */
	public static function confirmNoUses($code_id, $user) {
		return static::count(array(
			'conditions' => array(
				'code_id' => (string) $code_id,
				'user_id' => array('$ne' => $user)
		)));
	}
	public function promoCheck($entity, $code, $user, array $variables, $isOnlyDigital = false){
            extract($variables);
            $entity->code = $code;
            $success = false;
            if ($entity->code) {
                $code = Promocode::confirmCode($code);
                $entity->code = $code->code;
                if ($code) {
                    $count = static::confirmCount($code->_id, $user['_id']);
                    $uses = static::confirmNoUses($code->_id, $user['_id']);
                    if ($code->max_use > 0) {
                        if ($count >= $code->max_use) {
                            $entity->errors(
                                $entity->errors() + array(
                                    'promo' => "This promotion code has already been used"
                            ));
                        }
                    }
                    if ($code->max_total !== "UNLIMITED") {
                        if ($uses >= $code->max_total) {
                            $entity->errors(
                                $entity->errors() + array(
                                    'promo' => "This promotion code has already been used"
                            ));
                        }
                    }
                    if ($code->limited_use == true) {
                        $userPromotions = ($user->promotions) ? $user->promotions->data() : null;
                        if (!is_array($userPromotions) || !in_array((string) $code->_id, $userPromotions)) {
                            $entity->errors(
                                $entity->errors() + array(
                                    'promo' => "Your promotion code is invalid"
                            ));
                        }
                    }
                    if ($code->type == 'free_shipping' && !empty($services['freeshipping']['enable'])) {
						$entity->errors(
                           $entity->errors() + array(
                         		'promo' => "You have already used a shipping discount"
                        ));
					}	
                    if ($subTotal >= $code->minimum_purchase && !($entity->errors())) {
                        $entity->user_id = $user['_id'];
                        if ($code->type == 'percentage') {
                        	$entity->type = "percentage";
                            $entity->saved_amount = $subTotal * -$code->discount_amount;
                            Cart::updateSavings(null, 'discount', $subTotal * $code->discount_amount);
                        }
                        if ($code->type == 'dollar') {
                        	#If Negative total after discount, record correct used discount amount
                        	$test = $subTotal - $code->discount_amount;
                        	if($test < 0) {
                        		$entity->saved_amount = -$subTotal;
                        	} else {
                        		$entity->saved_amount = -$code->discount_amount;
                        	}
                        	$entity->type = "dollar";
                            Cart::updateSavings(null, 'discount', -$entity->saved_amount);
                        }
                        if ($code->type == 'free_shipping' && !($entity->errors()) && empty($services['freeshipping']['enable']) && !$isOnlyDigital) {
                            $entity->type = "free_shipping";
                           	$entity->saved_amount = -(7.95);
                            Cart::updateSavings(null, 'discount', 7.95);
                        }
                        Session::write('promocode', $code->data(), array('name' => 'default'));   
                    } else {
                        $entity->errors(
                            $entity->errors() + array(
                                'promo' => "You need a minimum order total of $$code->minimum_purchase to use this promotion code. Shipping and sales tax is not included."
                        ));
                    }
                } else {
                    $entity->errors(
                        $entity->errors() + array(
                            'promo' => 'Your promotion code is invalid'
                    ));
                }
                $errors = $entity->errors();
                if ($errors) {
                	Session::delete('promocode');
                	Cart::updateSavings(null, 'discount', 0);
                    $entity->saved_amount = 0;
                } else{
				    $entity->code_id = (string) $code->_id;
				    $entity->date_created = new MongoDate();
                    $success = true;
                }
                return $success;
            }
    }
}

?>