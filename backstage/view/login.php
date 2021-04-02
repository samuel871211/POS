<!DOCTYPE html>
<html lang="zh-TW"> 
<head>
	<meta charset="UTF-8"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0"> 
	<link rel="stylesheet" type="text/css" href="view/css/login.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<title>登入</title>
</head>
<body>
	<div>
		<div class="header">
			<img class="headerpic" src="view/images/top.png" >
			<div class="logo_container">
				<img class="logo" src="view/images/logo.png">
			</div>

		</div>
		<div class="content">
			<div>
				<input id="branch" type="text" placeholder="請輸入分店">
				<input id="username" type="text" placeholder="請輸入帳號">
				<input id="password" type="password" placeholder="請輸入密碼">
				<button id="submit" onclick="send()" >登入</button>
			</div>
		</div>
	</div>
	<script src="view/ajax/view_login.js"></script>
</body>
</html>