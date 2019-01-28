<?php 

use Core\Handles\ErrorHandle;
use Core\Handles\ExceptionHandle;
use Core\Handles\EnvHandle;
use Core\Handles\RouterHandle;
use Core\Handles\ConfigHandle;
use Core\Handles\LogHandle;
use Core\Handles\NosqlHandle;
use Core\Handles\UserDefinedHandle;
use Core\Handles\SessionHandle;
use Core\Exceptions\CoreHttpException;
use Core\Request;
use Core\Response;

require(__DIR__ . '/App.php');

error_reporting( E_ALL | E_STRICT );
ini_set("display_errors","On");

try {
	// 初始化应用
	$app = new Core\App(realpath(__DIR__ . '/..'), function(){
		return require(__DIR__ . '/Load.php');
	});

	// 预环境处理机制
	$app->load(function() {
		return new EnvHandle();
	});
	// 配置处理机制
	$app->load(function() {
		return new ConfigHandle();
	});
	// 日志处理机制
	$app->load(function() {
		return new LogHandle();
	});
    // Session 开启
    $app->load(function() {
    	return new SessionHandle();
    });
	// 错误处理机制
	$app->load(function() {
		return new ErrorHandle();
	});
	// 异常处理机制
	$app->load(function() {
		return new ExceptionHandle();
	});
	// nosql 处理机制
	$app->load(function() {
		return new NosqlHandle();
	});
	// 自定义机制
	$app->load(function() {
		return new UserDefinedHandle();
	});
	// 路由机制
	$app->load(function () {
        return new RouterHandle();
    });

    // 启动应用
    $app->run(function () use ($app) {
    	return new Request($app);
    });

    // 响应结果
    $app->response(function() {
    	return new Response();
    });
} catch (CoreHttpException $e) {
	// 捕获异常
	$e->response();
}
