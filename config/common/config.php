<?php 

return [
	'rest_response'	=> 'html',
	'view_dir'		=> 'common',
	# 默认模块 小写
	'module'	=> [
		'common'
	],
	# 路由默认配置
	'route'						=> [
		'default_module'		=> 'common',
		'default_controller'	=> 'index',
		'default_action'		=> 'index',
	],
];