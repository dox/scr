<?php
$user->pageCheck('wine');

$prepopulateWine = null;
if (!empty($_GET['wine_uid'])) {
	$wine_uid = (int)$_GET['wine_uid'];
	if ($wine_uid > 0) {
		// Fetch the wine data from your DB or use your Wine class
		$wine = new Wine($wine_uid);
		if ($wine->uid) {
			$prepopulateWine = [
				'uid'            => $wine->uid,
				'name'           => $wine->clean_name(),
				'qty'            => $wine->currentQty() ?? null,
				'price_internal' => $wine->price_internal ?? null,
				'price_external' => $wine->price_external ?? null
			];
		}
	}
}

echo pageTitle(
	'Add Wine Transaction',
	'Create transaction for wine(s)'
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_transactions">Transactions</a></li>
		<li class="breadcrumb-item active">Add</li>
	</ol>
</nav>

<hr/>

<div class="wine-search-wrapper mb-3 position-relative">
	<input 
		type="text"
		class="form-control form-control-lg"
		id="wine_search"
		placeholder="Quick search all cellars"
		autocomplete="off"
		spellcheck="false"
	>
	
	<ul id="wine_search_results" class="list-group"></ul>
</div>

<hr>

<form id="wine_invoice_form" method="post">
<div class="row">
	<div class="col-md-7 col-lg-8">
		<div id="selected_wines"></div>
	</div>
	<div class="col-md-5 col-lg-4">
		<div class="mb-3">
			<label for="name" class="form-label">Transaction Name</label>
			<input type="text" class="form-control" name="name" value="" required>
		</div>
		<div class="mb-3">
			<label for="date_posted" class="form-label">Posting Date</label>
			<div class="input-group">
				<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
				<input type="text" class="form-control" name="date_posted" id="date_posted" required>
			</div>
		</div>
		<div class="mb-3">
			<label for="description" class="form-label">Description</label>
			<textarea class="form-control" name="description"></textarea>
		</div>
	</div>
	
	<div class="col">
		<button type="submit" class="btn btn-primary transaction-create-btn w-100" id="create_button">Submit Transaction</button>
	</div>
</div>
</form>

<script>
// ----------------------------
// Live search & adding cards
// ----------------------------
liveSearch('wine_search', 'wine_search_results', './ajax/wine_livesearch.php', {
	onClick: addWineToInvoice
});

function addWineToInvoice(wine) {
	const container = document.getElementById('selected_wines');
	if (!container) return;

	// Prevent duplicates
	if (container.querySelector(`[data-uid="${wine.uid}"]`)) {
		container
			.querySelector(`[data-uid="${wine.uid}"] input[type="number"]`)
			.focus();
		return;
	}

	const card = document.createElement('div');
	card.className = 'card mb-3';
	card.dataset.uid = wine.uid;

	card.innerHTML = `
		<div class="card-body">
			<h5 class="card-title">${wine.name}</h5>

			<div class="row g-2">
				<div class="col d-flex flex-column">
					<label class="form-label">Qty.</label>
					<input type="number"
						   class="form-control mt-auto"
						   name="bottles[${wine.uid}]"
						   value="1"
						   min="1">
					<div class="form-text">
						Available: ${wine.qty ?? '—'}
					</div>
				</div>

				<div class="col d-flex flex-column">
					<label class="form-label">£/bottle</label>
					<input type="number"
						   class="form-control mt-auto"
						   name="price_per_bottle[${wine.uid}]"
						   step="0.01"
						   value="${wine.price_internal ?? ''}">
					<div class="form-text text-truncate">
						Internal: £${wine.price_internal ?? '—'}
						/ External: £${wine.price_external ?? '—'}
					</div>
				</div>

				<div class="col-2 d-flex flex-column">
					<label class="form-label invisible">Remove</label>
					<button type="button"
							class="btn btn-outline-danger mt-auto remove-wine"
							aria-label="Remove wine">
						&times;
					</button>
					<div class="form-text invisible">Spacer</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="wine_uid[]" value="${wine.uid}">
	`;

	// Remove wine button
	card.querySelector('button').addEventListener('click', () => card.remove());

	container.appendChild(card);

	// Focus qty for rapid entry
	card.querySelector('input[name^="bottles"]').focus();
}
</script>

<script>
// ----------------------------
// Tempus Dominus DateTime Picker
// ----------------------------
const icons = {
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
};

const baseDisplay = {
	icons: icons,
	components: {
		calendar: true,
		date: true,
		month: true,
		year: true,
		decades: true,
		clock: true,
		hours: true,
		minutes: true,
		seconds: false
	}
};

const dateTimeOptions = {
	defaultDate: new Date('<?= date('c') ?>'),
	display: baseDisplay,
	localization: {
		format: 'yyyy-MM-dd HH:mm'
	}
};

new tempusDominus.TempusDominus(
	document.getElementById('date_posted'),
	dateTimeOptions
);

// Pre-populate wine if passed via URL
<?php if ($prepopulateWine): ?>
addWineToInvoice(<?= json_encode($prepopulateWine) ?>);
<?php endif; ?>
</script>


