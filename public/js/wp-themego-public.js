(function($) {
    'use strict';

    // 获取保护设置
    const settings = window.wpThemeGoPublic ? window.wpThemeGoPublic.settings : {};

    // 禁用右键点击
    if (settings.disable_right_click) {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    }

    // 禁用 F12 键
    if (settings.disable_f12) {
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
        });
    }

    // 禁用开发者工具快捷键
    if (settings.disable_dev_shortcuts) {
        document.addEventListener('keydown', function(e) {
            // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
            if ((e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) ||
                // Ctrl+U (查看源代码)
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
        });
    }

    // 禁用查看源代码
    if (settings.disable_view_source) {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.keyCode === 85) {
                e.preventDefault();
                return false;
            }
        });
    }

    // 禁用保存页面
    if (settings.disable_save) {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                return false;
            }
        });
    }

    // 禁用打印
    if (settings.disable_print) {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.keyCode === 80) {
                e.preventDefault();
                return false;
            }
        });
        window.addEventListener('beforeprint', function(e) {
            e.preventDefault();
            return false;
        });
    }

    // 禁用复制粘贴
    if (settings.disable_copy_paste) {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86)) {
                e.preventDefault();
                return false;
            }
        });
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            return false;
        });
        document.addEventListener('paste', function(e) {
            e.preventDefault();
            return false;
        });
    }

    // 禁用拖拽
    if (settings.disable_drag) {
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        document.addEventListener('drop', function(e) {
            e.preventDefault();
            return false;
        });
    }

    // 禁用文本选择
    if (settings.disable_select) {
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });
        // 添加禁用选择的CSS类
        $('body').addClass('wp-themego-no-select');
    }

    // 禁用控制台
    if (settings.disable_console) {
        // 重写 console 方法
        const noop = function() {};
        const methods = ['log', 'debug', 'info', 'warn', 'error', 'assert', 'dir', 'dirxml', 'trace', 'group', 'groupEnd'];
        
        methods.forEach(function(method) {
            console[method] = noop;
        });
    }

})(jQuery); 