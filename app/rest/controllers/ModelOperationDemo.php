<?php 

namespace App\Rest\Controllers;

use Core\App;
use App\Rest\Models\TestTable;
use Exception;
use Core\Orm\DB;

/**
 * Model 
 */
class ModelOperationDemo
{
	public function modelExample()
	{
		try {
			DB::beginTransaction();

			$testTableModel = new TestTable();

			$testTableModel->modelFindOneDemo();
            // find all data
            $testTableModel->modelFindAllDemo();
			
            // save data
            $res = $testTableModel->modelSaveDemo();
            var_dump($res);
            // delete data
            $testTableModel->modelDeleteDemo();
            // update data
            // $testTableModel->modelUpdateDemo([
            //        'name' => 2
            //     ]);
            // count data
            $testTableModel->modelCountDemo();

            DB::commit();
            return 'success';
		} catch(Exception $e){
			DB::rollBack();
			return 'fail';
		}
	}	
}