function send(){
	let start = $("#start")[0].value;
	let end = $("#end")[0].value;
	if (start == "" || end == ""){return};
	$.ajax({
		url:'controller/controller_retrieve_count.php',
		type:'POST',
		data:{"start":start,"end":end},
		success:function(result){
			var die = ["Invalid query string","Connection Failed","No history","db error"];
			if (die.includes(result)){
				alert(result);
				$(".down table")[0].innerHTML = "";
			} else {
				result = JSON.parse(result);
				console.log(result);
				var down_table = $(".down table")[0];
				down_table.innerHTML = '<tr>\
											<td colspan="5" class="selected_date"></td>\
										</tr>\
										<tr>\
										    <th>項目</th>\
										    <th>商品貨號</th>\
										    <th>商品名稱</th>\
										    <th>單價</th>\
										    <th>銷售數量</th>\
										</tr>';
				$(".selected_date")[0].innerHTML = start+"~"+end+"銷售統計表";
				var keys = Object.keys(result);
				for (i=0;i<keys.length;i++){
					var tr = down_table.insertRow(down_table.rows.length);
					tr.insertCell(0).innerHTML = i+1;
					tr.insertCell(1).innerHTML = result[keys[i]].split(",")[2];
					tr.insertCell(2).innerHTML = keys[i];
					tr.insertCell(3).innerHTML = result[keys[i]].split(",")[1];
					tr.insertCell(4).innerHTML = result[keys[i]].split(",")[0];
				};
			};
		}
	})
}