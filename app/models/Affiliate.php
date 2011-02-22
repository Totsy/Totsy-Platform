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
        $conditions['level'] = 'super';
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
    /**
    *   This function appends neccessary information to affilates pixels so affiliates can function *   and collect data.
    *   @params $invited_by the affilate code associated with the pixel
    *           $pixel the pixel the affiliate provided
    *           $options Used those times when the url is dynamic and the affiliate need a pixel or
    *                    app on that page.  Available options: product - item view page, orderid -
    *                    for affiliates who need orderids for share revenue.
    *   @return Returns modified pixels.
    *   $TODO  Move the appending to the Helper
    **/
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
               $insert .= ' pi= http://' . $_SERVER['HTTP_HOST'] . '/image/' . $item->primary_image .'.jpeg';
               $insert .= ' pid=' . $item->_id;
               $insert .= ' plp=http://' . $_SERVER['HTTP_HOST'] . '/a/spinback?redirect=http://' . $_SERVER['HTTP_HOST'] . $product;
               $insert .= ' pn="' . $item->description . '"';
               $insert .= ' m="' . $item->vendor . '"';
               $insert .= 'msg= "Check out this great deal on Totsy!"';
               return str_replace('$',$insert,$pixel);
            }
        }else if($invited_by == 'linkshare'){

        }else{
            return '<br/>' . $pixel . '<br/>';
        }
    }

	public static function storeSubAffiliate($get_data, $affiliate) {
        if (array_key_exists('subid' , $get_data)) {
            $subaff = $get_data['subid'];
        } else if (array_key_exists('siteID' , $get_data)) {
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