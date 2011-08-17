<?php

namespace li3_dav\extensions\action;

class RequestSelector extends \lithium\core\StaticObject {

	protected static $_classes = array(
		'action' => 'lithium\action\Request',
		'dav' => 'li3_dav\extensions\action\DavRequest'
	);

	public static function create() {
		$isDav = false;

		if (isset($_GET['url'])) {
			$isDav = strpos($_GET['url'], 'files/dav') === 0;
		}

		$agents = array('Cyberduck');
		$regex = '/(' . implode('|', $agents) . ')/';
		$isDav = $isDav || preg_match($regex, $_SERVER['HTTP_USER_AGENT']);

		return static::_instance($isDav ? 'dav': 'action');
	}
}

?>