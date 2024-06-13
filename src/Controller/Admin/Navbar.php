<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use Laket\Admin\Support\Tree;

use Laket\Admin\CMS\Model\Datatable;
use Laket\Admin\CMS\Model\Navbar as NavbarModel;

/**
 * 导航
 *
 * @create 2024-6-12
 * @author deatil
 */
class Navbar extends Base 
{    
    /**
     * 列表
     */
    public function index() 
    {
        if ($this->request->isAjax()) {
            $result = NavbarModel::order([
                    'sort' => 'ASC', 
                    'id' => 'ASC',
                ])
                ->select()
                ->toArray();

            $Tree = new Tree();
            $resultTree = $Tree->withData($result)->buildArray(0);
            $list = $Tree->buildFormatList($resultTree, 'title');
            $total = count($list);
            
            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $list
            ];

            return json($result);
        }
        
        return $this->fetch("laket-cms::navbar.index");
    }

    /**
     * 全部
     */
    public function all() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $data = NavbarModel::where($map)
                ->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = NavbarModel::where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        }

        return $this->fetch("laket-cms::navbar.all");
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\Navbar.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = NavbarModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        } else {
            $parentid = $this->request->param('parentid', 0);
            
            $parents = NavbarModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            $parenTree = $Tree->withData($parents)->buildArray(0);
            $parents = $Tree->buildFormatList($parenTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("parents", $parents);
            
            return $this->fetch("laket-cms::navbar.add");
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, '\\Laket\\Admin\\CMS\\Validate\\Navbar.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = NavbarModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('数据不存在');
            }
            
            $result = NavbarModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = NavbarModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            $parentid = $info['parentid'];
            
            $parents = NavbarModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            
            $childsId = $Tree->getListChildsId($parents, $info['id']);
            $childsId[] = $info['id'];
            
            $newParents = [];
            foreach ($parents as $r) {
                if (in_array($r['id'], $childsId)) {
                    continue;
                }
                
                $newParents[] = $r;
            }
            
            $parenTree = $Tree->withData($newParents)->buildArray(0);
            $parents = $Tree->buildFormatList($parenTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("parents", $parents);
            
            return $this->fetch("laket-cms::navbar.edit");
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
        
        $data = NavbarModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $children = NavbarModel::where([
            'parentid' => $id,
        ])->count();
        if ($children > 0) {
            return $this->error('当前导航数据还有子导航，暂不能删除！');
        }
        
        $result = NavbarModel::where([
            'id' => $id,
        ])->delete();
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
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $status = input('status', '0', 'trim,intval');

        $result = NavbarModel::where([
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
        
        $result = NavbarModel::where([
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