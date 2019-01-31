<?php 

namespace Core;

use Closure;
use Core\Exceptions\CoreHttpException;
use Core\Container;

/**
 * 框架应用类
 */
class App
{
	/**
	 * 框架加载流程处理类集合
	 * @var array
	 */
	private $handlesList = [];

	/**
	 * 请求对象
	 * @var object
	 */
	private $request;

	/**
	 * 框架实例根目录
	 * @var string
	 */
	public $rootPath;

	/**
	 * 请求参数 path 信息 module|controller|action
	 *
	 * @var [type]
	 */
	public $pathInfo;

	/**
	 * 登录用户信息
	 *
	 * @var [type]
	 */
	public $userInfo;
	/**
	 * 响应对象
	 * @var [type]
	 */
	private $responseData;

	/**
	 * 框架实例
	 * @var object
	 */
	public static $app;

	/**
	 * 是否输出响应结果
	 * 
	 * 默认输出
	 *
	 * cli 模式，访问路径为空 不输出
	 * 
	 * @var boolean
	 */
	private $notOutput = false;

	/**
	 * 服务容器
	 * 
	 * @var object
	 */
	public static $container;

	/**
	 * 运行模式
	 * 支持 fpm/cli/
	 * 默认为 fpm
	 * @var string
	 */
	private $runningMode = 'fpm';

	/**
	 * 构造函数
	 * 
	 * @param string  $rootPath 框架实例根目录
	 * @param Closure $loader   注入自加载实例
	 */
	public function __construct($rootPath, Closure $loader)
	{
		// 运行模式
		$this->runningMode = getenv('RUN_MODE');
		// 根目录
		$this->rootPath = $rootPath;

		// 注册自加载
		$loader();
		Load::register($this);

		self::$app = $this;
		self::$container = new Container();
	}

	/**
	 * 魔法函数 __get
	 * 
	 * @param  string $name 属性名称
	 * @return mixed       
	 */
	public function __get($name = '')
	{
		return $this->$name;
	}

	/**
	 * 魔法函数 __set
	 * 
	 * @param string $name  属性名称
	 * @param string $value 属性值
	 */
	public function __set($name = '', $value = '')
	{
		$this->$name = $value;
	}

	/**
	 * 注册框架允许过程中处理类
	 * @param  Closure $handle handle类
	 * @return void          
	 */
	public function load(Closure $handle)
	{
		$this->handlesList[] = $handle;
	}

	/**
	 * 内部调用 get
	 * @param  string $uri   调用的 path
	 * @param  array  $argus 参数
	 * @return void        
	 */
	public function get($uri = '', $argus = array())
	{
		return $this->callSelf('get', $uri, $argus);
	}

	public function post($uri = '', $argus = array())
	{
		return $this->callSelf('post', $uri, $argus);
	}

	public function put($uri = '', $argus = array())
	{
		return $this->callSelf('put', $uri, $argus);
	}

	public function delete($uri = '', $argus = array())
	{
		return $this->callSelf('delete', $uri, $argus);
	}

	public function callSelf($method = '', $uri = '', $argus = array())
	{
		$requestUri = explode('/', $uri);
		if (count($requestUri) !== 3) {
			throw new CoreHttpException(400);
		}

		$request = self::$container->get('request');
		$request->method 		= $method;
		$request->requestParams = $argus;
		$request->getParams 	= $argus;
		$request->postParams 	= $argus;

		$router = self::$container->get('router');
		$router->meduleName  	= $requestUri[0];
		$router->controllerName = $requestUri[1];
		$router->actionName 	= $requestUri[2];
		$router->routeStrategy  = 'micromonomer';
		$router->init($this);
		return $this->responseData;
	}

	/**
	 * 运行应用
	 * @param  Closure $request 请求对象
	 * @return void           
	 */
	public function run(Closure $request)
	{
		self::$container->set('request', $request);
		foreach ($this->handlesList as $handle) {
			$handle()->register($this);
		}
	}

	/**
	 * 生命周期结束
	 * @param  Closure $closure 响应类
	 * @return json           
	 */
	public function response(Closure $closure)
	{
		if ($this->notOutput === true) {
			return;
		}
		if ($this->runningMode === 'cli') {
			$closure()->cliModeSuccess($this->responseData);
			return;
		}

		$useRest = self::$container->getSingle('config')->config['rest_response'];

		if ($useRest == 'rest') {
			$closure()->restSuccess($this->responseData);
		} else if ($useRest == 'html') {
			$closure()->responseHtml($this->responseData);
		}
		$closure()->response($this->responseData);
	}

	public function responseSwoole(Closure $closure)
	{
		$closure()->header('Content-Type', 'Application/json');
        $closure()->header('Charset', 'utf-8');
        $closure()->end(json_encode($this->responseData));
	}
}