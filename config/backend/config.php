<?php 

return [
	'rest_response'	=> 'html',
	'view_dir'		=> 'backend',
	# 默认模块 小写
	'module'	=> [
		'backend'
	],
	# 路由默认配置 后台入口
	'route'						=> [
		'default_module'		=> 'backend',
		'default_controller'	=> 'main',
		'default_action'		=> 'index',
	],
];