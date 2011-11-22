<?php

namespace admin\models;

use lithium\util\Validator;
use lithium\analysis\Logger;
use MongoId;

class Banner extends Base {

    protected $_meta = array('source' => 'banners');

	public $validates = array(
		'name' => array(
			array('notEmpty', 'required' => true, 'message' => 'Please add a banner name'),

		),
		'end_date' => array(
			array('notEmptyArray', 'required' => true, 'message' => 'Please enter an end date'),

		)
	);

    public static function __init(array $options = array()) {
		parent::__init($options);

		Validator::add('notEmptyArray', function ($value) {
			return (empty($value)) ? false : true;
		});
	}

	public function attachImage($entity, $name, $id) {
	    Logger::debug("Getting ready to attach images");
		$id = (string)$id;
		$type = BannerImage::$types[$name];
		$key = array_keys($type);
		Logger::debug(implode(" ", $key));
		$dataset = array();
		if ($type['multiple']) {
			if (is_array($id)){
				Logger::debug('Attaching files `' . implode(', ', $id) .
					 "to `{$entity->_id }`");
				$data = array();
				foreach($id as $value){
					$data[] = array('_id' => $value , 'newPage' => false);
				}
				$dataset = array('$pushAll' => array($type['field'] => $data));
			}else {
				Logger::debug("Attaching file `{$id}` to `{$entity->_id }`");
				$dataset = array('$push' => array($type['field'] =>
					array('_id' => $id , 'newPage' => false)
				));
			}
		} else {
		    Logger::debug("Attaching file `{$id}` to `{$entity->_id }`");
		    $dataset = array('$push' => array(
		        $type['field'] => array(
		            '_id' => $id ,
		            'newPage' => false
		            )
				));
		}

		static::collection()->update(
			array('_id' => new MongoId($entity->_id)),
			$dataset
		);

		return static::first(array(
			'conditions' => array('_id' => $entity->_id)
		));
	}
}

?>
