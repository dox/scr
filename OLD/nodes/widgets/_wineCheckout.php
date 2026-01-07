<?php
include_once("../../inc/autoload.php");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

echo wineListItem($cleanUID);



function wineListItem($wineUID) {
	$wine = new wine($wineUID);
	
	$output  = "<div class=\"card mb-3\" id=\"wine-selected\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<h5 class=\"card-title\">" . $wine->name . "</h5>";
	
	$output .= "<p class=\"card-text\">";
	$output .= "<div class=\"row\">";
	$output .= "<div class=\"col\">";
	$output .= "<label for=\"bottles\" class=\"form-label\">Qty.</label>";
	$output .= "<input type=\"number\" class=\"form-control\" id=\"bottles\" name=\"bottles[]\" placeholder=\"Qty\" value=\"1\">";
	$output .= "<div id=\"qtyHelpBlock\" class=\"form-text\">";
	$output .= "Available: " . $wine->currentQty();
	$output .= "</div>";
	$output .= "</div>";
	$output .= "<div class=\"col\">";
	$output .= "<label for=\"price_per_bottle\" class=\"form-label\">£/bottle</label>";
	$output .= "<input type=\"number\" class=\"form-control\" id=\"price_per_bottle\" name=\"price_per_bottle[]\" placeholder=\"Price/each\" value=\"" . $wine->price_internal . "\">";
	$output .= "<div id=\"priceHelpBlock\" class=\"form-text\">";
	$output .= "Internal: " . currencyDisplay($wine->price_internal) . " / External: " . currencyDisplay($wine->price_external);
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</p>"; //close card-text
	$output .= "</div>"; //close card-body
	$output .= "<input type=\"hidden\" id=\"wine_uid\" name=\"wine_uid[]\" value=\"" . $wine->uid . "\">";
	$output .= "</div>"; //close card
	
	echo $output;
}
?>