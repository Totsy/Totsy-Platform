<?php

namespace app\controllers;
use app\models\File;

class UploadsController extends \lithium\action\Controller {

	
	
	public function index() {

	}
	
	
	public function upload() {		
		if ($this->request->data) {
			
		}
		$message = '';
		
		if (!isset($_FILES["Filedata"])) {
			echo "Not received, probably exceeded POST_MAX_SIZE";
		}
		else if (!is_uploaded_file($_FILES["Filedata"]["tmp_name"])) {
			echo "Upload is not a file. PHP didn't like it.";
		} 
		else if ($_FILES["Filedata"]["error"] != 0) {
			echo "Upload error no. " + $_FILES["Filedata"]["error"];
		} else {
			$fileName = $_FILES['Filedata']['name'];
			$grid = File::getGridFS();
			$objectId = $grid->storeUpload('Filedata', $fileName);
			$id = $objectId->__toString();
		}
		
		return compact('id', 'fileName');
		
	}
	
	private function validate() {
		
	}
	
	private function getErrorMessage() {
		
	}
	
}