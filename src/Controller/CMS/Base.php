<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Controller\CMS;

use think\App;
use think\Response;
use think\facade\View;

use Laket\Admin\CMS\Service\Template;

/**
 * CMS
 *
 * @create 2024-6-12
 * @author deatil
 */
abstract class Base
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * 框架构造函数
     */
    protected function initialize()
    {
        $layoutFile = Template::themePath('layout.html');

        // seo相关信息
        $this->assign([
            'meta'       => config('cms'),
            'cms_layout' => $layoutFile,
        ]);
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return string|true
     */
    protected function validate(
        array $data, 
        $validate, 
        array $message = [], 
        bool $batch = false
    ) {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } elseif (is_object($validate) 
            && $validate instanceof Validate
        ) {
            $v = $validate;
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (! empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        
        if ($v->check($data)) {
            return true;
        }

        return $v->getError();
    }

    /**
     * 模板变量赋值
     *
     * @access protected
     * @param string|array $name  模板变量
     * @param mixed        $value 变量值
     *
     * @return $this
     */
    protected function assign($name, $value = null)
    {
        View::assign($name, $value);
        return $this;
    }

    /**
     * 视图过滤
     *
     * @access protected
     * @param Callable $filter 过滤方法或闭包
     *
     * @return $this
     */
    protected function filter($filter = null)
    {
        View::filter($filter);
        return $this;
    }

    /**
     * 解析和获取模板内容 用于输出
     *
     * @access protected
     * @param string $template 模板文件名或者内容
     * @param array  $vars     模板变量
     *
     * @return string
     * @throws \Exception
     */
    protected function fetch($template, $vars = [])
    {
        if (! file_exists($template)) {
            $template = app('laket-admin.view-finder')->find($template);
        }
        
        // 配置视图标签
        $viewTaglib = View::getConfig('taglib_build_in');

        $viewTaglibs = explode(',', $viewTaglib);
        $taglibs = (array) app('laket-admin.view-taglib')->getTaglibs();
        
        $newTaglibs = array_filter(array_merge($viewTaglibs, $taglibs));
        View::config([
            'taglib_build_in' => implode(',', $newTaglibs),
        ]);
        
        return View::fetch($template, $vars);
    }
    
    /**
     * 操作成功跳转的快捷方法
     * 
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function success(
        $msg  = '', 
        $url  = null, 
        $data = '', 
        $wait = 3, 
        array $header = []
    ) {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : url($url);
        }

        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => (string) $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type == 'html') {
            $html = Template::themePath(config('cms.dispatch_success_tmpl'));
            $response = view($html, $result);
        } else if ($type == 'json') {
            $response = json($result);
        }

        return $response;
    }

    /**
     * 操作错误跳转的快捷方法
     * 
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function error(
        $msg  = '', 
        $url  = null, 
        $data = '', 
        $wait = 3, 
        array $header = []
    ) {
        $type = $this->getResponseType();
        if (is_null($url)) {
            $url = request()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : url($url);
        }

        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => (string) $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type == 'html'){
            $html = Template::themePath(config('cms.dispatch_error_tmpl'));
            $response = view($html, $result);
        } else if ($type == 'json') {
            $response = json($result);
        }

        return $response;
    }

    /**
     * 返回封装后的API数据到客户端
     * 
     * @param  mixed     $data 要返回的数据
     * @param  integer   $code 返回的code
     * @param  mixed     $msg 提示信息
     * @param  string    $type 返回数据格式
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function result(
        $data, 
        $code = 0, 
        $msg  = '', 
        $type = '', 
        array $header = []
    ) {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'time' => time(),
        ];

        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        return $response;
    }

    /**
     * URL重定向
     * 
     * @param  string        $url 跳转的URL表达式
     * @param  array|integer $params 其它URL参数
     * @param  integer       $code http code
     * @param  array         $with 隐式传参
     * @return void
     */
    protected function redirect($url, $code = 302, $with = [])
    {
        $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : url($url);

        return Response::create((string) $url, 'redirect', $code)->with($with);
    }

    /**
     * 获取当前的response 输出类型
     * 
     * @return string
     */
    protected function getResponseType()
    {
        $type = (request()->isJson() || request()->isAjax()) ? 'json' : 'html';

        return $type;
    }

    /**
     * 设置标题
     */
    protected function setMetaTitle($title = '') 
    {
        if (! empty($title)) {
            $title = $title . ' - ' . config('cms.site_name');
        } else {
            $title = config('cms.site_name') . ' - ' . config('cms.site_slogan');
        }
        
        $this->assign('meta_title', $title);
    }

    /**
     * 设置关键字
     */
    protected function setMetaKeywords($keywords = '') 
    {
        if (empty($keywords)) {
            $keywords = config('cms.site_keywords');
        }
        
        $this->assign('meta_keywords', $keywords);
    }

    /**
     * 设置描述
     */
    protected function setMetaDescription($description = '') 
    {
        if (empty($description)) {
            $description = config('cms.site_description');
        }
        
        $this->assign('meta_description', $description);
    }
}
