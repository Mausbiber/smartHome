<?php
	echo "<nav><ul class='pagination'>";

	if($page_clients>1){
		echo "<li><a href='{$page_clients_dom}' title='Go to the first page.'>";
			echo "«";
		echo "</a></li>";
	}

	$total_rows = $clients->countAll();
	$total_pages = ceil($total_rows / $records_per_page);
	$range = 2;
	$initial_num = $page_clients - $range;
	$condition_limit_num = ($page_clients + $range)  + 1;

	for ($x=$initial_num; $x<$condition_limit_num; $x++) {

		if (($x > 0) && ($x <= $total_pages)) {
			if ($x == $page_clients) {
				echo "<li class='active'><a href=\"#\">$x</a></li>";
			}
			else {
				echo "<li><a href='{$page_clients_dom}?page_clients=$x'>$x</a></li>";
			}
		}
	}

	if($page_clients<$total_pages){
		echo "<li><a href='" .$page_clients_dom . "?page_clients={$total_pages}' title='Last page is {$total_pages}.'>";
			echo "»";
		echo "</a></li>";
	}

	echo "</ul></nav>";
?>
