<?php 

namespace App\Backend\Controllers;

use Core\App;
use Core\Common\Log;
use Core\Common\Smarty;
use Core\Orm\DB;
use PDO;

/**
 * Index
 */
class Index
{
	
	public function __construct()
	{
		# code...
	}

	public function hello()
	{
		$te = session_id();
		var_dump($te);exit;
		return "Hello Mx PHP";
	}

	public function test()
	{
		//http://www.mxphp.cos/index.php?module=Backend&contoller=Index&action=test&username=test&password=123456789123&code=123
		$request = App::$container->get('request');
		$request->check('username', 'require');
		$request->check('password', 'length', 12);
		$request->check('code', 'number');

		return [
			'username'	=> $request->get('username', 'default value')
		];
	}

	/**
	 * 框架内部调用
	 * @example http://www.mxphp.cn/index.php?module=Backend&contoller=Index&action=micro
	 * @return json
	 */
	public function micro()
	{
		return App::$app->get('backend/index/hello', ['user' => 'mxphp']);
	}

	/**
	 * 容器内获取实例
	 * @return void 
	 */
	public function getInstanceFromContainerDemo()
	{	
		// 请求对象
		App::$container->get('request');
		// 配置对象
		App::$container->getSingle('config');

		return [];
	}

	/**
	 * 日志演示
	 * @return void 
	 */
	public function log()
    {
        Log::debug('Mx PHP');
        Log::notice('Mx PHP');
        Log::warning('Mx PHP');
        Log::error('Mx PHP');

        return [];
    }

    /**
     * 容器内获取nosql实例演示
     * @return void
     */
    public function nosqlDemo()
    {
        // redis对象
        App::$container->getSingle('redis');
        // memcahe对象
        // App::$container->getSingle('memcached'); # unix
        App::$container->getSingle('memcache');	 # windows
        // mongodb对象
        // App::$container->getSingle('mongoDB');

        return [];
    }

    public function twig_test()
    {
    	$this->twigConfig = env('twig');
		$this->templatePath = App::$app->rootPath . $this->twigConfig['template'];
		$this->cachePath    = App::$app->rootPath . $this->twigConfig['cache'];
		$loader = new \Twig_loader_Filesystem($this->templatePath);
		$twig   = new \Twig_Environment($loader,[
			'cache' 		=> $this->cachePath,
			'debug' 		=> $this->twigConfig['debug'],
			'auto_reload'	=> $this->twigConfig['auto_reload']
		]);
		$twig->render('index.twig');
    }

    public function smarty_test()
    {
    	$smarty = new Smarty;
    	$data = ['title'=>'标题', 'name' => 'World'];
    	$tpl  = 'index';
    	$smarty->display($tpl, $data);
    }

    public function easyui()
	{
		$smarty = new Smarty;
		//http://www.mxphp.cos/index.php?module=Backend&contoller=Index&action=easyui&tpl=login
		$request = App::$container->get('request');
		$tpl = $request->get('tpl');
		$data = ['title' => $tpl];
		$smarty->display($tpl, $data);
	}

	// 验证账号
	public function accountvalidate()
	{
		return true;
	}

	public function citysData()
	{
		$data = [
			['id' => 1, 'country' => '中国', 'city' => '北京市'],
			['id' => 2, 'country' => '中国', 'city' => '上海市'],
			['id' => 3, 'country' => '美国', 'city' => '纽约'],
			['id' => 4, 'country' => '法国', 'city' => '里昂'],
		];
		echo json_encode($data);exit();
	}

	// 服务端检索
	public function filterContry()
	{
		$request = App::$container->get('request');
		$q = $request->get('q');
		$data = [];
		if ($q == '中国') {
			$data = [
				['id' => 1, 'country' => '中国', 'city' => '北京市'],
				['id' => 2, 'country' => '中国', 'city' => '上海市']
			];
		} else if ($q == '美国') {
			$data = [
				['id' => 3, 'country' => '美国', 'city' => '纽约'],
			];
		} else if ($q == '法国') {
			$data = [
				['id' => 4, 'country' => '法国', 'city' => '里昂'],
			];
		}
		
		echo json_encode($data);exit();
	}

	public function form()
	{
		$request = App::$container->get('request');
		$name = $request->post('nb');
		echo $name;
	}

	public function initForm()
	{
		$data = [
			'nickname' => '里斯',
			'age'		=> '21',
			'birthday'	=> '3/6/2001'
		];

		echo json_encode($data);
	}

	public function fileUpload()
	{
		var_dump($_FILES);
		exit;
		if (file_exists($_FILES["file"]["name"])) {
		    echo $_FILES["file"]["name"] . " 已存在";
		} else {
		    move_uploaded_file($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
		}
	}

	public function getPanel(){
		$request = App::$container->get('request');
		$index   = $request->get('index', 0);
		echo '测试加载数据[index='.$index.']';
	}

	public function createProduct() {
		$request 		= App::$container->get('request');
		$productname 	= $request->post('productname');
		$producttype 	= $request->post('producttype');
		$productprice 	= $request->post('productprice');
		$productvolume 	= $request->post('productvolume');
		$productaddress = $request->post('productaddress');
		$producttime 	= $request->post('producttime');

		$instance = DB::table('product');
		$productId = $instance->save([
			'productname' 	=> $productname, 
			'producttype'	=> $producttype,
			'productprice'	=> $productprice,
			'productvolume'	=> $productvolume,
			'productaddress'=> $productaddress,
			'producttime'	=> $producttime,
		]);


		$return = array(
			'status'=> 1, 
			'id' 	=> $productId, 
			'db'	=> $instance->masterSlave,
			'sql'	=> $instance->sql
		);
		echo json_encode($return);
	}

	public function getProduct()
	{
		$request = App::$container->get('request');
		$type    = $request->post('type', 0);
		$sort    = $request->post('sort');
		$order   = $request->post('order');
		$orderBy = $sort && $order ?  "{$sort} {$order}" : '';

		$start   = $request->post('start', 0);
		$end     = $request->post('end', 0);

		// 多条件查询有问题
		$where = [
			'id'	=> ['>=', 0],
			// 'producttime'	=> $start ? ['>=', date('Y/m/d', strtotime($start))] : ['>=', 0],
				// $end ? ['<=', date('Y/m/d', strtotime($end))] : ['<=', date('Y/m/d')]
			
		];
		// print_r($where);
		$instance = DB::table('product');
		if ($orderBy) {
			$data = $instance->where($where)->orderBy($orderBy)->findAll();
		} else {
			$data = $instance->where($where)->findAll();
		}

		$sum = [];;
		foreach ($data as $key => $value) {
			$sum['productprice'] = isset($sum['productprice']) ? $sum['productprice'] + $value['productprice'] : $value['productprice'];
			$sum['productvolume'] = isset($sum['productvolume']) ? $sum['productvolume'] + $value['productvolume'] : $value['productvolume'];
		}

		$footer = [
			[
				'productname' 	=> '总销量',
				'productvolume'	=> isset($sum['productvolume']) ? $sum['productvolume'] : '0',
			],
			[
				'productname'	=> '总销售金额',
				'productprice'	=> isset($sum['productprice']) ? $sum['productprice'] * $sum['productvolume'] : '0',
			]
		];
		$return = [
			'status' => 1,
			'db'	=> $instance->masterSlave,
			'sql'	=> $instance->sql,
			'rows'	=> $data,	// 行内容
			'footer'=> $footer 	// 页脚数据
		];
		if ($type == '1') {
			echo json_encode($return);
		} else {
			echo json_encode($data);
		}
	}

	// 组合网格
	public function combogrid(){
		$data = $return = [];
		$request = App::$container->get('request');
		$q = $request->post('q', '');
		if ($q) {
			$where = [
				'productname' => $q
			];
			$instance = DB::table('product');
			$data = $instance->where($where)->findAll();
			$return = [
				'status' => 1,
				'db'	=> $instance->masterSlave,
				'sql'	=> $instance->sql,
				'rows'	=> $data,	// 行内容
			];
		}
		echo json_encode($data);
	}

	public function getTreeData()
	{
		$data = $return = $data2 = [];
		$request = App::$container->get('request');
			$where = [
				'producttype' => 1
			];
			$instance = DB::table('product');
			$data = $instance->where($where)->findAll();

			// 根节点
			$dqarray = [
				"text" => "1",
				"children" => []
			];
			//在根节点添加产品
			for ($i=0; $i < count($data); $i++) { 
				array_push($dqarray["children"], array(
					'text' => $data[$i]['productname'],
				));
			}

			$where2 = [
				'producttype' => 2
			];
			$data2 = $instance->where($where2)->findAll();

			$sparray = [
				"text" => "2",
				"children" => []
			];
			for ($i=0; $i < count($data2); $i++) { 
				array_push($sparray["children"], array(
					'text' => $data2[$i]['productname'],
				));
			}

			// 将食品节点和电器节点添加到根节点
			array_push($return, $dqarray);
			array_push($return, $sparray);
		echo json_encode($return);
	}
	public function getSyncTreeData()
	{
		$data = $return = $data2 = [];
		$request = App::$container->get('request');
		$instance = DB::table('product');
		$id = $request->post('id', 1);
		if ($id == 2) {
			$sparray = [];
			$where = [
				'producttype' => 2
			];
			$data = $instance->where($where)->findAll();

			for ($i=0; $i < count($data); $i++) { 
				array_push($sparray, array(
					'text' => $data[$i]['productname']
				));
			}
			echo json_encode($sparray);
		} else {
			$dqarray = [];
			$where = [
				'producttype' => 1
			];
			$data = $instance->where($where)->findAll();

			$dqarray = [
				'text' => 1,
				'id'	=> 1,
				'children' => [],
			];
			for ($i=0; $i < count($data); $i++) { 
				array_push($dqarray['children'], array(
					'text' => $data[$i]['productname']
				));
			}
			array_push($return, $dqarray);
			array_push($return, array(
				'text' 	=> 2,
				'id'	=> 2,
				'state'	=> 'closed' // 食品节点没有子节点，设置为关闭状态
			));
			echo json_encode($return);
			
		}
	}

	public function getTreeGridData()
	{
		$data = $dqarray = $sparray = [];
		$request = App::$container->get('request');
		$instance = DB::table('product');

		$where = [
			'producttype' => 1
		];
		$dqarray = array(
			"id"	=> 10,
			"productname"	=> '电器',
			"children"		=> [],
		);

		$dq = $instance->where($where)->findAll();
		for ($i=0; $i < count($dq); $i++) { 
			array_push($dqarray["children"], $dq[$i]);
		}

		$sparray = array(
			"id"	=> 20,
			"productname"	=> '食品',
			"children"		=> []
		);

		$where = [
			'producttype' => 2
		];

		$sp = $instance->where($where)->findAll();
		for ($i=0; $i < count($sp); $i++) { 
			array_push($sparray["children"], $sp[$i]);
		}

		// 总数据
		$total_volume = $instance->sum('productvolume as sum_volume');
		$total_money  = $instance->sum('productprice as sum_money');

		$footer = array(
			array(
				'productname' => '总销量',
				'productvolume' => $total_volume[0]['sum(`productvolume`)']
			),
			array(
				'productname' => '总销售金额',
				'productprice' => $total_money[0]['sum(`productprice`)']*$total_volume[0]['sum(`productvolume`)']
			),
		);

		$data = array(
			'rows' 		=> array($dqarray, $sparray),
			'footer'	=> $footer
		);

		echo json_encode($data);
	}

	// PDO 查询参测试
	public function pdo_test()
	{
		$instance = DB::table('product');
		$where = array(
			// 'productname' 	=> ['LIKE' , '粮'],
			// 'id'			=> ['>', 2],
			// 'productname'	=> '猫粮',
			// 'id'			=> ['IN', [2, 3, 48]],
			'producttime' 	=> ['RANGE', ['2018/01/01', '2019/1/4']], // 范围搜索 >= MIN AND <= MAX
			// 'id'			=> ['RANGE', ['6', '3', 'OR']],				  // ID >= 6 OR ID <= 3
		);
		$data = $instance->where($where)->findAll();
		echo $instance->sql;
		// $data = $instance->findAllGroup(['producttype', 'productname', 'productprice']);
		print_r($data);
	}

	public function deleteProduct()
	{
		$returnArr = array('status' => 0, 'errorMsg' => '操作失败');
		echo json_encode($returnArr);
	}

	public function updateProduct()
	{
		$returnArr = array('status' => 0, 'errorMsg' => '操作失败');
		$request = App::$container->get('request');
		$data = $request->all();

		$instance = DB::table('product');

		$where['id'] = $data['id'];
		unset($data['module']);
		unset($data['contoller']);
		unset($data['action']);

		$res = $instance->where($where)
						->update($data);
		if ($res) {
			$returnArr['status'] = 1;
			$returnArr['errorMsg'] = '修改成功';
		}
		// var_dump($res);
		// echo $instance->sql;
		// print_r($data);exit;
		echo json_encode($returnArr);
	}

	public function bufferView()
	{
		$instance = DB::table('pagination');
		$data = $instance->findAll();
		echo json_encode($data);
	}

	public function scrollView()
	{
		$request = App::$container->get('request');
		$page = $request->post('page', 1);
		$rows = $request->post('rows', 10);

		$start = ($page-1) + $rows;
		
		$instance = DB::table('pagination');
		$data = $instance->limit($start, $rows)->findAll();

		// $total = $instance->query('select COUNT(*) FROM pagination');
		$instance = DB::table('pagination');
		$total = $instance->countValue('id'); // 先查询全部的，在进行分页查询，或者重新进行初始化

		$return = array('rows' => $data, 'total' => $total);
		echo json_encode($return);
	}

	public function createEtreeData()
	{
		$request = App::$container->get('request');
		$parentid = $request->post('parentId');
		if ($parentid) {
			$data = array(
				'id'	=> time(),
				"name"  => 'newItem1',
				"type"	=> $parentid
			);
			$instance = DB::table('treeproduct');
			$res = $instance->save($data);

		}
		$returnArr = array('status' => 1, 'msg' => $res);
	}

	public function getEtreeData()
	{
		$data = $return = $data2 = [];
		$request = App::$container->get('request');
		$instance = DB::table('treeproduct');
		$id = $request->post('id', 1);
		if ($id == 2) {
			$sparray = [];
			$where = [
				'producttype' => 2
			];
			$data = $instance->where($where)->findAll();

			for ($i=0; $i < count($data); $i++) { 
				array_push($sparray, array(
					'text' => $data[$i]['productname']
				));
			}
			echo json_encode($sparray);
		} else {
			$dqarray = [];
			$where = [
				'producttype' => 1
			];
			$data = $instance->where($where)->findAll();

			$dqarray = [
				'text' => 1,
				'id'	=> 1,
				'children' => [],
			];
			for ($i=0; $i < count($data); $i++) { 
				array_push($dqarray['children'], array(
					'text' => $data[$i]['productname']
				));
			}
			array_push($return, $dqarray);
			array_push($return, array(
				'text' 	=> 2,
				'id'	=> 2,
				'state'	=> 'closed' // 食品节点没有子节点，设置为关闭状态
			));
			echo json_encode($return);
			
		}
	}
}