<?php

return [
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => 'dispatch_jump.html',
    'dispatch_error_tmpl'   => 'dispatch_jump.html',

    // 标签库
    'taglib_build_in' => [
        '\\Laket\\Admin\\CMS\\Template\\Taglib\\Cms',
    ],
    
    // 前台模板位置
    'template' => root_path('view/laket-cms/template'),
    
    // 前台静态文件位置
    'assets' => public_path('static/laket-cms/template'),
    
    // 前台静态文件预览位置
    'assets_url' => '/static/laket-cms/template',
];
