<?php 

namespace App\Backend\Logics\Access;

use Core\Request;
use Core\App;

class CheckController extends Check
{	
	private $notLogin = ['login', 'register'];

	public function doCheck(Request $request)
	{
		// 先判断 path_info 里面是否带参数
		$controller = parse_pathinfo($request->path_info, 'controller') ?: $request->get('contoller');
		if ($controller && in_array(strtolower($controller), $this->notLogin)) {
			// 不需要登录即可访问
			return -1;
		}
		
		return 0;
	}
}