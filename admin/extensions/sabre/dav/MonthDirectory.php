<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\ItemDirectory;
use MongoDate;
use DateTime;
use DateInterval;

class MonthDirectory extends \admin\extensions\sabre\dav\Directory {

	public function getChild($name) {
		return new ItemDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$model = $this->_model();
		$data = $model::find('all', array(
			'conditions' => $this->_conditions()
		));

		$children = array();
		foreach ($data as $item) {
			$children[] = new ItemDirectory(array('value' => $item->url, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$model = $this->_model();
		return (boolean) $model::find('count', array(
			'conditions' => array('url' => $name)
		));
	}

	protected function _model() {
		return $this->getParent()->getParent()->getValue();
	}

	protected function _conditions() {
		$month = (string) $this;
		$year  = (string) $this->getParent();

		$current = DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-01");

		$start = $current->getTimestamp();
		$end = $current->add(new DateInterval('P1M'))->getTimestamp();

		return array(
			'created_date' => array(
				'$gte' => new MongoDate($start),
				'$lt' => new MongoDate($end)
			)
		);
	}
}

?>