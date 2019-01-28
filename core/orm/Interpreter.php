<?php 

namespace Core\Orm;

use Core\Exceptions\CoreHttpException;

/**
 * Sql 解释器
 */
trait Interpreter
{
	// 查询条件
	private $where 		= '';

	// 查询参数
	public $params  	= [];

	// 排序条件
	private $orderBy 	= '';

	// 限制条件
	private $limit		= '';

	// 查询偏移量
	private $offset		= '';

	// 表名称
	private $sql 		= '';

	/**
	 * 插入一条数据
	 *
	 * @param  array  $data 数据
	 *
	 * @return mixed       
	 */
	public function insert($data = [])
	{
		if (empty($data)) {
			throw new CoreHttpException("argument data is null", 400);
		}
		$fieldString = '';
		$valueString = '';
		$i = 0;
		foreach ($data as $k => $v) {
			if ($i === 0) {
				$fieldString .= "`{$k}`";
				$valueString .= ":{$k}";
				$this->params[$k] = $v;
				++$i;
				continue;
			}
			$fieldString .= " , `{$k}`";
			$valueString .= " , :{$k}";
			$this->params[$k] = $v;
			++$i;
		}
		unset($k);
		unset($v);

		$this->sql = "INSERT INTO `{$this->tableName}` ({$fieldString}) VALUES ({$valueString})";
	}

	/**
	 * 删除数据
	 *
	 * @var void
	 */
	public function del($data = [])
	{
		$this->sql = "DELETE FROM `{$this->tableName}`";
	}

	/**
	 * 更新一条数据
	 *
	 * @param  array  $data 数据
	 *
	 * @return void       
	 */
	public function updateData($data = [])
	{
		if (empty($data)) {
			throw new CoreHttpException("argument data is null", 400);
		}
		$set = '';
		$dataCopy = $data;

		$pop = array_pop($dataCopy);
		foreach ($data as $k => $v) {
			if ($v === $pop) {
				$set .= "`{$k}` = :$k";
				$this->params[$k] = $v;
				continue;
			}
			$set .= "`{$k}` = :$k,";
			$this->params[$k] = $v;
		}

		$this->sql = "UPDATE `{$this->tableName}` SET {$set}";
	}

	/**
	 * 查询一条数据
	 *
	 * @param  array  $data 查询字段
	 *
	 * @return mixed       
	 */
	public function select($data = [])
	{
		$field = '';
		$count = count($data);
		switch ($count) {
			case 0:
				$field = '*';
				break;
			case 1:
				if (! isset($data[0])) {
					throw new CoreHttpException("data format invalid", 400);
				}
				$field = "`{$data[0]}`";
				break;
			default:
				$last = array_pop($data);
				foreach ($data as $v) {
					$field .= "{$v},";
				}
				$field .= $last;
				break;
		}
		$this->sql = "SELECT $field FROM `{$this->tableName}`";
	}

	/**
	 * where 条件
	 *
	 * @param  array  $data 数据
	 *
	 * @return void       
	 */
	public function where($data = [])
	{
		if (empty($data)) {
			return;
		}
		$conditions = [];
		foreach ($data as $field => $dval) {
			if (is_array($dval)) {
				if (count($dval) > 0) {
					$operator = strtoupper($dval[0]);
					$params   = $dval[1];
					if (is_array($dval[1])) {
						$marks = [];
						// 同一个字段绑定参数
						foreach ($dval[1] as $dk => $dv) {
							if ($dv != '') {
								if ($operator == 'RANGE') {
									// 附加参数只对 RANGE 方式有效
									if ($dk == 2) {
										// 附加参数
										$_operator = $dv;
									} else {
										$marks[] = ":{$field}_{$dk}";
										$this->params["{$field}_{$dk}"] = $dv;
									}
								} else {
									$marks[] = ":{$field}_{$dk}";
									$this->params["{$field}_{$dk}"] = $dv;
								}
							}
						}

						// 操作符判断 {{{
						if (in_array($operator, array('IN', 'NOT IN'))) {
							// IN 查询
							$conditions[] = " `{$field}` {$operator} (".implode(',', $marks).")";
						} else if ($operator == 'RANGE') {
							// 范围搜索，一般用于时间段查询 (>= MIN AND <= MAX)
							if (isset($_operator)) {
								// 变种查询1：必须带第三个参数 id => ['RANGE', ['6', '3', 'OR']], // ID >= 6 OR ID <= 3
								$conditions[] = " (`{$field}` >= {$marks[0]} {$_operator} `{$field}` <= {$marks[1]})";
							} else {
								// 'producttime' 	=> ['RANGE', ['2018/01/01', '2019/1/3']], // 范围搜索 >= MIN AND <= MAX
								if (isset($marks[0])) {
									$conditions[] = " `{$field}` >= {$marks[0]}";
								}
								if(isset($marks[1])) {
									$conditions[] = " `{$field}` <= {$marks[1]}";
								}
							}
						} else {
							// 
						}
						// }}}
					} else {
						if ($operator == 'LIKE') {
							$this->params[$field] = '%'.$dval[1].'%';
						} else {
							$this->params[$field] = $dval[1];
						}
						$conditions[] = " `{$field}` {$operator} :{$field}";
					}
				}
			} else {
				$conditions[] = " `{$field}` = :{$field}";
				$this->params[$field] = $dval;
			}
		}
		// 拼接 WHERE 条件
		if (!empty($conditions)) {
			$this->where = " WHERE " . implode(' AND ', $conditions);
		}
		return $this;
		// 调试 debug 数据{{{
		var_dump($this->where);
		print_r($this->params);
		exit();
		// }}}
	}

	/**
	 * orderBy 排序
	 *
	 * @param  string $sort 排序
	 *
	 * @return mixed       
	 */
	public function orderBy($sort = '')
	{
		if (! is_string($sort)) {
			throw new CoreHttpException("argu is not string", 400);
		}
		$this->orderBy = " ORDER BY {$sort}";
		return $this;
	}

	/**
	 * limit 限制条件
	 *
	 * @param  integer $start 开始位置
	 * @param  integer $len   长度
	 *
	 * @return mixed         
	 */
	public function limit($start = 0, $len = 0)
	{
		if (! is_numeric($start) || (! is_numeric($len))) {
			throw new CoreHttpException(400);
		}
		if ($len === 0) {
			$this->limit = " limit {$start}";
			return $this;
		}
		$this->limit = " limit {$start},{$len}";
		return $this;
	}

	/**
	 * [countColumn description]
	 *
	 * @param  string $data 查询的字段
	 *
	 * @return mixed       
	 */
	public function countColumn($data = '')
	{
		$data 	= empty($data) ? '*' : $data;
		$field  = $this->packColumn('count', $data);

		$this->sql = "SELECT $field FROM `{$this->tableName}`";
	}

	/**
	 * [sumColumn description]
	 *
	 * @param  string $data 查询的字段
	 *
	 * @return mixed
	 */
	public function sumColumn($data = '')
	{
		$data 	= empty($data) ? '*' : $data;
		$field  = $this->packColumn('sum', $data);

		$this->sql = "SELECT $field FROM `{$this->tableName}`";
	}

	/**
	 * 组装 MySQL 函数字段
	 *
	 * @param  string $functionName mysql 函数名称
	 * @param  string $data         参数
	 *
	 * @return string               
	 */
	public function packColumn($functionName = '', $data = '')
	{
		$field 	= "{$functionName}(`{$data}`)";
		preg_match_all('/(\w+)\sas/', $data, $matchColumn);
		if (isset($matchColumn[1][0]) || (! empty($matchColumn[1][0]))) {
			$matchColumn = $matchColumn[1][0];
			$field = "{$functionName}(`{$matchColumn}`)";
			preg_match_all('/as\s/', $data, $match);
			if (isset($match[1][0]) || (!empty($match[1][0]))) {
				$match = $match[1][0];
				$field .= " as `{$match}`";
			}
		}

		return $field;
	}

	public function querySql($sql = '')
	{
		if (empty($sql)) {
			throw new CoreHttpException("sql is empty", 400);
		}
		$this->sql = $sql;
	}
}