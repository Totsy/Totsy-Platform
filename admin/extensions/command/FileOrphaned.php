<?php

namespace admin\extensions\command;

use admin\models\File;

/**
 * Provides tools to manage orphaned files.
 */
class FileOrphaned extends \lithium\console\Command {

	/**
	 * Detects and flags orphaned files for later manual removal.
	 */
	public function run() {
		$this->header('Searching for orphaned files...');

		$data = File::all(array(
			'conditions' => array(
				'$or' => array(
					array('pending' => false),
					array('pending' => array('$exists' => false)) /* BC */
				)
			)
		));
		foreach ($data as $item) {
			$before = $item->orphaned;

			if ($item->orphaned = !File::used($item->_id)) {
				$this->out("File `{$item->_id}` flagged as orphaned.");
			} elseif ($before) {
				$this->out("File `{$item->_id}` unflagged.");
			}
			if ($before != $item->orphaned) {
				$item->save();
			}
		}
		$this->out("Done.");
		return true;
	}
}

?>