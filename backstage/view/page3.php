<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,
	initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="view/css/page3.css">
	<title>銷售統計</title>
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
				<div class="op_date">
					<input class="date" type="date" id="start">
					<div class="to">～</div>
					<input class="date" type="date" id="end">
				</div>
				<div class="button_search" onclick="send()">搜尋</div>
			</div>

			<div class="down">
				<!-- <div class="selected_date">銷售統計表</div> -->
				<table>
					<!-- <tr>
						<td colspan="5" class="selected_date">銷售統計表</td>
					</tr>
					<tr>
					    <th>項目</th>
					    <th>商品貨號</th>
					    <th>商品名稱</th>
					    <th>單價</th>
					    <th>銷售數量</th>
					</tr> -->
				</table>
			</div>
		</div>
	</div>
	<script src="view/ajax/view_retrieve_count.js"></script>
</body>
</html>