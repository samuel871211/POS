<?php
class model_retrieve_item{

	function valid_data($number,$item){
		if ($item == ""){
			if (!is_numeric($number)){
				die("Invalid Query String");
			} else if (strlen($number)!=5 and strlen($number)!=13){
				die("Invalid Query String");
			};
		};
	}

	function generate_sql($number,$item){
		if ($item != ""){
			return "SELECT * FROM items.allitems WHERE name='".$item."'";
		} else if (strlen($number)==13){
			return "SELECT * FROM items.allitems WHERE ean13='".$number."'";
		} else if (strlen($number)==5){
			return "SELECT * FROM items.allitems WHERE number='".$number."'";
		};
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
	
	function retrieve_item($number,$item){
		$this->valid_data($number,$item);
		$connection = $this->get_connection();
		$sql = $this->generate_sql($number,$item);
		$stmt = $connection->prepare($sql);
		$stmt->execute();
 		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		if (isset($result[0])){
			return json_encode($result[0],JSON_UNESCAPED_UNICODE);
		} else {
			die("Not Found");
		};
	}
}
?>