<?php 

namespace App\Rest\Logics\Gateway;

use Core\Request;
use App\Rest\Logics\Gateway\Check;
use Core\Exceptions\CoreHttpException;

class CheckSign extends Check
{	
	// 不参与签名字段
	private $filiterField = [
		'sign',
		's'
	];

	// 校验公共参数
	public function doCheck(Request $request) {
		$appKeyMap 	= $request->env('app_key_map');
		$appKey 	= $request->request('app_key');
		$secretKey	= $appKeyMap[$appKey];
		$data 		= $request->all();
		ksort($data);
		$sign = array();
		foreach ($data as $key => $value) {
			if (!in_array($key, $this->filiterField) && (!empty($value) || $value == 0)) { // 过滤
				$sign[] = rowurldecode($value); 	// 解码过滤
			}
		}

		array_unshift($sign, $secretKey);
		array_push($sign, $secretKey);

		$string = implode('', $sign);
		$md5 	= md5(sha1($string));

		if ($data['sign'] !== $md5) {
			$info = 'invalid sign';
			// 开发环境输出正确签名信息
			if ($request->env('env')['env'] === 'develop') {
				$info = "invalid sign. info: string->{$string}, sign->{$md5}";
			}
			throw new CoreHttpException($info, 401);
			
		}
	}

	
}