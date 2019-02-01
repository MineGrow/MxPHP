<?php 

namespace App\Backend\Logics\Access;

use Core\App;
use App\Backend\Logics\Access\CheckSessionID;
use App\Backend\Logics\Access\CheckController;

/**
 * Access 入口
 * res 返回值：
 * -1： 不需要登录
 * 0 ： 需要登录
 * 大于0 ： 已登录用户ID
 */
class Entrance
{
	
	public function __construct(App $app)
	{
		$CheckController = new CheckController();
		$checkSessionID = new CheckSessionID();

		$CheckController->setNext($checkSessionID);

		$res = $CheckController->start(
			App::$container->get('request')
		);
		switch ($res) {
			case '-1':
				# 不需要登录，直接访问
				break;
			case '0':
				# 需要登录
				header("Location:/Backend/Login/Index/");exit();
				break;
			default:
			// 设置 App userInfo 信息
				$app->userInfo['backend'] = $res;
				break;
		}
	}
}