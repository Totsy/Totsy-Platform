<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Order;
use admin\models\Item;
use admin\models\Event;
use admin\models\User;
use MongoRegex;
use MongoDate;
use MongoId;
use Mongo;
use li3_flash_message\extensions\storage\FlashMessage;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;

/**
 * Handles the users main account information.
 */

class ItemsController extends BaseController {

	/**
	 * Main display of item data
	 */
	public function index() {
		$created_date = 0;
		$modified_date = 0;
		$files = 0;
		$items = Item::find('all', array(
				'fields' => compact(
					'created_date',
					'modified_date',
					'files'
				)));

		return compact('items');
	}
	/**
	 * Edits a product/item based on a preloaded CSV file.
	 *
	 * The edit method has several parts that need to be parsed
	 * before saved to the database. This primarily applies to the
	 * images that are attached to the item.
	 * @param string
	 * @return array
	 */
	public function edit($id = null) {
		$item = Item::find('first', array('conditions' => array('_id' => $id)));
		$event = Event::find('first', array('conditions' => array('_id' => $item->event[0])));

		if ($item) {
			$details = json_encode($item->details->data());
		} else {
			$this->redirect(array('controller' => 'items', 'action' => 'index'));
		}
		#T Get all possibles value for the multiple filters select
		$sel_filters = array();
		$all_filters = array();
		$result =  Item::getDepartments();
		foreach ($result['values'] as $value) {
			$all_filters[$value] = $value;
			if (array_key_exists('Momsdads',$all_filters) && !empty($all_filters['Momsdads'])) {
				$all_filters['Momsdads'] = 'Moms & Dads';
			}
		}
		#Get selected values of filters
		if(!empty($item->departments)) {
			$values = $item->departments->data();
			foreach ($values as $value) {
				$sel_filters[$value] = $value;
			}
		}
		if ($this->request->data) {
			$alternate_images = array();
			foreach ($this->request->data as $key => $value) {
				if (substr($key, 0, 10) == 'alternate-' ) {
					$alternate_images[] = substr($key, 10, 24);
					unset($this->request->data[$key]);
				}
			}
			if (!empty($item->event[0])) {
				$this->request->data['event'] = array($item->event[0]);
			}
			$dirtyUrl = $this->request->data['description']." ".$this->request->data['color'];
			$this->request->data['url'] = $this->cleanUrl($dirtyUrl);
			$this->request->data['modified_date'] = new MongoDate();
			$data = array_merge(Item::castData($this->request->data), compact('alternate_images'));
			//Clean filters posts
			if(!empty($data["departments"])) {
				foreach($data["departments"] as $value) {
					if(!empty($value)) {
						$departments[] = ucfirst($value);
					}
				}
				$data["departments"] = $departments;
			}
			#Get Vouchers Uploaded
			if ($_FILES['upload_file']['error'] == 0 && $_FILES['upload_file']['size'] > 0) {
				$vouchers = $this->parseVouchers($_FILES, $item->_id);
				if(!empty($vouchers)) {
					if(empty($this->request->data['voucher_overwrite'])) {
						if(!empty($item->vouchers)) {
							$data['vouchers'] = array_merge($item->vouchers->data(), $vouchers);
						} else { 
							$data['vouchers'] = $vouchers;
						}
					} else {
						$data['vouchers'] = $vouchers;
					}
				}
				$data['total_quantity'] = count($vouchers);
				unset($data['voucher_overwrite']);
				unset($data['upload_file']);
			}
			if ($item->save($data)) {
				$this->redirect(array(
						'controller' => 'items', 'action' => 'edit',
						'args' => array($item->_id)
				));
			}
		}
		return compact('item', 'details', 'event', 'all_filters', 'sel_filters');
	}

	public function preview() {
		$itemUrl = $this->request->item;
		$eventUrl = $this->request->event;
		if ($itemUrl == null || $eventUrl == null) {
			$this->redirect('/');
		} else {
			$event = Event::find('first', array(
					'conditions' => array(
						'enabled' => true,
						'url' => $eventUrl
					)));
			$items = Item::find('all', array(
					'conditions' => array(
						'enabled' => true,
						'url' => $itemUrl
					)));
			if ($event) {
				$matches = $items->data();
				foreach ($matches as $match) {
					if (in_array($match['_id'], $event->items->data())) {
						$item = Item::find($match['_id']);
					}
				}
				if ($item == null || $event == null) {
					$this->redirect('/');
				} else {
					$event = Event::find('first', array(
							'conditions' => array(
								'items' => array((string) $item->_id)
							)));
					$related = Item::related($item);
					$sizes = Item::sizes($item);
					$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				}
			}
		}
		$this->_render['layout'] = 'preview';
		$id = $item->_id;
		$preview = 'Items';
		return compact('item', 'event', 'related', 'sizes', 'shareurl', 'id', 'preview');
	}

	/**
	 * Remove Items from Event and Item Collection
	 *
	 * Based on the event _id items will be removed from the Item collection.
	 * The item field will also be unset.
	 * @return array
	 */
	public function removeItems() {
		if ($this->request->data) {
			$id = $this->request->data['event'];
			$event = Event::find('first', array('conditions' => array('_id' => $id)));
			if ($event->views <= 0){
				if ((!empty($event->items)) && Item::remove(array('event' => $id)) && Event::removeItems($id)) {
					FlashMessage::set('Items Removed', array('class' => 'pass'));
				} else {
					FlashMessage::set('Remove Failed', array('class' => 'warning'));
				}
			} else {
				FlashMessage::set('Items Cannot Be Removed the Event is Live', array('class' => 'fail'));
			}
			$this->redirect(array('Events::edit','args' => array($id)));
		}
	}

	public function search() {
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$itemCollection = Item::connection()->connection->items;
			$items = $itemCollection->find(
				array('$or' => array(
						array('description' => new MongoRegex("/$search/i")),
						array('vendor' => new MongoRegex("/$search/i")),
						array('vendor_style' => new MongoRegex("/$search/i")),
						array('skus' => array('$in' => array(new MongoRegex("/$search/i"))))
					)));
		}
		return compact('items');
	}

	/**
	 * Update Items from Items Collection
	 * Based on the event _id items will be update from the Item collection.
	 */
	public function itemUpdate() {
		$itemsCollection = Item::Collection();
		$itemId = array();
		$related_items = array();
		$event_items = array();

		if ($this->request->data) {
			$data = $this->request->data;

			$id = $data['id'];
			$event_items = Item::find('all', array('fields'=>array('_id'),'conditions'=>array('event'=>$id)));

			$event_items = array_values($event_items->data());

			unset($data['id']);
			array_reverse($data);

			//build selected items array
			foreach ($data as $key => $value) {
				//check if this is the related items (dropdown selection) or the description (text area)
				if(substr_count($key, 'related') > 0) {
					$item_id = substr($key, (strrpos($key, "_") + 1));
					$related_items[$item_id] = $value;

				} else {
					if($value) {
						$itemId = array("_id" => new MongoId($key));
						$itemsCollection->update($itemId, array('$set' => array("blurb" => $value)));
					}
				}
			}

			//run through related_items array and update the items
			foreach($event_items as $key=>$value){

				if(isset($related_items[$value['_id']])) {
					foreach($related_items[$value['_id']] as $k) {
						$temp[] = $k;
					}
					//print "update item:".$value['_id']." and set related items to ". implode(", ", $temp). "<br />";
					$itemsCollection->update(array("_id" => new MongoId($value['_id'])), array('$set' => array('related_items' => $temp)));
					unset($temp);
				} else {
					//print "remove related items for item of ID:".$value['_id']. "<br />";
					$itemsCollection->update(array("_id" => new MongoId($value['_id'])), array('$unset' => array('related_items'=> 1)));
				}
			}

		$this->redirect('/events/edit/'.$id.'#event_items');

		}
	}

	public function bulkCancel($search = null) {
		if ($this->request->data || $search) {
			if ($this->request->data['search']) {
				$search = $this->request->data['search'];
			}
			$itemCollection = Item::connection()->connection->items;
			$items = $itemCollection->find(
				array('$or' => array(
					array('_id') => new MongoRegex("/$search/i"),
					array('skus' => array('$in' => array(new MongoRegex("/$search/i"))))
			)));
			$items = iterator_to_array($items);

			if (strpos($search, "-")) { //detect if there is a '-' in the string, which means it is a SKU and not just an item_id
				$item_id = key($items);
				$search_sku = $search;
				$search_item_id = $item_id;
			}
			return compact("items","search_item_id","search_sku");
		}
	}
	
	/**
	 * This method parses the vouchers file that is uploaded in the Item Edit View.
	 *
	 */
	protected function parseVouchers($_FILES, $_id) {
		$vouchers = array();
		if ($this->request->data) {
			if ($_FILES['upload_file']['error'] == 0) {
				$file = $_FILES['upload_file']['tmp_name'];
				$objReader = PHPExcel_IOFactory::createReaderForFile("$file");
				$objPHPExcel = $objReader->load("$file");
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					for ($row = 2; $row <= $highestRow; ++ $row ) {
						$cell = $worksheet->getCellByColumnAndRow($col, $row);
						$val = $cell->getCalculatedValue();
						$vouchers[] = $val;
					}
				}
			}
		}
		return $vouchers;
	}
	
	public function exportVouchers($item_id) {
		$orderCollection = Order::Collection();
		$orders = $orderCollection->find(array('items.item_id' => $item_id));
		$idx = 0;
		foreach($orders as $order) {
			$user = User::find('first', array('conditions' => array('_id' => new MongoId($order['user_id']))));
			$datas[$idx]['order_id'] = (string) $order['_id'];
			$datas[$idx]['email'] = $user->email;
			foreach($order['items'] as $item) {
				if($item['item_id'] == $item_id) {
					$datas[$idx]['voucher'] = $item['voucher_code'];
				}
			}
			$idx++;
		}
		return compact("datas");

	}
}
?>