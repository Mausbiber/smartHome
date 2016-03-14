<?php
	include_once '../includes/config.php';
	include_once '../includes/scheduler.data.inc.php';
	
	$database = new Config();
	$db = $database->getConnection();
	
	$scheduler = new DataScheduler($db);
	$tmp = $_GET['id'];
	$scheduler->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
	

    if($scheduler->delete()) header("location: update_status.php?usage=timerswitch_delete&id=".$tmp);
?>
