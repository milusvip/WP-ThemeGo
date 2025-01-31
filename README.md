# WP-ThemeGo 插件

WordPress主题管理增强插件，提供主题管理、优化、备份等功能。

## 文件结构

```
WP-ThemeGo/
├── wp-themego.php                 # 插件主文件
├── README.md                      # 说明文档
├── admin/                         # 管理后台相关文件
│   ├── class-wp-themego-admin.php # 管理类主文件
│   ├── js/                        # JavaScript文件
│   │   ├── theme-management.js    # 主题管理交互脚本
│   │   └── code-insertion.js     # 代码插入交互脚本
│   ├── css/                       # CSS样式文件
│   │   └── wp-themego-admin.css  # 管理界面样式
│   └── partials/                  # 页面模板文件
│       ├── views/                 # 视图文件
│       │   ├── dashboard/         # 控制面板视图
│       │   ├── theme-management/  # 主题管理视图
│       │   │   ├── list.php      # 主题列表页面
│       │   │   └── code-insertion.php # 代码插入页面
│       │   ├── theme-settings/    # 主题设置视图
│       │   ├── tools/            # 工具视图
│       │   └── optimization/     # 优化功能视图
│       └── includes/             # 功能文件
│           ├── functions/        # 通用函数
│           └── classes/         # 功能类
│               ├── class-theme-manager.php  # 主题管理类
│               └── class-code-manager.php   # 代码管理类
├── includes/                    # 核心功能文件
└── public/                     # 前端相关文件
```

## 主要功能模块

### 1. 主题管理 (Theme Management)
- 主题列表与管理
- 代码插入功能
  - 插入自定义代码
  - 页脚代码管理
  - 自定义CSS插入
- 主题预览

### 2. 数据库优化 (Database Optimization)
- 数据表优化
- 数据清理
- 数据备份

### 3. 主题设置 (Theme Settings)
- 常规设置
- 样式设置
- 高级设置

### 4. 工具箱 (Tools)
- 主题诊断
- 文件管理
- 导入/导出

### 5. 一键优化 (One-Click Optimization)
- 代码优化
- 数据库优化
- 媒体优化
- 缓存优化

## 代码插入功能说明

### 功能特点
1. 插入自定义代码
   - 支持在页头和页尾插入代码
   - 代码语法高亮
   - 代码验证和错误提示

2. 页脚代码管理
   - 统一管理页脚代码
   - 支持条件加载
   - 代码优先级设置

3. 自定义CSS插入
   - 实时预览效果
   - 支持SCSS语法
   - 自动压缩优化

### 开发说明

### 类文件命名规范
- 类文件名使用 `class-{name}.php` 格式
- 类名使用 `WP_ThemeGo_{Name}` 格式

### 视图文件组织
- 每个主要功能模块在 `views` 目录下有独立目录
- 视图文件使用语义化命名，如 `list.php`, `settings.php`

### 资源文件
- JavaScript文件放在 `admin/js` 目录
- CSS文件放在 `admin/css` 目录
- 每个主要功能模块可以有独立的js和css文件

### 开发注意事项
1. 所有文件都需要防止直接访问
2. 使用 WordPress 钩子系统进行功能扩展
3. 遵循 WordPress 编码标准
4. 确保代码安全性和兼容性
5. 做好错误处理和日志记录 