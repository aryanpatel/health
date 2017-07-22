<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.sourcefragment.com
 * @since             1.0.0
 * @package           Bmps
 *
 * @wordpress-plugin
 * Plugin Name:       Book My Parking Space
 * Plugin URI:        http://www.sourcefragment.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Krutarth Patel
 * Author URI:        http://www.sourcefragment.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bmps
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bmps-activator.php
 */
function activate_bmps() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-bmps-activator.php';
    Bmps_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bmps-deactivator.php
 */
function deactivate_bmps() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-bmps-deactivator.php';
    Bmps_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_bmps');
register_deactivation_hook(__FILE__, 'deactivate_bmps');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-bmps.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bmps() {

    $plugin = new Bmps();
    $plugin->run();
    return $plugin;
}

/**
 * What type of request is this?
 *
 * @param  string $type admin, ajax, cron or frontend.
 * @return bool
 */
function is_request($type) {
    switch ($type) {
        case 'admin' :
            return is_admin();
        case 'ajax' :
            return defined('DOING_AJAX');
        case 'cron' :
            return defined('DOING_CRON');
        case 'frontend' :
            return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
    }
}

/**
 * Get the template location.
 * @return string
 */
function template_location() {
    return apply_filters('bmps_template_location', 'bmps/');
}

$GLOBALS['bmps'] = run_bmps();
