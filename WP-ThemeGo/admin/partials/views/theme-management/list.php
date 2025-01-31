<?php
/**
 * 主题列表视图
 *
 * @package WP-ThemeGo
 * @subpackage Admin/Views
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 获取主题管理器实例
$theme_manager = WP_ThemeGo_Theme_Manager::get_instance();
$themes = $theme_manager->get_themes_info();
?>

<div id="theme-list" class="content-section">
    <div class="section-header">
        <h2>主题列表</h2>
        <div class="header-actions">
            <button class="button button-primary" id="install-theme">
                <span class="dashicons dashicons-plus"></span>
                安装新主题
            </button>
            <button class="button" id="refresh-themes">
                <span class="dashicons dashicons-update"></span>
                刷新列表
            </button>
        </div>
    </div>

    <div class="themes-grid">
        <?php foreach ($themes as $theme) : ?>
            <div class="theme-card<?php echo $theme['is_active'] ? ' active' : ''; ?>" data-theme="<?php echo esc_attr($theme['directory']); ?>">
                <div class="theme-screenshot">
                    <?php if ($theme['screenshot']) : ?>
                        <img src="<?php echo esc_url($theme['screenshot']); ?>" alt="<?php echo esc_attr($theme['name']); ?>">
                    <?php else : ?>
                        <div class="no-screenshot">
                            <span class="dashicons dashicons-format-image"></span>
                            <span>无预览图</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="theme-info">
                    <div class="theme-header">
                        <h3><?php echo esc_html($theme['name']); ?></h3>
                        <span class="theme-version">v<?php echo esc_html($theme['version']); ?></span>
                    </div>
                    
                    <div class="theme-meta">
                        <span class="meta-item">
                            <span class="dashicons dashicons-admin-users"></span>
                            <?php echo esc_html($theme['author']); ?>
                        </span>
                        <span class="meta-item">
                            <span class="dashicons dashicons-calendar"></span>
                            <?php echo esc_html($theme['last_updated']); ?>
                        </span>
                        <span class="meta-item">
                            <span class="dashicons dashicons-cloud"></span>
                            <?php echo esc_html($theme['size']); ?>
                        </span>
                    </div>
                    
                    <p class="theme-description"><?php echo esc_html($theme['description']); ?></p>
                    
                    <div class="theme-actions">
                        <?php if ($theme['is_active']) : ?>
                            <span class="active-badge">
                                <span class="dashicons dashicons-yes"></span>
                                当前主题
                            </span>
                            <button class="button button-secondary customize-theme" data-theme="<?php echo esc_attr($theme['directory']); ?>">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                自定义
                            </button>
                        <?php else : ?>
                            <button class="button button-primary activate-theme" data-theme="<?php echo esc_attr($theme['directory']); ?>">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                启用主题
                            </button>
                        <?php endif; ?>
                        
                        <div class="theme-actions-dropdown">
                            <button class="button button-secondary dropdown-toggle">
                                <span class="dashicons dashicons-ellipsis"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" class="backup-theme" data-theme="<?php echo esc_attr($theme['directory']); ?>">
                                        <span class="dashicons dashicons-backup"></span>
                                        备份主题
                                    </a>
                                </li>
                                <?php if (!$theme['is_active']) : ?>
                                    <li>
                                        <a href="#" class="delete-theme" data-theme="<?php echo esc_attr($theme['directory']); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                            删除主题
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a href="#" class="preview-theme" data-theme="<?php echo esc_attr($theme['directory']); ?>">
                                        <span class="dashicons dashicons-visibility"></span>
                                        预览主题
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 主题预览模态框 -->
<div id="theme-preview-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>主题预览</h3>
            <button class="close-modal">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        <div class="modal-body">
            <iframe src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<!-- 主题备份列表模态框 -->
<div id="theme-backups-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>主题备份</h3>
            <button class="close-modal">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        <div class="modal-body">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>备份文件</th>
                        <th>创建时间</th>
                        <th>大小</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 备份列表将通过AJAX加载 -->
                </tbody>
            </table>
        </div>
    </div>
</div> 