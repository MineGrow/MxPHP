<?php 

namespace App\Backend\Logics\Gateway;

use Core\App;
// use App\Backend\Logics\Gateway\CheckAccessToken;
use App\Backend\Logics\Gateway\CheckFrequent;
use App\Backend\Logics\Gateway\CheckArguments;
use App\Backend\Logics\Gateway\CheckSign;
use App\Backend\Logics\Gateway\CheckAuthority;
use App\Backend\Logics\Gateway\CheckRouter;

// 网关实体入口
class Entrance
{	

	public function __construct()
	{
		// 初始化一个：必传参数校验的check
        $checkArguments   =  new CheckArguments();
        // 初始化一个：令牌校验的check
        $checkAppkey      =  new CheckAppkey();
        // 初始化一个：访问频次校验的check
        $checkFrequent    =  new CheckFrequent();
        // 初始化一个：签名校验的check
        $checkSign        =  new CheckSign();
        // 初始化一个：访问权限校验的check
        $checkAuthority   =  new CheckAuthority();
        // 初始化一个：网关路由规则
        $checkRouter      =  new CheckRouter();

        // 构成对象链
        $checkArguments->setNext($checkAppkey)
        				->setNext($checkFrequent)
        				->setNext($checkSign)
        				->setNext($checkAuthority)
        				->setNext($checkRouter);

       	// 启动网关
       	$checkArguments->start(
       		App::$container->get('request')
       	);

	}

	
}