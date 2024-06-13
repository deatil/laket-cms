<?php

use think\helper\Arr;
use Lake\File;

use Laket\Admin\CMS\Support\Pinyin;
use Laket\Admin\CMS\Service\Template;
use Laket\Admin\CMS\Model\Settings as SettingsModel;

/**
 * 获取配置
 */
function cms_config($key = null, $default = null) {
    return SettingsModel::config($key, $default);
}

/**
 * 当前主题目录
 */
function cms_theme_path($path = '') {
    return Template::themePath($path);
}

/**
 * 所有主题
 */
function cms_themes($path = null) {
    return Template::themes($path);
}

/**
 * 静态文件
 */
function cms_assets($path = null) {
    $root = config('cms.assets_url', '');
    
    $theme = SettingsModel::config('web_theme', 'default');
    if (! empty($theme)) {
        $root .= '/' . $theme;
    }
    
    return $root . ($path ? '/' . ltrim($path, '/') : $path);
}

/**
 * 获取中文字符拼音首字母组合
 */
function cms_get_py_first($zh) {
    return Pinyin::encode($zh, 'all', 'utf8');
}

/**
 * 栏目链接
 */
function cms_cate_url($catename) {
    if (is_int($catename)) {
        $url = laket_route('cms.cate', [
            'cateid' => $catename, 
            'page' => '[PAGE]',
        ]);
    } else {
        $url = laket_route('cms.cate', [
            'catename' => $catename, 
            'page' => '[PAGE]',
        ]);
    }
    
    return $url;
}

/**
 * 详情链接
 */
function cms_content_url($catename, $contentid) {
    if (is_int($catename)) {
        $url = laket_route('cms.content', [
            'cateid' => $catename, 
            'id' => $contentid,
        ]);
    } else {
        $url = laket_route('cms.content', [
            'catename' => $catename, 
            'id' => $contentid,
        ]);
    }
    
    return $url;
}

/**
 * 单页链接
 */
function cms_page_url($catename) {
    if (is_int($catename)) {
        $url = laket_route('cms.page', [
            'cateid' => $catename, 
        ]);
    } else {
        $url = laket_route('cms.page', [
            'catename' => $catename, 
        ]);
    }
    
    return $url;
}

/**
 * 标签详情链接
 */
function cms_tag_detail_url($title) {
    $url = laket_route('cms.tag-detail', [
        'title' => $title, 
    ]);
    
    return $url;
}

// =================

/**
 * 字符截取
 * @param $string 需要截取的字符串
 * @param $length 长度
 * @param $dot
 */
function cms_str_cut($sourcestr, $length, $dot = '...') {
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); 
    while (($n < $length) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); 
        if ($ascnum >= 224) { 
            $returnstr = $returnstr . substr($sourcestr, $i, 3); 
            $i = $i + 3; 
            $n++; 
        } elseif ($ascnum >= 192) { 
            $returnstr = $returnstr . substr($sourcestr, $i, 2); 
            $i = $i + 2; 
            $n++; 
        } elseif ($ascnum >= 65 && $ascnum <= 90) {
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; 
            $n++; 
        } else {
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; 
            $n = $n + 0.5; 
        }
    }
    
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; 
    }
    
    return $returnstr;
}

/**
 * 解析配置
 * @param string $value 配置值
 * @return array|string
 */
function cms_parse_attr($data = '')
{
    $array = preg_split('/[,;\r\n ]+/', trim($data, ",;\r\n"));
    if (strpos($data, ':')) {
        $value = [];
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    
    return $value;
}
