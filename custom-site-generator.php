<?php
/*
 * Plugin Name: Custom Site Generator
 * Description: A plugin for generating wordpress sites
 * Author: Dannyvpz
 * Version: 0.0.1
 * Text Domain: csg
 */

use Dvpz\CustomSiteGenerator;
use Dvpz\Options\CsgOptions;

if (!defined('WPINC')) {
	die;
}

define('CSG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CSG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CSG_PREFIX', 'csg');

require CSG_PLUGIN_DIR . '/vendor/autoload.php';

new CsgOptions();

add_action('init', function () {
    add_shortcode('custom-site-generator', function () {
        require CSG_PLUGIN_DIR . 'view/custom-site-generator.php';
    });
});
