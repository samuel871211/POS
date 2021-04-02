function send(){
	let branch = $("#branch")[0].value;;
	let username = $("#username")[0].value;
	let password = $("#password")[0].value;
	$.ajax({
		url:'controller/controller_login.php',
		type:'POST',
		data:{
			"branch":branch,
			"username":username,
			"password":password
		},
		success:function(result){
			if (result == "OK"){window.location.assign("")}
			else {alert(result)};
		}
	})
}