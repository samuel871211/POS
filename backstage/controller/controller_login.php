<?php
if (!isset($_SESSION)){
	session_start();
};
// header('Access-Control-Allow-Origin: http://'.$_SERVER["HTTP_HOST"].':5000');
// ALLOW LOGIN FROM PYTHON FLASK, BUT REMOTE SET SESSION IS NOT ALLOWED
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	#include model
	$model_path = "/backstage/model/model_login.php";
	include($_SERVER["DOCUMENT_ROOT"].$model_path);
	
	#create model object
	$model = new model_login();
	echo $model->login($_POST["username"],$_POST["password"],$_POST["branch"]);
}
?>