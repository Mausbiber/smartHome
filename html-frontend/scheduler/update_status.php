<?php
	date_default_timezone_set('Europe/Berlin');

	include_once '../includes/config.php';
	include_once '../includes/scheduler.data.inc.php';
    include_once '../languages/lang.php';

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
                        <div class="col-xs-12 col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8 widget-space">
                            <article class="first-widget">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2><?php echo $lang['sending_data_to_server']; ?></h2>
                                    </div>
                                </div>
                                <hr>
                                <p id="connect1_p"><?php echo $lang['connecting_to_server']; ?> <span id="connect1_span"></span></p>
                                <p id="connect2_p" style="display: none"><?php echo $lang['send_data']; ?> <span id="connect2_span"></span></p>
                                <p id="connect3_p" style="display: none"><?php echo $lang['waiting_for_response']; ?> <span id="connect3_span"></span></p>
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
    	<script src="../js/addons/bootstrap-clockpicker.min.js"></script>
    	<script src="../js/addons/bootstrap-datepicker.min.js"></script>
        <script src="../js/addons/ie10-viewport-bug-workaround.js"></script>
		<script>
            var parts = window.location.search.substr(1).split("&");
            var $_GET = {};
            for (var i = 0; i < parts.length; i++) {
                var temp = parts[i].split("=");
                $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
            }
            $_GET.usage = htmlEntities($_GET.usage)

            if (isNaN($_GET.id) == true ) {
                window.setTimeout(function(){window.location = "index.php"},500);
            }
			
			
/*  TODO hardcoded Server IP */
			var socketServer = new WebSocket('ws://10.0.0.61:5505');
			
			window.setTimeout(function(){
				if(socketServer.readyState === 0  || socketServer.readyState === 3){
					$("#connect1_span").replaceWith('<span class="off"> Keine Verbindung zum Server</span>');
					window.location = "index.php";
				}
			},1000);

           socketServer.onerror = function(error) {
                window.setTimeout(function(){$("#connect1_span").replaceWith('<span class="off"> Offline</span>');},500);
                window.setTimeout(function(){window.location = "index.php"},1000);
            };

            socketServer.onopen = function(event) {
                window.setTimeout(function(){$("#connect1_span").replaceWith('<span class="on"> OK</span>');},500);
                window.setTimeout(function(){$("#connect2_p").show();},1000);
                socketServer.send(JSON.stringify({ "usage" : $_GET.usage, "ip" : "", "id" : $_GET.id, "value" : "" }));
                window.setTimeout(function(){$("#connect2_span").replaceWith('<span class="on"> OK</span>');},1500);
                window.setTimeout(function(){$("#connect3_p").show();},2000);
            };

            socketServer.onmessage = function(event) {
                var tmp_json = JSON.parse(event.data);
                if (tmp_json.usage==$_GET.usage && tmp_json.id==$_GET.id && tmp_json.value) {
				    window.setTimeout(function(){$("#connect3_span").replaceWith('<span class="on"> OK</span>');},2500);
                    window.setTimeout(function(){window.location = "index.php"},3000);
                }
            };

            socketServer.onclose = function(event) {};

        </script>

    </body>
</html>
