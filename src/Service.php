<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS;

use think\facade\Console;

use Laket\Admin\Flash\Menu;
use Laket\Admin\Facade\View;
use Laket\Admin\Facade\Flash;
use Laket\Admin\Flash\Service as BaseService;

use Laket\Admin\CMS\Command;
use Laket\Admin\CMS\Service\Template;
use Laket\Admin\CMS\Service\Model as ModelService;
use Laket\Admin\CMS\Model\Model as ModelModel;
use Laket\Admin\CMS\Model\Settings as SettingsModel;

class Service extends BaseService
{
    /**
     * composer
     */
    public $composer = __DIR__ . '/../composer.json';
    
    protected $slug = 'laket-admin.flash.cms';
    
    /**
     * 脚本
     *
     * @var array
     */
    protected $commands = [
        Command\Demo::class,
    ];
    
    /**
     * 注册
     */
    public function register()
    {
        $this->registerCommand();
    }
    
    /**
     * 启动
     */
    public function boot()
    {
        Flash::extend('laket/laket-cms', __CLASS__);
    }
    
    /**
     * 开始，只有启用后加载
     */
    public function start()
    {
        // 路由
        $this->loadRoutesFrom(__DIR__ . '/../resources/routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../resources/routes/cms.php');
        
        // 全局配置
        $this->registerConfig();
        
        // 导入帮助文件
        $this->loadFilesFrom(__DIR__ . '/helper.php');
        
        // 配置信息
        $this->app->config->set(cms_config(), 'cms');
        
        // 视图
        $this->loadView();
    }
    
    /**
     * 脚本
     */
    public function registerCommand()
    {
        $this->commands($this->commands);
    }
    
    /**
     * 全局配置
     */
    protected function registerConfig() 
    {
        // 配置文件
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/cms.php', 'cms');
        
        // 字段类型
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/field.php', 'cms_field');
    }
    
    /**
     * 视图
     *
     * @return void
     */
    public function loadView()
    {
        // 后台模板位置
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laket-cms');
        
        // 前台模板位置
        $this->loadViewsFrom(config('cms.template', ''), 'cms');
        
        // 注册视图标签
        $taglibs = config('cms.taglib_build_in', []);
        $this->registerViewTaglib($taglibs);
        
        // 设置视图公用参数
        $viewPath = Template::themePath();
        View::assign([
            'cms_view_path' => $viewPath,
        ]);
    }
    
    /**
     * 安装后
     */
    public function install()
    {
        $slug = $this->slug;
        $menus = include __DIR__ . '/../resources/menus/menus.php';
        
        // 添加菜单
        Menu::create($menus);
        
        // 数据库
        Flash::executeSql(__DIR__ . '/../resources/database/install.sql');
        Flash::executeSql(__DIR__ . '/../resources/database/demo.sql');
        
        // 填充默认配置
        $setting = include __DIR__ . '/../resources/setting/setting.php';
        if (! empty($setting) && is_array($setting)) {
            foreach ($setting as $key => $item) {
                SettingsModel::insert([
                    'name'  => $key,
                    'value' => $item,
                ]);
            }
        }
        
        // 全局配置
        $this->registerConfig();
        
        // 推送配置文件 
        $this->publishes([
            __DIR__ . '/../resources/config/cms.php' => config_path() . 'cms.php',
        ], 'laket-cms-config');
        
        Console::call('laket-admin:publish', [
            '--tag=laket-cms-config',
            '--force',
        ]);

        // 推送静态文件
        $this->publishes([
            __DIR__ . '/../resources/assets/template/' => config('cms.assets', ''),
        ], 'laket-cms-assets');
        
        Console::call('laket-admin:publish', [
            '--tag=laket-cms-assets',
            '--force',
        ]);
        
        // 推送模板文件
        $this->publishes([
            __DIR__ . '/../resources/template/' => config('cms.template', ''),
        ], 'laket-cms-template');
        
        Console::call('laket-admin:publish', [
            '--tag=laket-cms-template',
            '--force',
        ]);
    }
    
    /**
     * 卸载后
     */
    public function uninstall()
    {
        Menu::delete($this->slug);
        
        // 删除模型表
        $models = ModelModel::order('id ASC')->select();
        foreach ($models as $model) {
            ModelModel::where([
                'id' => $model['id'],
            ])->delete();
            
            // 删除表
            ModelService::create()->deleteTable($model['tablename']);
        }
        
        // 数据库
        Flash::executeSql(__DIR__ . '/../resources/database/uninstall.sql');
    }
    
    /**
     * 更新后
     */
    public function upgrade()
    {}
    
    /**
     * 启用后
     */
    public function enable()
    {
        Menu::enable($this->slug);
    }
    
    /**
     * 禁用后
     */
    public function disable()
    {
        Menu::disable($this->slug);
    }
    
}
