## 模板开发

模板开发包括本地模板开发和插件模板开发


1. 本地模板位置

```
/view/laket-cms/template
```

本地开发模板只需要修改对应的模板


2. 插件模板开发

```php
// 注入信息放到服务提供者 `boot()` 方法
public function boot()
{
    // 判断主题并使用插件模板
    add_filter('cms_theme_path', function($themePath, $theme) {
        if ($theme == 'green') {
            return root_path('view/cms-green/green');
        }
        
        return $themePath;
    });

    // 添加模板到主题列表
    add_filter('cms_themes', function($themes) {
        // 获取插件主题信息
        $theme = Template::themeInfo(root_path('view/cms-green/green'));
        
        $themes[] = $theme;
        
        return $themes;
    });
}
```

插件模板和本地模板信息和文件相同
