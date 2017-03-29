<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/clients.data.inc.php';
	include_once '../includes/settings.data.inc.php';
    include_once("../languages/lang.php");

	$records_per_page = 3;

    $page_clients = isset($_GET['page_clients']) ? $_GET['page_clients'] : 1;

	$database = new Config();
	$db = $database->getConnection();

	$clients = new DataClients($db);
    $data_clients = $clients->readAll($page_clients, (($records_per_page * $page_clients) - $records_per_page), $records_per_page);
	$num_clients = $data_clients->rowCount();

    $settings = new DataSettings($db);
    $data_settings = $settings->readAll();
	$show_seconds = $data_settings['show_seconds'];
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

        <title>smartHome <?php echo $lang['settings']; ?></title>

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
            $site_name = $lang['settings'];
            include_once '../includes/navbar-top.php';
        ?>

        <!--Wrapper für die komplette Site-->
        <div id="wrapper">

    		<!--Navigationsleiste-->
			<?php include_once '../includes/navbar-site.php' ?>

			<div id="content-wrapper">
                <section class="container">

                    <!-- 1. Widget: Clients -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-offset-0 col-md-12 col-lg-offset-1 col-lg-10 widget-space">
                            <article class="first-widget">

                                <!--Widget Header-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2><?php echo $lang['clients']; ?></h2>
                                    </div>
                                </div>
                                <hr>

                                <div class="row no_padding no_margin">
                                    <div class="col-xs-12 col-lg-offset-1 col-lg-10 no_padding">

                                        <!--Button: Add Data-->
                                        <div class="row">
                                            <div class="col-xs-12 text-switch-center-left">
                                                <a class="btn btn-primary btn-big-margin" href="client_update.php" role="button"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>  <?php echo $lang['new_client']; ?></a>
                                            </div>
                                        </div>

                                        <!--Data Table-->
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <table class="table text-left scheduler">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['title']; ?></th>
                                                            <th><?php echo $lang['ip']; ?></th>
                                                            <th class="hidden-xs"><?php echo $lang['description']; ?></th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <?php
                                                        if($num_clients>0){
                                                            echo "<tbody>";
                                                            while ($row = $data_clients->fetch(PDO::FETCH_ASSOC)){
                                                                extract($row);
                                                                echo "<tr>";
                                                                    echo "<td>".$clients_title."</td>";
                                                                    echo "<td>".$clients_ip."</td>";
                                                                    echo "<td class='hidden-xs'>".$clients_description."</td>";
                                                                    echo "<td width='100px'><a class='btn btn-warning btn-sm' href='client_update.php?id={$clients_id}' role='button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a> <a class='btn btn-danger btn-sm' href='client_delete.php?id={$clients_id}' role='button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></td>";
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
                                                    if($num_clients>0){
                                                        $page_clients_dom = "index.php";
                                                        include_once '../includes/clients.pagination.inc.php';
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

                    <!-- 2. Widget: Allgemeine Einstellungen -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-offset-0 col-md-12 col-lg-offset-1 col-lg-10 widget-space">
                            <article class="second-widget">

                                <!--Widget Header-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2><?php echo $lang['general_settings']; ?></h2>
                                    </div>
                                </div>
                                <hr>

                                <?php
                                    if($_POST){
                                         $settings->scheduler_settings_page_per_view = $_POST['scheduler_settings_page_per_view'];
                                        $settings->scheduler_preview_period = $_POST['scheduler_preview_period'];
                                        $settings->scheduler_preview_items = $_POST['scheduler_preview_items'];
										$settings->show_seconds = $_POST['show_seconds'];

                                        if ($settings->update()) {
                                            $data_settings['scheduler_settings_page_per_view'] = $settings->scheduler_settings_page_per_view;
                                            $data_settings['scheduler_preview_period'] = $settings->scheduler_preview_period;
                                            $data_settings['scheduler_preview_items'] = $settings->scheduler_preview_items;
											$data_settings['show_seconds'] = $settings->show_seconds;
                                        }
                                     }
                                ?>

                                <div class="row no_padding no_margin">
                                    <div class="col-xs-12 col-lg-offset-1 col-lg-10 no_padding">
                                        <!--Form-->
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset>
                                                        <legend class="fieldset"><?php echo $lang['time_switch']; ?></legend>
                                                        <div class="row">
                                                            <div class="col-xs-8 text-left">
                                                                <label for="scheduler_settings_page_per_view" class="control-label small"><?php echo $lang['entries_per_site']; ?></label>
                                                            </div>
                                                            <div class="col-xs-2 no_padding text-left">
                                                                <input type="number" class="form-control fix-width-number" name="scheduler_settings_page_per_view" id="scheduler_settings_page_per_view" value='<?php echo $data_settings['scheduler_settings_page_per_view']; ?>' required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-8 text-left">
                                                                <label for="scheduler_preview_period" class="control-label small"><?php echo $lang['preview_period']; ?></label>
                                                            </div>
                                                            <div class="col-xs-2 no_padding text-left">
                                                                <input type="number" class="form-control fix-width-number" name="scheduler_preview_period" id="scheduler_preview_period" value='<?php echo $data_settings['scheduler_preview_period']; ?>' required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-8 text-left">
                                                                <label for="scheduler_preview_items" class="control-label small"><?php echo $lang['preview_quantity']; ?></label>
                                                            </div>
                                                            <div class="col-xs-2 no_padding text-left">
                                                                <input type="number" class="form-control fix-width-number" name="scheduler_preview_items" id="scheduler_preview_items" value='<?php echo $data_settings['scheduler_preview_items']; ?>' required>
                                                            </div>
															
                                                        </div>
														<!--show seconds (many TODO)-->
														<div class="row">
                                                            <div class="col-xs-8 text-left">
                                                                <label for="show_seconds" class="control-label small"><?php echo $lang['show_seconds']; ?></label>
                                                            </div>
                                                            <div class="col-xs-2 no_padding text-left">
                                                                <!--<input type="checkbox" class="form-control fix-width-number" name="show_seconds" id="show_seconds" value='<?php echo $data_settings['show_seconds']; ?>'> -->
																<input type="checkbox" class="form-control fix-width-number" name="show_seconds" id="show_seconds" value= "1" <?php echo ($data_settings['show_seconds']==1) ? 'checked' : '' ; ?>>
                                                            </div>
															
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row" style="margin-top: 24px">
                                                <div class="col-xs-12 text-center-right no_padding">
                                                    <button type="submit" name="submit" class="btn btn-primary btn-fix-width"><span class='glyphicon glyphicon-ok' aria-hidden='true'></span>  <?php echo $lang['save']; ?></button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                </section>
            </div>

		</div>

        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <?php include_once '../js/navigation-scripts.php'; ?>
        <script src="../js/addons/ie10-viewport-bug-workaround.js"></script>
        <script>
        </script>
    </body>
</html>
