<?php 

namespace Core\Handles;

use Core\App;
use Core\Handles\Handle;
use Core\Exceptions\CoreHttpException;
use Core\Common\Log;

/**
 * 框架日志处理
 */
class LogHandle implements Handle
{
	
	private $logConfig = '';

	public function register(App $app)
	{
		new LogHandle();
	}

	public function __construct()
	{
		$this->logConfig = env('log');
		if (empty($this->logConfig)) {
			throw new CoreHttpException(400, 'log config is not defined');
		}
		if (! isset($this->logConfig['path'])) {
			throw new CoreHttpException(400, 'log path is not defined');
		}
		if (! isset($this->logConfig['name'])) {
			throw new CoreHttpException(400, 'log name is not defined');
		}
		if (! isset($this->logConfig['size'])) {
			throw new CoreHttpException(400, 'log size is not defined');
		}

		$instance = Log::getInstance();
		$instance->logFileName = $this->logConfig['name'];
		$instance->logPath = App::$app->rootPath . $this->logConfig['path'];
		$instance->logFileSize = $this->logConfig['size'];
	}
}