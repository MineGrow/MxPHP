<?php
namespace Core\Common;

use Core\App;
use Core\Response;

class Smarty {

	private $smartyConfig = '';
	private $instance;
	private $viewsPath = '';

	public function __construct()
	{
		$this->instance = new \Smarty;

		# 配置 smarty 分隔符
    	$this->instance->left_delimiter = "{/";
		$this->instance->right_delimiter = "/}";
		
		$pathInfo = App::$app->pathInfo;
		$moduleName = isset($pathInfo['module']) ? strtolower($pathInfo['module']) : 'common';
		$controllerName = isset($pathInfo['controller']) ? strtolower($pathInfo['controller']) : 'index';
		
		# 获取模板路径 
		$this->viewsPath = App::$app->rootPath . '/public/' . strtolower($moduleName) . '/views';
		# 按控制器分文件夹
		$this->instance->setTemplateDir($this->viewsPath . "/templates/{$controllerName}/"); //设置模板目录
		$this->instance->setCompileDir($this->viewsPath . '/templates_c/');
		$this->instance->setConfigDir($this->viewsPath . '/configs/');
		$this->instance->setCacheDir($this->viewsPath . '/cache/');

		# 判断是否调试环境
		$this->smartyConfig = env('smarty');

		if ($this->smartyConfig['debug'] == true) {
			$this->instance->caching 		= false;
			$this->instance->cache_lifetime = 0;
		} else {
			$this->instance->caching 		= true;
			$this->instance->cache_lifetime = 120;
		}
	}


	public function setAssign($var, $value='')
	{
		$this->instance->assign($var, $value);
	}

	/**
	 * 渲染页面
	 *
	 * @param  string $tpl  html页面名称
	 * @param  array  $data 数据
	 *
	 * @return html       
	 */
	public function display($tpl, $data=[]){

		$this->instance->assign($data);
		$this->instance->display($tpl . '.'. $this->smartyConfig['suffix']);
	}
}