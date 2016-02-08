<?php
	include_once '../includes/config.php';
	include_once '../includes/switch_types.data.inc.php';

	$database = new Config();
	$db = $database->getConnection();
	$switch_types = new DataSwitchTypes($db);

    $tmp = $_GET['id'];
	$switch_types->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');

	if($switch_types->delete()){
		header("Location: switches.php");
	} else{
		echo "<script>alert('Fail')</script>";
	}
?>
