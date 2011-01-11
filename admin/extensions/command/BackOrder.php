<?php

namespace admin\extensions\command;

use admin\models\OrderShipped;
use admin\models\BackOrdered;
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
class BackOrder extends \lithium\console\Command {

	/**
	 * CSV or XLS file to be imported.
	 * 
	 * @var string
	 */
	public $file = null;

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
	public $path = null;

	/**
	 * File extensions.
	 */
	public $type = null;

	/**
	 * Entry point for the OrderImport script.
	 *
	 * The run method sets up the connection to the collection and
	 * routes the parsing type based on the type of file being parsed.
	 * The default parsing is tab delimited in favor of
	 */
	public function run() {
		$this->header('BackOrder Processor');
		$this->collection = BackOrdered::collection();
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
		}
		if ($this->path) {
			$this->_xlsParser();
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
					$this->_save($row, $shipRecords);
				}
			}
		}
		return true;
	}

	/**
	 * Parse Tab or CSV delimited files.
	 */
	protected function _tabParser() {
		$nn = 0;
		die(var_dump(OrderShipped::schema()));
		while (($data = fgetcsv($handle, 1000, chr(9))) !== FALSE) {
			$c = count($data);
			for ($x = 0; $x < $c ; $x++) {
				$eventItems[$nn][$key[$x]] = $data[$x];
			}
			$nn++;
		}
		return true;
	}

	/**
	 * Saves individual record to the order.shipped Mongo Collection
	 *
	 * @see admin\models\OrderShipped;
	 */
	private function _save($row, $shipRecords) {
		if (!empty($shipRecords[$row - 1])) {
			$record = $shipRecords[$row - 1] + array('hash' => md5(implode("", $shipRecords[$row - 1])));
			try {
				$record['Order Date'] = new MongoDate(strtotime($record['Order Date']));
				$id = $this->collection->save($record);
				$this->out("Saving Mongo Record: $record[hash]");
			} catch (Exception $e) {
				$this->out("Hash: $record[hash] has already been saved");
			}
		}
		return true;
	}



}

?>