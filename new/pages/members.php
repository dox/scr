<?php
$user->pageCheck('members');

$memberTypes = explode(',', $settings->get('member_types'));

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
						<a href="index.php?page=member&ldap=<?= $member->ldap ?>"><?= htmlspecialchars($member->name()) ?></a>
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
				<h5 class="modal-title">Test Modal <span class="text-danger"><strong>WARNING!</strong></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				Test Modal
				<p><span class="text-danger"><strong>WARNING!</strong> Are you sure you want to delete this member?</p>
				<p>This will also delete <strong>all</strong> bookings (past and present) for this member.<p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
			</div>
		</div>
	</div>
</div>
