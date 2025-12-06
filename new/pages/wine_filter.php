<?php
$user->pageCheck('wine');

$wines = new Wines();

echo pageTitle(
	"Wine Filter",
	"Filter wines based on multiple criteria"
);

$fieldSources = [
	'wine_wines.supplier'			=> $wines->listFromWines("supplier"),
	'wine_wines.category'			=> $wines->listFromWines("category"),
	'wine_wines.grape'				=> $wines->listFromWines("grape"),
	'wine_wines.country_of_origin'	=> $wines->listFromWines("country_of_origin"),
	'wine_wines.region_of_origin'	=> $wines->listFromWines("region_of_origin"),
	'wine_wines.status'				=> $wines->listFromWines("status"),
];

$fields = [
	['value'=>'wine_bins.name', 'label'=>'Bin Name', 'type'=>'text', 'operators'=>['=','!=','LIKE']],
	['value'=>'wine_bins.cellar_uid', 'label'=>'Cellar UID', 'type'=>'text', 'operators'=>['=','!=']],
	['value'=>'wine_wines.name', 'label'=>'Name', 'type'=>'text', 'operators'=>['=','!=','LIKE']],
	['value'=>'wine_wines.code', 'label'=>'Code', 'type'=>'text', 'operators'=>['=','!=','LIKE']],
	['value'=>'wine_wines.supplier', 'label'=>'Supplier', 'type'=>'select', 'operators'=>['=','!=','LIKE']],
	['value'=>'wine_wines.category', 'label'=>'Category', 'type'=>'select', 'operators'=>['=','!=']],
	['value'=>'wine_wines.grape', 'label'=>'Grape', 'type'=>'select', 'operators'=>['=','!=']],
	['value'=>'wine_wines.country_of_origin', 'label'=>'Country of Origin', 'type'=>'select', 'operators'=>['=','!=']],
	['value'=>'wine_wines.region_of_origin', 'label'=>'Region of Origin', 'type'=>'select', 'operators'=>['=','!=']],
	['value'=>'wine_wines.status', 'label'=>'Status', 'type'=>'select', 'operators'=>['=','!=','LIKE']],
	['value'=>'wine_wines.vintage', 'label'=>'Vintage', 'type'=>'text', 'operators'=>['=','!=','<=','>=']],
	['value'=>'wine_wines.price_purchase', 'label'=>'Price (Purchase)', 'type'=>'text', 'operators'=>['=','<=','>=']],
	['value'=>'wine_wines.price_internal', 'label'=>'Price (Internal)', 'type'=>'text', 'operators'=>['=','<=','>=']],
	['value'=>'wine_wines.price_external', 'label'=>'Price (External)', 'type'=>'text', 'operators'=>['=','<=','>=']],
	['value'=>'wine_wines.tasting', 'label'=>'Tasting Notes', 'type'=>'text', 'operators'=>['LIKE']],
	['value'=>'wine_wines.notes', 'label'=>'Notes', 'type'=>'text', 'operators'=>['LIKE']],
];
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Filter</li>
	</ol>
</nav>

<hr/>

<div class="p-3 mb-3 border rounded">
	<form id="searchForm" method="POST">
		<div id="conditionsContainer"></div>
		<div class="mb-3">
			<button type="button" class="btn btn-info" id="addBtn">+ Add Condition</button>
		</div>
		<div class="mb-3">
			<button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>
		</div>
	</form>
</div>



<div id="resultsContainer"></div>



<script>
const fieldDefinitions = <?= json_encode($fields) ?>;
const fieldSources = <?= json_encode($fieldSources) ?>;

function makeFieldOptions() {
	return fieldDefinitions.map(f => `<option value="${f.value}">${f.label}</option>`).join('');
}

function makeOperatorOptions(ops = ['=']) {
	return ops.map(op => `<option value="${op}">${op}</option>`).join('');
}

function findFieldDef(value) {
	return fieldDefinitions.find(f => f.value === value) || null;
}

function makeValueInput(def, idx) {
	if (def.type === 'select') {
		const source = fieldSources[def.value] || [];
		const opts = source.map(v => `<option value="${v}">${v}</option>`).join('');
		return `<select name="conditions[${idx}][value]" class="form-select value-input">${opts}</select>`;
	}
	return `<input type="text" name="conditions[${idx}][value]" class="form-control value-input"/>`;
}

// Add a new condition row
document.getElementById('addBtn').addEventListener('click', () => {
	const idx = Date.now();
	const defaultField = fieldDefinitions[0];
	const defaultOps = defaultField.operators || ['='];
	const valueInput = makeValueInput(defaultField, idx);

	const html = `
	<div class="d-flex gap-2 mb-2 condition-row" data-idx="${idx}">
		<select name="conditions[${idx}][field]" class="form-select field-select">
			${makeFieldOptions()}
		</select>

		<select name="conditions[${idx}][operator]" class="form-select operator-select">
			${makeOperatorOptions(defaultOps)}
		</select>

		${valueInput}

		<button type="button" class="btn btn-danger btn-sm removeRow">Remove</button>
	</div>
	`;

	document.getElementById('conditionsContainer').insertAdjacentHTML('beforeend', html);
});

// Delegated listener: update operators and value input when field changes
document.getElementById('conditionsContainer').addEventListener('change', e => {
	const el = e.target;
	if (!el.classList.contains('field-select')) return;

	const row = el.closest('.condition-row');
	const selectedValue = el.value;
	const def = findFieldDef(selectedValue);

	if (!def || !row) return;

	// Update operators
	const operatorSelect = row.querySelector('.operator-select');
	operatorSelect.innerHTML = makeOperatorOptions(def.operators);

	// Update value input
	const oldInput = row.querySelector('.value-input');
	oldInput.outerHTML = makeValueInput(def, row.dataset.idx);
});

// Remove row
document.getElementById('conditionsContainer').addEventListener('click', e => {
	if (e.target.classList.contains('removeRow')) {
		e.target.closest('.condition-row').remove();
	}
});

// Submit form and load results via AJAX
document.getElementById('searchForm').addEventListener('submit', e => {
	e.preventDefault();
	const results = document.getElementById('resultsContainer');
	results.innerHTML = `<div class="text-center my-4"><div class="spinner-border"></div></div>`;

	fetch('./ajax/wine_filter.php', {
		method: 'POST',
		body: new FormData(e.target)
	})
	.then(r => r.text())
	.then(html => results.innerHTML = html)
	.catch(err => results.innerHTML = `<div class="alert alert-danger">${err}</div>`);
});
</script>

