<?php

namespace admin\models;

use lithium\analysis\Logger;
use lithium\data\Connections;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use BadMethodCallException;
use admin\models\Event;

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
			$data = static::_upgrade($data);
			rewind($data);
			$image = $imagine->load(stream_get_contents($data));
		} else {
			return false;
		}
		// Do not resize if the uploaded image is smaller than the dimensions used on the site
		$uploaded_image_box = $image->getSize();
		$uploaded_image_width = $uploaded_image_box->getWidth();
		$uploaded_image_height = $uploaded_image_box->getHeight();
		// Setup a $fill_image for any uploaded or resized image that is smaller than the required dimensions
		$fill_image = $imagine->create(new Box($width, $height), new Color('fff', 100));

		if($uploaded_image_width < $width && $uploaded_image_height < $height) {
			// Figure out the x, y that places the pasted (smaller) image in the center
			$x = floor(($width / 2) - ($uploaded_image_width / 2));
			$y = floor(($height / 2) - ($uploaded_image_height / 2));
			$bytes = $fill_image->paste($image, new Point($x, $y))->get('png');
		} else {
			// resize() will not respect aspect ratio
			// $bytes = $image->resize(new Box($width, $height))->get('png');
			// Use thumbnail() instead to resize the image, it respects aspect ratio
			$resized_image = $image->thumbnail(new Box($width, $height));
			$resized_image_box = $resized_image->getSize();
			$resized_image_width = $resized_image_box->getWidth();
			$resized_image_height = $resized_image_box->getHeight();
			// Now paste the resized image in $fill_image (centered if smaller)
			if($resized_image_width < $width || $resized_image_height < $height) {
				$x = floor(($width / 2) - ($resized_image_width / 2));
				$y = floor(($height / 2) - ($resized_image_height / 2));
				$bytes = $fill_image->paste($resized_image, new Point($x, $y))->get('png');
			} else {
				$bytes = $fill_image->paste($image, new Point(0, 0))->get('png');
			}
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
		Logger::debug("Processing {$source}-file `{$meta['name']}`...");

		foreach (static::$types as $name => $type) {
			foreach ($type['regex'] as $regex) {
				/* Resize and save matched files. */
				if (!preg_match($regex, $meta['name'])) {
					continue;
				}
				Logger::debug("Matched `{$meta['name']}` against `{$regex}`.");

				if ($source == 'events') {
					/* If we don't have an event URL, what's the point of saving the image?
					   We could never associate it and the file was probably named incorrectly. */
					if (!$url = static::extractUrl($meta['name'])) {
						Logger::debug("Failed to extract URL from `{$meta['name']}`.");
						continue;
					}
				}

				/* So save it and return the File document object. */
				$file = static::resizeAndSave($name, $data, $meta);

				if (!$file) {
					continue;
				}
				// There are now different query conditions depending on the model
				// (they both used to find by URL, now Items find by event id and
				// item vendor_style instead of item url)
				if ($model == 'admin\models\Item' && isset($meta['event_id'])) {
					$vendor_style = static::extractVendorStyle($meta['name']);
					$item = $model::first(array('conditions' => array('vendor_style' => $vendor_style, 'event' => $meta['event_id'])));
					Logger::debug("Found item `{$item->_id}` by vendor style for `{$meta['name']}`.");
				} else {
					$item = $model::first(array('conditions' => compact('url')));
					Logger::debug("Found item `{$item->_id}` by URL for `{$meta['name']}`.");
				}

				if (!$item) {
					return false;
				}
				$item->attachImage($name, $file->_id);
				return $item->save();
			}
		}
		Logger::debug("Failed processing.");
		return false;
	}
	/**
	 * Extracts the URL from a file name (for events).
	 * This assumes URLs can not contain underscores.
	 *
	 * @param string $name The file name
	 * @return mixed The friendly URL or false when not matched
	*/
	public static function extractUrl($name) {
		preg_match('/^[a-z]+\_([a-z0-9\-]+)\_.*/i', $name, $matches);
		$url = isset($matches[1]) ? $matches[1] : false;

		// for file names like events_the-name.jpg (that do not use an additional underscore)
		if (!$url) {
			preg_match('/^[a-z]+\_([a-z0-9\-]+)\..*/i', $name, $matches);
			$url = isset($matches[1]) ? $matches[1] : false;
		}
		return $url;
	}

	/**
	 * Extracts the vendor style value from an Item file name.
	 * Vendor style values can contain underscores, spaces, etc.
	 * which URLs can't.
	 *
	 * @see extractUrl()
	 * @param string $name The file name for the item
	 * @return mixed The vendor_style value or false when not matched
	*/
	public static function extractVendorStyle($name) {
		preg_match('/^[a-z]+\_(.+)\_.*/i', $name, $matches);
		$vendor_style = isset($matches[1]) ? $matches[1] : false;
		return $vendor_style;
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