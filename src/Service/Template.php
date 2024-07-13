<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Service;

use think\helper\Arr;

use Laket\Admin\CMS\Model\Settings as SettingsModel;

/**
 * 模版
 *
 * @create 2020-1-14
 * @author deatil
 */
class Template 
{
    /**
     * 模版根目录
     */
    public static function path($path = '') 
    {
        $templatePath = config('cms.template', '');
        
        return $templatePath . $path;
    }

    /**
     * 当前主题目录
     */
    public static function themePath($path = '') 
    {
        $theme = SettingsModel::config('web_theme', 'default');
        
        $themePath = static::path($theme);
        $themePath = $themePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
        
        // 主题路径过滤器
        $themePath = apply_filters('cms_theme_path', $themePath, $theme);
        
        return $themePath;
    }

    /**
     * 所有主题
     */
    public static function themes($path = null) 
    {
        if (empty($path)) {
            $path = static::path();
        }
        
        $themes = glob($path.'/*');
        
        $newThemes = collect($themes)
            ->map(function($item) {
                return static::themeInfo($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->values()
            ->toArray();
        
        // 所有主题过滤器
        $newThemes = apply_filters('cms_themes', $newThemes);
        
        return $newThemes;
    }

    /**
     * 获取主题信息
     */
    public static function themeInfo($path) 
    {
        if (! empty($path) && file_exists($infoFile = $path . '/info.php')) {
            $info = include $infoFile;
            
            $coverFile = $path . '/' . Arr::get($info, 'cover');
            if (file_exists($coverFile)) {
                $coverData = file_get_contents(realpath($coverFile));
                $cover = "data:image/png;base64,".base64_encode($coverData);
            } else {
                $cover = "";
            }
            
            $themeInfo = [
                'name'    => Arr::get($info, 'name'),
                'remark'  => Arr::get($info, 'remark'),
                'cover'   => $cover,
                'version' => Arr::get($info, 'version'),
                'author'  => Arr::get($info, 'author'),
            ];
            
            // 主题信息过滤器
            $themeInfo = apply_filters('cms_theme_info', $themeInfo);
            
            return $themeInfo;
        }
        
        return [];
    }

}