<script src="../includes/timer_python_bridge.js"></script>

<?php
	include_once '../includes/config.php';
	include_once '../includes/scheduler.data.inc.php';
	
	$database = new Config();
	$db = $database->getConnection();
	
	$scheduler = new Data($db);
	$tmp = $_GET['id'];
	$scheduler->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
	
	if($scheduler->delete()){
		echo "<script type=\"text/javascript\">TimerDelete($tmp);</script>";
	} else{
		echo "<script>alert('Fail')</script>";	
	}
?>