<?php

namespace admin\extensions\command;

use admin\models\OrderShipped;
use admin\models\Order;
use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use admin\extensions\command\Exchanger;
use lithium\analysis\Logger;

/**
 * Li3 Import Command to load Order tracking information.
 *
 * 
 */
class OrderImport extends \lithium\console\Command {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';
	
	/**
	 * Allows verbose info logging. (default = false)
	 */
	public $verbose = false;

	/**
	 * CSV or XLS file to be imported.
	 * 
	 * @var string
	 */
	protected $file = null;

	/**
	 * MongoDB Collection
	 *
	 * @var object
	 */
	public $collection = null;

	/**
	 * Any files that should be excluded during import.
	 *
	 * @var array
	 */
	protected $_exclude = array(
		'.',
		'..',
		'.DS_Store',
		'backup',
		'empty'
	);

	/**
	 * Directory of files holding shipping logs.
	 *
	 * @var string
	 */
	public $source = '/resources/totsy/shipfiles';

	/**
	 * Directory of files holding shipping logs.
	 *
	 * @var string
	 */
	public $backup = '/resources/totsy/shipfiles/backup';

	/**
	 * Full path to file.
	 */
	protected $path = null;

	/**
	 * File extensions.
	 */
	protected $type = null;

	/**
	 * Entry point for the OrderImport script.
	 *
	 * The run method sets up the connection to the collection and
	 * routes the parsing type based on the type of file being parsed.
	 * The default parsing is tab delimited in favor of
	 */
	public function run() {
		Logger::info('Launching Li3 order-import');
		Environment::set($this->env);
		Exchanger::getAll();
		$this->source = LITHIUM_APP_PATH . $this->source;
		$this->backup = LITHIUM_APP_PATH . $this->backup;
		$this->collection = OrderShipped::collection();
		$this->collection->ensureIndex(array('hash' => 1), array("unique" => true));
		$handle = opendir($this->source);
		if ($handle) {
			while (false !== ($this->file = readdir($handle))) {
				if (!(in_array($this->file, $this->_exclude))) {
					Logger::info("Li3 order-import: Processing Ship File - $this->file");
					$this->path = $this->source.'/'.$this->file;
					$this->type = substr($this->file, -3);
					switch ($this->type) {
						case 'xls':
							$this->_xlsParser();
							break;
						default:
							$this->_tabParser();
							break;
					}
					if (!is_dir($this->backup)) {
						mkdir($this->backup);
					}
					if ($this->file) {
						$fullPath = implode('/', array($this->source, $this->file));
						$backupPath = implode('/', array($this->backup, $this->file));
						if (rename($fullPath, $backupPath)) {
							Logger::info("Li3 order-import: Renaming $fullPath to $backupPath");
						}
					}
				}
			}
		} else {
			Logger::error("Could not open $this->source");
		}
		Logger::info('Li3 order-import Finished');
	}

	/**
	 * Parse XLS files.
	 */
	protected function _xlsParser() {
		Logger::info('Parse XLS files.');
		$objReader = PHPExcel_IOFactory::createReaderForFile("$this->path");
		$objPHPExcel = $objReader->load("$this->path");
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
						$shipRecords[$row - 1][$heading[$col]] = $val;
					}
				}
				if (!empty($shipRecords)) {
					$this->_save($shipRecords);
				}
			}
		}
		return true;
	}

	/**
	 * Parse Tab delimited files.
	 *
	 */
	protected function _tabParser() {
		Logger::info('Parse Tab delimited files.');
		$nn = 0;
		$header = array_keys(OrderShipped::schema());
		if (($handle = fopen($this->path, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, chr(9))) !== FALSE) {
				$c = count($data);
				for ($x = 0; $x < $c ; $x++) {
					if ($data[$x] != '') {
						$key = function ($header, $x) {
							switch ($x) {
								case 54:
									return 'OrderId';
									break;
								case 55:
									return 'ItemId';
									break;
								default:
									return $header[$x];
									break;
							}
						};
						$shipRecords[$nn][$key($header, $x)] = $data[$x];
					}
				}
				$this->_save($shipRecords[$nn]);
				$nn++;
			}
		}
		return true;
	}

	/**
	 * Saves individual record to the order.shipped Mongo Collection.
	 * 
	 * There is a hash saved with the record so we can avoid duplicate entries.
	 * The order.shipped collection has an unique index on 'hash'. We are using the
	 * try catch to avoid issues if there is a duplicate.
	 *
	 * @see admin\models\OrderShipped;
	 */
	private function _save($shipRecord) {
		Logger::info('Saving Ship Record…');
		$orderCollection = Order::connection()->connection->orders;
		if (!empty($shipRecord)) {
			$hash = array('hash' => md5(implode("", $shipRecord)));
			$date = array('created_date' => new MongoDate());
			$record = $shipRecord + $hash + $date;
			try {
				$ship = OrderShipped::create($record);
				$ship->save();
				if ($ship) {
					preg_match('/[A-Z0-9]{8,12}/s', $ship->OrderNum, $match);
					Logger::info("Adding Ship Log MongoId $ship->_id to Order $match[0]");
					$orderCollection->update(
						array('$or' => array(array('order_id' => $match[0]), array('_id' => $ship->OrderId))),
						array('$addToSet' => array('ship_records' => $ship->_id)),
						array('upsert' => false)
					);
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				$this->log($message);
			}
		} else {
			Logger::info('Ship Records is empty');
		}
		return true;
	}

	public function log($message, $type = 'info') {
		if ($this->verbose) {
			Logger::$type('Li3 order-import:' . $message);
		}
	}


}

?>