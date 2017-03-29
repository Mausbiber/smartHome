<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/sensors.data.inc.php';
	include_once '../includes/clients.data.inc.php';
	include_once '../includes/settings.data.inc.php';	
    include_once '../languages/lang.php';

	$database = new Config();
	$db = $database->getConnection();
	$sensors = new DataSensors($db);
    $list_of_clients = $sensors->readClients();
    $list_of_sensor_types = $sensors->readTypes();

	$settings = new DataSettings($db);
    $data_settings = $settings->readAll();
	$show_seconds = $data_settings['show_seconds'];
	
	if (isset($_GET['id'])) {
		$sensors->id = $_GET['id'];
		$sensors->readOne();
	} else {
		$id = -1;
	}

    if($_POST){
        $sensors->title = htmlentities(strip_tags($_POST['title']));
        $sensors->description = htmlentities(strip_tags($_POST['description']));
        $sensors->clientTitle = $_POST['client_title'];
        $sensors->sensorTypTitle = $_POST['sensor_type_title'];
        $sensors->argA = htmlentities(strip_tags($_POST['arg_a']));
        $sensors->argB = htmlentities(strip_tags($_POST['arg_b']));
        $sensors->argC = htmlentities(strip_tags($_POST['arg_c']));
        $sensors->argD = htmlentities(strip_tags($_POST['arg_d']));

        if ($id<0) {
            $tmp = $sensors->create();
            if($tmp) header("Location: sensors.php");
        } else {
            $tmp = $sensors->update();
            if($tmp) header("Location: sensors.php");
        }
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

        <title>smartHome Einstellungen</title>

        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700,900' rel='stylesheet' type='text/css'>
    	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../css/base.css" rel="stylesheet" type="text/css">
        <link href="../css/forms.css" rel="stylesheet" type="text/css">

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

		<!--Pseudo-Navigationsleiste mit Menu-Button und Anzeige des aktuellen Menu's-->
        <?php
            $site_name = $lang['settings']." - ".$lang['sensors'];
            include_once '../includes/navbar-top.php';
        ?>

        <!--Wrapper für die komplette Site-->
        <div id="wrapper">

    		<!--Navigationsleiste-->
			<?php include_once '../includes/navbar-site.php' ?>

			<div id="content-wrapper">
	            <section class="container">

                    <div class="row">
                        <div class="col-xs-12 col-md-offset-3 col-md-6 widget-space">
                            <article class="first-widget">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <?php
                                            if ($id<0) {
                                                echo "<h2>".$lang['create_sensor']."</h2>";
                                            } else {
                                                echo "<h2>".$lang['modify_sensor']."</h2>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <hr>
                                <form method="post">

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="title" class="control-label"><?php echo $lang['title']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <input type="text" class="form-control" name="title" id="title" placeholder="" value='<?php echo $sensors->title; ?>' required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="client_title" class="control-label"><?php echo $lang['client']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-2 col-md-3 no_padding text-left">
                                            <select class="form-control" name="client_title" id="client_title">
                                                <?php
                                                    while ($tmp_row = $list_of_clients->fetch(PDO::FETCH_ASSOC)){
                                                        $tmp_title = $tmp_row['title'];
                                                        if($tmp_title == $sensors->clientTitle) {
                                                            echo "<option selected>".$tmp_title."</option>";
                                                        } else {
                                                            echo "<option>".$tmp_title."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="sensor_type_title" class="control-label"><?php echo $lang['sensor_device']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-2 col-md-3 no_padding text-left">
                                            <select class="form-control" name="sensor_type_title" id="sensor_type_title">
                                                <?php
                                                    while ($tmp_row = $list_of_sensor_types->fetch(PDO::FETCH_ASSOC)){
                                                        $tmp_title = $tmp_row['title'];
                                                        if($tmp_title == $sensors->sensorTypTitle) {
                                                            echo "<option selected>".$tmp_title."</option>";
                                                        } else {
                                                            echo "<option>".$tmp_title."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label class="control-label"><?php echo $lang['arguments']; ?></label>
                                        </div>
                                        <div class="col-xs-5 col-sm-4 col-md-7">
                                            <div class="row">
                                                <div class="col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_a" id="arg_a" placeholder="Arg A" value='<?php echo $sensors->argA; ?>'>
                                                </div>
                                                <div class="col-md-offset-1 col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_b" id="arg_b" placeholder="Arg B" value='<?php echo $sensors->argB; ?>'>
                                                </div>
                                                <div class="col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_c" id="arg_c" placeholder="Arg C" value='<?php echo $sensors->argC; ?>'>
                                                </div>
                                                <div class="col-md-offset-1 col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_d" id="arg_d" placeholder="Arg D" value='<?php echo $sensors->argD; ?>'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="description" class="control-label"><?php echo $lang['description']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <textarea style="width:100%" name="description" id="description" class="form-control" rows="3" placeholder=""><?php echo $sensors->description; ?></textarea>
                                        </div>
                                    </div>


                                    <div class="row" style="margin-top: 24px">
                                        <div class="col-xs-12 col-sm-6 text-switch-center-right no_padding">
                                            <button type="submit" name="submit" class="btn btn-primary btn-fix-width"><span class='glyphicon glyphicon-ok' aria-hidden='true'></span>  <?php echo $lang['save']; ?></button>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 text-switch-center-left no_padding">
                                            <a class="btn btn-default btn-fix-width" href="sensors.php" role="button"><span class='glyphicon glyphicon-remove' aria-hidden='true'></span>  <?php echo $lang['cancle']; ?></a>
                                        </div>
                                    </div>



                                </form>
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
        <?php include_once '../js/navigation-scripts.php'; ?>
        <script src="../js/addons/ie10-viewport-bug-workaround.js"></script>
		<script>
		</script>

    </body>
</html>
