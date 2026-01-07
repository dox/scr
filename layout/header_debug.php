<?php
if (APP_DEBUG) {
	$output  = "<header class=\"py-3 border-bottom sticky-top bg-warning text-dark d-print-none\">";
	$output .= "<div class=\"container text-center\">";
	$output .= "<i class=\"bi bi-exclamation-triangle mx-3\"></i><strong>DEBUG MODE</strong> Site is in debug mode. All data is for testing purposes only. No emails will be sent.<i class=\"bi bi-exclamation-triangle mx-3\"></i>";
	$output .= "</div>";
	$output .= "</header>";
	
	echo $output;
}
