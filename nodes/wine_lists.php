<?php
pageAccessCheck("wine");

$title = "Wine Lists";
$subtitle = "Add/edit/delete/share your lists";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#newListModal\"");

echo makeTitle($title, $subtitle, $icons, true);

$wineClass = new wineClass();

// check if we need to create a new bin
if (isset($_POST['name'])) {
	$newList = new wine_list();
	$newList->name = $_POST['name'];
	$newList->type = $_POST['type'];
	$newList->notes = $_POST['notes'];
	$newList->member_ldap = $_SESSION['username'];
	$newList->last_updated = date('c');
	
	$newList->create();
}
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Lists</li>
	</ol>
</nav>

<hr class="pb-3" />

<h3>My Lists</h3>
  <ul class="list-group list-group-flush">
	  <?php
	  $myListsFilter[] = array("field" => "type", "operator" => "=", "value" => "private");
	  $myListsFilter[] = array("field" => "member_ldap", "operator" => "=", "value" => $_SESSION['username']);
	  foreach ($wineClass->allLists($myListsFilter) AS $list) {
		  $list = new wine_list($list['uid']);
		  
		  echo $list->liItem($wine->uid);
	  }
	  ?>
  </ul>

<h3 class="pt-3">Public Lists</h3>
  <ul class="list-group list-group-flush">
	  <?php
	  $publicListsFilter[] = array("field" => "type", "operator" => "=", "value" => "public");
	  foreach ($wineClass->allLists($publicListsFilter) AS $list) {
		  $list = new wine_list($list['uid']);
		  
		  echo $list->liItem($wine->uid);
	  }
	  ?>
  </ul>

<form method="post" id="bin_new" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="modal fade" id="newListModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Cellar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-3">
						<div class="mb-3">
							<label for="type" class="form-label">Type</label>
							<select class="form-select" id="type" name="type" required>
								<option value="private">private</option>
								<option value="public">public</option>
							</select>
						</div>
					</div>
					<div class="col-9">
						<div class="mb-3">
							<label for="name" class="form-label">List Name</label>
							<input type="text" class="form-control" id="name" name="name">
						</div>
					</div>
					<div class="col">
						<div class="mb-3">
							<label for="notes" class="form-label">Notes</label>
							<textarea class="form-control" id="notes" name="notes"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" name="submit" class="btn btn-primary">Add List</button>
			</div>
			
		</div>
	</div>
</div>
</form>