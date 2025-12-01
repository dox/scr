<?php


$i = 0;

do {
	echo "<p>Error: " . $i . "</p>";
	error_log("Error: test error: " . $i, LOG_NOTICE);
	
	$i++;
} while ($i < 20);