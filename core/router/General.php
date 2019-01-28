<?php 

namespace Core\Router;

use Core\Router\Router;
use Core\Router\RouterInterface;

/**
 * 普通路由策略
 */
class General implements RouterInterface
{
	
	public function route(Router $entrance)
	{
		$app = $entrance->app;

		$request		= $app::$container->get('request');
		$moduleName 	= $request->request('module');
		$controllerName = $request->request('controller');
		$actionName 	= $request->request('action');

		if (!empty($moduleName)) {
			$entrance->moduleName = $moduleName;
		}

		if (!empty($controllerName)) {
			$entrance->controllerName = $controllerName;
		}

		if (!empty($actionName)) {
			$entrance->actionName = $actionName;
		}

		// CLI 模式不输出
		if (empty($actionName) && $entrance->app->runningMode === 'cli') {
			$entrance->app->notOutput = true;
		}
	}
}