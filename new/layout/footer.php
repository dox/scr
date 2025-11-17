<div class="container">
	<footer class="pt-4 my-md-5 pt-md-5 border-top">
		<div class="row">
			<div class="col-12 col-md-6">
				<svg width="1.3em" height="1.3em" aria-hidden="true">
					<use xlink:href="assets/images/icons.svg#chough"/>
				</svg> <a class="link-secondary" href="https://github.com/dox/scr"><?php echo APP_NAME; ?></a> developed by <a href="https://github.com/dox" class="link-secondary">Andrew Breakspear</a>
			  <small class="d-block mb-3 text-muted">&copy; 2008-<?php echo date('Y'); ?></small>
			</div>
			<?php
			if ($user->isLoggedIn()) {
				echo "<div class=\"col-12 col-md-6 text-end d-print-none\">";
				echo "<a class=\"link-secondary\" href=\"index.php?page=information&subtype=accessibility\">Accessibility Statement</a>";
				echo "</div>";
			}
			?>
		</div>
	</footer>
</div>