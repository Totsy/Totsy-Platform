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
	'items.item_id' => '4e401d6bd6b025fe0f0002e7'
);
$results = $ordersCollection->find($conditions);
#Get Counting
foreach($results as $result) {
	foreach	($result['items'] as $item) {
		if	($item['item_id'] == '4e401d6bd6b025fe0f0002e7' && $item['size'] === '4' ) {
			var_dump($result['date_created']);
			$count += $item['quantity'];
		}
	}
	$idx++;
}
#Print Result
echo "Orders Found  : ".$idx. " Items Sold : " .$count;
?>