<?php 

namespace App\Backend\Models;

use Core\App;
use Core\Orm\Model;
use Core\Exception\CoreHttpException;

class SessionTable extends Model 
{

	public function findOne($where=[], $select=[]) 
	{
		if (!is_array($where)) {
			$where = ['id' => $where];
		}
		$result = $this->where($where)
						->select($select)
						->findOne();
		return $result;
	}

	// 增加超时时间
	public function activate($sessionid, $data)
	{
		$where = ['key' => $sessionid];
		$result = $this->where($where)->update($data);
		return $result;
	}
}