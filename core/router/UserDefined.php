<?php 

namespace Core\Router;

use Core\Router\Router;
use Core\Router\RouterInterface;
use Core\Exceptions\CoreHttpException;

/**
 * 自定义路由策略
 */
class Userdefined implements RouterInterface
{
	private $getMap = [];

	private $postMap = [];

	private $putMap = [];

	private $deleteMap = [];

	public function get($uri = '', $function = '')
	{
		$this->getMap[$uri] = $function;
	}

	public function post($uri = '', $function = '')
	{
		$this->postMap[$uri] = $function;
	}

	public function put($uri = '', $function = '')
	{
		$this->putMap[$uri] = $function;
	}

	public function delete($uri = '', $function = '')
	{
		$this->deleteMap[$uri] = $function;
	}

	public function route(Router $entrance)
	{
		if ($entrance->routeStrategy === 'job') {
			return;
		}

		$module = $entrance->config->config['module'];
		foreach ($module as $v) {
			$routeFile = "{$entrance->app->rootPath}/config/{$v}/route.php";
			if (file_exists($routeFile)) {
				require($routeFile);
			}
		}

		$uri = "{$entrance->moduleName}/{$entrance->controllerName}/{$entrance->actionName}";
		$app = $entrance->app;
		$request = $app::$container->get('request');
		$method  = strtolower($request->method) . 'Map';
		if (!isset($this->$method)) {
			throw new CoreHttpException(
				404, 
				"Http Method:{$request->method}"
			);
		}
		if (!array_key_exists($uri, $this->$method)) {
			return false;
		}

		// 执行自定义路由匿名函数
		$map = $this->$method;
		$entrance->app->responseData = $map[$uri]($app);
		if ($entrance->app->runningMode === 'cli') {
			$entrance->app->notOutput = false;
		}
		return true;
	}
}