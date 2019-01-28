<?php 

return [
	'nosql'			=> env('nosql')['support'],
	'redis'			=> [
		'host'		=> env('redis')['host'],
		'port'		=> env('redis')['port'],
		'password'	=> env('redis')['password'],
	],
	'memcached'		=> [
		'host'		=> env('memcached')['host'],
		'port'		=> env('memcached')['port'],
		'password'	=> env('memcached')['password'],
	],
	'memcache'		=> [
		'host'		=> env('memcache')['host'],
		'port'		=> env('memcache')['port'],
		'password'	=> env('memcache')['password'],
	],
	'mongoDB'		=> [
		'host'		=> env('mongoDB')['host'],
		'port'		=> env('mongoDB')['port'],
		'database'	=> env('mongoDB')['database'],
		'username'	=> env('mongoDB')['username'],
		'password'	=> env('mongoDB')['password'],
	],
];