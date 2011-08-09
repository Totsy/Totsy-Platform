<?
//Connect to totsy Database
//$m = new Mongo('mongodb://db4.totsy.com:27017');
$m = new Mongo('mongodb://dev3.totsy.com:27017');

//$m->setSlaveOkay();
$db = $m->selectDB("totsy");
$ordersCollection = $db->orders;
$i = 0;
$idx = 0;
$count = 0;
$sum = 0;
$start_day = 27;
$start_hour = 11;
//end date
$end_year = 2011;
$end_month = 6;
$end_day = 27;
$end_hour = 0;
$end_date = mktime($end_hour, 0, 0, $end_month, $end_day, $end_year);
$ids = array();
do {
	$start_day++;
	$start_for_selected_event = mktime($start_hour + 1, 0, 0, 4, $start_day - 1, 2011);
	$end_for_selected_event = mktime(0 + 1, 0, 0, 4, $start_day, 2011);
	$conditions = array(
	'date_created' => array(
       '$gt' => new MongoDate($start_for_selected_event),
       '$lte' => new MongoDate($end_for_selected_event) 
     ),
	'total' =>  array(
		'$gt' => (float) 45
	));
	$results = $ordersCollection->find($conditions);
	$user_ids = null;
	$orders = null;
	$users = null;
	$idx=0;
	foreach($results as $result) {
		if(!in_array($result['user_id'], $ids)) {
			$user_ids[] = $result['user_id'];
		}
		$ids[] = $result['user_id'];
		$idx++;
	}
	$orders = $idx++;
	$user_ids = array_unique($user_ids);
	$users = count($user_ids);
	$test[$i]['date'] = date("d",$end_for_selected_event) . '/' .date("m",$end_for_selected_event) . '/' .date("Y",$end_for_selected_event);
	$test[$i]['orders'] = $orders;
	$test[$i]['users'] = $users;
	$i++;
	$start_hour = 0;
} while ($end_date != $end_for_selected_event);

/**************CREATE A CSV FILE OF THE BUYERS FILTERED BY MONTH****************/
//Open a CSV file - don't forget to have the good permission
$fp = fopen('disney_stat.csv', 'w');
//Create each line of the csv
foreach ($test as $fields) {
    fputcsv($fp, $fields, ',', '"');
}
//Close the csv file
fclose($fp);



?>