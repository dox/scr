<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?php
$user->pageCheck('members');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Handle member deletion
	$deleteMemberUID = filter_input(INPUT_POST, 'deleteMemberUID', FILTER_SANITIZE_NUMBER_INT);
	if ($deleteMemberUID) {
		$member = Member::fromUID($deleteMemberUID);
		if ($member) {
			$member->delete();
		}
	}

	// Handle reordering
	if (!empty($_POST['order']) && is_array($_POST['order'])) {
		foreach ($_POST['order'] as $type => $orderString) {
			$uidArray = array_map('trim', explode(',', $orderString));
			foreach ($uidArray as $position => $uid) {
				$member = Member::fromUID($uid);
				if ($member) {
					$member->updatePosition($position + 1);
				}
			}
		}
		$log->add("Member Order Updated", 'member', Log::SUCCESS);
		toast('Member Order Updated', 'Member precedence order updated sucesfully', 'text-success');
	}

	// Handle new member creation
	$ldap = filter_input(INPUT_POST, 'ldap', FILTER_UNSAFE_RAW);
	if ($ldap) {
		$newMember = new Members();
		$newMember->create($_POST);
	}
}

$memberTypes = explode(',', $settings->get('member_types'));
$membersClass = new Members();
foreach ($memberTypes as $type) {
	$members[$type] = $membersClass->all([
		'type' => ['=', $type]
	]);
}

$first = true; // to mark the first tab as active

echo pageTitle(
	"Members",
	"Members, and their order of precedence",
	[
		[
			'permission' => 'members',
			'title' => 'Add new',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addMemberModal'
			]
		]
	]
);
?>

<ul class="nav nav-tabs nav-fill mb-3" id="scrUserList" role="tablist">
<?php foreach ($members as $type => $contents): ?>
	<li class="nav-item" role="presentation">
		<a class="nav-link <?= $first ? 'active' : '' ?>" 
		   id="<?= htmlspecialchars($type) ?>-tab" 
		   data-bs-toggle="tab" 
		   href="#<?= htmlspecialchars($type) ?>" 
		   role="tab" 
		   aria-controls="<?= htmlspecialchars($type) ?>" 
		   aria-selected="<?= $first ? 'true' : 'false' ?>">
			<?= htmlspecialchars($type) ?> (<?= count($contents) ?>)
		</a>
	</li>
<?php 
	$first = false;
endforeach; ?>
</ul>

<div class="tab-content" id="scrUserListContent">
<?php
$first = true; // reset for tab content
foreach ($members as $type => $contents):
	?>
	<div class="tab-pane fade <?= $first ? 'show active' : '' ?>" 
		 id="<?= htmlspecialchars($type) ?>" 
		 role="tabpanel" 
		 aria-labelledby="<?= htmlspecialchars($type) ?>-tab">
	
	<div class="row mb-3">
		<div class="col">
		  <input 
			type="text" 
			class="form-control form-control-lg filter-input"
			data-target="#<?= htmlspecialchars($type) ?>_sortable"
			placeholder="Quick search"
			autocomplete="off"
			spellcheck="false">
		</div>
	  </div>
		 
		<?php if (!empty($contents)): ?>
			<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" id="memberForm">
			<ul class="list-group mt-2 sortable-list" id="<?= htmlspecialchars($type) ?>_sortable">
				<?php foreach ($contents as $member): ?>
					<li class="list-group-item <?= $member->enabled ? '' : ' list-group-item-secondary' ?>" data-id="<?= $member->uid ?>">
						<i class="bi bi-grip-vertical"></i>
						<a href="index.php?page=member&ldap=<?= htmlspecialchars($member->ldap) ?>"><?= $member->name() ?></a>
						<span class="text-muted">@<?= strtoupper($member->ldap) ?></span>
						<span class="float-end"><?= $member->stewardBadge() ?>
						<span class="text-muted"><?= $member->category ?></span>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
			
			<!-- Hidden input to store order -->
			<input type="hidden" 
			   name="order[<?= htmlspecialchars($type) ?>]" 
			   id="<?= htmlspecialchars($type) ?>_order">
			</form>
		<?php else: ?>
			<p class="text-muted mt-2">No members in this category.</p>
		<?php endif; ?>
	</div>
	
	
	
<?php
	$first = false;
endforeach;
?>

<button 
  id="globalSaveOrderBtn"
  class="btn btn-primary shadow-lg d-none"
  style="position: fixed; bottom: 40px; right: 50%; z-index: 1050;">
  Save Order
</button>
</div>


<!-- Add Member Modal -->
<div class="modal fade" tabindex="-1" id="addMemberModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<form id="createUserForm" method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" class="modal-content needs-validation" novalidate>

			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Member</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<div class="modal-body">
					<!-- Title -->
					<div class="mb-3">
						<label for="title" class="form-label">Title</label>
						<select class="form-select" name="title" id="title" required>
							<option value=""></option>
							<?php
							$memberTitles = explode(',', $settings->get('member_titles'));
							foreach ($memberTitles as $title) {
								$title = trim($title);
								echo "<option value=\"{$title}\">{$title}</option>";
							}
							?>
						</select>
						<div class="invalid-feedback">
							Title is required.
						</div>
					</div>

					<!-- First name -->
					<div class="mb-3">
						<label for="firstname" class="form-label">First name</label>
						<input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" required>
						<div class="invalid-feedback">
							Valid first name is required.
						</div>
					</div>

					<!-- Last name -->
					<div class="mb-3">
						<label for="lastname" class="form-label">Last name</label>
						<input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" required>
						<div class="invalid-feedback">
							Valid last name is required.
						</div>
					</div>

					<!-- LDAP Username -->
					<div class="mb-3">
						<label for="ldap" class="form-label">LDAP Username</label>
						<div class="input-group">
							<span class="input-group-text">@</span>
							<input type="text" class="form-control" name="ldap" id="ldap" placeholder="LDAP Username" required>
							<div class="valid-feedback">
								Username is available.
							</div>
							<div class="invalid-feedback">
								Username is required or already exists.
							</div>
						</div>
					</div>

					<!-- Member Category -->
					<div class="mb-3">
						<label for="category" class="form-label">Member Category</label>
						<select class="form-select" name="category" id="category" <?= $user->hasPermission('members') ? '' : 'disabled' ?> required>
							<option value=""></option>
							<?php
							$memberCategories = explode(',', $settings->get('member_categories'));
							foreach ($memberCategories as $category) {
								$category = trim($category);
								echo "<option value=\"{$category}\">{$category}</option>";
							}
							?>
						</select>
						<div class="invalid-feedback">
							Valid category is required.
						</div>
					</div>

					<!-- Dietary info (optional) -->
					<div class="accordion mb-3" id="accordionDietary">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									Dietary Information&nbsp;<i>(Maximum: <?= $settings->get('meal_dietary_allowed'); ?>)</i>
								</button>
							</h2>
							<div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionDietary">
								<div class="accordion-body">
									<?php
									$dietaryOptions = array_map('trim', explode(',', $settings->get('meal_dietary')));
									$dietaryOptionsMax = (int)$settings->get('meal_dietary_allowed');

									foreach ($dietaryOptions as $index => $dietaryOption) {
										$safeValue = htmlspecialchars($dietaryOption, ENT_QUOTES);
										$checkboxId = "dietary_{$index}";
										echo '<div class="form-check">';
										echo '<input class="form-check-input dietaryOptionsMax" type="checkbox" onclick="checkMaxCheckboxes(' . $dietaryOptionsMax . ')" name="dietary[]" id="' . $checkboxId . '" value="' . $safeValue . '">';
										echo '<label class="form-check-label" for="' . $checkboxId . '">' . $safeValue . '</label>';
										echo '</div>';
									}
									?>
									<small class="form-text text-muted"><?= $settings->get('meal_dietary_message'); ?></small>
								</div>
							</div>
						</div>
					</div>

					<!-- Email -->
					<div class="mb-3">
						<label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Email address">
					</div>

					<!-- Member Type -->
					<div class="mb-3">
						<label for="type" class="form-label">Member Type</label>
						<select class="form-select" name="type" id="type" <?= $user->hasPermission('members') ? '' : 'disabled' ?> required>
							<?php
							$memberTypes = explode(',', $settings->get('member_types'));
							foreach ($memberTypes as $type) {
								$type = trim($type);
								echo "<option value=\"{$type}\">{$type}</option>";
							}
							?>
						</select>
						<div class="invalid-feedback">
							Valid member type is required.
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Add Member</button>
					<input type="hidden" name="precedence" value="999">
					<input type="hidden" name="opt_in" value="1">
					<input type="hidden" name="calendar_hash" value="<?= bin2hex(random_bytes(8)) ?>">
				</div>
			</div>
		</form>
	</div>
</div>

<script>
// filter list for members
document.querySelectorAll('.filter-input').forEach(input => {
  input.addEventListener('input', function () {
	const list = document.querySelector(this.dataset.target);
	const filter = this.value.toLowerCase();

	list.querySelectorAll('li').forEach(li => {
	  li.style.display = li.textContent.toLowerCase().includes(filter)
		? ''
		: 'none';
	});
  });
});

// draggable list for members
document.addEventListener('DOMContentLoaded', function () {
  const saveBtn = document.getElementById('globalSaveOrderBtn');
  let orderChanged = false;

  document.querySelectorAll('.sortable-list').forEach(list => {
	const type = list.id.replace('_sortable', '');
	const orderInput = document.getElementById(type + '_order');

	const sortable = Sortable.create(list, {
	  animation: 150,
	  onEnd: function () {
		const ids = sortable.toArray();
		orderInput.value = ids.join(',');

		if (!orderChanged) {
		  orderChanged = true;
		  saveBtn.classList.remove('d-none');
		}
	  }
	});

	orderInput.value = sortable.toArray().join(',');

  });

  saveBtn.addEventListener('click', function () {
	const activeForm = document.querySelector('.tab-pane.active form');
	if (activeForm) activeForm.submit();
  });

});




document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('createUserForm');
  const ldapInput = form.querySelector('input[name="ldap"]');
  const validFeedback = ldapInput.parentElement.querySelector('.valid-feedback');
  const invalidFeedback = ldapInput.parentElement.querySelector('.invalid-feedback');
  let debounceTimer = null;
  let lastCheckValid = false;
  let ajaxPending = false;

  // Async username validation
  async function checkUsername() {
	const value = ldapInput.value.trim();

	// Empty input
	if (!value) {
	  ldapInput.classList.remove('is-valid', 'is-invalid');
	  lastCheckValid = false;
	  if (invalidFeedback) invalidFeedback.textContent = 'Username is required';
	  if (validFeedback) validFeedback.textContent = '';
	  return false;
	}

	ajaxPending = true;

	try {
	  const res = await fetch(`./ajax/member_check_username.php?ldap=${encodeURIComponent(value)}`);
	  const data = await res.json();

	  if (data.valid) {
		ldapInput.classList.add('is-valid');
		ldapInput.classList.remove('is-invalid');
		lastCheckValid = true;
		if (validFeedback && data.message) validFeedback.textContent = data.message;
		if (invalidFeedback) invalidFeedback.textContent = '';
	  } else {
		ldapInput.classList.add('is-invalid');
		ldapInput.classList.remove('is-valid');
		lastCheckValid = false;
		if (invalidFeedback && data.message) invalidFeedback.textContent = data.message;
		if (validFeedback) validFeedback.textContent = '';
	  }

	  return lastCheckValid;

	} catch {
	  ldapInput.classList.remove('is-valid', 'is-invalid');
	  lastCheckValid = false;
	  if (validFeedback) validFeedback.textContent = '';
	  if (invalidFeedback) invalidFeedback.textContent = 'Validation failed';
	  return false;
	} finally {
	  ajaxPending = false;
	}
  }

  // Debounce input
  ldapInput.addEventListener('input', () => {
	clearTimeout(debounceTimer);
	debounceTimer = setTimeout(checkUsername, 300);
  });

  // Form submission
  form.addEventListener('submit', async (e) => {
	e.preventDefault();
	e.stopPropagation();

	// Ensure username check runs before submit
	if (!ldapInput.classList.contains('is-valid') && !ldapInput.classList.contains('is-invalid')) {
	  await checkUsername();
	}

	if (!form.checkValidity() || !lastCheckValid || ajaxPending) {
	  form.classList.add('was-validated');
	  return;
	}

	form.submit(); // all valid, submit
  });
});

</script>