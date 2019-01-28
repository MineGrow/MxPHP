<?php 

return [
	# 应用目录名称
	'application_folder_name'	=> 'app',
	# 脚本目录名称	
	'jobs_folder_name'			=> 'jobs',
	# 默认模块
	'module'					=> [
		'backend'
	],
	# 路由默认配置
	'route'						=> [
		'default_module'		=> 'backend',
		'default_controller'	=> 'index',
		'default_action'		=> 'hello',
	],
	# 响应结果是否使用 rest 风格
	'rest_response'				=> 'rest',
	#默认时区
	'default_timezone'			=> 'Asia/Shanghai',
	# 前端模板文件夹
	'view_dir'					=> 'common',

];