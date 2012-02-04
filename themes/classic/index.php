<?php
/*
 * Classic theme
 * Author: Jonathan Kim
 * Date: 30/12/2011
 */

if(!defined('THEME_DIRECTORY')) define('THEME_DIRECTORY', dirname(__FILE__));
if(!defined('THEME_NAME')) define('THEME_NAME', 'classic');
if(!defined('THEME_URL')) define('THEME_URL', STANDARD_URL.'themes/'.THEME_NAME.'/');

/*
 * Set default site wide resources
 */
$this->resources = new ResourceManager(
    /* CSS files */
    array('style.css'), 
    /* JS files */
    array('plugins.js', 'script.js')
);

/*
 * Set default sidebar
 */
$this->setSidebar(array(   
    'socialLinks',
    'mostPopular',
    'fbActivity'
));

global $hooks;
$hooks->addAction('contact_us', 'contact_us');

function contact_us() {
    var_dump($_REQUEST);
}
?>
