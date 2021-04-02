<?php
class model_retrieve_count{
	public $m1;
	public $d1;
	public $y1;
	public $m2;
	public $d2;
	public $y2;
	function valid_data($start,$end){
		if ($start == "" or $end == ""){
			die("Invalid Query String");
		};
		$this->m1 = substr($start,5,2);
		$this->d1 = substr($start,8,2);
		$this->y1 = substr($start,0,4);
		$this->m2 = substr($end,5,2);
		$this->d2 = substr($end,8,2);
		$this->y2 = substr($end,0,4);
		if (!is_numeric($this->m1) or !is_numeric($this->m2)){
			die("Invalid query string");
		};
		if (!is_numeric($this->d1) or !is_numeric($this->d2)){
			die("Invalid query string");
		};
		if (!is_numeric($this->y1) or !is_numeric($this->y2)){
			die("Invalid query string");
		}
		if (!checkdate($this->m1,$this->d1,$this->y1)){
	    	die("Invalid query string");
	  	};
	  	if (!checkdate($this->m2,$this->d2,$this->y2)){
	  		die("Invalid query string");
	  	};
	  	if (strtotime($start)>strtotime($end)){
	  		die("Invalid query string");
	  	};
	  	if ($this->y2!=$this->y1 or $this->m2-$this->m1>1){
	  		# 不可跨年，且跨月不可超過兩個月
	  		die("Invalid query string");
	  	};
	}

	function generate_sql($start,$end){
		if ($this->m2==$this->m1){
			return "SELECT items,amount FROM trades.".$this->y1.$this->m1."_001_aa WHERE trade_date BETWEEN '".$start."' AND '".$end."'";
		} else {
			return  "SELECT items,amount FROM trades.".$this->y1.$this->m1."_001_aa WHERE trade_date BETWEEN '".$start."' AND '".substr($start,0,8)."31"."' UNION ALL SELECT items,amount FROM trades.".$this->y2.$this->m2."_001_aa WHERE trade_date BETWEEN '".substr($end,0,8)."01"."' AND '".$end."'";
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
	
	function retrieve_count($start,$end){
		$this->valid_data($start,$end);
		$connection = $this->get_connection();
		$sql = $this->generate_sql($start,$end);
		try {
			$stmt = $connection->prepare($sql);
			$stmt->execute();
	 		$stmt->setFetchMode(PDO::FETCH_NUM);
			$result = $stmt->fetchAll();
			if (empty($result)){
				die("No history");
			};
			print_r($this->combine_And_Sort($result));
		} catch (PDOException $e){
			die("No history");
		};
	}

	function combine_And_Sort($result){
		$items = array(); # associative array
		foreach ($result as $itemArray) {
			$item = explode(",",$itemArray[0]);
			$amount = explode(",",$itemArray[1]);
			for ($i=0;$i<count($item);$i++){
				if (in_array(trim($item[$i]),array_keys($items))){
					$items[trim($item[$i])] += $amount[$i];
				} else {
					$items[trim($item[$i])] = (int)$amount[$i];
				};
			};
		};
		arsort($items);
		return $this->get_number_and_price($items);
	}

	function get_number_and_price($items){
		$connection = $this->get_connection();
		foreach ($items as $name => $amount) {
			try {
				$name = trim($name);
				$amount = trim($amount);
				$sql = "SELECT price,number FROM items.allitems WHERE name='".$name."'";
				$stmt = $connection->prepare($sql);
				$stmt->execute();
		 		$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$result = $stmt->fetchAll();
				$items[$name] = $items[$name].",".$result[0]["price"].",".$result[0]["number"];
			} catch (PDOException $e){
				die("db error");
			};
		};
		return json_encode($items,JSON_UNESCAPED_UNICODE);
	}
}
?>