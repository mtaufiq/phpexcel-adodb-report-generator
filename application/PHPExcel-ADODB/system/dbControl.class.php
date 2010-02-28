<?php if ( !isset($GLOBALS['base_url'])) exit('No direct script access allowed');


class dbControl{

	var $host = "localhost";
	var $user = "root";
	var $pass = "";
	var $db = "penta-account";
	
	public function connect(){
		$conn = &ADONewConnection('mysql'); 
		$conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$conn->PConnect($this->host,$this->user,$this->pass,$this->db);
		return $conn;
	}
	
	public function query($query){
		$conn = $this->connect();
		$result = $conn->execute($query);
		
		return $result;
		
	}
	
	
	
	

}	

?>