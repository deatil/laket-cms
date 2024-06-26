<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Service;

use think\facade\Db;

use Laket\Admin\CMS\Support\Datatable;

/**
 * 模型
 *
 * @create 2020-1-8
 * @author deatil
 */
class Model 
{
    /**
     * 类型转换列表 
     */
    protected $types = [
        "array"    => "VARCHAR",
        "checkbox" => "VARCHAR",
        "color"    => "VARCHAR",
        "date"     => "INT",
        "datetime" => "INT",
        "file"     => "VARCHAR",
        "files"    => "TEXT",
        "hidden"   => "VARCHAR",
        "image"    => "VARCHAR",
        "images"   => "TEXT",
        "number"   => "INT",
        "radio"    => "VARCHAR",
        "select"   => "VARCHAR",
        "switch"   => "TINYINT",
        "tags"     => "VARCHAR",
        "text"     => "VARCHAR",
        "textarea" => "VARCHAR",
        "password" => "VARCHAR",
        "editor"   => "TEXT",
    ];
    
    /**
     * 创建
     */
    public static function create()
    {
        return new static();
    }
    
    /**
     * append Types
     */
    public function appendTypes(array $types)
    {
        $this->types = array_merge($this->types, $types);
        return $this;
    }
    
    /**
     * datatable
     */
    public function getDatatable()
    {
        $modelPrefix = 'cms_ext_';
        $prefix = app()->db->connect()->getConfig('prefix');
        $datatable = new Datatable();
        $datatable->setPrefix($prefix . $modelPrefix)
            ->setCharset('utf8mb4')
            ->setEngineType('MyISAM');
        
        return $datatable;
    }
    
    /**
     * 检测表
     */
    public function checkTable($table) 
    {
        $datatable = $this->getDatatable();
        if (! $datatable->checkTable($table)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 创建表
     */
    public function createTable(
        $table = '', 
        $comment = '', 
        $pk = 'id', 
        $charset = null, 
        $engine_type = null
    ) {
        $datatable = $this->getDatatable();
        if ($datatable->checkTable($table)) {
            return false;
        }
        
        $result = $datatable
            ->createTable($table, $comment, $pk, $charset, $engine_type)
            ->query();
            
        return $result;
    }
    
    /**
     * 更新数据表
     */
    public function updateTableName($oldTable = '', $newTable = '') 
    {
        $datatable = $this->getDatatable();
        if (! $datatable->checkTable($oldTable)) {
            return false;
        }
        
        $result = $datatable
            ->updateTableName($oldTable, $newTable)
            ->query();
            
        return $result;
    }
    
    /**
     * 删除数据表
     */
    public function deleteTable($table = '') 
    {
        $datatable = $this->getDatatable();
        if (! $datatable->checkTable($table)) {
            return false;
        }
        
        $result = $datatable->deleteTable($table)
            ->query();
            
        return $result;
    }
    
    /**
     * 检测字段
     */
    public function checkField($table, $field = '') 
    {
        $datatable = $this->getDatatable();
        if (! $datatable->checkField($table, $field)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 添加字段
     */
    public function createField($table, $attr = []) 
    {
        if (isset($attr['type'])) {
            $newAttr = $attr;
            $newAttr['type'] = $this->types[$attr['type']] ?: $attr['type'];
        }
        
        $datatable = $this->getDatatable();
        if ($datatable->checkField($table, $newAttr['name'])) {
            return false;
        }
        
        $result = $datatable->createField($table, $newAttr)
            ->query();
        
        return $result;
    }
    
    /**
     * 更新字段
     */
    public function changeField($table, $attr = []) 
    {
        if (isset($attr['type'])) {
            $newAttr = $attr;
            $newAttr['type'] = $this->types[$attr['type']] ?: $attr['type'];
        }
        
        $datatable = $this->getDatatable();
        
        // 字段名不相同时
        if ($newAttr['name'] != $newAttr['oldname']) {
            // 旧字段不存在
            if (! $datatable->checkField($table, $newAttr['oldname'])) {
                return false;
            }
            
            // 新字段存在
            if ($datatable->checkField($table, $newAttr['name'])) {
                return false;
            }
        }
        
        $result = $datatable->changeField($table, $newAttr)
            ->query();
        
        return $result;
    }
    
    /**
     * 删除字段
     */
    public function deleteField($table, $field = '') 
    {
        $datatable = $this->getDatatable();
        if (! $datatable->checkField($table, $field)) {
            return false;
        }
        
        $result = $datatable->deleteField($table, $field)
            ->query();
        
        return $result;
    }
    
}