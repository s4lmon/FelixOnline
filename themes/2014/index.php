<?php
/*
 * Classic theme
 * Author: Jonathan Kim
 * Date: 30/12/2011
 */

if(!defined('THEME_DIRECTORY')) define('THEME_DIRECTORY', dirname(__FILE__));
if(!defined('THEME_NAME')) define('THEME_NAME', '2014');
if(!defined('THEME_URL')) define('THEME_URL', STANDARD_URL.'themes/'.THEME_NAME.'/');

global $hooks;

/*
 * Load in theme specific functions
 */
require_once(THEME_DIRECTORY.'/functions.php');

/*
 * Set default site wide resources
 */
$this->resources = new ResourceManager(
	/* CSS files */
	array('style.css'), 
	/* JS files */
	array('plugins.js', 'script.js')
);

?>
