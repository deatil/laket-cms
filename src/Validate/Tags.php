<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Validate;

use think\Validate;

/**
 * 标签
 *
 * @create 2024-6-12
 * @author deatil
 */
class Tags extends Validate
{
    // 定义验证规则
    protected $rule = [
        'title|标签标题' => 'require|chsAlphaNum|max:200',
    ];
    
    // 定义验证提示
    protected $message = [
        'title.require' => '标签标题不得为空',
        'title.chsAlphaNum' => '标签标题只能为只能是汉字、字母、数字和下划线',
        'title.max' => '标签标题最大长度为200',
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
