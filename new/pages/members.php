<?php
$user->pageCheck('members');

$memberTypes = explode(',', $settings->get('member_types'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['deleteMemberUID'])) {
		$deleteMemberUID = filter_input(INPUT_POST, 'deleteMemberUID', FILTER_SANITIZE_NUMBER_INT);
		
		$member = Member::fromUID($deleteMemberUID);
		$member->delete();
	} else {
		$newMember = new Members();
		$newMember->create($_POST);
	}
}

foreach ($memberTypes as $type) {
	$members[$type] = Members::getAllByType($type);
}

$first = true; // to mark the first tab as active

echo pageTitle(
	"Members",
	"Members, and their order of precedence",
	[
		[
			'permission' => 'settings',
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
		  <input type="text" id="filterInput" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
		</div>
	  </div>
		 
		<?php if (!empty($contents)): ?>
			<ul class="list-group mt-2" id="<?= $type ?>-sortable">
				<?php foreach ($contents as $member): ?>
					<li class="list-group-item">
						<i class="bi bi-grip-vertical"></i>
						<a href="index.php?page=member&ldap=<?= $member->ldap ?>"><?= $member->name() ?></a>
						<span class="text-muted">@<?= strtoupper($member->ldap) ?></span>


						<span class="float-end"><?= $member->stewardBadge() ?>
						<span class="text-muted"><?= $member->category ?></span>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<p class="text-muted mt-2">No members in this category.</p>
		<?php endif; ?>
	</div>
	
	<script>
	document.querySelector('#filterInput')
	  .addEventListener('input', () => filterList('#filterInput', '#<?= $type ?>-sortable'));
	</script>
<?php
	$first = false;
endforeach;
?>
</div>










<!-- Add Member Modal -->
<div class="modal fade" tabindex="-1" id="addMemberModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Member</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
					<div class="mb-3">
						<label for="title" class="form-label">Title</label>
						<select class="form-select" name="title" id="title" required>
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
					
					<div class="mb-3">
						<label for="firstname" class="form-label">First name</label>
						<input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" required>
						<div class="invalid-feedback">
							Valid first name is required.
						</div>
					</div>
					
					<div class="mb-3">
						<label for="firstname" class="form-label">Last name</label>
						<input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name"  required>
						<div class="invalid-feedback">
							Valid last name is required.
						</div>
					</div>
					
					<div class="mb-3">
						<label for="ldap" class="form-label">LDAP Username</label>
						<div class="input-group">
							<span class="input-group-text">@</span>
							<input
								type="text"
								class="form-control"
								name="ldap"
								id="ldap"
								placeholder="LDAP Username"
								required
							>
							<div class="invalid-feedback">
								Valid LDAP username is required.
							</div>
						</div>
					</div>
					
					<div class="mb-3">
						<label for="title" class="form-label">Member Category</label>
						<select class="form-select" name="category" id="category" <?= $user->hasPermission('members') ? '' : 'disabled' ?> required>
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
					
					<div class="accordion mb-3" id="accordionDietary">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> Dietary Information&nbsp;<i>(Maximum: <?php echo $settings->get('meal_dietary_allowed'); ?>)</i></button>
							</h2>
							<div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
								<div class="accordion-body">
									<?php
									$dietaryOptions    = array_map('trim', explode(',', $settings->get('meal_dietary')));
									$dietaryOptionsMax = (int) $settings->get('meal_dietary_allowed');
									
									$output = '';
									
									foreach ($dietaryOptions as $index => $dietaryOption) {
										$safeValue  = htmlspecialchars($dietaryOption, ENT_QUOTES);
										$checkboxId = "dietary_{$index}";
									
										$output .= '<div class="form-check">';
										$output .= '<input class="form-check-input dietaryOptionsMax" '
												 . 'type="checkbox" '
												 . 'onclick="checkMaxCheckboxes(' . $dietaryOptionsMax . ')" '
												 . 'name="dietary[]" '
												 . 'id="' . $checkboxId . '" '
												 . 'value="' . $safeValue . '"' 
												 . '>';
										$output .= '<label class="form-check-label" for="' . $checkboxId . '">' 
												 . $safeValue 
												 . '</label>';
										$output .= '</div>';
									}
									
									echo $output;
									?>
									
									<small id="nameHelp" class="form-text text-muted"><?php echo $settings->get('meal_dietary_message'); ?></small>
								</div>
							</div>
						</div>
					</div>
					
					<div class="mb-3">
						<label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Email address">
					</div>
					
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
					
					<div class="modal-footer">
						<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Add Member</button>
						<input type="hidden" name="precedence" value="999">
						<input type="hidden" name="opt_in" value="1">
						<input type="hidden" name="calendar_hash" value="<?= bin2hex(random_bytes(8)) ?>">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
