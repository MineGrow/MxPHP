<?php 

namespace App\Rest\Logics\Gateway;

use Core\Request;
use App\Rest\Logics\Gateway\Check;
use Core\Exceptions\CoreHttpException;

class CheckArguments extends Check
{	

	private $commonArgus = [
		'sign',
		'app_key',
		'timestamp',
		'nonce',
		'device_id'
	];

	// 校验公共参数
	public function doCheck(Request $request) {
		// 获取所有参数
		$params = $request->all();

		foreach ($this->commonArgus as $v) {
			if (! isset($params[$v]) || empty($params[$v])) {
				throw new CoreHttpException("Gateway's common argument [{$v}] is empty", 400);
			}
		}
	}

	
}