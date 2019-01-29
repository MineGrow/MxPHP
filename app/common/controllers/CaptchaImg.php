<?php 

namespace App\Common\Controllers;


use Core\Common\Captcha;

/**
 * 验证码
 */
class CaptchaImg
{
	
	function __construct()
	{
		# code...
	}

	public function index()
	{
		$Captcha = new Captcha();
		$Captcha->captchaImg();
		$_SESSION['authnum_session'] = $Captcha->getCaptcha();
	}

	public function getAuthnum()
	{
		echo $_SESSION['authnum_session'];
	}
}


