<?php 

namespace App\Backend\Logics\Access;

use Core\Request;

class CheckController extends Check
{	
	private $notLogin = ['login', 'register'];

	public function doCheck(Request $request)
	{
		$controller = strtolower($request->request('contoller'));
		
		if ($controller && in_array($controller, $this->notLogin)) {
			// 不需要登录即可访问
			return -1;
		}
		
		return 0;
	}
}