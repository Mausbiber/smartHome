<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/sensor_types.data.inc.php';
	include_once '../includes/sensors.data.inc.php';
	include_once '../includes/settings.data.inc.php';
    include_once '../languages/lang.php';

	$records_per_page = 3;
    $startWidget = 0;

    if (isset($_GET['page_sensor_types'])) {
        $page_sensor_types = $_GET['page_sensor_types'];
        $startWidget = 1;
    } else {
        $page_sensor_types = 1;
    }
    if (isset($_GET['page_sensors'])) {
        $page_sensors = $_GET['page_sensors'];
        $startWidget = 0;
    } else {
        $page_sensors = 1;
    }

	$database = new Config();
	$db = $database->getConnection();
    
    $settings = new DataSettings($db);
    $data_settings = $settings->readAll();
	$show_seconds = $data_settings['show_seconds'];
	$records_per_page = $data_settings['scheduler_settings_page_per_view'];
    
	$sensor_types = new DataSensorTypes($db);
	$data_sensor_types = $sensor_types->readAll($page_sensor_types, (($records_per_page * $page_sensor_types) - $records_per_page), $records_per_page);
	$num_sensor_types = $data_sensor_types->rowCount();

	$sensors = new DataSensors($db);
	$data_sensors = $sensors->readAll($page_sensors, (($records_per_page * $page_sensors) - $records_per_page), $records_per_page);
	$num_sensors = $data_sensors->rowCount();


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

        <title>smartHome <?php echo $lang['sensors']; ?></title>

        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700,900' rel='stylesheet' type='text/css'>
    	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../css/addons/swiper.min.css" rel="stylesheet" type="text/css">
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
            $site_name = $lang['sensor']."".$lang['sensor_devices'];
            include_once '../includes/navbar-top.php';
        ?>

        <!--Wrapper für die komplette Site-->
        <div id="wrapper">

    		<!--Navigationsleiste-->
			<?php include_once '../includes/navbar-site.php' ?>

			<div id="content-wrapper">
                <section class="container">

                    <!--<div class="extra_padding">
                        <article class="second-widget">
                            <div class="row">
                                <div class="col-xs-3">
                                    <div class="btn-submenu">
                                        <a data-slide="1" href="#">Client Rechner</a>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="btn-submenu">
                                        <a data-slide="2" href="#">Schalter-Arten</a>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="btn-submenu">
                                        <a href="#">Schalter</a>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="btn-submenu">
                                        <a href="#">Sonstiges</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>-->

                    <div class="row">
                        <div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-offset-0 col-md-12 col-lg-offset-1 col-lg-10 widget-space">

                            <div class="swiper-container">
                                <div class="swiper-wrapper">

                                    <?php
                                        if ($num_sensor_types>0) {
                                    ?>
                                    <div class="swiper-slide">
                                        <article class="first-widget">

                                            <!--Widget Header-->
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <h2><?php echo $lang['sensors']; ?></h2>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row no_padding no_margin">
                                                <div class="col-xs-12 col-lg-offset-1 col-lg-10 no_padding">

                                                    <!--Button: Add Data-->
                                                    <div class="row">
                                                        <div class="col-xs-12 text-switch-center-left">
                                                            <a class="btn btn-primary btn-big-margin" href="sensor_update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>  <?php echo $lang['new_sensor']; ?></a>
                                                        </div>
                                                    </div>

                                                    <!--Data Table-->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table text-left scheduler">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?php echo $lang['title']; ?></th>
                                                                        <th class="hidden-xs"><?php echo $lang['client']; ?></th>
                                                                        <th class="hidden-sm hidden-md hidden-lg"></th>
                                                                        <th class="hidden-xs"><?php echo $lang['sensor_device']; ?></th>
                                                                        <th class="hidden-xs"><?php echo $lang['description']; ?></th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    if($num_sensors>0){
                                                                        echo "<tbody>";
                                                                        while ($row_sensors = $data_sensors->fetch(PDO::FETCH_ASSOC)){
                                                                            extract($row_sensors);
                                                                            echo "<tr>";
                                                                            echo "<td>".$sensors_title."</td>";
                                                                            echo "<td class='hidden-xs'>".$clients_title."</td>";
                                                                            echo "<td class='hidden-sm hidden-md hidden-lg'>(".$sensor_types_title.")<br>".$clients_title."</td>";
                                                                            echo "<td class='hidden-xs'>".$sensor_types_title."</td>";
                                                                            echo "<td class='hidden-xs'>".$sensors_description."</td>";
                                                                            echo "<td width='100px'><a class='btn btn-warning btn-sm' href='sensor_update.php?id={$sensors_id}' role='button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a> <a class='btn btn-danger btn-sm' href='sensor_delete.php?id={$sensors_id}' role='button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></td>";
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
                                                                if($num_sensors>0){
                                                                    $page_sensors_dom = "sensors.php";
                                                                    include_once '../includes/sensors.pagination.inc.php';
                                                                } else {
                                                                    echo "<br>";
                                                                    echo "<p>Keine Daten vorhanden</p>";
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </article>
                                    </div>
                                    <?php
                                        }
                                    ?>
                                    <div class="swiper-slide">
                                        <article class="first-widget">

                                            <!--Widget Header-->
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <h2><?php echo $lang['sensor_devices']; ?></h2>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row no_padding no_margin">
                                                <div class="col-xs-12 col-lg-offset-1 col-lg-10 no_padding">

                                                    <!--Button: Add Data-->
                                                    <div class="row">
                                                        <div class="col-xs-12 text-switch-center-left">
                                                            <a class="btn btn-primary btn-big-margin" href="sensor_type_update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>  <?php echo $lang['new_sensor_device']; ?></a>
                                                        </div>
                                                    </div>

                                                    <!--Data Table-->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table text-left scheduler">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?php echo $lang['title']; ?></th>
                                                                        <th><?php echo $lang['icon']; ?></th>
                                                                        <th class="hidden-xs"><?php echo $lang['description']; ?></th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    if($num_sensor_types>0){
                                                                        echo "<tbody>";
                                                                        while ($row_sensor_types = $data_sensor_types->fetch(PDO::FETCH_ASSOC)){
                                                                            extract($row_sensor_types);
                                                                            echo "<tr>";
                                                                            echo "<td>".$sensor_types_title."</td>";
                                                                            echo "<td><img src='../img/icons/".$sensor_types_icon."'></td>";
                                                                            echo "<td class='hidden-xs'>".$sensor_types_description."</td>";
                                                                            echo "<td width='100px'><a class='btn btn-warning btn-sm' href='sensor_type_update.php?id={$sensor_types_id}' role='button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a> <a class='btn btn-danger btn-sm' href='sensor_type_delete.php?id={$sensor_types_id}' role='button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></td>";
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
                                                                if($num_sensor_types>0){
                                                                    $page_sensor_types_dom = "sensors.php";
                                                                    include_once '../includes/sensor_types.pagination.inc.php';
                                                                } else {
                                                                    echo "<br>";
                                                                    echo "<p>Keine Daten vorhanden</p>";
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </article>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                </section>
            </div>

		</div>

        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/addons/swiper.jquery.min.js"></script>
        <?php include_once '../js/navigation-scripts.php'; ?>
        <script src="../js/addons/ie10-viewport-bug-workaround.js"></script>
        <script>
            $(document).ready(function () {

                // var $window = $(window);
                // Function to handle changes to style classes based on window width
                // function checkWidth() {
                //     if ($window.width() > 1800) {
                //         $('#swiperC').removeClass('swiper-container');
                //         $('#swiperW').removeClass('swiper-wrapper');
                //         $('#swiperS').removeClass('swiper-slide');
                //     } else {
                //         $('#swiperC').addClass('swiper-container');
                //         $('#swiperW').addClass('swiper-wrapper');
                //         $('#swiperS').addClass('swiper-slide');
                //     }
                // }
                // Execute on load
                // checkWidth();
                // Bind event listener
                // $(window).resize(checkWidth);

                //initialize swiper when document ready
                var startWidget = <?php echo($startWidget); ?>;
                var mySwiper = new Swiper ('.swiper-container', {
                    loop: true,
                    initialSlide: startWidget
                })


            });
        </script>
    </body>
</html>
