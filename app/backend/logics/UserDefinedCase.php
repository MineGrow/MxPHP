<?php 

namespace App\Backend\Logics;

use Core\App;

/**
 * 用户自定义处理机制
 */
class UserDefinedCase
{
	
	private $map = [
		// 加载自定义网关
		// 'App\Backend\Logics\Gateway\Entrance'
	];

	public function __construct(App $app)
	{
		foreach ($this->map as $v) {
			new $v($app);
		}
	}

}