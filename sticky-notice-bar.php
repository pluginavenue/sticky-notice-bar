<?php
/**
 * Plugin Name: Sticky Notice Bar
 * Plugin URI: https://pluginavenue.com/plugins/sticky-notice-bar
 * Description: Display a customizable notice bar on your site.
 * Version: 1.0.0
 * Author: Plugin Avenue
 * Author URI: https://pluginavenue.com
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Tested up to: 6.8
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: stickynotice
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// ✅ Define plugin path and URL constants
define('STICKYNOTICE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STICKYNOTICE_PLUGIN_URL', plugin_dir_url(__FILE__));

// ✅ Load admin settings
require_once STICKYNOTICE_PLUGIN_DIR . 'includes/admin-settings.php';
