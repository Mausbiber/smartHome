<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/switches.data.inc.php';
	include_once '../includes/clients.data.inc.php';
    include_once '../languages/lang.php';

	$database = new Config();
	$db = $database->getConnection();
	$switches = new DataSwitches($db);
    $list_of_clients = $switches->readClients();
    $list_of_switch_types = $switches->readTypes();

	if (isset($_GET['id'])) {
		$switches->id = $_GET['id'];
		$switches->readOne();
	} else {
		$id = -1;
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
            $site_name = $lang['settings']." - ".$lang['switches'];
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
                                                echo "<h2>".$lang['create_switch']."</h2>";
                                            } else {
                                                echo "<h2>".$lang['modify_switch']."</h2>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <hr>
                                <?php
                                    if($_POST){

                                        $switches->title = $_POST['title'];
                                        $switches->description = $_POST['description'];
                                        $switches->clientTitle = $_POST['client_title'];
                                        $switches->switchTypTitle = $_POST['switch_type_title'];
                                        $switches->argA = $_POST['arg_a'];
                                        $switches->argB = $_POST['arg_b'];
                                        $switches->argC = $_POST['arg_c'];
                                        $switches->argD = $_POST['arg_d'];

                                        if ($id<0) {
                                            $tmp = $switches->create();
                                            if($tmp) header("Location: switches.php");
                                        } else {
                                            $tmp = $switches->update();
                                            if($tmp) header("Location: switches.php");
                                        }

                                     }
                                ?>
                                <form method="post">

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="title" class="control-label"><?php echo $lang['title']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <input type="text" class="form-control" name="title" id="title" placeholder="" value='<?php echo $switches->title; ?>' required>
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
                                                        if($tmp_title == $switches->clientTitle) {
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
                                            <label for="switch_type_title" class="control-label"><?php echo $lang['switching_device']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-2 col-md-3 no_padding text-left">
                                            <select class="form-control" name="switch_type_title" id="switch_type_title">
                                                <?php
                                                    while ($tmp_row = $list_of_switch_types->fetch(PDO::FETCH_ASSOC)){
                                                        $tmp_title = $tmp_row['title'];
                                                        if($tmp_title == $switches->switchTypTitle) {
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
                                                    <input type="text" class="form-control argument" name="arg_a" id="arg_a" placeholder="Arg A" value='<?php echo $switches->argA; ?>'>
                                                </div>
                                                <div class="col-md-offset-1 col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_b" id="arg_b" placeholder="Arg B" value='<?php echo $switches->argB; ?>'>
                                                </div>
                                                <div class="col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_c" id="arg_c" placeholder="Arg C" value='<?php echo $switches->argC; ?>'>
                                                </div>
                                                <div class="col-md-offset-1 col-md-5 no_padding text-left">
                                                    <input type="text" class="form-control argument" name="arg_d" id="arg_d" placeholder="Arg D" value='<?php echo $switches->argD; ?>'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-5 col-sm-4 col-sm-offset-1 col-lg-3 text-right">
                                            <label for="description" class="control-label"><?php echo $lang['description']; ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-5 no_padding text-left">
                                            <textarea style="width:100%" name="description" id="description" class="form-control" rows="3" placeholder=""><?php echo $switches->description; ?></textarea>
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
