<?php

/**
 * The public-facing functionality of the plugin.
 */
class WP_ThemeGo_Public {

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

        // 添加样式和脚本的钩子
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // 获取防护设置
        $protection_settings = get_option('wp_themego_protection_settings', array());
        $has_protection = false;

        // 检查是否有任何防护功能被启用
        foreach ($protection_settings as $setting => $value) {
            if ($value) {
                $has_protection = true;
                break;
            }
        }

        // 只有在启用了防护功能的情况下才加载样式
        if ($has_protection) {
            wp_enqueue_style(
                'wp-themego-public',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/wp-themego-public.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // 获取防护设置
        $protection_settings = get_option('wp_themego_protection_settings', array());
        $has_protection = false;

        // 检查是否有任何防护功能被启用
        foreach ($protection_settings as $setting => $value) {
            if ($value) {
                $has_protection = true;
                break;
            }
        }

        // 只有在启用了防护功能的情况下才加载脚本
        if ($has_protection) {
            wp_enqueue_script(
                'wp-themego-public',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/wp-themego-public.js',
                array('jquery'),
                $this->version,
                true
            );

            // 将设置传递给JavaScript
            wp_localize_script(
                'wp-themego-public',
                'wpThemeGoPublic',
                array(
                    'settings' => $protection_settings
                )
            );
        }
    }
} 