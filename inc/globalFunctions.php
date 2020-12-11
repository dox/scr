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

$icon_chough = "<svg width=\"4em\" height=\"4em\" xmlns=\"http://www.w3.org/2000/svg\"  viewBox=\"0 0 250 210\">
  <g transform=\"matrix(1.3333 0 0 -1.3333 0 1122.5)\">
    <path fill=\"#ffffff\" fill-rule=\"nonzero\" d=\"m28.34198,825.47272c-9.961,1.64996 -13.99402,0.70099 -23.84198,-1.02704c6.39099,6.77301 21.14499,9.46301 26.69199,9.71606c-1.88699,-2.23999 -2.047,-5.73407 -2.85001,-8.68903\" id=\"path74\"/>
    <path fill=\"#ffffff\" fill-rule=\"nonzero\" d=\"m97.9221,720.63873c-2.48596,-3.01001 -4.36096,-6.547 -6.39299,-9.91998c-1.77301,-2.953 -1.586,-6.19904 -3.90399,-8.65601c-1.06201,-1.12097 -2.11105,-3.44397 -1.75198,-4.86499c0.39096,-1.57501 4.73996,-1.84204 6.09801,-1.84204c1.38699,0 2.61499,-0.64099 3.867,-1.18994c4.40198,-1.953 3.07996,-3.11902 0.32599,-2.65002c-0.91602,0.15198 -1.81601,0.93396 -2.79501,0.76599c-1.07599,-0.17804 -1.71303,-0.91302 -3.02904,-0.99701c-2.01801,-0.13 -3.33197,-0.88 -5.35599,-0.73804c-1.20996,0.07806 -3.46802,1.28503 -4.52902,0.57404c-0.97797,-0.66797 -2.047,-0.94299 -3.146,-1.29602c-0.82095,-0.26398 -1.19397,-0.63898 -2.11597,-0.73297c-1.73398,-0.17999 -4.77899,0.39496 -6.26501,-0.62701c-1.86301,-1.271 -2.08002,-2.88898 -4.18201,-2.18701c-1.32202,0.44501 0.27802,3.71606 1.01602,4.14801c0.80795,0.47498 1.61899,0.94299 2.45297,1.37897c1.047,0.53705 2.39798,0.02307 3.27698,0.47705c0.24103,0.13 0.53903,0.43298 0.72507,0.65802c-2.87903,0.72797 -5.72507,2.203 -8.68405,2.203c-1.90796,0 -2.685,-0.65399 -3.922,-2.27502c-0.539,-0.70197 -3.35297,-2.70801 -3.35297,-0.38104c0,2.427 3.75598,6.60504 5.86899,6.91205c3.29697,0.45898 5.76797,-1.86505 8.85202,-1.99402c3.09296,-0.12299 6.53696,-0.64899 8.29697,2.763c1.52103,2.95697 3.75104,5.26599 5.73401,7.89301c2.01001,2.65802 2.20901,1.84601 4.004,4.97296c0.88699,1.53705 3.18703,6.68701 4.05403,8.06604c0.45599,0.71698 3.96902,-0.461 4.85397,-0.461\" id=\"path78\"/>
    <path fill=\"#ffffff\" fill-rule=\"nonzero\" d=\"m106.00999,726.46887c-0.91599,-3.01599 -0.87097,-9.35205 -2.02298,-12.58203c-1.21301,-3.36096 -2.28802,-3.07996 -3.15302,-6.28101c-1.80298,-6.711 -15.65199,2.70502 -16.23,-5.047c3.633,-0.28101 3.47198,1.81201 11.20903,-0.47302c0.55099,-0.16199 1.67398,0.44702 1.59,-0.68097c-0.04901,-0.62701 -1.48703,-0.89899 -1.90503,-0.922c-1.81799,-0.08398 -3.63997,-0.23297 -5.451,-0.479c-1.71301,-0.242 -3.10004,-0.492 -4.40204,-1.742c-1.47296,-1.40601 4.517,-0.172 5.12305,-0.15796c3.58,0.09399 5.80198,-0.86707 9.01698,0.45898c2.43604,1.01001 7.84201,0.56995 10.34201,0.63293c1.811,0.05603 2.63303,2.16907 4.17001,2.54303c1.23099,0.29205 4.12701,-1.948 4.858,-1.16797c1.69699,1.79999 -5.68597,4.01697 -7.48099,4.01697c-1.70703,0 -3.22302,-0.28497 -4.896,0.15601c-2.22705,0.59601 -1.77802,2.33002 -1.29102,4.24799c1.56396,6.06104 3.793,11.48499 4.56,17.80298c-1.34601,0.07001 -1.04297,0.99005 -2.49603,1.06305c0,-0.18805 -1.54099,-1.20502 -1.54099,-1.38898\" id=\"path82\"/>
    <path fill=\"#ffffff\" fill-rule=\"nonzero\" d=\"m25.40259,825.75201c7.60699,-2.97699 9.45898,-14.52301 10.17398,-21.64099c0.39603,-3.96301 -0.30701,-8.224 -0.40598,-12.20502c-0.112,-4.29297 -0.13699,-8.586 -0.22299,-12.888c-0.08603,-4.02203 1.35901,-7.83197 2.285,-11.72699c0.90601,-3.76404 4.24799,-9.36096 6.44,-12.40198c4.72195,-6.54102 9.517,-12.94403 16.77899,-16.60205c11.02301,-5.539 25.76901,-4.73193 31.98402,-15.51898c0.92203,-1.59601 0.07599,-2.12897 2.07397,-2.12897c1.84402,0 3.35004,-1.57501 3.55701,0.552c0.315,3.23096 -1.60696,1.58997 -2.707,4.854c-0.43198,1.28296 1.65598,2.80896 2.65598,1.93701c0.94006,-0.80603 2.77405,-2.04407 3.99805,-1.77502c2.99298,0.633 4.39899,4.95703 3.98502,0.46503c0.48801,0.24799 3.70096,-1.25806 4.02499,-0.086c0.25998,0.96698 1.15399,0.83997 1.72299,1.13495c2.72,1.43903 8.228,1.37701 11.14395,1c4.25201,-0.52997 14.84607,1.88501 24.48904,0.41803c3.65195,-0.55103 8.65195,-2.80304 14.18301,-4.69403c7.14697,-2.19696 20.83795,-7.06396 17.95096,-3.23999c-1.74399,2.30304 -5.49997,3.815 -7.94098,5.20502c-2.88101,1.65601 -5.85001,3.17603 -8.80099,4.67798c-0.25604,0.12701 -4.07803,2.17401 -2.564,2.10901c1.78098,-0.08801 3.06796,-0.39801 4.76898,-0.922c3.49203,-1.08197 6.34402,-1.85901 9.95303,-2.06799c0.18597,1.49194 -1.52905,2.992 -2.82404,3.229c3.41803,0.06598 7.01599,0.43298 10.40997,0c-5.05795,6.04498 -11.79697,9.38 -18.61301,12.70099c-7.67593,3.73596 -11.67996,5.38495 -19.54095,9.27502c-3.04898,1.508 -7.297,4.84192 -9.83005,5.03693c-4.28693,0.31903 13.66003,-5.922 5.54504,2.24603c-5.48999,5.52203 -27.42404,16.383 -33.74197,20.75397c-3.56107,2.45703 -6.996,5.22308 -10.879,7.07404c-3.98903,1.91602 -7.98903,3.77997 -12.01405,5.61401c-7.39603,3.37903 -14.32397,7.10895 -18.31799,14.78302c-1.84802,3.539 -2.57501,8.11493 -3.60403,11.98798c-1.03299,3.84601 -2.92599,7.823 -4.83597,11.25397c-3.72198,6.69904 -14.82001,7.13104 -21.07001,5.28906c-2.88098,-0.85107 -4.96698,-3.12903 -7.10703,-5.099c-2.39294,-2.19604 -1.86798,-5.47107 -2.59995,-8.67804\" id=\"path86\"/>
   </g>
</svg>";
?>
