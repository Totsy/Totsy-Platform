<?php

namespace admin\models;

use MongoDate;
use MongoId;
use admin\controllers\BaseController;
use lithium\analysis\Logger;

class Promocode extends Base {

	protected $_meta = array('source' => 'promocodes');

	/**
	 * Transform boolean form values to actual booleans
	 */
	public static function setToBool($var) {
		if ( $var == '1' || $var == 'on' ){
			return true;
		} else {
			return false;
		}
	}

	/**
	* Creates promocode
	* @param object $entity - The record or document object to be saved in the database.
	* @param array $code_data - data to be saved into the DB
	* @param array $extra_data - any extra data that needs to be saved as well
	* @param array $options - same options for Promocode::save();
	* @return boolean true if save was success, false if otherwise
	**/
	public function createCode($entity, $code_data = null, array $extra_data = array(), array $options = array()){
		$entity->description = $code_data['description'];
		$entity->code = $code_data['code'];
		$entity->type = $code_data['type'];
		if ($code_data['type'] != 'free_shipping') {
			$entity->discount_amount = (float) $code_data['discount_amount'];
		} else {
			$entity->discount_amount = (float) 0;
		}
		if (empty($code_data['max_total']) || $code_data['max_total'] == "UNLIMITED"){
			$entity->max_total = "UNLIMITED";
		} else {
			$entity->max_total = (int) $code_data['max_total'];
		}
		$entity->enabled = static::setToBool($code_data['enabled']);
		if (array_key_exists('limited_use', $code_data)) {
			$entity->limited_use = static::setToBool($code_data['limited_use']);
		}
		$entity->minimum_purchase = (int) $code_data['minimum_purchase'];
		$entity->max_use = (int) $code_data['max_use'];
		$entity->start_date = new MongoDate(strtotime($code_data['start_date']));
		$entity->end_date = new MongoDate(strtotime($code_data['end_date']));
		$entity->date_created = new MongoDate(strtotime(date('D M d Y')));
		$entity->created_by = static::createdBy();

		if (!empty ($extra_data)){
			foreach ($extra_data as $key => $value){
				$entity->{$key} = $value;
			}
		}

		return $entity->save();
	}

	/**
	* Create a parent promocode for unique generated promocodes
	* @param object $entity The record or document object to be saved in the database
	* @param array $code_data data to be saved into the DB
	* @see admin\models\Promocode::createCode()
	* @return object parent id
	**/
	public function createParent($entity,$code_data, array $options = array()){
		$entity->createCode($code_data, array('parent' => true), $options );
		return $entity->_id;
	}

	/**
	* Create a unique child promocodes with parent id attached
	* @param object $entity The record or document object to be saved in the database
	* @param array $code_data - data to be saved into the DB
	* @see admin\models\Promocode::createParent()
	* @return boolean true if save was success, false if otherwise
	**/
	public function createChild($entity, $code_data, $parent_id, array $options = array()) {
		return $entity->createCode($code_data, array(
			'parent_id' => new MongoId($parent_id),
			'special' => true
			),
			$options
		);
	}

    /**
    * Update Promocode
    * @param object $entity The record or document object to be saved in the database
    * @param array $code_data - data to be saved into the DB
    * @see admin\models\Promocode::createParent()
    * @return boolean true if save was success, false if otherwise
    **/
    public function updateCode ($entity, $code_data, array $options = array()){
        return $entity->createCode($code_data, $options);
    }
	/**
	* Returns the number of children of a given parent promocode
	* @param object $parent_id
	**/
	public static function countChildren($parent_id){
		$col = static::collection();
		return $col->count(array('parent_id' => $parent_id));
	}

	/**
	* Update parent promocode that was created using generate Promocode
	* @param object $entity The record or document object to be saved in the database
    * @param array $code_data - data to be saved into the DB
    * @see admin\models\Promocode::updateCode()
    * @see admin\models\Promocode::updateChildren()
    * @return boolean true if save was success, false if otherwise
	 */
	public function updateParent($entity, $code_data){
		$entity->updateCode($code_data);
		$entity->updateChildren($code_data);
	}

	/**
	 * @todo Document Me
	 */
	public function updateChildren($entity, $code_data){
		$children = static::find('all', array('conditions' => array(
				'special' => true,
				'parent_id' => $entity->_id
			)));
		$new_code = $code_data['code'];
		$original_data = $code_data;
		foreach ($children as $child){
			if (preg_match('/^' . $new_code . '/', $child->code)){
				$code_data['code'] = $child->code;
			}else{
				$len = strlen($child->code);
				//seven is the length of the random string
				$begin = $len - 7;
				$randomCode = substr($child->code,$begin, $len);
				$temp = $new_code . $randomCode;
				$code_data['code'] = $temp;
			}
			$child->createCode($code_data);
		}
	}
	/**
	 *
	**/
	public function massGeneratePromo($entity, $data = null){
	    $codes = array();
	    $parent_id = (string)$entity->_id;
        Logger::debug("Going to generate promocodes", array('name' => 'default'));
	    if ($data) {
	         gc_enable();
	        $loop_size = (int)$data['generate_amount'];
	        $i = 0;
            while( $i < $loop_size){
                $promoCode = static::create();
                $col = static::collection();
                do{
                    $code = $entity->code;
                    $rand = BaseController::randomString(7, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                    $code .= $rand;
                    $conditions = array('code' => $code, 'special' => true);
                    $amount = $col->count($conditions);
                }while($amount > 0);

                $data = $data;
                $data['code'] = $code;
                $promoCode->createChild($data, $parent_id);
                $codes[] = $promoCode->code;
               ++$i;
            }//end of loop
            gc_disable();
        }
        return $codes;
	}

	public static function changePromocodeStatus($data) {
	    $success = false;
        if ($data && array_key_exists('change_status', $data)) {
            $conditions = array();

            switch($data["change_status"]) {
                case 'disable':
                    $conditions = array('_id' => new MongoId($data['code_id']));
                    $data = array('$set' => array('enabled' => false));
                    break;
                case 'enable':
                    $conditions = array('_id' => new MongoId($data['code_id']));
                    $data = array('$set' => array('enabled' => true));
                    break;
            }
            if(!empty($conditions)) {
                $success = Promocode::update($data,$conditions);
            }

        }
        return $success;
	}
}

?>