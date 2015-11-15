<nav class="navbar-inverse navbar-fixed-top">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <a href="#menu-toggle" class="navbar-toggle" id="menu-toggle">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <h1 class="no_margin"><?php echo $site_name; ?></h1>
        </div>
        <div class="hidden-xs col-sm-6 text-right">    
            <?php
                $monate = array(1=>"Januar", 2=>"Februar", 3=>"März", 4=>"April", 5=>"Mai", 6=>"Juni", 7=>"Juli", 8=>"August", 9=>"September", 10=>"Oktober", 11=>"November", 12=>"Dezember");
                $datum = date("j") . ". " . $monate[date("n")] . " " . date("Y");
            ?>
            <span id="anzeige_uhrzeit"><?php echo date('H:i'); ?> Uhr</span> <span id="anzeige_datum"><?php echo $datum; ?></span>
        </div>
    </div>
</nav>
