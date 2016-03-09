<?php
    $events = $scheduler->scheduled_events_list("+7 days");
    if (count($events)>0) {
?>
<div class="row">
    <div class="col-xs-12 col-lg-offset-1 col-lg-10 widget-space ">
       	<article class="title-widget no_padding_bottom">
	        <div class="row">
    	        <div class="col-xs-12">
                	<h2><?php echo $lang['upcoming_cycle_times']; ?></h2>
	            </div>
    	    </div>
        	<hr class="no_margin_bottom">
		</article>
    </div>
</div>    
<div class="row">
    <?php
		$first_pass = true;
		$zaehler = 0;
		$even_box = true;
		$tag_old ='';
		foreach ($events as $item) {
			$tag_new = $item['datum'];
			if ($tag_new<>$tag_old) {
				if (!$first_pass) {
							echo '</div>';
						echo '</div>';
						echo '<hr class="small hidden-sm hidden-md hidden-lg">';
					echo '</article>';
				echo '</div>';
				} 
				if ($even_box) {
					echo '<div class="col-xs-12 col-sm-6 col-lg-offset-1 col-lg-5 widget-space">';
						echo '<article class="second-widget">';
					$even_box = false;
				} else {
					echo '<div class="col-xs-12 col-sm-6 col-lg-5 widget-space">';
						echo '<article class="second-widget">';
					$even_box = true;
				}
						echo '<div class="row">';
							echo '<div class="col-xs-3 col-md-4">';
                                $tage = array($lang['sunday'],$lang['monday'],$lang['tuesday'],$lang['wednesday'],$lang['thursday'],$lang['friday'],$lang['saturday']);
								echo '<h3 class="text-center">'.$tage[$item['wochentag']].'</h3>';
								echo '<h3 class="text-center">'.$item['datum'].'</h3>';
							echo '</div>';
							$tag_old = $tag_new;
							$first_pass = false;
							echo '<div class="col-xs-9 col-md-8 scrolling-div">';
			}
			if ($item['status']=="on") {
				echo '<p class="text-left">'.$item['uhrzeit'].' : '.$item['scheduler_title'].' - <span class="on">'.$item['status'].'</span></p>';
			} else {
				echo '<p class="text-left">'.$item['uhrzeit'].' : '.$item['scheduler_title'].' - <span class="off">'.$item['status'].'</span></p>';
			}
			if ($zaehler>50) break;
			$zaehler++;
		}
							echo '</div>';
						echo '</div>';
						echo '<br class="hidden-sm hidden-md hidden-lg">';
					echo '</article>';
				echo '</div>';
    ?>
</div>

<?php
    }
?>
