<?php
	date_default_timezone_set('Europe/Berlin');
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	
	include_once '../includes/config.php';
	include_once '../includes/scheduler.data.inc.php';

	$records_per_page = 10;
	$from_record_num = ($records_per_page * $page) - $records_per_page;
	 
	$database = new Config();
	$db = $database->getConnection();
	$timer = new Data($db);
	$stmt = $timer->readAll($page, $from_record_num, $records_per_page);
	$num_schedulers = $stmt->rowCount();
	
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
    
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Anonymous+Pro:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        
    	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../css/timeline.css" rel="stylesheet" type="text/css">
        <link href="../css/base.css" rel="stylesheet" type="text/css">
    
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    
    <body>
    
		<!--Pseudo-Navigationsleiste mit Menu-Button und Anzeige des aktuellen Menu's-->
        <nav class="navbar-inverse navbar-fixed-top">
			<a href="#menu-toggle" class="navbar-toggle" id="menu-toggle">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
            </a>
            <div class="navbar-brand">Zeitschaltuhr</div>
        </nav>
    
        <!--Wrapper für die komplette Site-->
        <div id="wrapper">
    
    		<!--Navigationsleiste-->
			<div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Zeitschaltuhr</a></li>
                    <li><a href="#">Temperatur</a></li>
                </ul>
			</div>

			<div id="content-wrapper">
                <section class="container">
                    <div class="row">
                       
                        <div class="col-xs-12 col-md-7 col-lg-8">
                        	<div class="row">
                                <div class="col-xs-12 col-widget">
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
                                                <div class="table-responsive scheduler">
                                                    <table class="table active text-left scheduler">
                                                        <thead>
                                                            <tr>
                                                                <th>Bezeichnung</th>
                                                                <th class="hidden-xs">Schalter</th>
                                                                <th class="hidden-xs">Datum</th>
                                                                <th>Start</th>
                                                                <th>Stop</th>
                                                                <th>Dauer</th>
                                                                <th class="hidden-xs hidden-sm hidden-md">Ende</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                            if($num_schedulers>0){
                                                                echo "<tbody>";
                                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                                    extract($row);
                                                                    echo "<tr>";
                                                                        echo "<td>".$scheduler_title."</td>";
                                                                        echo "<td class='hidden-xs'>".$switch_title."</td>"; 
                                                                        $strDateStart = date("d.m.Y", strtotime($date_start_on));
                                                                        $strTimeStart = date("H:i", strtotime($date_start_on));
                                                                        $strTimeStop = date("H:i", strtotime($date_start_off));
                                                                        echo "<td class='hidden-xs'>".$strDateStart."</td>" ;
                                                                        echo "<td>".$strTimeStart."</td>"; 
                                                                        echo "<td>".$strTimeStop."</td>"; 
                                                                        echo "<td>".$duration."</td>"; 
                                                                        if ($date_stop>0) {
                                                                            $strDateStop = date("d.m.Y", strtotime($date_stop_on));
                                                                            echo "<td class='hidden-xs hidden-sm hidden-md'>".$strDateStop."</td>";
                                                                        } else {
                                                                            if ($duration=="einmalig") {
                                                                                echo "<td class='hidden-xs hidden-sm hidden-md'></td>";
                                                                            } else {
                                                                                echo "<td class='hidden-xs hidden-sm hidden-md'>Nein</td>";
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
                                <div class="col-xs-12 col-widget">
		                            <article class="status-widget hidden-xs">
										<p class='status-text'>Server Status wird geprüft ...</p>
                    		        </article>
                            	</div>
                            </div>                                                         
						</div>
       
                        <div class="col-xs-12 col-md-5 col-lg-4 col-widget">
                        	<article class="second-widget">
                            
                            	<!--Timeline-->
								<?php include_once '../includes/scheduler.next-widget.inc.php' ?>
    						
                            </article>
                        </div>
                    </div>
                </section>
            </div>
		</div>
    
    
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/validator.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../js/ie10-viewport-bug-workaround.js"></script>
		<script>
			var socketServer = new WebSocket('ws://192.168.127.30:5554');

			socketServer.onerror = function(error) {
				$('.status-text').text("Server ist offline");
				$('.status-widget').addClass("status-widget-bad");
				window.setTimeout(function(){$('.status-widget').addClass("hide")},30000);
				console.log('WebSocket Error : ' + error);
				};

			socketServer.onopen = function(event) {
				$('.status-text').text("Server ist online");
				$('.status-widget').addClass("status-widget-good");
				window.setTimeout(function(){$('.status-widget').addClass("hide")},30000);
				};
			
			socketServer.onmessage = function(event) {};
			
			socketServer.onclose = function(event) {};
		
			$("#menu-toggle").click(function(e) {
				e.preventDefault();
				$("#wrapper").toggleClass("toggled");
			});
		</script>
		    
    </body>
</html>