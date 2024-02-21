<?php

/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://maheshthorat.web.app
 * @since             0.1
 * @package           All_in_one_WP_Content_Security
 *
 * Plugin Name: All in one WP Content Protector
 * Plugin URI: https://wordpress.org/plugins/all-in-one-wp-content-security/
 * Description: Protect your WordPress content effortlessly with All in One WP Content Protector. Block elements selection, image drag-and-drop, right-click, copy-paste, and more
 * Version: 1.1
 * Author: Mahesh Thorat
 * Author URI: https://maheshthorat.web.app
 **/

/**
 * Prevent file to be called directly
 */
if ((!defined('ABSPATH')) || ('all-in-one-wp-content-security.php' == basename($_SERVER['SCRIPT_FILENAME']))) {
   die;
}

/**
 * Define Constants
 */
define('AOWPCS_PLUGIN_FULLNAME', 'All in one WP Content Protector');
define('AOWPCS_PLUGIN_IDENTIFIER', 'all-in-one-wp-content-security');
define('AOWPCS_PLUGIN_VERSION', '1.1');
define('AOWPCS_PLUGIN_LAST_RELEASE', '2022/04/16');
define('AOWPCS_PLUGIN_LANGUAGES', 'English');
define('AOWPCS_PLUGIN_ABS_PATH', plugin_dir_path(__FILE__));

/**
 * The core plugin class that is used to define internationalization
 * admin-specific hooks and public-facing site hooks
 */
require AOWPCS_PLUGIN_ABS_PATH . 'includes/class-all-in-one-wp-content-security-core.php';

/**
 * Begins execution of the plugin
 */
if (!function_exists('run_all_in_one_wp_content_security')) {
   function run_all_in_one_wp_content_security()
   {
      $plugin = new All_In_One_WP_Content_Security_Core();
      $plugin->run();
   }
   run_all_in_one_wp_content_security();
}
