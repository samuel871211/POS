<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){

	#include model
	$model_path = "/backstage/model/model_new_member.php";
	include($_SERVER["DOCUMENT_ROOT"].$model_path);
	
	#create model object
	$model = new model_new_member();
	echo $model->new_member($_POST["name"],$_POST["phone"],$_POST["birth"]);

}
?>