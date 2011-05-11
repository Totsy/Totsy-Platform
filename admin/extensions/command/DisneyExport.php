<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;
use admin\models\Order;
use admin\models\User;
use admin\models\Disney;
use MongoId;
use MongoDate;
use li3_silverpop\extensions\Silverpop;

/**
 * The `Disney Export' Command upload a file that contains user who
 * order for more then 45$ to a disney ftp.
 * It also send a mail with the log of the operations.
 */
class DisneyExport extends \lithium\console\Command {

	/**
	 * FTP Server of 3PL we are sending files to.
	 *
	 * @var string
	 */
	protected $_server = 'cdsfiles.com';

	/**
	 * FTP Server of 3PL we are sending files to.
	 *
	 * @var string
	 */
	protected $remote_directory = '/CDSFiles/CDS/OFFLINE/';
	
	/**
	 * FTP User Name.
	 *
	 * @var string
	 */
	protected $_user = 'mmiller@totsy.com';

	/**
	 * FTP Password.
	 *
	 * @var string
	 */
	protected $_password = 'cds6504';
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * Directory of files holding the files to FTP.
	 *
	 * @var string
	 */
	public $source = '/resources/totsy/tmp/';
	
	/**
	 * Minimum price of order where disney offer is applied
	 *
	 * @var int
	 */
	public $min_price = 45;
	
	/**
	 * Maximum field size for Disney Doc
	 *
	 * @var int
	 */
	public $max_field = 27;
	
	/**
	 * Day for orders requested
	 *
	 * @var int
	 */
	public $startDay = null;
	
	/**
	 * Month for orders requested
	 *
	 * @var int
	 */
	public $startMonth = null;
	
	/**
	 * Year for orders requested
	 *
	 * @var int
	 */
	public $startYear = null;
	
	/**
	 * Year for orders requested
	 *
	 * @var int
	 */
	public $initial = false;

	/**
	 * Instances
	 */
	public function run() {
		$infos = array();
		#Get Informations Order from DB
		$infos = $this->getInformations();
		#Create the temporary file
		$myFile = $this->saveFile($infos);
		#Count of the records
		$records = count($infos);
		#Send log email to Disney
		$this->sendMail($myFile, $records);
		#Upload the file to specified ftp
		$this->transferFile($myFile, LITHIUM_APP_PATH . $this->source);
		#Save log to DB
		$this->saveRecords($myFile, $records);
	}

	/**
	*  Get Last Orders and Write Files
	* @param boolean $initial
	* @return array
	*/
	public function getInformations() {
		Environment::set($this->env);
		$infos = array();
		$ordersCollection = Order::collection();
		/****Conditions****/
		//start date
		$now = getdate();
		if (empty($this->startMonth)) { 
			$MonthSel = $now['mon'];
		}
		if (empty($this->startDay)) { 
			$DaySel = $now['mday'];
		}
		if (empty($this->startYear)) { 
			$YearSel = $now['year'];
		}
		if (!empty($this->initial)) {
			//4th April 2011, 10am
			$start_date = mktime(10, 0, 0, 4, 1, 2011);
		} else {
			$start_date = mktime(0,0,0,$MonthSel,$DaySel - 1,$YearSel);
		}
		$end_date = mktime(0,0,0,$MonthSel,$DaySel,$YearSel);
		$conditions_order = array(
			'date_created' => array(
				'$gt' => new MongoDate($start_date),
				'$lte' => new MongoDate($end_date)
				),
				'total' =>  array(
					'$gt' => (float) $this->min_price
				)
		);
		/****Query****/
		$result_order = $ordersCollection->find($conditions_order);
		foreach ($result_order as $order) {
			$search = explode(",",".,ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
			$replace = explode(",",",c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
			/***Preparation of Datas***/
			#NAME
			$name = $order['shipping']['firstname'] . ' ' .  $order['shipping']['lastname'];
			if(strlen($name) > $this->max_field) {
				$name = substr($name, 0, $this->max_field);
			}
			$name = str_pad(strtoupper(str_replace($search, $replace, $name)),$this->max_field);
			#ADDRESS1
			$address1 = $order['shipping']['address'];
			if(strlen($address1) > $this->max_field) {
				$address1 = substr($address1, 0, $this->max_field);
			}
			$address1 = str_pad(strtoupper(str_replace($search, $replace, $address1)),$this->max_field);
			#ADDRESS2
			if(!empty($order['shipping']['address_2'])) {
				$address2 = $order['shipping']['address_2'];
				if(strlen($address2) > $this->max_field) {
					$address2 = substr($address2, 0, $this->max_field);
				}
			} else {
				$address2 = ' ';
			}
			$address2 = str_pad(strtoupper(str_replace($search, $replace, $address2)),$this->max_field);
			#CITY
			$city = $order['shipping']['city'];
			if(strlen($address1) > 13) {
				$city = substr($city, 0, 13);
			}
			$city = str_pad(strtoupper(str_replace($search, $replace, $city)),13);
			#STATE
			$state = $order['shipping']['state'];
			if(strlen($state) > 2) {
				$state = substr($state, 0, 2);
			}
			$state = str_pad(strtoupper(str_replace($search, $replace, $state)),2);
			#2IP
			$zip = $order['shipping']['zip'];
			if(strlen($zip) > 6) {
				$zip = substr($zip, 0, 6);
			}
			$zip = str_pad(strtoupper(str_replace($search, $replace, $zip)),6);
			$infos[] = '21'
						. '000000000'
						. $name
						. $address1
						. $address2
						. $city
						. $state
						. $zip
						. str_pad('99999',17,'0')
						. 'FAF'
						. 'N' //New/renewal Code
						. '1' //Order Entry Type
						. '010' //Term Of Order
						. '01000' // Order Value
						. str_pad('',4) //Credit Card Type
						. str_pad('',17,'0') //Credit Card
						. '0000' //Credit Card Expire
						. '0000' //Not Used
						. '001' //Number Of Copies
						. 'PFTOT11  ' //Document Key
						. 'D' //Medium Code
						. '  ' //Source Code
						. '000'
						. ' '
						. '       ' // Start Issue Description
						. '       ' //First issue sent
						. '00' // Number of issues sent
						. ' ' //GIFT
						. ' ' //Gift Card
						. str_pad('',25,'0') //Gift Signature
						. 'A'
						. str_pad('',5,'0')
						. 'N'
						. '0'
						. str_pad('',10,'0') //Telephone Number
						. str_pad('',7,'0') //General Gift Card
						. ' ' //Type Of Gift Card
						. '  ' //Fax Number
						. str_pad('',7);
		}
		return $infos;
	}

	/**
	* Save the file with datas in the temporary folder
	* @param string $info
	* See Disney File Documentation
	* @return string
	*/
	public function saveFile($infos) {
		$now = getdate();
		$day = date("d",$now["0"]);
		$month = date("m",$now["0"]);
		$myFile =  "TOT" . $month . $day . "1.txt";
		$myFilePath = LITHIUM_APP_PATH . $this->source . $myFile;
		$fh = fopen($myFilePath, 'wb');
		if(!empty($infos)) {
			foreach ($infos as $info) {
				fwrite($fh, $info);
				fputs($fh, "\r\n"); 
			}
		}
		fclose($fh);
		return $myFile;
	}

	/**
	* Send a log mail to disney and micah miller
	* @param string $file
	* @param int $records
	*/
	public function sendMail($file, $records) {
		$data = array(
			'file' => $file,
			'records' => $records,
			'from_email' => 'no-reply@totsy.com',
			'to_email' => 'vendorfiles@cds-global.com'
		);
		$data_2 = array(
			'file' => $file,
			'records' => $records,
			'from_email' => 'no-reply@totsy.com',
			'to_email' => $this->_user
		);
		$data_3 = array(
			'file' => $file,
			'records' => $records,
			'from_email' => 'no-reply@totsy.com',
			'to_email' => 'marti@strategicmediallc.com'
		);
		Silverpop::send('disney', $data);
		Silverpop::send('disney', $data_2);
		Silverpop::send('disney', $data_3);
	}

	/**
	* Put a file to the connected FTP Server.
	* @param string $file
	* @param string $path
	*/
	public function transferFile($file, $path) {
		$host = $this->_server;
		$connection = @ssh2_connect($this->_server, 22);
		if (! $connection)
			throw new Exception("Could not connect to $host on port 22.");
		
		if(! ssh2_auth_password($connection, $this->_user, $this->_password))
			throw new Exception("Could not authenticate with username and password.");
			
		$sftp = ssh2_sftp($connection);	
		$stream = fopen("ssh2.sftp://$sftp" . $this->remote_directory . $file, 'w');
		if (!$stream)
			throw new Exception("Could not open file: $file");

		$data_to_send = file_get_contents($path . $file);
		if ($data_to_send === false)
			throw new Exception("Could not open local file: $file.");
		
		if (fwrite($stream, $data_to_send) === false)
			throw new Exception("Could not send data from file: $file.");
	
		fclose($stream);
		unlink($path . $file);
	}

	/**
	 * Save records in the DB
	* @param string $myFile
	* @param int $records
	*/
	public function saveRecords($myFile, $records) {
		$datas = array(
						'file' => $myFile,
						'records' => $records,
						'date' => new MongoDate()
		);
		Disney::collection()->insert($datas);
	}

}