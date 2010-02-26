<?php

class sfGuardLogger{

	public $password;
	public $user;

	function __construct($user,$pass){

		$this->authenticate($user,$pass);
	}
	
	public function getUser(){
		return $this->user;
	}
	
	private function authenticate($user, $pass){
		$db = new dbControl();
		$db->connect();
		$rs = $db->query("SELECT * 
							FROM `sf_guard_user` 
							WHERE MD5(`username`) = '".$user."'
							AND MD5(`password`) = '".$pass."'"
						);

		if($rs->RecordCount() == 0){
			header("Location: system/sf_guard_logger/loginError.php");
		}
	
	}

}

?>