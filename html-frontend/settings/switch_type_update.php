<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/switch_types.data.inc.php';
    include_once("../languages/lang.php");

	$database = new Config();
	$db = $database->getConnection();
	$switch_types = new DataSwitchTypes($db);

    if (isset($_GET['id'])) {
		$switch_types->id = $_GET['id'];
		$switch_types->readOne();
	} else {
		$id = -1;
	}

    if($_POST){
        $switch_types->title = htmlentities(strip_tags($_POST['title']));
        $switch_types->icon = $_FILES['icon_file']['name'];
        $switch_types->icon_tmp = $_FILES['icon_file']['tmp_name'];
        $switch_types->icon_size = $_FILES['icon_file']['size'];
        $switch_types->description = htmlentities(strip_tags($_POST['description']));

         if ($id<0) {
            $tmp = $switch_types->create();
            if($tmp>0) header("Location: switches.php");
        } else {
            $tmp = $switch_types->update();
            if($tmp) header("Location: switches.php");
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
            $site_name = $lang['settings']." - ".$lang['switching_devices'];
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
                                                echo "<h2>".$lang['create_switching_device']."</h2>";
                                            } else {
                                                echo "<h2>".$lang['modify_switching_device']."</h2>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <hr>
                                <form method="post" enctype="multipart/form-data">

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="title" class="control-label"><?php echo $lang['title']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <input type="text" class="form-control" name="title" id="title" placeholder="tf_remote_typ_A" value='<?php echo $switch_types->title; ?>' required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="icon_file" class="control-label"><?php echo $lang['icon']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <input type="file" name="icon_file" id="icon_file" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="description" class="control-label"><?php echo $lang['description']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <textarea style="width:100%" name="description" id="description" class="form-control" rows="3" placeholder="Funk Schalter, ELO, Typ A"><?php echo $switch_types->description; ?></textarea>
                                        </div>
                                    </div>


                                    <div class="row" style="margin-top: 24px">
                                        <div class="col-xs-12 col-sm-6 text-switch-center-right no_padding">
                                            <button type="submit" name="submit" class="btn btn-primary btn-fix-width"><span class='glyphicon glyphicon-ok' aria-hidden='true'></span>  <?php echo $lang['save']; ?></button>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 text-switch-center-left no_padding">
                                            <a class="btn btn-default btn-fix-width" href="switches.php" role="button"><span class='glyphicon glyphicon-remove' aria-hidden='true'></span>  <?php echo $lang['cancle']; ?></a>
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
        <script src="../js/navigation-scripts.js"></script>
        <script src="../js/addons/ie10-viewport-bug-workaround.js"></script>
		<script>
		</script>

    </body>
</html>
