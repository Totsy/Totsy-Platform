<?php

namespace app\models;

use app\Models\Order;
use app\Models\Item;

class Affiliate extends Base {

    protected $_meta = array('source'=> 'affiliates');
	public $validates = array();

	public static function getPixels($url, $invited_by) {
        $orderid = NULL;
        if(strpos($url, '&')) {
            $url = substr($url,0,strpos($url, '&'));
        }
        if(preg_match('(/orders/view/)',$url)) {
            $orderid = substr($url,13);
            $url = '/orders/view';
        }

        $conditions['active'] = true;
        $conditions['pixel'] = array('$elemMatch' => array(
                                    'page' => $url,
                                    'enable' => true
                                ));
        $conditions['invitation_codes'] = $invited_by;

		$options = array('conditions' => $conditions,
		                'fields'=>array(
		                    'pixel.pixel' => 1, 'pixel.page' => 1
		                    ));
		$pixels = Affiliate::find('all', $options );
		$pixels = $pixels->data();

		$pixel = NULL;

		foreach($pixels as $data) {
			foreach($data['pixel'] as $index) {

                if(in_array($url, $index['page'])) {
                    if($url == '/orders/view'){
                        $pixel .= static::generatePixel($invited_by, $index['pixel'], array( 'orderid' => $orderid));
                    }else{
                        $pixel .= static::generatePixel($invited_by, $index['pixel']);
                    }
				}
			}
		}
		return $pixel;
	}

	protected static function randomString($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)) {
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		return $string;
	}

    public static function generatePixel($invited_by, $pixel, $options = array()) {

        if($invited_by == 'w4'){
            $transid = 'totsy' . static::randomString();
            return '<br/>' . str_replace('$', $transid,$pixel );
        }else if($invited_by == 'spinback' && ($options)) {
            $insert = '';
            if (array_key_exists('orderid', $options) && ($options['orderid'])) {
                $orderid = $options['orderid'];
                $order = Order::find('all', array('conditions' => array('order_id' => $orderid)));
                $order = $order->data();
                if(($order)) {
                    $insert = 'oid =' . $orderid;
                    $insert .= ' total=' . $order[0]['subTotal'];
                }
                return str_replace('$' , $insert, $pixel);
            }

            if(array_key_exists('product', $options) && ($options['product'])) {
                $product = $options['product'];
                $last = strrpos($product, '/');
                $item = substr($product, $last + 1);
                $item = Item::find('first', array(
                    'conditions' => array(
                        'enabled' => true,
                        'url' => $item),
                    'order' => array('modified_date' => 'DESC'
                )));
               $insert .= ' pi= http://totsy.local/image/' . $item->primary_image .'.jpeg';
               $insert .= ' pid=' . $item->_id;
               $insert .= ' plp="http://totsy.local/a/spinback&url=http://totsy.local' . $product . '"';
               $insert .= ' pn="' . $item->description . '"';
               $insert .= ' m="' . $item->vendor . '"';
               $insert .= 'msg= "Check out this item!"';

               return str_replace('$',$insert,$pixel);
            }
        }else if($invited_by == 'linkshare'){

        }else{
            return '<br/>' . $pixel . '<br/>';
        }
    }

	public static function storeSubAffiliate($get_data, $affiliate) {
        if (array_key_exists('subid',$get_data)) {
            $subaff = $get_data['subid'];
        } else if (array_key_exists('subID',$get_data)) {
            $subaff = $get_data['siteID'];
        }else {
            return $affiliate;
        }

        $conditions = array('invitation_codes' => $affiliate);
        $col = Affiliate::collection();
        if($col->count($conditions) > 0) {
            $col->update($conditions, array(
                    '$addToSet' => array(
                        'sub_affiliates' => $subaff
                    )));
        }
       return $affiliate .= $subaff;
	}
}

?>