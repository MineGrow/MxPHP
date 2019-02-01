<?php 
/**
 * 框架助手函数文件
 */
use Core\App;
use Core\Common\Captcha;

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

if (! function_exists('get_client_ip')) {
	// 获取请求 IP
	function get_client_ip($type=0)
	{
		$type = $type ? 1 : 0;
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        $long = sprintf("%u",ip2long($ip));
    	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    	return $ip[$type];
	}
}

if (! function_exists('parse_pathinfo')) {
	// 解析 PATH_INFO 参数
	function parse_pathinfo($path, $field='module')
	{
		if ($path) {
			$temp = rtrim(ltrim($path, '/'), '/');

			$path_info = explode('/', $temp);
			$fields = ['module' => 0, 'controller' => 1, 'action' => 2];
	    	return (isset($fields[$field]) && isset($path_info[$fields[$field]])) ? $path_info[$fields[$field]] : '';
		} else {
			return '';
		}

	}
}

if (! function_exists('response_json')) {
	// 返回 json 数据
	function response_json($data)
	{
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
	}
}

if (! function_exists('check_captcha')) {
	// 验证码校验
	function check_captcha($captcha)
	{
		$OCaptcha = new Captcha();
		$result = $OCaptcha->checkCaptcha($captcha);
		return $result;
	}
}