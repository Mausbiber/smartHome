<?php
	include_once '../includes/config.php';
	include_once '../includes/switches.data.inc.php';

	$database = new Config();
	$db = $database->getConnection();
	$switches = new DataSwitches($db);

    $tmp = $_GET['id'];
	$switches->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');

	if($switches->delete()){
		header("Location: switches.php");
	} else{
		echo "<script>alert('Fail')</script>";
	}
?>
