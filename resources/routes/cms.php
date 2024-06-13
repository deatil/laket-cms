<?php

use think\facade\Route;

use Laket\Admin\Facade\Flash;
use Laket\Admin\CMS\Controller\CMS as CMSController;

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

