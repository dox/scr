<?php

echo $user->getUsername() ?? null;



$i = 0;

do {
	//echo "<p>Error: " . $i . "</p>";
	//error_log("Error: test error: " . $i, LOG_NOTICE);
	error_log("SECURITY ALERT: Failed login attempt from IP " . $_SERVER['REMOTE_ADDR']);
	echo "<p>error</p>";
	$i++;
} while ($i < 20);






//printArray($user->authenticate('breakspear', 'P!ssport7', false));

