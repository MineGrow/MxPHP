<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;

/**
 * 框架日志处理
 */
class ExceptionHandle implements Handle
{
	
	private $mode = 'fmp';

	private $info = [];

	public function __construct()
	{
		# code
	}

	public function register(App $app)
	{
		set_exception_handler([$this, 'exceptionHandler']);
	}

	public function exceptionHandler($exception)
	{
		$this->info = [
			'code'		=> $exception->getCode(),
			'message'	=> $exception->getMessage(),
			'file'		=> $exception->getFile(),
			'line'		=> $exception->getLine(),
			'trace'		=> $exception->getTrace(),
			'previous'	=> $exception->getPrevious()
		];
		$this->end();
	}

	private function end()
	{
		switch ($this->mode) {
			case 'swoole':
				CoreHttpException::responseErrSwoole($this->info);
				break;
			
			default:
				CoreHttpException::responseErr($this->info);
				break;
		}
	}
}