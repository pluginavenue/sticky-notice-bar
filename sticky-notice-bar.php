<?php
/**
 * Plugin Name: Sticky Notice Bar
 * Plugin URI: https://pluginavenue.com/plugins/sticky-notice-bar
 * Description: Display a customizable notice bar on your site.
 * Version: 1.0.0
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Author: Plugin Avenue
 * Author URI: https://pluginavenue.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sticky-notice-bar
 */

if (!defined('ABSPATH')) exit;

// ✅ Define plugin path and URL constants
define('STICKYNOTICE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STICKYNOTICE_PLUGIN_URL', plugin_dir_url(__FILE__));

// ✅ Load admin settings + enqueue scripts/styles
require_once STICKYNOTICE_PLUGIN_DIR . 'includes/admin-settings.php';

// ✅ Render the sticky notice bar on the frontend (handled inside admin-settings.php)
