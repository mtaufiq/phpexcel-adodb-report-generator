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
		/*
		WHERE MD5(`username`) = '23228e5653802c2e7889329087aa669c'
		AND MD5(`password`) = '942175560c04da5bc1015232da8204ec'"
		*/
		//echo print_r($rs->fields); # shows array(['col1']=>'v0',['col2'] =>'v1')
		
		//echo
		if($rs->RecordCount() == 0){
			// Request URI is /boo/blah.php
			header("Location: ../system/sf_guard_logger/loginError.php");
		}
	
	}

}

?>