<?php

namespace app\models;

use app\extensions\helper\Pixel;
use app\models\User;
use lithium\storage\Session;
use MongoDate;

class Affiliate extends Base {

    protected $_meta = array('source'=> 'affiliates');
	public $validates = array();
    /**
    * Retrieves active pixels related to active affiliates
    *
    * @param $url the pixel needs to be placed
    * @param $invited_by the affiliate associated with the user
    * @return the pixels associated to the affiliate and url
    */
	public static function getPixels($url, $invited_by) {

	    $cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
	    $userInfo = Session::read('userLogin', array('name' => 'default'));
		$userCollection = User::collection();
		$user = $userCollection->findOne(array('email' => $userInfo['email']));
        $orderid = NULL;

        if(preg_match('(orders/view/)',$url)) {
            $orderid = substr($url,12);
            $url = '/orders/view';
        }

        if(preg_match('(^a/)',$url)) {
            $url = '/a/' . $invited_by;
        }

        /*for affilliates that have a category
        build the URL here in order to find a match and get the right pixel info for this affiliate */

		/**
		* This detaches the invited by from the unique identifier
		* for affiliate invited by retrieved from
		* the user's record.  e.g. linkshare_1x3uebdu395769
		**/
        if($index = strpos($invited_by, '_')) {
            $invited_by = substr($invited_by, 0 , $index);
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
		                    'pixel.pixel' => 1, 'pixel.page' => 1,
							'_id' => 0
		                    ));

		$pixels = Affiliate::find('all', $options );
		$pixels = $pixels->data();
		$pixel = NULL;

        if($url == '/orders/view'){
            if($user && array_key_exists('affiliate_share', $user) && $user['affiliate_share']){
     			$cookie['affiliate'] = $user['affiliate_share']['affiliate'];
                $cookie['entryTime'] = $user['affiliate_share']['landing_time'];
                Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
            }
        }

		foreach($pixels as $data) {
			foreach($data['pixel'] as $index) {
                if(is_array($index['page']) && in_array($url, $index['page'])) {
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
    * Stores an affiliates subid/siteid that were passed in through the special
    * affiliate url.
    *
    * @param $get_data GET array
    * @param $affiliate: affiliate's code
    * @return if an subid/siteid is available the return value is the affiliate_subid, if not the
    * affiliate name will return.
    */
	public static function storeSubAffiliate($get_data, $affiliate) {
        $pattern = 'siteId|siteID|siteid|subid|subID|subId';
        $needles = explode('|',$pattern);
        $keys = array_keys($get_data);
        if($key = array_intersect($needles, $keys)){
            $value = $needles[key($key)];
            $subaff = $get_data[$value];
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
       return $affiliate .= '_' . $subaff;
	}
    /**
    * This function appends neccessary information to affilate's pixels so affiliates can collect data.
    *
    * @param $invited_by the affilate code associated with the pixel
    * @param $pixel the pixel the affiliate provided
    * @param $options Available options: product -> item view page, orderid ->
    *                    for affiliates who need orderids for share revenue, trans_type -> for
    *                    transmitting 'new'/'cancel' order to revenue share affiliates.
    * @return string modified pixels.
    * @TODO  Move the appending to the Helper
    */
	public static function generatePixel($invited_by, $pixel, $options = array()) {
	    /**
	    *   This if block is for affiliates who want a dynamic string in their pixel
	    *   The random string is created and is place where ever the $ is placed in the
	    *   pixel.  The $ sign is a place holder for where the random string is will be
	    */
        if($invited_by == 'w4' || $invited_by == "pmk" || $invited_by == "emiles" ){
            $transid = 'totsy' . static::randomString();
            return '<br/>' . str_replace('$', $transid,$pixel);
        }
        /**
        *   This if block is for spinback.  It appends the appropriate information
        *   to their spinback button that are located in various places on the site
        *   - the events page, product page, order confirmation page, and invite a friend page
        *   The options variable holds what which page is calling the function.
        */
        if($invited_by == 'spinback' && ($options)) {
            $insert = '';
            if (array_key_exists('invite', $options) && ($options['invite'])){
                $session = Session::read('userLogin');
                $user = User::find('first', array('conditions' => array(
                    'email' => $session['email']
                )));
                if (is_object($user->invitation_codes) && get_class($user->invitation_codes) == "lithium\data\collection\DocumentArray") {
                    $code = $user->invitation_codes[0];
                } else {
                    $code = $user->invitation_codes;
                }
                $insert = static::spinback_share('/img/logo.png', $user->_id, '/join/' . $code, 'The best brands for kids, moms & families up to 90% off!', '' ,"I saved tons on Totsy and you can too! Membership is FREE so join today!", ' st="Invite Your Friends" ');
                return str_replace('$' , $insert, $pixel);
            }
            if (array_key_exists('orderid', $options) && ($options['orderid'])) {
                $orderid = $options['orderid'];
                $order = Order::find('all', array('conditions' => array(
                    'order_id' => $orderid
                )));
                $order = $order->data();
                if(($order)) {
                    $insert .= 'cid="totsy"';
                    $insert .= ' oid="' . $orderid . '"';
                    $insert .= ' total="' . $order[0]['subTotal'] . '"';
                }
                return str_replace('$' , $insert, $pixel);
            }
            if (array_key_exists('order', $options) && ($options['order'])){
                $orderurl = $options['order'];
                $last = strrpos($orderurl, '/');
                $orderid = substr($orderurl, $last + 1);
                $order = Order::find('first', array('conditions' => array(
                    'order_id' => $orderid
                )));

                $insert = static::spinback_share('/img/logo.png', $order->order_id, '/sales', 'Great Deals on Totsy', '' ,"I just saved on Totsy.", ' st="Share Your Order" ');
                return str_replace('$',$insert,$pixel);
            }
            if (array_key_exists('product', $options) && ($options['product'])) {
                $product = $options['product'];
                $last = strrpos($product, '/');
                $item = substr($product, $last + 1);
                $item = Item::find('first', array(
                    'conditions' => array(
                        'enabled' => true,
                        'url' => $item),
                    'order' => array('modified_date' => 'DESC'
                )));
                $insert = static::spinback_share('/image/' . $item->primary_image . '.jpeg',$item->_id, $product,  htmlspecialchars($item->description), htmlspecialchars($item->vendor), "Check out this great deal on Totsy!"  );

               return str_replace('$',$insert,$pixel);
            }
            if(array_key_exists('event', $options) && ($options['event'])) {
                $event = $options['event'];
                $last = strrpos($event, '/');
                $vendorurl = substr($event, $last + 1);
                if ( preg_match('/filter/', $vendorurl)){
                    $len = strlen($vendorurl);
                    $url = substr($event,0, $last);
                    $last = strrpos($url, '/');
                    $vendorurl = substr($url, $last + 1);
                }
                $event = Event::find('first', array('conditions' => array(
                            'url' => $vendorurl
                        )));
		if (is_object($event)){
                	$insert = static::spinback_share(
				'/image/' .$event->logo_image . '.gif',
				$event->_id,
				$options['event'],
				htmlspecialchars($event->name),
				htmlspecialchars($event->name),
				"Check out this SALE on Totsy!",
				' st="Share this Sale!"'
			);
		} else {
			$insert = '';
		}
               return str_replace('$',$insert,$pixel);
            }
        }
        if($invited_by == 'linkshare') {
            if( array_key_exists('orderid', $options) && $options['orderid']) {
                $raw = '';
                if (array_key_exists('trans_type', $options) && $options['trans_type']) {
                    $trans_type = $options['trans_type'];
                }else{
                    $trans_type = 'new';
                }
                $orderid = $options['orderid'];
                $cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
                $order = Order::find('first', array('conditions' => array(
                        'order_id' => $orderid
                    )));

				/**
				* This line prevents the linkshare rev pixel from firing if the
				* user goes to the /order/view page just to see an old order
				**/
				$order_age = (Order::timeValue($order->date_created) > (time() - 30)) ? true : false;
				//reset the pixel if the order is 30 secs or more old
				if (!$order_age) {
					$pixel = "";
				}

                $user = User::find('first', array(
                    'conditions' => array('_id' => $order->user_id),
                    'fields' => array('affiliate_share' => true)
                    ));
                if($user->affiliate_share){
                    $track = $user->affiliate_share['affiliate'];
                    $entryTime = $user->affiliate_share['landing_time'];
                }else if(array_key_exists('affiliate', $cookie) && preg_match('@^(linkshare)@i', $cookie['affiliate'])){
                    $track = $cookie['affiliate'];
                    $entryTime = $cookie['entryTime'];
                }
                $raw = static::linkshareRaw($order, $track, $entryTime, $trans_type);

                if(($pixel) && $order_age){
                    $insert = static::linkshareRaw($order, $track, $entryTime, null);
                    $pixel  = str_replace('$',$insert,$pixel);
                }
                //Encrypting raw message
                $base64 = base64_encode($raw);
                $msg = str_replace('-','_',str_replace('+','/',$base64));

                //Used for authenticity
                $md5_raw = hash_hmac('md5', $raw, 'Ve3YGHn7', true);
                $md5 = base64_encode($md5_raw);
                $data = 'http://track.linksynergy.com/nvp?mid=36138&msg=' . urlencode($msg) . '&md5=' . urlencode($md5) . '&xml=1';
                static::transaction($data, 'linkshare', $orderid, $trans_type);
            }
        }
        return '<br/>' . $pixel . '<br/>';
    }

    /**
    * Spinback share params for javascript
    *
    * @param $pi image to share
    * @param $pid id related to share topic
    * @param $plp url to the share item
    * @param $pn name of share topic
    * @param $m name of vendor
    * @param $msg message to display
    * @return string of variables for javascript
    */
    private static function spinback_share($pi, $pid, $plp, $pn, $m, $msg, $extra = null){
        $insert ='';
        $insert .= ' pi=" http://' . $_SERVER['HTTP_HOST'] . $pi . '"';
       $insert .= ' pid="' . $pid . '"';
       if($extra == ' st="Invite Your Friends" '){
            $insert .= ' plp="http://' . $_SERVER['HTTP_HOST'] . $plp . '"';
       }else{
            $insert .= ' plp="http://' . $_SERVER['HTTP_HOST'] . '/a/spinback?redirect=http://' . $_SERVER['HTTP_HOST'] . $plp . '"';
       }
       $insert .= ' pn="' .$pn . '"';
       $insert .= ' m="' . $m. '"';
       $insert .= 'msg= "' . $msg . '"';
       $insert .= $extra;
        return $insert;
    }
    /**
    *
    **/
    public static function linkshareRaw($order, $tr, $entryTime, $trans_type){
        $raw = '';
        $raw .= 'ord=' . $order->order_id . '&';
        if(($trans_type)){
            $raw .= 'tr=' . substr($tr, strlen('linkshare')+1) . '&';
            $raw .= 'land=' . date('Ymd_Hi', (int) $entryTime) . '&';
            $raw .= 'date=' . date('Ymd_Hi', Order::timeValue($order->date_created)) . '&';
        } else {
            $raw .= 'mid=36138&';
        }
        $skulist = array();
        $namelist = array();
        $qlist = array();
        $amtlist = array();
        foreach($order->items as $item) {
            if($trans_type == 'cancel') {
                if($order->cancel) {
                    $itemInfo = Item::find( $item->item_id);
                }else if($item->cancel) {
                     $itemInfo = Item::find( $item->item_id);
                }else{
                    continue;
                }
            }else{
                $itemInfo = Item::find( $item->item_id);
            }
            $skulist[] = $itemInfo->sku_details->{$item->size};
            $namelist[] = urlencode($itemInfo->description);
            $qlist[] =  $item->quantity;
            $amtlist[] = ($trans_type == 'cancel') ? (-round($item->sale_retail,2) * $item->quantity)*100 : (round($item->sale_retail,2) * $item->quantity)*100 ;
        }
         if($order->promo_code){
            $raw .= 'skulist=' . implode('|', $skulist) . '|Discount&';
            $raw .= 'namelist=' . implode('|', $namelist) . '|Discount&';
            $raw .= 'qlist=' . implode('|' , $qlist) . '|0&';
            $raw .= 'cur=USD&';
            $raw .= 'amtlist='. implode('|', $amtlist) . '|' . number_format($order->promo_discount,2)*100;
        }else{
            $raw .= 'skulist=' . implode('|', $skulist) . '&';
            $raw .= 'namelist=' . implode('|', $namelist) . '&';
            $raw .= 'qlist=' . implode('|' , $qlist) . '&';
            $raw .= 'cur=USD&';
            $raw .= 'amtlist='. implode('|', $amtlist);
        }
        return $raw;
    }

    /**
    * This function sends order transactions to linkshare.
    * @params $data is the information that needs to be passes
    * @return True or False
    **/
	public static function transaction($data, $affiliate, $orderid, $trans_type = 'new') {
        static::meta('source','affiliate.log');
        /**Avoiding sending duplicate information**/
        $transaction = static::collection()->count(array(
                            'order_id' => $orderid,
                            'trans_type' => $trans_type,
                            'success' => true
                        ));
        if( $transaction >= 1){
            return true;
        }
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, file_get_contents($data), $response, $index);
        xml_parser_free($parser);
        $status = $response[1]['value'];
        if( $status == 'Access denied' ) {
             $success = false;
        }else{
            $success = ( (bool) $response[5]['value'] ) ? (bool) $response[5]['value'] : (bool) $response[7]['value'];
        }
        $trans['trans_id'] = $response[1]['value'];
        $trans['affiliate'] = $affiliate;
        $trans['success'] = $success;
        $trans['order_id'] = $orderid;
        $trans['trans_type'] = $trans_type;
        $trans['created_date'] = new MongoDate(strtotime('now'));
        return static::collection()->save($trans);
    }
    /**
	 * @todo Document me
	 */
	public static function linkshareCheck($userId, $affiliate, $cookie) {
		$success = false;
		if (preg_match('@^(linkshare)@i', $affiliate)){
			$user = User::collection();
			$user->find(array( '_id' => $userId),
                    array('fields' => array('affiliate_share' => true))
                    );
                $affiliate_share = array('affiliate' => $affiliate , 'landing_time' => $cookie['entryTime']);
                $success = ($user->update(array( '_id' => $userId), array('$set' => array('affiliate_share' => $affiliate_share)))) ? true : false;
		}
		return $success;
	}
}
?>
