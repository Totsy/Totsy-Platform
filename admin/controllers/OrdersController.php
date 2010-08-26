<?php

namespace admin\controllers;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use admin\extensions\Mailer;


class OrdersController extends \lithium\action\Controller {

	public function index() {
		if ($this->request->data) {

		}

		return compact('orders');
	}

	public function view() {
		$order = null;
		if ($this->request->data) {
			$order =  Order::lookup($this->request->data['order_id']);
		}
		return compact('order');
	}

	public function update() {
		$_shipToHeaders = array(
			'ShipDate',
			'OrderNum',
			'ShipMethod',
			'Tracking #',
			'Cost',
			'SKU',
			'Email'
		);
		if ($this->request->data) {
			if ($_FILES['upload']['error'] == 0) {
				$file = $_FILES['upload']['tmp_name'];
				$objReader = PHPExcel_IOFactory::createReaderForFile("$file");
				$objPHPExcel = $objReader->load("$file");
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					for ($row = 1; $row <= $highestRow; ++ $row) {
						for ($col = 0; $col < $highestColumnIndex; ++ $col) {
							$cell = $worksheet->getCellByColumnAndRow($col, $row);
							$val = $cell->getValue();
							if ($row == 1) {
								$heading[] = $val;
							} else {
								if (in_array($heading[$col], $_shipToHeaders) && ($val != null)) {
									$shipRecords[$row - 1][$heading[$col]] = $val;
								}
							}
 						}
 					}
				}
			}
			if ($shipRecords) {
				$updated = array();
				foreach ($shipRecords as $shipRecord) {
					$checkedItems = array();
					$order = Order::lookup($shipRecord['OrderNum']);
					if ($order) {
						$item = Item::find('first', array(
							'conditions' => array(
								'vendor_style' => $shipRecord['SKU']
						)));
						if ($item) {
							$itemId = (string) $item->_id;
							$orderData = $order->data();
							foreach ($orderData['items'] as $orderItem) {
								if ($orderItem['item_id'] == $itemId) {
									$orderItem['status'] = "Order Shipped";
									$orderItem['tracking_number'] = $shipRecord['Tracking #'];
								}
								$checkedItems[] = $orderItem;
							}
							$order->items = $checkedItems;
							$order->ship_method = $shipRecord['ShipMethod'];
						}
						$details = array(
							'Order' => $order->order_id,
							'SKU' => $shipRecord['SKU'],
							'First Name' => $order->shipping->firstname,
							'Last Name' => $order->shipping->lastname,
							'Ship Method' => $order->ship_method,
							'Tracking Number' => $shipRecord['Tracking #']
						);
						if (empty($order->auth_confirmation)) {
							$order->process();
						} else {
							$order->save();
						}
						$details['Confirmation Number'] = $order->auth_confirmation;
						$updated[] = $details;
						Mailer::send(
							'shipped',
							"Totsy - Shipping Notification - $order->order_id",
							array('name' => $order->firstname, 'email' => $shipRecord['Email']),
							compact('order', 'details')
						);
					}
				}
			}
		}

		return compact('updated');
	}
}
?>