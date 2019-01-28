<?php 

namespace Core\Router;

use Core\Router\Router;

/**
 * 路由策略接口
 */
Interface RouterInterface
{
	public function route(Router $entrance);
}