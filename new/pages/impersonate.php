<?php
if (isset($_SESSION['impersonation_backup'])) {
	
} else {
	$user->pageCheck('impersonate');
}

echo pageTitle(
	"Impersonate",
	"Assume the identity of an SCR Member for the purposes of managing their meal bookings"
);


?>

<div class="col-4 mx-auto">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	
		<input 
			type="text"
			id="impersonate-input"
			name="impersonate"
			class="form-control mb-3"
			list="members-list"
			placeholder="Search Members"
			aria-label="Search"
			required
			<?= isset($_SESSION['impersonation_backup']) ? 'disabled' : '' ?>
		>
	
		<?php if (!isset($_SESSION['impersonation_backup'])): ?>
	
			<div class="form-check mb-3">
				<input 
					type="checkbox"
					class="form-check-input"
					id="maintainAdminAccess"
					name="maintainAdminAccess"
					value="1"
					checked
				>
				<label class="form-check-label" for="maintainAdminAccess">
					Maintain Current Access Level*
				</label>
			</div>
	
			<button 
				type="submit"
				id="impersonate_submit_button"
				name="impersonate_submit_button"
				class="btn btn-primary w-100 mb-3"
			>
				Impersonate
			</button>
	
		<?php else: ?>
	
			<button 
				type="submit"
				id="restore_impersonation"
				name="restore_impersonation"
				class="btn btn-info w-100 mb-3"
			>
				Stop Impersonating
			</button>
	
		<?php endif; ?>
	
		<p class="small">
			†<span class="text-muted">
				By default, when you impersonate another member, you retain your access.  
				Uncheck to walk in another’s shoes, permissions and all.
			</span>
		</p>
	
	</form>
</div>





<datalist id="members-list">
<?php
$members = new Members();

foreach ($members->getAllByType('SCR') as $member) {
	if ($user->getUsername() != $member->ldap) {
		echo "
		<option
			value=\"" . htmlspecialchars($member->uid) . "\"
			label=\"" . htmlspecialchars($member->name()) . "\"
		></option>";
	}
}
?>
</datalist>