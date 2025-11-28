<?php
function timeAgoFromSeconds($seconds) {
	if ($seconds <= 0) {
		return 'just now';
	}

	// Define time units
	$units = [
		'year' => 31536000,   // 365 days
		'month' => 2592000,   // 30 days
		'day' => 86400,       // 1 day
		'hour' => 3600,       // 1 hour
		'minute' => 60,       // 1 minute
		'second' => 1         // 1 second
	];

	foreach ($units as $unit => $value) {
		if ($seconds >= $value) {
			$count = floor($seconds / $value);
			return $count . ' ' . $unit . ($count > 1 ? 's' : '') . ' ago';
		}
	}
}

function printArray($data): void {
	echo "<div class=\"alert alert-info\" role=\"alert\" style=\"font-family:monospace;\"><pre>";
	
	if (is_array($data) || is_object($data)) {
		echo htmlspecialchars(print_r($data, true));
	} else {
		echo htmlspecialchars(var_export($data, true));
	}
	echo "</pre></div>";
}

function formatMoney(int|float $amount): string {
	$currencySymbol = "£";
	
	// Ensure proper rounding and thousands separator
	return $currencySymbol . number_format($amount, 2, '.', ',');
}

function formatDate(string|int|\DateTimeInterface|null $date, string $format = 'long'): string {
	if ($date === null || $date === '') {
		return '';
	}

	if (!$date instanceof \DateTimeInterface) {
		try {
			$date = new \DateTime($date);
		} catch (\Exception $e) {
			return '';
		}
	}

	// choose the pattern
	global $settings; // or pass this into the function if purism calls

	$pattern = match ($format) {
		'long'  => $settings->get('datetime_format_long'),
		'short' => $settings->get('datetime_format_short'),
		default => $settings->get('datetime_format_long'),
	};

	return $date->format($pattern);
}

function formatTime(string|int|\DateTimeInterface|null $time): string {
	if ($time === null || $time === '') {
		return '';
	}

	if (!$time instanceof \DateTimeInterface) {
		try {
			$time = new \DateTime($time);
		} catch (\Exception $e) {
			return '';
		}
	}

	// Classic 24-hour clock, without seconds
	return $time->format('H:i');
}

function pageTitle(string $title, string $subtitle = '', array $actions = []): string {
	global $user;
	
	$html = '<div class="p-3 p-md-5 text-center">';
	$html .= '<h1 class="display-4">' . $title . '</h1>';
	if ($subtitle !== '') {
		$html .= '<p class="lead">' . $subtitle . '</p>';
	}
	$html .= '</div>';

	if (!empty($actions)) {
		$html .= '<div class="row mb-3">';
		$html .= '<div class="col-8"></div>';
		$html .= '<div class="col-4 text-end">';
		$html .= '<div class="d-inline-flex gap-2">';
		$html .= '<div class="dropdown">';
		$html .= '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>';
		$html .= '<ul class="dropdown-menu">';

		foreach ($actions as $action) {
			if ($action['permission'] == "everyone" || $user->hasPermission($action['permission'])) {
		
				$titleAttr = htmlspecialchars($action['title']);
				$classAttr = htmlspecialchars($action['class'] ?? '');
				$icon = !empty($action['icon']) ? '<i class="bi me-2 bi-' . htmlspecialchars($action['icon']) . '"></i> ' : '';
				$eventAttr = $action['event'] ?? '';
				$extraAttrs = '';
		
				// Handle Bootstrap data attributes (modals, etc.)
				if (!empty($action['data']) && is_array($action['data'])) {
					foreach ($action['data'] as $k => $v) {
						$extraAttrs .= ' data-' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
					}
				}
		
				// Determine rendering
				if (!empty($eventAttr)) {
					if (str_starts_with($eventAttr, 'javascript:')) {
						// Inline JS
						$html .= '<li><a class="dropdown-item ' . $classAttr . '" href="#" onclick="' 
							   . substr($eventAttr, 11) . '"' . $extraAttrs . '>' 
							   . $icon . $titleAttr . '</a></li>';
					} else {
						// Treat as normal link, same window
						$html .= '<li><a class="dropdown-item ' . $classAttr . '" href="' 
							   . htmlspecialchars($eventAttr) . '"' . $extraAttrs . '>' 
							   . $icon . $titleAttr . '</a></li>';
					}
				} else {
					// No href, maybe modal only
					$html .= '<li class="dropdown-item ' . $classAttr . '"' . $extraAttrs . '>' 
						   . $icon . $titleAttr . '</li>';
				}
			}
		}

		$html .= '</ul></div></div></div></div>';
	}

	return $html;
}

function toast($title, $message, $class = 'text-success') {
	$_SESSION['toasts'][] = [
		'title'   => $title,
		'message' => $message,
		'class'    => $class
	];
}

function displayToast($array) {
	$title   = $array['title']   ?? 'Notification';
	$message = $array['message'] ?? '';
	
	// Only allow valid Bootstrap text classes
	$allowedClasses = ['text-primary', 'text-secondary', 'text-success', 'text-danger', 'text-warning', 'text-info', 'text-light', 'text-dark'];
	$class = $array['class'] ?? 'text-success';
	if (!in_array($class, $allowedClasses, true)) {
		$class = 'text-success';
	}
	
	$output  = '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">';
	$output .= '<div class="toast-header">';
	$output .= '<i class="bi bi-square-fill ' . $class . ' me-2"></i>';
	$output .= '<strong class="me-auto">' . htmlspecialchars($title) . '</strong>';
	$output .= '<small>just now</small>';
	$output .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>';
	$output .= '</div>';
	$output .= '<div class="toast-body">' . htmlspecialchars($message) . '</div>';
	$output .= '</div>';
	
	return $output;
}
