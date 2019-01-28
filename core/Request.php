<?php 

namespace Core;

use Core\Exceptions\CoreHttpException;

class Request
{
	// 请求 header 参数
	private $headerParams 	= [];
	// 请求 server 参数
	private $serverParams 	= [];
	// 请求所有参数
	private $requestParams	= [];
	// 请求 GET 参数
	private $getParams    	= [];
	// 请求 POST 参数
	private $postParams   	= [];
	// cookie
	private $cookie 	  	= [];
	// file
	private $file 			= [];
	// 服务 ip
	private $serverIp 		= '';
	// 客户端 ip
	private $clientIp		= '';
	// 请求开始时间
	private $beginTime		= 0;
	// 请求结束时间
	private $endTime		= 0;
	// 请求消耗时间 毫秒
	private $consumeTime	= 0;
	// 请求身份 id 每个请求唯一识别 id
	private $rquestId		= '';

	public function __construct(App $app)
	{
		if ($app->runningMode === 'swoole') {
			$swooleRequest = $app::$container->get('request-swoole');
            $this->headerParams  = $swooleRequest->header;
            $this->serverParams  = $swooleRequest->server;
            $this->method        = $this->serverParams['request_method'];
            $this->serverIp      = $this->serverParams['remote_addr'];
            $this->clientIp      = $this->serverParams['remote_addr'];
            $this->beginTime     = $this->serverParams['request_time_float'];
            $this->getParams     = isset($swooleRequest->get)? $swooleRequest->get: [];
            $this->postParams    = isset($swooleRequest->post)? $swooleRequest->post: [];
            $this->cookie        = isset($swooleRequest->cookie)? $swooleRequest->cookie: [];
            $this->file          = isset($swooleRequest->files)? $swooleRequest->files: [];
            $this->requestParams = array_merge($this->getParams, $this->postParams);
			return;
		}

		$this->serverParams = $_SERVER;
		$this->method 		= isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
		$this->serverIp 	= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$this->clientIp		= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
		$this->beginTime	= isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);

		if ($app->runningMode === 'cli') {
			// cli 模式
			$this->requestParams = isset($_REQUEST['argv']) ? $_REQUEST['argv'] : []; 
			$this->getParams 	 = isset($_REQUEST['argv']) ? $_REQUEST['argv'] : []; 
			$this->postParams	 = isset($_REQUEST['argv']) ? $_REQUEST['argv'] : []; 
			return ;
		}

		$this->requestParams 	= $_REQUEST;
		$this->getParams 		= $_GET;
		$this->postParams 		= $_POST;
	}

	public function __get($name = '')
	{
		return $this->$name;
	}

	public function __set($name = '', $value = '')
	{
		$this->$name = $value;
	}

	/**
	 * 获取 get 参数
	 * @param  string  $value      参数名
	 * @param  string  $default    默认值
	 * @param  boolean $checkEmpty 值为空时是否返回默认值，默认为true
	 * @return mixed              
	 */
	public function get($value = '', $default = '', $checkEmpty = true)
	{
		if (!isset($this->getParams[$value])) {
			return '';
		}
		if (empty($this->getParams[$value]) && $checkEmpty) {
			return $default;
		}
		return htmlspecialchars($this->getParams[$value]);
	}

	public function post($value = '', $default = '', $checkEmpty = true)
	{
		if (empty($this->postParams[$value]) && $checkEmpty) {
			return $default;
		}
		if (!isset($this->postParams[$value])) {
			return '';
		}
		return htmlspecialchars($this->postParams[$value]);
	}

	public function request($value = '', $default = '', $checkEmpty = true)
	{
		if (!isset($this->requestParams[$value])) {
			return '';
		}
		if (empty($this->requestParams[$value]) && $checkEmpty) {
			return $default;
		}
		return htmlspecialchars($this->requestParams[$value]);
	}

	/**
	 * 获取所有参数
	 * @return array 
	 */
	public function all()
	{
		$res = array_merge($this->postParams, $this->getParams);
		foreach ($res as &$v) {
			$v = htmlspecialchars($v);
		}
		return $res;
	}

	public function server($value = '')
	{
		if (isset($this->serverParams[$value])) {
			return $this->serverParams[$value];
		}
		return '';
	}


	public function check($paramName = '', $rule = '', $length = 0)
	{
		if (!is_int($length)) {
			throw new CoreHttpException(400, 'length type is not int');
		}

		if ($rule === 'require') {
			if (!empty($this->request($paramName))) {
				return;
			}
			throw new CoreHttpException(404, "param {$paramName}");
		}

		if ($rule === 'length') {
			if (strlen($this->request($paramName)) === $length) {
				return;
			}
			throw new CoreHttpException(400, "param {$paramName} length is not {$length}");
		}

		if ($rule === 'number') {
			if (is_numeric($this->request($paramName))) {
				return;
			}
			throw new CoreHttpException(400, "{$paramName} type is not number");
		}
	}

}
