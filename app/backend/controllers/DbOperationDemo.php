<?php 

namespace App\Backend\Controllers;

use Core\App;
use Core\Orm\DB;

class DbOperationDemo
{
	public function __construct()
	{

	}

	// 查询一条记录
	public function dbFindDemo()
	{
		$where = [
			'id'	=> ['>=', 0],
		];

		$instance = DB::table('user');
		$res = $instance->where($where)
						->orderBy('id asc')
						->findOne();
		$sql = $instance->sql;
		$database = $instance->masterSlave;

		return [
			'db'	=> $database,
			'sql'	=> $sql,
			'res'	=> $res
		];
	}

	public function dbFindAllDemo()
	{
		$where = [
			'id'	=> ['>=', 0],
		];

		$instance = DB::table('user');
		$res = $instance->where($where)
						->orderBy('id asc')
						->limit(2)
						->findAll(['id', 'username']);
		$sql = $instance->sql;
		$database = $instance->masterSlave;

		return [
			'db'	=> $database,
			'sql'	=> $sql,
			'res'	=> $res
		];
	}

	public function dbSaveDemo()
	{
		DB::beginTransaction();
		$user = DB::table('user');
		$userId = $user->save([
			'username' 	=> 'mxphp', 
			'password'	=> 'mxphp'
		]);

		$test = DB::table('test');
		$testId = $test->save(['name' => '11']);

		if (!$testId) {
			DB::rollBack();
		} else {
			DB::commit();
		}

		return [
			'db'	=> $user->masterSlave,
			'sql'	=> $user->sql,
			'res'	=> [
				'user_id'	=> $userId,
				'test_id'	=> $testId
			]
		];
	}

	public function dbDeleteDemo()
	{
		$where = [
			'id'	=> ['>', 2]
		];

		$instance = DB::table('user');
		$res = $instance->where($where)->delete();
		$sql = $instance->sql;

		return $res;
	}

	public function dbUpdateDemo()
	{
		$where = [
			'id'	=> ['>=', 2]
		];

		$instance = DB::table('user');
		$res = $instance->where($where)
						->update([
							'username'	=> 'pxda'
						]);
		// $sql = $instance->sql;

		return $res;
	}

	public function dbCountDemo()
	{
		$where = [
			'id'	=> ['>=', 1]
		];
		$instance = DB::table('user');
		$res = $instance->where($where)
						->count('id as CountId');
		// $sql = $instance->sql;

		return $res;
	}

	public function dbSumDemo()
	{
		$where = [
			'id'	=> ['>=', 1]
		];
		$instance = DB::table('user');
		$res = $instance->where($where)
						->sum('id as SumId');
		// $sql = $instance->sql;

		return $res;
	}

	public function dbQueryDemo()
	{
		$instance 	= DB::table('user');
		$res	  	= $instance->query("SELECT id as sumid FROM mx_user WHERE id >= 1");
		$sql 		= $instance->sql;
		// echo $sql;
		return $res;
	}
}