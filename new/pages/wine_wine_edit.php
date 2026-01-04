<?php
$user->pageCheck('wine');

$wines = new Wines();

// Are we creating a new wine, or editing an existing one?
$isNew = empty($_GET['uid']);

// Load wine
$wineUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$wine    = $isNew ? new Wine() : new Wine($wineUID);

// Load bin
$binUID = $isNew ? filter_input(INPUT_GET, 'bin_uid', FILTER_SANITIZE_NUMBER_INT) : $wine->bin_uid;

$bin = new Bin($binUID);

// Resolve cellar UID: GET takes precedence, bin is the fallback
$cellarUID = filter_input(INPUT_GET, 'cellar_uid', FILTER_SANITIZE_NUMBER_INT) ?? $bin->cellar_uid;

// Load cellar
$cellar = new Cellar($cellarUID);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Save or update
	if ($isNew) {
		$newWine = new Wine();
		$uid = $newWine->createWine($_POST);		// Create new record
		
		//header("Location: index.php?page=wine_wine&uid={$uid}");
		//exit;
	} else {
		$wine->update($_POST);                // Update existing
		if (isset($_FILES['photograph']) && $_FILES['photograph']['error'] === UPLOAD_ERR_OK) {
			$wine->updatePhotograph($_FILES['photograph']);
		}
		$wine = new Wine($wineUID);          // Reload object
	}
}

// Title and action buttons
echo pageTitle(
	$isNew ? "Add New Wine" : $wine->clean_name(),
	$isNew ? "initial import" : "Edit"
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">

		<!-- Wine index -->
		<li class="breadcrumb-item">
			<a href="index.php?page=wine_index">Wine</a>
		</li>

		<!-- Cellar (always present) -->
		<li class="breadcrumb-item">
			<a href="index.php?page=wine_cellar&uid=<?= (int) $cellar->uid ?>">
				<?= htmlspecialchars($cellar->name) ?>
			</a>
		</li>

		<?php if (!$isNew): ?>
			<!-- Existing wine: show bin and wine -->
			<li class="breadcrumb-item">
				<a href="index.php?page=wine_bin&uid=<?= (int) $bin->uid ?>">
					<?= htmlspecialchars($bin->name) ?>
				</a>
			</li>

			<li class="breadcrumb-item">
				<a href="index.php?page=wine_wine&uid=<?= (int) $wine->uid ?>">
					<?= $wine->clean_name() ?>
				</a>
			</li>

			<li class="breadcrumb-item active">Edit Wine</li>

		<?php else: ?>
			<!-- New wine -->
			<li class="breadcrumb-item active">Add New Wine</li>
		<?php endif; ?>

	</ol>
</nav>


<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data">
<div class="row">
	<div class="col-4 mb-3">
		<label for="cellar_uid" class="form-label">Cellar</label>
		<select class="form-select" name="cellar_uid" disabled required>
			<?php
			foreach ($wines->cellars() as $cellarChoice) {
				$title = trim($cellarChoice->name);
				$selected = ($cellarChoice->uid === $cellar->uid) ? ' selected' : '';
				echo "<option value=\"{$cellarChoice->uid}\"{$selected}>{$title}</option>";
			}
			?>
		</select>
	</div>
	<div class="col-4 mb-3">
		<label for="cellar_uid" class="form-label">Bin</label>
		<select class="form-select" name="bin_uid" required>
			<?php
			foreach ($cellar->sections() AS $section) {
				$output = "<optgroup label=\"" . $section . "\">";
				
				foreach ($cellar->bins(['section' => $section]) AS $binOption) {
					if ($binOption->uid == $bin->uid) {
						$output .= "<option value=\"" . $binOption->uid . "\" selected>" . $binOption->name . "</option>";
					} else {
						$output .= "<option value=\"" . $binOption->uid . "\">" . $binOption->name . "</option>";
					}
				}
				
				$output .= "</optgroup>";
				
				echo $output;
			}
			?>
		</select>
	</div>
	<div class="col-4 mb-3">
		<label for="status" class="form-label">Status</label>
		<select class="form-select" name="status" required>
			<?php
			$wineStatuses = explode(',', $settings->get('wine_status'));
			
			foreach ($wineStatuses as $wineStatus) {
				$type = trim($wineStatus);
				$selected = ($type === $wine->status) ? ' selected' : '';
				echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
			}
			?>
		</select>
	</div>
</div>

<div class="row">
	<div class="col-4 mb-3">
		<label for="category" class="form-label">Category</label>
		<select class="form-select" name="category" required>
			<?php
			$wine_categories = explode(',', $settings->get('wine_category'));
			
			foreach ($wine_categories as $wine_category) {
				$type = trim($wine_category);
				$selected = ($type === $wine->category) ? ' selected' : '';
				echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
			}
			?>
		</select>
	</div>
	<div class="col-8 mb-3">
		<label for="name" class="form-label">Wine Name</label>
		<input type="text" class="form-control" name="name" value="<?= $wine->name ?>" required>
	</div>
</div>

<hr>

<div class="row">
	<div class="col mb-3">
		<label for="name" class="form-label">Supplier</label>
		<input type="text" class="form-control" name="supplier" value="<?= htmlspecialchars($wine->supplier ?? '', ENT_QUOTES, 'UTF-8') ?>" list="suppliers">
		<datalist id="suppliers">
			<?php
			foreach ($wines->listFromWines("supplier") as $supplier) {
				echo '<option value="' . htmlspecialchars($supplier) . '"></option>';
			}
			?>
		</datalist>
	</div>
	<div class="col mb-3">
		<label for="name" class="form-label">Supplier Order Reference</label>
		<input type="text" class="form-control" name="supplier_ref" value="<?= htmlspecialchars($wine->supplier_ref ?? '', ENT_QUOTES, 'UTF-8') ?>">
	</div>
</div>

<hr>

<div class="row">
	<div class="col-4 mb-3">
		<label for="name" class="form-label">Country of Origin</label>
		<input type="text" class="form-control" name="country_of_origin" list="codes-countries" value="<?= htmlspecialchars($wine->country_of_origin ?? '', ENT_QUOTES, 'UTF-8') ?>">
		<datalist id="codes-countries">
			<?php
			foreach ($wines->listFromWines("country_of_origin") as $country_of_origin) {
				echo '<option value="' . htmlspecialchars($country_of_origin) . '"></option>';
			}
			?>
		</datalist>
	</div>
	<div class="col-4 mb-3">
		<label for="name" class="form-label">Region of Origin</label>
		<input type="text" class="form-control" name="region_of_origin" list="codes-regions" value="<?= htmlspecialchars($wine->region_of_origin ?? '', ENT_QUOTES, 'UTF-8') ?>">
		<datalist id="codes-regions">
			<?php
			foreach ($wines->listFromWines("region_of_origin") as $region_of_origin) {
				echo '<option value="' . htmlspecialchars($region_of_origin) . '"></option>';
			}
			?>
		</datalist>
	</div>
	<div class="col-4 mb-3">
		<label for="name" class="form-label">Grape</label>
		<input type="text" class="form-control" name="grape" list="codes-grapes" value="<?= htmlspecialchars($wine->grape ?? '', ENT_QUOTES, 'UTF-8') ?>">
		<datalist id="codes-grapes">
			<?php
			foreach ($wines->listFromWines("grape") as $grape) {
				echo '<option value="' . htmlspecialchars($grape) . '"></option>';
			}
			?>
		</datalist>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<label for="qty" class="form-label text-truncate">Bottles Qty.</label>
				<input type="text" class="form-control" name="qty" value="<?= $wine->currentQty() ?>" <?= $isNew ? '' : 'disabled' ?> required pattern="[0-9]*">
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<label for="price_purchase" class="form-label text-truncate">Purchase Price <a href="#" data-bs-toggle="tooltip" data-bs-title="All prices are ex VAT"><i class="bi bi-info-circle"></i></a></label>
				<input type="text" class="form-control" name="price_purchase" value="<?= htmlspecialchars($wine->price_purchase ?? '', ENT_QUOTES, 'UTF-8') ?>" pattern="[0-9]+([\.,][0-9]+)?" required>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<label for="price_internal" class="form-label text-truncate">Internal/External Price <a href="#" data-bs-toggle="tooltip" data-bs-title="All prices are ex VAT"><i class="bi bi-info-circle"></i></a></label>
				<div class="row">
					<div class="col-sm">
						<input type="text" class="form-control" name="price_internal" value="<?= htmlspecialchars($wine->price_internal ?? '', ENT_QUOTES, 'UTF-8') ?>" required pattern="[0-9]+([\.,][0-9]+)?">
					</div>
					<div class="col-sm">
						<input type="text" class="form-control" name="price_external" value="<?= htmlspecialchars($wine->price_external ?? '', ENT_QUOTES, 'UTF-8') ?>" required pattern="[0-9]+([\.,][0-9]+)?">
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<label for="price_purchvintagease" class="form-label text-truncate">Vintage Year</label>
				<input type="text" class="form-control" id="vintage" name="vintage" value="<?= htmlspecialchars($wine->vintage ?? '', ENT_QUOTES, 'UTF-8') ?>" pattern="[0-9]*">
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<label for="code" class="form-label text-truncate">Wine Code</label>
				<input type="text" class="form-control" name="code" list="codes-code" value="<?= htmlspecialchars($wine->code ?? '', ENT_QUOTES, 'UTF-8') ?>">
				<datalist id="codes-code">
					<?php
					foreach ($wines->listFromWines("code") as $code) {
						echo '<option value="' . htmlspecialchars($code) . '"></option>';
					}
					?>
				</datalist>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-7 col-lg-8">
		<div class="mb-3">
			<label for="tasting" class="form-label text-truncate">Tasting Note</label>
			<textarea class="form-control" name="tasting" rows="4"><?= htmlspecialchars($wine->tasting ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
		</div>
		<div class="mb-3">
			<label for="notes" class="form-label text-truncate">Notes <span class="badge rounded-pill text-bg-warning">Private</span></label>
			<textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($wine->notes ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
		</div>
	</div>
	<div class="col-md-5 col-lg-4">
		<div class="card mb-3">
			<img src="<?= $wine->photographURL() ?>" class="card-img-top" alt="Wine bottle image">
			<div class="card-body">
				<input class="form-control" type="file" name="photograph">
			</div>
		</div>
	</div>
</div>

<hr>
<?php
if ($isNew) {
	echo "<button type=\"submit\" class=\"btn btn-primary w-100\">Add Wine</button>";
} else {
	echo "<button type=\"submit\" class=\"btn btn-primary w-100\">Update Wine</button>";
	echo "<input type=\"hidden\" name=\"uid\" value=\"" . $wine->uid . "\">";
}
?>

</form>

<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

const el = document.getElementById('vintage');
const options = {
	display: {
		icons: {
			type: 'icons',
			time: 'bi bi-clock',
			date: 'bi bi-calendar',
			up: 'bi bi-arrow-up',
			down: 'bi bi-arrow-down',
			previous: 'bi bi-chevron-left',
			next: 'bi bi-chevron-right',
			today: 'bi bi-calendar-check',
			clear: 'bi bi-trash',
			close: 'bi bi-close'
		},
		components: {
			calendar: true,
			date: false,
			month: false,
			year: true,
			decades: true,
			clock: false
		}
	},
	localization: {
		format: 'yyyy',
	  }
};

new tempusDominus.TempusDominus(el, options);
</script>
