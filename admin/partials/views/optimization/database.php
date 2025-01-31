<?php
/**
 * 数据库优化视图
 *
 * @package WP-ThemeGo
 * @subpackage Admin/Views
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 获取数据库管理器实例
$db_manager = WP_ThemeGo_Database_Manager::get_instance();
?>

<div id="database-optimization" class="content-section">
    <h2>数据库优化</h2>
    
    <!-- 选项卡导航 -->
    <div class="tab-navigation">
        <button class="tab-button active" data-tab="table-optimization">
            <span class="dashicons dashicons-database"></span>
            数据优化
        </button>
        <button class="tab-button" data-tab="data-cleanup">
            <span class="dashicons dashicons-trash"></span>
            数据库清理
        </button>
    </div>

    <!-- 选项卡内容 -->
    <div class="tab-content">
        <!-- 数据优化选项卡 -->
        <div id="table-optimization" class="tab-pane active">
            <div class="optimization-card">
                <div class="notice notice-warning">
                    <p><span class="dashicons dashicons-warning"></span> <strong>风险提示：</strong></p>
                    <ul>
                        <li>数据库优化是一个高风险操作，请在执行前备份数据库。</li>
                        <li>优化过程可能需要一些时间，请耐心等待。</li>
                        <li>建议在网站访问量较少的时间段进行优化。</li>
                    </ul>
                </div>

                <div class="card-header">
                    <h3>数据表优化</h3>
                    <div class="header-actions">
                        <button class="button button-primary optimize-button" id="optimize-all-tables">
                            <span class="dashicons dashicons-update"></span>
                            一键优化所有表
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <div class="table-wrapper">
                        <table class="wp-list-table widefat fixed striped table-view-list">
                            <thead>
                                <tr>
                                    <th class="column-name">表名称</th>
                                    <th class="column-description">描述</th>
                                    <th class="column-engine">引擎</th>
                                    <th class="column-size">大小</th>
                                    <th class="column-rows">行数</th>
                                    <th class="column-actions">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tables_info = $db_manager->get_tables_info();
                                foreach ($tables_info as $table) :
                                ?>
                                <tr>
                                    <td class="column-name">
                                        <strong><?php echo esc_html($table['name']); ?></strong>
                                    </td>
                                    <td class="column-description">
                                        <?php echo esc_html($table['description']); ?>
                                    </td>
                                    <td class="column-engine">
                                        <span class="engine-badge"><?php echo esc_html($table['engine']); ?></span>
                                    </td>
                                    <td class="column-size">
                                        <span class="size-value"><?php echo esc_html($table['size']); ?></span>
                                    </td>
                                    <td class="column-rows">
                                        <?php echo number_format_i18n($table['rows']); ?>
                                    </td>
                                    <td class="column-actions">
                                        <button class="button optimize-table-button" 
                                                data-table="<?php echo esc_attr($table['name']); ?>">
                                            <span class="dashicons dashicons-update"></span>
                                            优化
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 数据库清理选项卡 -->
        <div id="data-cleanup" class="tab-pane">
            <div class="optimization-card">
                <div class="card-header">
                    <h3>数据库清理</h3>
                </div>
                <div class="card-content">
                    <div class="cleanup-grid">
                        <?php
                        $cleanup_items = array(
                            array(
                                'type' => 'revisions',
                                'name' => '修订文章',
                                'count' => wp_count_posts('revision')->inherit,
                                'desc' => '帖子修订版本内容，WordPress自动保存下的修订内容，推荐删除'
                            ),
                            array(
                                'type' => 'drafts',
                                'name' => '草稿文章',
                                'count' => wp_count_posts()->draft,
                                'desc' => '文章草稿'
                            ),
                            array(
                                'type' => 'auto-drafts',
                                'name' => '自动草稿',
                                'count' => wp_count_posts()->auto_draft,
                                'desc' => '写文章时的时候自动保存的草稿，推荐删除'
                            ),
                            array(
                                'type' => 'spam-comments',
                                'name' => '垃圾评论',
                                'count' => wp_count_comments()->spam,
                                'desc' => '垃圾评论，跟评论管理页面垃圾评论数量一致。推荐删除'
                            ),
                            array(
                                'type' => 'trash-comments',
                                'name' => '回收站评论',
                                'count' => wp_count_comments()->trash,
                                'desc' => '回收站评论，跟评论管理页面回收站评论数量一致。推荐删除'
                            ),
                            array(
                                'type' => 'orphan-postmeta',
                                'name' => '孤立文章字段',
                                'count' => $db_manager->get_orphan_postmeta_count(),
                                'desc' => '删除文章的时候，残留的字段内容。推荐删除。'
                            ),
                            array(
                                'type' => 'orphan-commentmeta',
                                'name' => '孤立评论字段',
                                'count' => $db_manager->get_orphan_commentmeta_count(),
                                'desc' => '删除评论的时候，残留的字段内容。推荐删除。'
                            ),
                            array(
                                'type' => 'orphan-relationships',
                                'name' => '孤立关系字段',
                                'count' => $db_manager->get_orphan_relationships_count(),
                                'desc' => '删除文章后残留的文章关联标签、分类等信息。推荐删除。'
                            ),
                            array(
                                'type' => 'expired-transients',
                                'name' => '过期缓存',
                                'count' => $db_manager->get_expired_transients_count(),
                                'desc' => 'WP自带缓存功能，留下的没有被自动清理的过期缓存。推荐删除。'
                            )
                        );

                        foreach ($cleanup_items as $item) :
                        ?>
                        <div class="cleanup-item">
                            <div class="cleanup-info">
                                <div class="cleanup-header">
                                    <h4><?php echo esc_html($item['name']); ?></h4>
                                    <span class="cleanup-count"><?php echo number_format_i18n($item['count']); ?></span>
                                </div>
                                <p class="cleanup-desc"><?php echo esc_html($item['desc']); ?></p>
                            </div>
                            <div class="cleanup-action">
                                <button class="button cleanup-button" 
                                        data-type="<?php echo esc_attr($item['type']); ?>"
                                        <?php echo $item['count'] == 0 ? 'disabled' : ''; ?>>
                                    <span class="dashicons dashicons-trash"></span>
                                    删除
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 