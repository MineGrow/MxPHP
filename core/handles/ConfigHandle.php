<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;

/**
 * 配置文件处理类
 */
class ConfigHandle implements Handle
{	
	/**
	 * 框架实例
	 * 
	 * @var object
	 */
	private $app;

	/**
	 * 配置
	 * 
	 * @var array
	 */
	private $config = [];

	public function __construct()
	{

	}

	public function __get($name = '')
	{
		return $this->$name;
	}

	public function __set($name = '', $value = '')
	{
		$this->$name = $value;
	}

	/**
	 * 注册 配置文件 处理机制
	 * @param  App    $app 框架实例
	 * @return void      
	 */
	public function register(App $app)
	{
		require($app->rootPath . '/core/Helper.php');

		$this->app = $app;
		$app::$container->setSingle('config', $this);
		$this->loadConfig($app);

		// 设置时区
		date_default_timezone_set($this->config['default_timezone']);
	}

	/**
	 * 加载配置文件
	 * 
	 * @param  App    $app 框架实例
	 * @return void      
	 */
	public function loadConfig(App $app)
	{
		// 加载公共自定义配置
		$defaultCommon 		= require($app->rootPath . '/config/common.php');
		$defaultNoSql  		= require($app->rootPath . '/config/nosql.php');
		$defaultDatabase 	= require($app->rootPath . '/config/database.php');
		$defaultSwoole	 	= require($app->rootPath . '/config/swoole.php');
		$defaultWebserver 	= require($app->rootPath . '/config/webserver.php');

		$this->config = array_merge($defaultCommon, $defaultNoSql, $defaultDatabase, $defaultSwoole);

		// 加载模块自定义配置
		$module = $app::$container->getSingle('config')->config['module'];
		foreach ($module as $value) {
			$file = "{$app->rootPath}/config/{$value}/config.php";
			if (file_exists($file)) {
				$this->config = array_merge($this->config, require($file));
			}
		}
	}

	// 读取自定义配置
	public function loadModuleConfig(App $app, $module)
	{
		$value =strtolower($module);
		$files = "{$app->rootPath}/config/{$value}/config.php";
		if (file_exists($files)) {
			$this->config = array_merge($this->config, require($files));
		}
	}
}