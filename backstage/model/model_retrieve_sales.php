<?php
class model_retrieve_sales{
	public $m1;
	public $d1;
	public $y1;
	public $m2;
	public $d2;
	public $y2;
	public $ymd1;
	public $ymd2;
	function valid_data($query){
		if ($query!="today" and $query!="month"){
			$this->m1 = substr($query,5,2);
			$this->d1 = substr($query,8,2);
			$this->y1 = substr($query,0,4);
			$this->m2 = substr($query,16,2);
			$this->d2 = substr($query,19,2);
			$this->y2 = substr($query,11,4);
			$this->ymd1 = substr($query,0,10);
			$this->ymd2 = substr($query,11,10);
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
		  	if (strtotime($this->ymd1)>strtotime($this->ymd2)){
		  		die("Invalid query string");
		  	};
		  	if ($this->y2!=$this->y1 or $this->m2-$this->m1>1){
		  		# 不可跨年，且跨月不可超過兩個月
		  		die("Invalid query string");
		  	};
		};
	}
	
	function generate_sql($query){
		$ym = gmdate("Ym");
		if ($query == "today"){
			return "SELECT price,items,trade_time,trade_date,member,payment FROM trades.".$ym."_001_aa WHERE trade_date='".gmdate("Y-m-d")."'";
		} else if ($query == "month"){
			return "SELECT price,items,trade_time,trade_date,member,payment FROM trades.".$ym."_001_aa";
		} else {
			if ($this->m2==$this->m1){
				return "SELECT price,items,trade_time,trade_date,member,payment FROM trades.".$this->y1.$this->m1."_001_aa WHERE trade_date BETWEEN '".$this->ymd1."' AND '".$this->ymd2."'";
			} else {
				return  "SELECT price,items,trade_time,trade_date,member,payment FROM trades.".$this->y1.$this->m1."_001_aa WHERE trade_date BETWEEN '".$this->ymd1."' AND '".substr($this->ymd1,0,8)."31"."' UNION ALL SELECT price,items,trade_time,trade_date,member,payment FROM trades.".$this->y2.$this->m2."_001_aa WHERE trade_date BETWEEN '".substr($this->ymd2,0,8)."01"."' AND '".$this->ymd2."'";
			};
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

	function retrieve_sales($query){
		try {
			$this->valid_data($query);
			$connection = $this->get_connection();
			$sql = $this->generate_sql($query);
			$stmt = $connection->prepare($sql);
			$stmt->execute();
	 		$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = $stmt->fetchAll();
			if (empty($result)){
				die("No transaction");
			};
			return json_encode($result,JSON_UNESCAPED_UNICODE);
		} catch (PDOException $e){
			die("table not found");
		}
	}
}
?>