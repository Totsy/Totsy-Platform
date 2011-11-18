<?php

namespace admin\models;

use lithium\analysis\Logger;
use MongoId;
use BadMethodCallException;

class BannerImage extends \admin\models\Image {

	public static $types = array(
		'dinkers' => array(
			'dimensions' => array(230, 403),
			'field' => 'img',
			'multiple' => true,
			'regex' => array(
				'/^banners\_.+\_dinkers\..*/i'
			),
			'uploadName' => array(
				'form' => 'banners_{:url}_{:name}.jpg',
				'dav' => '/banners/{:year}/{:month}/{:event}/{:name}/{:file}.jpg'
			)
		)

	);

	/**
	 * Processes event item images uploaded from a web browser via the admin UI.
	 * Example Item URL: horses-velour-top-pants-set-fuschia
	 *
	 * @return boolean
	 */
	public static function process($data, $meta = array()) {

		$model = str_replace('Image', '', get_called_class());
		$source = $model::meta('source');

	    Logger::debug("Processing banner-file `{$meta['name']}`...");

		if (!isset($meta['name'])) {
			$message  = 'No value provided for `name` for meta; ';
			$message .= 'but a name is neeeded in order to match against, failing.';
			trigger_error($message, E_USER_WARNING);
			return false;
		}

		foreach (static::$types as $name => $type) {

			/* So save it and return the File document object. */
			$file = static::resizeAndSave($name, $data, $meta);

			if (!$file) {
				continue;
			}
            Logger::debug("banner id to search for {$meta['banner_id']} ");
			if (isset($meta['banner_id'])) {
				$banner = $model::first($meta['banner_id']);
				if (!$banner) {
                    return false;
                }
				Logger::debug("Found banner `{$banner->_id}` by id for `{$meta['name']}`.");
			}

			$banner->attachImage($name, $file->_id);
			return $banner->save();
		}
		Logger::debug("Failed processing.");
		return false;
	}
}

?>
