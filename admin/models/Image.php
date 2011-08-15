<?php

namespace admin\models;

use lithium\data\Connections;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class Image extends \admin\models\File {

	protected $_meta = array("source" => "fs.files");

	/**
	 * Image sizes for images. With associated dimensions width and
	 * height. Each item in the array should have following structure.
	 * {{{
	 * 'dimensions' => array(298, 300),
	 * 'field' => 'primary_image',
	 * 'multiple' => false,
	 * 'regex' => array(
	 *    ...
	 * )
	 * }}}
	 *
	 * @var types array
	 */
	public static $types = array();

	/*
	 *
	 * @param string $position The item image position (primary, zoom, etc.)
	 * @param array $data The file data array from the POST data - a single file
	 * @return
	*/
	public static function resizeAndSave($position, $data, $meta = array()) {
		if (empty($data) || !isset(static::$types[$position])) {
			return false;
		}
		list($width, $height) = static::$types[$position]['dimensions'];

		$imagine = new Imagine();
		$filename = null;

		if (is_array($data) && isset($data['tmp_name'])) { /* fileupload */
			$image = $imagine->open($data['tmp_name']);
			$filename = $data['name'];
		} elseif (is_string($data)) { /* bytes */
			$image = $imagine->load($data);
		} elseif (is_resource($data)) {
			rewind($data);
			$image = $imagine->load(stream_get_contents($data));
		} else {
			return false;
		}

		$bytes = $image->resize(new Box($width, $height))->get('png');

		// Write the image to GridFS
		// Return what should be the file object that write() returns... this will have an id to associate
		return static::write($bytes, $meta + array('name' => $filename, 'mime_type' => 'image/png'));
	}

	/**
	 * Processes event item images uploaded from a web browser via the admin UI.
	 * Example Item URL: horses-velour-top-pants-set-fuschia
	 *
	 * @return boolean
	 */
	public static function process($file) {
		$model = str_replace('Image', '', get_class());
		$source = $model::meta('source');

		foreach (static::$types as $name => $type) {
			foreach ($type['regex'] as $regex) {
				/* Resize and save matched files. */
				if (!preg_match($regex, $file['name'])) {
					continue;
				}

				preg_match('/^' . $source . '\_(.+)\_.*/i', $file['name'], $matches);
				$url = isset($matches[1]) ? $matches[1] : false;

				/* If we don't have an event URL, what's the point of saving the image?
				   We could never associate it and the file was probably named incorrectly. */
				if (!$url) {
					continue;
				}

				/* So save it and return the File document object. */
				$file = static::resizeAndSave($name, $file, array('name' => $file['name']));

				if (!$file) {
					continue;
				}
				return $model::updateImage($name, (string) $file->_id, compact('url'));
			}
		}
		return false;
	}
}

?>