<?php 

namespace Core\Common;

require_once '../vendor/autoload.php';

use Core\App;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;


/**
 * Twig 模板引擎
 */
class Twig{
	private $twigConfig = '';
	private $templatePath = '';
	private $cachePath = '';

	public $view;
	public $data;
	public $twig;

	public function __construct($view, $data)
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

		$this->view = $view;
		$this->data = $data;
	}

	public static function render($view, $data = [])
	{
		return new Twig($view, $data);
	}

	public function __destruct()
	{
		$this->twig->display($this->view, $this->data);
	}
}