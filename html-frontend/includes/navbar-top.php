<nav class="navbar-inverse navbar-fixed-top">
    <div class="row">

        <div class="col-xs-12 col-sm-6">
            <a href="#menu-toggle" class="navbar-toggle" id="menu-toggle">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <h1 class="no_margin"><?php echo $site_name; ?>
            <li class="hidden-sm hidden-md hidden-lg dropdown language-toggle">
                <a href="#" class="dropdown-toggle language-toggle" data-toggle="dropdown"><?php echo get_lang_id();?><b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="javascript:setLang('de')">de</a></li>
                    <li><a href="javascript:setLang('en')">en</a></li>
                </ul>
            </li>
            </h1>

        </div>

        <div class="hidden-xs col-sm-6 text-right">    
            <?php
                $monate = array(1=>$lang['january'], 2=>$lang['february'], 3=>$lang['march'], 4=>$lang['april'], 5=>$lang['may'], 6=>$lang['june'], 7=>$lang['july'], 8=>$lang['august'], 9=>$lang['september'], 10=>$lang['october'], 11=>$lang['november'], 12=>$lang['december']);
                $datum = date("j") . ". " . $monate[date("n")] . " " . date("Y");
            ?>
            <div>
            <span id="anzeige_uhrzeit"><?php echo date('H:i'); ?> Uhr</span> <span id="anzeige_datum"><?php echo $datum; ?></span>

            <li class="dropdown language-toggle">
                <a href="#" class="dropdown-toggle language-toggle" data-toggle="dropdown"><?php echo get_lang_id();?><b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="javascript:setLang('de')">de</a></li>
                    <li><a href="javascript:setLang('en')">en</a></li>
                </ul>
            </li>
            </div>
        </div>

        <div class="col-xs-1">
        </div>

    </div>
</nav>
