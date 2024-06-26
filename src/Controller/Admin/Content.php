<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use think\helper\Arr;

use Laket\Admin\Support\Tree;

use Laket\Admin\CMS\Support\Validate;
use Laket\Admin\CMS\Model\Category as CategoryModel;
use Laket\Admin\CMS\Model\Model as ModelModel;
use Laket\Admin\CMS\Model\ModelField as ModelFieldModel;
use Laket\Admin\CMS\Model\Content as ContentModel;

/**
 * 内容
 *
 * @create 2024-6-12
 * @author deatil
 */
class Content extends Base 
{    
    /**
     * 列表
     */
    public function index() 
    {
        $cate = CategoryModel::where([
                ['status', '=', 1], 
            ])
            ->order("sort DESC, id DESC")
            ->select()
            ->toArray();
        
        $newCategory = [];
        foreach ($cate as $cate) {
            $data = [
                'id' => $cate['id'],
                'parentid' => $cate['parentid'],
                'title' => $cate['title'],
                'type' => $cate['type'],
                'field' => 'id',
                'spread' => true,
            ];
            $newCategory[] = $data;
        }
        
        $newCategory = (new Tree)
            ->withConfig('buildChildKey', 'children')
            ->withData($newCategory)
            ->buildArray(0);

        $this->assign("category", $newCategory);
        
        return $this->fetch("laket-cms::content.index");
    }

    /**
     * 内容首页
     */
    public function main() 
    {
        // 单页数量
        $pages = CategoryModel::where([
                'type' => 2,
                'status' => 1,
            ])
            ->count();
        $this->assign("pages", $pages);
        
        // 列表数量
        $cates = CategoryModel::with(['model'])
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        
        $newCates = [];
        foreach ($cates as $cate) {
            $cate['count'] = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'categoryid' => $cate['id'],
                    'status' => 1,
                ])
                ->count();
            $newCates[] = $cate;
        }
        $this->assign("cates", $newCates);
        
        return $this->fetch("laket-cms::content.main");
    }

    /**
     * 单页
     */
    public function page() 
    {
        if (request()->isPost()) {
            $data = $this->request->post();
            
            $id = $data['id'];
            if (empty($id)) {
                $this->error("信息ID不能为空！");
            }
            unset($data['id']);
            
            $cateid = request()->param('cateid');
            if (empty($cateid)) {
                $this->error("请指定栏目ID！");
            }
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 2,
                    'status' => 1,
                ])
                ->find();
            if (empty($cate)) {
                $this->error('该栏目不存在！');
            }
            
            $validateFields = ContentModel::validateFields([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ], 1);
            
            // 验证
            $validate = (new Validate())
                ->withRules(Arr::get($validateFields, 'rule', []))
                ->withMessages(Arr::get($validateFields, 'message', []))
                ->withScenes(Arr::get($validateFields, 'scene', []))
                ->scene('update');
            
            $model = ModelModel::where([
                    'id' => $cate['model']['id'],
                    'status' => 1,
                ])->find();
            $fields = $model['fields'];
            $data['modelField'] = ContentModel::formatFormFieldsInsert($fields, $data['modelField']);
            
            $result = $this->validate($data['modelField'], $validate, []);
            if (true !== $result) {
                return $this->error($result);
            }
            
            $table = $cate['model']['tablename'];
            $data = $data['modelField'];
            $where = [
                ['id', '=', $id],
            ];
            
            $result = ContentModel::newUpdate($table, $data, $where);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 关联标签
            if (isset($data['modelField'])) {
                $tags = ContentModel::formatFormFieldTags($fields, $data['modelField']);
            
                foreach ($tags as $tag) {
                    ContentModel::updateTagsContent($tag, $cate['model']['id'], $cateid, $id);
                }
            }
            
            return $this->success('修改成功！');
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 2,
                    'status' => 1,
                ])
                ->find();
            
            $info = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'categoryid' => $cateid,
                ])
                ->order('id ASC')
                ->find();
            if (empty($info)) {
                $createData['categoryid'] = $cateid;
                ContentModel::newCreate($cate['model']['tablename'], $createData);
            }
            $this->assign("info", $info);
            
            $modelField = ContentModel::formFields([
                    'id' => $cate['modelid'],
                    'status' => 1,
                ], 1);
            $modelField = ContentModel::formatFormFieldsShow($modelField, $info);
            $this->assign("fieldList", $modelField);
            
            return $this->fetch("laket-cms::content.page");
        }
    }

    /**
     * 列表
     */
    public function cate() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $keyword = $this->request->param('keyword/s', '', 'trim');
            
            $cateid = $this->request->param('cateid', 0);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            
            $modelField = ModelFieldModel::where([
                    'modelid' => $cate['model']['id'],
                    'status' => 1,
                ])
                ->order('sort ASC, id ASC')
                ->select()
                ->toArray();
            foreach ($modelField as $field) {
                if ($field['is_filter'] == 1) {
                    $map[] = [$field['name'], 'like', "%$keyword%"];
                }
            }
            
            $query = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    ['categoryid', '=', $cate['id']],
                ])
                ->where($map);
            $queryCount = clone $query;
            $data = $query->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = $queryCount->count();
            
            foreach ($data as $key => $item) {
                $data[$key]['url'] = cms_content_url($cate['name'], $item['id']);
            }

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            
            return json($result);
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            $this->assign("cate", $cate);
            
            return $this->fetch("laket-cms::content.cate");
        }
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = $this->request->post();
            
            $cateid = $this->request->param('cateid', 0);
            if (empty($cateid)) {
                $this->error("请指定栏目ID！");
            }
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            if (empty($cate)) {
                $this->error('该栏目不存在！');
            }
            
            $validateFields = ContentModel::validateFields([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ], 2);
            
            // 验证
            $validate = (new Validate())
                ->withRules(Arr::get($validateFields, 'rule', []))
                ->withMessages(Arr::get($validateFields, 'message', []))
                ->withScenes(Arr::get($validateFields, 'scene', []))
                ->scene('create');
            
            $model = ModelModel::where([
                    'id' => $cate['model']['id'],
                    'status' => 1,
                ])->find();
            $fields = $model['fields'];
            $data['modelField'] = ContentModel::formatFormFieldsInsert($fields, $data['modelField']);
            
            $result = $this->validate($data['modelField'], $validate, []);
            if (true !== $result) {
                return $this->error($result);
            }
            
            $data['modelField']['categoryid'] = $cateid;
            $result = ContentModel::newCreate($cate['model']['tablename'], $data['modelField']);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            // 关联标签
            if (isset($data['modelField'])) {
                $tags = ContentModel::formatFormFieldTags($fields, $data['modelField']);
            
                foreach ($tags as $tag) {
                    ContentModel::updateTagsContent($tag, $cate['model']['id'], $cateid, $result->id);
                }
            }
            
            return $this->success('添加成功！');
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            
            $modelField = ContentModel::formFields([
                    'id' => $cate['modelid'],
                    'status' => 1,
                ], 2);
            $modelField = ContentModel::formatFormFieldsShow($modelField, []);
            $this->assign("fieldList", $modelField);
            
            return $this->fetch("laket-cms::content.add");
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = $this->request->post();
            
            $id = request()->param('id');
            if (empty($id)) {
                $this->error("信息ID不能为空！");
            }
            
            $cateid = request()->param('cateid');
            if (empty($cateid)) {
                $this->error("请指定栏目ID！");
            }
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            if (empty($cate)) {
                $this->error('该栏目不存在！');
            }
            
            $validateFields = ContentModel::validateFields([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ], 3);
            
            // 验证
            $validate = (new Validate())
                ->withRules(Arr::get($validateFields, 'rule', []))
                ->withMessages(Arr::get($validateFields, 'message', []))
                ->withScenes(Arr::get($validateFields, 'scene', []))
                ->scene('update');
            
            $model = ModelModel::where([
                    'id' => $cate['model']['id'],
                    'status' => 1,
                ])->find();
            $fields = collect($model['fields'])->toArray();
            $data['modelField'] = ContentModel::formatFormFieldsInsert($fields, $data['modelField']);
            
            $result = $this->validate($data['modelField'], $validate, []);
            if (true !== $result) {
                return $this->error($result);
            }
            
            $table = $cate['model']['tablename'];
            $data = $data['modelField'];
            $where = [
                ['id', '=', $id],
            ];
            
            $result = ContentModel::newUpdate($table, $data, $where);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 关联标签
            if (isset($data)) {
                $tags = ContentModel::formatFormFieldTags($fields, $data);
            
                foreach ($tags as $tag) {
                    ContentModel::updateTagsContent($tag, $cate['model']['id'], intval($cateid), intval($id));
                }
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->param('id');
            
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            
            $info = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'id' => $id,
                    'categoryid' => $cateid,
                ])
                ->find();
                
            $modelField = ContentModel::formFields([
                    'id' => $cate['modelid'],
                    'status' => 1,
                ], 3);
            $modelField = ContentModel::formatFormFieldsShow($modelField, $info);
            $this->assign("fieldList", $modelField);
            
            return $this->fetch("laket-cms::content.edit");
        }
    }

    /**
     * 删除
     */
    public function delete() 
    {
        $cateid = $this->request->param('cateid', 0);
        if (! $cateid) {
            return $this->error("非法操作！");
        }
        
        $ids = request()->param('ids/a');
        if (! $ids) {
            return $this->error("非法操作！");
        }
        
        $cate = CategoryModel::with(['model'])
            ->where([
                'id' => $cateid,
                'type' => 1,
                'status' => 1,
            ])
            ->find()
            ->toArray();
        
        $result = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['id', 'in', $ids],
                ['categoryid', '=', $cateid],
            ])
            ->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        return $this->success('删除成功！');
    }
    
    /**
     * 修改状态
     */
    public function state() 
    {
        $cateid = $this->request->param('cateid', 0);
        if (! $cateid) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id/d');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $cate = CategoryModel::with(['model'])
            ->where([
                'id' => $cateid,
                'type' => 1,
                'status' => 1,
            ])
            ->find()
            ->toArray();
        
        $status = input('status', '0', 'trim,intval');

        $result = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['id', '=', $id],
                ['categoryid', '=', $cateid],
            ])
            ->update([
                'status' => $status,
            ]);
        if (false === $result) {
            return $this->error("设置失败！");
        }
        
        return $this->success("设置成功！");
    } 
    
}