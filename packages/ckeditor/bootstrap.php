<?php
/**
 * CKEditor Package
 *
 * Originally derived from the CKEditor package found here: https://github.com/alwarren/fuelphp.CKEditor_package
 * This version is refactored and updated for CKEditor 4.3.1 and FuelPHP 1.7.1.
 *
 * @package     FuelPHP
 * @subpackage  CKEditor
 * @author      Shea Lewis <shea.lewis89@gmail.com>
 */

/**
 * Add namespace
 *
 * Necessary in order for the autoloader to be able to find classes
 */
Autoloader::add_namespace('CKEditor', __DIR__.'/classes/');

/**
 * Add as a core namespace
 */
Autoloader::add_core_namespace('CKEditor');

/**
 * Add classes
 *
 * This is useful for:
 * - optimization: no path searching is necessary
 * - required in order to be used as a core namespace
 */
Autoloader::add_classes(array(
	'CKEditor\\CKEditor' => __DIR__.DS.'classes'.DS.'ckeditor.php',
));

/**
 * Load CKEditor config file
 */
Config::load(__DIR__.DS.'config'.DS.'config.php', 'ckeditor', false, true);