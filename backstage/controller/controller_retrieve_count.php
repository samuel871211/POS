<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){

	#include model
	$model_path = "/backstage/model/model_retrieve_count.php";
	include($_SERVER["DOCUMENT_ROOT"].$model_path);
	
	#create model object
	$model = new model_retrieve_count();
	$model->retrieve_count($_POST["start"],$_POST["end"]);

}
?>