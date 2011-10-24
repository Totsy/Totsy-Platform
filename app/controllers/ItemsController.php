<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Affiliate;
use app\models\Item;
use app\models\Event;
use app\models\Cart;
use app\models\User;
use app\models\Order;
use lithium\storage\Session;

/**
 * Controls the user experience with an item.
 *
 * @see app\models\Affiliate
 * @see app\models\Item
 * @see app\models\Event
 * @see app\models\Cart
 */
class ItemsController extends BaseController {

	/**
	 * Loads all elements needed to view an item.
	 *
	 * The method first checks if the url from the request is valid
	 * to process. If the event or item can't be properly matched then
	 * there will be a forced redirect to the sales page.
	 * Otherwise, the item is found and returned to the view.
	 * In this method we also provide a tracking pixel for spinback.
	 *
	 * @see app/models/Affiliate::getPixels()
	 * @see app/models/Affiliate::generatePixel()
	 * @see app/models/Item::sizes()
	 * @return compact
	 *  * $item: `Object` of the item.
	 *  * $event: `Object` of the event.
	 *  * $related: `Object` of items that should be shown within the view.
	 *  * $sizes: `Array` of sizes that are available for the item.
	 *  * $shareurl: `String` containing URL that will be shared with 3rd party systems.
	 *  * $reserved: `Objects` of related items.
	 *  * $spinback_fb: `String` containing pixel that will fire in the view.
	*/
	public function view() {
		$ordersCollection = Order::Collection();
		#Get Users Informations
		$user = Session::read('userLogin');
		$itemUrl = $this->request->item;
		$eventUrl = $this->request->event;
		$item = null;
		$voucher_disable = false;
		if ($itemUrl == null || $eventUrl == null) {
			$this->redirect('/sales');
		} else {
			$event = Event::find('first', array(
				'conditions' => array(
					'enabled' => true,
					'url' => $eventUrl
			)));
			if (!$event) {
				$event = Event::first(array(
					'conditions' => array(
					'viewlive' => true,
					'url' => $eventUrl
				)));
			}

			$items = Item::find('all', array(
				'conditions' => array(
					'enabled' => true,
					'url' => $itemUrl
			)));
			$matches = $items->data();
			foreach ($matches as $match) {
				if (in_array($match['_id'], $event->items->data())) {
					$item = Item::find($match['_id']);
				}
			}
			if ($item == null || $event == null) {
				$this->redirect('/sales');
			} else {
				if ($event->end_date->sec < time()) {
					$this->redirect('/sales');
				} else {
					++$item->views;
					$item->save();
					$related = Item::related($item);
					$sizes = Item::sizes($item);
					$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					#Check If the user has reached the maximum number of vouchers ordered
					if(!empty($item->voucher)) {
						$quantity = 0;
						$orders = $ordersCollection->find(array('user_id' => $user['_id'], 'items.item_id' => (string) $item->_id));
						foreach($orders as $order) {
							foreach($order['items'] as $item_purch) {
								if($item_purch['item_id'] == (string) $item->_id) {
									$quantity += $item_purch['quantity'];
								}
							}
						}
						if($quantity >= (int) $item->voucher_max_use) {
							$voucher_disable = true;
						}
					}
				}
			}
            $pixel = Affiliate::getPixels('product', 'spinback');
			$spinback_fb = Affiliate::generatePixel('spinback', $pixel,
				array('product' => $_SERVER['REQUEST_URI']));
		}

		return compact(
			'item',
			'event',
			'related',
			'sizes',
			'shareurl',
			'reserved',
			'spinback_fb',
			'voucher_disable'
		);
	}

	/**
	 * Checks the availability of an item.
	 *
	 * The method checks if a single item (color/size) is available for purchase.
	 * A boolean of `true` is returned if the actual quantity available less reserved
	 * items in the cart is greater than zero.
	 *
	 * @see app/models/Cart::reserved()
	 * @return boolean
	 */
	public function available() {
		$available = false;
		$this->render(array('layout' => false));
		if ($this->request->query) {
			$data = $this->request->query;
			$size = ($data['item_size'] == 'undefined') ? 'no size' : $data['item_size'];
			$reserved = Cart::reserved($data['item_id'], $size);
			$item = Item::find('first', array(
				'conditions' => array(
					'_id' => $data['item_id']
			)));
			$qty = $item->details->{$size} - $reserved;
			$available = ($qty > 0) ? true : false;
			echo json_encode($available);
		}
	}
}

?>