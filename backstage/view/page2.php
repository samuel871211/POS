<!DOCTYPE html>
<html lang="zh-TW"> 
<head>
	<meta charset="UTF-8"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0"> 
	<link rel="stylesheet" type="text/css" href="view/css/page2.css">
	<title>查業績</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
			<div class="up">
				<div class="time">
					<div class="today">
						<input type="radio" value="today" name="a">
						<span>本日</span>
					</div>
					<div class="month">
						<input type="radio" value="month" name="a"> 
						<span>本月</span>
					</div>
					<div class="range">
						<input type="radio" value="range" name="a">
						<span>區間</span>
					</div>
					<div>
						<input id="start" type="date">
					</div>
					<div>
						<img class="minus" src="view/images/minus.png">
					</div>
					<div>
						<input id="end" type="date">
					</div>
				</div>
				<div class="search" onclick="send()">搜尋</div>
			</div>

			<div class="down"><table></table></div>
		</div>
		<div class="gray" onclick="display()">
		</div>

		<div class="more">
			<table>
				<tr>
				    <th colspan="4">2020/11/23 現金銷售表</th>
				 </tr>
				 <tr>
				    <th>時間</th>
				    <th>商品</th>
				    <th>金額</th>
				    <th>會員</th>
				 </tr>
			</table>	
		</div>
	</div>
	<script src="view/ajax/view_retrieve_sales.js"></script>
</body>
</html>