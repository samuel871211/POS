<?php
class model_login{
	public $username;
	public $password;
	public $branch;
	function valid_data(){
		if ($this->username=="" or $this->password=="" or $this->branch==""){
			die("Invalid Query String");
		};
	}

	function generate_sql(){
		return "SELECT *FROM admins.alladmins WHERE branch='".$this->branch."' AND password='".$this->password."' AND username='".$this->username."'";
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
	
	function login($username,$password,$branch){
		$this->username = $username;
		$this->password = $password;
		$this->branch = $branch;
		$this->valid_data();

		try {
			$connection = $this->get_connection();
			$sql = $this->generate_sql();
			$stmt = $connection->prepare($sql);
			$stmt->execute();
	 		$stmt->setFetchMode(PDO::FETCH_NUM);
			$result = $stmt->fetchAll();
			if (count($result) == 0){
				echo "登入失敗";
			} else {
				if (!isset($_SESSION)){
					session_start();
				};
				$_SESSION["username"] = $username;
				$_SESSION["password"] = $password;
				$_SESSION["branch"] = $branch;
				echo "OK";
			};
		} catch (PDOException $e){
			die("DB Error");
		};
	}
}
?>