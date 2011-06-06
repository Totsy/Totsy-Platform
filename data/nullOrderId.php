<?php

$mongo = new Mongo();
$db = $mongo->selectDB("totsy");

$orderCollection = $db->orders;

$nullOrders = $orderCollection->find(array("order_id" => array('$exists' => false)));

var_dump($nullOrders->count());

foreach($nullOrders as $order) {
   $order['order_id'] = strtoupper(substr((string)$order['_id'], 0, 8) . substr((string)$order['_id'], 13, 4));
   $orderCollection->save($order);
}

$nullOrders = $orderCollection->find(array("order_id" => array('$exists' => false)));

var_dump($nullOrders->count());

?>