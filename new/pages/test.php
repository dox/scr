<?php
echo pageTitle(
	"Test Page",
	"For testing purposes only"
);



$givenDate = "2025-11-23";
$firstDayOfWeek = $terms->firstDayOfWeek($givenDate);
$isCurrentWeek = $terms->isCurrentWeek($givenDate);

echo "<h1>Given Date</h1>";
printArray($givenDate);

echo "<h1>First Day Of Week</h1>";
printArray($firstDayOfWeek);

echo "<h1>Is Current Week</h1>";
printArray($isCurrentWeek);
?>