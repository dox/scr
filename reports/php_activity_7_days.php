<div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
  <div class="col-md-5 p-lg-5 mx-auto my-5">
    <h1 class="display-4 fw-normal">Site Activity</h1>
    <p class="lead fw-normal">Activity over the last 30 days</p>
    <a class="btn btn-outline-secondary" href="#">Coming soon</a>
  </div>
  <div class="product-device shadow-sm d-none d-md-block"></div>
  <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>
</div>

<div class="row mb-3">
  <div class="col-6 bg-primary text-center overflow-hidden">
    <div class="my-3 py-3">
      <h2 class="display-5">Access</h2>
      <p class="lead">Users that have accessed the system</p>
    </div>
    <div class="bg-dark shadow-sm mx-auto text-light" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
    å</div>
  </div>
  <div class="col-6 text-center overflow-hidden">
    <div class="my-3 py-3">
      <h2 class="display-5">Access</h2>
      <p class="lead">Users that have accessed the system</p>
    </div>
    <div class="bg-primary shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
  </div>
</div>

<div class="row mb-3">
  <div class="col-6 bg-primary text-center overflow-hidden">
    <div class="my-3 py-3">
      <h2 class="display-5">Access</h2>
      <p class="lead">Users that have accessed the system</p>
    </div>
    <div class="bg-dark shadow-sm mx-auto text-light" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
      <?php
      $sql  = "SELECT * FROM members WHERE date_lastlogon > '2020-02-01' ORDER BY date_lastlogon DESC";
      $members = $db->query($sql)->fetchAll();

      $output = "<ul class=\"list-unstyled\">";
      foreach ($members AS $member) {
        $output .= "<li>[" . $member['type'] . "] " . $member['lastname'] . ", ". $member['firstname'] . "</li>";
      }
      $output .= "</ul>";

      echo $output;
      ?>
    </div>
  </div>
  <div class="col-6 text-center overflow-hidden">
    <div class="my-3 py-3">
      <h2 class="display-5">Access</h2>
      <p class="lead">Users that have accessed the system</p>
    </div>
    <div class="bg-primary shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
  </div>
</div>

<?php
$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run";
$logsClass->create($logArray);
?>
