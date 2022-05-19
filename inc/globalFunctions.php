<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function dateDisplay($date = null, $longFormat = false) {
	global $settingsClass;

	if ($longFormat == true) {
		$dateFormat = $settingsClass->value('datetime_format_long');
	} else {
		$dateFormat = $settingsClass->value('datetime_format_short');
	}

	$returnDate = date($dateFormat, strtotime($date));

	return $returnDate;
}

function firstDayOfWeek($inputDate = null) {
		// considers Sunday the first day of the week
		
		if ($inputDate == null) {
			$date = strtotime(date('Y-m-d'));
		} else {
			$date = strtotime($inputDate);
		}
		
		if (date('N', $date) == 7) {
			$returnDate = date('Y-m-d', $date);
		} else {
			$returnDate = date('Y-m-d', strtotime('sunday last week', $date));
		}
		
		return $returnDate;
	}

function timeDisplay($date = null, $hour12 = false) {
	global $settingsClass;

	if ($hour12 == true) {
		$dateFormat = 'H:i a';
	} else {
		$dateFormat = 'H:i';
	}

	$returnDate = date($dateFormat, strtotime($date));

	return $returnDate;
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
	$var = stripslashes($var);
	$var = htmlentities($var);
	//$var = htmlspecialchars($value, ENT_QUOTES);
	$var = strip_tags($var);
	$var = str_replace("'", "\'", $var);

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

function siteURL() {
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

	return $actual_link;
}

function admin_gatekeeper() {
	if ($_SESSION['admin'] != true) {
		global $logsClass;

		$logArray['category'] = "admin";
    $logArray['result'] = "danger";
    $logArray['description'] = "Page view for " . $_SERVER['REQUEST_URI'] . " failed";
    $logsClass->create($logArray);

		$output  = "<div class=\"text-center mt-4\">";
		$output .= "<svg width=\"48\" height=\"48\"><use xlink:href=\"img/icons.svg#x-circle\"/></svg>";
		$output .= "<h1 class=\"text-center mt-4\">Access denied</h1>";
		$output .= "</div>";

		echo $output;


		header("Location: " . siteURL() . "/logon.php");
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

	$output .= "<div class=\"pb-3 text-end\">";
	foreach ($iconsArray AS $icon) {
		$output .= "<button type=\"button\" class=\"btn btn-sm ms-1 " . $icon['class'] . "\"" . $icon['value'] . ">";
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

function onToOne($input = null) {
	$return = "";
	if ($input == "on" || $input == "1") {
		$return = 1;
	}
	return $return;
}
?>
