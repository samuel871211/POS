<?php
class model_retrieve_member{
	public $name;
	public $phone;
	public $birth;
	function valid_data(){
		if ($this->phone != "" and !is_numeric($this->phone)){
			die("Invalid Query String");
		};
	}

	function generate_sql(){
		if ($this->phone != ""){
			return "SELECT * FROM members.prefix_09".substr($this->phone,2,1)." WHERE phonenumber='".$this->phone."'";
		} else {
			$sql = "";
			for ($i=0;$i<10;$i++){
				$sql = $sql."SELECT * FROM members.prefix_09".$i." WHERE name='".$this->name."' OR birth='".$this->birth."' UNION ALL ";
			};
			$sql = substr($sql,0,-11);
			return $sql;
		}
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
	
	function retrieve_member($name,$phone,$birth){
		$this->name = $name;
		$this->phone = $phone;
		$this->birth = $birth;
		$this->valid_data();
		$connection = $this->get_connection();
		$sql = $this->generate_sql();
		$stmt = $connection->prepare($sql);
		$stmt->execute();
 		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		if (count($result) == 0){
			echo "not found";
		} else if (count($result) > 1){
			echo "narrow it down";
		} else {
			$member = $result[0]["name"]."<br>".$result[0]["phonenumber"];
			$retrieve_trades = new model_retrieve_trades($member);
			$stat = $retrieve_trades->member_trading_statistics();
			print_r(json_encode(array_merge($result[0],$stat),JSON_UNESCAPED_UNICODE));
		}
	}
}

class model_retrieve_trades{
	
	function __construct($member){
		$this->member = $member;
	}
	
	function get_connection(){
		try{
			$connection = new PDO(
				"mysql:host=localhost;dbname=trades",
				'root','cat2163472'
			);
			$connection->setAttribute(
				PDO::ATTR_ERRMODE,
				PDO::ERRMODE_EXCEPTION
			);
			return $connection;
		} catch(PDOException $e){
			die("Connection Failed");
		};
	}
	
	function generate_trade_history_sql(){
		$tables = $this->get_all_tables();
		$sql = "";
		foreach ($tables as $result) {
			foreach ($result as $key => $value) {
				$sql = $sql."SELECT price,items,trade_date,payment FROM ".$value." WHERE member = '".$this->member."' UNION ALL ";
			};
		};
		$sql = substr($sql,0,-11);
		return $sql;
	}

	function get_all_tables(){
		try {
			$connection = $this->get_connection();
			$stmt = $connection->prepare("SHOW tables");
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_NUM);
			$result = $stmt->fetchAll();
			return $result;
		} catch(PDOException $e){
			die("Connection Failed");
		};
	}

	function get_recent_trades(){
		try {
			$connection = $this->get_connection();
			$month = $this->get_all_tables();
			$month = end($month);
			$sql = "SELECT price FROM ".end($month)." WHERE member = '".$this->member."'";
			$stmt = $connection->prepare($sql);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = $stmt->fetchAll();
			return $result;
		} catch(PDOException $e){
			die("Connection Failed");
		};
	}

	function member_trading_statistics(){
		try {
			$connection = $this->get_connection();
			$sql = $this->generate_trade_history_sql();
			$stmt = $connection->prepare($sql);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = $stmt->fetchAll();
			$stat  = array(
				"total_price"=>0,
				"total_count"=>0,
				"recent_price"=>0,
				"recent_count"=>0,
				"popular_item"=>""
			);
			if (!empty($result)){
				$item_count = array();
				foreach ($result as $res) {
					foreach ($res as $key => $value) {
						if ($key == 'price'){
							$stat["total_price"] += $value;
							$stat["total_count"] += 1;
						} else if ($key == 'items'){
							$items = explode(",",$value);
							foreach ($items as $key => $value) {
								if (in_array($value,$item_count)){
									$item_count[$value] += 1;
								} else {
									$item_count[$value] = 1;
								};
							};
						};
					};
				};
				arsort($item_count);
				$stat["popular_item"] = array_keys($item_count)[0];
			};
			
			$recent_trades = $this->get_recent_trades();
			if (!empty($recent_trades)){
				foreach ($recent_trades as $res) {
					foreach ($res as $key => $value) {
						$stat["recent_price"] += $value;
						$stat["recent_count"] += 1;
					};
				};
			};
			$stat["history"] = json_encode($result,JSON_UNESCAPED_UNICODE);
			return $stat;
		} catch (PDOException $e){
			die("Connection Failed");
		}
	}
}
?>