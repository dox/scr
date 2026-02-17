<nav class="navbar navbar-expand-lg bg-body-tertiary d-print-none">
	<div class="container">
		<a class="navbar-brand" href="index.php">
			<svg class="me-2" width="1.3em" height="1.3em" aria-hidden="true">
				<use xlink:href="assets/images/icons.svg#chough"></use>
			</svg> <?php echo APP_NAME; ?>
		</a>
		
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarMain">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li>
					<a href="index.php?page=member" class="nav-link"><i class="bi bi-person me-2"></i>Profile</a>
				</li>
				<li>
					<a href="index.php?page=information&subpage=scr_information" class="nav-link"><i class="bi bi-info-circle me-2"></i>SCR Information</a>
				</li>
				<li>
					<a href="index.php?page=information&subpage=dining_arrangements" class="nav-link"><i class="bi bi-journal-text me-2"></i>Dining Arrangements</a>
				</li>
			</ul>
			
			<div class="d-flex align-items-center gap-3">
				<?php
				if (isset($_SESSION['impersonating'])) {
					$output  = "<a class=\"btn btn-sm btn-info\" href=\"index.php?page=impersonate\" role=\"button\" aria-disabled=\"true\">Impersonating</a>";
					echo $output;
				}
				?>
				
				<!--<button type="button" class="btn btn-link position-relative">
					<i class="bi bi-bell"></i>
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
						99+
						<span class="visually-hidden">notifications</span>
					</span>
				</button>-->
				
				<?php
				// in development
				if ($user->getUsername() == "BREAKSPEAR") {
					echo '<a href="index.php?page=advent" class="jiggle-icon btn btn-link position-relative" role="button" aria-label="Open Advent">
					  <i class="bi bi-tree-fill" aria-hidden="true"></i>
					  <span class="sparkle" aria-hidden="true"></span>
					</a>';
				}
				?>
				
				<div class="dropdown">
					<a href="#" id="bd-theme" class="d-block link-body-emphasis text-decoration-none dropdown-toggle"
					   data-bs-toggle="dropdown" aria-expanded="true" aria-label="Toggle theme (auto)">
						<i class="bi bi-circle-half"></i>
						<span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
					</a>
				
					<ul class="dropdown-menu dropdown-menu-end text-small" data-bs-popper="static">
						<li>
							<button type="button"
								class="dropdown-item position-relative"
								data-bs-theme-value="light" aria-pressed="false">
								<span><i class="bi bi-sun me-2"></i> Light</span>
								<i class="bi bi-check2 d-none"></i>
							</button>
						</li>
						<li>
							<button type="button"
								class="dropdown-item position-relative"
								data-bs-theme-value="dark" aria-pressed="false">
								<span><i class="bi bi-moon-stars-fill me-2"></i> Dark</span>
								<i class="bi bi-check2 d-none"></i>
							</button>
						</li>
						<li>
							<button type="button"
								class="dropdown-item position-relative active"
								data-bs-theme-value="auto" aria-pressed="true">
								<span><i class="bi bi-circle-half me-2"></i> Auto</span>
								<i class="bi bi-check2"></i>
							</button>
						</li>
					</ul>
				</div>
				
				<div class="dropdown">
					<a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?= $user->getUsername(); ?></a>
					<ul class="dropdown-menu dropdown-menu-end text-small">
						<?php
						if($user->hasPermission("impersonate")) {
							echo "<li><a class=\"dropdown-item\" href=\"index.php?page=impersonate\"><i class=\"bi me-2 bi-person-gear\" aria-hidden=\"true\"></i>Impersonate</a></li>";
						}
						
						if($user->hasPermission("members")) {
							echo "<li><a class=\"dropdown-item\" href=\"index.php?page=members\"><i class=\"bi me-2 bi-people\" aria-hidden=\"true\"></i>Members</a></li>";
						}
						
						if($user->hasPermission("meals")) {
							echo "<li><a class=\"dropdown-item\" href=\"index.php?page=meals\"><i class=\"bi me-2 bi-fork-knife\" aria-hidden=\"true\"></i>Meals</a></li>";
						}
						
						if($user->hasPermission("wine")) {
							echo '<li><a class="dropdown-item" href="index.php?page=wine_index"><svg class="bi me-2" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg>Wine</a></li>';
						}
						
						if($user->hasPermission("terms")) {
							echo "<li><a class=\"dropdown-item\" href=\"index.php?page=terms\"><i class=\"bi me-2 bi-calendar4-range\" aria-hidden=\"true\"></i>Terms</a></li>";
						}
						
						/*if($user->hasPermission("notifications")) {
							echo "<li><a class=\"dropdown-item\" href=\"index.php?page=notifications\"><i class=\"bi me-2 bi-bell\" aria-hidden=\"true\"></i>Notifications</a></li>";
						}*/
						
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
