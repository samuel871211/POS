<?php
if (!isset($_SESSION)){
	session_start();
};
class model_new_member{
	public $name;
	public $phone;
	public $birth;
	function valid_data(){
		if ($this->name=="" or $this->phone=="" or $this->birth==""){
			die("Invalid Query String");
		} else if (!is_numeric($this->phone) or strlen($this->phone) != 10){
			die("Invalid Query String");
		} else {
			$y = substr($this->birth,0,4);
			$m = substr($this->birth,5,2);
			$d = substr($this->birth,8,2);
			if (!is_numeric($y) or !is_numeric($m) or !is_numeric($d)){
				die("Invalid Query String");
			} else if (!checkdate($m,$d,$y)){
				die("Invalid Query String");
			} else if (strtotime($this->birth) > strtotime(gmdate("Y-m-d"))){
				die("Invalid Query String");
			};
		};
	}

	function generate_sql(){
		return "INSERT INTO members.prefix_09".substr($this->phone,2,1)."(name,phonenumber,birth,branch) VALUES ('".$this->name."','".$this->phone."','".$this->birth."','".$_SESSION["branch"]."')";
	}

	function get_connection(){
		try{
			$connection = new PDO("mysql:host=localhost",'root','cat2163472');
			$connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			return $connection;
		} catch(PDOException $e){
			die("Connection Failed");
		};
	}
	
	function new_member($name,$phone,$birth){
		$this->name = $name;
		$this->phone = $phone;
		$this->birth = $birth;
		$this->valid_data();
		try {
			$connection = $this->get_connection();
			$sql = $this->generate_sql();
			$connection->exec($sql);
			return "ok";
		} catch (PDOException $e) {
			die("Connection Failed");
		}
	}
}
?>