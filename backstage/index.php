<?php
if (!isset($_SESSION)){
	session_start();
};
if (!in_array("username",array_keys($_SESSION))){
	include("view/login.php");
} else if (!isset($_GET["page"])){
	include("view/page0.php");
} else if ($_GET["page"] == 1){
	include("view/page1.php");
} else if ($_GET["page"] == 2){
	include("view/page2.php");
} else if ($_GET["page"] == 3){
	include("view/page3.php");
} else if ($_GET["page"] == 4){
	include("view/page4.php");
} else {
	echo "source not found";
};
?>