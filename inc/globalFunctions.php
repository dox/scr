<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function autoPluralise ($singular, $plural, $count = 1) {
	// fantasticly clever function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
} // END function autoPluralise

function escape($var) {
	$var=stripslashes($var);
	$var=htmlentities($var);
	$var=strip_tags($var);
	$var=str_replace("'", "\'", $var);

	return $var;
}

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
    /*
    $interval can be:
    yyyy - Number of full years
    q    - Number of full quarters
    m    - Number of full months
    y    - Difference between day numbers
           (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d    - Number of full days
    w    - Number of full weekdays
    ww   - Number of full weeks
    h    - Number of full hours
    n    - Number of full minutes
    s    - Number of full seconds (default)
    */

    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto   = strtotime($dateto, 0);
    }

    $difference        = $dateto - $datefrom; // Difference in seconds
    $months_difference = 0;

    switch ($interval) {
        case 'yyyy': // Number of full years
            $years_difference = floor($difference / 31536000);
            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                $years_difference--;
            }

            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                $years_difference++;
            }

            $datediff = $years_difference;
        break;

        case "q": // Number of full quarters
            $quarters_difference = floor($difference / 8035200);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $quarters_difference--;
            $datediff = $quarters_difference;
        break;

        case "m": // Number of full months
            $months_difference = floor($difference / 2678400);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $months_difference--;

            $datediff = $months_difference;
        break;

        case 'y': // Difference between day numbers
            $datediff = date("z", $dateto) - date("z", $datefrom);
        break;

        case "d": // Number of full days
            $datediff = floor($difference / 86400);
        break;

        case "w": // Number of full weekdays
            $days_difference  = floor($difference / 86400);
            $weeks_difference = floor($days_difference / 7); // Complete weeks
            $first_day        = date("w", $datefrom);
            $days_remainder   = floor($days_difference % 7);
            $odd_days         = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

            if ($odd_days > 7) { // Sunday
                $days_remainder--;
            }

            if ($odd_days > 6) { // Saturday
                $days_remainder--;
            }

            $datediff = ($weeks_difference * 5) + $days_remainder;
        break;

        case "ww": // Number of full weeks
            $datediff = floor($difference / 604800);
        break;

        case "h": // Number of full hours
            $datediff = floor($difference / 3600);
        break;

        case "n": // Number of full minutes
            $datediff = floor($difference / 60);
        break;

        default: // Number of full seconds (default)
            $datediff = $difference;
        break;
    }

    return $datediff;
}

function ordinal($number) {
	$ends = array('th','st','nd','rd','th','th','th','th','th','th');
	if ((($number % 100) >= 11) && (($number%100) <= 13)) {
		return $number. 'th';
	} else {
		return $number. $ends[$number % 10];
	}
}

function admin_gatekeeper() {
	if ($_SESSION['admin'] != true) {
		global $logsClass;
		$logsClass->create("view_fail", "Page view for " . $_SERVER['REQUEST_URI'] . " failed");

		echo "<p class=\"text-center\">Access denied</p>";

		header("Location: http://scr2.seh.ox.ac.uk/logon.php");
	  exit;
	}
}

function makeTitle($title = null, $subtitle = nulll, $iconsArray = null) {
	$output  = "<div class=\"px-3 py-3 pt-md-5 pb-md-4 text-center\">";
	$output .= "<h1 class=\"display-4\">" . $title . "</h1>";

	if ($subtitle != null) {
		$output .= "<p class=\"lead\">" . $subtitle . "</p>";
	}

	$output .= "</div>";

	$output .= "<div class=\"pb-3 text-right\">";
	foreach ($iconsArray AS $icon) {
		$output .= "<button type=\"button\" class=\"btn " . $icon['class'] . "\"" . $icon['value'] . ">";
		$output .= $icon['name'];
		$output .= "</button>";
	}
	$output .= "</div>";

	return $output;
}

function ALTmakeTitle($title = null, $subtitle = nulll, $iconsArray = null) {

	$output  = "<div class=\"page-header text-white d-print-none\">";
	$output .= "<div class=\"row align-items-center\">";
	$output .= "<div class=\"col\">";
	$output .= " <div class=\"page-pretitle\">" . $subtitle . "</div>";
	$output .= "<h2 class=\"page-title\">" . $title . "</h2>";
	$output .= "</div>";
	$output .= "<div class=\"col-auto ms-auto d-print-none\">";
	$output .= "<div class=\"btn-list\">";
	$output .= "<span class=\"d-none d-sm-inline\">";
	$output .= "<a href=\"#\" class=\"btn btn-white\">test</a>";
	$output .= "</span>";
	$output .= "<a href=\"#\" class=\"btn btn-primary d-none d-sm-inline-block\" data-bs-toggle=\"modal\" data-bs-target=\"#modal-report\">";
	$output .= "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path><line x1=\"12\" y1=\"5\" x2=\"12\" y2=\"19\"></line><line x1=\"5\" y1=\"12\" x2=\"19\" y2=\"12\"></line></svg>";
	$output .= "Create new report";
	$output .= "</a>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";

	return $output;
}

//ICONS

$icon_add_member = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-person-plus\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
  <path fill-rule=\"evenodd\" d=\"M8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10zM13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z\"/>
</svg>";

$icon_add_term = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-calendar2-plus\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
  <path fill-rule=\"evenodd\" d=\"M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z\"/>
  <path d=\"M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z\"/>
  <path fill-rule=\"evenodd\" d=\"M8 8a.5.5 0 0 1 .5.5V10H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V11H6a.5.5 0 0 1 0-1h1.5V8.5A.5.5 0 0 1 8 8z\"/>
</svg>";

$icon_edit = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-sliders\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
  <path fill-rule=\"evenodd\" d=\"M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z\"/>
</svg>";

$icon_delete = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-trash\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
  <path d=\"M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z\"/>
  <path fill-rule=\"evenodd\" d=\"M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z\"/>
</svg>";
?>
