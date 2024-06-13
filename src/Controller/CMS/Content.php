<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\CMS;

use think\helper\Arr;

use Laket\Admin\CMS\Service\Template;
use Laket\Admin\CMS\Template\Model as TemplateModel;

/**
 * 内容详情
 *
 * @create 2024-6-12
 * @author deatil
 */
class Content extends Base
{
    /**
     * 详情
     */
    public function index()
    {
        // 栏目ID
        $cateid = $this->request->param('cateid/d', '');
        
        // 栏目标识
        $catename = $this->request->param('catename/s', '');
        
        // 内容ID
        $contentid = $this->request->param('id/d', 0);
        
        // 内容
        $data = TemplateModel::getCateContentInfo([
            'cateid' => $cateid,
            'catename' => $catename,
            'contentid' => $contentid,
            'viewinc' => true,
        ]);
        if (empty($data)) {
            return $this->error('信息不存在');
        }
        
        // 栏目
        $cate = Arr::only($data['cate'], [
            'id', 'name', 'title', 
            'keywords', 'description', 
            'cover', 'template_detail'
        ]);
        
        // 内容
        $info = $data['info'];
        
        $this->assign([
            'cate' => $cate,
            'info' => $info,
        ]);
        if (empty($info)) {
            return $this->error('信息不存在');
        }
        
        // SEO信息
        $this->setMetaTitle($info['title'] . ' - ' . $cate['title']);
        $this->setMetaKeywords($info['keywords']);
        $this->setMetaDescription($info['description']);
        
        // 模版
        $viewFile = Template::themePath($cate['template_detail']);
        
        return $this->fetch($viewFile);
    }
}
