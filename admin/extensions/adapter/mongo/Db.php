<?php 

namespace admin\extensions\adapter\mongo;

use Mongo;
use MongoId;
use MongoCode;
use MongoDate;
use MongoDBRef;
use MongoRegex;
use MongoGridFSFile;
use lithium\util\Inflector;
use lithium\core\NetworkException;
use Exception;


class Db extends \lithium\data\source\MongoDb {

	/**
	 * The Mongo class instance manager.
	 *
	 * @var array of objects
	 */
	public static $server_manager = array(
		'master' => null,
		'slave' => null
	);

	/**
	 * Stores connection string for master nad slave
	 * 
	 * @var array of strings
	 */
	private $server_manager_config = array(
		'master' => null,
		'slave' => null
	);

	/**
	 * Indicates wheather to use on $slave->setSlaveOkay(true) or not
	 * 
	 * @var boolean
	 */
	public $isSlaveOk = false;

	/**
	 * Connects to the Mongo server.
	 *
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
			//$this->server = new Mongo($connection, $options);
			$server = new Mongo($connection, $options);
			$this->server = $server;
			$isMaster = $this->detectAndSelect($server);
			if ($isMaster === true){
				static::$server_manager['master'] = $server;
				if(!is_null($this->server_manager_config['slave'])){
					$connect = is_array($this->server_manager_config['slave']) ? join(',', $this->server_manager_config['slave']) : $this->server_manager_config['slave'];
					static::$server_manager['slave'] = new Mongo('mongodb://'.$connect, $options);
				}
			} 
			unset($server);
			if (array_key_exists('setSlaveOkay',$cfg) && $cfg['setSlaveOkay'] === true ){
				if (!is_null(static::$server_manager['slave'])){
					$this->isSlaveOk = true;
					static::$server_manager['slave']->setSlaveOkay(true);
				}	
			}

			if ($this->connection = $this->server->{$cfg['database']}) {
				$this->_isConnected = true;
			}
		} catch (Exception $e) {
			throw new NetworkException("Could not connect to the database.", 503, $e);
		}
		return $this->_isConnected;
	}


	public function __destruct() {
		if ($this->_isConnected) {
			$this->disconnect();
		}
	}

	public function disconnect() {
		unset($this->connection, $this->server);
		foreach (static::$server_manager as $server){
			if (!is_null($server) && $server->connection) {
				try {
					$server->close();
				} catch (Exception $e) {}
			}
		}
		$this->isSlaveOk = false;
		$this->_isConnected = false;
		return true;
	}

	public function read($query, array $options = array()) {
		$this->getConnection();
		return parent::read($query,$options);
	}

	public function group($group, $context) {
		$this->getConnection();
		return parent::group($group,$context);
	}

	public function update($query, array $options = array()) {
		$this->getConnection(true);
		return parent::update($query,$options);
	}

	public function delete($query, array $options = array()) {
		$this->getConnection(true);		
		return parent::delete($query,$options);
	}

	public function create($query, array $options = array()) {
		$this->getConnection(true);
		return parent::create($query,$options);		
	}

	/**
	 * By calling this method you looking for all available hosts
	 * in a replica set... did not test on single db )))
	 * 
	 * @param dbHandle $server
	 */
	private function detectAndSelect(&$server){
		$isMaster = true;
		$host = is_array($this->_config['host']) ? join(',', $this->_config['host']) : $this->_config['host'];
		$availableHosts = $server->getHosts();
		$slaves = array();
		if (is_array($availableHosts) && count($availableHosts)>0){
			foreach ($availableHosts as $name=>$aH){
				$aH['name'] = $name;
				if ($aH['state'] == 2 && preg_match("/".$aH['name']."/",$host)){
					$isMaster = false;
				}
				if ($aH['state'] == 1) {
					$this->server_manager_config['master'] = $aH['name'];
				} else if ($aH['state'] == 2){
					$slaves[] = $aH['name'];
				}
			}
		} 
		unset($availableHosts);
		$cs = count($slaves);
		$serverId = 0;
		if ($cs>0){
			if ($cs>1) { 
				//$slave = join(',', $slaves);
				$slaveId = mt_rand(0,count($slaves)-1); 
			} else { $slave = $slaves[0]; }
			
			//static::logChooser($slaves[$slaveId]['name']);
			$this->server_manager_config['slave'] = $slaves; 
		}
		return $isMaster;
	}

	/**
	 * This method checks the connection and in case of disconnected
	 * will try to connect again. This loop could be done up to 5 times.
	 * If you have replica set with primary and slaves db servers you will 
	 * not be able to write to slave, so need to have connection to primary 
	 * (for write), so it means that you need to pass truw for write param.
	 * 
	 * @param boolean $write sets proper connection with the server. 
	 */
	private function getConnection($write = false,$try = 0){
		if (!is_null(static::$server_manager['slave']) && $write === false){
			$this->server = static::$server_manager['slave'];
		} else {
			$this->server = static::$server_manager['master'];
		} 

		// Make shure that we are connected
		// if not then do connect and getConnection again

		if ($this->server->connected == 1){
			if ($this->connection = $this->server->{$this->_config['database']}) {
			$this->_isConnected = true;
			$this->triesToGetConnection = 0;
			}			
		} else {
			// just to prevent 8 on a side loop ;) 
			if ( $try<=5 ){
				$try++;
				$this->connect();
				$this->getConnection($write,$try);
			}
		}
	}


	private static function logChooser ($name){
		//$fh = fopen(LITHIUM_APP_PATH . '/resources/tmp/logs/MongoDbSlaveSelector.log','a');
		$fh = fopen('/tmp/slaveSelector.log','a');
		fwrite($fh,date('[ d-m-Y H:i:s ]').' '.$name."\n");
		fclose($fh);
	}
}
?>