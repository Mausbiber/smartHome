<?php
	include_once '../includes/config.php';
	include_once '../includes/clients.data.inc.php';

	$database = new Config();
	$db = $database->getConnection();
	$clients = new DataClients($db);

    $tmp = $_GET['id'];
	$clients->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');

	if($clients->delete()){
		header("Location: index.php");
	} else{
		echo "<script>alert('Fail')</script>";
	}
?>
