<?php
$ldap   = $_GET['ldap'] ?? $user->getUsername() ?? null;
$member = Member::fromLDAP($ldap);

if (!isset($member->uid)) {
	die("Unknown or unavailable member");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$member->update($_POST);
	$member = Member::fromLDAP($ldap);
}

echo pageTitle(
	(strtoupper($member->ldap) == $user->getUsername()) ? "Your Profile" : $member->name() . " Profile",
	$member->type . " (" . $member->category . ")" . $member->stewardBadge(),
	[
		[
			'permission' => 'everyone',
			'title' => 'Add meals to your calendar',
			'class' => '',
			'event' => '',
			'icon' => 'calendar2-week',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#calendarModal'
			]
		],
		[
			'divider' => true
		],
		[
			'permission' => 'meals',
			'title' => 'Delete Member',
			'class' => 'text-danger',
			'event' => '',
			'icon' => 'trash3',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteMemberModal'
			]
		]
	]
);
?>

<div class="row mb-3">
	<div class="col">

		<div class="dropdown mb-2">
			<button
				class="btn btn-sm btn-outline-secondary dropdown-toggle"
				type="button"
				id="statsScopeDropdown"
				data-bs-toggle="dropdown"
				aria-expanded="false"
			>
				All
			</button>

			<ul class="dropdown-menu" aria-labelledby="statsScopeDropdown">
				<li>
					<a class="dropdown-item stats-link fw-bold"
					   href="#"
					   data-label="All"
					   data-url="./ajax/member_stats.php?memberUID=<?= $member->uid ?>&scope=all">
						All
					</a>
				</li>
				<li>
					<a class="dropdown-item stats-link"
					   href="#"
					   data-label="Last 12 Months"
					   data-url="./ajax/member_stats.php?memberUID=<?= $member->uid ?>&scope=12m">
						Last 12 Months
					</a>
				</li>
				<li>
					<a class="dropdown-item stats-link"
					   href="#"
					   data-label="YTD"
					   data-url="./ajax/member_stats.php?memberUID=<?= $member->uid ?>&scope=ytd">
						Year To Date
					</a>
				</li>
				<li><hr class="dropdown-divider"></li>
				<li>
					<a class="dropdown-item stats-link"
					   href="#"
					   data-label="Last Term (<?= $terms->previousTerm()->name ?>)"
					   data-url="./ajax/member_stats.php?memberUID=<?= $member->uid ?>&scope=term_previous">
						Last Term (<?= $terms->previousTerm()->name ?>)
					</a>
				</li>
				<li>
					<a class="dropdown-item stats-link"
					   href="#"
					   data-label="This Term (<?= $terms->currentTerm()->name ?>)"
					   data-url="./ajax/member_stats.php?memberUID=<?= $member->uid ?>&scope=term">
						This Term (<?= $terms->currentTerm()->name ?>)
					</a>
				</li>
			</ul>
		</div>

		<div id="member_stats_container">
			<div class="spinner-border" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>

	</div>
</div>

<hr>

<div class="row">
	<div class="col-md-7 col-lg-8">
		<h4>Personal Information</h4>
		
		<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
			<div class="row">
				<div class="col-3 mb-3">
					<label for="title" class="form-label">Title</label>
					<select class="form-select" name="title" id="title" required>
						<option value=""></option>
						<?php
						$memberTitles = explode(',', $settings->get('member_titles'));
						
						foreach ($memberTitles as $title) {
							$title = trim($title);
							$selected = ($title === $member->title) ? ' selected' : '';
							echo "<option value=\"{$title}\"{$selected}>{$title}</option>";
						}
						?>
					</select>
					<div class="invalid-feedback">
						Title is required.
					</div>
				</div>
				<div class="col mb-3">
					<label for="firstname" class="form-label">First name</label>
					<input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" value="<?php echo $member->firstname; ?>" required>
					<div class="invalid-feedback">
						Valid first name is required.
					</div>
				</div>
			</div>
			
			<div class="mb-3">
				<label for="firstname" class="form-label">Last name</label>
				<input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" value="<?php echo $member->lastname; ?>" required>
				<div class="invalid-feedback">
					Valid last name is required.
				</div>
			</div>
			
			<div class="mb-3">
				<label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
				<input type="email" class="form-control" name="email" id="email" placeholder="Email address" value="<?php echo $member->email; ?>">
			</div>
			
			<div class="mb-3">
				<label for="ldap" class="form-label">LDAP Username</label>
				<div class="input-group">
					<span class="input-group-text">@</span>
					<input
						type="text"
						class="form-control"
						name="ldap"
						id="ldap"
						placeholder="LDAP Username"
						value="<?= htmlspecialchars($member->ldap ?? '', ENT_QUOTES) ?>"
						<?= $user->hasPermission('global_admin') ? '' : 'disabled' ?>
						required
					>
					<div class="invalid-feedback">
						Valid LDAP username is required.
					</div>
				</div>
				<?php
				if ($member->lastLogon()) {
					echo "<small class=\"form-text text-muted\">Last logon: " . formatDate($member->lastLogon()) . " " . formatTime($member->lastLogon()) . "</small>";
				}
				?>
			</div>
			
			<div class="row">
				<div class="col">
					<div class="mb-3">
						<label for="type" class="form-label">Member Type</label>
						<select class="form-select" name="type" id="type" <?= $user->hasPermission('members') ? '' : 'disabled' ?> required>
							<?php
							$memberTypes = explode(',', $settings->get('member_types'));
							
							foreach ($memberTypes as $type) {
								$type = trim($type);
								$selected = ($type === $member->type) ? ' selected' : '';
								echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
							}
							?>
						</select>
						<div class="invalid-feedback">
							Valid member type is required.
						</div>
					</div>
				</div>
				<div class="col">
					<div class="mb-3">
						<label for="title" class="form-label">Member Category</label>
						<select class="form-select" name="category" id="category" <?= $user->hasPermission('members') ? '' : 'disabled' ?> required>
							<?php
							$memberCategories = explode(',', $settings->get('member_categories'));
							
							foreach ($memberCategories as $category) {
								$category = trim($category);
								$selected = ($category === $member->category) ? ' selected' : '';
								echo "<option value=\"{$category}\"{$selected}>{$category}</option>";
							}
							?>
						</select>
						<div class="invalid-feedback">
							Valid category is required.
						</div>
					</div>
				</div>
			</div>
			
			
			
			<div class="mb-3">
				<label for="enabled" class="form-label">Enabled/Disabled Status</label>
				<select class="form-select" name="enabled" id="enabled" <?= $user->hasPermission('members') ? '' : 'disabled' ?> required>
					<option value="1" <?php if ($member->enabled == "1") { echo " selected"; } ?>>Enabled</option>
					<option value="0" <?php if ($member->enabled == "0") { echo " selected"; } ?>>Disabled</option>
				</select>
				<div class="invalid-feedback">
					Enabled/disabled status is required.
				</div>
			</div>
			
			<hr>
			
			<h4>Default Preferences</h4>
			
			<div class="accordion mb-3" id="accordionDietary">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Dietary Information<i class="ms-1">(Maximum: <?= $settings->get('meal_dietary_allowed'); ?>)</i>
					</h2>
					<div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
						<div class="accordion-body" data-max="<?php echo $settings->get('meal_dietary_allowed'); ?>">
							<?php
							$dietaryOptions    = array_map('trim', explode(',', $settings->get('meal_dietary')));
							$memberDietary     = array_map('trim', explode(',', $member->dietary ?? ''));
							$dietaryNotes      = $member->dietary_notes ?? '';
							
							asort($dietaryOptions);
							
							$output = '';
							
							foreach ($dietaryOptions as $index => $dietaryOption) {
								$checked    = in_array($dietaryOption, $memberDietary, true) ? ' checked' : '';
								$safeValue  = htmlspecialchars($dietaryOption, ENT_QUOTES);
								$checkboxId = "dietary_{$index}";
							
								$output .= '<div class="form-check">';
								$output .= '<input class="form-check-input dietaryOptionsMax" '
										 . 'type="checkbox" '
										 . 'name="dietary[]" '
										 . 'id="' . $checkboxId . '" '
										 . 'value="' . $safeValue . '"' 
										 . $checked 
										 . '>';
								$output .= '<label class="form-check-label" for="' . $checkboxId . '">' 
										 . $safeValue 
										 . '</label>';
								$output .= '</div>';
							}
							
							echo $output;
							?>
							
							<textarea class="form-control mt-3" id="dietary_notes" name="dietary_notes" rows="3" placeholder="Additional dietary notes/requests"><?= htmlspecialchars($dietaryNotes, ENT_QUOTES); ?></textarea>
							
							<small id="nameHelp" class="form-text text-muted"><?php echo $settings->get('meal_dietary_message'); ?></small>
						</div>
					</div>
				</div>
			</div>
			
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="opt_in" name="opt_in" value="1" <?php if ($member->opt_in == "1") { echo " checked";} ?> switch>
				<label class="form-check-label" for="opt_in">Allow my name to appear on dining lists (also applies to my guests</label>
			</div>
			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" id="email_reminders" name="email_reminders" value="1" <?php if ($member->email_reminders == "1") { echo " checked";} ?> switch>
				<label class="form-check-label" for="email_reminders">Send me an email confirmation when I book a meal</label>
			</div>
			<div class="mb-3">
				<label for="wine_choice" class="form-label">Default Wine <small>(when available)</small></label>
				<select class="form-select" id="default_wine_choice" name="default_wine_choice" required>
					<?php
					$wineOptions = explode(",", $settings->get('booking_wine_options'));
					
					foreach ($wineOptions as $i => $wineOption) {
						$wineOption = trim($wineOption);
						$selected = ($wineOption === $member->default_wine_choice) ? ' selected' : '';
						echo "<option value=\"{$wineOption}\"{$selected}>{$wineOption}</option>";
					}
					?>
				</select>
			</div>
			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" id="default_dessert" name="default_dessert" value="1" <?php if ($member->default_dessert == "1") { echo " checked";} ?> switch>
				<label class="form-check-label" for="email_reminders">Default Dessert <small>(when available)</small></label>
			</div>
			
			
			<?php
			// only show permissions to global admins
			if ($user->hasPermission('global_admin')) {
				$grantedPermissions = $member->permissions();
				
				$output = "<hr><h4>Permissions</h4>";
				foreach ($user->available_permissions() as $permission => $description) {
					if (in_array($permission, $grantedPermissions)) {
						$checked = " checked ";
					} else {
						$checked = " ";
					}
					
					$output .= "<div class=\"form-check\">";
					$output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $permission . "\" name=\"permissions[]\" " . $checked . ">";
					$output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\"><strong>" . $permission . "</strong> <small>" . $description . "</small></label>";
					$output .= "</div>";
				}
				
				$output .= "<hr>";
				echo $output;
			  }
			  ?>
			  
			  <button type="submit" class="btn btn-primary w-100 mb-3">Update Profile</button>
		</form>
	</div>
	<div class="col-md-5 col-lg-4">
		<?php
		$upcomingBookings = $member->bookingsBetweenDates(date('Y-m-d'), date('Y-m-d', strtotime('+1 year')));
		?>
		<h4 class="d-flex justify-content-between align-items-center mb-3">
		  <span>Upcoming Meals</span>
		  <span class="badge bg-secondary rounded-pill"><?php echo count($upcomingBookings); ?></span>
		</h4>
		<ul class="list-group mb-3">
			<?php
			foreach ($upcomingBookings as $booking) {
				echo $booking->displayMemberListGroupItem();
			}
			?>
		</ul>
		
		<?php
		$recentBookings = $member->bookingsBetweenDates(date('Y-m-d', strtotime('-1 year')), date('Y-m-d', strtotime('-1 day')));
		krsort($recentBookings);
		?>
		<h4 class="mb-3">
		  <span>Recent Meals</span>
		</h4>
		<ul class="list-group mb-3">
			<?php
			foreach (array_slice($recentBookings, 0, 10) as $booking) {
				echo $booking->displayMemberListGroupItem();
			}
			?>
		</ul>
		
		<div class="text-end">
			<button class="btn btn-sm btn-outline-light" 
					type="button" 
					onclick="window.location='report.php?page=member_bookings&uid=<?= $member->uid; ?>'">
				<i class="bi bi-download"></i> export
			</button>
		</div>
		
		<hr>
		
		<h4 class="mb-3">Bookings by Day</h4>
		<div class="mb-3">
			<canvas id="chart_bookingsByDay"></canvas>
		</div>
	</div>
</div>










<!-- Delete Member Modal -->
<div class="modal fade" tabindex="-1" id="deleteMemberModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php?page=members">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Member <span class="text-danger"><strong>WARNING!</strong></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong></span> Are you sure you want to delete this member?</p>
				<p>This will also delete <strong>all</strong> bookings (past and present) for this member.</p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
				<input type="text" class="form-control mb-3" id="delete_confirm" placeholder="Type 'DELETE' to confirm" oninput="enableOnExactMatch('delete_confirm', 'delete_button', 'DELETE')">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger" id="delete_button" disabled>Delete Member</button>
				<input type="hidden" name="deleteMemberUID" value="<?= $member->uid; ?>">
			</div>
		</div>
	</div>
	</form>
</div>

<!-- Calendar Subscribe Modal -->
<div class="modal fade" tabindex="-1" id="calendarModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Subscribe to your meal calendar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="accordion" id="accordionCalendar">
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMicrosoft" aria-expanded="false" aria-controls="collapseMicrosoft">
							<i class="bi me-2 bi-microsoft" aria-hidden="true"></i>Windows/Microsoft Outlook</button>
						</h2>
						<div id="collapseMicrosoft" class="accordion-collapse collapse" data-bs-parent="#accordionCalendar">
							<div class="accordion-body">
								<?php
								$url = "https://" . $_SERVER['HTTP_HOST'] . "/calendar.php?hash=" . $member->calendar_hash;
								?>
								
								<p>Please <strong>copy</strong> the following link (this is your unique URL to your meal calendar)</p>
								
								<div class="input-group mb-3">
								  <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $url; ?>">
								  <span class="input-group-text" id="basic-addon1" onclick="copyToClipboard(this)" onKeyDown="copyToClipboard(this)" data-copy="<?php echo $url; ?>"><i class="bi bi-copy"></i></span>
								</div>
								  
								<p>Open Outlook (Or log in to your Outlook account on the web at <a href="https://outlook.live.com/">https://outlook.live.com/</a> and open your calendar.</p>
								<p>Click on "Add calendar" in the left-hand panel.</p>
								<p>Click "Subscribe from web" and paste in the copied URL.  Give your calendar a name, customise any details, and click "Import".</p>
								<p>Your calendar will appear under "Other calendars".</p>
								
								<hr />
								
								<p><strong>Please note:</strong> changes to your SCR bookings can take up to 24 hours to update in your calendar!</p>
							</div>
						</div>
					</div>
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseApple" aria-expanded="false" aria-controls="collapseApple">
							<i class="bi me-2 bi-apple" aria-hidden="true"></i>Apple/iCalendar</button>
						</h2>
						
						<div id="collapseApple" class="accordion-collapse collapse" data-bs-parent="#accordionCalendar">
							<div class="accordion-body">
								<p>Please <strong>click</strong> the following link (this is your unique URL to your meal calendar) then click 'Allow', then 'Subscribe'.</p>
								<p><a href="webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/calendar.php?hash=<?php echo $member->calendar_hash; ?>">webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/calendar.php?hash=<?php echo $member->calendar_hash; ?></a></p>
								<p>Your calendar will appear under "Other calendars".</p>
								
								<hr />
								
								<p><strong>Please note:</strong> changes to your SCR bookings can take up to 2 hours to update in your calendar!</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php
$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

$datasets = [];
foreach ($member->bookingsByDay() as $mealType => $dayCounts) {
	$data = [];
	foreach ($days as $dayName) {
		$data[] = $dayCounts[$dayName] ?? 0;
	}
	$datasets[] = [
		'label' => $mealType,
		'data' => $data
	];
}
?>

<script>
const ctx = document.getElementById('chart_bookingsByDay');
const chart = new Chart(ctx, {
  type: 'bar',
  data: {
	labels: <?= json_encode($days) ?>,
	datasets: <?= json_encode($datasets) ?>
  },
  options: {
	plugins: {
	  legend: {
		display: true
	  }
	},
	scales: {
	  x: { stacked: true },
	  y: { stacked: true, beginAtZero: true }
	}
  }
});
</script>

<script>
// Initialize stats links (unchanged)
initAjaxLoader('.stats-link', '#member_stats_container', { event: 'click', cache: true });

// Handle active state + dropdown label
document.addEventListener('click', function (e) {
	const link = e.target.closest('.stats-link');
	if (!link) return;

	e.preventDefault();

	// Clear active state
	document.querySelectorAll('.stats-link').forEach(l => l.classList.remove('fw-bold'));
	link.classList.add('fw-bold');

	// Update dropdown button label (if present)
	const dropdownButton = document.getElementById('statsScopeDropdown');
	const label = link.dataset.label;

	if (dropdownButton && label) {
		dropdownButton.textContent = label;
	}
});



function copyToClipboard(button) {
	const text = button.dataset.copy;

	navigator.clipboard.writeText(text).then(() => {
		// Optional feedback
		button.innerHTML = '<i class="bi bi-check2"></i>';
		setTimeout(() => {
			button.innerHTML = '<i class="bi bi-copy"></i>';
		}, 1500);
	}).catch(err => {
		console.error('Failed to copy:', err);
	});
}
</script>