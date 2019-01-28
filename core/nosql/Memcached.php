<?php 

namespace Core\Nosql;

use Core\App;
use Memcached as rootMemcached;

/**
 * Memcached 操作类
 */
class Memcached
{
	public static function init()
	{
		$config = App::$container->getSingle('config');
		$config = $config->config['memcached'];
		$memcached  = new rootMemcached();
		$memcached->connect($config['host'], $config['port']);
		return $memcached;
	}
}