<?php

namespace admin\models;

use lithium\analysis\Logger;
use MongoId;
use BadMethodCallException;

class AffiliateImage extends \admin\models\Image {

	public static $types = array(
		'affiliate' => array(
			'dimensions' => array(3000, 3000),
			'field' => 'category',
			'multiple' => true,
			'uploadName' => array(
				'form' => 'affiliate_{:name}.jpg',
				'dav' => '/banners/{:year}/{:month}/{:affiliate}/{:name}/{:file}.jpg'
			)
		)

	);

	/**
	 * Processes event item images uploaded from a web browser via the admin UI.
	 * Example Item URL: horses-velour-top-pants-set-fuschia
	 *
	 * @return boolean
	 */
	public static function process($file, $meta = array()) {

		$model = str_replace('Image', '', get_called_class());
		$source = $model::meta('source');

	    Logger::debug("Processing affiliate-file `{$meta['name']}`...");

		if (!isset($meta['name'])) {
			$message  = 'No value provided for `name` for meta; ';
			$message .= 'but a name is neeeded in order to match against, failing.';
			trigger_error($message, E_USER_WARNING);
			return false;
		}

		foreach (static::$types as $name => $type) {

			/* So save it and return the File document object. */
		//	$file = static::resizeAndSave($name, $data, $meta);
		    $file = File::write($file, $meta + array('type' => 'affiliate'));

			if (!$file) {
				continue;
			}
            Logger::debug("affiliate_id to search for {$meta['affiliate_id']} ");
			if (isset($meta['affiliate_id'])) {
				$affiliate = $model::first($meta['affiliate_id']);
				if (!$affiliate) {
                    return false;
                }
				Logger::debug("Found affiliate `{$affiliate->_id}` by id for `{$meta['name']}`.");
			}

			$affiliate->attachImage($name, $file->_id);
			return $affiliate->save();
		}
		Logger::debug("Failed processing.");
		return false;
	}
}

?>
