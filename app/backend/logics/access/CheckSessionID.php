<?php 

namespace App\Backend\Logics\Access;

use Core\Request;
use App\Backend\Models\SessionTable;

class CheckSessionID extends Check
{	
	// session 有效时间为一个小时
	private $maxtime = 3600;
	private $time;

	public function doCheck(Request $request)
	{
		$instance = new SessionTable();
		$sessionId = isset($_SESSION['user_login_session']) ? $_SESSION['user_login_session'] : '0';
		if ($sessionId) {
			$this->time = time();
			$where = [
				'key' => $sessionId,
				'ip'  => get_client_ip(),
			];
			
			$data = $instance->findOne($where);

			if ($data && $data['expiry'] >= $this->time) {
				if ($data['uid']) {
					// 更新 expiry 时间
					$updateData = [
						'expiry' 	=> $this->time + $this->maxtime,
						'mod_date'	=> $this->time,
					];
					$instance->activate($sessionId, $updateData);
					return $data['uid'];
				}
			}
		}
		return 0;
	}
}