<?php

namespace admin\models;

use lithium\analysis\Logger;
use MongoId;

class Affiliate extends Base {

	protected $_meta = array('source' => 'affiliates');

	protected $_schema = array(
			'_id' => array('type' => 'id'),
			'invitation_codes'=>array('type'=>'array', 'null'=>false ),
			'affiliate'=> array('type'=>'boolean', 'null'=>false, 'default'=>true),
			'active'=>array('type'=>'boolean', 'null'=>false, 'default'=>true)
			);

	public static function pixelFormating($pixels, $codes, $category) {
		if ( empty($pixels) ){ return array(); }
		$formatted = array();
		foreach($pixels as $key=>$pixel){

		    if ($pixel['enable'] == '1' || $pixel['enable'] == 'on') {
		    	$temp['enable'] = true;
		    } else {
		    	$temp['enable'] = false;
		    }

		    if ($pixel['enable'] && array_key_exists('page', $pixel)) {
		        if (empty($pixel['codes'])) { $pixel['codes'] = array('all');}
                if (in_array('all', $pixel['codes'])) {
                    $temp['codes'] = $codes;
                } else {
                    $temp['codes'] = $pixel['codes'];
                }
		    }

		    $temp['page'] = array_values($pixel['page']);
		    $temp['pixel'] = $pixel['pixel'];
		    $formatted[] = $temp;
		}
		return $formatted;
	}

	/* Gets all unique categories from the affiliates collection */
	public static function getCategories() {

		$affiliateCategories = array();

		$temp = Affiliate::collection()->find( array('affiliate'=>true), array('category' => true));

    	foreach($temp as $affCat) {
    		if(array_key_exists('category', $affCat)) {
    			if(is_array($affCat['category'])) {
    				foreach($affCat['category'] as $cat) {
    				    if(!in_array($cat['name'], $affiliateCategories)) {
    				    	if ($cat['name']) {
								$affiliateCategories[] = $cat['name'];
							}
    				    }
    				}
    			}
    		}
    	}

    	return $affiliateCategories;
	}

	public static function landingPageFormating($data) {
		if ( !array_key_exists('img', $data) && empty($data['img'])){
			return array();
		}
		$tmp = array();
		foreach($data['img'] as $img) {
			$category = $data['affiliate_category'][$img];
			$code = $data['apply_code'][$img];
			$tmp[] = array(
				'name' => $category,
				'code' => $code,
				'background_image' => $img,
			);
		}
		return $tmp;
	}

	public function attachImage($entity, $name, $id) {

	    Logger::debug("Getting ready to attach images");
		$id = (string)$id;
		$type = AffiliateImage::$types[$name];
		$key = array_keys($type);
		Logger::debug(implode(" ", $key));
		$dataset = array();
		if ($type['multiple']) {
			if (is_array($id)){
				Logger::debug('Attaching files `' . implode(', ', $id) .
					 "to `{$entity->_id }`");
				$data = array();
				foreach($id as $value){
					$data[] = array('background_image' => $id , 'name' => null);
				}
				$dataset = array('$pushAll' => array($type['field'] =>
					array('background_image' => $id , 'category' => null)
				));
			} else {
				Logger::debug("Multiple but not array. Attaching file `{$id}` to `{$entity->_id }`");
				Logger::debug("Pushing to {$type['field']}");
				$dataset = array('$push' => array($type['field'] =>
					array('background_image' => $id , 'name' => null)
				));
			}
		} else {
		    Logger::debug("Attaching file `{$id}` to `{$entity->_id }`");
		    $dataset = array('$push' => array(
		        $type['field'] => array(
		            'background_image' => $id ,
		            'name' => null
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
