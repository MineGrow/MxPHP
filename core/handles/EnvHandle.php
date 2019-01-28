<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;

class EnvHandle implements Handle
{	
	/**
	 * 请求参数
	 * 
	 * @var array
	 */
	private $envParams = [];

	public function __construct()
	{

	}

	/**
	 * 注册 env 处理机制
	 * @param  App    $app 框架实例
	 * @return void      
	 */
	public function register(App $app)
	{
		// 加载环境参数
		$this->loadEnv($app);

		App::$container->setSingle('envt', $this);
	}

	/**
	 * 获取 env 参数
	 * @param  string $value 参数名
	 * @return mixed        
	 */
	public function env($value = '')
	{
		if (isset($this->envParams[$value])) {
			return $this->envParams[$value];
		}
		return '';
	}

	/**
	 * 加载环境参数
	 * @param  App    $app 框架实例
	 * @return void      
	 */
	public function loadEnv(App $app)
	{
		$env = parse_ini_file($app->rootPath . '/.env', true);
		if ($env === false) {
			throw CoreHttpException('load env fail', 500);
		}
		$this->envParams = array_merge($_ENV, $env);
	}
}