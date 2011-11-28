<?php

namespace admin\controllers;

use lithium\analysis\Logger;
use admin\models\File;
use admin\models\EventImage;
use admin\models\Event;
use admin\models\Item;
use admin\models\ItemImage;
use admin\models\Banner;
use admin\models\BannerImage;
use admin\models\Affiliate;
use admin\models\AffiliateImage;

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
		$this->_render['layout'] = !$this->request->is('ajax');
		$conditions = array();
		if ($on = $this->request->on) {
			switch($this->request->search_type){
				case 'affiliate':
					Logger::debug('Searching for all pending affiliate backgrounds');
					$conditions += array('affiliate_id' => $on);
				break;
				default:
				Logger::debug('Searching for all pending event images');
					$conditions += array('event_id' => $on);
				break;
			}
		}

		$files = File::pending($conditions);
		return compact('files');
	}

	public function orphaned() {
		$this->_render['layout'] = !$this->request->is('ajax');

		$files = File::orphaned();
		return compact('files');
	}

	public function rename() {
		$file = File::first(array('conditions' => array('_id' => $this->request->id)));

		$result = $file->rename($this->request->data['name'])->save();

		if ($this->request->is('ajax')) {
			$this->_render['type'] = 'json';
			$this->_render['status'] = $result ? 200 : 500;

			return array('name' => $file->name);
		}
		return $this->redirect($this->request->referer());
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

	public function associate() {
		$files = array();
		$result = true;

		if ($this->request->scope == 'pending') {
			$conditions = array();
			if ($on = $this->request->on) {
				$conditions += array('event_id' => $on);
			}
			$files = File::pending($conditions);
		} else {
			if ($file = File::first(array('conditions' => array('_id' => $this->request->id)))) {
				$files[] = $file;
			}
		}
		foreach ($files as $file) {
			$meta = array('name' => $file->name);

			if ($file->event_id) {
				$meta += array('event_id' => $file->event_id);
			}
			if ($file->banner_id) {
				$meta += array('banner_id' => $file->banner_id);
			}
			if ($file->affiliate_id) {
				$meta += array('affiliate_id' => $file->affiliate_id);
			}
			$bytes = $file->file->getBytes();
			$eventitems = $file->event_id && ( EventImage::process($bytes, $meta) || ItemImage::process($bytes, $meta) );
			$banners = $file->banner_id && BannerImage::process($bytes, $meta);
			$affiliates = $file->affiliate_id && AffiliateImage::process($file, $meta);
			if ($eventitems || $banners ||	$affiliates	) {
				/* This implicitly moves it into the "orphaned" state. */
				if (!$file->save(array('pending' => false))) {
					$result = false;
					break;
				}
			} else {
				Logger::debug("Was unable to associate `{$meta['name']}`.");
				$result = false;
			}
		}
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

		switch (strtolower($type)) {
			case 'all':
			case 'event':
				if (empty($this->request->data['Filedata'])) {
					return $this->render(array('status' => 500, 'head' => true));
				}
				Logger::debug('Receiving uploaded file.');

				/* Always want an array of objects, but if a single file was uploaded,
				   it will come in as a single object that we can't loop the way we would
				   an array of objects. */
				if (isset($this->request->data['Filedata'][0])) {
					$files = $this->request->data['Filedata'];
				} else {
					$files = array(0 => $this->request->data['Filedata']);
				}

				foreach ($files as $file) {
					$handle = fopen($file['tmp_name'], 'rb');
					$meta = array('name' => $file['name']);

					// An event id may be passed along if the files are Item images.
					// Item images can only be uploaded with a reference to the event id.
					// Any item image uploaded without an event_id reference will not be saved.
					if(isset($this->request->data['event_id'])) {
						$meta['event_id'] = $this->request->data['event_id'];
					}

					if(isset($this->request->data['banner_id'])) {
						$meta['banner_id'] = $this->request->data['banner_id'];
					}
					if(isset($this->request->data['affiliate_id'])) {
						$meta['affiliate_id'] = $this->request->data['affiliate_id'];
					}

					if (EventImage::process($handle, $meta)) {
						Logger::debug("File `{$file['name']}` matched & processed as event image.");

					} else if (ItemImage::process($handle, $meta)) {
						Logger::debug("File `{$file['name']}` matched & processed as item image.");

					} else if (array_key_exists('banner_id', $meta) && BannerImage::process($handle, $meta)) {
						Logger::debug("File `{$file['name']}` matched & processed as banner image.");
					} else { /* All unmatched files are not resized and saved as pending. */
						$file = File::write($handle, $meta + array('pending' => true));
						$id = $file->_id;
						$this->set(compact('id'));
						Logger::debug("the file has id $id");
						Logger::debug("Saving unmatched file `{$file['name']}` as pending.");

					}
					fclose($handle);
				}
				return $this->render(array('status' => 200, 'head' => true));
			break;
			default:
				/* @deprecated This was the old upload() method code... */
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
	 * Validate the file that is being uploaded.
	 *
	 * @deprecated
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
	 * @deprecated
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
