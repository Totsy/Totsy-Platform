<?
#Connect to totsy Database
$m = new Mongo('mongodb://db4.totsy.com:27017');
$m->setSlaveOkay();
$db = $m->selectDB("totsy");

$ordersCollection = $db->orders;

$idx = 0;
$count = 0;
$sum = 0;
#Query
$conditions = array(
	'items.item_id' => '4e319320974f5b8d52000339'
);
$results = $ordersCollection->find($conditions);
#Get Counting
foreach($results as $result) {
	foreach	($result['items'] as $item) {
		if	($item['item_id'] == '4e319320974f5b8d52000339') {
			$count += $item['quantity'];
		}
	}
	$idx++;
}
#Print Result
echo "Orders Found  : ".$idx. " Items Sold : " .$count;
?>