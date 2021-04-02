<!DOCTYPE html>
<html lang="zh-TW"> 
<head>
	<meta charset="UTF-8"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0"> 
	<link rel="stylesheet" type="text/css" href="view/css/page4.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet'>
	<title>會員管理</title>
</head>
<body>
	<div>
		<div class="header">
			<img class="headerpic" src="view/images/top.png" >
			<div class="logo_container">
				<img class="logo" src="view/images/logo.png">
			</div>
		</div>
		<div class="bottom">
			<div>
				<div>
					<b class="input_placeholder">姓名</b>
					<input type="text" id="name">
				</div>
				<div>
					<b class="input_placeholder">電話</b>
					<input type="text" id="phone">
				</div>
				<div>
					<b class="input_placeholder">生日</b>
					<input type="date" id="birth">
				</div>
				<div class="buttons">
					<button onclick="confirm()">註冊</button>
					<button onclick="insert()" id="insert">確認註冊</button>
					<button onclick="search()">搜尋</button>
				</div>
				<div class="status_container">
					<div class="status" style="visibility:hidden">
						註冊成功<i class="fa fa-check-circle"></i>
						<!--如果要增加圖示大小，可以調整font-size，或者在class後面加上 fa-2x(代表2倍) fa-3x(代表3倍)...類推到5倍，例如<i class="fa fa-check-circle fa-5x"></i> -->
					</div>
				</div>
			</div>
		</div>
	
		<div class="gray" onclick="display()" style="display:none"></div>

		<div class="more" style="display:none">
			<div class="moreleft">
				<img src="view/images/account.png">
				<div class="info">
					<span>姓名:</span>
					<span></span>
					<br>
					<span>電話:</span>
					<span></span>
					<br>
					<span>生日:</span>
					<span></span>
					<br>
					<span>註冊日期:</span>
					<span></span>
				</div>
			</div>
			<div class="moreright">
				<table>
					<tr>
					    <th colspan="2">消費資訊</th>
					 </tr>
					 <tr>
					    <td>總金額:</td>
					    <td>總次數:</td>
					 </tr>
					 <tr>
					    <th colspan="2">近一個月的消費資訊</th>
					 </tr>
					 <tr>
					    <td>金額:</td>
					    <td>次數:</td>
					 </tr>
					 <tr>
					    <th colspan="2">最常購買的商品</th>
					 </tr>
					 <tr>
					    <td colspan="2"><div class="showhistory" onclick="showHistory()">查看交易紀錄</div></td>
					 </tr>
				</table>
			</div>
		</div>

		<div class="more2" style="display:none">
			<table>
				<tr>
					<th>日期</th>
					<th>商品</th>
					<th>價格</th>
					<th>付款</th>
				</tr>
			</table>
		</div>
	</div>
	<script src="view/ajax/view_retrieve_member.js"></script>
</body>
</html>