<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Validate;

use think\Validate;

/**
 * 导航
 *
 * @create 2024-6-12
 * @author deatil
 */
class Navbar extends Validate
{
    //定义验证规则
    protected $rule = [
        'title|栏目标题' => 'require',
    ];

    //定义验证提示
    protected $message = [
        'title.require' => '导航标题不能为空',
    ];

    protected $scene = [
        'add' => [
            'title',
        ],
        'edit' => [
            'title',
        ],
    ];
}
