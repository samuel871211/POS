function insert(){
	let name = $("#name")[0].value;
	let phone = $("#phone")[0].value;
	let birth = $("#birth")[0].value;
	if (name == "" || phone == "" || birth == ""){return};
	$.ajax({
		url:'controller/controller_new_member.php',
		type:'POST',
		data:{
			"name":name,
			"phone":phone,
			"birth":birth
		},
		success:function(result){
			var die = ["Invalid Query String","Connection Failed"];
			if (die.includes(result)){
				$(".status")[0].innerHTML = '註冊失敗<i class="fa fa-close"></i>';
			} else if (result == "ok"){
				$(".status")[0].innerHTML = '註冊成功<i class="fa fa-check-circle"></i>';
			};
			$(".status")[0].style["visibility"] = "";
			setTimeout(function(){
				$(".status")[0].style["visibility"] = "hidden";
				$(".status")[0].innerHTML = "目前狀況";
			},5000)
		}
	})
}

function confirm(){
	$(".buttons button")[0].style["display"] = "none";
	$("#insert").fadeToggle();
}

function search(){
	let name = $("#name")[0].value;
	let phone = $("#phone")[0].value;
	let birth = $("#birth")[0].value;
	if (name == "" && phone == "" && birth == ""){return};
	$.ajax({
		url:'controller/controller_retrieve_member.php',
		type:'POST',
		data:{
			"name":name,
			"phone":phone,
			"birth":birth
		},
		success:function(result){
			var die = ["not found","narrow it down","Connection Failed","Connection Failed"];
			if (die.includes(result)){
				$(".status")[0].innerHTML = '未查詢到相關結果<i class="fa fa-close"></i>';
				$(".status")[0].style["visibility"] = "";
				setTimeout(function(){
					$(".status")[0].style["visibility"] = "hidden";
					$(".status")[0].innerHTML = "目前狀況";
				},5000)
			} else {
				result = JSON.parse(result);
				result["history"] = JSON.parse(result["history"]);
				
				update_info(result);
				update_trades(result.history);
			};
		}
	})
}

function display(){
	$(".gray").fadeToggle();
	if ($(".more")[0].style["display"] == "none"){
		$(".more2").fadeToggle();
	} else if ($(".more2")[0].style["display"] == "none"){
		$(".more").fadeToggle();
	};
}


function update_info(result){
	var info = $(".info")[0].children;
	info[1].innerHTML = result.name;
	info[4].innerHTML = result.phonenumber;
	info[7].innerHTML = result.birth;
	info[10].innerHTML = result.reg_date;
	
	var stat = $(".moreright table")[0].rows;
	stat[1].cells[0].innerHTML = "總金額:"+result.total_price;
	stat[1].cells[1].innerHTML = "總次數:"+result.total_count;
	stat[3].cells[0].innerHTML = "總金額:"+result.recent_price;
	stat[3].cells[1].innerHTML = "總次數:"+result.recent_count;
	stat[4].cells[0].innerHTML = "最常購買的商品："+result.popular_item;

	$(".gray").fadeToggle();
	$(".more").fadeToggle();
}

function update_trades(history){
	var table = $(".more2 table")[0];
	table.innerHTML =   "<tr>\
							<th>日期</th>\
							<th>商品</th>\
							<th>價格</th>\
							<th>付款</th>\
						</tr>";
	
	for (i=0;i<history.length;i++){
		var tr = table.insertRow(table.rows.length);
		tr.insertCell(0).innerHTML = history[i].trade_date;
		tr.insertCell(1).innerHTML = history[i].items;
		tr.insertCell(2).innerHTML = history[i].price;
		tr.insertCell(3).innerHTML = history[i].payment;
	};

	var td = table.insertRow(table.rows.length).insertCell(0);
	td.setAttribute("colspan","4");
	var div = document.createElement("div");
	div.innerHTML = "返回";
	div.setAttribute("onclick","showHistory()");
	div.className = "back";
	td.appendChild(div);
}

function showHistory(){
	$(".more").fadeToggle();
	$(".more2").fadeToggle();
}