<?php
function printArray($data): void {
	echo "<div class=\"alert alert-info\" role=\"alert\" style=\"font-family:monospace;\"><pre>";
	
	if (is_array($data) || is_object($data)) {
		echo htmlspecialchars(print_r($data, true));
	} else {
		echo htmlspecialchars(var_export($data, true));
	}
	echo "</pre></div>";
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

function timeago($date) {
   $timestamp = strtotime($date);	
   
   $strTime = array("second", "minute", "hour", "day", "month", "year");
   $length = array("60","60","24","30","12","10");

   $currentTime = time();
   if($currentTime >= $timestamp) {
		$diff     = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		
		return $diff . " " . autoPluralise($strTime[$i], $strTime[$i] . "s", $diff) . " ago ";
   }
}

function currencyDisplay($value = null) {
	if (is_numeric($value)) {
		$output = "£" . number_format($value,2);
	} else {
		$output = "£" . $value;
	}
	
	return $output;
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

function escape($var = null) {
	if (!empty($var) && !is_array($var)) {
		$var = stripslashes($var);
		$var = htmlentities($var);
		//$var = htmlspecialchars($value, ENT_QUOTES);
		$var = strip_tags($var);
		$var = str_replace("'", "\'", $var);
	}
	

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

function available_permissions() {
	$availablePermissions = array(
		"global_admin" => "Access to all settings.  Overrides for all limits",
		"meals" => "Add/Edit/Delete meals",
		"bookings" => "Ability to override meal restrictions/cutoffs",
		"members" => "Add/Edit/Delete members",
		"impersonate" => "Ability to impersonate other members",
		"notifications" => "Add/Edit/Delete notifications",
		"terms" => "Add/Edit/Delete terms",
		"wine" => "Add/Edit/Delete wines",
		"reports" => "Add/Edit/Delete/Run reports",
		"settings" => "Add/Edit/Delete site settings",
		"logs" => "View logs"
	);
	
	return $availablePermissions;
}

function isLoggedIn() {
  if ($_SESSION['logon'] == 1) {
	return true;
  } else {
	return false;
  }
}

function checkpoint_charlie($arrayOfAcceptablePermissions = null) {
	$return = false;
	
	if (isLoggedIn()) {
		if (!is_array($arrayOfAcceptablePermissions)) {
			$arrayOfAcceptablePermissions = explode(",", $arrayOfAcceptablePermissions);
		}
		
		// if a member only has 1 permission, make this an array anyway
		if (!is_array($_SESSION['permissions'])) {
			$_SESSION['permissions'] = array($_SESSION['permissions']);
		}
		
		if (in_array("global_admin", $_SESSION['permissions'])) {
			$return = true;
		} else {
			foreach ($arrayOfAcceptablePermissions AS $permission) {
				if (in_array($permission, $_SESSION['permissions'])) {
					$return = true;
				}
			}
		}
	}
	
	return $return;
}

function pageAccessCheck($arrayOfAcceptablePermissions = null) {
	if (!checkpoint_charlie($arrayOfAcceptablePermissions)) {
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

function makeTitle($title = null, $subtitle = null, $iconsArray = null, $allowGrouping = false, $extra = null) {
	$output  = "<div class=\"p-3 p-md-5 text-center\">";
	$output .= "<h1 class=\"display-4\">" . $title . "</h1>";
	
	if ($subtitle != null) {
		$output .= "<p class=\"lead\">" . $subtitle . "</p>";
	}
	
	$output .= "</div>";
	
	$output .= "<div class=\"row\">";
	$output .= "<div class=\"col-8\">";
	$output .= "";
	$output .= "</div>";
	$output .= "<div class=\"col-4 text-end\">";
		$output .= "<div class=\"d-inline-flex gap-2\">";
			if (!empty($extra)) {
				$output .= $extra;
			}
			
			$output .= makeTitleActionButton($iconsArray, $allowGrouping);
		$output .= "</div>";
	
	$output .= "</div>";
	$output .= "</div>";

	
	
	

	return $output;
}

function makeTitleActionButton($iconsArray, $allowGrouping = false) {
	$output = "";
	
	if (!is_array($iconsArray)) {
		return false;
	}
	
	if ($allowGrouping == false) {
		foreach ($iconsArray AS $icon) {
			$output .= "<button type=\"button\" class=\"btn btn-sm ms-1 " . $icon['class'] . "\"" . $icon['value'] . ">";
			$output .= $icon['name'];
			$output .= "</button>";
		}
	} else {
		$output .= "<div class=\"dropdown\">";
		$output .= "<button class=\"btn btn-sm btn-secondary dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">Actions</button>";
		$output .= "<ul class=\"dropdown-menu\">";
		
		foreach ($iconsArray AS $icon) {
			$output .= "<li class=\"dropdown-item " . $icon['class'] . "\"" . $icon['value'] . ">";
			$output .= $icon['name'];
			$output .= "</li>";
		}
		
		$output .= "</ul>";
		$output .= "</div>";
	}
	
	
	
	return $output;
}

function onToOne($input = null) {
	$return = "";
	if ($input == "on" || $input == "1") {
		$return = 1;
	}
	return $return;
}

function sendMail($subject = "No Subject Specified", $recipients = NULL, $body = NULL, $senderAddress = NULL, $senderName = NULL) {
	global $mail, $logsClass;
	
	// clear addresses of all types
	$mail->clearAllRecipients();

	$mail->IsSMTP();
	$mail->Host = smtp_server;
	$mail->IsHTML(true);

	if (isset($senderAddress)) {
		$mail->From = $senderAddress;
		$mail->FromName = $senderName;
		$mail->AddReplyTo($senderAddress, $senderName);
	} else {
		$mail->From = smtp_sender_address;
		$mail->FromName = smtp_sender_name;
	}
	
	foreach ($recipients AS $recipient) {
		$mail->addBCC($recipient);
	}
	
	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->IsHTML(true);                                  // Set email format to HTML

	$mail->Subject = $subject;
	$mail->Body    = $body;
	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
	if (debug) {
		// don't email when we are in debug mode!
		$logArray['category'] = "email";
		$logArray['result'] = "success";
		$logArray['description'] = "DEBUG MODE ON, but otherwise email would have been sent to " . implode(", ",array_keys($mail->getAllRecipientAddresses())) . ". Subject: " . $subject;
		$logsClass->create($logArray);
	} else {
		if($mail->Send()) {
			$logArray['category'] = "email";
			$logArray['result'] = "success";
			$logArray['description'] = "Email sent to " . implode(", ",array_keys($mail->getAllRecipientAddresses())) . ". Subject: " . $subject;
			$logsClass->create($logArray);
		} else {
			$logArray['category'] = "email";
			$logArray['result'] = "danger";
			$logArray['description'] = "Email could not be sent to " . implode(", ",array_keys($mail->getAllRecipientAddresses())) . " <code>" . $mail->ErrorInfo . "</code>";
			$logsClass->create($logArray);
		}
	}
	
}

function randomQuote($seed = null) {
	$quotes = [
		"Provision for this week hath not yet been made known.",
		"Repast for this week remaineth shrouded in mystery.",
		"The victuals ordained for this week are, as yet, unknown to mortal man.",
		"Provision for the coming days hath not been disclosed, perchance due to forces beyond our ken.",
		"Lo, the feast of the week is veiled in secrecy, as though the gods themselves withhold it.",
		"The sustenance of the week remaineth a riddle wrapped in an enigma, basted in uncertainty.",
		"Alas, the bill of fare for this week hath not yet been divined by augurs nor scribes.",
		"No herald hath yet declared what sustenance shall grace our tables in the days to come.",
		"The week's repast remaineth as elusive as the Holy Grail, and twice as coveted.",
		"Lo, the larder-keepers keepeth their counsel, and naught is revealed of the coming feasts.",
		"The mysteries of the menu endure, locked away as though in the vaults of the gods themselves.",
		"The provender of the week is as yet unknown—mayhap lost in the ether or concealed by mischievous sprites.",
		"This week’s nourishment is but a spectre, glimpsed not by even the keenest of gastronomic soothsayers."
	];
	
	$hash = crc32($seed); // Generate a numeric hash from the week
	$index = $hash % count($quotes); // Get a consistent index within bounds
	
	return $quotes[$index];
}

?>
