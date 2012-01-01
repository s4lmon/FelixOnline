<?php
/*
 * Classic theme
 * Author: Jonathan Kim
 * Date: 30/12/2011
 */

if(!defined('THEME_DIRECTORY')) define('THEME_DIRECTORY', dirname(__FILE__));
if(!defined('THEME_NAME')) define('THEME_NAME', 'classic');

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
