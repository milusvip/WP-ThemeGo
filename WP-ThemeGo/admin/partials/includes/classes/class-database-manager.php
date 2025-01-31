<?php
/**
 * 数据库管理类
 * 
 * 提供数据库优化和清理功能
 *
 * @package WP-ThemeGo
 * @subpackage Admin/Classes
 */

class WP_ThemeGo_Database_Manager {
    /**
     * 类实例
     *
     * @var WP_ThemeGo_Database_Manager
     */
    private static $instance = null;

    /**
     * 获取类的单例实例
     *
     * @return WP_ThemeGo_Database_Manager
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取数据库表信息
     *
     * @return array 数据库表信息数组
     */
    public function get_tables_info() {
        global $wpdb;
        
        $tables = array();
        $results = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
        
        foreach ($results as $result) {
            if (strpos($result['Name'], $wpdb->prefix) === 0) {
                $tables[] = array(
                    'name' => $result['Name'],
                    'engine' => $result['Engine'],
                    'rows' => $result['Rows'],
                    'size' => size_format($result['Data_length'] + $result['Index_length'], 2),
                    'data_size' => $result['Data_length'],
                    'index_size' => $result['Index_length'],
                    'description' => $this->get_table_description($result['Name'])
                );
            }
        }
        
        return $tables;
    }

    /**
     * 获取表描述
     *
     * @param string $table_name 表名
     * @return string 表描述
     */
    private function get_table_description($table_name) {
        global $wpdb;
        $descriptions = array(
            $wpdb->prefix . 'posts' => '文章、页面和菜单项',
            $wpdb->prefix . 'comments' => '评论',
            $wpdb->prefix . 'users' => '用户',
            $wpdb->prefix . 'usermeta' => '用户元数据',
            $wpdb->prefix . 'terms' => '分类、标签等分类法',
            $wpdb->prefix . 'term_taxonomy' => '分类法详细信息',
            $wpdb->prefix . 'term_relationships' => '文章与分类的关联关系',
            $wpdb->prefix . 'postmeta' => '文章元数据',
            $wpdb->prefix . 'options' => '网站设置和配置',
            // 可以根据需要添加更多表的描述
        );

        return isset($descriptions[$table_name]) ? $descriptions[$table_name] : '数据表';
    }

    /**
     * 优化指定的数据表
     *
     * @param string $table_name 表名
     * @return array 优化结果
     */
    public function optimize_table($table_name) {
        global $wpdb;
        
        // 安全检查：确保表名以 WordPress 表前缀开头
        if (strpos($table_name, $wpdb->prefix) !== 0) {
            return array(
                'success' => false,
                'message' => '无效的表名'
            );
        }

        // 执行优化
        $result = $wpdb->query("OPTIMIZE TABLE `$table_name`");
        
        if ($result === false) {
            return array(
                'success' => false,
                'message' => '优化表时发生错误'
            );
        }

        return array(
            'success' => true,
            'message' => '表优化成功'
        );
    }

    /**
     * 优化所有数据表
     *
     * @return array 优化结果
     */
    public function optimize_all_tables() {
        $tables = $this->get_tables_info();
        $results = array();
        
        foreach ($tables as $table) {
            $results[$table['name']] = $this->optimize_table($table['name']);
        }
        
        return $results;
    }

    /**
     * 清理指定类型的数据
     *
     * @param string $type 清理类型
     * @return array 清理结果
     */
    public function cleanup_data($type) {
        global $wpdb;
        
        switch ($type) {
            case 'revisions':
                $count = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
                break;
                
            case 'drafts':
                $count = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'draft'");
                break;
                
            case 'auto-drafts':
                $count = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
                break;
                
            case 'spam-comments':
                $count = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
                break;
                
            case 'trash-comments':
                $count = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
                break;
                
            case 'orphan-postmeta':
                $count = $wpdb->query("DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.ID IS NULL");
                break;
                
            case 'orphan-commentmeta':
                $count = $wpdb->query("DELETE cm FROM $wpdb->commentmeta cm LEFT JOIN $wpdb->comments c ON c.comment_ID = cm.comment_id WHERE c.comment_ID IS NULL");
                break;
                
            case 'orphan-relationships':
                $count = $wpdb->query("DELETE tr FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->posts p ON p.ID = tr.object_id WHERE p.ID IS NULL");
                break;
                
            case 'expired-transients':
                $count = $this->delete_expired_transients();
                break;
                
            default:
                return array(
                    'success' => false,
                    'message' => '未知的清理类型'
                );
        }
        
        if ($count === false) {
            return array(
                'success' => false,
                'message' => '清理过程中发生错误'
            );
        }
        
        return array(
            'success' => true,
            'message' => sprintf('成功清理 %d 项数据', $count)
        );
    }

    /**
     * 删除过期的临时缓存
     *
     * @return int|false 删除的记录数或失败标志
     */
    private function delete_expired_transients() {
        global $wpdb;
        
        $time = time();
        $count = 0;
        
        // 删除过期的临时值
        $expired = $wpdb->query(
            $wpdb->prepare(
                "DELETE a, b FROM $wpdb->options a, $wpdb->options b
                WHERE a.option_name LIKE %s
                AND a.option_name NOT LIKE %s
                AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
                AND b.option_value < %d",
                $wpdb->esc_like('_transient_') . '%',
                $wpdb->esc_like('_transient_timeout_') . '%',
                $time
            )
        );
        
        if ($expired !== false) {
            $count += $expired;
        }
        
        // 删除过期的网站级临时值
        $expired = $wpdb->query(
            $wpdb->prepare(
                "DELETE a, b FROM $wpdb->options a, $wpdb->options b
                WHERE a.option_name LIKE %s
                AND a.option_name NOT LIKE %s
                AND b.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(a.option_name, 17))
                AND b.option_value < %d",
                $wpdb->esc_like('_site_transient_') . '%',
                $wpdb->esc_like('_site_transient_timeout_') . '%',
                $time
            )
        );
        
        if ($expired !== false) {
            $count += $expired;
        }
        
        return $count;
    }
} 