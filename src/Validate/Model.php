<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Validate;

use think\Validate;

/**
 * 模型
 *
 * @create 2024-6-12
 * @author deatil
 */
class Model extends Validate
{
    // 定义验证规则
    protected $rule = [
        'title|模型名称' => 'require|chsAlphaNum|max:30',
        'tablename|表键名' => 'require|max:20|unique:cms_model|regex:/^[a-zA-Z][A-Za-z0-9\_]+$/',
        'status' => 'in:0,1',
    ];
    
    // 定义验证提示
    protected $message = [
        'title.require' => '模型名称不得为空',
        'title.chsAlphaNum' => '模型名称只能为只能是汉字、字母、数字和下划线',
        'title.max' => '模型名称最大长度为30',
        'tablename.require' => '表键名不得为空',
        'tablename.unique' => '表键名已经存在',
        'tablename.regex' => '表键名只能为字母和数字，并且仅能字母开头',
        'status.in' => '模型状态格式错误',
    ];
    
    protected $scene = [
        'add' => [
            'title',
            'tablename',
            'status',
        ],
        'edit' => [
            'title',
            'tablename',
            'status',
        ],
    ];
}
