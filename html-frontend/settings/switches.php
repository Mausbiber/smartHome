<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/switch_types.data.inc.php';
	include_once '../includes/switches.data.inc.php';

	$records_per_page = 3;
    $startWidget = 0;

    if (isset($_GET['page_switch_types'])) {
        $page_switch_types = $_GET['page_switch_types'];
        $startWidget = 1;
    } else {
        $page_switch_types = 1;
    }
    if (isset($_GET['page_switches'])) {
        $page_switches = $_GET['page_switches'];
        $startWidget = 0;
    } else {
        $page_switches = 1;
    }

	$database = new Config();
	$db = $database->getConnection();

	$switch_types = new DataSwitchTypes($db);
	$data_switch_types = $switch_types->readAll($page_switch_types, (($records_per_page * $page_switch_types) - $records_per_page), $records_per_page);
	$num_switch_types = $data_switch_types->rowCount();

	$switches = new DataSwitches($db);
	$data_switches = $switches->readAll($page_switches, (($records_per_page * $page_switches) - $records_per_page), $records_per_page);
	$num_switches = $data_switches->rowCount();


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

        <title>smartHome Settings</title>

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
            $site_name = "Einstellungen";
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
                                        if ($num_switch_types>0) {
                                    ?>
                                    <div class="swiper-slide">
                                        <article class="first-widget">

                                            <!--Widget Header-->
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <h2>Schalter</h2>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row no_padding no_margin">
                                                <div class="col-xs-12 col-lg-offset-1 col-lg-10 no_padding">

                                                    <!--Button: Add Data-->
                                                    <div class="row">
                                                        <div class="col-xs-12 text-switch-center-left">
                                                            <a class="btn btn-primary btn-big-margin" href="switch_update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>  Neuer Schalter</a>
                                                        </div>
                                                    </div>

                                                    <!--Data Table-->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table text-left scheduler">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Bezeichnung</th>
                                                                        <th class="hidden-xs">Client</th>
                                                                        <th class="hidden-sm hidden-md hidden-lg"></th>
                                                                        <th class="hidden-xs">Schalter-Art</th>
                                                                        <th class="hidden-xs">Beschreibung</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    if($num_switches>0){
                                                                        echo "<tbody>";
                                                                        while ($row_switches = $data_switches->fetch(PDO::FETCH_ASSOC)){
                                                                            extract($row_switches);
                                                                            echo "<tr>";
                                                                            echo "<td>".$switches_title."</td>";
                                                                            echo "<td class='hidden-xs'>".$clients_title."</td>";
                                                                            echo "<td class='hidden-sm hidden-md hidden-lg'>(".$switch_types_title.")<br>".$clients_title."</td>";
                                                                            echo "<td class='hidden-xs'>".$switch_types_title."</td>";
                                                                            echo "<td class='hidden-xs'>".$switches_description."</td>";
                                                                            echo "<td width='100px'><a class='btn btn-warning btn-sm' href='switch_update.php?id={$switches_id}' role='button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a> <a class='btn btn-danger btn-sm' href='switch_delete.php?id={$switches_id}' role='button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></td>";
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
                                                                if($num_switches>0){
                                                                    $page_switches_dom = "switches.php";
                                                                    include_once '../includes/switches.pagination.inc.php';
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
                                                    <h2>Schalter-Arten/-Typen</h2>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row no_padding no_margin">
                                                <div class="col-xs-12 col-lg-offset-1 col-lg-10 no_padding">

                                                    <!--Button: Add Data-->
                                                    <div class="row">
                                                        <div class="col-xs-12 text-switch-center-left">
                                                            <a class="btn btn-primary btn-big-margin" href="switch_type_update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>  Neue Art von Schalter</a>
                                                        </div>
                                                    </div>

                                                    <!--Data Table-->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table text-left scheduler">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Bezeichnung</th>
                                                                        <th>Icon</th>
                                                                        <th class="hidden-xs">Beschreibung</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    if($num_switch_types>0){
                                                                        echo "<tbody>";
                                                                        while ($row_switch_types = $data_switch_types->fetch(PDO::FETCH_ASSOC)){
                                                                            extract($row_switch_types);
                                                                            echo "<tr>";
                                                                            echo "<td>".$switch_types_title."</td>";
                                                                            echo "<td><img src='../img/icons/".$switch_types_icon."'></td>";
                                                                            echo "<td class='hidden-xs'>".$switch_types_description."</td>";
                                                                            echo "<td width='100px'><a class='btn btn-warning btn-sm' href='switch_type_update.php?id={$switch_types_id}' role='button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a> <a class='btn btn-danger btn-sm' href='switch_type_delete.php?id={$switch_types_id}' role='button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></td>";
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
                                                                if($num_switch_types>0){
                                                                    $page_switch_types_dom = "switches.php";
                                                                    include_once '../includes/switch_types.pagination.inc.php';
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
        <script src="../js/navigation-scripts.js"></script>
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
