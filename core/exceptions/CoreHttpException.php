<?php 

namespace Core\Exceptions;

use Exception;
use Core\App;

class CoreHttpException extends Exception
{
	/**
	 * 异常响应 code
	 * 
	 * @var array
	 */
	private $httpCode = [
		400	=> 'Bad Request', 	// 缺少参数或者必传参数为空
		403	=> 'Forbidden',		// 没有访问权限
		404 => 'Not Found',		// 访问的资源不存在
		500	=> 'Internet Server Error',	// 代码错误
		503	=> 'Service Unavailable',	// Remote Service error
	];

	public function __construct($code = 200, $extra = '')
	{
		$this->code = $code;
		if (empty($extra)) {
			$this->message = $this->httpCode[$code];
			return;
		}
		$this->message = $extra . ' ' . $this->httpCode[$code];
	}

	/**
	 * rest 风格 http 响应
	 * @return json 
	 */
	public function response()
	{
		$data = [
			'__coreError' => [
				'code'		=> $this->getCode(),
				'message'	=> $this->getMessage(),
				'infomations'	=> [
					'file'	=> $this->getFile(),
					'line'	=> $this->getLine(),
					'trace'	=> $this->getTrace(),
				],
			]
		];

		// Log::error(json_encode($data));

		header('Content-Type:Application/json; Charset=utf-8');
		die(json_encode($data, JSON_UNESCAPED_UNICODE));
	}


	/**
	 * rest 异常 http 响应
	 * @param  array $e 异常
	 * @return json    
	 */
	public static function responseErr($e)
	{
		$data = [
			'__coreError'	=> [
				'code'		=> 500,
				'message'	=> $e,
				'infomations'	=> [
					'file'	=> $e['file'],
					'line'	=> $e['line']
				]
			]
		];

		// Log::error(json_encode($data));
		
		// header('Content-Type:Application/json; Charset=utf-8');
		die(json_encode($data, JSON_UNESCAPED_UNICODE));
		exit();
	}
}