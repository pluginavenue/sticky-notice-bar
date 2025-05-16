<?php
if (!defined('ABSPATH')) exit;

// === Add settings page to Settings menu
function stickynotice_add_admin_menu() {
    add_options_page(
        'Sticky Notice Bar Settings',
        'Sticky Notice Bar',
        'manage_options',
        'sticky-notice-bar',
        'stickynotice_settings_page'
    );
}
add_action('admin_menu', 'stickynotice_add_admin_menu');

// === Register settings
function stickynotice_register_settings() {
    register_setting('stickynotice_options_group', 'stickynotice_notice_text', 'wp_kses_post');
    register_setting('stickynotice_options_group', 'stickynotice_background_color', 'sanitize_hex_color');
    register_setting('stickynotice_options_group', 'stickynotice_text_color', 'sanitize_hex_color');
    register_setting('stickynotice_options_group', 'stickynotice_start_date', 'sanitize_text_field');
    register_setting('stickynotice_options_group', 'stickynotice_end_date', 'sanitize_text_field');
    register_setting('stickynotice_options_group', 'stickynotice_notice_version', 'sanitize_text_field');
    register_setting('stickynotice_options_group', 'stickynotice_position', 'sanitize_text_field');
}
add_action('admin_init', 'stickynotice_register_settings');

// === Enqueue admin color picker + styles
function stickynotice_admin_enqueue_scripts($hook_suffix) {
    if ('settings_page_sticky-notice-bar' !== $hook_suffix) return;

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'snb-admin-script',
        plugin_dir_url(__DIR__) . 'assets/snb-admin.js',
        ['wp-color-picker'],
        '1.0',
        true
    );

    $upgrade_styles = '
        .snb-upgrade-box {
            background: #111827;
            border-left: 5px solid #f97316;
            color: #f3f4f6;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .snb-upgrade-box h3 {
            margin-top: 0;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #f9fafb;
        }
        .snb-upgrade-box ul {
            margin: 10px 0 10px 20px;
            padding: 0;
        }
        .snb-upgrade-box ul li {
            margin-bottom: 6px;
        }
        .snb-upgrade-box .button-orange {
            background-color: #f97316;
            color: #fff;
            border: none;
            box-shadow: none;
        }
        .snb-upgrade-box .button-orange:hover {
            background-color: #fb923c;
            color: #fff;
        }
    ';
    wp_add_inline_style('wp-color-picker', $upgrade_styles);
}
add_action('admin_enqueue_scripts', 'stickynotice_admin_enqueue_scripts');

// === Enqueue frontend JS + CSS
function stickynotice_enqueue_frontend_scripts() {
    wp_enqueue_style(
        'snb-style',
        STICKYNOTICE_PLUGIN_URL . 'assets/snb-style.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'snb-frontend',
        STICKYNOTICE_PLUGIN_URL . 'assets/snb-frontend.js',
        [],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'stickynotice_enqueue_frontend_scripts');

// === Render settings page
function stickynotice_settings_page() {
    ?>
    <div class="wrap">
        <h1>Sticky Notice Bar Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('stickynotice_options_group'); ?>
            <?php do_settings_sections('sticky-notice-bar'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Notice Text</th>
                    <td><input type="text" name="stickynotice_notice_text" value="<?php echo esc_attr(get_option('stickynotice_notice_text')); ?>" style="width: 400px;" /></td>
                </tr>
                <tr>
                    <th scope="row">Notice Version</th>
                    <td>
                        <input type="text" name="stickynotice_notice_version" value="<?php echo esc_attr(get_option('stickynotice_notice_version', '1')); ?>" style="width: 100px;" />
                        <p class="description">Increment to re-show to users who dismissed the last version.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Background Color</th>
                    <td><input type="text" class="snb-color-field" name="stickynotice_background_color" value="<?php echo esc_attr(get_option('stickynotice_background_color', '#f97316')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Text Color</th>
                    <td><input type="text" class="snb-color-field" name="stickynotice_text_color" value="<?php echo esc_attr(get_option('stickynotice_text_color', '#ffffff')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Start Date</th>
                    <td><input type="date" name="stickynotice_start_date" value="<?php echo esc_attr(get_option('stickynotice_start_date')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">End Date</th>
                    <td><input type="date" name="stickynotice_end_date" value="<?php echo esc_attr(get_option('stickynotice_end_date')); ?>" /></td>
                </tr>

                <?php do_action('stickynotice_after_settings_fields'); ?>
            </table>

            <?php submit_button(); ?>
        </form>

        <?php if (!defined('STICKYNOTICE_PREMIUM') || !STICKYNOTICE_PREMIUM): ?>
        <div class="snb-upgrade-box">
            <h3>
                <img src="<?php echo esc_url(STICKYNOTICE_PLUGIN_URL . 'assets/pluginavenue-icon.png'); ?>" alt="" style="width: 24px; height: 24px; vertical-align: middle;" />
                Upgrade to Sticky Notice Bar Pro
            </h3>
            <ul>
                <li>🎯 Display at top or bottom</li>
                <li>🗕 Set recurring schedules</li>
                <li>🗂 Target specific pages</li>
                <li>✨ Add icons, animations, and more</li>
            </ul>
            <a href="https://pluginavenue.com/plugins/sticky-notice-bar-pro" target="_blank" class="button button-orange">
                Learn More
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// === Output sticky notice bar
add_action('wp_body_open', 'stickynotice_display_notice_bar');
add_action('wp_footer', 'stickynotice_display_notice_bar');

function stickynotice_display_notice_bar() {
    if (is_admin()) return;

    if (defined('stickynotice_BAR_RENDERED')) return;
    define('stickynotice_BAR_RENDERED', true);

    $text     = get_option('stickynotice_notice_text');
    $bg       = get_option('stickynotice_background_color', '#f97316');
    $color    = get_option('stickynotice_text_color', '#ffffff');
    $version  = get_option('stickynotice_notice_version', '1');
    $start    = get_option('stickynotice_start_date');
    $end      = get_option('stickynotice_end_date');
    $position = strtolower(trim(sanitize_text_field(get_option('stickynotice_position', 'top'))));
    $now      = gmdate('Y-m-d');

    if (($start && $now < $start) || ($end && $now > $end)) return;
    if (!in_array($position, ['top', 'bottom'], true)) {
        $position = 'top';
    }

    $inline_styles = "
        background: $bg;
        color: $color;
        position: fixed;
        left: 0;
        right: 0;
        z-index: 9999;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        " . ($position === 'bottom' ? 'bottom: 0;' : 'top: 0;');

    echo '<!-- SNB DEBUG - PHP POSITION: ' . esc_html($position) . ' -->';
    echo '<div id="snb-notice-bar" class="' . esc_attr($position) . '" data-position="' . esc_attr($position) . '" data-version="' . esc_attr($version) . '" style="' . esc_attr($inline_styles) . '">';

    echo '<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 1200px;">';

    echo '<div style="flex: 1; text-align: center;">';
    echo '<div style="font-size: 1rem;">' . esc_html($text) . '</div>';
    do_action('stickynotice_after_notice_text');
    echo '</div>';

    echo '<div style="padding-left: 1rem;">
        <button id="snb-dismiss" aria-label="Dismiss notice" style="background: transparent; border: none; font-size: 1.5rem; color: ' . esc_attr($color) . '; cursor: pointer; padding: 0.25rem 0.5rem;">&times;</button>
    </div>';

    echo '</div></div>';
}

// === Ensure snb-frontend.js is excluded from Autoptimize aggregation
add_filter('autoptimize_filter_js_exclude', function($exclude) {
    return $exclude . ',snb-frontend.js';
});