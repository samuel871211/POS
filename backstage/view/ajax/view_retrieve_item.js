function send(){
	let query = $("#query")[0].value;
	if (query == ""){return};
	if (Number.isInteger(parseInt(query))){data = {"number":query,"item":""}}
	else {data = {"number":"","item":query}};
	$.ajax({
		url:'controller/controller_retrieve_item.php',
		type:'POST',
		data:data,
		success:function(result){
			var die = ["Invalid Query String","Connection Failed","Not Found"];
			if (die.includes(result)){alert(result)}
			else {
				result = JSON.parse(result);
				result["discount"] = JSON.parse(result["discount"]);
				console.log(result);
				$(".card2 p")[0].innerHTML = result["ean13"];
				$(".card3 p")[0].innerHTML = result["number"];
				$(".card4 p")[0].innerHTML = result["name"];
				if (result["discount"] != null){$(".card5 p")[0].innerHTML = result["discount"]["description"]+result["discount"]["dis_rate"]}
				else {$(".card4 p")[0].innerHTML = result["discount"]};
			}
		}
	})
}