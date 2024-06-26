<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use Laket\Admin\CMS\Service\Model as ModelService;
use Laket\Admin\CMS\Model\Model as ModelModel;
use Laket\Admin\CMS\Model\ModelField as ModelFieldModel;

/**
 * 模型
 *
 * @create 2024-6-12
 * @author deatil
 */
class Model extends Base 
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
            
            $data = ModelModel::where($map)
                ->order("sort DESC, id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = ModelModel::where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        } else {
        return $this->fetch("laket-cms::model.index");
        }
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\Model.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = ModelModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            // 创建表
            try {
                $modelService = ModelService::create();
                $modelService->createTable($data['tablename'], $data['comment']);
                $this->setDefaultField($modelService, $result);
            } catch(\Exception $e) {
                // 删除模型
                ModelModel::where([
                    'id' => $result['id'],
                ])->delete();
                
                // 删除表字段
                ModelFieldModel::where([
                    'modelid' => $result['id'],
                ])->delete();
                
                // 删除表
                ModelService::create()->deleteTable($result['tablename']);
                
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        } else {
            return $this->fetch("laket-cms::model.add");
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\Model.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = ModelModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('数据不存在');
            }
            
            $data = request()->post();
            
            $result = ModelModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 修改表名
            if ($info['tablename'] != $data['tablename']) {
                ModelService::create()->updateTableName($info['tablename'], $data['tablename']);
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = ModelModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            return $this->fetch("laket-cms::model.edit");
        }
    }

    /**
     * 删除
     */
    public function delete() 
    {
        if (! request()->isPost()) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $data = ModelModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $result = ModelModel::where([
            'id' => $id,
        ])->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        // 删除表字段
        $result = ModelFieldModel::where([
            'modelid' => $id,
        ])->delete();
        
        // 删除表
        ModelService::create()->deleteTable($data['tablename']);
        
        return $this->success('删除成功！');
    }
    
    /**
     * 修改状态
     */
    public function state() 
    {
        if (! request()->isPost()) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $status = input('status', '0', 'trim,intval');

        $result = ModelModel::where([
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
        if (! request()->isPost()) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $sort = $this->request->param('value/d', 100);
        
        $result = ModelModel::where([
            'id' => $id,
        ])->update([
            'sort' => $sort,
        ]);
        
        if (false === $result) {
            return $this->error("排序失败！");
        }
        
        return $this->success("排序成功！");
    }
    
    /**
     * 添加默认字段
     */
    protected function setDefaultField($modelService, $data) 
    {
        $attrs = [
            [
                'name' => 'categoryid', 
                'title' => '所属栏目', 
                'type' => 'number', 
                'length' => 10, 
                'options' => "", 
                'value' => '',
                'remark' => '所属栏目ID，与栏目关联', 
                'show_type' => 4, 
                'is_must' => 1, 
            ],
            [
                'name' => 'status', 
                'title' => '状态', 
                'type' => 'switch', 
                'length' => 1, 
                'after' => 'categoryid', 
                'options' => "", 
                'value' => '1',
                'remark' => '状态', 
                'show_type' => 4, 
                'is_must' => 1, 
            ],
            [
                'name' => 'edit_time', 
                'title' => '更新时间', 
                'type' => 'datetime', 
                'length' => 10, 
                'after' => 'status', 
                'extra' => '', 
                'remark' => '更新时间', 
                'show_type' => 4, 
                'is_must' => 0, 
                'value'=> '0',
            ],
            [
                'name' => 'edit_ip', 
                'title' => '更新IP', 
                'type' => 'text', 
                'length' => 50, 
                'after' => 'edit_time', 
                'extra' => '', 
                'remark' => '更新IP', 
                'show_type' => 4, 
                'is_must' => 0, 
                'value'=> '',
            ],
            'add_time' => [
                'name' => 'add_time', 
                'title' => '添加时间', 
                'type' => 'datetime', 
                'length' => 10, 
                'after' => 'edit_ip', 
                'extra' => '', 
                'remark' => '添加时间', 
                'show_type' => 4, 
                'is_must' => 0, 
                'value'=> '0',
            ],
            'add_ip' => [
                'name' => 'add_ip', 
                'title' => '添加IP', 
                'type' => 'text', 
                'length' => 50, 
                'after' => 'add_time', 
                'extra' => '', 
                'remark' => '添加IP', 
                'show_type' => 4, 
                'is_must' => 0, 
                'value'=> '',
            ],
        ];
        
        foreach ($attrs as $attr) {
            $attr['modelid'] = $data->id;
            $modelService->createField($data->tablename, $attr);
            
            // 添加字段管理
            ModelFieldModel::create($attr);
        }
    }
    
}