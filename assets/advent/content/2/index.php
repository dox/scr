<?php
require_once '../../inc/autoload.php';

$day = intval($_GET['day'] ?? 0);

if ($day < 1 || $day > 24) {
	echo "<div class='alert alert-warning'>Invalid day.</div>";
	exit;
}

echo "<h3>Day {$day}</h3>";
echo "<p>This content is coming from PHP 🎄</p>";