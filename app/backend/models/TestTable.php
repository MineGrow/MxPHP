<?php 

namespace App\Backend\Models;

use Core\App;
use Core\Orm\Model;
use Core\Exceptions\CoreHttpException;

/**
 * model 演示
 */
class TestTable extends Model
{
	public function modelFindOneDemo()
	{
		$where = [
			'id'	=> ['>=', 2]
		];

		$res = $this->where($where)
					-> orderBy('id asc')
					->findOne();
		return $res;
	}
	public function modelFindAllDemo()
    {
        $where = [
            'id'   => ['>=', 2],
        ];
        $res      = $this->where($where)
                             ->orderBy('id asc')
                             ->limit(5)
                             ->findAll(['id','create_at']);
        $sql      = $this->sql;

        // return $sql;
        return $res;
    }

    /**
     * sql 操作示例
     *
     * Insert
     *
     * @return void
     */
    public function modelSaveDemo()
    {
        $data = [
            'name' => '123',
            // 'create_at'=> time(),
        ];
        $res      = $this->save($data);
        $sql      = $this->sql;

        return $sql;
        return $res;
    }

    /**
     * sql 操作示例
     *
     * Delete
     *
     * @return void
     */
    public function modelDeleteDemo()
    {
        $where = [
            'id'   => ['>=', 1],
        ];
        $res      = $this->where($where)
                             ->delete();
        $sql      = $this->sql;

        // return $sql;
        return $res;
    }

    /**
     * sql 操作示例
     *
     * Update
     *
     * @return void
     */
    public function modelUpdateDemo($data = [])
    {
        $where = [
            'id'   => ['>=', 1],
        ];
        $res      = $this->where($where)
                         ->update($data);
        $sql      = $this->sql;

        // return $sql;
        return $res;
    }

    /**
     * sql 操作示例
     *
     * Count
     *
     * @return void
     */
    public function modelCountDemo()
    {
        $where = [
            'id'   => ['>=', 2],
        ];
        $res      = $this->where($where)
                         ->count('id as CountId');
        $sql      = $this->sql;

        // return $sql;
        return $res;
    }

    /**
     * sql 操作示例
     *
     * Sum
     *
     * @return void
     */
    public function modelSumDemo()
    {
        $where = [
            'id'   => ['>=', 1],
        ];
        $res      = $this->where($where)
                             ->sum('id as SumId');
        $sql      = $this->sql;

        return $sql;
        return $res;
    }

    /**
     * sql 操作示例
     *
     * query
     *
     * @return void
     */
    public function modelQueryDemo()
    {
        $res      = $this->query('SELECT `id` as `SumId` FROM `mx_user` WHERE `id` >= 1');
        $sql      = $this->sql;

        // return $sql;
        return $res;
    }
}