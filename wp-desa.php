<?php

/**
 * Plugin Name: WP Desa
 * Plugin URI:  https://websweetstudio.com/wp-desa
 * Description: Plugin WordPress untuk fitur web desa dengan REST API dan Alpine.js
 * Version:     1.0.0
 * Author:      Aditya Kristyanto
 * Author URI:  https://websweetstudio.com
 * License:     GPL-2.0+
 * Text Domain: wp-desa
 */

if (! defined('ABSPATH')) {
  exit;
}

// Define Plugin Constants
define('WP_DESA_VERSION', '1.0.0');
define('WP_DESA_PATH', plugin_dir_path(__FILE__));
define('WP_DESA_URL', plugin_dir_url(__FILE__));
