<?php

namespace app\controllers;
use lithium\storage\Session;
use app\controllers\BaseController;
use app\models\Affiliate;
use app\models\Item;
use app\models\Event;
use app\models\Cart;

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
		$itemUrl = $this->request->item;
		$eventUrl = $this->request->event;
		$item = null;
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
				}
			}
            $pixel = Affiliate::getPixels('product', 'spinback');
            
            if(Session::read('layout', array('name' => 'default'))!=='mamapedia') {
				$spinback_fb = Affiliate::generatePixel('spinback', $pixel,
				array('product' => $_SERVER['REQUEST_URI']));
			}
		}
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_view';
		}
		return compact(
			'item',
			'event',
			'related',
			'sizes',
			'shareurl',
			'reserved',
			'spinback_fb'
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