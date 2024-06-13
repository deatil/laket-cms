<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use Lake\Admin\Model\FieldType as FieldTypeModel;

use Laket\Admin\CMS\Service\Datatable;
use Laket\Admin\CMS\Service\Model as ModelService;
use Laket\Admin\CMS\Model\Model as ModelModel;
use Laket\Admin\CMS\Model\ModelField as ModelFieldModel;

/**
 * 模型字段
 *
 * @create 2024-6-12
 * @author deatil
 */
class Field extends Base 
{    
    /**
     * 列表
     */
    public function index() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $modelid = $this->request->param('modelid', 0);
            $query = ModelFieldModel::where([
                    'modelid' => $modelid,
                ])
                ->where($map);
            $queryCount = clone $query;
            
            $data = $query
                ->order("sort desc, add_time desc")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = $queryCount->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        } else {
            $modelid = $this->request->param('modelid');
            
            // 模型详情
            $model = ModelModel::where([
                'id' => $modelid,
            ])->find();

            $data = [
                'modelid' => $modelid,
                'model' => $model,
            ];
            $this->assign($data);
            
            return $this->fetch("laket-cms::field.index");
        }
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\ModelField.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = ModelFieldModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            // 模型详情
            $model = ModelModel::where([
                'id' => $data['modelid'],
            ])->find();
            
            try {
                // 添加字段
                $modelService = ModelService::create();
                $modelService->createField($model['tablename'], $data);
            } catch(\Exception $e) {
                ModelFieldModel::where([
                        'id' => $result['id'],
                    ])
                    ->delete();
                
                return $this->error($e->getMessage());
            }
            
            return $this->success('添加成功！');
        } else {
            $modelid = $this->request->param('modelid');
            $this->assign("modelid", $modelid);
            
            $fieldType = config('cms_field', []);
            $this->assign("fieldType", $fieldType);
            
            return $this->fetch("laket-cms::field.add");
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\ModelField.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误！');
            }
            
            $info = ModelFieldModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('字段不存在！');
            }
            
            $result = ModelFieldModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 模型详情
            $model = ModelModel::where([
                'id' => $data['modelid'],
            ])->find();
            
            try {
                // 更新字段
                $modelService = ModelService::create();
                $data['oldname'] = $info['name'];
                $modelService->changeField($model['tablename'], $data);
            } catch(\Exception $e) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = ModelFieldModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            $fieldType = config('cms_field', []);
            $this->assign("fieldType", $fieldType);
            
            return $this->fetch("laket-cms::field.edit");
        }
    }

    /**
     * 删除
     */
    public function delete() 
    {
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $data = ModelFieldModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $result = ModelFieldModel::where([
            'id' => $id,
        ])->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        // 模型详情
        $model = ModelModel::where([
            'id' => $data['modelid'],
        ])->find();
        
        // 删除字段
        $modelService = ModelService::create();
        $modelService->deleteField($model['tablename'], $data['name']);
        
        return $this->success('删除成功！');
    }
    
    /**
     * 修改状态
     */
    public function state() 
    {
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $status = input('status', '0', 'trim,intval');

        $result = ModelFieldModel::where([
                'id' => $id,
            ])
            ->update([
                'status' => $status,
            ]);
        if (false === $result) {
            return $this->error("设置失败！");
        }
        
        return $this->success("设置成功！");
    } 

    /**
     * 排序
     */
    public function sort()
    {
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $sort = $this->request->param('value/d', 100);
        
        $result = ModelFieldModel::where([
            'id' => $id,
        ])->update([
            'sort' => $sort,
        ]);
        
        if (false === $result) {
            return $this->error("排序失败！");
        }
        
        return $this->success("排序成功！");
    }
    
}