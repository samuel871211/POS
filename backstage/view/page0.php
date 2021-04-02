<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="utf-8">
	<meta name="viewport"content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="view/css/page0.css">
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet'></link>
	<title>Home</title>
</head>
<body>
	<div>
		<div class="header">
			<img class="headerpic" src="view/images/top.png" >
			<div class="logo_container">
				<img class="logo" src="view/images/logo.png">
		</div>
	</div>
	<div id="back"><i class="fa fa-arrow-circle-left fa-5x" onclick="window.location.assign('http://'+window.location.hostname+':5000/counter.html')"></i></div>
	<div class="content">
		<div class="a" onclick="window.location.assign('?page=1')">
			<img class="p1" src="view/images/1.png">
			<h2>商品查詢</h2>
		</div>
		<div class="b"  onclick="window.location.assign('?page=2')">
			<img style="width: 19%;position: absolute;left:39%;" class="p1" src="view/images/2.png">
			<h2>查業績</h2>
			
		</div>
		<div class="c" onclick="window.location.assign('?page=3')">
			<img class="p1" style="width: 28%;" src="view/images/3.png">
			<h2>銷售統計</h2>
		</div>
		<div class="d" onclick="window.location.assign('?page=4')">
			<img class="p1"src="view/images/4.png">
			<h2>會員管理</h2>
		</div>
		<div class="e"  onclick="window.location.assign('?page=5')">
			<img class="p1" src="view/images/5.png">
			<h2>進銷存</h2>
		</div>
		<div class="f" onclick="window.location.assign('?page=6')">
			<img class="p1" src="view/images/6.png">
			<h2>出勤紀錄</h2>
		</div>
		
	</div>

</body>
</html>