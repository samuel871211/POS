<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="utf-8">
	<meta name="viewport"content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="view/css/page1.css">
	<title>商品查詢</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
	<div class="header">
		<img class="headerpic" src="view/images/top.png" >
		<div class="logo_container">
			<img class="logo" src="view/images/logo.png">
		</div>
	</div>

	<!-- <div id="content">
	<script type="text/javascript" src="js/postwo.js"></script>
	<input id="toggle-on" class="toggle toggle-left" name="toggle" value="false" type="radio" checked>
	<label for="toggle-on" class="btn">
	商品編號</label>
	<input id="toggle-off" class="toggle toggle-right" name="toggle" value="true" type="radio">
	<label for="toggle-off" class="btn">商品名稱</label>
  
	
    </div> -->
  		<input type="text" placeholder="輸入商品編號/名稱" id="query">
		<button class="s" onclick="send()">搜尋</button>
		</div>
		<div class="card">
			<h3 class="ct">商品資訊</h3>
			<h3 class="c" style="top:32.5%;">商品條碼：</h3><div class="card2">
				<p></p>
			</div>
			<h3 class="c" style="top:47.857%;">商品編號：</h3><div class="card3">
				<p></p>
			</div>
			<h3 class="c" style="top:63.214%;">商品名稱：</h3><div class="card4">
				<p></p>
			</div>
			<h3 class="c" style="top:78.571%;">目前優惠：</h3><div class="card5">
				<p></p>
			</div>
		</div>
	<script src="view/ajax/view_retrieve_item.js"></script>
</body>
</html>