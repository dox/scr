<?php
$logArray['category'] = "view";
$logArray['result'] = "danger";
$logArray['description'] = htmlspecialchars($_SERVER['REQUEST_URI']) . " viewed but didn't exist";
$logsClass->create($logArray);
?>

<h1 class="display-1 text-center"><strong>404</strong> Page not found</h1>
<p class="text-center">The page you are looking for <code>'<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>'</code> isn't available.</p>
<p class="text-center">Please report this to <a href="mailto:<?php echo support_email;?>"><?php echo support_email;?></a>.
