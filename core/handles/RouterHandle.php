<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;
use ReflectionClass;
use Closure;
use Core\Router\Job;
use Core\Router\BasicRouter;

/**
 * 自定义处理机制
 * 自定义框架运行到路由前的操作
 */
class RouterHandle implements Handle
{
	/**
	 * 注册路由处理机制
	 * @param  App    $app 框架实例
	 * @return void      
	 */
	public function register(App $app)
	{
		// 初始化路由入口
		(new BasicRouter())->init($app);
	}
}