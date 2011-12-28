<?php

namespace app\extensions\data;

use RuntimeException;

class DocumentSet extends \lithium\data\collection\DocumentSet {

	protected $_processes = array();

	protected $_finalizers = array();

	protected $_processContext = array();

	public function addProcess($callback) {
		if ($this->_hasInitialized) {
			throw new RuntimeException("Processes cannot be added after iteration has begun.");
		}
		$this->_processes[] = $callback;
	}

	public function finalize($callback) {
		if ($this->_hasInitialized) {
			throw new RuntimeException("Processes cannot be added after iteration has begun.");
		}
		$this->_finalizers[] = $callback;
	}

	public function summarize() {
		$result = $this->_processContext;

		foreach ($this->_finalizers as $process) {
			$result = $process($result);
		}
		return $result;
	}

	public function next() {
		if (!$this->_processes) {
			return parent::next();
		}
		$prev = key($this->_data);
		$result = parent::next();

		if ($this->_valid && $prev !== key($this->_data)) {
			unset($this->_data[$prev]);
		}
		return $result;
	}

	protected function _populate($data = null, $key = null) {
		if ($this->closed() || !($model = $this->_model)) {
			return;
		}
		$conn = $model::connection();

		if (($data = $data ?: $this->_result->next()) === null) {
			return $this->close();
		}
		$result = $conn->cast($model, array($key => $data), array(
			'exists' => true, 'first' => true, 'pathKey' => $this->_pathKey
		));
		foreach ($this->_processes as $process) {
			$result = $process($result, $this->_processContext);
		}
		return $this->_data[] = $result;
	}
}

?>