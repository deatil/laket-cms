<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\CMS;

use Laket\Admin\CMS\Service\Template;
use Laket\Admin\CMS\Template\Model as TemplateModel;
use Laket\Admin\CMS\Model\Settings as SettingsModel;

/**
 * 标签
 *
 * @create 2024-6-12
 * @author deatil
 */
class Tag extends Base
{
    /**
     * 框架构造函数
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 列表
     * /cms/tag
     */
    public function index()
    {
        // 功能开启检测
        $openTag = SettingsModel::config('web_site_tag', 0);
        if ($openTag != 1) {
            return $this->error('页面不能存在！');
        }

        // 页码
        $page = $this->request->param('page/d', 1);
        
        // 排序
        $sort = $this->request->param('sort', 'hot');
        if ($sort == 'time') {
            $order = 'add_time DESC, id DESC';
        } elseif ($sort == 'hot') {
            $order = 'views DESC, id DESC';
        } else {
            $order = 'add_time DESC, id DESC';
        }
        
        $limit = 20;
        
        // 内容
        $data = TemplateModel::getTagList([
            'page' => $page,
            'limit' => $limit,
            'order' => $order,
        ]);

        $pageHtml = TemplateModel::getPagerHtml([
            'url' => $this->request->baseUrl(),
            'page' => $page,
            'total' => $data['total'],
        ]);
        
        $this->assign([
            'list' => $data['list'],
            'total' => $data['total'],
            'page' => $pageHtml,
        ]);
        
        // SEO信息
        $this->setMetaTitle('标签');
        $this->setMetaKeywords('标签,标签列表');
        $this->setMetaDescription('标签');
        
        return $this->fetchTheme('tag.html');
    }
    
    /**
     * 详情
     * /cms/tag/测试
     */
    public function detail()
    {
        // 功能开启检测
        $openTag = SettingsModel::config('web_site_tag', 0);
        if ($openTag != 1) {
            return $this->error('页面不能存在！');
        }

        // 名称
        $title = $this->request->param('title/s');
        
        // 内容
        $data = TemplateModel::getTagInfo([
            'title' => $title,
            'viewinc' => 1,
        ]);
        if (empty($data)) {
            return $this->error($title . '标签不存在', '');
        }
        
        $this->assign([
            'info' => $data,
        ]);
        
        // SEO信息
        $this->setMetaTitle($data['title'] . ' - 标签');
        $this->setMetaKeywords($data['keywords']);
        $this->setMetaDescription($data['description']);
        
        return $this->fetchTheme('tag_detail.html');
    }
}
