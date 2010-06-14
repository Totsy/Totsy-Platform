<?php

namespace app\controllers;
use app\models\File;


/**
 * This controller works with SWFUpload to provide flash/javascript
 * file upload functionality with lithium. 
 */
class UploadsController extends \lithium\action\Controller {

	
	/**
	 * Currently index is just setting up the view. 
	 */
	public function index() {

	}
	
	/**
	 * Get the uploaded file from $POST and write it to GridFS if valid
	 * @return array
	 */
	public function upload() {	
		$success = false;
		//Check that we have a $POST	
		if ($this->request->data) {
			if ($this->validate()) {
				$success = $this->write();
				if ($success) {
					$id = $this->id;
					$fileName = $this->fileName;
				}
			}
		}
		
		return compact('id', 'fileName');
	}
	
	/**
	 * Validate the file that is being uploaded
	 * @return boolean
	 */
	private function validate() {
		$post = $this->request->data['Filedata'];
		$uploadError = $this->request->data['Filedata']['error'];
		$tmpFile = $this->request->data['Filedata']["tmp_name"];
		$dataRecieved = is_uploaded_file($tmpFile);
		
		if (!isset($post)){
			$this->setError = 'Validation failed. Expected file upload field to be named Filedata.';
		}
		if (!$dataRecieved) {
			$this->setError = "Upload is not a file. PHP didn't like it.";
		}
		if ($uploadError) {
			$this->setError = 'Validation failed. ' . $this->getErrorMessage($uploadError); 
		}
		return !$uploadError && $post && $tmpFile; 
	}
	
 	/** 
     * parses file upload error code into human-readable phrase. 
     * @param int $err PHP file upload error constant. 
     * @return string human-readable phrase to explain issue. 
     */ 
    private function getUploadErrorMessage($error) { 
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
        } 

        return $message; 
    }
	
	/**
	 * Writes the file to GridFS and sets the Object ID of the file. We are not checking
	 * if this file has been uploaded already. There will be a master cron job to clear out
	 * all unused files from GridFS. 
	 * @return boolean
	 */
	private function write() {
		$success = false;
		$grid = File::getGridFS();
		$this->fileName = $this->request->data['Filedata']['name'];
		$objectId = $grid->storeUpload('Filedata', $this->fileName);
		$this->id = $objectId->__toString();
		if($this->id) {
			$success = true;
		}
		return $success;
	}
	
}
?>