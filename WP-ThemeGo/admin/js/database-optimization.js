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

    // 优化单个表
    $('.optimize-table-button').on('click', function() {
        var button = $(this);
        var tableName = button.data('table');
        
        // 禁用按钮并显示加载状态
        button.prop('disabled', true).addClass('updating-message');
        
        // 发送AJAX请求
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_optimize_table',
                nonce: wpThemeGoAdmin.nonce,
                table: tableName
            },
            success: function(response) {
                if (response.success) {
                    // 显示成功消息
                    button.removeClass('updating-message').addClass('updated-message');
                    setTimeout(function() {
                        button.removeClass('updated-message');
                    }, 2000);
                } else {
                    // 显示错误消息
                    alert(response.data.message || '优化失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                // 重新启用按钮
                button.prop('disabled', false);
            }
        });
    });

    // 优化所有表
    $('#optimize-all-tables').on('click', function() {
        var button = $(this);
        
        // 确认操作
        if (!confirm('确定要优化所有数据表吗？此操作可能需要一些时间。')) {
            return;
        }
        
        // 禁用所有优化按钮
        $('.optimize-table-button, #optimize-all-tables').prop('disabled', true);
        button.addClass('updating-message');
        
        // 发送AJAX请求
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_optimize_all_tables',
                nonce: wpThemeGoAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    // 显示成功消息
                    button.removeClass('updating-message').addClass('updated-message');
                    setTimeout(function() {
                        button.removeClass('updated-message');
                    }, 2000);
                    
                    // 更新表格大小信息
                    if (response.data.tables) {
                        $.each(response.data.tables, function(tableName, result) {
                            var row = $('button[data-table="' + tableName + '"]').closest('tr');
                            if (result.size) {
                                row.find('.size-value').text(result.size);
                            }
                        });
                    }
                } else {
                    alert(response.data.message || '优化失败');
                }
            },
            error: function() {
                alert('请求失败，请重试');
            },
            complete: function() {
                // 重新启用所有按钮
                $('.optimize-table-button, #optimize-all-tables').prop('disabled', false);
            }
        });
    });

    // 清理数据
    $('.cleanup-button').on('click', function() {
        var button = $(this);
        var type = button.data('type');
        var itemName = button.closest('.cleanup-item').find('h4').text();
        
        // 确认操作
        if (!confirm('确定要删除' + itemName + '吗？此操作不可撤销。')) {
            return;
        }
        
        // 禁用按钮并显示加载状态
        button.prop('disabled', true).addClass('updating-message');
        
        // 发送AJAX请求
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_themego_cleanup_data',
                nonce: wpThemeGoAdmin.nonce,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    // 更新计数
                    button.closest('.cleanup-item').find('.cleanup-count').text('0');
                    // 禁用按钮
                    button.prop('disabled', true);
                    // 显示成功消息
                    button.removeClass('updating-message').addClass('updated-message');
                    setTimeout(function() {
                        button.removeClass('updated-message');
                    }, 2000);
                } else {
                    alert(response.data.message || '清理失败');
                    button.prop('disabled', false);
                }
            },
            error: function() {
                alert('请求失败，请重试');
                button.prop('disabled', false);
            },
            complete: function() {
                button.removeClass('updating-message');
            }
        });
    });
}); 