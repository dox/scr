<div class="container">
<footer class="pt-4 my-md-5 pt-md-5 border-top">
  <div class="row">
    <div class="col-6">
      <svg width="1em" height="1em" class="text-muted">
				<use xlink:href="img/icons.svg#chough"/>
			</svg>
      <a class="link-secondary" href="https://github.com/dox/scr"><?php echo site_name; ?></a> developed by <a href="https://github.com/dox" class="link-secondary">Andrew Breakspear</a>
      <small class="d-block mb-3 text-muted">&copy; 2008-<?php echo date('Y'); ?></small>
    </div>
    <div class="col-6 text-end">
      <?php
      if (isLoggedIn()) {
        echo "<ul class=\"list-unstyled text-small\">";
        echo "<li><a class=\"link-secondary\" href=\"index.php?n=accessibility\">Accessibility Statement</a></li>";
        echo "</ul>";
      }
      ?>
    </div>
  </div>
</footer>
</div>
