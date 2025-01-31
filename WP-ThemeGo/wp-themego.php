<?php
/**
 * Plugin Name: WP-ThemeGo 主题管理器
 * Plugin URI: https://example.com/wp-themego
 * Description: 一个强大的WordPress主题管理插件，提供主题预览、快速切换、备份等功能
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-themego
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_THEMEGO_VERSION', '1.0.0');
define('WP_THEMEGO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_THEMEGO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WP_THEMEGO_PLUGIN_DIR . 'includes/class-wp-themego.php';

// Initialize the plugin
function run_wp_themego() {
    $plugin = new WP_ThemeGo();
    $plugin->run();
    
    // Register AJAX handlers
    add_action('wp_ajax_wp_themego_optimize', 'wp_themego_handle_optimization');
    add_action('wp_ajax_wp_themego_optimize_table', 'wp_themego_handle_optimize_table');
    add_action('wp_ajax_wp_themego_optimize_all_tables', 'wp_themego_handle_optimize_all_tables');
    add_action('wp_ajax_wp_themego_cleanup_data', 'wp_themego_handle_cleanup_data');
    add_action('wp_ajax_wp_themego_get_settings', 'wp_themego_handle_get_settings');
    add_action('wp_ajax_wp_themego_save_settings', 'wp_themego_handle_save_settings');
}
add_action('plugins_loaded', 'run_wp_themego');

// Localize script with nonce
function wp_themego_localize_script() {
    wp_localize_script('wp-themego-admin', 'wpThemeGoAdmin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_themego_optimization')
    ));
}
add_action('admin_enqueue_scripts', 'wp_themego_localize_script');

// Activation hook
register_activation_hook(__FILE__, 'wp_themego_activate');
function wp_themego_activate() {
    // 插件激活时的代码
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'wp_themego_deactivate');
function wp_themego_deactivate() {
    // 插件停用时的代码
}
