<?php

namespace app\models;

use lithium\data\Connections;


class File extends \lithium\data\Model {

	protected $_meta = array("source" => "fs.files");
		
	public function getGridFS() {
		$collection = File::_connection()->connection;
		return $collection->getGridFS();
	}
	
	public function mimeType($entity) {
		if ($entity->mime_type) {
			return $entity->mime_type;
		}
		/* Some files in GridFS may not yet have a `mime_type` field.
		   This field was added later so the code segment below
		   provides BC for that. */

		return static::detectMimeType($entity->file->getBytes());
	}
	
	public static function detectMimeType($data) {
		$context = finfo_open(FILEINFO_MIME);

		if (is_resource($data)) {
			rewind($data);
			$peekBytes = 1000000;
			$result = finfo_buffer($context, fread($data, $peekBytes));
		} else {
			$result = finfo_buffer($context, $data);
		}
		list($type, $attributes) = explode(';', $result, 2) + array(null, null);

		finfo_close($context);

		if ($type != 'application/x-empty') {
			return $type;
		}
	}
	
}


?>
