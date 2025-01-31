<?php
/**
 * WP-ThemeGo 功能实现文件
 * 
 * 包含插件的主要功能实现
 * 
 * @package WP-ThemeGo
 * @since 1.0.0
 */

// 如果直接访问此文件，则中止
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 代码优化相关函数
 */

/**
 * 合并 CSS 文件
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_merge_css() {
    try {
        // 获取主题目录中的所有 CSS 文件
        $theme_dir = get_template_directory();
        $css_files = glob($theme_dir . '/**/*.css');
        
        // 合并文件内容
        $merged_content = '';
        foreach ($css_files as $file) {
            $merged_content .= file_get_contents($file) . "\n";
        }
        
        // 保存合并后的文件
        $merged_file = $theme_dir . '/merged-styles.css';
        file_put_contents($merged_file, $merged_content);
        
        return array(
            'success' => true,
            'message' => 'CSS 文件合并成功'
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'CSS 文件合并失败：' . $e->getMessage()
        );
    }
}

/**
 * 压缩 CSS 代码
 * 
 * @param string $css CSS 代码
 * @return string 压缩后的 CSS 代码
 */
function wp_themego_minify_css($css) {
    // 移除注释
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // 移除空格
    $css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $css);
    $css = preg_replace('/\s+/', ' ', $css);
    // 移除不必要的空格
    $css = str_replace(array(' {', '{ '), '{', $css);
    $css = str_replace(array(' }', '} '), '}', $css);
    $css = str_replace(array('; ', ' ;'), ';', $css);
    $css = str_replace(array(': ', ' :'), ':', $css);
    $css = str_replace(array(', ', ' ,'), ',', $css);
    
    return $css;
}

/**
 * 合并 JavaScript 文件
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_merge_js() {
    try {
        // 获取主题目录中的所有 JS 文件
        $theme_dir = get_template_directory();
        $js_files = glob($theme_dir . '/**/*.js');
        
        // 合并文件内容
        $merged_content = '';
        foreach ($js_files as $file) {
            $merged_content .= file_get_contents($file) . ";\n";
        }
        
        // 保存合并后的文件
        $merged_file = $theme_dir . '/merged-scripts.js';
        file_put_contents($merged_file, $merged_content);
        
        return array(
            'success' => true,
            'message' => 'JavaScript 文件合并成功'
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'JavaScript 文件合并失败：' . $e->getMessage()
        );
    }
}

/**
 * 压缩 JavaScript 代码
 * 
 * @param string $js JavaScript 代码
 * @return string 压缩后的 JavaScript 代码
 */
function wp_themego_minify_js($js) {
    // 移除单行注释
    $js = preg_replace('!\/\/.*$!m', '', $js);
    // 移除多行注释
    $js = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js);
    // 移除空格
    $js = str_replace(array("\r\n", "\r", "\n", "\t"), '', $js);
    $js = preg_replace('/\s+/', ' ', $js);
    
    return $js;
}

/**
 * 数据库优化相关函数
 */

/**
 * 获取 WordPress 数据库表信息
 * 
 * @return array 包含数据库表信息的数组
 */
function wp_themego_get_tables_info() {
    global $wpdb;
    
    $tables_info = array();
    $tables = $wpdb->get_results('SHOW TABLE STATUS');
    
    foreach ($tables as $table) {
        if (strpos($table->Name, $wpdb->prefix) === 0) {
            $size = ($table->Data_length + $table->Index_length);
            $formatted_size = size_format($size, 2);
            
            $description = '';
            switch (str_replace($wpdb->prefix, '', $table->Name)) {
                case 'posts':
                    $description = '文章、页面和自定义文章类型';
                    break;
                case 'postmeta':
                    $description = '文章元数据';
                    break;
                case 'comments':
                    $description = '评论数据';
                    break;
                case 'commentmeta':
                    $description = '评论元数据';
                    break;
                case 'terms':
                    $description = '分类、标签等分类法';
                    break;
                case 'termmeta':
                    $description = '分类元数据';
                    break;
                case 'term_relationships':
                    $description = '文章与分类的关联';
                    break;
                case 'term_taxonomy':
                    $description = '分类法数据';
                    break;
                case 'users':
                    $description = '用户数据';
                    break;
                case 'usermeta':
                    $description = '用户元数据';
                    break;
                case 'options':
                    $description = '网站选项和设置';
                    break;
                default:
                    $description = '其他数据表';
            }
            
            $tables_info[] = array(
                'name' => $table->Name,
                'description' => $description,
                'engine' => $table->Engine,
                'size' => $formatted_size,
                'rows' => $table->Rows
            );
        }
    }
    
    return $tables_info;
}

/**
 * 优化指定数据表
 * 
 * @param string $table_name 数据表名称
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_optimize_table($table_name) {
    global $wpdb;
    
    try {
        // 检查表是否存在
        $table_exists = $wpdb->get_var(
            $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
        );
        
        if (!$table_exists) {
            throw new Exception('数据表不存在');
        }
        
        // 优化表
        $result = $wpdb->query("OPTIMIZE TABLE $table_name");
        
        if ($result === false) {
            throw new Exception('优化表失败');
        }
        
        // 获取优化后的表状态
        $table_status = $wpdb->get_row("SHOW TABLE STATUS LIKE '$table_name'");
        
        return array(
            'success' => true,
            'message' => sprintf(
                '表 %s 优化成功，当前大小: %s',
                $table_name,
                size_format($table_status->Data_length + $table_status->Index_length, 2)
            )
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => sprintf('优化表 %s 失败：%s', $table_name, $e->getMessage())
        );
    }
}

/**
 * 优化所有数据表
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_optimize_all_tables() {
    global $wpdb;
    
    try {
        $tables_info = wp_themego_get_tables_info();
        $optimized = 0;
        $results = array();
        
        foreach ($tables_info as $table) {
            $result = wp_themego_optimize_table($table['name']);
            if ($result['success']) {
                $optimized++;
            }
            $results[] = $result;
        }
        
        return array(
            'success' => true,
            'message' => sprintf('成功优化 %d 个数据表', $optimized),
            'details' => $results
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '优化数据表失败：' . $e->getMessage()
        );
    }
}

/**
 * 修复数据表
 * 
 * @param string $table_name 数据表名称
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_repair_table($table_name) {
    global $wpdb;
    
    try {
        // 检查表是否存在
        $table_exists = $wpdb->get_var(
            $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
        );
        
        if (!$table_exists) {
            throw new Exception('数据表不存在');
        }
        
        // 修复表
        $result = $wpdb->query("REPAIR TABLE $table_name");
        
        if ($result === false) {
            throw new Exception('修复表失败');
        }
        
        return array(
            'success' => true,
            'message' => sprintf('表 %s 修复成功', $table_name)
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => sprintf('修复表 %s 失败：%s', $table_name, $e->getMessage())
        );
    }
}

/**
 * 清理文章修订版本
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_clean_revisions() {
    global $wpdb;
    
    try {
        // 删除文章修订
        $deleted = $wpdb->query(
            "DELETE FROM $wpdb->posts WHERE post_type = 'revision'"
        );
        
        return array(
            'success' => true,
            'message' => sprintf('成功清理 %d 个修订版本', $deleted)
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '清理修订版本失败：' . $e->getMessage()
        );
    }
}

/**
 * 清理自动草稿
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_clean_drafts() {
    global $wpdb;
    
    try {
        // 删除自动草稿
        $deleted = $wpdb->query(
            "DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'"
        );
        
        return array(
            'success' => true,
            'message' => sprintf('成功清理 %d 个自动草稿', $deleted)
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '清理自动草稿失败：' . $e->getMessage()
        );
    }
}

/**
 * 清理垃圾评论
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_clean_spam_comments() {
    global $wpdb;
    
    try {
        // 删除垃圾评论
        $deleted = $wpdb->query(
            "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'"
        );
        
        return array(
            'success' => true,
            'message' => sprintf('成功清理 %d 条垃圾评论', $deleted)
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '清理垃圾评论失败：' . $e->getMessage()
        );
    }
}

/**
 * 媒体优化相关函数
 */

/**
 * 压缩图片
 * 
 * @param string $file_path 图片文件路径
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_compress_image($file_path) {
    try {
        if (!file_exists($file_path)) {
            throw new Exception('文件不存在');
        }
        
        // 获取图片信息
        $info = getimagesize($file_path);
        if ($info === false) {
            throw new Exception('无效的图片文件');
        }
        
        // 根据图片类型创建图片资源
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file_path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file_path);
                break;
            default:
                throw new Exception('不支持的图片格式');
        }
        
        // 压缩并保存图片
        $quality = 85; // 压缩质量
        imagejpeg($image, $file_path, $quality);
        imagedestroy($image);
        
        return array(
            'success' => true,
            'message' => '图片压缩成功'
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '图片压缩失败：' . $e->getMessage()
        );
    }
}

/**
 * 生成 WebP 格式图片
 * 
 * @param string $file_path 原图片文件路径
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_generate_webp($file_path) {
    try {
        if (!file_exists($file_path)) {
            throw new Exception('文件不存在');
        }
        
        // 检查是否支持 WebP
        if (!function_exists('imagewebp')) {
            throw new Exception('服务器不支持 WebP 格式');
        }
        
        // 获取图片信息
        $info = getimagesize($file_path);
        if ($info === false) {
            throw new Exception('无效的图片文件');
        }
        
        // 根据图片类型创建图片资源
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file_path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file_path);
                break;
            default:
                throw new Exception('不支持的图片格式');
        }
        
        // 生成 WebP 文件
        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);
        imagewebp($image, $webp_path, 80);
        imagedestroy($image);
        
        return array(
            'success' => true,
            'message' => 'WebP 格式生成成功'
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'WebP 格式生成失败：' . $e->getMessage()
        );
    }
}

/**
 * 清理未使用的媒体文件
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_clean_unused_media() {
    try {
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        
        // 获取数据库中的附件列表
        global $wpdb;
        $attachments = $wpdb->get_results(
            "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file'"
        );
        
        $db_files = array();
        foreach ($attachments as $attachment) {
            $db_files[] = $attachment->meta_value;
        }
        
        // 扫描上传目录
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        $deleted = 0;
        foreach ($files as $file) {
            if ($file->isFile()) {
                $file_path = str_replace($base_dir . '/', '', $file->getPathname());
                if (!in_array($file_path, $db_files)) {
                    unlink($file->getPathname());
                    $deleted++;
                }
            }
        }
        
        return array(
            'success' => true,
            'message' => sprintf('成功清理 %d 个未使用的媒体文件', $deleted)
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '清理未使用媒体文件失败：' . $e->getMessage()
        );
    }
}

/**
 * 缓存优化相关函数
 */

/**
 * 启用页面缓存
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_enable_page_cache() {
    try {
        // 检查是否已经存在高级缓存插件
        if (wp_using_ext_object_cache()) {
            throw new Exception('检测到已启用其他缓存插件');
        }
        
        // 创建缓存目录
        $cache_dir = WP_CONTENT_DIR . '/cache/wp-themego';
        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        // 添加缓存配置到 wp-config.php
        $config_path = ABSPATH . 'wp-config.php';
        $config_content = file_get_contents($config_path);
        
        if (strpos($config_content, 'WP_CACHE') === false) {
            $cache_config = "define('WP_CACHE', true);\n";
            $cache_config .= "define('WP_CACHE_KEY_SALT', 'wp_themego_');\n";
            
            // 插入配置到 wp-config.php
            $config_content = preg_replace(
                '/(<\?php)/i',
                "<?php\n" . $cache_config,
                $config_content
            );
            
            file_put_contents($config_path, $config_content);
        }
        
        return array(
            'success' => true,
            'message' => '页面缓存已启用'
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '启用页面缓存失败：' . $e->getMessage()
        );
    }
}

/**
 * 启用浏览器缓存
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_enable_browser_cache() {
    try {
        $htaccess_path = ABSPATH . '.htaccess';
        $htaccess_content = '';
        
        if (file_exists($htaccess_path)) {
            $htaccess_content = file_get_contents($htaccess_path);
        }
        
        // 添加浏览器缓存规则
        $cache_rules = "
# BEGIN WP-ThemeGo Browser Cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg \"access plus 1 year\"
    ExpiresByType image/jpeg \"access plus 1 year\"
    ExpiresByType image/gif \"access plus 1 year\"
    ExpiresByType image/png \"access plus 1 year\"
    ExpiresByType image/webp \"access plus 1 year\"
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType text/javascript \"access plus 1 month\"
    ExpiresByType application/x-javascript \"access plus 1 month\"
    ExpiresByType text/html \"access plus 1 minute\"
</IfModule>
# END WP-ThemeGo Browser Cache
";
        
        if (strpos($htaccess_content, 'WP-ThemeGo Browser Cache') === false) {
            file_put_contents($htaccess_path, $cache_rules . $htaccess_content);
        }
        
        return array(
            'success' => true,
            'message' => '浏览器缓存已启用'
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '启用浏览器缓存失败：' . $e->getMessage()
        );
    }
}

/**
 * 预生成缓存
 * 
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_pregenerate_cache() {
    try {
        // 获取热门页面列表
        $popular_posts = get_posts(array(
            'posts_per_page' => 10,
            'orderby' => 'comment_count',
            'order' => 'DESC'
        ));
        
        $cached = 0;
        foreach ($popular_posts as $post) {
            $url = get_permalink($post->ID);
            // 预热缓存
            wp_remote_get($url);
            $cached++;
        }
        
        return array(
            'success' => true,
            'message' => sprintf('成功预生成 %d 个页面的缓存', $cached)
        );
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => '预生成缓存失败：' . $e->getMessage()
        );
    }
}

/**
 * 网站防护相关函数
 */

/**
 * 保存网站防护设置
 * 
 * @param array $settings 设置数组
 * @return array 包含操作状态和消息的数组
 */
function wp_themego_save_protection_settings($settings) {
    if (!is_array($settings)) {
        return array(
            'success' => false,
            'message' => '无效的设置数据'
        );
    }

    // 获取现有设置
    $existing_settings = get_option('wp_themego_protection_settings', array());
    
    // 定义所有可用的保护选项
    $protection_options = array(
        'disable_right_click',
        'disable_f12',
        'disable_dev_shortcuts',
        'disable_view_source',
        'disable_save',
        'disable_print',
        'disable_copy_paste',
        'disable_drag',
        'disable_select',
        'disable_console'
    );

    // 处理每个选项
    $clean_settings = array();
    foreach ($protection_options as $option) {
        // 如果设置中存在该选项，使用新值；否则保持现有值
        if (isset($settings[$option])) {
            // 确保值为布尔类型
            if (is_string($settings[$option])) {
                $clean_settings[$option] = filter_var($settings[$option], FILTER_VALIDATE_BOOLEAN);
            } else {
                $clean_settings[$option] = (bool)$settings[$option];
            }
        } else {
            // 如果选项不存在于新设置中，则设为 false
            $clean_settings[$option] = false;
        }
    }

    // 保存设置
    $saved = update_option('wp_themego_protection_settings', $clean_settings);

    if ($saved) {
        // 检查是否有任何功能被启用
        $has_enabled = false;
        foreach ($clean_settings as $value) {
            if ($value === true) {
                $has_enabled = true;
                break;
            }
        }

        return array(
            'success' => true,
            'message' => $has_enabled ? '网站防护设置已启用并保存' : '网站防护功能已禁用',
            'settings' => $clean_settings
        );
    } else {
        return array(
            'success' => false,
            'message' => '保存设置失败，请重试',
            'settings' => $existing_settings
        );
    }
}

/**
 * 获取网站防护设置
 * 
 * @return array 防护设置数组
 */
function wp_themego_get_protection_settings() {
    $default_settings = array(
        'disable_right_click' => false,
        'disable_f12' => false,
        'disable_dev_shortcuts' => false,
        'disable_view_source' => false,
        'disable_save' => false,
        'disable_print' => false,
        'disable_copy_paste' => false,
        'disable_drag' => false,
        'disable_select' => false,
        'disable_console' => false
    );
    
    $saved_settings = get_option('wp_themego_protection_settings', array());
    
    // 确保所有值都是布尔类型
    foreach ($saved_settings as $key => $value) {
        if (is_bool($value)) {
            $saved_settings[$key] = $value;
        } else if (is_string($value)) {
            $saved_settings[$key] = in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
        } else if (is_numeric($value)) {
            $saved_settings[$key] = (bool)$value;
        } else {
            $saved_settings[$key] = false;
        }
    }
    
    // 合并并确保所有选项都存在
    return array_merge($default_settings, $saved_settings);
}

/**
 * AJAX 处理函数
 */

/**
 * 处理优化请求的 AJAX 函数
 */
function wp_themego_handle_optimization() {
    check_ajax_referer('wp_themego_optimization', 'nonce');
    
    $type = $_POST['type'];
    $options = isset($_POST['options']) ? $_POST['options'] : array();
    $response = array();
    
    switch ($type) {
        case 'code':
            if (in_array('merge_css', $options)) {
                $response['css_merge'] = wp_themego_merge_css();
            }
            if (in_array('merge_js', $options)) {
                $response['js_merge'] = wp_themego_merge_js();
            }
            break;
            
        case 'database':
            if (in_array('clean_revisions', $options)) {
                $response['revisions'] = wp_themego_clean_revisions();
            }
            if (in_array('clean_drafts', $options)) {
                $response['drafts'] = wp_themego_clean_drafts();
            }
            if (in_array('clean_spam', $options)) {
                $response['spam'] = wp_themego_clean_spam_comments();
            }
            if (in_array('optimize_tables', $options)) {
                $response['tables'] = wp_themego_optimize_tables();
            }
            break;
            
        case 'media':
            // 处理媒体优化
            break;
            
        case 'cache':
            if (in_array('enable_page_cache', $options)) {
                $response['page_cache'] = wp_themego_enable_page_cache();
            }
            if (in_array('enable_browser_cache', $options)) {
                $response['browser_cache'] = wp_themego_enable_browser_cache();
            }
            if (in_array('pregenerate_cache', $options)) {
                $response['pregenerate'] = wp_themego_pregenerate_cache();
            }
            break;
    }
    
    wp_send_json($response);
}
add_action('wp_ajax_wp_themego_optimize', 'wp_themego_handle_optimization');

// 处理单个表优化请求
function wp_themego_handle_optimize_table() {
    check_ajax_referer('wp_themego_optimization', 'nonce');
    
    if (!isset($_POST['table'])) {
        wp_send_json_error(array('message' => '缺少表名参数'));
    }
    
    $table_name = sanitize_text_field($_POST['table']);
    $result = wp_themego_optimize_table($table_name);
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_wp_themego_optimize_table', 'wp_themego_handle_optimize_table');

// 处理所有表优化请求
function wp_themego_handle_optimize_all_tables() {
    check_ajax_referer('wp_themego_optimization', 'nonce');
    
    $result = wp_themego_optimize_all_tables();
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_wp_themego_optimize_all_tables', 'wp_themego_handle_optimize_all_tables');

// 处理数据清理请求
function wp_themego_handle_cleanup_data() {
    check_ajax_referer('wp_themego_optimization', 'nonce');
    
    if (!isset($_POST['type'])) {
        wp_send_json_error(array('message' => '缺少清理类型参数'));
    }
    
    $type = sanitize_text_field($_POST['type']);
    $result = wp_themego_cleanup_data($type);
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_wp_themego_cleanup_data', 'wp_themego_handle_cleanup_data');

/**
 * 处理获取设置的 AJAX 函数
 */
function wp_themego_handle_get_settings() {
    // 检查nonce
    if (!check_ajax_referer('wp_themego_save_settings', 'nonce', false)) {
        wp_send_json_error(array('message' => '安全验证失败'));
        return;
    }
    
    // 检查权限
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => '权限不足'));
        return;
    }
    
    // 获取设置类型
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    
    if (empty($type)) {
        wp_send_json_error(array('message' => '未指定设置类型'));
        return;
    }

    // 根据类型获取不同的设置
    switch ($type) {
        case 'protection':
            $settings = wp_themego_get_protection_settings();
            wp_send_json_success(array(
                'message' => '获取设置成功',
                'settings' => $settings
            ));
            break;
        default:
            wp_send_json_error(array('message' => '未知的设置类型'));
            return;
    }
}
add_action('wp_ajax_wp_themego_get_settings', 'wp_themego_handle_get_settings');

/**
 * 处理保存设置的 AJAX 函数
 */
function wp_themego_handle_save_settings() {
    // 检查nonce
    if (!check_ajax_referer('wp_themego_save_settings', 'nonce', false)) {
        wp_send_json_error(array('message' => '安全验证失败'));
        return;
    }
    
    // 检查权限
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => '权限不足'));
        return;
    }
    
    // 获取设置数据
    $settings = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : array();
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    
    if (empty($type)) {
        wp_send_json_error(array('message' => '未指定设置类型'));
        return;
    }

    // 根据类型处理不同的设置
    switch ($type) {
        case 'protection':
            $result = wp_themego_save_protection_settings($settings);
            break;
        default:
            wp_send_json_error(array('message' => '未知的设置类型'));
            return;
    }
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_wp_themego_save_settings', 'wp_themego_handle_save_settings');
