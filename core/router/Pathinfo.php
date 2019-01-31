<?php 

namespace Core\Router;

use Core\Router\Router;
use Core\Router\RouterInterface;

/**
 * pathinfo 路由策略
 */
class Pathinfo implements RouterInterface
{
	
	public function route(Router $entrance)
	{
		if (strpos($entrance->requestUri, '?')) {
			preg_match_all('/^\/(.*)\?/', $entrance->requestUri, $uri);
		} else {
			preg_match_all('/^\/(.*)/', $entrance->requestUri, $uri);
		}

		// var_dump($entrance->requestUri);exit();
		// 使用默认模块/控制器/操作逻辑
		if (!isset($uri[1][0]) || empty($uri[1][0])) {
			if ($entrance->app->runningMode === 'cli') {
				$entrance->app->notOutput = true;
			}
			return;
		}
		$uri = $uri[1][0];


		// 自定义路由判断  最后面的 "/" 过滤
		$uri = explode('/', rtrim($uri, '/'));
		switch (count($uri)) {
			case 3:
				$entrance->moduleName 		= $uri['0'];
				$entrance->controllerName	= $uri['1'];
				$entrance->actionName 		= $uri['2'];
				break;

			case 2:
				$entrance->controllerName  	= $uri['0'];
				$entrance->actionName 		= $uri['1'];
				break;

			case 1:
				$entrance->actionName 		= $uri['0'];
				break;

			default:
				# code...
				break;
		}
	}
}