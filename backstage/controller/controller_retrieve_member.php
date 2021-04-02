<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){

	#include model
	$model_path = "/backstage/model/model_retrieve_member.php";
	include($_SERVER["DOCUMENT_ROOT"].$model_path);
	
	#create model object
	$model = new model_retrieve_member();
	$model->retrieve_member($_POST["name"],$_POST["phone"],$_POST["birth"]);
}
?>