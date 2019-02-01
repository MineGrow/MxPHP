<?php 

namespace App\Backend\Controllers;

use Core\App;
use Core\Common\Log;
use Core\Common\Smarty;
use Core\Orm\DB;
use PDO;

class Login
{
	
	public function __construct()
	{
		# code...
	}

	public function index()
	{
		$smarty = new Smarty;
		$data = ['title' => '欢迎登录 MxPHP 系统'];
		$smarty->display('index', $data);
	}

	public function login()
	{
		$returnArr = array('status' => 0, 'msg' => 'login error');
		$request = App::$container->get('request');
		// 获取 POST 参数
		$postData = $request->all();
		if (!empty($postData)) {

			// 验证码验证
			$captcha_res = check_captcha($postData['captcha']);
			if ($captcha_res['status'] != '1') {
				$returnArr['status'] = -2;
				$returnArr['msg'] = '验证码错误|'.$captcha_res['msg'];
			} else {
				// 验证用户名和密码\
				$reuturnArr['status'] = 1;
				
				$returnArr['msg'] = '验证用户名和密码';
				
			}

		} else {
			$returnArr['status'] = -1;
			$returnArr['msg'] = '参数错误';
		}
		response_json($returnArr);
	}

}