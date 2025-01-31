(function($) {
    'use strict';

    // 获取防护设置输入框的函数
    function getProtectionInputs() {
        return $('#site-protection').find('input[type="checkbox"]');
    }

    // 更新开关状态的函数
    function updateSwitchStates(settings) {
        if (!settings) return;
        
        getProtectionInputs().each(function() {
            const input = $(this);
            const name = input.attr('name');
            if (name in settings) {
                input.prop('checked', settings[name]);
            }
        });
    }

    $(document).ready(function() {
        console.log('DOM Ready - 初始化网站防护设置');
        
        // 初始化网站防护设置
        initializeProtectionSettings();
        
        // 菜单交互
        $('.menu-item.has-submenu > a').on('click', function(e) {
            e.preventDefault();
            const menuItem = $(this).parent();
            const firstSubmenuItem = menuItem.find('.submenu li:first-child a');
            
            // 如果菜单项未激活，则激活并显示第一个子菜单内容
            if (!menuItem.hasClass('active')) {
                // 关闭其他展开的菜单
                $('.menu-item.has-submenu').not(menuItem).removeClass('active');
                // 激活当前菜单
                menuItem.addClass('active');
                // 触发第一个子菜单项的点击事件
                if (firstSubmenuItem.length) {
                    firstSubmenuItem.trigger('click');
                }
            }
            // 如果菜单项已经激活，则保持当前状态
        });

        // 内容区域切换
        $('.menu-item a, .submenu a').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');
            const menuItem = $(this).parent();
            
            if (target === '#') return;

            // 如果点击的是子菜单项
            if (menuItem.closest('.submenu').length > 0) {
                // 移除所有菜单项的激活状态
                $('.menu-item a, .submenu li').removeClass('active');
                // 激活当前子菜单项
                menuItem.addClass('active');
                // 保持父菜单项的展开状态
                menuItem.closest('.menu-item.has-submenu').addClass('active');

                // 显示对应的内容区域
                $('.content-section').removeClass('active');
                $(target).addClass('active');
            }
            // 如果点击的是没有子菜单的主菜单项
            else if (!menuItem.hasClass('has-submenu')) {
                // 移除所有菜单项的激活状态
                $('.menu-item a, .submenu li').removeClass('active');
                menuItem.addClass('active');
                // 关闭所有展开的子菜单
                $('.menu-item.has-submenu').removeClass('active');

                // 显示对应的内容区域
                $('.content-section').removeClass('active');
                $(target).addClass('active');
            }
        });

        // 搜索功能
        $('.search-box input').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            // 在主题列表中搜索
            $('.theme-card').each(function() {
                const themeName = $(this).find('h3').text().toLowerCase();
                const themeDesc = $(this).find('p').text().toLowerCase();
                
                if (themeName.includes(searchTerm) || themeDesc.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // 页面加载时初始化开关状态
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'wp_themego_get_settings',
                nonce: wpThemeGoAdmin.nonce,
                type: 'protection'
            },
            success: function(response) {
                if (response.success && response.data.settings) {
                    // 更新所有开关状态
                    const settings = response.data.settings;
                    $('#site-protection').find('input[type="checkbox"]').each(function() {
                        const input = $(this);
                        const name = input.attr('name');
                        if (name in settings) {
                            input.prop('checked', settings[name]);
                        }
                    });
                }
            }
        });

        // 保存按钮点击事件
        $('.save-button').on('click', function() {
            // 获取当前激活的内容区域
            const activeSection = $('.content-section.active');
            const sectionId = activeSection.attr('id');
            const button = $(this);
            
            // 根据不同的内容区域收集不同的设置
            let settings = {};
            let type = '';
            let hasChanges = false;
            
            switch (sectionId) {
                case 'site-protection':
                    type = 'protection';
                    // 收集所有网站防护设置的状态
                    getProtectionInputs().each(function() {
                        const input = $(this);
                        const name = input.attr('name');
                        const currentValue = input.is(':checked');
                        const originalValue = input.data('original-value');
                        
                        // 检查是否有改变
                        if (currentValue !== originalValue) {
                            hasChanges = true;
                        }
                        settings[name] = currentValue;
                    });

                    // 如果没有改变，显示提示并返回
                    if (!hasChanges) {
                        showNotification('当前设置未改变', 'info');
                        return;
                    }
                    break;
                default:
                    showNotification('请先选择要保存的设置项', 'error');
                    return;
            }
            
            console.log('保存设置:', settings);
            
            // 禁用按钮并显示加载状态
            button.prop('disabled', true).addClass('updating-message');
            
            // 发送AJAX请求
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'wp_themego_save_settings',
                    nonce: wpThemeGoAdmin.nonce,
                    type: type,
                    settings: settings
                },
                success: function(response) {
                    console.log('保存响应:', response);
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        
                        // 更新所有开关的原始值
                        if (response.data.settings) {
                            getProtectionInputs().each(function() {
                                const input = $(this);
                                const name = input.attr('name');
                                if (name in response.data.settings) {
                                    input.data('original-value', response.data.settings[name]);
                                }
                            });
                        }
                    } else {
                        showNotification(response.data.message || '保存失败，请重试', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    showNotification('保存失败，请重试', 'error');
                },
                complete: function() {
                    // 恢复按钮状态
                    button.prop('disabled', false).removeClass('updating-message');
                }
            });
        });

        // 添加通知函数
        function showNotification(message, type = 'success') {
            // 移除现有通知
            $('.wp-themego-notification').remove();
            
            // 创建新通知
            const notification = $('<div>', {
                class: `wp-themego-notification ${type}`
            }).append(
                $('<span>', {
                    class: 'notification-icon'
                })
            ).append(
                $('<span>', {
                    class: 'notification-message',
                    text: message
                })
            );
            
            // 添加到页面
            $('body').append(notification);
            
            // 使用 requestAnimationFrame 确保 DOM 更新后再添加显示类
            requestAnimationFrame(() => {
                notification.addClass('show');
                
                // 3秒后隐藏通知
                setTimeout(() => {
                    notification.addClass('hide').removeClass('show');
                    // 等待过渡动画完成后移除元素
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            });
        }

        // 主题激活处理
        $('.activate-theme').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const themeStylesheet = button.data('theme');
            
            if (!themeStylesheet) {
                return;
            }

            // 在激活过程中禁用按钮
            button.prop('disabled', true);
            button.text('正在启用...');

            // 发送AJAX请求激活主题
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wp_themego_activate_theme',
                    theme: themeStylesheet,
                    nonce: wpThemeGoAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 重新加载页面以显示更新后的主题状态
                        window.location.reload();
                    } else {
                        alert(response.data.message || '主题启用失败，请重试。');
                        button.prop('disabled', false);
                        button.text('启用主题');
                    }
                },
                error: function() {
                    alert('主题启用失败，请重试。');
                    button.prop('disabled', false);
                    button.text('启用主题');
                }
            });
        });

        // 初始化：根据URL hash激活对应的菜单项
        const hash = window.location.hash;
        if (hash) {
            const targetMenuItem = $(`.menu-item a[href="${hash}"], .submenu a[href="${hash}"]`);
            if (targetMenuItem.length) {
                targetMenuItem.trigger('click');
            }
        } else {
            // 如果没有hash，默认激活第一个菜单项的第一个子菜单
            const firstSubmenuItem = $('.menu-item.has-submenu:first .submenu li:first-child a');
            if (firstSubmenuItem.length) {
                firstSubmenuItem.trigger('click');
            }
        }

        // 移动端菜单处理
        // 添加遮罩层
        $('body').append('<div class="sidebar-overlay"></div>');
        
        // 点击菜单按钮
        $('.mobile-menu-toggle').on('click', function() {
            $('.wp-themego-sidebar, .sidebar-overlay').addClass('active');
            $('body').css('overflow', 'hidden');
        });
        
        // 点击遮罩层关闭菜单
        $('.sidebar-overlay').on('click', function() {
            $('.wp-themego-sidebar, .sidebar-overlay').removeClass('active');
            $('body').css('overflow', '');
        });
        
        // 监听窗口大小变化
        $(window).on('resize', function() {
            if ($(window).width() > 782) {
                $('.wp-themego-sidebar, .sidebar-overlay').removeClass('active');
                $('body').css('overflow', '');
            }
        });

        // 搜索框切换
        $('.search-toggle').on('click', function(e) {
            e.preventDefault();
            $('.search-box').addClass('active').find('input').focus();
        });

        $('.search-close').on('click', function(e) {
            e.preventDefault();
            $('.search-box').removeClass('active');
        });

        // 点击外部关闭搜索框
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-box, .search-toggle').length) {
                $('.search-box').removeClass('active');
            }
        });
    });

    // 初始化网站防护设置函数
    function initializeProtectionSettings() {
        console.log('开始获取网站防护设置');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'wp_themego_get_settings',
                nonce: wpThemeGoAdmin.nonce,
                type: 'protection'
            },
            success: function(response) {
                console.log('获取设置响应:', response);
                if (response.success && response.data.settings) {
                    // 更新开关状态并记录初始值
                    getProtectionInputs().each(function() {
                        const input = $(this);
                        const name = input.attr('name');
                        if (name in response.data.settings) {
                            const value = response.data.settings[name];
                            input.prop('checked', value);
                            input.data('original-value', value);
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('获取设置失败:', status, error);
            }
        });
    }

})(jQuery); 