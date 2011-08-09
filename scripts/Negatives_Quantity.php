<?
#Connect to totsy Database
$m = new Mongo('mongodb://db4.totsy.com:27017');
$m->setSlaveOkay();
$db = $m->selectDB("totsy");

$itemsCollection = $db->items;

$idx = 0;
$count = 0;
$sum = 0;
#Query
$conditions = array(
	'total_quantity' => array('$lt' => 0),
	'created_date' => array('$gt' => new MongoDate(mktime(0, 0, 0, 8, 1, 2011)))
);

$conditions_details = array(
	'created_date' => array('$gt' => new MongoDate(mktime(0, 0, 0,8, 1, 2011)))
);
$results = $itemsCollection->find($conditions_details);
#Get Counting
foreach($results as $result) {
	foreach($result['details'] as $key => $details) {
		if($details < 0 ) {
				$idx++;
				if($key != 'no size') {
					print_r(' @ ID ' . $result['_id'] . ' vendor_style ' . $result['vendor_style'] );
					print_r(' - total_quantity ' . $result['total_quantity'] . ' details ' . $details);
				} else {
					echo ' ********';
				}
		}
	}
}
#Print Result
echo "Items Found  : " . $idx;
?>