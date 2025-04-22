<?php
if (!defined('ABSPATH')) exit;

// === Add settings page to Settings menu
function snb_add_admin_menu() {
    add_options_page(
        'Sticky Notice Bar Settings',
        'Sticky Notice Bar',
        'manage_options',
        'sticky-notice-bar',
        'snb_settings_page'
    );
}
add_action('admin_menu', 'snb_add_admin_menu');

// === Register settings
function snb_register_settings() {
    register_setting('snb_options_group', 'snb_notice_text');
    register_setting('snb_options_group', 'snb_background_color');
    register_setting('snb_options_group', 'snb_text_color');
    register_setting('snb_options_group', 'snb_start_date');
    register_setting('snb_options_group', 'snb_end_date');
    register_setting('snb_options_group', 'snb_notice_version');
    register_setting('snb_options_group', 'snb_position'); // ✅ this was missing
}
add_action('admin_init', 'snb_register_settings');

// === Enqueue admin color picker + styles
function snb_admin_enqueue_scripts($hook_suffix) {
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
add_action('admin_enqueue_scripts', 'snb_admin_enqueue_scripts');

// === Enqueue frontend JS + CSS
function snb_enqueue_frontend_scripts() {
    wp_enqueue_style(
        'snb-style',
        SNB_PLUGIN_URL . 'assets/snb-style.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'snb-frontend',
        SNB_PLUGIN_URL . 'assets/snb-frontend.js',
        [],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'snb_enqueue_frontend_scripts');

// === Render settings page
function snb_settings_page() {
    $pro_active = defined('SNB_PREMIUM') && SNB_PREMIUM;
    ?>
    <div class="wrap">
        <h1>Sticky Notice Bar Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('snb_options_group'); ?>
            <?php do_settings_sections('sticky-notice-bar'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Notice Text</th>
                    <td><input type="text" name="snb_notice_text" value="<?php echo esc_attr(get_option('snb_notice_text')); ?>" style="width: 400px;" /></td>
                </tr>
                <tr>
                    <th scope="row">Notice Version</th>
                    <td>
                        <input type="text" name="snb_notice_version" value="<?php echo esc_attr(get_option('snb_notice_version', '1')); ?>" style="width: 100px;" />
                        <p class="description">Increment to re-show to users who dismissed the last version.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Background Color</th>
                    <td><input type="text" class="snb-color-field" name="snb_background_color" value="<?php echo esc_attr(get_option('snb_background_color', '#f97316')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Text Color</th>
                    <td><input type="text" class="snb-color-field" name="snb_text_color" value="<?php echo esc_attr(get_option('snb_text_color', '#ffffff')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Start Date</th>
                    <td><input type="date" name="snb_start_date" value="<?php echo esc_attr(get_option('snb_start_date')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">End Date</th>
                    <td><input type="date" name="snb_end_date" value="<?php echo esc_attr(get_option('snb_end_date')); ?>" /></td>
                </tr>
            
                <?php do_action('snb_after_settings_fields'); ?>
            </table>

            <?php submit_button(); ?>
        </form>
        
        <?php if (!$pro_active): ?>
        <div class="snb-upgrade-box">
            <h3>
                <img src="<?php echo plugin_dir_url(__DIR__) . 'assets/pluginavenue-icon.png'; ?>" alt="" style="width: 24px; height: 24px; vertical-align: middle;" />
                Upgrade to Sticky Notice Bar Pro
            </h3>
            <ul>
                <li>🎯 Display at top or bottom</li>
                <li>📅 Set recurring schedules</li>
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
add_action('wp_body_open', 'snb_display_notice_bar');
add_action('wp_footer', 'snb_display_notice_bar');


function snb_display_notice_bar() {
    if (is_admin()) return;

    // ✅ Prevent duplicate output from multiple hooks or theme conflicts
    if (defined('SNB_BAR_RENDERED')) return;
    define('SNB_BAR_RENDERED', true);

    $text     = get_option('snb_notice_text');
    $bg       = get_option('snb_background_color', '#f97316');
    $color    = get_option('snb_text_color', '#ffffff');
    $version  = get_option('snb_notice_version', '1');
    $start    = get_option('snb_start_date');
    $end      = get_option('snb_end_date');
    $position = strtolower(trim(sanitize_text_field(get_option('snb_position', 'top'))));
    $now      = date('Y-m-d');

    // Date visibility check
    if (($start && $now < $start) || ($end && $now > $end)) return;

    // Fallback to 'top' if position is invalid
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
    echo '<div id="snb-notice-bar" class="' . esc_attr($position) . '" data-position="' . esc_attr($position) . '" data-version="' . esc_attr($version) . '" style="' . $inline_styles . '">';

    echo '<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 1200px;">';

    echo '<div style="flex: 1; text-align: center;">';
    echo '<div style="font-size: 1rem;">' . esc_html($text) . '</div>';
    do_action('snb_after_notice_text');
    echo '</div>';

    echo '<div style="padding-left: 1rem;">
        <button id="snb-dismiss" aria-label="Dismiss notice" style="
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: ' . esc_attr($color) . ';
            cursor: pointer;
            padding: 0.25rem 0.5rem;
        ">&times;</button>
    </div>';

    echo '</div></div>';
}

// === Ensure snb-frontend.js is excluded from Autoptimize aggregation
add_filter('autoptimize_filter_js_exclude', function($exclude) {
    return $exclude . ',snb-frontend.js';
});