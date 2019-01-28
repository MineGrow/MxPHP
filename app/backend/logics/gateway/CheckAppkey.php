<?php 

namespace App\Backend\Logics\Gateway;

use Core\Request;
use App\Backend\Logics\Gateway\Check;
use Core\Exceptions\CoreHttpException;

class CheckAppkey extends Check
{	

	public function doCheck(Request $request) {
		$appKeyMap = $request->env('app_key_map');
		$appKey    = $request->request('app_key');

		if (isset($appKeyMap[$appKey])) {
			return ;
		}
		throw new CoreHttpException('app_key is not found', 404);
		
	}

	
}