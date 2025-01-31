<?php

/**
 * The admin-specific functionality of the plugin.
 */
class WP_ThemeGo_Admin {

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($version) {
        $this->version = $version;

        // 加载函数文件
        require_once WP_THEMEGO_PLUGIN_DIR . 'admin/includes/functions.php';

        // 添加AJAX处理钩子
        add_action('wp_ajax_wp_themego_activate_theme', array($this, 'handle_activate_theme'));
        
        // 添加前端保护钩子
        add_action('wp_head', array($this, 'add_protection_scripts'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'wp-themego-admin',
            WP_THEMEGO_PLUGIN_URL . 'admin/css/wp-themego-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'wp-themego-admin',
            WP_THEMEGO_PLUGIN_URL . 'admin/js/wp-themego-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // 添加nonce到JavaScript
        wp_localize_script(
            'wp-themego-admin',
            'wpThemeGoAdmin',
            array(
                'nonce' => wp_create_nonce('wp_themego_save_settings')
            )
        );
    }

    /**
     * Add menu item to the WordPress admin menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('WP ThemeGo', 'wp-themego'),
            __('WP ThemeGo', 'wp-themego'),
            'manage_options',
            'wp-themego',
            array($this, 'display_plugin_admin_page'),
            'dashicons-admin-appearance',
            60
        );
    }

    /**
     * Render the admin page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        include_once WP_THEMEGO_PLUGIN_DIR . 'admin/partials/wp-themego-admin-display.php';
    }

    /**
     * 处理主题激活的AJAX请求
     *
     * @since    1.0.0
     */
    public function handle_activate_theme() {
        // 检查nonce
        if (!check_ajax_referer('wp_themego_activate_theme', 'nonce', false)) {
            wp_send_json_error(array('message' => '安全验证失败'));
        }

        // 检查用户权限
        if (!current_user_can('switch_themes')) {
            wp_send_json_error(array('message' => '您没有切换主题的权限'));
        }

        // 获取主题样式表
        $theme = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : '';
        if (empty($theme)) {
            wp_send_json_error(array('message' => '未指定主题'));
        }

        // 检查主题是否存在
        $themes = wp_get_themes();
        if (!isset($themes[$theme])) {
            wp_send_json_error(array('message' => '指定的主题不存在'));
        }

        // 切换主题
        switch_theme($theme);

        // 发送成功响应
        wp_send_json_success(array('message' => '主题已成功启用'));
    }

    /**
     * 添加前端保护脚本
     */
    public function add_protection_scripts() {
        $settings = wp_themego_get_protection_settings();
        
        if (array_filter($settings)) {
            ?>
            <script>
            (function() {
                <?php if ($settings['disable_right_click']) : ?>
                // 禁用右键菜单
                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });
                <?php endif; ?>

                <?php if ($settings['disable_f12'] || $settings['disable_dev_shortcuts'] || $settings['disable_view_source'] || $settings['disable_save'] || $settings['disable_print']) : ?>
                // 禁用键盘快捷键
                document.addEventListener('keydown', function(e) {
                    if (
                        <?php if ($settings['disable_f12']) : ?>
                        e.keyCode === 123 || // F12
                        <?php endif; ?>
                        <?php if ($settings['disable_dev_shortcuts']) : ?>
                        (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                        (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                        <?php endif; ?>
                        <?php if ($settings['disable_view_source']) : ?>
                        (e.ctrlKey && e.keyCode === 85) || // Ctrl+U
                        <?php endif; ?>
                        <?php if ($settings['disable_save']) : ?>
                        (e.ctrlKey && e.keyCode === 83) || // Ctrl+S
                        <?php endif; ?>
                        <?php if ($settings['disable_print']) : ?>
                        (e.ctrlKey && e.keyCode === 80) // Ctrl+P
                        <?php endif; ?>
                    ) {
                        e.preventDefault();
                        return false;
                    }
                });
                <?php endif; ?>

                <?php if ($settings['disable_copy_paste']) : ?>
                // 禁用复制粘贴
                document.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86)) {
                        e.preventDefault();
                        return false;
                    }
                });
                document.addEventListener('copy', function(e) {
                    e.preventDefault();
                    return false;
                });
                document.addEventListener('paste', function(e) {
                    e.preventDefault();
                    return false;
                });
                <?php endif; ?>

                <?php if ($settings['disable_drag']) : ?>
                // 禁用拖拽
                document.addEventListener('dragstart', function(e) {
                    e.preventDefault();
                    return false;
                });
                document.addEventListener('drop', function(e) {
                    e.preventDefault();
                    return false;
                });
                <?php endif; ?>

                <?php if ($settings['disable_select']) : ?>
                // 禁用文本选择
                document.addEventListener('selectstart', function(e) {
                    e.preventDefault();
                    return false;
                });
                <?php endif; ?>

                <?php if ($settings['disable_console']) : ?>
                // 禁用控制台
                function disableConsole() {
                    // 清空控制台
                    console.clear();
                    // 重写控制台方法
                    var methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'trace'];
                    methods.forEach(function(method) {
                        console[method] = function() {};
                    });
                    // 禁用 debugger
                    setInterval(function() {
                        debugger;
                    }, 50);
                }
                disableConsole();
                // 监听开发者工具状态
                var devtools = function() {};
                devtools.toString = function() {
                    disableConsole();
                    return '';
                };
                setInterval(function() {
                    console.profile(devtools);
                    console.profileEnd(devtools);
                }, 1000);
                <?php endif; ?>
            })();
            </script>
            <?php
        }
    }
} 