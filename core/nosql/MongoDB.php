<?php 

namespace Core\Nosql;

use Core\App;
use MongoDB\Client;

/**
 * redis 操作类
 */
class MongoDB
{
	public static function init()
	{
		$config = App::$container->getSingle('config');
		$config = $config->config['mongoDB'];
		$client = new client(
			"{$config['host']}:{$config['port']}",
			[
				'database'	=> $config['database'],
				'username'	=> $config['username'],
				'password'	=> $config['password']
			]
		);
		$database = $client->selectDatabase($config['database']);
		return $database;
	}
}