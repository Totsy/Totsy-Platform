<?php

namespace admin\controllers;

use lithium\core\Libraries;
use lithium\core\Environment;
use admin\models\File;
use admin\extensions\dav\Auth;
use Sabre_DAV_Server;
use Sabre_DAV_FS_Directory;
use Sabre_DAV_Locks_Backend_File;
use Sabre_DAV_Locks_Plugin;
use Sabre_DAV_TemporaryFileFilterPlugin;

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
		$success = false;

		$this->_render['template'] = in_array($type, array('item', 'event','banner','service', 'affiliate')) ? $type : 'upload';

        //Check if there are any tags associated with the image
        if(array_key_exists('tag',$this->request->data)){
            $meta = array('tag' => $this->request->data['tag'] );
        }else{
            $meta = null;
        }
		// Check that we have a POST
		if (($this->request->data) && $this->validate() && $this->write($meta)) {
			$id = $this->id;
			$fileName = $this->fileName;
			$tag = $this->tag;
		}
		return compact('id', 'fileName', 'tag');
	}

	public function dav() {
		$resources = Libraries::get('admin', 'resources');

		// @todo This is temporary and will be replaced by an implementation
		//       storing files directly in GridFS.
		$root = new Sabre_DAV_FS_Directory($resources . '/dav/share');

		$server = new Sabre_DAV_Server($root);

		$server->debugExceptions = !Environment::is('production');
		$server->setBaseUri('/dav');

		$backend = new Sabre_DAV_Locks_Backend_File($resources . '/dav/locks.dat');
		$plugin = new Sabre_DAV_Locks_Plugin($backend);
		$server->addPlugin($plugin);

		$plugin = new Sabre_DAV_TemporaryFileFilterPlugin($resources . '/dav/temporary');
		$server->addPlugin($plugin);

		$backend = new Auth();
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
	protected function _validate() {
		$post = $this->request->data['Filedata'];
		$uploadError = $this->request->data['Filedata']['error'];
		$tmpFile = $this->request->data['Filedata']["tmp_name"];
		$dataRecieved = is_uploaded_file($tmpFile);

		if (!isset($post)) {
			$this->setError = 'Validation failed. Expected file upload field to be named Filedata.';
		}
		if (!$dataRecieved) {
			$this->setError = "Upload is not a file. PHP didn't like it.";
		}
		if ($uploadError) {
			$this->setError = 'Validation failed. ' . $this->_errorMessage($uploadError);
		}
		return !$uploadError && $post && $tmpFile;
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

	/**
	 * Writes uploaded file to GridFS and sets the MongoId of the file if it doesn't
	 * already exist in MongoDb.
	 *
	 * If the file has already been uploaded then set the id accordingly.
	 *
	 * @return boolean
	 */
	protected function _write($meta = null) {
		$success = false;
		$this->_render['layout'] = false;

		$grid = File::getGridFS();
		$this->fileName = $this->request->data['Filedata']['name'];
		$md5 = md5_file($this->request->data['Filedata']['tmp_name']);

		$file = File::first(array('conditions' => array('md5' => $md5)));

		if ($file) {
			$success = true;
			$this->id = (string) $file->_id;
		} else {
			$this->id = (string) $grid->storeUpload('Filedata', $this->fileName);

			if ($this->id) {
				$success = true;

				if ($meta) {
					$search = File::first(array(
						'conditions' => array('filename' => $this->fileName)
					));
					$search->tag = $meta['tag'];
					$search->save();
			   }
			}
		}
		return $this->id;
	}
}

?>