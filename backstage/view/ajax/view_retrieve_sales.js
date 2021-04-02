var ordered = {};
var down_table = $(".down table")[0];
var more_table = $(".more table")[0];

function send(){
	if ($("input[type=radio]")[0].checked == true){
		var query = "today";
	} else if ($("input[type=radio]")[1].checked == true){
		var query = "month";
	} else if ($("input[type=radio]")[2].checked == true){
		var query = $("#start")[0].value+"~"+$("#end")[0].value;

	} else {return};
	$.ajax({
		url:'controller/controller_retrieve_sales.php',
		data:{"query":query},
		type:'POST',
		success:function(result){
			reset_down_table();
			let die = ["table not found","Invalid query string","Connection Failed","No transaction"];
			if (die.indexOf(result) != -1){
				alert(result);
				ordered = {};
				down_table.innerHTML = "";
				reset_more_table('','');
			} else {
				result = JSON.parse(result);
				if (result.length == 1){
					var tr = down_table.insertRow(down_table.rows.length);
					tr.insertCell(0).innerHTML = result[0]["trade_date"];
					tr.insertCell(1).innerHTML = 0;
					tr.insertCell(2).innerHTML = 0;
					tr.insertCell(3).innerHTML = 0;
					tr.insertCell(4).innerHTML = result[0]["price"];
					if (result[0]["payment"] == "cash"){
						tr.cells[1].innerHTML = result[0]["price"];
					} else if (result[0]["payment"] == "credit"){
						tr.cells[2].innerHTML = result[0]["price"];
					} else if (result[0]["payment"] == "easycard"){
						tr.cells[3].innerHTML = result[0]["price"];
					};
				} else {
					order_by_date(result);
					update_down_table();
				};
			};
		}
	})
}

function reset_down_table(){
	down_table.innerHTML = "<tr>\
								<th>日期</th>\
								<th>現金</th>\
								<th>信用卡</th>\
								<th>悠遊卡</th>\
								<th>總金額</th>\
							</tr>";
}

function reset_more_table(date,payment){
	more_table.innerHTML = "<tr>\
							    <th colspan='4'>"+date+" "+payment+"銷售表</th>\
							</tr>\
							<tr>\
								<th>時間</th>\
							    <th>商品</th>\
							    <th>金額</th>\
							    <th>會員</th>\
							</tr>";
}

function update_down_table(){
	let keys = Object.keys(ordered);
	reset_down_table();
	for (i=0;i<keys.length;i++){
		let tr = down_table.insertRow(down_table.rows.length);
		tr.insertCell(0).innerHTML = keys[i];
		tr.insertCell(1).innerHTML = 0;
		tr.insertCell(2).innerHTML = 0;
		tr.insertCell(3).innerHTML = 0;
		tr.insertCell(4).innerHTML = 0;
		tr.cells[1].setAttribute("onclick","generate_sales_table('"+keys[i]+"','cash')");
		tr.cells[2].setAttribute("onclick","generate_sales_table('"+keys[i]+"','credit')");
		tr.cells[3].setAttribute("onclick","generate_sales_table('"+keys[i]+"','easycard')");
		for (j=0;j<ordered[keys[i]].length;j++){
			if (ordered[keys[i]][j]["payment"] == "cash"){
				tr.cells[1].innerHTML = parseInt(tr.cells[1].innerHTML) + parseInt(ordered[keys[i]][j]["price"]);
			} else if (ordered[keys[i]][j]["payment"] == "credit"){
				tr.cells[2].innerHTML = parseInt(tr.cells[2].innerHTML) + parseInt(ordered[keys[i]][j]["price"]);
			} else if (ordered[keys[i]][j]["payment"] == "easycard"){
				tr.cells[3].innerHTML = parseInt(tr.cells[3].innerHTML) + parseInt(ordered[keys[i]][j]["price"]);
			};
			tr.cells[4].innerHTML = parseInt(tr.cells[4].innerHTML) + parseInt(ordered[keys[i]][j]["price"]);
		};
	};
}

function order_by_date(result){
	for (i=0;i<result.length;i++){
		if (Object.keys(ordered).includes(result[i]["trade_date"])){
			ordered[result[i]["trade_date"]].push(result[i]);
		} else {
			ordered[result[i]["trade_date"]] = [result[i]];
		};
	};
}

function generate_sales_table(date,payment){
	reset_more_table(date,payment);
	for (i=0;i<ordered[date].length;i++){
		if (ordered[date][i]["payment"] == payment){
			let tr = more_table.insertRow(more_table.rows.length);
			tr.insertCell(0).innerHTML = ordered[date][i]["trade_time"];
			tr.insertCell(1).innerHTML = ordered[date][i]["items"];
			tr.insertCell(2).innerHTML = ordered[date][i]["price"];
			tr.insertCell(3).innerHTML = ordered[date][i]["member"];
		};
	};
	display();
}

function display(){
	$(".gray").fadeToggle();
	$(".more").fadeToggle();
}