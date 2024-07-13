## 模板开发

模板开发包括本地模板开发和插件模板开发


1. 本地模板位置

```
/view/laket-cms/template
```

本地开发模板只需要在模板文件夹内修改已有的模板或者添加新的模板


2. 插件模板开发

```php
use Laket\Admin\Flash\Service as BaseService;

class Service extends BaseService
{
    // 模板信息放到插件服务提供者 `start()` 方法
    // 开始，只有插件启用后加载
    public function start()
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
}
```

插件模板信息和本地模板信息及文件相同，请注意测试
