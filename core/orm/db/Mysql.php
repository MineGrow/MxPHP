<?php 

namespace Core\Orm\Db;

use Core\Orm\DB;
use Core\Exceptions\CoreHttpException;
use PDO;

/**
 * mysql 实体类
 */
class Mysql
{
	private $dbhost 	= '';
	private $dbname 	= '';
	private $dsn		= '';
	private $username 	= '';
	private $password	= '';
	private $pdo 		= '';

	/**
	 * 预处理实例
	 * 代表一条预处理语句，并在该语句被执行后代表一个相关的结果集
	 * @var object
	 */
	private $pdoStatement = '';

	/**
	 * 初始化连接
	 *
	 * @param string $dbhost   host
	 * @param string $dbname   database name
	 * @param string $username database username
	 * @param string $password database password
	 */
	public function __construct(
		$dbhost = '',
		$dbname = '',
		$username = '',
		$password = ''
	) 
	{
		$this->dbhost 	= $dbhost;
		$this->dbname 	= $dbname;
		$this->dsn 	 	= "mysql:dbname={$this->dbname};host={$this->dbhost};";
		$this->username = $username;
		$this->password = $password;

		$this->connect();
	}

	private function connect()
	{
		$this->pdo = new PDO(
			$this->dsn,
			$this->username,
			$this->password
		);
	}

	public function __get($name = '')
	{
		return $this->$name;
	}

	public function __set($name = '', $value = '')
	{
		$this->$name = $value;
	}

	/**
	 * 返回一条数据
	 *
	 * @param  DB     $db DB
	 *
	 * @return array     返回一个索引为结果集的数组
	 */
	public function findOne(DB $db)
	{
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		$this->pdoStatement->execute();
		return $this->pdoStatement->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * 返回多条数据
	 *
	 * @param  DB     $db DB
	 *
	 * @return array     返回一个包含结果集中所有行的数组
	 */
	public function findAll(DB $db)
	{
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		$this->pdoStatement->execute();
		return $this->pdoStatement->fetchAll(PDO::FETCH_BOTH);
	}

	/**
	 * 返回多条数据
	 *
	 * @param  DB     $db DB
	 *
	 * @return array     根据第一列分组
	 */
	public function findAllGroup(DB $db)
	{
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		$this->pdoStatement->execute();
		return $this->pdoStatement->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
	}

	/**
	 * 保存数据
	 *
	 * @param  DB     $db DB类
	 *
	 * @return int     返回最后插入行的ID或序列值
	 */
	public function save(DB $db)
	{
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		$res = $this->pdoStatement->execute();
		if (!$res) {
			return false;
		} 
		return $db->id = $this->pdo->lastInsertId();
	}

	/**
	 * 删除数据
	 *
	 * @param  DB     $db DB
	 *
	 * @return int     返回删除影响的行数
	 */
	public function delete(DB $db)
	{
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		$this->pdoStatement->execute();
		return $this->pdoStatement->rowCount();
	}

	public function update(DB $db)
	{
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		return $this->pdoStatement->execute();
	}

	/**
	 * 执行 SQL 语句，返回 PDOStatement 对象，可以理解为结果集
	 *
	 * @param  DB     $db DB类
	 *
	 * @return array     结果集
	 */
	public function query(DB $db)
	{
		$res = [];
		foreach ($this->pdo->query($db->sql, PDO::FETCH_ASSOC) as $v) {
			$res[] = $v;
		}
		return $res;
	}

	/**
	 * 获取某个字段的值
	 *
	 * @param  DB     $db [description]
	 *
	 * @return mixed     
	 */
	public function countValue(DB $db) {
		$this->pdoStatement = $this->pdo->prepare($db->sql);
		$this->bindValue($db);
		$this->pdoStatement->execute();
		return $this->pdoStatement->fetchColumn();
	}

	/**
	 * 把一个值绑定到一个参数
	 *
	 * @param  DB     $db DB类
	 *
	 * @return mixed     
	 */
	public function bindValue(DB $db)
	{
		if (empty($db->params)) {
			return;
		}
		foreach ($db->params as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $_k => $_v) {
					$this->pdoStatement->bindValue(($_k+1), $_v);
				}
			} else {
				$this->pdoStatement->bindValue(":{$k}", $v);
			}
		}
	}

	/**
	 * 启动一个事务
	 *
	 * @return mixed 
	 */
	public function beginTransaction()
	{
		$this->pdo->beginTransaction();
	}

	/**
	 * 提交一个事务
	 *
	 * @return mixed 
	 */
	public function commit()
	{
		$this->pdo->commit();
	}

	/**
	 * 事务回滚
	 *
	 * @return mixed 
	 */
	public function rollBack()
	{
		$this->pdo->rollBack();
	}

	/**
	 * 关闭连接
	 *
	 * @return mixed 
	 */
	public function closeConnection()
	{
		$this->pdo = null;
	}
}