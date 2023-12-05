<?php
include_once("inc/autoload.php");


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_once("views/html_head.php"); ?>
</head>

<body class="bg-body-tertiary">
		<?php
		include_once("views/header.php");
		?>

    <div class="container">
      <?php
  		$node = "nodes/index.php";
  			if (isset($_GET['n'])) {
  				$node = "nodes/" . $_GET['n'] . ".php";

  				if (!file_exists($node)) {
  					$node = "nodes/404.php";
  				}
  			}
  		include_once($node);
      ?>
    </div>
    <?php
		include_once("views/footer.php");
		?>
</body>
</html>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://help.seh.ox.ac.uk/assets/chat/chat.min.js"></script>
<script>
$(function() {
  new ZammadChat({
    title: 'Need IT Support?',
    background: '#6b7889',
    fontSize: '12px',
    chatId: 1
  });
});
</script>