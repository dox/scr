<header>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<div class="container">
	  <a class="navbar-brand" href="index.php">
			<svg width="28" height="24">
				<use xlink:href="img/icons.svg#chough-regular"/>
			</svg> SCR Meal Booking</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarLinkCollapse" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarLinkCollapse">
	    <ul class="navbar-nav me-auto mb-2 mb-md-0">
	      <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">
					<svg width="16" height="16" class="text-muted">
						<use xlink:href="img/icons.svg#house-door"/>
					</svg> Home</a>
	      </li>
	      <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?n=member">
					<svg width="16" height="16" class="text-muted">
						<use xlink:href="img/icons.svg#person"/>
					</svg> Your Profile</a>
	      </li>
	      <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?n=member">
					<svg width="16" height="16" class="text-muted">
						<use xlink:href="img/icons.svg#list-stars"/>
					</svg> Your Meals</a>
	      </li>
	      <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?n=information">
					<svg width="16" height="16" class="text-muted">
						<use xlink:href="img/icons.svg#journal-text"/>
					</svg> SCR Information</a>
	      </li>
	    </ul>
	    <div class="d-flex">
	      <ul class="navbar-nav mr-auto">
	        <li class="nav-item dropdown">
	          <a class="nav-link dropdown-toggle" href="#" id="navbarAvatarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	            <?php echo $_SESSION['username']; ?>
	            <!--<div class="small text-muted"><?php echo $_SESSION['username']; ?></div>-->
	          </a>
	          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
	            <?php if($_SESSION['admin'] == true) { ?><a class="dropdown-item" href="index.php?n=admin_impersonate">Impersonate</a><?php } ?>
	            <a class="dropdown-item" href="index.php?n=admin_members">Members</a>
	            <a class="dropdown-item" href="index.php?n=admin_meals">Meals</a>
	            <a class="dropdown-item" href="index.php?n=admin_terms">Terms</a>
	            <a class="dropdown-item" href="index.php?n=admin_logs">Logs</a>
	            <a class="dropdown-item" href="index.php?n=admin_settings">Site Settings</a>
	            <div class="dropdown-divider"></div>
	            <a class="dropdown-item" href="logon.php?logout=true">Logout</a>
	          </div>
	        </li>
	      </ul>
	    </div>
	  </div>

	</div>
	</nav>
</header>
