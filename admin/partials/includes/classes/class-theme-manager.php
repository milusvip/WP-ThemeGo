<?php
/**
 * 主题管理类
 * 
 * 提供主题管理、安装、备份等功能
 *
 * @package WP-ThemeGo
 * @subpackage Admin/Classes
 */

class WP_ThemeGo_Theme_Manager {
    /**
     * 类实例
     *
     * @var WP_ThemeGo_Theme_Manager
     */
    private static $instance = null;

    /**
     * 获取类的单例实例
     *
     * @return WP_ThemeGo_Theme_Manager
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取所有主题信息
     *
     * @return array 主题信息数组
     */
    public function get_themes_info() {
        $themes = wp_get_themes();
        $current_theme = wp_get_theme();
        $themes_info = array();

        foreach ($themes as $theme_dir => $theme) {
            $themes_info[] = array(
                'name' => $theme->get('Name'),
                'version' => $theme->get('Version'),
                'description' => $theme->get('Description'),
                'author' => $theme->get('Author'),
                'screenshot' => $theme->get_screenshot(),
                'directory' => $theme_dir,
                'is_active' => ($current_theme->get_stylesheet() == $theme_dir),
                'last_updated' => $this->get_theme_last_updated($theme_dir),
                'size' => $this->get_theme_size($theme->get_stylesheet_directory())
            );
        }

        return $themes_info;
    }

    /**
     * 获取主题最后更新时间
     *
     * @param string $theme_dir 主题目录
     * @return string 格式化的日期
     */
    private function get_theme_last_updated($theme_dir) {
        $theme_root = get_theme_root();
        $theme_path = $theme_root . '/' . $theme_dir;
        
        if (file_exists($theme_path)) {
            return date_i18n(get_option('date_format'), filemtime($theme_path));
        }
        
        return '';
    }

    /**
     * 获取主题大小
     *
     * @param string $directory 主题目录路径
     * @return string 格式化的大小
     */
    private function get_theme_size($directory) {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        return size_format($size, 2);
    }

    /**
     * 激活主题
     *
     * @param string $theme_dir 主题目录
     * @return array 激活结果
     */
    public function activate_theme($theme_dir) {
        if (!current_user_can('switch_themes')) {
            return array(
                'success' => false,
                'message' => '没有权限切换主题'
            );
        }

        $themes = wp_get_themes();
        if (!isset($themes[$theme_dir])) {
            return array(
                'success' => false,
                'message' => '主题不存在'
            );
        }

        switch_theme($theme_dir);

        return array(
            'success' => true,
            'message' => '主题已成功激活'
        );
    }

    /**
     * 备份主题
     *
     * @param string $theme_dir 主题目录
     * @return array 备份结果
     */
    public function backup_theme($theme_dir) {
        if (!current_user_can('edit_themes')) {
            return array(
                'success' => false,
                'message' => '没有权限备份主题'
            );
        }

        $theme_root = get_theme_root();
        $theme_path = $theme_root . '/' . $theme_dir;
        
        if (!file_exists($theme_path)) {
            return array(
                'success' => false,
                'message' => '主题不存在'
            );
        }

        // 创建备份目录
        $backup_dir = WP_CONTENT_DIR . '/themego-backups';
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }

        // 创建备份文件
        $date = date('Y-m-d-His');
        $backup_file = $backup_dir . '/' . $theme_dir . '-' . $date . '.zip';

        require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
        $zip = new PclZip($backup_file);
        
        $result = $zip->create($theme_path, PCLZIP_OPT_REMOVE_PATH, $theme_root);

        if ($result === 0) {
            return array(
                'success' => false,
                'message' => '备份创建失败：' . $zip->errorInfo(true)
            );
        }

        return array(
            'success' => true,
            'message' => '主题备份成功',
            'backup_file' => $backup_file
        );
    }

    /**
     * 获取主题备份列表
     *
     * @return array 备份列表
     */
    public function get_theme_backups() {
        $backup_dir = WP_CONTENT_DIR . '/themego-backups';
        $backups = array();

        if (file_exists($backup_dir)) {
            $files = glob($backup_dir . '/*.zip');
            foreach ($files as $file) {
                $filename = basename($file);
                preg_match('/^(.+)-(\d{4}-\d{2}-\d{2}-\d{6})\.zip$/', $filename, $matches);
                
                if (isset($matches[1]) && isset($matches[2])) {
                    $backups[] = array(
                        'theme' => $matches[1],
                        'date' => date('Y-m-d H:i:s', strtotime(str_replace('-', ' ', $matches[2]))),
                        'size' => size_format(filesize($file), 2),
                        'file' => $filename
                    );
                }
            }
        }

        return $backups;
    }

    /**
     * 恢复主题备份
     *
     * @param string $backup_file 备份文件名
     * @return array 恢复结果
     */
    public function restore_theme_backup($backup_file) {
        if (!current_user_can('edit_themes')) {
            return array(
                'success' => false,
                'message' => '没有权限恢复主题'
            );
        }

        $backup_path = WP_CONTENT_DIR . '/themego-backups/' . $backup_file;
        if (!file_exists($backup_path)) {
            return array(
                'success' => false,
                'message' => '备份文件不存在'
            );
        }

        require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
        $zip = new PclZip($backup_path);
        
        $theme_root = get_theme_root();
        $result = $zip->extract(PCLZIP_OPT_PATH, $theme_root);

        if ($result === 0) {
            return array(
                'success' => false,
                'message' => '恢复失败：' . $zip->errorInfo(true)
            );
        }

        return array(
            'success' => true,
            'message' => '主题恢复成功'
        );
    }

    /**
     * 删除主题备份
     *
     * @param string $backup_file 备份文件名
     * @return array 删除结果
     */
    public function delete_theme_backup($backup_file) {
        if (!current_user_can('edit_themes')) {
            return array(
                'success' => false,
                'message' => '没有权限删除备份'
            );
        }

        $backup_path = WP_CONTENT_DIR . '/themego-backups/' . $backup_file;
        if (!file_exists($backup_path)) {
            return array(
                'success' => false,
                'message' => '备份文件不存在'
            );
        }

        if (unlink($backup_path)) {
            return array(
                'success' => true,
                'message' => '备份删除成功'
            );
        }

        return array(
            'success' => false,
            'message' => '备份删除失败'
        );
    }
} 