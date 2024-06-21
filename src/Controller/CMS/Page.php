<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\CMS;

use think\helper\Arr;

use Laket\Admin\CMS\Service\Template;
use Laket\Admin\CMS\Template\Model as TemplateModel;

/**
 * 单页
 *
 * @create 2024-6-12
 * @author deatil
 */
class Page extends Base
{
    /**
     * 详情
     *
     * eg:
     * /cms/page.html?catename=[name]
     */
    public function index()
    {
        // 栏目ID
        $cateid = $this->request->param('cateid/d', '');
        
        // 栏目标识
        $catename = $this->request->param('catename/s', '');
        
        // 内容
        $data = TemplateModel::getCatePageInfo([
            'cateid' => $cateid,
            'catename' => $catename,
            'viewinc' => 'views',
        ]);
        if (empty($data)) {
            return $this->error('信息不存在');
        }
        
        // 分类
        $cate = $data['cate'];
        
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
        $this->setMetaTitle($info['title']);
        $this->setMetaKeywords($info['keywords']);
        $this->setMetaDescription($info['description']);
        
        return $this->fetchTheme($cate['template_page']);
    }
}
