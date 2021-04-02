function send(){
	let branch = $("#branch")[0].value;;
	let username = $("#username")[0].value;
	let password = $("#password")[0].value;
	$.ajax({
		url:'/signIn',
		type:'POST',
		data:{
			"branch":branch,
			"username":username,
			"password":password
		},
		success:function(result){
			if (result == "NO"){alert("查無資料")}
			else if (result == "OK"){window.location.assign("counter.html")} 
			else if (result == "no"){alert("資料庫重新連線中")};
		}
	})
}