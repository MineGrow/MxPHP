<?php 

namespace Core\Traits;

trait GlobalConstant
{
	public function registerGlobalConst()
	{
		define('NOW_TIME', time());
		define('NOW_MICROTIME', microtime(true));
	}
}