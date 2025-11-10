<?php
declare(strict_types=1);
session_start();

/** @var PDO $db */
/** @var members $membersClass */

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

/**
 * Reassign bookings from one LDAP to another.
 */
function reassignBookings(string $from, string $to): void {
     global $db;
     $db->query(
         "UPDATE bookings SET member_ldap = ? WHERE member_ldap = ?",
         $to,
         $from
     );
 }

/**
 * Retrieve orphaned bookings (bookings with no corresponding member record).
 *
 * @return array<int, array{bookingsCount:int, member_ldap:string}>
 */
function getOrphanedBookings(): array {
  global $db;
  
    $stmt = $db->query(
        'SELECT COUNT(*) AS bookingsCount, member_ldap
         FROM bookings
         WHERE member_ldap NOT IN (SELECT ldap FROM members)
         GROUP BY member_ldap
         ORDER BY bookingsCount DESC'
    )->fetchAll();
    return $stmt;
}

// Handle POST reassignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignFrom = $_POST['assignFrom'] ?? '';
    $assignTo   = $_POST['assignTo'] ?? '';
    $csrf       = $_POST['csrf'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], (string)$csrf)) {
        exit('Invalid CSRF token.');
    }

    if ($assignFrom !== '' && $assignTo !== '') {
        reassignBookings(trim($assignFrom), trim($assignTo));
        // Optionally redirect back to clear POST and refresh
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    exit('Invalid parameters.');
}

// Instantiate members class and collect data
$membersClass ??= new members();
$members = $membersClass->getMembers();
$orphanedBookings = getOrphanedBookings();

$totalOrphanedBookings = array_sum(array_column($orphanedBookings, 'bookingsCount'));
$totalOrphanedMembers = count($orphanedBookings);
$csrfToken = $_SESSION['csrf_token'];
?>


<datalist id="members">
  <?php foreach ($members as $member): ?>
    <?php
    $content = htmlspecialchars("{$member->firstname} {$member->lastname} ({$member->ldap})", ENT_QUOTES, 'UTF-8');
    $ldap = htmlspecialchars($member->ldap, ENT_QUOTES, 'UTF-8');
    ?>
    <option data-value="<?= $ldap ?>" value="<?= $content ?>"></option>
  <?php endforeach; ?>
</datalist>

<div class="p-5 mb-4 bg-body-tertiary rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Orphaned Bookings</h1>
    <p class="col-md-8 fs-4">
      <?= number_format($totalOrphanedBookings) ?> bookings belonging to
      <?= number_format($totalOrphanedMembers) ?> unknown members
    </p>
  </div>
</div>

<div class="container mb-5">
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">Bookings</th>
        <th scope="col">Booking LDAP</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orphanedBookings as $booking): ?>
        <?php
        $ldap = htmlspecialchars($booking['member_ldap'], ENT_QUOTES, 'UTF-8');
        $count = (int)$booking['bookingsCount'];
        ?>
        <tr>
          <td><?= $count ?></td>
          <td><kbd><?= $ldap ?></kbd></td>
          <td>
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Member Name"
                     list="members" id="<?= $ldap ?>-browser" autocomplete="off">
              <button class="btn btn-outline-secondary" type="button"
                      data-member="<?= $ldap ?>" onclick="reassign(this)">
                Reassign
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function reassign(button) {
  const assignFrom = button.dataset.member;
  const input = document.getElementById(`${assignFrom}-browser`);
  const shownVal = input.value;
  const option = document.querySelector(`#members option[value="${shownVal}"]`);

  if (!option) {
    alert("Please select a valid member from the list.");
    return;
  }

  const assignTo = option.dataset.value;

  // Create a plain form and submit it
  const form = document.createElement("form");
  form.method = "POST";
  form.action = window.location.href;

  const addField = (name, value) => {
    const field = document.createElement("input");
    field.type = "hidden";
    field.name = name;
    field.value = value;
    form.appendChild(field);
  };

  addField("assignFrom", assignFrom);
  addField("assignTo", assignTo);
  addField("csrf", "<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>");

  document.body.appendChild(form);
  form.submit();
}
</script>