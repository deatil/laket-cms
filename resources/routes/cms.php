<?php

use think\facade\Route;

use Laket\Admin\Facade\Flash;
use Laket\Admin\CMS\Model\Category as CategoryModel;
use Laket\Admin\CMS\Controller\CMS as CMSController;

laket_runhook('cms_route_before');

// 自定义路由
$cates = CategoryModel::getAll();
foreach ($cates as $c) {
    // 列表
    if ($c['type'] == 1) {
        if (! empty($c['content_url'])) {
            Route::get($c['content_url'], CMSController\Content::class . '@index')
                ->append([
                    'catename' => $c['name'],
                ])
                ->name('cms.content-'.$c['name']);
        }

        if (! empty($c['index_url'])) {
            Route::get($c['index_url'], CMSController\Cate::class . '@index')
                ->append([
                    'catename' => $c['name'],
                ])
                ->name('cms.cate-'.$c['name']);
        }
    } else {
        if (! empty($c['index_url'])) {
            Route::get($c['index_url'], CMSController\Page::class . '@index')
                ->append([
                    'catename' => $c['name'],
                ])
                ->name('cms.page-'.$c['name']);
        }
    }
}

Route::group("cms", function() {
    Route::get('/', CMSController\Index::class . '@index')->name('cms.index');
    Route::get('/cate/:catename', CMSController\Cate::class . '@index')->name('cms.cate');
    Route::get('/content/:catename/:id', CMSController\Content::class . '@index')->name('cms.content');
    Route::get('/page/:catename', CMSController\Page::class . '@index')->name('cms.page');
    Route::get('/tag/:title', CMSController\Tag::class . '@detail')->name('cms.tag-detail');
    Route::get('/tag', CMSController\Tag::class . '@index')->name('cms.tag');
    Route::get('/search', CMSController\Search::class . '@index')
        ->name('cms.search')
        ->pattern([
            'keyword' => '\w+', 
            'cateid' => '\d+', 
            'time' => '\w+',
        ]);
});

laket_runhook('cms_route_after');

