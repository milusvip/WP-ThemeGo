jQuery(document).ready(function($) {
    // 主题卡片悬停效果
    $('.theme-card').hover(
        function() {
            $(this).addClass('hover');
        },
        function() {
            $(this).removeClass('hover');
        }
    );

    // 下拉菜单切换
    $('.dropdown-toggle').on('click', function(e) {
        e.stopPropagation();
        $(this).siblings('.dropdown-menu').toggleClass('show');
    });

    // 点击其他地方关闭下拉菜单
    $(document).on('click', function() {
        $('.dropdown-menu').removeClass('show');
    });

    // 激活主题
    $('.activate-theme').on('click', function() {
        var button = $(this);
        var themeDir = button.data('theme');
        var card = button.closest('.theme-card');
        
        // 确认操作
        if (!confirm('确定要切换到这个主题吗？')) {
            return;
        }
        
        // 禁用按钮并显示加载状态
        button.prop('disabled', true).addClass('updating-message');
        
        // 发送AJAX请求
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_activate_theme',
                nonce: wpThemeGoAdmin.nonce,
                theme: themeDir
            },
            success: function(response) {
                if (response.success) {
                    // 更新UI状态
                    $('.theme-card').removeClass('active');
                    card.addClass('active');
                    
                    // 刷新页面以显示新主题
                    location.reload();
                } else {
                    alert(response.data.message || '激活主题失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                button.prop('disabled', false).removeClass('updating-message');
            }
        });
    });

    // 备份主题
    $('.backup-theme').on('click', function(e) {
        e.preventDefault();
        var link = $(this);
        var themeDir = link.data('theme');
        
        // 禁用链接并显示加载状态
        link.addClass('updating');
        
        // 发送AJAX请求
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_backup_theme',
                nonce: wpThemeGoAdmin.nonce,
                theme: themeDir
            },
            success: function(response) {
                if (response.success) {
                    alert('主题备份成功');
                    // 刷新备份列表
                    loadThemeBackups();
                } else {
                    alert(response.data.message || '备份主题失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                link.removeClass('updating');
            }
        });
    });

    // 删除主题
    $('.delete-theme').on('click', function(e) {
        e.preventDefault();
        var link = $(this);
        var themeDir = link.data('theme');
        
        // 确认操作
        if (!confirm('确定要删除这个主题吗？此操作不可恢复。')) {
            return;
        }
        
        // 禁用链接并显示加载状态
        link.addClass('updating');
        
        // 发送AJAX请求
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_delete_theme',
                nonce: wpThemeGoAdmin.nonce,
                theme: themeDir
            },
            success: function(response) {
                if (response.success) {
                    // 移除主题卡片
                    link.closest('.theme-card').fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message || '删除主题失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                link.removeClass('updating');
            }
        });
    });

    // 预览主题
    $('.preview-theme').on('click', function(e) {
        e.preventDefault();
        var themeDir = $(this).data('theme');
        var previewUrl = wpThemeGoAdmin.previewUrl + '&theme=' + themeDir;
        
        // 更新预览iframe的src
        $('#theme-preview-modal iframe').attr('src', previewUrl);
        
        // 显示模态框
        $('#theme-preview-modal').addClass('show');
    });

    // 关闭模态框
    $('.close-modal').on('click', function() {
        $(this).closest('.modal').removeClass('show');
    });

    // 点击模态框外部关闭
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            $(this).removeClass('show');
        }
    });

    // 加载主题备份列表
    function loadThemeBackups() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_get_theme_backups',
                nonce: wpThemeGoAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    var tbody = $('#theme-backups-modal tbody');
                    tbody.empty();
                    
                    if (response.data.backups.length === 0) {
                        tbody.append('<tr><td colspan="4">暂无备份</td></tr>');
                        return;
                    }
                    
                    response.data.backups.forEach(function(backup) {
                        tbody.append(
                            '<tr>' +
                            '<td>' + backup.theme + '</td>' +
                            '<td>' + backup.date + '</td>' +
                            '<td>' + backup.size + '</td>' +
                            '<td>' +
                            '<button class="button restore-backup" data-file="' + backup.file + '">' +
                            '<span class="dashicons dashicons-backup"></span> 恢复' +
                            '</button> ' +
                            '<button class="button delete-backup" data-file="' + backup.file + '">' +
                            '<span class="dashicons dashicons-trash"></span> 删除' +
                            '</button>' +
                            '</td>' +
                            '</tr>'
                        );
                    });
                }
            }
        });
    }

    // 恢复备份
    $(document).on('click', '.restore-backup', function() {
        var button = $(this);
        var file = button.data('file');
        
        if (!confirm('确定要恢复这个备份吗？当前主题文件将被覆盖。')) {
            return;
        }
        
        button.prop('disabled', true).addClass('updating-message');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_restore_backup',
                nonce: wpThemeGoAdmin.nonce,
                file: file
            },
            success: function(response) {
                if (response.success) {
                    alert('备份恢复成功');
                    location.reload();
                } else {
                    alert(response.data.message || '恢复备份失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                button.prop('disabled', false).removeClass('updating-message');
            }
        });
    });

    // 删除备份
    $(document).on('click', '.delete-backup', function() {
        var button = $(this);
        var file = button.data('file');
        
        if (!confirm('确定要删除这个备份吗？')) {
            return;
        }
        
        button.prop('disabled', true).addClass('updating-message');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_delete_backup',
                nonce: wpThemeGoAdmin.nonce,
                file: file
            },
            success: function(response) {
                if (response.success) {
                    button.closest('tr').fadeOut(function() {
                        $(this).remove();
                        if ($('#theme-backups-modal tbody tr').length === 0) {
                            $('#theme-backups-modal tbody').append('<tr><td colspan="4">暂无备份</td></tr>');
                        }
                    });
                } else {
                    alert(response.data.message || '删除备份失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                button.prop('disabled', false).removeClass('updating-message');
            }
        });
    });

    // 刷新主题列表
    $('#refresh-themes').on('click', function() {
        location.reload();
    });
}); 