<?php
// a quick function to recalculate all wines and update their static qty (based off of transactional qty)

include_once("../inc/autoload.php");

$wineClass = new wineClass();

foreach ($wineClass->allWines() AS $wine) {
	$wine = new wine($wine['uid']);
	
	$transactionalQty = $wine->currentQty(date('Y-m-d'));
	
	if ($wine->qty != $transactionalQty) {
		$updateArray = array(
			'qty' => $transactionalQty
		);
		
		echo "<p>Updating qty for <a href=\"index.php?n=wine_wine&wine_uid=" . $wine->uid . "\">[wineUID:" . $wine->uid . "]</a> from " . $wine->qty . " to " . $transactionalQty . "</p>";
		$wine->update($updateArray);
	}
}

echo "<p>EOF</p>";
?>
