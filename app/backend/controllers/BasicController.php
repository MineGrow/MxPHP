<?php 

namespace App\Backend\Controllers;

use Core\App;
use Exception;
use Core\Orm\DB;

/**
 * 后台父类
 */
class BasicController
{
      public function __construct(App $app)
      {

      }

      // 检测是否需要登录才可访问
      public function checkAccess()
      {
            
      }
      
}