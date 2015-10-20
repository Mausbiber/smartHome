<div class="row">
    <div class="col-xs-12">
        <h2>bevorstehende Schaltungen ...</h2>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-xs-12">
    <?php
        $events = $timer->scheduled_events_list("+14 days");
		echo '<ul class="timeline">';
		$row_index = 0;
		$zaehler = 0;
		foreach ($events as $item) {
			if ($row_index == 0) {
				echo '<li>';
				$row_index = 1;
			} else {
				echo '<li class="timeline-inverted">';
				$row_index = 0;
			}
				echo '<div class="timeline-badge"><img src="../img/icons/'.$item['icon'].'"></div>';
				echo '<div class="timeline-panel">';
					echo '<div class="timeline-heading">';
						echo '<h4 class="timeline-title">'.$item['datum'].' Uhr</h4>';
						echo '<p>'.$item['scheduler_title'].' - '.$item['status'].'</p>';
					echo '</div>';
				echo '</div>';
			echo '</li>';
			$zaehler++;
			if ($zaehler == 8) break;
		}
		echo '</ul>';
    ?>
    </div>
</div>

