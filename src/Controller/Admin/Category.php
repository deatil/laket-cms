<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\Admin;

use Laket\Admin\Support\Tree;

use Laket\Admin\CMS\Validate;
use Laket\Admin\CMS\Service\Template;
use Laket\Admin\CMS\Service\ModelTemplate;
use Laket\Admin\CMS\Model\Model as ModelModel;
use Laket\Admin\CMS\Model\Category as CategoryModel;

/**
 * 栏目
 *
 * @create 2024-6-12
 * @author deatil
 */
class Category extends Base 
{    
    /**
     * 列表
     */
    public function index() 
    {
        if ($this->request->isAjax()) {
            $result = CategoryModel::with(['model'])
                ->order([
                    'sort' => 'DESC', 
                    'id' => 'DESC',
                ])
                ->select()
                ->toArray();
            foreach ($result as $key => $item) {
                if ($item['type'] == 1) {
                    $result[$key]['url'] = cms_cate_url($item['name']);
                } else {
                    $result[$key]['url'] = cms_page_url($item['name']);
                }
            }
            
            $Tree = new Tree();
            $resultTree = $Tree->withData($result)->buildArray(0);
            $list = $Tree->buildFormatList($resultTree, 'title');
            $total = count($list);

            return json([
                "code" => 0, 
                "count" => $total, 
                "data" => $list
            ]);
        }
        
        return $this->fetch("laket-cms::category.index");
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
            
            $data = CategoryModel::with(['model'])
                ->where($map)
                ->order([
                    'sort' => 'DESC', 
                    'id' => 'DESC',
                ])
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = CategoryModel::where($map)
                ->count();
            foreach ($data as $key => $item) {
                if ($item['type'] == 1) {
                    $data[$key]['url'] = cms_cate_url($item['name']);
                } else {
                    $data[$key]['url'] = cms_page_url($item['name']);
                }
            }

            return json([
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ]);
        }
        
        return $this->fetch("laket-cms::category.all");
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, Validate\Category::class . '.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = CategoryModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        } else {
            $parentid = $this->request->param('parentid', 0);
            
            $parents = CategoryModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            $parenTree = $Tree->withData($parents)->buildArray(0);
            $parents = $Tree->buildFormatList($parenTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("parents", $parents);
            
            $models = ModelModel::where([
                    ['status', '=', 1],
                ])
                ->order([
                    'sort', 
                    'id' => 'ASC',
                ])
                ->select()
                ->toArray();
            $this->assign("models", $models);
            
            return $this->fetch("laket-cms::category.add");
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, Validate\Category::class . '.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误！');
            }
            
            $info = CategoryModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('表单不存在！');
            }
            
            $result = CategoryModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = CategoryModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            $parentid = $info['parentid'];
            
            $parents = CategoryModel::order([
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
            
            $models = ModelModel::where([
                    ['status', '=', 1],
                ])
                ->order([
                    'sort', 
                    'id' => 'ASC',
                ])
                ->select()
                ->toArray();
            $this->assign("models", $models);
            
            return $this->fetch("laket-cms::category.edit");
        }
    }

    /**
     * 设置
     */
    public function setting() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = CategoryModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('表单不存在');
            }
            
            $result = CategoryModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('设置失败！');
            }
            
            return $this->success('设置成功！');
        } else {
            $id = request()->get('id');
            
            $info = CategoryModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            // 模版列表
            $themePath = Template::themePath();
            
            $modelTemplate = (new ModelTemplate)->withPath($themePath);
            $lists = $modelTemplate->lists();
            $details = $modelTemplate->details();
            $pages = $modelTemplate->pages();
            
            $this->assign("template", [
                'lists' => $lists,
                'details' => $details,
                'pages' => $pages,
            ]);
            
            return $this->fetch("laket-cms::category.setting");
        }
    }

    /**
     * 删除
     */
    public function delete() 
    {
        $id = request()->param('id');
        if (! $id) {
            return $this->error("参数错误！");
        }
        
        $data = CategoryModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $children = CategoryModel::where([
            'parentid' => $id,
        ])->count();
        if ($children > 0) {
            return $this->error('当前分类数据还有子分类，暂不能删除！');
        }
        
        $result = CategoryModel::where([
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
            return $this->error("参数错误！");
        }
        
        $status = input('status', '0', 'trim,intval');

        $result = CategoryModel::where([
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
            return $this->error("参数错误！");
        }
        
        $sort = $this->request->param('value/d', 100);
        
        $result = CategoryModel::where([
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