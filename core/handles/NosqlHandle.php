<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;

/**
 * nosql 处理机制
 */
class NosqlHandle implements Handle
{
	
	public function __construct()
	{
		# code...
	}

	public function register(App $app)
	{
		$config = $app::$container->getSingle('config');
		if (empty($config->config['nosql'])) {
			return ;
		}
		$config = explode(',', $config->config['nosql']);
		foreach ($config as $v) {
			$className = 'Core\Nosql\\' . ucfirst($v);
			App::$container->setSingle($v, function() use ($className) {
				// 懒加载
				return $className::init();
			});
		}
	}
}