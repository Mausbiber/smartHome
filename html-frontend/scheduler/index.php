<?php
	date_default_timezone_set('Europe/Berlin');
	
	include_once '../includes/config.php';
    include_once '../languages/lang.php';
	// include_once '../includes/scheduler.data.inc.php';
	include_once '../includes/settings.data.inc.php';
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$records_per_page = 5;
	$from_record_num = ($records_per_page * $page) - $records_per_page;
	 
	$database = new Config();
	$db = $database->getConnection();
	
	$settings = new DataSettings($db);
	$data_settings = $settings->readAll();
	$show_seconds = $data_settings['show_seconds'];
	//$show_seconds = 1;
	include_once '../includes/scheduler.data.inc.php';
	$scheduler = new DataScheduler($db);
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
    	<link href="../css/addons/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
        <link href="../css/base.css" rel="stylesheet" type="text/css">
    
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    
    <body>
    
		<!--Pseudo-Navigationsleiste mit Menu-Button und Anzeige des aktuellen Menu's-->
        <?php
            $site_name = $lang['time_switch'];
            include_once '../includes/navbar-top.php';
        ?>
        
        <!--Wrapper für die komplette Site-->
        <div id="wrapper">
    
    		<!--Navigationsleiste-->
			<?php include_once '../includes/navbar-site.php' ?>
            
			<div id="content-wrapper">
                <section class="container">

                    <div class="row">
                        <div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-offset-0 col-md-12 col-lg-offset-1 col-lg-10 widget-space">
                            <article class="first-widget">

                                <!--Widget Header-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2><?php echo $lang['cycle_times']; ?></h2>
                                    </div>
                                </div>
                                <hr>
              
                                <!--Button: Add Data-->
                                <div class="row">
                                    <div class="col-xs-6 text-left">
                                        <a class="btn btn-primary btn-big-margin" href="update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span><?php echo $lang['new_cycle_time']; ?></a>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <a class="btn btn-danger btn-big-margin" href="update_status.php?usage=timerswitch_restart&id=0" role="button"><span class='glyphicon glyphicon-refresh' aria-hidden='true'></span><?php echo $lang['restart_server']; ?></a>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <a class="btn btn-danger btn-big-margin" href="update_status.php?usage=read_sensor&id=0" role="button"><span class='glyphicon glyphicon-refresh' aria-hidden='true'></span><?php echo "read Sensor"; ?></a>
                                    </div>									
                                </div>
                                
                                <!--Data Table-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table text-left scheduler">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $lang['title']; ?></th>
                                                    <th class="hidden-xs"><?php echo $lang['switch']; ?></th>
                                                    <th class="hidden-xs"><?php echo $lang['date']; ?></th>
                                                    <th class="hidden-sm hidden-md hidden-lg"><?php echo $lang['period']; ?></th>
                                                    <th class="hidden-xs"><?php echo $lang['start']; ?></th>
                                                    <th class="hidden-xs"><?php echo $lang['stop']; ?></th>
                                                    <th class="hidden-xs"><?php echo $lang['duration']; ?></th>
                                                    <th class="hidden-xs hidden-sm"><?php echo $lang['finish']; ?></th>
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
                                                            //$strTimeStart = date("H:i", strtotime($date_start_on));
                                                            if($show_seconds == 1){
																	$strTimeStart = date("H:i:s", strtotime($date_start_on));
															} else {
																	$strTimeStart = date("H:i", strtotime($date_start_on));
															};
															if($show_seconds == 1){
																	$strTimeStop = date("H:i:s", strtotime($date_start_off));
															} else {
																	$strTimeStop = date("H:i", strtotime($date_start_off));
															};
															//$strTimeStop = date("H:i", strtotime($date_start_off));
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
				<?php include_once '../includes/scheduler.next-widget.inc.php'; ?>
                    
                </section>
            </div>
            
		</div>
    
        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/addons/jquery.mCustomScrollbar.js"></script>
        <!-- <script src="../js/navigation-scripts.js"></script> -->
        <script src="../js/addons/ie10-viewport-bug-workaround.js"></script>
		<?php include_once '../js/navigation-scripts.php'; ?>
		
		<script>
            $(window).load(function(){
				$(".scrolling-div").mCustomScrollbar({
					theme:"light",
					autoHideScrollbar: false
				});
			});
		</script>
		    
    </body>
</html>