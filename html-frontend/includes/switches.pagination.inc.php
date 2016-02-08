<?php
	echo "<nav><ul class='pagination'>";

	if($page_switches>1){
		echo "<li><a href='{$page_switches_dom}' title='Go to the first page.'>";
			echo "«";
		echo "</a></li>";
	}

	$total_rows = $switches->countAll();
	$total_pages = ceil($total_rows / $records_per_page);
	$range = 2;
	$initial_num = $page_switches - $range;
	$condition_limit_num = ($page_switches + $range)  + 1;

	for ($x=$initial_num; $x<$condition_limit_num; $x++) {

		if (($x > 0) && ($x <= $total_pages)) {
			if ($x == $page_switches) {
				echo "<li class='active'><a href=\"#\">$x</a></li>";
			}
			else {
				echo "<li><a href='{$page_switches_dom}?page_switches=$x'>$x</a></li>";
			}
		}
	}

	if($page_switches<$total_pages){
		echo "<li><a href='" .$page_switches_dom . "?page_switches={$total_pages}' title='Last page is {$total_pages}.'>";
			echo "»";
		echo "</a></li>";
	}

	echo "</ul></nav>";
?>
