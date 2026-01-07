<?php
include_once("../inc/autoload.php");

if (checkpoint_charlie("wine")) {
	if (isset($_POST['wine_uid'])) {
		$wineUID = filter_var($_POST['wine_uid'], FILTER_SANITIZE_NUMBER_INT);
		
		$wine = new wine($wineUID);
		$wine->delete();
		echo "DELETE";
	}
}
?>