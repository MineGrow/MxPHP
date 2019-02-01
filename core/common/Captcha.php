<?php 

namespace Core\Common;

/**
 * Twig 模板引擎
 */
class Captcha{

	private $charset = 'abcdefghkmnpqrstvwxyzABCDEFGHKMNPQRSTUVWXYZ23456789'; 	// 随机因子

	private $code;			// 验证码
	private $codelen = 4;	// 验证码长度
	private $width   = 130;	// 验证码宽度
	private $height  = 50;	// 验证码高度
	private $img;			// 图形资源句柄
	private $font;			// 指定的字体
	private $fontsize	= 20;	// 指定字体的大小
	private $fontcolor;			// 指定字体的颜色

	public function __construct()
	{
		$this->font = dirname(__FILE__) . '/font/Elephant.ttf';
	}

	// 生成随机码
	private function createCode() 
	{
		$_len = strlen($this->charset)-1;
		for ($i=0; $i < $this->codelen; $i++) { 
			$this->code .= $this->charset[mt_rand(0, $_len)];
		}
	}

	// 生成背景图
	private function createBg()
	{
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
		imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
	}

	// 生成文字
	private function createFont()
	{
		$_x = $this->width / $this->codelen;
		for ($i=0; $i < $this->codelen; $i++) { 
			$this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x*$i+mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
		}
	}

	// 生成线条、雪花
	private function createLine()
	{
		// 线条
		for ($i=0; $i < 6; $i++) { 
			$color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
		}
		// 雪花
		for ($i=0; $i < 50; $i++) { 
			$color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
		}
	}

	// 输出图片
	private function output() {
		header('Content-type:image/png');
		imagepng($this->img);
		imagedestroy($this->img);
	}

	// 对外生成
	public function captchaImg()
	{
		$this->createBg();
		$this->createCode();
		$this->createLine();
		$this->createFont();
		$this->output();
	}

	// 获取验证码
	public function getCaptcha()
	{
		return strtolower($this->code);
	}

	// 验证码校验
	public function checkCaptcha($captcha)
	{
		$returnArr = array('status' => 0, 'msg' =>'checkCaptcha_error');
		if (isset($_SESSION['authnum_session'])) {
			if (strtolower($captcha) != $_SESSION['authnum_session']) {
				$returnArr['status'] = -1;
				$returnArr['msg']    = $_SESSION['authnum_session'];
			} else {
				$returnArr['status'] = 1;
				$returnArr['msg']    = 'success';
			}
		} else {
			$returnArr['msg'] = 'authnum_session_error';
		}
		return $returnArr;
	}

	public function __destruct()
	{
		
	}
}