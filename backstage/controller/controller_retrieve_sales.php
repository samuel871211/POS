<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	#include model
	$model_path = "/backstage/model/model_retrieve_sales.php";
	include($_SERVER["DOCUMENT_ROOT"].$model_path);
	
	#create model object
	$model = new model_retrieve_sales();
	echo $model->retrieve_sales($_POST["query"]);
}
?>