<?php
	date_default_timezone_set('Europe/Berlin');
	
	include_once '../includes/config.php';
	include_once '../includes/scheduler.data.inc.php';

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$records_per_page = 5;
	$from_record_num = ($records_per_page * $page) - $records_per_page;
	 
	$database = new Config();
	$db = $database->getConnection();
	$scheduler = new Data($db);
	$data_schedulers = $scheduler->readAll($page, $from_record_num, $records_per_page);
	$num_schedulers = $data_schedulers->rowCount();
	
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>smartHome Zeitschaltuhr</title>
    
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700,900' rel='stylesheet' type='text/css'>
    	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
    	<link href="../css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
        <link href="../css/base.css" rel="stylesheet" type="text/css">
    
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    
    <body>
    
		<!--Pseudo-Navigationsleiste mit Menu-Button und Anzeige des aktuellen Menu's-->
    	<?php include_once '../includes/navbar-top.php' ?>
        
        <!--Wrapper für die komplette Site-->
        <div id="wrapper">
    
    		<!--Navigationsleiste-->
			<?php include_once '../includes/navbar-site.php' ?>
            
			<div id="content-wrapper">
                <section class="container">

                    <div class="row">
                        <div class="col-xs-12 col-lg-offset-1 col-lg-10 widget-space">
                            <article class="first-widget">

                                <!--Widget Header-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2>Schaltzeiten</h2>
                                    </div>
                                </div>
                                <hr>
              
                                <!--Button: Add Data-->
                                <div class="row">
                                    <div class="col-xs-12 text-switch-center-left">
                                        <a class="btn btn-primary btn-big-margin" href="update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>  Neuer Schaltplan</a>
                                    </div>
                                </div>
                                
                                <!--Data Table-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table text-left scheduler">
                                            <thead>
                                                <tr>
                                                    <th>Bezeichnung</th>
                                                    <th class="hidden-xs">Schalter</th>
                                                    <th class="hidden-xs">Datum</th>
                                                    <th class="hidden-sm hidden-md hidden-lg">Zeitraum</th>
                                                    <th class="hidden-xs">Start</th>
                                                    <th class="hidden-xs">Stop</th>
                                                    <th class="hidden-xs">Dauer</th>
                                                    <th class="hidden-xs hidden-sm">Ende</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <?php
                                                if($num_schedulers>0){
                                                    echo "<tbody>";
                                                    while ($row = $data_schedulers->fetch(PDO::FETCH_ASSOC)){
                                                        extract($row);
                                                        echo "<tr>";
                                                            echo "<td>".$scheduler_title."</td>";
                                                            echo "<td class='hidden-xs'><img src='../img/icons/".$switch_icon."'>".$switch_title."</td>"; 
                                                            $strDateStart = date("d.m.Y", strtotime($date_start_on));
                                                            $strTimeStart = date("H:i", strtotime($date_start_on));
                                                            $strTimeStop = date("H:i", strtotime($date_start_off));
                                                            $zeitraum = $strTimeStart."-".$strTimeStop;
                                                            echo "<td class='hidden-xs'>".$strDateStart."</td>" ;
                                                            echo "<td class='hidden-sm hidden-md hidden-lg'>".$zeitraum."<br>(".$duration.")</td>"; 
                                                            echo "<td class='hidden-xs'>".$strTimeStart."</td>"; 
                                                            echo "<td class='hidden-xs'>".$strTimeStop."</td>"; 
                                                            echo "<td class='hidden-xs'>".$duration."</td>"; 
                                                            if ($date_stop>0) {
                                                                $strDateStop = date("d.m.Y", strtotime($date_stop_on));
                                                                echo "<td class='hidden-xs hidden-sm'>".$strDateStop."</td>";
                                                            } else {
                                                                if ($duration=="einmalig") {
                                                                    echo "<td class='hidden-xs hidden-sm'></td>";
                                                                } else {
                                                                    echo "<td class='hidden-xs hidden-sm'>Nein</td>";
                                                                }
                                                            }
                                                            echo "<td width='100px'><a class='btn btn-warning btn-sm' href='update.php?id={$scheduler_id}' role='button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a> <a class='btn btn-danger btn-sm' href='delete.php?id={$scheduler_id}' role='button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></td>";
                                                        echo "</tr>";
                                                    }
                                                    echo "</tbody>";
                                                }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                                
                                <!--Pagination-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?php 
                                            if($num_schedulers>0){ 
                                                $page_dom = "index.php";
                                                include_once '../includes/scheduler.pagination.inc.php';
                                            } else {
                                                echo "<br>";
                                                echo "<p>Keine Daten vorhanden</p>";
                                            }
                                         ?>
                                    </div>
                                </div>  
                            
                            </article>
                        </div>
                    </div>
                    
                    <!--Vorschau-Widget-->
					<?php include_once '../includes/scheduler.next-widget.inc.php' ?>
                    
                </section>
            </div>
            
		</div>
    
        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/jquery.mCustomScrollbar.js"></script>
        <script src="../js/navigation-scripts.js"></script>
        <script src="../js/validator.js"></script>
        <script src="../js/ie10-viewport-bug-workaround.js"></script>

		<script>
			var socketServer = new WebSocket('ws://192.168.127.30:5554');
			socketServer.onerror = function(error) {};
			socketServer.onopen = function(event) {};
			socketServer.onmessage = function(event) {};
			socketServer.onclose = function(event) {};
				
			$(window).load(function(){
				$(".scrolling-div").mCustomScrollbar({
					theme:"light",
					autoHideScrollbar: false
				});
			});
		</script>
		    
    </body>
</html>