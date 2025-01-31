<?php
/**
 * 提供插件的管理界面视图
 *
 * 用于标记插件管理界面的外观
 */
?>

<div class="wp-themego-admin-wrapper">
    <!-- 顶部栏 -->
    <div class="wp-themego-header">
        <div class="header-left">
            <button class="mobile-menu-toggle">
                <span class="dashicons dashicons-menu-alt"></span>
            </button>
            <h1>WP ThemeGo 主题管理器</h1>
        </div>
        <div class="header-right">
            <div class="search-box">
                <input type="text" placeholder="搜索...">
            </div>
            <button class="save-button">
                <span class="dashicons dashicons-saved"></span>
                保存更改
            </button>
        </div>
    </div>

    <!-- 主要内容区域 -->
    <div class="wp-themego-main">
        <!-- 左侧菜单 -->
        <div class="wp-themego-sidebar">
            <!-- 主菜单 -->
            <div class="menu-list">
                <ul>
                    <li class="menu-item has-submenu">
                        <a href="#dashboard">
                            <span class="dashicons dashicons-dashboard"></span>
                            控制面板
                            <span class="submenu-arrow dashicons dashicons-arrow-down-alt2"></span>
                        </a>
                        <ul class="submenu">
                            <li><a href="#dashboard-overview">系统总览</a></li>
                            <li><a href="#dashboard-diagnostic">WP诊断</a></li>
                        </ul>
                    </li>
                    <li class="menu-item has-submenu">
                        <a href="#optimization">
                            <span class="dashicons dashicons-performance"></span>
                            一键优化
                            <span class="submenu-arrow dashicons dashicons-arrow-down-alt2"></span>
                        </a>
                        <ul class="submenu">
                            <li><a href="#code-optimization">代码优化</a></li>
                            <li><a href="#database-optimization">数据库优化</a></li>
                            <li><a href="#media-optimization">媒体优化</a></li>
                            <li><a href="#cache-optimization">缓存优化</a></li>
                        </ul>
                    </li>
                    <li class="menu-item has-submenu">
                        <a href="#code-insertion">
                            <span class="dashicons dashicons-editor-code"></span>
                            插入代码
                            <span class="submenu-arrow dashicons dashicons-arrow-down-alt2"></span>
                        </a>
                        <ul class="submenu">
                            <li><a href="#custom-code">自定义代码</a></li>
                            <li><a href="#footer-code">页脚代码</a></li>
                            <li><a href="#custom-css">自定义CSS</a></li>
                        </ul>
                    </li>
                    <li class="menu-item has-submenu">
                        <a href="#security">
                            <span class="dashicons dashicons-shield"></span>
                            网站安全
                            <span class="submenu-arrow dashicons dashicons-arrow-down-alt2"></span>
                        </a>
                        <ul class="submenu">
                            <li><a href="#site-protection">网站防护</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 右侧内容区域 -->
        <div class="wp-themego-content">
            <div id="dashboard-overview" class="content-section active">
                <!-- 系统总览内容 -->
                <h2>系统总览</h2>
                
                <!-- 基础统计卡片 -->
                <div class="dashboard-overview-grid">
                    <div class="overview-card">
                        <div class="card-icon"><span class="dashicons dashicons-admin-appearance"></span></div>
                        <div class="card-content">
                            <h3>已安装主题</h3>
                            <p class="number"><?php echo count(wp_get_themes()); ?></p>
                            <p class="trend up">较上月 +2</p>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="card-icon"><span class="dashicons dashicons-admin-plugins"></span></div>
                        <div class="card-content">
                            <h3>活跃插件</h3>
                            <p class="number"><?php echo count(get_option('active_plugins')); ?></p>
                            <p class="trend">较上月 0</p>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="card-icon"><span class="dashicons dashicons-wordpress"></span></div>
                        <div class="card-content">
                            <h3>WordPress版本</h3>
                            <p class="version"><?php echo get_bloginfo('version'); ?></p>
                            <p class="update-status"><?php echo version_compare(get_bloginfo('version'), '6.0', '>=') ? '已是最新' : '建议更新'; ?></p>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="card-icon"><span class="dashicons dashicons-admin-users"></span></div>
                        <div class="card-content">
                            <h3>用户总数</h3>
                            <p class="number"><?php echo count_users()['total_users']; ?></p>
                            <p class="trend up">较上月 +5</p>
                        </div>
                    </div>
                </div>

                <!-- 主题使用统计 -->
                <div class="overview-section">
                    <h3>主题使用统计</h3>
                    <div class="theme-stats-grid">
                        <div class="stats-card">
                            <h4>主题切换频率</h4>
                            <div class="stats-chart">
                                <!-- 这里可以添加图表 -->
                                <div class="placeholder-chart"></div>
                            </div>
                        </div>
                        <div class="stats-card">
                            <h4>主题使用时长</h4>
                            <div class="theme-usage-list">
                                <?php
                                $current_theme = wp_get_theme();
                                ?>
                                <div class="usage-item">
                                    <span class="theme-name"><?php echo $current_theme->get('Name'); ?></span>
                                    <span class="usage-time">使用时长：30天</span>
                                </div>
                                <!-- 可以添加更多主题使用记录 -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 系统健康状态 -->
                <div class="overview-section">
                    <h3>系统健康状态</h3>
                    <div class="health-status-grid">
                        <div class="health-card good">
                            <span class="status-icon dashicons dashicons-yes-alt"></span>
                            <div class="status-content">
                                <h4>数据库状态</h4>
                                <p>运行正常，无优化需求</p>
                            </div>
                        </div>
                        <div class="health-card warning">
                            <span class="status-icon dashicons dashicons-warning"></span>
                            <div class="status-content">
                                <h4>缓存状态</h4>
                                <p>建议清理过期缓存</p>
                            </div>
                        </div>
                        <div class="health-card good">
                            <span class="status-icon dashicons dashicons-yes-alt"></span>
                            <div class="status-content">
                                <h4>文件权限</h4>
                                <p>权限设置正确</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="dashboard-diagnostic" class="content-section">
                <!-- WP诊断内容 -->
                <h2>WordPress诊断</h2>
                
                <!-- 系统信息 -->
                <div class="diagnostic-section">
                    <h3>系统信息</h3>
                    <table class="diagnostic-table">
                        <tr>
                            <td>PHP 版本</td>
                            <td><?php echo PHP_VERSION; ?></td>
                            <td class="status">
                                <?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 
                                    '<span class="status-good">良好</span>' : 
                                    '<span class="status-warning">建议升级</span>'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>MySQL 版本</td>
                            <td><?php echo mysqli_get_client_info(); ?></td>
                            <td class="status">
                                <span class="status-good">良好</span>
                            </td>
                        </tr>
                        <tr>
                            <td>WordPress 内存限制</td>
                            <td><?php echo WP_MEMORY_LIMIT; ?></td>
                            <td class="status">
                                <?php echo (wp_convert_hr_to_bytes(WP_MEMORY_LIMIT) >= 67108864) ? 
                                    '<span class="status-good">良好</span>' : 
                                    '<span class="status-warning">建议增加</span>'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>最大上传限制</td>
                            <td><?php echo size_format(wp_max_upload_size()); ?></td>
                            <td class="status">
                                <?php echo (wp_max_upload_size() >= 8388608) ? 
                                    '<span class="status-good">良好</span>' : 
                                    '<span class="status-warning">建议增加</span>'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>服务器软件</td>
                            <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                            <td class="status">
                                <span class="status-good">良好</span>
                            </td>
                        </tr>
                        <tr>
                            <td>操作系统</td>
                            <td><?php echo PHP_OS; ?></td>
                            <td class="status">
                                <span class="status-good">良好</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- PHP扩展检查 -->
                <div class="diagnostic-section">
                    <h3>PHP扩展检查</h3>
                    <div class="extension-grid">
                        <?php
                        $required_extensions = array(
                            'mysql' => '数据库支持',
                            'gd' => '图像处理',
                            'curl' => '远程请求',
                            'json' => 'JSON支持',
                            'xml' => 'XML支持',
                            'mbstring' => '多字节字符',
                            'zip' => 'ZIP支持',
                            'openssl' => 'SSL支持',
                            'fileinfo' => '文件类型检测',
                            'exif' => '图片信息读取'
                        );

                        foreach ($required_extensions as $ext => $desc) :
                            $installed = extension_loaded($ext);
                            if ($ext === 'mysql') {
                                $installed = extension_loaded('mysqli') || class_exists('mysqli') || function_exists('mysqli_connect');
                            }
                        ?>
                            <div class="extension-card <?php echo $installed ? 'installed' : 'missing'; ?>">
                                <span class="ext-icon dashicons <?php echo $installed ? 'dashicons-yes' : 'dashicons-no'; ?>"></span>
                                <div class="ext-info">
                                    <h4><?php echo $ext; ?></h4>
                                    <p><?php echo $desc; ?></p>
                                </div>
                                <span class="ext-status"><?php echo $installed ? '已安装' : '未安装'; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 性能检测 -->
                <div class="diagnostic-section">
                    <h3>性能检测</h3>
                    <div class="performance-grid">
                        <div class="performance-card">
                            <div class="performance-header">
                                <h4>数据库查询</h4>
                                <span class="performance-score good">92</span>
                            </div>
                            <div class="performance-meter">
                                <div class="meter-bar" style="width: 92%"></div>
                            </div>
                            <p class="performance-desc">数据库性能良好，查询响应时间在正常范围内</p>
                        </div>
                        <div class="performance-card">
                            <div class="performance-header">
                                <h4>页面加载</h4>
                                <span class="performance-score warning">78</span>
                            </div>
                            <div class="performance-meter">
                                <div class="meter-bar" style="width: 78%"></div>
                            </div>
                            <p class="performance-desc">页面加载速度尚可，建议优化图片和脚本</p>
                        </div>
                        <div class="performance-card">
                            <div class="performance-header">
                                <h4>缓存效率</h4>
                                <span class="performance-score good">95</span>
                            </div>
                            <div class="performance-meter">
                                <div class="meter-bar" style="width: 95%"></div>
                            </div>
                            <p class="performance-desc">缓存系统工作正常，命中率高</p>
                        </div>
                    </div>
                </div>

                <!-- 安全检查 -->
                <div class="diagnostic-section">
                    <h3>安全检查</h3>
                    <div class="security-checklist">
                        <div class="security-item good">
                            <span class="security-icon dashicons dashicons-shield"></span>
                            <div class="security-content">
                                <h4>SSL证书</h4>
                                <p>已启用HTTPS，证书有效</p>
                            </div>
                        </div>
                        <div class="security-item warning">
                            <span class="security-icon dashicons dashicons-privacy"></span>
                            <div class="security-content">
                                <h4>文件权限</h4>
                                <p>部分目录权限需要调整</p>
                            </div>
                        </div>
                        <div class="security-item good">
                            <span class="security-icon dashicons dashicons-admin-network"></span>
                            <div class="security-content">
                                <h4>管理员登录</h4>
                                <p>登录保护已启用</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="theme-list" class="content-section">
                <!-- 主题列表内容 -->
                <h2>主题列表</h2>
                <div class="themes-grid">
                    <?php
                    $themes = wp_get_themes();
                    foreach ($themes as $theme) :
                        $screenshot = $theme->get_screenshot();
                        $is_active = (get_template() === $theme->get_template());
                    ?>
                        <div class="theme-card<?php echo $is_active ? ' active' : ''; ?>">
                            <div class="theme-screenshot">
                                <?php if ($screenshot) : ?>
                                    <img src="<?php echo esc_url($screenshot); ?>" alt="<?php echo esc_attr($theme->get('Name')); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="theme-info">
                                <h3><?php echo esc_html($theme->get('Name')); ?></h3>
                                <p><?php echo esc_html($theme->get('Description')); ?></p>
                                <div class="theme-actions">
                                    <?php if (!$is_active) : ?>
                                        <button class="button activate-theme" data-theme="<?php echo esc_attr($theme->get_stylesheet()); ?>">
                                            <?php _e('启用主题', 'wp-themego'); ?>
                                        </button>
                                    <?php else : ?>
                                        <span class="active-badge">
                                            <?php _e('当前主题', 'wp-themego'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 添加一键优化相关内容区域 -->
            <div id="code-optimization" class="content-section">
                <h2>代码优化</h2>
                <div class="optimization-section">
                    <div class="optimization-card">
                        <div class="card-header">
                            <h3>代码优化</h3>
                            <button class="optimize-button">开始优化</button>
                        </div>
                        <div class="card-content">
                            <ul class="optimization-options">
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">合并 CSS 文件</span>
                                            <span class="option-desc">将多个 CSS 文件合并为一个，减少 HTTP 请求</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="merge_css">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">压缩 CSS 代码</span>
                                            <span class="option-desc">移除空格和注释，减小文件体积</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="minify_css">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">合并 JavaScript 文件</span>
                                            <span class="option-desc">将多个 JS 文件合并为一个，减少加载时间</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="merge_js">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">压缩 JavaScript 代码</span>
                                            <span class="option-desc">压缩 JS 代码，提高加载速度</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="minify_js">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 数据库优化部分 -->
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
                                            $tables_info = wp_themego_get_tables_info();
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
                                            'name' => '修订文章',
                                            'count' => 0,
                                            'desc' => '帖子修订版本内容，WordPress自动保存下的修订内容，推荐删除'
                                        ),
                                        array(
                                            'name' => '草稿文章',
                                            'count' => 0,
                                            'desc' => '文章草稿'
                                        ),
                                        array(
                                            'name' => '自动草稿',
                                            'count' => 5,
                                            'desc' => '写文章时的时候自动保存的草稿，推荐删除'
                                        ),
                                        array(
                                            'name' => '垃圾评论',
                                            'count' => 0,
                                            'desc' => '垃圾评论，跟评论管理页面垃圾评论数量一致。推荐删除'
                                        ),
                                        array(
                                            'name' => '回收站评论',
                                            'count' => 0,
                                            'desc' => '回收站评论，跟评论管理页面回收站评论数量一致。推荐删除'
                                        ),
                                        array(
                                            'name' => '孤立文章字段',
                                            'count' => 3,
                                            'desc' => '删除文章的时候，残留的字段内容。推荐删除。'
                                        ),
                                        array(
                                            'name' => '孤立评论字段',
                                            'count' => 0,
                                            'desc' => '删除评论的时候，残留的字段内容。推荐删除。'
                                        ),
                                        array(
                                            'name' => '孤立关系字段',
                                            'count' => 0,
                                            'desc' => '删除文章后残留的文章关联标签、分类等信息。推荐删除。'
                                        ),
                                        array(
                                            'name' => '过期缓存',
                                            'count' => 0,
                                            'desc' => 'WP自带缓存功能，留下的没有被自动清理的过期缓存。推荐删除。'
                                        )
                                    );

                                    foreach ($cleanup_items as $item) :
                                    ?>
                                    <div class="cleanup-item">
                                        <div class="cleanup-info">
                                            <div class="cleanup-header">
                                                <h4><?php echo esc_html($item['name']); ?></h4>
                                                <span class="cleanup-count"><?php echo $item['count']; ?></span>
                                            </div>
                                            <p class="cleanup-desc"><?php echo esc_html($item['desc']); ?></p>
                                        </div>
                                        <div class="cleanup-action">
                                            <button class="button cleanup-button" data-type="<?php echo sanitize_title($item['name']); ?>">
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

                <style>
                /* 选项卡样式优化 */
                .tab-navigation {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 25px;
                    border-bottom: 1px solid #e5e7eb;
                    padding: 0 20px;
                    background: #fff;
                    border-radius: 8px 8px 0 0;
                }

                .tab-button {
                    padding: 15px 24px !important;
                    font-size: 14px !important;
                    font-weight: 500 !important;
                    color: #666 !important;
                    background: none !important;
                    border: none !important;
                    border-bottom: 2px solid transparent !important;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    display: inline-flex !important;
                    align-items: center;
                    gap: 8px;
                    margin-bottom: -1px;
                }

                .tab-button .dashicons {
                    font-size: 18px;
                    width: 18px;
                    height: 18px;
                    transition: transform 0.2s ease;
                }

                .tab-button:hover {
                    color: #34d399 !important;
                }

                .tab-button:hover .dashicons {
                    transform: scale(1.1);
                }

                .tab-button.active {
                    color: #34d399 !important;
                    border-bottom-color: #34d399 !important;
                }

                /* 内容区域样式 */
                .tab-content {
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                }

                .tab-pane {
                    display: none;
                    padding: 0;
                }

                .tab-pane.active {
                    display: block;
                }

                /* 风险提示样式 */
                .notice-warning {
                    margin: 0 0 20px 0 !important;
                    padding: 16px 20px !important;
                    border: 1px solid rgba(255, 171, 0, 0.2) !important;
                    background: #fffaf0 !important;
                    border-radius: 8px !important;
                }

                .notice-warning p {
                    display: flex;
                    align-items: center;
                    margin: 0 0 10px 0;
                    color: #b45309;
                    font-size: 14px;
                    font-weight: 600;
                }

                .notice-warning .dashicons {
                    color: #f59e0b;
                    font-size: 20px;
                    width: 20px;
                    height: 20px;
                    margin-right: 8px;
                }

                .notice-warning ul {
                    margin: 0;
                    padding-left: 28px;
                }

                .notice-warning li {
                    color: #92400e;
                    margin: 8px 0;
                    font-size: 13px;
                    line-height: 1.5;
                }

                /* 表格样式优化 */
                .table-wrapper {
                    margin: 0;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    overflow: hidden;
                    max-height: 460px;
                    overflow-y: auto;
                }

                .table-wrapper::-webkit-scrollbar {
                    width: 8px;
                    height: 8px;
                }

                .table-wrapper::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 4px;
                }

                .table-wrapper::-webkit-scrollbar-thumb {
                    background: #ddd;
                    border-radius: 4px;
                }

                .table-wrapper::-webkit-scrollbar-thumb:hover {
                    background: #ccc;
                }

                .wp-list-table {
                    border: none;
                    margin: 0;
                }

                .wp-list-table th {
                    background: #f9fafb;
                    padding: 12px 16px;
                    font-size: 13px;
                    font-weight: 600;
                    color: #374151;
                    text-transform: none;
                    letter-spacing: normal;
                    border-bottom: 1px solid #e5e7eb;
                }

                .wp-list-table td {
                    padding: 12px 16px;
                    vertical-align: middle;
                    border-bottom: 1px solid #f0f0f1;
                    font-size: 13px;
                }

                .wp-list-table tr:hover td {
                    background: #f9fafb;
                }

                .wp-list-table tr:last-child td {
                    border-bottom: none;
                }

                /* 表格内容样式 */
                .column-name strong {
                    color: #1f2937;
                    font-size: 13px;
                    font-weight: 500;
                }

                .column-description {
                    color: #6b7280;
                    font-size: 13px;
                }

                .engine-badge {
                    display: inline-block;
                    padding: 4px 10px;
                    background: #f0f9ff;
                    color: #0369a1;
                    font-size: 12px;
                    font-weight: 500;
                    border-radius: 20px;
                    border: 1px solid #e0f2fe;
                }

                .size-value {
                    color: #4b5563;
                    font-family: 'SF Mono', Monaco, monospace;
                    font-size: 12px;
                }

                /* 按钮样式优化 */
                .optimize-table-button {
                    padding: 6px 12px !important;
                    height: 28px !important;
                    background: #34d399 !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 6px !important;
                    font-size: 12px !important;
                    font-weight: 500 !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 6px !important;
                    transition: all 0.2s ease !important;
                    min-width: 80px !important;
                    justify-content: center !important;
                }

                .optimize-table-button:hover {
                    background: #10b981 !important;
                    transform: translateY(-1px);
                }

                .optimize-table-button .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                    margin: 0;
                }

                #optimize-all-tables {
                    padding: 8px 16px !important;
                    height: 36px !important;
                    background: #34d399 !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 6px !important;
                    font-size: 13px !important;
                    font-weight: 500 !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 8px !important;
                    transition: all 0.2s ease !important;
                }

                #optimize-all-tables:hover {
                    background: #10b981 !important;
                    transform: translateY(-1px);
                }

                /* 卡片样式优化 */
                .optimization-card {
                    background: #fff;
                    border-radius: 8px;
                }

                .card-header {
                    padding: 16px 20px;
                    border-bottom: 1px solid #f0f0f1;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .card-header h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: #1f2937;
                }

                .card-content {
                    padding: 20px;
                }

                /* 数据库清理项样式 */
                .cleanup-grid {
                    display: grid;
                    gap: 12px;
                }

                .cleanup-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 16px;
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    transition: all 0.2s ease;
                }

                .cleanup-item:hover {
                    border-color: #d1d5db;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                }

                .cleanup-info {
                    flex: 1;
                    margin-right: 20px;
                }

                .cleanup-header {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 6px;
                }

                .cleanup-header h4 {
                    margin: 0;
                    font-size: 14px;
                    font-weight: 500;
                    color: #1f2937;
                }

                .cleanup-count {
                    padding: 2px 8px;
                    background: #f3f4f6;
                    color: #6b7280;
                    font-size: 12px;
                    font-weight: 500;
                    border-radius: 20px;
                }

                .cleanup-desc {
                    margin: 0;
                    font-size: 13px;
                    color: #6b7280;
                    line-height: 1.5;
                }

                .cleanup-button {
                    padding: 6px 12px !important;
                    height: 28px !important;
                    background: #34d399 !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 6px !important;
                    font-size: 12px !important;
                    font-weight: 500 !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 6px !important;
                    transition: all 0.2s ease !important;
                    min-width: 80px !important;
                    justify-content: center !important;
                }

                .cleanup-button:hover {
                    background: #10b981 !important;
                    transform: translateY(-1px);
                }

                .cleanup-button .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                    margin: 0;
                }
                </style>

                <script>
                jQuery(document).ready(function($) {
                    // 选项卡切换功能
                    $('.tab-button').on('click', function() {
                        var targetTab = $(this).data('tab');
                        
                        // 更新按钮状态
                        $('.tab-button').removeClass('active');
                        $(this).addClass('active');
                        
                        // 更新内容显示
                        $('.tab-pane').removeClass('active');
                        $('#' + targetTab).addClass('active');
                    });
                });
                </script>
            </div>

            <div id="media-optimization" class="content-section">
                <h2>媒体优化</h2>
                <div class="optimization-section">
                    <div class="optimization-card">
                        <div class="card-header">
                            <h3>媒体优化</h3>
                            <button class="optimize-button">开始优化</button>
                        </div>
                        <div class="card-content">
                            <ul class="optimization-options">
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">压缩图片</span>
                                            <span class="option-desc">优化图片质量，减小文件体积</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="compress_images">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">生成 WebP 格式</span>
                                            <span class="option-desc">转换图片为 WebP 格式，提供更好的压缩率</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="generate_webp">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">清理未使用的媒体文件</span>
                                            <span class="option-desc">删除未被引用的图片和媒体文件</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="clean_unused_media">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div id="cache-optimization" class="content-section">
                <h2>缓存优化</h2>
                <div class="optimization-section">
                    <div class="optimization-card">
                        <div class="card-header">
                            <h3>缓存优化</h3>
                            <button class="optimize-button">开始优化</button>
                        </div>
                        <div class="card-content">
                            <ul class="optimization-options">
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">启用页面缓存</span>
                                            <span class="option-desc">缓存静态页面内容，提高加载速度</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="enable_page_cache">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">启用浏览器缓存</span>
                                            <span class="option-desc">设置适当的缓存头，优化资源加载</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="enable_browser_cache">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <div class="option-text">
                                            <span class="option-title">预生成缓存</span>
                                            <span class="option-desc">自动预生成热门页面的缓存</span>
                                        </div>
                                        <div class="switch-wrapper">
                                            <input type="checkbox" name="pregenerate_cache">
                                            <span class="switch-slider"></span>
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 网站防护内容区域 -->
            <div id="site-protection" class="content-section">
                <h2>网站防护</h2>
                <div class="optimization-card">
                    <div class="card-header">
                        <h3>网站防护</h3>
                    </div>
                    <div class="card-content">
                        <ul class="optimization-options">
                            <!-- 现有的功能 -->
                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用右键菜单</span>
                                        <span class="option-desc">阻止访问者使用鼠标右键菜单</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_right_click">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>
                            
                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用F12开发者工具</span>
                                        <span class="option-desc">阻止访问者使用F12打开浏览器开发者工具</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_f12">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <!-- 新增的功能 -->
                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用开发者工具快捷键</span>
                                        <span class="option-desc">禁用 Ctrl+Shift+I 和 Ctrl+Shift+J 快捷键</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_dev_shortcuts">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用查看源代码</span>
                                        <span class="option-desc">禁用 Ctrl+U 快捷键</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_view_source">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用页面保存</span>
                                        <span class="option-desc">禁用 Ctrl+S 快捷键</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_save">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用页面打印</span>
                                        <span class="option-desc">禁用 Ctrl+P 快捷键</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_print">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用复制粘贴</span>
                                        <span class="option-desc">禁用 Ctrl+C 和 Ctrl+V 快捷键</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_copy_paste">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用拖拽</span>
                                        <span class="option-desc">阻止页面元素的拖拽操作</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_drag">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用文本选择</span>
                                        <span class="option-desc">阻止页面文本的选择操作</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_select">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>

                            <li class="option-item">
                                <label>
                                    <div class="option-info">
                                        <span class="option-title">禁用控制台</span>
                                        <span class="option-desc">阻止使用浏览器控制台</span>
                                    </div>
                                    <div class="switch-wrapper">
                                        <input type="checkbox" name="disable_console">
                                        <span class="switch-slider"></span>
                                    </div>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 