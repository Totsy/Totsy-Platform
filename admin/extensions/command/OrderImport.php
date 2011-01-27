<?php

namespace admin\extensions\command;

use admin\models\OrderShipped;
use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;


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
		'.DS_Store'
	);

	/**
	 * Directory of files holding shipping logs.
	 *
	 * @var string
	 */
	public $dir = null;

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
		$this->header('Ship File Processor');
		Environment::set($this->env);
		$this->collection = OrderShipped::collection();
		$this->collection->ensureIndex(array('hash' => 1), array("unique" => true));
		if ($this->dir) {
			$handle = opendir($this->dir);
			while (false !== ($this->file = readdir($handle))) {
				if (!(in_array($this->file, $this->_exclude))) {
		            $this->out("Trying to Process - $this->file");
					$this->path = $this->dir.'/'.$this->file;
					$this->type = substr($this->file, -3);
					switch ($this->type) {
						case 'xls':
							$this->_xlsParser();
							break;
						default:
							$this->_tabParser();
							break;
					}
		        }
		    }
		} else {
			$this->out('Error: No directory provided');
		}
	}

	/**
	 * Parse XLS files.
	 */
	protected function _xlsParser() {
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
	 * Parse Tab or CSV delimited files.
	 *
	 *
	 */
	protected function _tabParser() {
		$nn = 0;
		$header = OrderShipped::$_header;
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
	 * Saves individual record to the order.shipped Mongo Collection
	 *
	 * @see admin\models\OrderShipped;
	 */
	private function _save($shipRecord) {
		if (!empty($shipRecord)) {
			$record = $shipRecord + array('hash' => md5(implode("", $shipRecord)));
			try {
				$this->collection = OrderShipped::collection();
				$id = $this->collection->save($record);
			} catch (Exception $e) {
				$this->out("Hash: $record[hash] has already been saved");
			}
		}
		return true;
	}



}

?>