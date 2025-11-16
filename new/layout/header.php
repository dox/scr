<header class="p-3 bg-body-tertiary">
	<div class="container">
		<div class="navbar d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
			<a href="index.php" class="navbar-brand text-white text-decoration-none">
				<svg width="1.3em" height="1.3em" aria-hidden="true">
					<use xlink:href="assets/images/icons.svg#chough"/>
				</svg> <?php echo APP_NAME; ?>
			</a>
			
			<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
				<li>
					<a href="index.php?page=member" class="nav-link px-2 text-white"><i class="bi me-2 bi-person" aria-hidden="true"></i>Profile</a>
				</li>
				<li>
					<a href="index.php?page=information&subpage=information" class="nav-link px-2 text-white"><i class="bi me-2 bi-journal-text" aria-hidden="true"></i>Information</a>
				</li>
				<li>
					<a href="index.php?page=information&subpage=dining" class="nav-link px-2 text-white"><i class="bi me-2 bi-info-circle" aria-hidden="true"></i>Dining Arrangements</a>
				</li>
			</ul>
			
			<div class="dropdown text-end">
				<a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
					<?php
					echo $user->getUsername(); ?>
				</a>
				
				<ul class="dropdown-menu text-small">
					<?php
					if($user->hasPermission("impersonate")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=admin_impersonate\"><i class=\"bi me-2 bi-person-gear\" aria-hidden=\"true\"></i>Impersonate</a>";
					}
					
					if($user->hasPermission("members")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=members\"><i class=\"bi me-2 bi-people\" aria-hidden=\"true\"></i>Members</a>";
					}
					
					if($user->hasPermission("meals")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=meals\"><i class=\"bi me-2 bi-fork-knife\" aria-hidden=\"true\"></i>Meals</a>";
					}
					
					if($user->hasPermission("wine")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=wine_index\">Wine</a>";
					}
					
					if($user->hasPermission("terms")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=terms\"><i class=\"bi me-2 bi-calendar4-range\" aria-hidden=\"true\"></i>Terms</a>";
					}
					
					if($user->hasPermission("notifications")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=notifications\"><i class=\"bi me-2 bi-bell\" aria-hidden=\"true\"></i>Notifications</a>";
					}
					
					if($user->hasPermission("reports")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=reports\"><i class=\"bi me-2 bi-graph-up-arrow\" aria-hidden=\"true\"></i>Reports</a>";
					}
					
					if($user->hasPermission("settings")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=settings\"><i class=\"bi me-2 bi-gear\" aria-hidden=\"true\"></i>Settings</a>";
					}
					
					if($user->hasPermission("logs")) {
						echo "<a class=\"dropdown-item\" href=\"index.php?page=logs\"><i class=\"bi me-2 bi-clock-history\" aria-hidden=\"true\"></i>Logs</a>";
					}
					?>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="logout.php">Sign out</a></li>
				</ul>
			</div>
		</div>
	</div>
</header>