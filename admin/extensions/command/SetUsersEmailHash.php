<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\User;

/**
 * Make a check for a normal transaction email.
 */
class SailThruTest extends \lithium\console\Command {
	
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
	 * Instances
	 */
	public function run() {
		Environment::set($this->env);
		
		$arguments = array(
			'email_hash' => array(
				'$exists'=>false
			)
		);
		$fields = array(
			'_id' => true,
			'email' => true
		);
		$cursor = User::collection()
							->find($arguments)
							->fields($fields);
		$result = $this->processCursor($cursor);
		
		if ($result){
			echo 'Process ended successfylly'."\n";
		}
	}
	
	private function processCursor(&$cursor){
		$percent = 0;
		$current = 1;
		$sleep = $this->sleep_after;
		$total = $cursor->count();
		if ($total>0){ return false; }
		
		$collection = User::collection();
		
		foreach ($cursor as $doc){
			$collection->update(
				array(
					'_id' => new MongoId($doc['_id'])), 
				array(
                	'$set'=>array(
                    	'email_hash' => md5($d['email'])
                ))
			);	
			$current++;
			
			if ($current == $sleep){
				$percent = round(($current/$total) * 10, 3);
				echo 'Done: '.$percent.' %'."\n";			
				$sleep = $current + $this->sleep_after;
				sleep($this->sleep_time);
			}
		}
		echo 'Total documents processed: '.$current."\n";
		
		return true;
	}
	
}