<header>
	<nav class="navbar navbar-expand-lg bg-body-secondary">
	<div class="container">
	  <a class="navbar-brand" href="index.php">
			<svg width="1.3em" height="1.3em">
				<use xlink:href="img/icons.svg#chough"/>
			</svg> <?php echo site_name; ?></a>
	  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLinkCollapse" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarLinkCollapse">
	    <ul class="navbar-nav me-auto mb-2 mb-md-0">
	      <li class="nav-item d-print-none"><a class="nav-link active" aria-current="page" href="index.php">
					<svg width="1em" height="1em" class="text-muted">
						<use xlink:href="img/icons.svg#house-door"/>
					</svg> Home</a>
	      </li>
	      <li class="nav-item d-print-none"><a class="nav-link" aria-current="page" href="index.php?n=member">
					<svg width="1em" height="1em" class="text-muted">
						<use xlink:href="img/icons.svg#person"/>
					</svg> Your Profile</a>
	      </li>
				<li class="nav-item d-print-none"><a class="nav-link" aria-current="page" href="index.php?n=information">
					<svg width="1em" height="1em" class="text-muted">
						<use xlink:href="img/icons.svg#journal-text"/>
					</svg> SCR Information</a>
	      </li>
				<?php
				foreach (navbar_addon AS $navbarAddOn => $value) {
					$output  = "<li class=\"nav-item d-print-none\">";
					$output .= "<a class=\"nav-link\" aria-current=\"page\" href=\"" . $value['url'] . "\">";
					$output .= $value['icon'];
					$output .= " " . $value['name'];
					$output .= "</a></li>";

					echo $output;
				}
				?>
	    </ul>
			<?php
			if ($_SESSION['impersonating'] == true) {
				$impersonateClass = "";
			} else {
				$impersonateClass = "visually-hidden";
			}
			echo "<div id=\"impersonating_header_button\" class=\"float-end " . $impersonateClass . "\">";
			echo "<a href=\"index.php?n=admin_impersonate\" class=\"btn btn-sm btn-warning\">IMPERSONATING</a>";
			echo "</div>";
			?>
		<div class="d-flex">
			<ul class="navbar-nav me-auto mb-2 mb-md-0">
			  <li class="nav-item dropdown me-2">
				<a class="nav-link dropdown-toggle d-print-none theme-icon-active" id="bd-theme" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					<svg width="1em" height="1em" class="text-muted">
						<use xlink:href="img/icons.svg#dark-mode"/>
					</svg><span class="visually-hidden" id="bd-theme-text">Toggle theme</span></a>
				<ul class="dropdown-menu">
				  <li><button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
						  <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="img/icons.svg#light-mode"></use></svg>
						  Light
						  <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
						</button>
					</li>
					<li>
						<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
							<svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="img/icons.svg#dark-mode"></use></svg>
							Dark
							<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
						  </button>
					</li>
					<li>
						<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
							<svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="img/icons.svg#auto-mode"></use></svg>
							Auto
							<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
						  </button>
					</li>
				</ul>
			  </li>
			</ul>
			  
		</div>
	    <div class="d-flex">
	      <ul class="navbar-nav mr-auto">
	        <li class="nav-item dropdown">
	          <a class="nav-link dropdown-toggle" href="#" id="navbarAvatarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php
							echo $_SESSION['username'];
							?>
	          </a>
	          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
	            <?php
				if(checkpoint_charlie("impersonate")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_impersonate\">Impersonate</a>";
				}
				
				if(checkpoint_charlie("members")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_members\">Members</a>";
				}
				
				if(checkpoint_charlie("meals")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_meals\">Meals</a>";
				}
				
				// needs allowing for checkpoint_charlie("wine") when ready
				if($_SESSION["username"] == "BREAKSPEAR") {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=wine_index\">Wine</a>";
				}
				
				if(checkpoint_charlie("terms")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_terms\">Terms</a>";
				}
				
				if(checkpoint_charlie("notifications")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_notifications\">Notifications</a>";
				}
				
				if(checkpoint_charlie("reports")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_reports\">Reports</a>";
				}
				
				if(checkpoint_charlie("settings")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_settings\">Settings</a>";
				}
				
				if(checkpoint_charlie("logs")) {
					echo "<a class=\"dropdown-item\" href=\"index.php?n=admin_logs\">Logs</a>";
				}
				?>
	            <div class="dropdown-divider"></div>
	            <a class="dropdown-item" href="logon.php?logout=true">Logout</a>
	          </div>
	        </li>
	      </ul>
	    </div>
	  </div>

	</div>
	</nav>
	<?php
	if (debug) {
		$output  = "<nav class=\"navbar navbar-expand-lg bg-warning\">";
		$output .= "<div class=\"container\">";
		$output .= "<div class=\"navbar-text\">";
		$output .= "<strong>Warning!</strong> This site is in <code>debug mode</code>.  It is for testing purposes only!";
		$output .= "</div>";
		$output .= "<span class=\"float-end\">IP: " . $_SERVER['REMOTE_ADDR'] . "</span>";
		$output .= "</div>";
		$output .= "</nav>";

		//echo $output;
	}


	$notificationsClass = new notifications();
	$notifications = $notificationsClass->allCurrentForMember();
	if (count($notifications)) {
		include_once("notifications.php");
	}
	?>


</header>
