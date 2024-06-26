<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Service;

use think\facade\Db;

/**
 * 模型模版
 *
 * @create 2020-1-7
 * @author deatil
 */
class ModelTemplate 
{
    /**
     * $param string
     */
    protected $path = null;
    
    /**
     * 创建
     */
    public static function create()
    {
        return new static();
    }
    
    /**
     * 设置目录
     */
    public function withPath($path = null)
    {
        $this->path = $path;
        return $this;
    }
    
    /**
     * 列表
     */
    public function lists($path = null)
    {
        return $this->formatTemplates($path, 'list*');
    }
    
    /**
     * 详情
     */
    public function details($path = null)
    {
        return $this->formatTemplates($path, 'detail*');
    }
    
    /**
     * 单页
     */
    public function pages($path = null)
    {
        return $this->formatTemplates($path, 'page*');
    }
    
    /**
     * 列表模版
     */
    public function formatTemplates($path = null, $string = 'index*')
    {
        if (empty($path)) {
            $path = $this->path;
        }

        if (empty($path)) {
            return [];
        }
        
        if (! is_dir($path)) {
            return [];
        }
        
        $templates = glob(rtrim($path, '/') . DIRECTORY_SEPARATOR . $string);
            
        $newTemplates = collect($templates)->map(function ($item) use($path) {
            return substr($item, strlen($path) + 1);
        })->toArray();
        
        return $newTemplates;
    }
}