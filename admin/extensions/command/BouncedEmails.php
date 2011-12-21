<?php

namespace admin\extensions\command;

use MongoDate;

use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\models\User;
use admin\models\EmailsBounced;
use admin\extensions\Mailer;
use admin\extensions\command\Pid;

/**
 * Update bounsed email form postman. Right now we have Sailthru as our postman
 */
class BouncedEmails extends \lithium\console\Command {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	public $list = 'registered';
	protected $postman = 'sailthru';
	protected $job_id = null;
	protected $job_status = null;
	protected $job_file = null;
	protected $tmp_folder = '/tmp/';
	protected $downloaded = null;
	protected $bounceList = array(
		'softbounce',
		'hardbounce'
	);
	protected $exe_times = array();
	
	/**
	 * Directory of tmp files.
	 *
	 * @var string
	 */
	public $tmp = '/resources/totsy/tmp/';
	
	public function run(){
		Logger::info("\n");
		Logger::info('Bounsed Emails Processor');
		Environment::set($this->env);
		
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$pid = new Pid($this->tmp,  'BouncedEmails');
		
		if ($pid->already_running == false) {
			$this->getCommandLineParams();
			
			//print_r($this);
			
			$this->runBouncer();
		} else {
			Logger::info('Already Running! Stoping Execution'."\n");
		}
		Logger::info('Bounsed Emails Processor Finished'."\n");
	}
	
	
	public function runBouncer(){

		Logger::info('STEP 1: request a report list data job');
		$st = microtime(true);
		//
		if (is_null($this->job_file)){
			$this->makeJob();
		} else {
			$this->job_status = 'completed';
		}
		$et = microtime(true) - $st;
		Logger::info('STEP 1: DONE. Execution time: '.$et);
		
		if (is_null($this->job_id)){
			Logger::info('no job_id received. Exit');
			return;
		}
		
		Logger::info('STEP 2: make shure job (#'.$this->job_id.') is finished');
		$st = microtime(true);
		//
		if ($this->job_status != 'completed'){
			$exe_time = 60*60*2;
			$max = time()+$exe_time;
			while  ($this->job_status != 'completed' ){
				$this->checker();
				
				//stop script after 2 hours if job is not completed
				if (time()>$max){
					Logger::info('Max execution time of '.$exe_time." sec. exeded\n");
					return;
				}
				// if job not completed wait for 10 sec and try again
				sleep(10);
			}
		}
		$et = microtime(true) - $st; 
		Logger::info('STEP 2: DONE. Execution time: '.$et);
		
		Logger::info('STEP 3: download data file ('. $this->job_file .')');
		$st = microtime(true);
		//
		$this->downloader();
		$et = microtime(true) - $st;
		Logger::info('STEP 3: DONE. Execution time: '.$et);		

		Logger::info('STEP 4: data parcer');
		$st = microtime(true);
		//
		$parsed = $this->parseDataFile();
		$et = microtime(true) - $st;
		Logger::info('STEP 4: DONE. Execution time: '.$et);
		
		if (count($parsed)==0){
			//kill the scropt if no data returned
			Logger::info('No data to add to db. Exit');
			return;
		}
		
		Logger::info('STEP 5: add parced data to db');
		$st = microtime(true);
		//
		$this->addRecords($parsed);
		$et = microtime(true) - $st;
		Logger::info('STEP 5: DONE. Execution time: '.$et);
		
		Logger::info('STEP 6: Cleaning ... ');
		if (file_exists($this->tmp_folder.$this->downloaded)){
			unlink($this->tmp_folder.$this->downloaded);
		}
	}
	
	protected function makeJob(){
		$job = Mailer::exportJobListData($this->list);

		$this->job_id = $job['job_id'];
		$this->job_status = $job['status'];
		
		if (array_key_exists('export_url', $job)){
			$this->job_file = $job['export_url'];
		}
	}
	
	protected function checker(){
		$job = Mailer::checkJobStatus($this->job_id);
		
		$this->job_status = $job['status'];
		
		if ($this->job_status == 'completed' && array_key_exists('export_url', $job)){
			$this->job_file = $job['export_url'];
		}
	}
	
	protected function downloader(){
		// set tmp.dat file name
		$this->downloaded = md5(time());
		//file handler for the remote file
		$desctination = fopen($this->tmp_folder.$this->downloaded.'.dat','w');
		if ($desctination == false){
			Logger::info('Cannot open destination file: "'.$this->tmp_folder.$this->downloaded.'.dat"');
			return;
		}
		//file handler for tmp file
		$source = fopen($this->job_file,'r');
		if (!$source){
			Logger::info('Cannot open remote file: '.$this->job_file);
			return array();
		}
		//bytes downloaded
		$down = 0;
		//download 1024 bytes and write them into tmp file
		while( $data=fread($source,1024)){
			
			fwrite($desctination, $data);
			$down = $down + 1024;
		}
		Logger::info('Downloaded: '.$down.' bytes');
		
		//close remote file handler
		fclose($source);
		// close tmp file handler
		fclose($desctination);
	}
	
	protected function parseDataFile(){
		
		$file = array();
		$header = null;
		if (!file_exists($this->tmp_folder.$this->downloaded.'.dat')){
			Logger::info('File "'.$this->tmp_folder.$this->downloaded.'.dat" does not exist');
			return $file;
		}
		if (($handle = fopen($this->tmp_folder.$this->downloaded.'.dat', 'r')) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	if (empty($header)){ 
		    		$header = 1; 
		    	} else if (in_array($data['2'], $this->bounceList)) {
		    		$file[] = array(
		    			'email_hash'=>$data['1'],
		    			'engagement'=>$data['2'],
		    			'delivery' => array(
		    				'status_time' => new MongoDate(strtotime($data['12'])),
		    				'message' => $data['13']
		    			)
		    		);
		    	}
		    }
		    fclose($handle);
		}
		return $file;
	}
	
	protected function addRecords($data){
		$users = User::collection();

		foreach ($data as $key=>$value){

			$users->update(
				array( 'email_hash' => $value['email_hash'] ),
				array( '$set' => array( 
									'email_engagement' => array(
										'type' => $value['engagement'],
										'date' => new MongoDate(),
										'delivery' => $value['delivery']
									)
								))
			);
		}
	}
	
	private function getCommandLineParams(){
		$args = $this->request->argv;
		$params = array();
		
		foreach($args as $arg){
			if ($arg{0} == '-'){
				parse_str($arg,$a);
				$key = preg_replace("/[\-]+/", '', key($a));
				$params[ $key ] = $a[key($a)];
			}
		}
		
		$vars = get_class_vars(get_class($this));
		foreach ($vars as $var=>$value){
			if (array_key_exists($var,$params)){
				$this->{$var} = $params[$var];
			}
		}
	}
}