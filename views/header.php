<header class="navbar navbar-expand-md navbar-light d-print-none">
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
			<span class="navbar-toggler-icon"></span>
		</button>
		<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
			<a href="index.php">
				<svg width="1em" height="1em" viewBox="0 0 16 16" class="navbar-brand-image" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
					<path d="M7.84 4.1a.178.178 0 0 1 .32 0l.634 1.285a.178.178 0 0 0 .134.098l1.42.206c.145.021.204.2.098.303L9.42 6.993a.178.178 0 0 0-.051.158l.242 1.414a.178.178 0 0 1-.258.187l-1.27-.668a.178.178 0 0 0-.165 0l-1.27.668a.178.178 0 0 1-.257-.187l.242-1.414a.178.178 0 0 0-.05-.158l-1.03-1.001a.178.178 0 0 1 .098-.303l1.42-.206a.178.178 0 0 0 .134-.098L7.84 4.1z"/>
				</svg>
			</a>
		</h1>
		<div class="navbar-nav flex-row order-md-last">
			<div class="nav-item dropdown d-none d-md-flex me-3">
				<a href="index.php" class="nav-link px-0" data-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"></path><path d="M9 17v1a3 3 0 0 0 6 0v-1"></path></svg>
					<span class="badge bg-red"></span>
				</a>

				<div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
					<div class="card">
						<div class="card-body">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad amet consectetur exercitationem fugiat in ipsa ipsum, natus odio quidem quod repudiandae sapiente. Amet debitis et magni maxime necessitatibus ullam.
						</div>
					</div>
				</div>
			</div>

			<div class="nav-item dropdown">
				<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown" aria-label="Open user menu">
					<span class="avatar avatar-sm" style="background-image: url(http://ocsd.seh.ox.ac.uk//photos/UAS_UniversityCard-10076320.jpg)"></span>
					<div class="d-none d-xl-block ps-2">
						<div><?php echo $_SESSION['username']; ?></div>
						<div class="mt-1 small text-muted"><?php echo $_SESSION['username']; ?></div>
					</div>
				</a>
				<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
					<?php
						if($_SESSION['admin'] == true) { ?><a class="dropdown-item" href="index.php?n=admin_impersonate">Impersonate</a><?php } ?>
						<a class="dropdown-item" href="index.php?n=admin_members">Members</a>
						<a class="dropdown-item" href="index.php?n=admin_meals">Meals</a>
						<a class="dropdown-item" href="index.php?n=admin_terms">Terms</a>
						<a class="dropdown-item" href="index.php?n=admin_logs">Logs</a>
						<a class="dropdown-item" href="index.php?n=admin_settings">Site Settings</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="logon.php?logout=true">Logout</a>
				</div>
			</div>
		</div>
		<div class="collapse navbar-collapse" id="navbar-menu">
			<div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="index.php">
							<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="5 12 3 12 12 3 21 12 19 12"></polyline><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path></svg></span>
							<span class="nav-link-title">
								Home
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?n=member">
							<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"></polyline><line x1="12" y1="12" x2="20" y2="7.5"></line><line x1="12" y1="12" x2="12" y2="21"></line><line x1="12" y1="12" x2="4" y2="7.5"></line><line x1="16" y1="5.25" x2="8" y2="9.75"></line></svg>
							</span>
							<span class="nav-link-title">
								Your Profile
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php">
							<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="9 11 12 14 20 6"></polyline><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"></path></svg>
							</span>
							<span class="nav-link-title">
								Your Meals
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?n=information">
							<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><line x1="9" y1="9" x2="10" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>
							</span>
							<span class="nav-link-title">
								SCR Information
							</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>
