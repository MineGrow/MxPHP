<?php 

namespace App\Backend\Logics\Gateway;

use Core\Request;
use App\Backend\Logics\Gateway\Check;
use Core\Exceptions\CoreHttpException;

// 检验接口访问效率
class CheckFrequent extends Check
{	
	// 限定时间段 单位秒
	private $timsScope = 60;

	// 限定次数
	private $times = 60;

	// 校验公共参数
	public function doCheck(Request $request) {
		$key = 'Gateway-client-ip:' . $request->clientIp;
		$redis = App::$container->getSingle('redis');
		$value = $redis->get($key);

		if (!$value) {
			$redis->setex($key, $this->timsScope, 0);
		}

		if ($value >= $this->times) {
			throw new CoreHttpException("too many request per {$this->timsScope} seconds", 1);
		}
		$redis->incr($key);
	}

	
}