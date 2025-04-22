<?php
/**
 * Plugin Name: Sticky Notice Bar
 * Description: Display a customizable notice bar on your site.
 * Version: 1.0.0
 * Author: Plugin Avenue
 * License: GPL2
 */

if (!defined('ABSPATH')) exit;

// ✅ Define plugin path and URL constants
define('SNB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SNB_PLUGIN_URL', plugin_dir_url(__FILE__));

// ✅ Load admin settings + enqueue scripts/styles
require_once SNB_PLUGIN_DIR . 'includes/admin-settings.php';

// ✅ Render the sticky notice bar on the frontend
add_action('wp_body_open', 'snb_display_notice_bar');
