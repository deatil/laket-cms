<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Model;

use think\helper\Arr;
use think\Model as BaseModel;

use Laket\Admin\CMS\Support\Pinyin;
use Laket\Admin\CMS\Support\Field as SupportField;
use Laket\Admin\CMS\Model\ModelField;

/**
 * 内容
 */
class Content extends BaseModel
{
    // 设置当前模型对应的数据表名称
    protected $name = 'cms_ext_';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    // 清除默认设置数据，防止报错
    protected static $maker = [];
    
    public static function onBeforeInsert($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }
    
    public static function onBeforeUpdate($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
    }
    
    /**
     * 分组
     */
    public function cate()
    {
        return $this->hasOne(Category::class, 'id', 'categoryid');
    }
    
    /**
     * 新建模型表
     */
    public static function newTable($table)
    {
        return self::suffix($table);
    }
    
    /**
     * 新建数据
     */
    public static function newCreate(
        string $table, 
        array $data, 
        array $allowField = [], 
        bool $replace = false
    ) {
        return self::create($data, $allowField, $replace, $table);
    }
    
    /**
     * 更新数据
     */
    public static function newUpdate(
        string $table, 
        array $data, 
        $where = [], 
        array $allowField = []
    ) {
        return self::update($data, $where, $allowField, $table);
    }
    
    /**
     * 检测规则
     */
    public function getValidateRules($data)
    {
        if (empty($data)) {
            return [];
        }
        
        $validateRules = SupportField::parseRule(Arr::get($data, 'validate_rule', ''));
        return $validateRules;
    }
    
    /**
     * 检测提示
     */
    public function getValidateMessages($data)
    {
        if (empty($data)) {
            return [];
        }
        
        $validateMessages = SupportField::parseMessage(Arr::get($data, 'validate_message', ''));
        return $validateMessages;
    }
    
    /**
     * 表单需要使用的模型字段
     */
    public static function formFields($where, $showType = 1)
    {
        $data = Model::where($where)->find();
        $fields = $data['fields'];
        if ($fields) {
            $fields = $fields->toArray();
        }
        
        $data = collect($fields)
            ->order('sort', 'desc')
            ->map(function($item) use($showType) {
                if ($item['status'] != 1) {
                    return null;
                }
                
                if ($item['show_type'] != 1) {
                    if ($item['show_type'] != $showType
                        || $item['show_type'] == 4
                    ) {
                        return null;
                    }
                }
                
                $options = SupportField::parseAttr($item['options']);
                
                return [
                    'type' => $item['type'],
                    'name' => $item['name'],
                    'title' => $item['title'],
                    'value' => $item['value'],
                    'options' => $options,
                    'remark' => $item['remark'],
                    'ifrequire' => $item['is_must'],
                    'fieldArr' => 'modelField',
                ];
            })
            ->filter(function($item) {
                if (! empty($item)) {
                    return $item;
                }
            })
            ->values()
            ->toArray();
            
        return $data;
    }
    
    /**
     * 表单更新显示
     */
    public static function formatFormFieldsShow($fields, $info = [])
    {
        foreach ($fields as $key => $value) {
            if (isset($info[$value['name']])) {
                $value['value'] = $info[$value['name']];
            }
            
            if (! isset($value['value'])) {
                $value['value'] = '';
            }
            
            if (! empty($value['value'])) {
                if ($value['type'] == 'switch') {
                    $value['value'] = empty($value['value']) ? 1 : $value['value'];
                }
                
                if ($value['type'] == 'checkbox') {
                    $value['value'] = empty($value['value']) ? [] : explode(',', $value['value']);
                }
                
                if ($value['type'] == 'date') {
                    $value['value'] = empty($value['value']) ? '' : date('Y-m-d', (int) $value['value']);
                }
                
                if ($value['type'] == 'datetime') {
                    $value['value'] = date('Y-m-d H:i:s', (int) $info[$value['name']]);
                }

                if ($value['type'] == 'editor') {
                    $value['value'] = htmlspecialchars_decode($value['value']);
                }
            } else {
                if ($value['type'] == 'switch') {
                    $value['value'] = 1;
                }
                if ($value['type'] == 'checkbox') {
                    $value['value'] = [];
                }
                
                if ($value['type'] == 'date') {
                    $value['value'] = date('Y-m-d');
                }
                if ($value['type'] == 'datetime') {
                    $value['value'] = date('Y-m-d H:i:s');
                }
            }
            
            $fields[$key]['value'] = $value['value'];
        }
        
        return $fields;
    }
    
    /**
     * 格式化字段信息
     */
    public static function formatFormFieldsInsert($fields, $userData)
    {
        foreach ($fields as $field) {
            if (isset($userData[$field['name']])) {
                if ($field['type'] == 'checkbox') {
                    $userData[$field['name']] = implode(',', $userData[$field['name']]);
                }
                
                if ($field['type'] == 'date') {
                    $userData[$field['name']] = strtotime($userData[$field['name']]);
                }
                
                if ($field['type'] == 'datetime') {
                    $userData[$field['name']] = strtotime($userData[$field['name']]);
                }
                
                if ($field['type'] == 'Ueditor') {
                    $userData[$field['name']] = htmlspecialchars(stripslashes($userData[$field['name']]));
                }
            }
        }
        
        return $userData;
    }
    
    /**
     * 格式化显示字段信息
     */
    public static function formatShowFields($fields, $userData)
    {
        foreach ($fields as $field) {
            if (! isset($userData[$field['name']])) {
                continue;
            }
            
            $key = $field['name'];
            $value = $userData[$field['name']];
            
            switch ($field['type']) {
                case 'array':
                    $userData[$key] = cms_parse_attr($value);
                    break;
                case 'radio':
                    if (! empty($value)) {
                        if (! empty($field['options'])) {
                            $optionArr = cms_parse_attr($field['options']);
                            $userData[$key] = isset($optionArr[$value]) ? $optionArr[$value] : $value;
                        }
                    }
                    break;
                case 'select':
                    if (! empty($value)) {
                        if (! empty($field['options'])) {
                            $optionArr = cms_parse_attr($field['options']);
                            $userData[$key] = isset($optionArr[$value]) ? $optionArr[$value] : $value;
                        }
                    }
                    break;
                case 'checkbox':
                    if (! empty($value)) {
                        if (! empty($field['options'])) {
                            $optionArr = cms_parse_attr($field['options']);
                            $valueArr = explode(',', $value);
                            
                            $newData = [];
                            foreach ($valueArr as $v) {
                                if (isset($optionArr[$v])) {
                                    $newData[$v] = $optionArr[$v];
                                } elseif ($v) {
                                    $newData[$v] = $v;
                                }
                            }
                            
                            $userData[$key] = $newData;
                        } else {
                            $userData[$key] = [];
                        }
                    } else {
                        $userData[$key] = [];
                    }
                    break;
                case 'image':
                    $userData[$key] = empty($value) ? '' : laket_attachment_url($value);
                    break;
                case 'images':
                    $userData[$key] = empty($value) ? [] : laket_attachment_url($value . ',');
                    break;
                case 'file':
                    $userData[$key] = empty($value) ? '' : laket_attachment_url($value);
                    break;
                case 'files':
                    $userData[$key] = empty($value) ? [] : laket_attachment_url($value . ',');
                    break;
                case 'tags':
                    $userData[$key] = explode(',', $value);
                    break;
                case 'Ueditor':
                    $userData[$key] = htmlspecialchars_decode($value);
                    break;
                default:
                    $userData[$key] = $value;
                    break;
            }
        }
        
        return $userData;
    }
    
    /**
     * 更新标签关联
     */
    public static function updateTagsContent(
        array $tags, 
        int $modelid,
        int $cateid,
        int $contentid
    ) {
        TagsContent::where([
            ['modelid', '=', $modelid],
            ['cateid', '=', $cateid],
            ['contentid', '=', $contentid],
        ])->delete();
        foreach ($tags as $tag) {
            $tagData = Tags::where([
                ['title', '=', $tag],
            ])->find();
            if (empty($tagData)) {
                $newTag = Tags::create([
                    'name' => Pinyin::encode(trim($tag), 'all', 'utf8'),
                    'title' => $tag,
                ]);
                
                if ($newTag !== false) {
                    $newTagId = $newTag->id;
                }
            } else {
                $newTagId = $tagData->id;
            }
            
            TagsContent::create([
                'tagid' => $newTagId,
                'modelid' => $modelid,
                'cateid' => $cateid,
                'contentid' => $contentid,
            ]);
        }
    }
    
    /**
     * 格式化标签信息
     */
    public static function formatFormFieldTags($fields, $userData)
    {
        $tags = [];
        foreach ($fields as $field) {
            if (isset($userData[$field['name']])) {
                if ($field['type'] == 'tags') {
                    $array = preg_split('/[,;\r\n ]+/', trim($userData[$field['name']], ",;\r\n"));
                    $tags[] = $array;
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * 表单验证的模型字段
     */
    public static function validateFields($where, $showType = 1)
    {
        $fields = ModelField::where($where)
            ->order("id ASC")
            ->select()
            ->toArray();
        
        $data = [];
        foreach ($fields as $field) {
            // 过滤不需要的字段
            if ($field['show_type'] != 1) {
                if ($field['show_type'] != $showType
                    || $field['show_type'] == 4
                ) {
                    continue;
                }
            }
            
            if (! empty($field['validate_rule'])) {
                $validateRules = SupportField::parseRule($field['validate_rule']);
            } else {
                $validateRules = [];
            }
            
            if ($field['is_must'] == 1) {
                $validateRules[] = 'require';
            }
            
            if (! empty($validateRules)) {
                $validateRule = implode('|', $validateRules);
                $data['rule'][$field['name'] . '|' . $field['title']] = $validateRule;
            }
            
            if (! empty($field['validate_message'])) {
                $validateMessages = SupportField::parseMessage($field['validate_message'] );
                foreach ($validateMessages as $key => $message) {
                    $data['message'][$field['name'].'.'.$key] = $message;
                }
            }
            
            if ($field['validate_time'] == 'always' 
                || $field['validate_time'] == 'create'
            ) {
                $data['scene']['create'][] = $field['name'];
            }
            if ($field['validate_time'] == 'always' 
                || $field['validate_time'] == 'update'
            ) {
                $data['scene']['update'][] = $field['name'];
            }
        }
        
        return $data;
    }

}
