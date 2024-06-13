<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Model;

use think\facade\Cache;
use think\Model as BaseModel;

use Laket\Admin\Support\Tree;

use Laket\Admin\CMS\Support\Field as SupportField;

/**
 * 栏目
 */
class Category extends BaseModel
{
    // 设置当前模型对应的数据表名称
    protected $name = 'cms_category';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    // 追加字段
    protected $append = [
        'cate_url',
        'info_url',
        'list_grids',
    ];
    
    public static function onBeforeInsert($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }
    
    public static function onBeforeUpdate($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
    }
    
    /**
     * 栏目链接
     */
    public function getCateUrlAttr()
    {
        $url = "";
        
        if (! empty($this->index_url)) {
            $url = str_replace([
                '{id}',
                '{name}',
                '{title}',
            ], [
                $this->id,
                $this->name,
                $this->title,
            ], $this->index_url);
        }
        
        return $url;
    }
    
    /**
     * 内容链接
     */
    public function getInfoUrlAttr()
    {
        $url = "";
        
        if (! empty($this->content_url)) {
            $url = str_replace([
                '{cateid}',
                '{name}',
                '{title}',
            ], [
                $this->id,
                $this->name,
                $this->title,
            ], $this->content_url);
        }
        
        return $url;
    }
    
    /**
     * 列表定义
     */
    public function getListGridsAttr()
    {
        $listGrids = SupportField::parseGrid($this->list_grid);
        return $listGrids;
    }
    
    /**
     * 模型
     */
    public function model()
    {
        return $this->hasOne(Model::class, 'id', 'modelid');
    }
    
    /**
     * 格式化内容链接
     */
    public static function formatInfoUrl($cateid, $id)
    {
        $data = static::where([
            ['id', '=', $cateid],
            ['status', '=', 1],
        ])->find();
        
        $url = str_replace([
            '{id}',
        ], [
            $id,
        ], $data['info_url']);
        
        return $url;
    }
    
    /**
     * 获取子级列表
     */
    public static function getChildren($id)
    {
        $result = static::where([
                ['status', '=', 1],
            ])
            ->order('sort ASC')
            ->select()
            ->toArray();
        
        $Tree = new Tree();
        $resultTree = $Tree->withData($result)->buildArray($id);
        $list = $Tree->buildFormatList($resultTree, 'title');
        
        return $list;
    }
    
    /**
     * 获取子级列表树
     */
    public static function getChildrenTree($id)
    {
        $result = static::where([
                ['status', '=', 1],
            ])
            ->order('sort ASC')
            ->select();
        
        $Tree = new Tree();
        $list = $Tree->withData($result)->buildArray($id);
        
        return $list;
    }
    
    /**
     * 获取子级id列表
     */
    public static function getChildrenIds($id)
    {
        $list = static::getChildren($id);
        
        $ids = collect($list)->map(function($item) {
            return $item['id'];
        })->toArray();
        
        return $ids;
    }
    
    /**
     * 获取全部启用列表
     */
    public static function getAll()
    {
        $cacheId = 'cms_cate_cache';
        
        $list = Cache::get($cacheId);
        if (! $list) {
            $list = static::where([
                    ['status', '=', 1],
                ])
                ->order('sort ASC')
                ->select()
                ->toArray();
            
            Cache::set($cacheId, $list, 10080);
        }
        
        return $list;
    }
}
