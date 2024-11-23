<?php

use think\facade\Route;

use Laket\Admin\Facade\Flash;
use Laket\Admin\CMS\Controller\Admin as AdminController;

// 插件后台路由
Flash::routes(function() {
    Route::group("cms", function() {
        // 分类
        $categoryController = AdminController\Category::class;
        Route::get('category/index', [$categoryController, 'index'])->name('admin.cms.category-index');
        Route::post('category/index', [$categoryController, 'index'])->name('admin.cms.category-index-data');
        Route::get('category/all', [$categoryController, 'all'])->name('admin.cms.category-all');
        Route::post('category/all', [$categoryController, 'all'])->name('admin.cms.category-all-data');
        Route::get('category/add', [$categoryController, 'add'])->name('admin.cms.category-add');
        Route::post('category/add', [$categoryController, 'add'])->name('admin.cms.category-add-save');
        Route::get('category/edit', [$categoryController, 'edit'])->name('admin.cms.category-edit');
        Route::post('category/edit', [$categoryController, 'edit'])->name('admin.cms.category-edit-save');
        Route::get('category/setting', [$categoryController, 'setting'])->name('admin.cms.category-setting');
        Route::post('category/setting', [$categoryController, 'setting'])->name('admin.cms.category-setting-save');
        Route::post('category/delete', [$categoryController, 'delete'])->name('admin.cms.category-delete');
        Route::post('category/state', [$categoryController, 'state'])->name('admin.cms.category-state');
        Route::post('category/sort', [$categoryController, 'sort'])->name('admin.cms.category-sort');
        
        // 模型
        $modelController = AdminController\Model::class;
        Route::get('model/index', [$modelController, 'index'])->name('admin.cms.model-index');
        Route::post('model/index', [$modelController, 'index'])->name('admin.cms.model-index-data');
        Route::get('model/add', [$modelController, 'add'])->name('admin.cms.model-add');
        Route::post('model/add', [$modelController, 'add'])->name('admin.cms.model-add-save');
        Route::get('model/edit', [$modelController, 'edit'])->name('admin.cms.model-edit');
        Route::post('model/edit', [$modelController, 'edit'])->name('admin.cms.model-edit-save');
        Route::post('model/delete', [$modelController, 'delete'])->name('admin.cms.model-delete');
        Route::post('model/state', [$modelController, 'state'])->name('admin.cms.model-state');
        Route::post('model/sort', [$modelController, 'sort'])->name('admin.cms.model-sort');

        // 模型字段
        $fieldController = AdminController\Field::class;
        Route::get('field/index', [$fieldController, 'index'])->name('admin.cms.field-index');
        Route::post('field/index', [$fieldController, 'index'])->name('admin.cms.field-index-data');
        Route::get('field/add', [$fieldController, 'add'])->name('admin.cms.field-add');
        Route::post('field/add', [$fieldController, 'add'])->name('admin.cms.field-add-save');
        Route::get('field/edit', [$fieldController, 'edit'])->name('admin.cms.field-edit');
        Route::post('field/edit', [$fieldController, 'edit'])->name('admin.cms.field-edit-save');
        Route::post('field/delete', [$fieldController, 'delete'])->name('admin.cms.field-delete');
        Route::post('field/state', [$fieldController, 'state'])->name('admin.cms.field-state');
        Route::post('field/sort', [$fieldController, 'sort'])->name('admin.cms.field-sort');

        // 导航
        $navbarController = AdminController\Navbar::class;
        Route::get('navbar/index', [$navbarController, 'index'])->name('admin.cms.navbar-index');
        Route::post('navbar/index', [$navbarController, 'index'])->name('admin.cms.navbar-index-data');
        Route::get('navbar/all', [$navbarController, 'all'])->name('admin.cms.navbar-all');
        Route::post('navbar/all', [$navbarController, 'all'])->name('admin.cms.navbar-all-data');
        Route::get('navbar/add', [$navbarController, 'add'])->name('admin.cms.navbar-add');
        Route::post('navbar/add', [$navbarController, 'add'])->name('admin.cms.navbar-add-save');
        Route::get('navbar/edit', [$navbarController, 'edit'])->name('admin.cms.navbar-edit');
        Route::post('navbar/edit', [$navbarController, 'edit'])->name('admin.cms.navbar-edit-save');
        Route::post('navbar/delete', [$navbarController, 'delete'])->name('admin.cms.navbar-delete');
        Route::post('navbar/state', [$navbarController, 'state'])->name('admin.cms.navbar-state');
        Route::post('navbar/sort', [$navbarController, 'sort'])->name('admin.cms.navbar-sort');

        // 标签
        $tagsController = AdminController\Tags::class;
        Route::get('tags/index', [$tagsController, 'index'])->name('admin.cms.tags-index');
        Route::post('tags/index', [$tagsController, 'index'])->name('admin.cms.tags-index-data');
        Route::get('tags/add', [$tagsController, 'add'])->name('admin.cms.tags-add');
        Route::post('tags/add', [$tagsController, 'add'])->name('admin.cms.tags-add-save');
        Route::get('tags/edit', [$tagsController, 'edit'])->name('admin.cms.tags-edit');
        Route::post('tags/edit', [$tagsController, 'edit'])->name('admin.cms.tags-edit-save');
        Route::post('tags/delete', [$tagsController, 'delete'])->name('admin.cms.tags-delete');
        Route::post('tags/state', [$tagsController, 'state'])->name('admin.cms.tags-state');
        Route::post('tags/sort', [$tagsController, 'sort'])->name('admin.cms.tags-sort');
        
        // 设置
        $settingController = AdminController\Setting::class;
        Route::get('setting/index', [$settingController, 'index'])->name('admin.cms.setting-index');
        Route::post('setting/index', [$settingController, 'index'])->name('admin.cms.setting-index-save');
        Route::get('setting/theme', [$settingController, 'theme'])->name('admin.cms.setting-theme');
        Route::post('setting/theme', [$settingController, 'theme'])->name('admin.cms.setting-theme-save');
        
        // 内容
        $contentController = AdminController\Content::class;
        Route::get('content/index', [$contentController, 'index'])->name('admin.cms.content-index');
        Route::get('content/main', [$contentController, 'main'])->name('admin.cms.content-main');
        Route::get('content/page', [$contentController, 'page'])->name('admin.cms.content-page');
        Route::post('content/page', [$contentController, 'page'])->name('admin.cms.content-page-save');
        Route::get('content/cate', [$contentController, 'cate'])->name('admin.cms.content-cate');
        Route::post('content/cate', [$contentController, 'cate'])->name('admin.cms.content-cate-save');
        Route::get('content/add', [$contentController, 'add'])->name('admin.cms.content-add');
        Route::post('content/add', [$contentController, 'add'])->name('admin.cms.content-add-save');
        Route::get('content/edit', [$contentController, 'edit'])->name('admin.cms.content-edit');
        Route::post('content/edit', [$contentController, 'edit'])->name('admin.cms.content-edit-save');
        Route::post('content/delete', [$contentController, 'delete'])->name('admin.cms.content-delete');
        Route::post('content/state', [$contentController, 'state'])->name('admin.cms.content-state');
    });
});
