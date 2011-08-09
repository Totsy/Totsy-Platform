<?
//Connect to totsy Database
$m = new Mongo('mongodb://db4.totsy.com:27017');
$m->setSlaveOkay();
$db = $m->selectDB("totsy");
$ordersCollection = $db->orders;

$idx = 0;
$count = 0;
$sum = 0;
$start_date = mktime(10, 0, 0, 4, 1, 2011);
$conditions = array(
	'date_created' => array(
       '$gt' => new MongoDate($start_date)
     ),
	'total' =>  array(
		'$gt' => (float) 45
	)
);
$results = $ordersCollection->find($conditions);

foreach($results as $result) {
	$user_ids[] = $result['user_id'];
	$idx++;
}

$user_ids = array_unique($user_ids);

//PRINT RESULT
echo "Users of Disney : ".count($user_ids)." Orders  : ".$idx;

?>