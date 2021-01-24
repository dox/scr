<?php


if (count($notifications) > 0) {
  $output  = "<nav class=\"navbar navbar-expand-lg navbar-light\" style=\"background-color: #e3f2fd;\">";

  foreach ($notifications AS $notification) {
    $notificationIcon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#chat-dots\"/></svg>";

    $output .= "<div class=\"container text-center\">";
    $output .= "<div class=\"\" id=\"test\">";
    $output .= $notificationIcon . " " . $notification['message'];
    $output .= "</div>";
    $output .= "</div>";
  }
  $output .= "</nav>";

  //echo $output;
}

if (count($notifications) > 0) {
  $output  = "<div class=\"container\">";

  foreach ($notifications AS $notification) {
    $output .= $notificationsClass->display($notification);
  }

  $output .= "</div>";
  echo $output;



}
?>

<script>
document.querySelectorAll('.notificationAlert').forEach(item => {
  item.addEventListener('closed.bs.alert', event => {
    var notificationUID = item.id;

    var formData = new FormData();

    formData.append("notificationUID", notificationUID);

    var request = new XMLHttpRequest();
    request.open("POST", "../actions/notification_dismiss.php", true);
    request.send(formData);

    // 4. This will be called after the response is received
    request.onload = function() {
      if (request.status != 200) { // analyze HTTP status of the response
        alert("Something went wrong.  Please refresh this page and try again.");
        //alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
      } else {
        //alert(`${request.status}: ${request.statusText}`); // e.g. 404: Not Found
      }
    };

    request.onerror = function() {
      alert("Request failed");
    };

    return false;
  })
})


</script>
