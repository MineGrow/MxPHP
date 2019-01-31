<?php 

namespace App\Backend\Logics\Access;

use Core\Request;

abstract class Check
{	
	// 下一个 check 实体
	private $nextCheckInstance;

	abstract public function doCheck(Request $request);

	// 设置责任链上的下一个对象
	public function setNext(Check $check)
	{
		$this->nextCheckInstance = $check;
		return $check;
	}

	// 启动
	public function start(Request $request)
	{
		$res = $this->doCheck($request);
		// 调用下一个对象
		if (!empty($this->nextCheckInstance)) {
			$this->nextCheckInstance->start($request);
		}
		return $res;
	}
}