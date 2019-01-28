<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;

/**
 * 自定义处理机制
 * 自定义框架运行到路由前的操作
 */
class UserDefinedHandle implements Handle
{
	use \Core\Traits\GlobalConstant;

	public function __construct()
	{
		$this->registerGlobalConst();
	}

	/**
	 * 注册用户自定义操作
	 * @param  App    $app 框架实例
	 * @return void      
	 */
	public function register(App $app)
	{
		$config = $app::$container->getSingle('config');

		foreach ($config->config['module'] as $v) {
			$v = ucwords($v);
			$className = "\App\\{$v}\\Logics\UserDefinedCase";
			new $className($app);
		}
	}
}