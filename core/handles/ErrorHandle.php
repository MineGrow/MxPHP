<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;

/**
 * 错误处理机制
 */
class ErrorHandle implements Handle
{
	// 运行模式
	private $mode = 'fmp';

	// 错误信息
	private $info = [];

	public function __construct()
	{

	}

	// 注册错误处理机制
	public function register(App $app)
	{
		$this->mode = $app->runningMode;

		set_error_handler([$this, 'errorHandler']);

		register_shutdown_function([$this, 'shutdown']);
	}

	// 脚本结束
	public function shutdown()
	{
		$error = error_get_last();
		if (empty($error)) {
			return ;
		}
		$this->info = [
			'type'		=> $error['type'],
			'message'	=> $error['message'],
			'file'		=> $error['file'],
			'line'		=> $error['line']
		];
		$this->end();
	}

	/**
     * 错误捕获
     *
     * @param  int    $errorNumber  错误码
     * @param  int    $errorMessage 错误信息
     * @param  string $errorFile    错误文件
     * @param  string $errorLine    错误行
     * @param  string $errorContext 错误文本
     * @return mixed               　
     */
	public function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine, $errorContext)
	{
		$this->info = [
			'type'		=> $errorNumber,
			'message'	=> $errorMessage,
			'file'		=> $errorFile,
			'line'		=> $errorLine,
			'context'	=> $errorContext,
		];
		$this->end();
	}

	private function end()
	{
		switch ($this->mode) {
			case 'swoole':
				CoreHttpException::responseErr($this->info);
				break;
			
			default:
				CoreHttpException::responseErr($this->info);
				break;
		}
	}
}