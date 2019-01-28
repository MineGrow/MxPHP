<?php 

namespace App\Rest\Controllers;

use Core\App;
use Core\Common\Log;
use Core\Common\Smarty;

/**
 * Index
 */
class Index
{
	
	public function __construct()
	{
		# code...
	}

	public function hello()
	{
		return "Hello Mx PHP";
	}

	public function test()
	{
		//http://www.mxphp.cos/index.php?module=Backend&contoller=Index&action=test&username=test&password=123456789123&code=123
		$request = App::$container->get('request');
		$request->check('username', 'require');
		$request->check('password', 'length', 12);
		$request->check('code', 'number');

		return [
			'username'	=> $request->get('username', 'default value')
		];
	}

	/**
	 * 框架内部调用
	 * @example http://www.mxphp.cn/index.php?module=Backend&contoller=Index&action=micro
	 * @return json
	 */
	public function micro()
	{
		return App::$app->get('backend/index/hello', ['user' => 'mxphp']);
	}

	/**
	 * 容器内获取实例
	 * @return void 
	 */
	public function getInstanceFromContainerDemo()
	{	
		// 请求对象
		App::$container->get('request');
		// 配置对象
		App::$container->getSingle('config');

		return [];
	}

	/**
	 * 日志演示
	 * @return void 
	 */
	public function log()
    {
        Log::debug('Mx PHP');
        Log::notice('Mx PHP');
        Log::warning('Mx PHP');
        Log::error('Mx PHP');

        return [];
    }

    /**
     * 容器内获取nosql实例演示
     * @return void
     */
    public function nosqlDemo()
    {
        // redis对象
        App::$container->getSingle('redis');
        // memcahe对象
        // App::$container->getSingle('memcached'); # unix
        App::$container->getSingle('memcache');	 # windows
        // mongodb对象
        // App::$container->getSingle('mongoDB');

        return [];
    }

    public function twig_test()
    {
    	$this->twigConfig = env('twig');
		$this->templatePath = App::$app->rootPath . $this->twigConfig['template'];
		$this->cachePath    = App::$app->rootPath . $this->twigConfig['cache'];
		$loader = new \Twig_loader_Filesystem($this->templatePath);
		$twig   = new \Twig_Environment($loader,[
			'cache' 		=> $this->cachePath,
			'debug' 		=> $this->twigConfig['debug'],
			'auto_reload'	=> $this->twigConfig['auto_reload']
		]);
		$twig->render('index.twig');
    }

    public function smarty_test()
    {
    	$smarty = new Smarty;
    	$data = ['title'=>'标题', 'name' => 'World'];
    	$tpl  = 'index';
    	$smarty->display($tpl, $data);
    }
}