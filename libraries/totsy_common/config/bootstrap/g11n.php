<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This bootstrap file contains class configuration for all aspects of globalizing your application,
 * including localization of text, validation rules, setting timezones and character inflections,
 * and identifying a user's locale.
 */
use lithium\core\Environment;

/**
 * Sets the default timezone used by all date/time functions.
 */
date_default_timezone_set('America/New_York');

/**
 * Adds globalization specific settings to the environment.
 *
 * The settings for the current locale, time zone and currency are kept as environment
 * settings. This allows for _centrally_ switching, _transparently_ setting and retrieving
 * globalization related settings.
 *
 * The environment settings are:
 * - `'locale'` The effective locale. Defaults to `'en'`.
 * - `'availableLocales'` Application locales available. Defaults to `array('en')`.
 */
$locale = 'en';
$locales = array('en' => 'English');

Environment::set('production', compact('locale', 'locales'));
Environment::set('development', compact('locale', 'locales'));
Environment::set('test', array('locale' => 'en', 'locales' => array('en' => 'English')));

?>