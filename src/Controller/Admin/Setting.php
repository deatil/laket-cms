<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use think\facade\Cache;

use Lake\File;

use Laket\Admin\CMS\Model\Settings as SettingsModel;

/**
 * 设置
 *
 * @create 2024-6-12
 * @author deatil
 */
class Setting extends Base
{
    /**
     * 设置
     */
    public function index()
    {
        // 默认配置
        $default_setting = [
            'web_site_status' => 0,
            'web_site_tag' => 0,
            'web_site_search' => 0,
            // 'web_theme' => 'lake',
    
            'web_site_logo' => '', // 网站LOGO
            'web_site_company' => '', // 公司名称
            'web_site_address' => '', // 地址
            'web_site_qq' => '', // QQ
            'web_site_telphone' => '', // 电话
            'web_site_phone' => '', // 手机
            'web_site_email' => '', // 邮箱
            'web_site_icp' => '', // 备案信息
            'web_site_statistics' => '', // 站点代码
            
            'site_url' => '', // 站点代码
            'site_name' => '',
            'site_slogan' => '', // 网站标语
            'site_keywords' => '',
            'site_description' => '',
            
            'site_cache_time' => 3600,
        ];

        if ($this->request->isPost()) {
            $setting = $this->request->param('setting/a');
            $setting['web_site_status'] = isset($setting['web_site_status']) ? 1 : 0;
            
            $setting = array_merge($default_setting, $setting);

            if (!empty($setting)) {
                foreach ($setting as $key => $item) {
                    $info = SettingsModel::where([
                        'name' => $key,
                    ])->find();
                    
                    if (! empty($info)) {
                        SettingsModel::where([
                            'name' => $key,
                        ])->update([
                            'value' => $item,
                        ]);
                    } else {
                        SettingsModel::insert([
                            'name' => $key,
                            'value' => $item,
                        ]);
                    }
                }
            }
            
            Cache::delete("cms_setting");
            
            return $this->success('设置更新成功！');
        } else {
            $config = SettingsModel::column('name,value');
            
            $setting = [];
            if (!empty($config)) {
                foreach ($config as $val) {
                    $setting[$val['name']] = $val['value'];
                }
            }
            
            if (!empty($setting)) {
                $setting = array_merge($default_setting, $setting);
            } else {
                $setting = $default_setting;
            }
            
            $this->assign('setting', $setting);

            return $this->fetch("laket-cms::setting.index");
        }

    }
    
    /**
     * 设置主题
     */
    public function theme()
    {
        if ($this->request->isPost()) {
            $name = $this->request->param('name', 'default');
            
            $info = SettingsModel::where([
                'name' => 'web_theme',
            ])->find();
            
            if (! empty($info)) {
                SettingsModel::where([
                    'name' => 'web_theme',
                ])->update([
                    'value' => $name,
                ]);
            } else {
                SettingsModel::insert([
                    'name' => 'web_theme',
                    'value' => $name,
                ]);
            }
            
            // 清除缓存
            SettingsModel::clearCache();
            
            return $this->success('启用模版('.$name.')成功！');
        } else {
            $theme = SettingsModel::where([
                    'name' => 'web_theme',
                ])->value('value');
            $this->assign("theme", $theme);
            
            // 主题
            $themes = cms_themes();
            $this->assign("themes", $themes);

            return $this->fetch("laket-cms::setting.theme");
        }
    }
    
}
