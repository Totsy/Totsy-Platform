<?php

namespace admin\controllers;

use lithium\core\Libraries;
use lithium\core\Environment;
use admin\models\File;
use admin\models\EventImage;
use admin\extensions\sabre\dav\auth\backend\Lithium as Sabre_DAV_Auth_Backend_Lithium;
use Sabre_DAV_Server;
// use Sabre_DAV_FS_Directory;
// use admin\extensions\sabre\dav\TotsyTree;
use admin\extensions\sabre\dav\ModelDirectory;
use Sabre_DAV_Locks_Backend_File;
use Sabre_DAV_Locks_Plugin;
use Sabre_DAV_TemporaryFileFilterPlugin;
use Sabre_DAV_Auth_Plugin;

/**
 * This controller works with SWFUpload to provide flash/javascript
 * file upload functionality with Lithium.
 */
class FilesController extends \lithium\action\Controller {

	/**
	 * Currently index is just setting up the view.
	 */
	public function index() {}

	/**
	 * Get the uploaded file from $POST and write it to GridFS if valid.
	 *
	 * @return array
	 */
	public function upload($type = null) {
		
		switch(strtolower($type)) {
			case 'event':
				static::processEventImages();
				static::processEventItemImages();
				break;
				
		}
		
		//echo 'console.dir('.json_encode($this->request->data).');';
		
		echo 'console.dir('.json_encode($event_images).');';
		
		exit();
		
		$success = false;

		$enabled = array('item', 'event', 'banner', 'service');
		$this->_render['template'] = in_array($type, $enabled) ? $type : 'upload';
		
		

		/* Check that we have POST data. */
		if ($this->request->data && $this->_validate($this->request->data)) {
			$this->_render['layout'] = false;

			$handle = fopen($this->request->data['Filedata']['tmp_name'], 'rb');
			$meta['name'] = $this->request->data['Filedata']['name'];

			/* Check if there are any tags associated with the image. */
			if (array_key_exists('tag', $this->request->data)){
				$meta['tag'] = $this->request->data['tag'];
			}

			$file = File::write($handle, $meta);
			fclose($handle);

			if ($file) {
				/* We're using name -> fileName here for BC. */
				return array('id' => $file->_id, 'fileName' => $meta['name']);
			}
		}
	}
	
	/**
	 * Processes event images uploaded from a web browser via the admin UI.
	 * 
	 * @return 
	*/
	public static function processEventImages() {
		$event_images = array();
		if(isset($this->request->data['Filedata']) && !empty($this->request->data['Filedata'])) {
			foreach($this->request->data['Filedata'] as $file) {
				// Event Image
				if(preg_match('/^e\_\_i\_\_/i', $file['name'])) {
					$event_images['event_image'] = $file;
				}
				// Event Logo
				if(preg_match('/^e\_\_l\_\_/i', $file['name'])) {
					$event_images['event_logo'] = $file;
				}
				// Event Big Splash Image
				if(preg_match('/^e\_\_sbi\_\_/i', $file['name'])) {
					$event_images['splash_big_image'] = $file;
				}
				// Event Small Splash Image
				if(preg_match('/^e\_\_ssi\_\_/i', $file['name'])) {
					$event_images['splash_small_image'] = $file;
				}
			}
		}
		
	}
	
	/**
	 * Processes event item images uploaded from a web browser via the admin UI.
	 * 
	 * @return 
	*/
	public static function processEventItemImages() {
		$item_images = array();
		if(isset($this->request->data['Filedata']) && !empty($this->request->data['Filedata'])) {
			foreach($this->request->data['Filedata'] as $file) {
				// Event Image
				if(preg_match('/^e\_\_i\_\_/i', $file['name'])) {
					$event_images['event_image'] = $file;
				}
				// Event Logo
				if(preg_match('/^e\_\_l\_\_/i', $file['name'])) {
					$event_images['event_logo'] = $file;
				}
				// Event Big Splash Image
				if(preg_match('/^e\_\_sbi\_\_/i', $file['name'])) {
					$event_images['splash_big_image'] = $file;
				}
				// Event Small Splash Image
				if(preg_match('/^e\_\_ssi\_\_/i', $file['name'])) {
					$event_images['splash_small_image'] = $file;
				}
			}
		}
		
	}

	public function dav() {
		$resources = Libraries::get('admin', 'resources');

		// @todo This is temporary and will be replaced by an implementation
		//       storing files directly in GridFS.
		// $root = new Sabre_DAV_FS_Directory($resources . '/dav/share');
		$root = array(
			new ModelDirectory(array('value' => '\admin\models\Event')),
			// new ModelDirectory('items')
		);
		$server = new Sabre_DAV_Server($root);

		$server->debugExceptions = !Environment::is('production');
		$server->setBaseUri('/files/dav');

		$backend = new Sabre_DAV_Locks_Backend_File($resources . '/dav/locks.dat');
		$plugin = new Sabre_DAV_Locks_Plugin($backend);
		$server->addPlugin($plugin);

		$plugin = new Sabre_DAV_TemporaryFileFilterPlugin($resources . '/dav/temporary');
		$server->addPlugin($plugin);

		$backend = new Sabre_DAV_Auth_Backend_Lithium();
		$plugin = new Sabre_DAV_Auth_Plugin($backend, 'Totsy DAV'); /* 2nd arg is the realm. */
		$server->addPlugin($plugin);

		$server->exec();
		exit;
	}

	/**
	 * Validate the file that is being uploaded.
	 *
	 * @return boolean
	 */
	protected function _validate($data) {
		if (!isset($data['Filedata'])) {
			// 'Validation failed. Expected file upload field to be named Filedata.';
			return false;
		}
		if (!is_uploaded_file($tmp = $data['Filedata']['tmp_name'])) {
			// "Upload is not a file. PHP didn't like it.";
			return false;
		}
		if ($error = $data['Filedata']['error']) {
			// 'Validation failed. ' . $this->_errorMessage($error);
			return false;
		}
		return true;
	}

	/**
	 * Parses file upload error code into human-readable phrase.
	 *
	 * @param int $err PHP file upload error constant.
	 * @return string human-readable phrase to explain issue.
	 */
	protected function _errorMessage($error) {
		$message = null;

		switch ($error) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
				$message = 'The uploaded file exceeds the upload_max_filesize ('.ini_get('upload_max_filesize').') in php.ini.';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
				break;
			case UPLOAD_ERR_PARTIAL:
				$message = 'The uploaded file was only partially uploaded.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$message = 'No file was uploaded.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$message = 'The remote server has no temporary folder for file uploads.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$message = 'Failed to write file to disk.';
				break;
			default:
				$message = 'Unknown File Error. Check php.ini settings.';
				break;
		}

		return $message;
	}
}

?>