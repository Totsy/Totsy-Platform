<?php

namespace app\extensions\adapter\data\source;

use Mongo;
use lithium\core\NetworkException;

class MongoDb extends \lithium\data\source\MongoDb {

	/**
	 * Instantiates the MongoDB adapter with the default connection information.
	 *
	 * @see lithium\data\Connections::add()
	 * @see lithium\data\source\MongoDb::$_schema
	 * @link http://php.net/manual/en/mongo.construct.php PHP Manual: Mongo::__construct()
	 * @param array $config All information required to connect to the database, including:
	 *        - `'database'` _string_: The name of the database to connect to. Defaults to `null`.
	 *        - `'host'` _string_: The IP or machine name where Mongo is running, followed by a
	 *           colon, and the port number. Defaults to `'localhost:27017'`.
	 *        - `'persistent'` _mixed_: Determines a persistent connection to attach to. See the
	 *           `$options` parameter of
	 *            [`Mongo::__construct()`](http://www.php.net/manual/en/mongo.construct.php) for
	 *            more information. Defaults to `false`, meaning no persistent connection is made.
	 *        - `'timeout'` _integer_: The number of milliseconds a connection attempt will wait
	 *          before timing out and throwing an exception. Defaults to `100`.
	 *        - `'schema'` _closure_: A closure or anonymous function which returns the schema
	 *          information for a model class. See the `$_schema` property for more information.
	 *        - `'gridPrefix'` _string_: The default prefix for MongoDB's `chunks` and `files`
	 *          collections. Defaults to `'fs'`.
	 *        - `'replicaSet'` _boolean_: See the documentation for `Mongo::__construct()`. Defaults
	 *          to `false`.
	 *
	 * Typically, these parameters are set in `Connections::add()`, when adding the adapter to the
	 * list of active connections.
	 */
	public function __construct(array $config = array()) {
		$defaults = array(
			'persistent' => true,
			'login'      => null,
			'password'   => null,
			'host'       => Mongo::DEFAULT_HOST . ':' . Mongo::DEFAULT_PORT,
			'database'   => null,
			'timeout'    => 5000,
			'replicaSet' => false,
			'schema'     => null,
			'gridPrefix' => 'fs'
		);
		parent::__construct($config + $defaults);
	}

	/**
	 * Connects to the Mongo server. Matches up parameters from the constructor to create a Mongo
	 * database connection.
	 *
	 * @see lithium\data\source\MongoDb::__construct()
	 * @link http://php.net/manual/en/mongo.construct.php PHP Manual: Mongo::__construct()
	 * @return boolean Returns `true` the connection attempt was successful, otherwise `false`.
	 */
	public function connect() {
		$cfg = $this->_config;
		$this->_isConnected = false;

		$host = is_array($cfg['host']) ? join(',', $cfg['host']) : $cfg['host'];
		$login = $cfg['login'] ? "{$cfg['login']}:{$cfg['password']}@" : '';
		$connection = "mongodb://{$login}{$host}" . ($login ? "/{$cfg['database']}" : '');
		$options = array(
			'connect' => true, 'timeout' => $cfg['timeout'], 'replicaSet' => $cfg['replicaSet']
		);

		try {
			if ($persist = $cfg['persistent']) {
				$options['persist'] = $persist === true ? 'default' : $persist;
			}
			$this->server = new Mongo($connection, $options);

			if ($this->connection = $this->server->{$cfg['database']}) {
				$this->_isConnected = true;
				if ( isset($cfg['setSlaveOkay']) ){
					$this->connection->setSlaveOkay( $cfg['setSlaveOkay'] );
				} else{
					$this->connection->setSlaveOkay(false);
				}
			}
		} catch (Exception $e) {
			throw new NetworkException("Could not connect to the database.", 503, $e);
		}
		return $this->_isConnected;
	}
}

?>