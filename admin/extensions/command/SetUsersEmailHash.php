<?php

namespace admin\extensions\command;

use MongoId;

use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\models\User;
use admin\extensions\command\Pid;

/**
 * Make a check for a normal transaction email.
 */
class SetUsersEmailHash extends \lithium\console\Command {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	
	/**
	 * Sleep time after some number of updates 
	 * In secunds
	 * 
	 * @var float 
	 */
	public $sleep_time = 0.5;
	
	/**
	 * Go sleep after this number of updates
	 * 
	 * @var integer
	 */
	public $sleep_after = 1000;
	
	/**
	 * Directory of tmp files.
	 *
	 * @var string
	 */
	public $tmp = '/resources/totsy/tmp/';
	
	/**
	 * Instances
	 */
	public function run() {
		Logger::info("\n");
		Logger::info('Emails Hash Processor');
		
		Environment::set($this->env);
		
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$pid = new Pid($this->tmp,  'EmailHashProcessor');
		
		if ($pid->already_running == false) {
			$this->doConversion();
		} else {
			Logger::info('Already Running! Stoping Execution'."\n");
		}
		Logger::info('Emails Hash Processor Finished'."\n");
	}
	
	private function doConversion(){
		$arguments = array(
			'email_hash' => array(
				'$exists'=>false
			)
		);
		$fields = array(
			'_id' => true,
			'email' => true
		);
		$time_start = microtime(true);
		$cursor = User::collection()
							->find($arguments)
							->fields($fields);
		$time_end = microtime(true) - $time_start;
		Logger::info('Found '.$cursor->count().' documents. Total time: '.$time_end);
		
		$time_start = microtime(true);
		$result = $this->processCursor($cursor);
		$time_end = microtime(true) - $time_start;
		Logger::info('Update data. Total time: '.$time_end);
		
		if ($result){
			Logger::info('Success');	
		} else {
			Logger::info('FAILED');
		}
	}
	
	private function processCursor(&$cursor){
		$percent = 0;
		$current = 1;
		$sleep = $this->sleep_after;
		$total = (integer) $cursor->count();
		if (0>=$total){ return false; }
		
		$collection = User::collection();
		
		foreach ($cursor as $doc){
			$collection->update(
				array( '_id' => $doc['_id'] ), 
				array(
                	'$set'=>array(
                    	'email_hash' => md5($doc['email'])
                ))
			);	
			$current++;
			
			if ($current == $sleep){
				$per = round(($current/$total) * 100, 2);
				
				if ($per > $percent){
					Logger::info('Done: '.$per.' % ('.$current.')');
					$percent = ceil($per);
				}
							
				$sleep = $current + $this->sleep_after;
				sleep($this->sleep_time);
			}
		}
		
		return true;
	}
	
}