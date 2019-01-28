<?php 
namespace Core\Handles;

use Core\App;

class SessionHandle implements Handle
{
	private $sessionEnv = [];
	/**
	 * 注册处理机制
	 * 
	 * @param  App    $app 对象
	 * @return mixed      
	 */
	public function register(App $app) {
		new SessionHandle();
	}

	public function __construct() {
		if (!isset($_SESSION)) {
			$this->sessionEnv = env('session');
			if (!empty($this->sessionEnv)) {
				if (!empty($this->sessionEnv['save_path'])) {
					ini_set("session.save_path", $this->sessionEnv['save_path']);
				}
				if (!empty($this->sessionEnv['gc_maxlifetime'])) {
					ini_set("session.gc_maxlifetime", $this->sessionEnv['gc_maxlifetime']);
				}
			}
			session_start();
		}
	}
}