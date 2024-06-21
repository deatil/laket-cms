<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\CMS;

use Laket\Admin\CMS\Service\Template;

/**
 * CMS首页
 *
 * @create 2024-6-12
 * @author deatil
 */
class Index extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        // SEO信息
        $this->setMetaTitle();
        $this->setMetaKeywords();
        $this->setMetaDescription();
        
        return $this->fetchTheme('index.html');
    }
}
