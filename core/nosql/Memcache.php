<?php 

namespace Core\Nosql;

use Core\App;
use Memcache as rootMemcache;

/**
 * Memcache 操作类
 */
class Memcache
{
	public static function init()
	{
		$config = App::$container->getSingle('config');
		$config = $config->config['memcache'];
		$memcache  = new rootMemcache();
		$memcache->connect($config['host'], $config['port']);
		return $memcache;
	}
}