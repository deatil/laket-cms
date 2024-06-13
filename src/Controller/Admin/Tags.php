<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use Laket\Admin\CMS\Model\Tags as TagsModel;
use Laket\Admin\CMS\Model\TagsContent as TagsContentModel;

/**
 * 标签
 *
 * @create 2024-6-12
 * @author deatil
 */
class Tags extends Base 
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
            
            $data = TagsModel::where($map)
                ->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = TagsModel::where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        }

        return $this->fetch("laket-cms::tags.index");
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\Tags.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = TagsModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        }

        return $this->fetch("laket-cms::tags.add");
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\Tags.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = TagsModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('数据不存在');
            }
            
            $result = TagsModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = TagsModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            return $this->fetch("laket-cms::tags.edit");
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
        
        $data = TagsModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $result = TagsModel::where([
            'id' => $id,
        ])->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        // 删除关联数据
        TagsContentModel::where([
            'tagid' => $id,
        ])->delete();
        
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

        $result = TagsModel::where([
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
        
        $result = TagsModel::where([
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