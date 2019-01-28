<?php 
/**
 * 框架助手函数文件
 */
use Core\App;


/**
 * 获取环境参数
 * @param  string $paramName 参数名
 * @return mixed            
 */
function env($paramName = '')
{
	return App::$container->getSingle('envt')->env($paramName);
}

/**
 * 浏览器打印数据
 */
if (! function_exists('dump')) {
	function dump($data = [])
	{
		ob_start();
		var_dump($data);
		$output = ob_get_clean();
		if (!extension_loaded('xdebug')) {
			$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
			$output = '<prev>' . htmlspecialchars($output, ENT_QUOTES) . '</prev>';
		}
		echo ($output);
		return null;
	}
}

/**
 * 日志
 */
if (! function_exists('log')) {
	/**
	 * 日志记录
	 * @param  array  $data     log数据
	 * @param  string $fileName log文件名 绝对路径
	 * @return void           
	 */
	function log($data = [], $fileName = 'debug')
	{
		$time = date('Y-m-d H:i:s', time());
		error_log(
			"[{$time}]: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n",
			3,
			$fileName . '.log'
		);
	}
}

if (! function_exists('debug')) {
	function debug($data = [])
	{
		header('Content-Type:Application;');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}