<nav class="navbar navbar-expand-lg bg-body-tertiary">
	<div class="container">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarTogglerDemo01">
			  <a class="navbar-brand" href="index.php">
				  <svg class="me-2" width="1.3em" height="1.3em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#chough"/></svg> <?php echo APP_NAME; ?>
			  </a>
			  
			  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
				  <li>
					  <a href="index.php?page=member" class="nav-link"><i class="bi me-2 bi-person" aria-hidden="true"></i>Profile</a>
				  </li>
				  <li>
					  <a href="index.php?page=information&subpage=information" class="nav-link"><i class="bi me-2 bi-journal-text" aria-hidden="true"></i>Information</a>
				  </li>
				  <li>
					  <a href="index.php?page=information&subpage=dining" class="nav-link"><i class="bi me-2 bi-info-circle" aria-hidden="true"></i>Dining Arrangements</a>
				  </li>
			  </ul>
			  
			  <div class="d-flex">
				  
				  
				  <div class="dropdown me-3">
						<a href="#" id="bd-theme"
						   class="d-block link-body-emphasis text-decoration-none dropdown-toggle"
						   data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-moon-stars-fill"></i>
							<span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
						</a>
						
						<ul class="dropdown-menu text-small">
							<li><button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
								  <i class="bi bi-sun me-2"></i>
								  Light
								  <i class="bi bi-check2"></i>
								</button>
							</li>
							<li>
								<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
									<i class="bi bi-moon-stars-fill me-2"></i>
									Dark
									<i class="bi bi-check2"></i>
								  </button>
							</li>
							<li>
								<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
									<i class="bi bi-circle-half me-2"></i>
									Auto
									<i class="bi bi-check2"></i>
								  </button>
							</li>
						</ul>
					</div>
				  
				  
					
					<div class="dropdown text-end">
						  <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							  <?php
							  echo $user->getUsername(); ?>
						  </a>
						  
						  <ul class="dropdown-menu text-small">
							  <?php
							  if($user->hasPermission("impersonate")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=admin_impersonate\"><i class=\"bi me-2 bi-person-gear\" aria-hidden=\"true\"></i>Impersonate</a></li>";
							  }
							  
							  if($user->hasPermission("members")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=members\"><i class=\"bi me-2 bi-people\" aria-hidden=\"true\"></i>Members</a></li>";
							  }
							  
							  if($user->hasPermission("meals")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=meals\"><i class=\"bi me-2 bi-fork-knife\" aria-hidden=\"true\"></i>Meals</a></li>";
							  }
							  
							  if($user->hasPermission("wine")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=wine_index\">Wine</a></li>";
							  }
							  
							  if($user->hasPermission("terms")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=terms\"><i class=\"bi me-2 bi-calendar4-range\" aria-hidden=\"true\"></i>Terms</a></li>";
							  }
							  
							  if($user->hasPermission("notifications")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=notifications\"><i class=\"bi me-2 bi-bell\" aria-hidden=\"true\"></i>Notifications</a></li>";
							  }
							  
							  if($user->hasPermission("reports")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=reports\"><i class=\"bi me-2 bi-graph-up-arrow\" aria-hidden=\"true\"></i>Reports</a></li>";
							  }
							  
							  if($user->hasPermission("settings")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=settings\"><i class=\"bi me-2 bi-gear\" aria-hidden=\"true\"></i>Settings</a></li>";
							  }
							  
							  if($user->hasPermission("logs")) {
								  echo "<li><a class=\"dropdown-item\" href=\"index.php?page=logs\"><i class=\"bi me-2 bi-clock-history\" aria-hidden=\"true\"></i>Logs</a></li>";
							  }
							  ?>
							  <li><hr class="dropdown-divider"></li>
							  <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
						  </ul>
					  </div>
			  </div>
		  </div>
	  </div>
</nav>

<?php
if (APP_DEBUG) {
	$output  = "<nav class=\"navbar navbar-expand-lg bg-warning\" data-bs-theme=\"dark\">";
	$output .= "<div class=\"container\">";
	$output .= "<strong>PRODUCTION</strong> Site is in production mode.  All data is for testing purposes only.  No emails will be sent.";
	$output .= "</div>";
	$output .= "</nav>";
	
	echo $output;
}
?>