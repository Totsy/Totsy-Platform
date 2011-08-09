<?php

namespace admin\controllers;

use lithium\core\Libraries;
use lithium\core\Environment;
use admin\models\File;
use admin\models\EventImage;
use Sabre_DAV_Server;
use admin\extensions\sabre\dav\ModelDirectory;
use admin\extensions\sabre\dav\PendingDirectory;
use Sabre_DAV_Locks_Backend_File;
use Sabre_DAV_Locks_Plugin;
use Sabre_DAV_TemporaryFileFilterPlugin;
use Sabre_HTTP_Request;

/**
 * This controller works with SWFUpload to provide flash/javascript
 * file upload functionality with Lithium.
 */
class FilesController extends \lithium\action\Controller {

	/**
	 * Currently index is just setting up the view.
	 */
	public function index() {}

	public function pending() {
		$files = File::pending();
		return compact('files');
	}

	public function delete() {
		$file = File::create(array('_id' => $this->request->id), array('exists' => true));
		$result = $file->delete();

		if ($this->request->is('ajax')) {
			return $this->render(array(
				'status' => $result ? 200 : 500,
				'head' => true
			));
		}
		return $this->redirect($this->request->referer());
	}

	/**
	 * Get the uploaded file from $POST and write it to GridFS if valid.
	 *
	 * @return array
	 */
	public function upload($type = null) {

		switch(strtolower($type)) {
			case 'event':
				$this->processEventImages();
				$this->processEventItemImages();
				break;
            default:
				// This was the old upload() method code...
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
				break;
		}
	}

	/**
	 * Processes event images uploaded from a web browser via the admin UI.
	 *
	 * @return
	*/
	public function processEventImages() {
		$event_images = array();
		if(isset($this->request->data['Filedata']) && !empty($this->request->data['Filedata'])) {
			// Always want an array of objects, but if a single file was uploaded, it will come in as a single object that we can't loop the way we would an array of objects
			$files = (isset($this->request->data['Filedata'][0])) ? $this->request->data['Filedata']:array(0 => $this->request->data['Filedata']);
			// Now loop the array of files
			foreach($files as $file) {
				// Event Image (2 ways to name)
				if(preg_match('/^events\_.+\_image\..*/i', $file['name'])) {
					$event_images['image'] = $file;
				}
				// matches: events_pretty-url.jpg
				// ...but events_pretty-url_anything... won't be matched.
				if(preg_match('/^events\_.+(?<!\_)\..*/i', $file['name'])) {
					$event_images['image'] = $file;
				}
				// Event Logo
				if(preg_match('/^events\_.+\_logo\..*/i', $file['name'])) {
					$event_images['logo'] = $file;
				}
				// Event Big Splash Image (2 ways to name)
				if(preg_match('/^events\_.+\_big\_splash\..*/i', $file['name'])) {
					$event_images['splash_big_image'] = $file;
				}
				if(preg_match('/^events\_.+\_splash\_big\..*/i', $file['name'])) {
					$event_images['splash_big_image'] = $file;
				}
				// Event Small Splash Image (2 ways to name)
				if(preg_match('/^events\_.+\_small\_splash\..*/i', $file['name'])) {
					$event_images['splash_small_image'] = $file;
				}
				if(preg_match('/^events\_.+\_splash\_small\..*/i', $file['name'])) {
					$event_images['splash_small_image'] = $file;
				}
			}
		}
		// Resize and save
		if(!empty($event_images)) {
			foreach($event_images as $k => $v) {
				EventImage::resizeAndSave($k, $v);
			}
		}
	}

	/**
	 * Processes event item images uploaded from a web browser via the admin UI.
	 *
	 * @return
	*/
	public function processEventItemImages() {
		$item_images = array();
		if(isset($this->request->data['Filedata']) && !empty($this->request->data['Filedata'])) {
			foreach($this->request->data['Filedata'] as $file) {
				// TODO: Regex for event item images and then resize and save...
			}
		}

	}

	/**
	 * Provides a single point of entry for all DAV requests. This action
	 * configures any SabreDAV classes, plugins and custom VFS implementations.
	 * Any URLs below the route leading to this action are mapped/handled by
	 * SabreDAV.
	 *
	 * @return void
	 */
	public function dav() {
		/*
		   At this point lithium\action\Request has already opened
		   `php://input` and read from it. The following provides a workaround
		   and re-enables Sabre's request object to read the body.
		*/
		if ($this->request->is('put')) {
			$stream = fopen('php://temp', 'w+b');
			fwrite($stream, current($this->request->data));
			rewind($stream);

			Sabre_HTTP_Request::$defaultInputStream = $stream;

			/* Uncomment to unset data if this gets to heavy on memory. */
			// unset($this->request->data);
		}

		$root = array(
			new ModelDirectory(array('value' => '\admin\models\Event')),
			new ModelDirectory(array('value' => '\admin\models\Item')),
			new PendingDirectory()
		);
		$server = new Sabre_DAV_Server($root);

		$server->debugExceptions = !Environment::is('production');
		$server->setBaseUri('/files/dav');

		/* Filtering and locking are still using local files. */
		$resources = Libraries::get('admin', 'resources');

		$backend = new Sabre_DAV_Locks_Backend_File($resources . '/dav/locks.dat');
		$plugin = new Sabre_DAV_Locks_Plugin($backend);
		$server->addPlugin($plugin);

		$plugin = new Sabre_DAV_TemporaryFileFilterPlugin($resources . '/dav/temporary');
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