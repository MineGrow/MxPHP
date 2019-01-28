<?php 

namespace Core\Router;

use Core\App;
use Core\Router\Router;
use Core\Router\RouterInterface;
use Core\Exceptions\CoreHttpException;
use ReflectionClass;
use Closure;

/**
 * 任务路由处理机制
 */
class Job implements RouterInterface
{
	private $app;

	private $config;

	public function route(Router $entrance)
	{
		$entrance->app->notOutput = true;

		$app 		= $entrance->app;
		$request	= $app::$container->get('request');
		$moduleName = $request->request('module');
		$jobName	= $request->request('job');
		$actionName = $request->request('action');

		$entrance->moduleName 	= $moduleName;
		$entrance->jobName	 	= $jobName;
		$entrance->actionName 	= $actionName;

		$jobName		= ucfirst($jobName);
		$moduleName		= ucfirst($moduleName);
		$appName		= ucfirst($entrance->config->config['jobs_folder_name']);
		$entrance->classPath = "{$appName}\\{$moduleName}\\{$jobName}";
	}
}