<?php 

namespace Core\Nosql;

use Core\App;
use Redis as rootRedis;

/**
 * redis 操作类
 */
class Redis
{
	public static function init()
	{
		$config = App::$container->getSingle('config');
		$config = $config->config['redis'];
		$redis  = new rootRedis();
		$redis->connect($config['host'], $config['port']);
		return $redis;
	}
}