<?php
// allow this page if the user is already impersonating, otherwise restrict to role
if (!isset($_SESSION['impersonating'])) {
	$user->pageCheck('impersonate');
}

echo pageTitle(
	"Impersonate",
	"Assume the identity of an SCR Member for the purposes of managing their meal bookings"
);
?>

<div class="col-md-12 col-lg-4 mx-auto">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" id="impersonate-form">
		<input
			type="text"
			id="member-search"
			<?= isset($_SESSION['impersonating']) ? ' value="' . $_SESSION['user']['name'] . '"' : '' ?>
			class="form-control mb-2"
			placeholder="Search Members"
			autocomplete="off"
			required
			<?= isset($_SESSION['impersonating']) ? 'disabled' : '' ?>
		>
		
		<?php if (isset($_SESSION['impersonating'])): ?>
			<button 
				type="submit"
				id="restore_impersonation"
				name="restore_impersonation"
				value="1"
				class="btn btn-info w-100 mb-3"
			>
				Stop Impersonating
			</button>
		<?php else: ?>
			
			
			<ul id="search-results" class="list-group mb-3" style="display:none; position:absolute; z-index:1000;"></ul>
			
			<input type="hidden" id="member-id" name="impersonate">
			
			<div class="form-check mb-3">
				<input type="checkbox" class="form-check-input" id="maintainAdminAccess" name="maintainAdminAccess" value="1" checked>
				<label class="form-check-label" for="maintainAdminAccess">Maintain Current Access Level †</label>
			</div>
			
			<button type="submit" class="btn btn-primary w-100 mb-3">Impersonate</button>
			
		<?php endif; ?>
		
		<p class="small">
			†<span class="text-muted">
				By default, when you impersonate another member, you retain your current level of access. Uncheck this option to see this site with the same permissions as the other member.
			</span>
		</p>
	</form>
	
	
	
	

</div>


<datalist id="members-list">
<?php
$members = new Members();

$membersArray = array();
foreach ($members->getAllByType('SCR') as $member) {
	if ($user->getUsername() != $member->ldap) {
		$membersArray[] = "{ id: " . $member->uid . ",  name: \"" . $member->name() . "\" }";
	}
}
?>
</datalist>

<script>
// Local data source on the page (could be server-generated)
const members = [
	<?php echo implode(",", $membersArray); ?>
];

const input  = document.getElementById("member-search");
const list   = document.getElementById("search-results");
const hidden = document.getElementById("member-id");

input.addEventListener("input", function () {
	const value = this.value.toLowerCase().trim();
	list.innerHTML = "";

	if (!value) {
		list.style.display = "none";
		hidden.value = "";
		return;
	}

	const matches = members.filter(m => m.name.toLowerCase().includes(value));

	if (matches.length === 0) {
		list.style.display = "none";
		return;
	}

	matches.forEach(member => {
		const item = document.createElement("li");
		item.className = "list-group-item list-group-item-action";
		item.textContent = member.name;

		item.addEventListener("click", () => {
			input.value = member.name;
			hidden.value = member.id;
			list.style.display = "none";
		});

		list.appendChild(item);
	});

	list.style.display = "block";
});
</script>
