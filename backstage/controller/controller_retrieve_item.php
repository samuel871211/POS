<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){

	#include model
	$model_path = "/backstage/model/model_retrieve_item.php";
	include($_SERVER["DOCUMENT_ROOT"].$model_path);
	
	#create model object
	$model = new model_retrieve_item();
	print_r($model->retrieve_item($_POST["number"],$_POST["item"]));

}
?>