<div class="mb-3">
	<header class="navbar navbar-expand-md navbar-light d-print-none">
		<div class="container-xl">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
				<span class="navbar-toggler-icon"></span>
			</button>
			<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
				<a href=".">
					<svg width="1em" height="1em" viewBox="0 0 16 16" class="navbar-brand-image mr-3" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
						<path d="M7.84 4.1a.178.178 0 0 1 .32 0l.634 1.285a.178.178 0 0 0 .134.098l1.42.206c.145.021.204.2.098.303L9.42 6.993a.178.178 0 0 0-.051.158l.242 1.414a.178.178 0 0 1-.258.187l-1.27-.668a.178.178 0 0 0-.165 0l-1.27.668a.178.178 0 0 1-.257-.187l.242-1.414a.178.178 0 0 0-.05-.158l-1.03-1.001a.178.178 0 0 1 .098-.303l1.42-.206a.178.178 0 0 0 .134-.098L7.84 4.1z"/>
					</svg>SCR Meal Booking
				</a>
			</h1>

			<div class="navbar-nav flex-row order-md-last">
				<div class="nav-item dropdown d-none d-md-flex mr-3">
					<a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"></path><path d="M9 17v1a3 3 0 0 0 6 0v-1"></path></svg>
						<span class="badge bg-red"></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-card">
						<div class="card">
							<div class="card-body">
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad amet consectetur exercitationem fugiat in ipsa ipsum, natus odio quidem quod repudiandae sapiente. Amet debitis et magni maxime necessitatibus ullam.
							</div>
						</div>
					</div>
				</div>

				<div class="nav-item dropdown">
					<a href="#" role="button" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown" aria-label="Open user menu">
						<span class="avatar avatar-sm" style="background-image: url(http://ocsd.seh.ox.ac.uk//photos/UAS_UniversityCard-10076320.jpg)"></span>
						<div class="d-none d-xl-block pl-2">
							<div><?php echo $_SESSION['username']; ?></div>
							<div class="mt-1 small text-muted"><?php echo $_SESSION['username']; ?></div>
						</div>
					</a>


					<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
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
		</div>
	</header>

	<div class="navbar-expand-md">
		<div class="collapse navbar-collapse" id="navbar-menu">
			<div class="navbar navbar-light">
				<div class="container-xl">
					<ul class="navbar-nav">
						<li class="nav-item active">
							<a class="nav-link" href="index.php?n=member">
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
								</span>
								<span class="nav-link-title">
									Your Profile
								</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="index.php">
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 7a2 2 0 0 1 2 2v12l-5 -3l-5 3v-12a2 2 0 0 1 2 -2h6z" /><path d="M9.265 4a2 2 0 0 1 1.735 -1h6a2 2 0 0 1 2 2v12l-1 -.6" /></svg>
								</span>
								<span class="nav-link-title">Your Meals</span>
								<span class="badge bg-red">2</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="index.php?n=information">
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><line x1="3" y1="6" x2="3" y2="19" /><line x1="12" y1="6" x2="12" y2="19" /><line x1="21" y1="6" x2="21" y2="19" /></svg>
								</span>
								<span class="nav-link-title">SCR Information</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled" href="./#">
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path></svg>
								</span>
								<span class="nav-link-title">Disabled</span>
							</a>
						</li>
					</ul>
					<div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
						<form action="." method="get">
							<div class="input-icon">
								<span class="input-icon-addon">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="10" cy="10" r="7"></circle><line x1="21" y1="21" x2="15" y2="15"></line></svg>
								</span>
								<input type="text" class="form-control" placeholder="Search…" aria-label="Search in website">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
