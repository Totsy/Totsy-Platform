<?php

namespace admin\models;

use lithium\data\Connections;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use BadMethodCallException;

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

	public static function write($data, $meta = array()) {
		$meta += array(
			'dimensions' => static::detectDimensions($data)
		);
		return parent::write($data, $meta);
	}

	/*
	 *
	 * @param string $position The item image position (primary, zoom, etc.)
	 * @param array $data The file data array from the POST data - a single file
	 * @returnand we c
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
		
		// Do not resize if the uploaded image is smaller than the dimensions used on the site
		$uploaded_image_box = $image->getSize();
		$uploaded_image_width = $uploaded_image_box->getWidth();
		$uploaded_image_height = $uploaded_image_box->getHeight();
		if($uploaded_image_width < $width || $uploaded_image_height < $height) {
			// Instead, put the smaller image, centered, inside a white box that meets the required dimensions
			$fill_image = $imagine->create(new Box($width, $height), new Color('fff', 100));
			// Figure out the x, y that places the pasted (smaller) image in the center
			$x = floor(($width / 2) - ($uploaded_image_width / 2));
			$y = floor(($height / 2) - ($uploaded_image_height / 2));
			$bytes = $fill_image->paste($image, new Point($x, $y))->get('png');
		} else {
			$bytes = $image->resize(new Box($width, $height))->get('png');
		}

		// Write the image to GridFS
		// Return what should be the file object that write() returns... this will have an id to associate
		return static::write($bytes, $meta + array(
			'name' => $filename, 'mime_type' => 'image/png'
		));
	}

	/**
	 * Processes event item images uploaded from a web browser via the admin UI.
	 * Example Item URL: horses-velour-top-pants-set-fuschia
	 *
	 * @return boolean
	 */
	public static function process($data, $meta = array()) {
		if (get_called_class() == get_class()) {
			$message = "This method can only be called from a subclass i.e. `EventImage`.";
			throw new BadMethodCallException($message);
		}

		$model = str_replace('Image', '', get_called_class());
		$source = $model::meta('source');

		if (!isset($meta['name'])) {
			$message  = 'No value provided for `name` for meta; ';
			$message .= 'but a name is neeeded in order to match against, failing.';
			trigger_error($message, E_USER_WARNING);
			return false;
		}

		foreach (static::$types as $name => $type) {
			foreach ($type['regex'] as $regex) {
				/* Resize and save matched files. */
				if (!preg_match($regex, $meta['name'])) {
					continue;
				}

				preg_match('/^[a-z]+\_(.+)\_.*/i', $meta['name'], $matches);
				$url = isset($matches[1]) ? $matches[1] : false;
				
				// for file names like events_the-name.jpg (that do not use an additional underscore)
				if(!$url) {
					preg_match('/^[a-z]+\_(.+)\..*/i', $meta['name'], $matches);
					$url = isset($matches[1]) ? $matches[1] : false;
				}
				
				/* If we don't have an event URL, what's the point of saving the image?
				   We could never associate it and the file was probably named incorrectly. */
				if (!$url) {
					continue;
				}

				/* So save it and return the File document object. */
				$file = static::resizeAndSave($name, $data, $meta);

				if (!$file) {
					continue;
				}
				return $model::updateImage($name, (string) $file->_id, compact('url'));
			}
		}
		return false;
	}

	public function dimensions($entity) {
		if ($entity->dimensions) {
			return $entity->dimensions->data();
		}
		return static::detectDimensions($entity->file->getBytes());
	}

	public static function detectDimensions($data) {
		$imagine = new Imagine();
		$box = $imagine->load($data)->getSize();

		return array(
			'width' => $box->getWidth(),
			'height' => $box->getHeight()
		);
	}
}

?>