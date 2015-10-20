<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/scheduler.data.inc.php';
	 
	$database = new Config();
	$db = $database->getConnection();
	$timer = new Data($db);
	$switches = $timer->readSwitches();

	if (isset($_GET['id'])) {
		$timer->id = $_GET['id'];
		$timer->readOne();
	} else {
		$id = -1;
		$timer->duration = 'einmalig';
	}
	
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

    	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
    	<link href="../css/bootstrap-clockpicker.min.css" rel="stylesheet" type="text/css">
    	<link href="../css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css">
        <link href="../css/base.css" rel="stylesheet" type="text/css">
        <link href="../css/timeline.css" rel="stylesheet" type="text/css">
        <link href="../css/forms.css" rel="stylesheet" type="text/css">
    
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

		<script src="../includes/timer_python_bridge.js"></script>
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
                        <div class="col-xs-12 col-md-7 col-lg-8 col-widget">
                            <article class="first-widget">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2>Schaltplan ändern</h2>
                                    </div>
                                </div>
                                <hr>
                                <?php
                                    if($_POST){
                                     
                                        $timer->title = $_POST['title'];
                                        $timer->switchCommand = $_POST['switch_command'];
                                        $timer->strDateStart = $_POST['str_date_start'];
                                        $timer->strTimeStart = $_POST['str_time_start'];
                                        $timer->strTimeStop = $_POST['str_time_stop'];
                                        $timer->strDateStop = $_POST['str_date_stop'];
                                        $timer->duration = $_POST['duration'];
                                        $timer->intervalNumber = $_POST['interval_number'];
                                        $timer->intervalUnit = $_POST['interval_unit'];
                                        $timer->weeklyMonday = $_POST['weekly_monday'];
                                        $timer->weeklyTuesday = $_POST['weekly_tuesday'];
                                        $timer->weeklyWednesday = $_POST['weekly_wednesday'];
                                        $timer->weeklyThursday = $_POST['weekly_thursday'];
                                        $timer->weeklyFriday = $_POST['weekly_friday'];
                                        $timer->weeklySaturday = $_POST['weekly_saturday'];
                                        $timer->weeklySunday = $_POST['weekly_sunday'];
                                        
                                        if ($id<0) {
                                            $tmp = $timer->create();
                                            if($tmp>0) echo "<script type=\"text/javascript\">TimerNew($tmp);</script>";
                                        } else {
                                            $tmp = $timer->update();
                                            if($tmp) echo "<script type=\"text/javascript\">TimerUpdate($timer->id);</script>";
                                        }
    
                                     }
                                ?>
                                <form method="post">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-5 col-lg-5">
                                            <div class="row form-inline">
                                                <div class="col-xs-4 col-md-4 col-lg-5 text-right">
                                                    <label for="title" class="control-label">Name</label>
                                                </div>
                                                <div class="col-xs-6 col-md-8 col-lg-7 no_padding text-left">
                                                    <input type="text" class="form-control title-scheduler" name="title" id="title" value='<?php echo $timer->title; ?>' required>                                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-7">
                                            <div class="row form-inline">
                                                <div class="col-xs-4 col-md-3 text-right">
                                                    <label for="device" class="control-label">Schalter</label>
                                                </div>
                                                <div class="col-xs-6 no_padding text-left">
                                                    <select class="form-control" name="switch_command" id="switch_command">
                                                        <?php
                                                            $switchesID = $timer->switchesID; 
                                                            while ($sw_row = $switches->fetch(PDO::FETCH_ASSOC)){
                                                                $s_id = $sw_row['id'];
                                                                $s_title = $sw_row['title'];
                                                                if(intval($s_id) == intval($switchesID)) {
                                                                    echo "<option selected>".$s_title."</option>";
                                                                } else {
                                                                    echo "<option>".$s_title."</option>";
                                                                }
                                                            }
                                                        ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-xs-12 col-md-5 col-lg-5">
                                            <div class="row form-inline">
                                                <div class="col-xs-4 col-md-4 col-lg-5 text-right">
                                                    <label for="str_date_start" class="control-label">Datum</label>
                                                </div>
                                                <div class="col-xs-6 col-md-8 col-lg-7 text-left no_padding ">
                                                    <div class="input-group date datepicker no_padding ">
                                                        <input type="text" id="str_date_start" class="form-control" name="str_date_start" value='<?php echo $timer->strDateStart; ?>' required readonly><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-7">
                                            <div class="row form-inline">
                                                <div class="col-xs-4 col-md-3 col-lg-3 text-right">
                                                    <label for="str_time_start" class="control-label">von</label>
                                                </div>
                                                <div class="col-xs-6 col-md-4 col-lg-3 no_padding text-left">
                                                    <div class="input-group clockpicker">
                                                        <input type="text" class="form-control" id="str_time_start" name="str_time_start" value='<?php echo $timer->strTimeStart; ?>' required readonly><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                                    </div>
                                                </div>
                                                <div class="clearfix visible-xs-block"></div>
                                                <div class="col-xs-4 col-md-1 col-lg-1 no_padding_extra text-switch-right-center">
                                                    <label for="str_time_stop" class="control-label">bis</label>
                                                </div>
                                                <div class="col-xs-6 col-md-4 col-lg-3 no_padding text-left">
                                                    <div class="input-group clockpicker">
                                                        <input type="text" class="form-control" id="str_time_stop" name="str_time_stop" value='<?php echo $timer->strTimeStop; ?>' required readonly><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                                       
                                    <div class="row">
                                        <div class="col-xs-12 col-md-5 col-lg-5">
                                            <div class="row form-inline">
                                                <div class="col-xs-4 col-md-4 col-lg-5 text-right">
                                                    <label for="str_date_stop" class="control-label">Ende</label>
                                                </div>
                                                <div class="col-xs-6 col-md-8 col-lg-7 no_padding text-left">
                                                    <div class="input-group date datepicker no_padding ">
                                                      <input type="text" id="str_date_stop" class="form-control" name="str_date_stop" value='<?php if ($timer->dateStop) echo $timer->strDateStop; ?>' readonly><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-7">                                    
                                            <div class="row">
                                                <div class="col-xs-4 col-md-3 text-right">
                                                    <label class="control-label">Dauer</label>
                                                </div>
                                                <div class="col-xs-7 col-xs-offset-1 col-md-offset-0 no_padding text-left">
                                                    <div class="form-group">
                                                        <label class="radio-scheduler"><input name="duration" id="duration" value="einmalig" type="radio" class="radio-scheduler" <?php if ($timer->duration=='einmalig') echo "checked" ?>>einmalig Durchlauf</label>
                                                        <label class="radio-scheduler"><input name="duration" id="duration" value="intervall" type="radio" class="radio-scheduler" data-toggle="modal" data-target="#modalIntervall" <?php if ($timer->duration=='intervall') echo "checked" ?>>Wiederholung in Intervallen</label>
                                                        <label class="radio-scheduler"><input name="duration" id="duration" value="wochentag" type="radio" class="radio-scheduler" data-toggle="modal" data-target="#modalWochentag" <?php if ($timer->duration=='wochentag') echo "checked" ?>>Wiederholung an Wochentagen</label>
                                                    </div>												
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    

                                                
                                    <div class="row">
                                        <div class="col-xs-12 col-md-5 col-lg-5">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-8 col-md-offset-4 col-lg-offset-5 text-switch-center-left no_padding">
                                                    <button type="submit" name="submit" class="btn btn-primary "><span class='glyphicon glyphicon-ok' aria-hidden='true'></span>  Änderungen speichern</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-7">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-7 col-md-offset-3 text-switch-center-left no_padding">
                                                    <a class="btn btn-default" href="index.php" role="button"><span class='glyphicon glyphicon-remove' aria-hidden='true'></span>  Abbrechen</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="modal" id="modalIntervall" tabindex="-1" role="dialog" aria-labelledby="modalIntervallLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="modalIntervallLabel">Intervall</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row form-inline">
                                                        <div class="col-xs-4 col-sm-3 col-sm-offset-1 text-right">
                                                            <label for="interval_number" class="control-label">wiederhole alle:</label>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-2">
                                                            <div class="input-group">
                                                                <input type="number" id="interval_number" class="form-control" name="interval_number" value='<?php echo $timer->intervalNumber; ?>'>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-4 col-sm-3 no_padding">
                                                            <select class="form-control" name="interval_unit" id="interval_unit" value='<?php echo $timer->intervalUnit; ?>'>
                                                              <option <?php if ($timer->intervalUnit=='Minuten') echo "selected"?>>Minuten</option>
                                                              <option <?php if ($timer->intervalUnit=='Stunden') echo "selected"?>>Stunden</option>
                                                              <option <?php if ($timer->intervalUnit=='Tage') echo "selected"?>>Tage</option>
                                                              <option <?php if ($timer->intervalUnit=='Wochen') echo "selected"?>>Wochen</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                                          
                                    <div class="modal" id="modalWochentag" tabindex="-1" role="dialog" aria-labelledby="modalWochentagLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="modalWochentagLabel">Intervall</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-xs-4 col-sm-3 col-sm-offset-1 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_monday" id="weekly_monday" <?php if ($timer->weeklyMonday) echo "checked" ?>> Montag</label>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-3 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_tuesday" id="weekly_tuesday" <?php if ($timer->weeklyTuesday) echo "checked" ?>> Dienstag</label>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-3 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_wednesday" id="weekly_wednesday" <?php if ($timer->weeklyWednesday) echo "checked" ?>> Mittwoch</label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-4 col-sm-3 col-sm-offset-1 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_thursday" id="weekly_thursday" <?php if ($timer->weeklyThursday) echo "checked" ?>> Donnerstag</label>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-3 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_friday" id="weekly_friday" <?php if ($timer->weeklyFriday) echo "checked" ?>> Freitag</label>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-3 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_saturday" id="weekly_saturday" <?php if ($timer->weeklySaturday) echo "checked" ?>> Samstag</label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-4 col-sm-3 col-sm-offset-1 text-left">
                                                                <label class="control-label"><input type="checkbox" value="true" name="weekly_sunday" id="weekly_sunday" <?php if ($timer->weeklySunday) echo "checked" ?>> Sonntag</label>
                                                            </div>
                                                        </div>
                                                        <br>
                                                    </fieldset>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                </form>
                            </article>
                        </div>  
                    
                        <div class="col-xs-12 col-md-5 col-lg-4 col-widget">
                        	<article class="second-widget">
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
    	<script src="../js/bootstrap-clockpicker.min.js"></script>
    	<script src="../js/bootstrap-datepicker.min.js"></script>
        <script src="../js/validator.js"></script>
        <script src="../js/ie10-viewport-bug-workaround.js"></script>
		<script>
	
			$("#menu-toggle").click(function(e) {
					e.preventDefault();
					$("#wrapper").toggleClass("toggled");
			});
			
			$('.clockpicker').clockpicker({
				donetext: 'Fertig',
				'default': 'now'
			});
			
			$('.input-group.date').datepicker({
				format: "dd.mm.yyyy",
				language: "de",
				orientation: "top auto",
				autoclose: true
			});
		</script>
		    
    </body>
</html>