<?php 

namespace Core\Handles;

use Core\App;

Interface Handle
{
	/**
	 * 注册处理机制
	 * 
	 * @param  App    $app 对象
	 * @return mixed      
	 */
	public function register(App $app);
}