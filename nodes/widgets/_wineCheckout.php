<?php
include_once("../../inc/autoload.php");

$uids = explode(",", $_GET['uids']);

foreach ($uids AS $uid) {
	$cleanUID = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
	
	echo wineListItem($cleanUID);
}



function wineListItem($wineUID) {
	$wine = new wine($wineUID);
	
	$output  = "<div class=\"card mb-3\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<h5 class=\"card-title\">" . $wine->name . "</h5>";
	$output .= "<input type=\"hidden\" id=\"name\" name=\"name[]\" value=\"" . $wine->name . "\">";
	$output .= "<input type=\"hidden\" id=\"grape\" name=\"grape[]\" value=\"" . $wine->grape . "\">";

	$output .= "<p class=\"card-text\">";
	$output .= "<div class=\"row\">";
	$output .= "<div class=\"col\">";
	$output .= "<label for=\"qty\" class=\"form-label\">Qty.</label>";
	$output .= "<input type=\"number\" class=\"form-control\" id=\"qty\" name=\"qty[]\" placeholder=\"Qty\" value=\"1\">";
	$output .= "<div id=\"qtyHelpBlock\" class=\"form-text\">";
	$output .= "Available: " . $wine->qty;
	$output .= "</div>";
	$output .= "</div>";
	$output .= "<div class=\"col\">";
	$output .= "<label for=\"price\" class=\"form-label\">Price/each</label>";
	$output .= "<input type=\"number\" class=\"form-control\" id=\"price\" name=\"price[]\" placeholder=\"Price/each\" value=\"" . $wine->price_external . "\">";
	$output .= "<div id=\"priceHelpBlock\" class=\"form-text\">";
	$output .= "Internal / External Price: " . currencyDisplay($wine->price_internal) . " / " . currencyDisplay($wine->price_external);
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</p>"; //close card-text
	$output .= "</div>"; //close card-body
	$output .= "<input type=\"hidden\" id=\"uid\" name=\"uid[]\" value=\"" . $wine->uid . "\">";
	$output .= "</div>"; //close card
	
	echo $output;
}
?>