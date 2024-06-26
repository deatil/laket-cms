<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Template;

use think\facade\Cache;

use Laket\Admin\Support\Tree;

use Laket\Admin\CMS\Model\Navbar as NavbarModel;
use Laket\Admin\CMS\Model\Category as CategoryModel;
use Laket\Admin\CMS\Model\Tags as TagsModel;
use Laket\Admin\CMS\Model\Content as ContentModel;
use Laket\Admin\CMS\Model\Settings as SettingsModel;
use Laket\Admin\CMS\Model\ModelField as ModelFieldModel;
use Laket\Admin\CMS\Paginator\Page as PaginatorPage;

/**
 * 模版数据
 *
 * @create 2020-1-15
 * @author deatil
 */
class Model
{
    /**
     * 导航列表
     */
    public static function getNavbarList($tag = [])
    {
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 缓存
        $cache = !isset($tag['cache']) ? false : (int) $tag['cache'];
        
        // 是否使用树结构
        $tree = isset($tag['tree']) ? intval($tag['tree']) : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = NavbarModel::field($field)
            ->where($map)
            ->where($condition)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        if (! empty($tree)) {
            $Tree = new Tree();
            $list = $Tree->withData($dataList['data'])->buildArray($tree);
        } else {
            $list = $dataList['data'];
        }
        
        // 总数
        $total = $data->total();
        
        return [
            'list' => $list, 
            'total' => $total,
        ];
    }
    
    /**
     * 导航详情
     */
    public static function getNavbarInfo($tag = [])
    {
        if (! isset($tag['navbarid'])) {
            return [];
        }
        
        $id = $tag['navbarid'];
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = NavbarModel::field($field)
            ->where([
                'id' => $id,
            ])
            ->where($map)
            ->where($condition)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 添加阅读量
        if (! empty($viewinc)) {
            NavbarModel::where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
        return $info;
    }
    
    /**
     * 栏目列表
     */
    public static function getCateList($tag = [])
    {
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 缓存
        $cache = !isset($tag['cache']) ? false : (int) $tag['cache'];
        
        // 是否使用树结构
        $tree = isset($tag['tree']) ? intval($tag['tree']) : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = CategoryModel::with(['model'])
            ->field($field)
            ->where($map)
            ->where($condition)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        if (! empty($tree)) {
            $Tree = new Tree();
            $list = $Tree->withData($dataList['data'])->buildArray($tree);
        } else {
            $list = $dataList['data'];
        }
        
        // 总数
        $total = $data->total();
        
        return [
            'list' => $list, 
            'total' => $total,
        ];
    }
    
    /**
     * 栏目详情
     */
    public static function getCateInfo($tag = [])
    {
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $name = isset($tag['name']) ? $tag['name'] : '';
        
        // 栏目名称
        $title = isset($tag['title']) ? $tag['title'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($id) && empty($name) && empty($title)) {
            return [];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = CategoryModel::field($field)
            ->where(function($query) use($cateid, $name, $title) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $name,
                    ])
                    ->whereOr([
                        'title' => $title,
                    ]);
            })
            ->where($map)
            ->where($condition)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 添加阅读量
        if (! empty($viewinc)) {
            CategoryModel::where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
        return $info;
    }
    
    /**
     * 栏目内容列表
     */
    public static function getCateContentList($tag = [])
    {
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [
                'cate' => [], 
                'list' => [], 
                'total' => 0,
            ];
        }
        
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // flag：key:value/key2:value2
        $flag = empty($tag['flag']) ? '' : $tag['flag'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 包含子级
        $inchildren = isset($tag['inchildren']) ? $tag['inchildren'] : '';
        
        // 缓存
        $cache = !isset($tag['cache']) ? false : (int) $tag['cache'];
        
        // 格式化条件
        $condition = static::formatFlag(explode('/', $flag), $condition);
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [
                'cate' => [], 
                'list' => [], 
                'total' => 0,
            ];
        }
        
        $cate = $cate->toArray();
        
        $map = [
            ['status', '=', 1],
        ];
        
        // 设置子级条件
        $whereOr = [];
        if (! empty($inchildren)) {
            if ($inchildren == 'inchildren' 
                && $cate['is_inchildren'] == 1
            ) {
                $childrenIds = CategoryModel::getChildrenIds($cate['id']);
                if (! empty($childrenIds)) {
                    $whereOr[] = ['categoryid', 'in', implode(',', $childrenIds)];
                }
            } else {
                if (! empty($childrenIds)) {
                    $whereOr[] = ['categoryid', 'in', $inchildren];
                }
            }
        }
        
        // 分页数量重置
        if ($limit == 'auto') {
            $limit = $cate['pagesize'];
        }
        
        $data = ContentModel::newTable($cate['model']['tablename'])
            ->with(['cate'])
            ->field($field)
            ->where(function($query) use($cate, $whereOr) {
                $query->where([
                        ['categoryid', '=', $cate['id']],
                    ])
                    ->whereOr($whereOr);
            })
            ->where($map)
            ->where($condition)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        $list = $dataList['data'];
        
        // 总数
        $total = $data->total();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        foreach ($list as $key => $value) {
            $list[$key] = ContentModel::formatShowFields($modelField, $value);
        }
        
        return [
            'cate' => $cate, 
            'list' => $list, 
            'total' => $total,
        ];
    }
    
    /**
     * 栏目内容详情
     */
    public static function getCateContentInfo($tag = [])
    {
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($contentid) || (empty($cateid) && empty($catename))) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $cate = $cate->toArray();
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                'id' => $contentid,
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->where([
                ['status', '=', 1],
            ])
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        $viewField = '';
        foreach ($modelField as $field) {
            if ($field['is_view'] == 1) {
                $viewField = $field['name'];
                break;
            }
        }
        
        // 添加阅读量
        if (! empty($viewinc) && ! empty($viewField)) {
            ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'id' => $info['id'],
                ])
                ->inc($viewField, 1)
                ->update();
        }
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 栏目内容上一条
     */
    public static function getCateContentPrevInfo($tag = [])
    {
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($contentid) || (empty($cateid) && empty($catename))) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['id', '<', $contentid],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->where($map)
            ->order('id DESC')
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 栏目内容下一条
     */
    public static function getCateContentNextInfo($tag = [])
    {
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($contentid) || (empty($cateid) && empty($catename))) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['id', '>', $contentid],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->where($map)
            ->order('id ASC')
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 栏目单页详情
     */
    public static function getCatePageInfo($tag = [])
    {
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 2,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->where([
                ['status', '=', 1],
            ])
            ->order('id DESC')
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        // 添加阅读量
        if (! empty($viewinc)) {
            ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 模型内容列表
     */
    public static function getModelContentList($tag = [])
    {
        // 模型表
        $table = isset($tag['table']) ? $tag['table'] : '';
        
        if (empty($table)) {
            return [];
        }
        
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // flag：key:value/key2:value2
        $flag = empty($tag['flag']) ? '' : $tag['flag'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 缓存
        $cache = !isset($tag['cache']) ? false : (int) $tag['cache'];
        
        // 格式化条件
        $condition = static::formatFlag(explode('/', $flag), $condition);
        
        $data = ContentModel::newTable($table)
            ->field($field)
            ->where([
                ['status', '=', 1],
            ])
            ->where($condition)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        foreach ($data as $key => $value) {
            $data->{$key} = ContentModel::formatShowFields($modelField, $value);
        }
        
        return $data;
    }
    
    /**
     * 模型内容详情
     */
    public static function getModelContentInfo($tag = [])
    {
        // 模型表
        $table = isset($tag['table']) ? $tag['table'] : '';
        
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($contentid) || empty($table)) {
            return [];
        }
        
        $info = ContentModel::newTable($table)
            ->field($field)
            ->where([
                'id' => $contentid,
            ])
            ->where($condition)
            ->where([
                ['status', '=', 1],
            ])
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        // 添加阅读量
        if (! empty($viewinc)) {
            ContentModel::newTable($table)
                ->where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
        return $info;
    }
    
    /**
     * 标签列表
     */
    public static function getTagList($tag = [])
    {
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 缓存
        $cache = !isset($tag['cache']) ? false : (int) $tag['cache'];
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = TagsModel::where($map)
            ->where($condition)
            ->field($field)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        $list = $dataList['data'];
        
        // 总数
        $total = $data->total();
        
        // 分页
        $pageHtml = $data->render();
        
        return [
            'list' => $list, 
            'total' => $total,
            'page' => $pageHtml,
        ];
    }
    
    /**
     * 标签详情
     */
    public static function getTagInfo($tag = [])
    {
        // 名称
        $name = isset($tag['name']) ? $tag['name'] : '';
        
        // 标题
        $title = isset($tag['title']) ? $tag['title'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量字段，为空不启用
        $viewinc = isset($tag['viewinc']) ? intval($tag['viewinc']) : '';
        
        if (empty($name) && empty($title)) {
            return [];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = TagsModel::field($field)
            ->where(function($query) use($name, $title) {
                $query->where([
                        'title' => $title,
                    ]);
            })
            ->where($map)
            ->where($condition)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 添加阅读量
        if (! empty($viewinc)) {
            TagsModel::where($map)
                ->where([
                    'title' => $title,
                ])
                ->inc('views', 1)
                ->update();
        }
        
        return $info;
    }
    
    /**
     * 设置
     */
    public static function getSetting($tag = [])
    {
        if (! isset($tag['name'])) {
            return '';
        }
        
        if (! isset($tag['default'])) {
            $tag['default'] = '';
        }
        
        return SettingsModel::config($tag['name'], $tag['default']);
    }

    /**
     * 内容页分页HTML
     */
    public static function getPagerHtml($tag)
    {
        // 链接
        $url = isset($tag['url']) ? $tag['url'] : '';
        
        // 当前页
        $page = isset($tag['page']) ? intval($tag['page']) : '';
        
        // 总数
        $total = isset($tag['total']) ? intval($tag['total']) : '';
        
        // url的query. e.g: k=v&k1=v1&k2=v2
        $query = isset($tag['query']) ? $tag['query'] : '';
        
        // 简单列表
        $simple = isset($tag['simple']) ? true : false;
        
        if ($total <= 1) {
            return '';
        }
        
        // 编码链接
        $url = urldecode($url);
        
        // 解析地址参数
        parse_str($query, $newQuery);
        
        $result = new PaginatorPage([], 10, $page, $total, $simple, [
            'path' => $url, 
            'query' => $newQuery,
            'simple' => $simple,
        ]);
        
        return "<div class='pager laketcms-page'>" . $result->render() . "</div>";
    }
    
    /**
     * 格式化flag
     */
    protected static function formatFlag($flag = '', $condition = '')
    {
        // flag：key:value,valuee
        
        if (empty($flag)) {
            return $condition;
        }
        
        if (is_array($flag)) {
            $result = '';
            foreach ($flag as $v) {
                $result .= static::formatFlag($v, $condition);
            }
            
            return $result;
        }
        
        list ($key, $flag) = explode(':', $flag, 2);
        
        if (empty($key) || empty($flag)) {
            return $condition;
        }
        
        if (stripos($flag, '&') !== false) {
            $arr = [];
            foreach (explode('&', $flag) as $k => $v) {
                $arr[] = "FIND_IN_SET('{$v}', {$key})";
            }
            if ($arr) {
                $condition .= "(" . implode(' AND ', $arr) . ")";
            }
        } else {
            $condition .= ($condition ? ' AND ' : '');
            $arr = [];
            foreach (explode(',', str_replace('|', ',', $flag)) as $k => $v) {
                $arr[] = "FIND_IN_SET('{$v}', {$key})";
            }
            if ($arr) {
                $condition .= "(" . implode(' OR ', $arr) . ")";
            }
        }
        
        return $condition;
    }

}
