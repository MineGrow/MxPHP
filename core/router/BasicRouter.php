<?php 

namespace Core\Router;

use Core\App;
use Core\Router\Router;
use core\Exceptions\CoreHttpException;
use Closure;

/**
 * 路由入口
 */
class BasicRouter implements Router
{
	private $app;

	private $config;

	private $request;

	private $moduleName = '';

	private $controllerName = '';

	private $actionName = '';

	private $classPath = '';

	// 类文件执行类型
	private $executeType = 'controller';

	// 请求uri
	private $requestUri = '';

	// 路由策略
	private $routeStrategy = '';

	// 路由策略映射
	private $routeStrategyMap = [
		'general'		=> 'Core\Router\General',
		'pathinfo'		=> 'Core\Router\Pathinfo',
		'user-defined'	=> 'Core\Router\UserDefined',
		'micromonomer'	=> 'Core\Router\Micromonomer',
		'job'			=> 'Core\Router\Job',
	];

	public function __get($name = '')
	{
		return $this->$name;
	}

	public function __set($name = '', $value = '')
	{
		$this->$name = $value;
	}

	public function init(App $app)
	{
		// 注入当前对象到容器
		$app::$container->set('router', $this);

		$this->request 		= $app::$container->get('request');
		$this->requestUri	= $this->request->server('REQUEST_URI');

		$this->app 			= $app;
		
		$this->config 		= $app::$container->getSingle('config');

		
		$this->moduleName 		= !empty($this->request->get('module')) ? $this->request->get('module') : $this->config->config['route']['default_module'];
		$this->controllerName	= !empty($this->request->get('contoller')) ? $this->request->get('contoller') : $this->config->config['route']['default_controller'];
		$this->actionName 		= !empty($this->request->get('action')) ? $this->request->get('action') : $this->config->config['route']['default_action'];

		// 初始化 APP 的pathInfo 参数
		$this->app->pathInfo    = ['module' => $this->moduleName, 'controller' => $this->controllerName, 'action' => $this->actionName];
		// 自定义模块配置
		if ($this->moduleName != $this->config->config['route']['default_module']) {
			$this->config->loadModuleConfig($this->app, $this->moduleName);
		}

		// 路由决策
		$this->strategyJudge();

		// 路由策略
		(new $this->routeStrategyMap[$this->routeStrategy])->route($this);

		$this->makeClassPath($this);

		// 自定义路由判断
		if ((new $this->routeStrategyMap['user-defined'])->route($this)) {
			return ;
		}

		// 启动路由
		$this->start();
	}

	/**
	 * 路由决策
	 * @return void 
	 */
	public function strategyJudge()
	{
		// 路由策略
		if (!empty($this->routeStrategy)) {
			return;
		}

		// 任务路由
		if ($this->app->runningMode === 'cli' && $this->request->get('router_mode') === 'job') {
			$this->routeStrategy = 'job';
			return;
		}

		// 普通路由
		if (strpos($this->requestUri, 'index.php') || $this->app->runningMode === 'cli') {
			$this->routeStrategy = 'general';
			return;
		}

		$this->routeStrategy = 'pathinfo';
	}

	public function makeClassPath()
	{
		if ($this->routeStrategy === 'job') {
			return;
		}

		$controllerName 	= ucfirst($this->controllerName);
		$folderName			= ucfirst($this->config->config['application_folder_name']);
		$this->classPath 	= "{$folderName}\\{$this->moduleName}\\Controllers\\{$controllerName}";
	}

	public function start()
	{
		// var_dump(strtolower($this->moduleName), $this->config->config['module']);
		// 判断模块不存在
		if (! in_array(strtolower($this->moduleName), $this->config->config['module'])) {
			throw new CoreHttpException(404, "{$this->executeType}:{$this->classPath}");
		}

		// 实例化当前控制器
		$controller = new $this->classPath();
		if (!method_exists($controller, $this->actionName)) {
			throw new CoreHttpException(404, "Action:{$this->actionName}");
		}

		// 调用操作
		$actionName = $this->actionName;

		// 获取返回值
		$this->app->responseData = $controller->$actionName();
	}
}