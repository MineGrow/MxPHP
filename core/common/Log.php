<?php 

namespace Core\Common;

use \Exception;

/**
 * Log::debug()
 * Log::notice()
 * Log::warning()
 * Log::error()
 */
class Log
{
	private $buffer = [
		"\n"
		// "\n---date---|---level---|---pid---|---memory---|---log---\n"
	];

	// 支持的方法
	private $methodSupport = [
		'debug',
		'notice',
		'warning',
		'error'
	];

	// 可自定义的参数
	private $variableAllow = [
		'logPath',
		'logFileSize',
		'logFileName'
	];

	private $logFileName = '';

	private $finalFileName = '';

	private $logPath = '/tmp/logs/';

	// 日志文件大小 单位 M 
	private $logFileSize = 512;

	private $log = '';

	private static $_instance;

	private function __construct()
	{
		register_shutdown_function([$this, 'write']);
	}

	public function __set($name = '', $value = '')
	{
		if (!in_array($name, $this->variableAllow)) {
			throw new Exception("Operate is forbidden for this variable {$name}", 401);
		}
		$this->$name = $value;
	}

	public function __clone()
	{
		throw new Exception("Clone is forbidden", 401);
	}

	public static function getInstance()
	{
		if (!self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public static function __callStatic($method = '', $log = [])
	{
		$instance = self::getInstance();
		if (!in_array($method, $instance->methodSupport)) {
			throw new Exception("log method not support", 500);
		}
		$instance->decorate($method, $log);
		$instance->pushLog();
	}

	private function decorate($rank = 'info', $log = [])
	{
		if (!$log) {
			$log = [];
		} 
		$time = date('Y-m-d H:i:s', time());
		if (strtoupper(substr(PHP_OS,0,3))==='WIN') {
			$pid  = get_current_user();
		} else {
			$pid = posix_getpid();
		}
		$memoryUsage = round(memory_get_usage()/1024, 2) . 'kb';

		switch ($rank) {
			case 'debug':
                $rank = "\033[32m{$rank}\033[0m";
            break;
			case 'notice':
				$rank = "\033[36m{$rank} \033[0m";
			break;
			case 'warning':
				$rank = "\033[33m{$rank}\033[0m";
            break;
            case 'error':
                $rank = "\033[31m{$rank}\033[0m";
            break;
			default:
				# code...
				break;
		}

		$default = [
			$time,
			$rank,
			$pid,
			$memoryUsage
		];

		if ($log) {
			foreach ($log as &$v) {
				if (is_array($v)) {
					if (defined('JSON_UNESCAPED_UNICODE')) {
						$v = json_encode($v, JSON_UNESCAPED_UNICODE);
					} else {
						$v = json_encode($v);
					}
				}
			}
			unset($v);
		}

		$log = array_merge($default, $log);

		$tmp = '';
		foreach ($log as $k => $v) {
			if ($k === 0) {
				$tmp = "{$v}";
				continue;
			}
			$tmp .= " | {$v}";
		}
		$this->log = $tmp;
	}

	public function write()
	{
		if (!$this->buffer) {
			return ;
		}

		$msg = '';
		foreach ($this->buffer as $v) {
			$msg .= $v . PHP_EOL;
		}

		if (!file_exists($this->logPath)) {
			mkdir($this->logPath, 0777, true);
		}
		$this->finalFileName = $this->logPath . "{$this->logFileName}." . date('Y-m-d', time()) . '.log';

		if (file_exists($this->finalFileName)) {
			$filesize = filesize(realpath($this->finalFileName));
			if ($filesize >= $this->logFileSize*1024*1024) {
				$this->finalFileName = $this->logPath . "{$this->logFileName}." .date('Y-m-d', time());
				$this->finalFileName .= '.' . date('H-i-s', time()) . '.log';
			}
		}

		if (trim($msg)) {
			error_log(
				$msg,
				3,
				$this->finalFileName
			);
		}
	}

	public function pushLog()
	{
		$this->buffer[] = $this->log;
	}

}