<?php
pageAccessCheck("wine");

$wineClass = new wineClass();

$subtitle = "Filter wines based on multiple criteria";

$title = "Wine Filter";
echo makeTitle($title, $subtitle, true);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page">Filter</li>
	</ol>
</nav>

<hr class="pb-3" />

<form id="searchForm" method="POST" id="searchForm">
	<div class="mb-3" id="conditionsContainer">
		<?php
		foreach ($_POST['conditions'] AS $condition) {
			echo $condition['field'] . " " . $condition['operator'] . " " . $condition['value'] . "<br >";
		} 
		?>
	</div>
	
	<div class="mb-3">
		<button type="button" class="btn btn-info" onclick="addCondition()">+ Add Condition</button>
	</div>
	
	<div class="mb-3">
		<button type="submit" class="btn btn-primary">Submit</button>
	</div>
</form>


<hr class="pb-3" />


	<div id="resultsContainer"></div>


<?php
foreach ($wineClass->allCellars() AS $cellar) {
	$cellars[] = ['value' => $cellar['uid'], 'label' => $cellar['name']];
}
foreach ($wineClass->allBins() AS $bin) {
	$bins[] = ['value' => $bin['uid'], 'label' => $bin['name']];
}
foreach ($wineClass->listFromWines("supplier") AS $supplier) {
	$suppliers[] = ['value' => $supplier['supplier'], 'label' => $supplier['supplier']];
}
foreach ($wineClass->listFromWines("category") AS $category) {
	$categories[] = ['value' => $category['category'], 'label' => $category['category']];
}
foreach ($wineClass->listFromWines("grape") AS $grape) {
	$grapes[] = ['value' => $grape['grape'], 'label' => $grape['grape']];
}
foreach ($wineClass->listFromWines("country_of_origin") AS $country_of_origin) {
	$countries[] = ['value' => $country_of_origin['country_of_origin'], 'label' => $country_of_origin['country_of_origin']];
}
foreach ($wineClass->listFromWines("region_of_origin") AS $region_of_origin) {
	$regions[] = ['value' => $region_of_origin['region_of_origin'], 'label' => $region_of_origin['region_of_origin']];
}
foreach ($wineClass->listFromWines("status") AS $status) {
	$statuses[] = ['value' => $status['status'], 'label' => $status['status']];
}
?>

<script>
const cellars = <?php echo json_encode($cellars, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const bins = <?php echo json_encode($bins, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const suppliers = <?php echo json_encode($suppliers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const categories = <?php echo json_encode($categories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const grapes = <?php echo json_encode($grapes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const countries = <?php echo json_encode($countries, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const regions = <?php echo json_encode($regions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const statuses = <?php echo json_encode($statuses, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

const fields = [
	{ 
		value: 'wine_wines.name', 
		label: 'Name', 
		type: 'text' 
	},
	{ 
		value: 'cellar_uid', 
		label: 'Cellar', 
		type: 'select',
		options: cellars
	},
	{ 
		value: 'bin_uid', 
		label: 'Bin', 
		type: 'select',
		options: bins
	},
	{ 
		value: 'date_created', 
		label: 'Date Created', 
		type: 'text' 
	},
	{ 
		value: 'date_updated', 
		label: 'Date Updated', 
		type: 'text' 
	},
	{ 
		value: 'code', 
		label: 'Code', 
		type: 'text' 
	},
	{ 
		value: 'status', 
		label: 'Status', 
		type: 'select',
		options: statuses
	},
	{ 
		value: 'wine_wines.name', 
		label: 'Name', 
		type: 'text' 
	},
	{
		value: 'supplier',
		label: 'Supplier',
		type: 'select',
		options: suppliers
	},
	{ 
		value: 'supplier_ref', 
		label: 'Supplier Ref.', 
		type: 'text' 
	},
	{
		value: 'wine_wines.category',
		label: 'Category',
		type: 'select',
		options: categories
	},
	{
		value: 'grape',
		label: 'Grape',
		type: 'select',
		options: grapes
	},
	{
		value: 'country_of_origin',
		label: 'County of Origin',
		type: 'select',
		options: countries
	},
	{
		value: 'region_of_origin',
		label: 'Region of Origin',
		type: 'select',
		options: regions
	},
	{ 
		value: 'vintage', 
		label: 'Vintage', 
		type: 'text' 
	},
	{ 
		value: 'price_purchase', 
		label: 'Price (Purchase)', 
		type: 'text' 
	},
	{ 
		value: 'price_internal', 
		label: 'Price (Internal)', 
		type: 'text' 
	},
	{ 
		value: 'price_external', 
		label: 'Price (External)', 
		type: 'text' 
	},
	{ 
		value: 'tasting', 
		label: 'Tasting Notes', 
		type: 'text' 
	},
	{ 
		value: 'wine_wines.notes', 
		label: 'Notes (Private)', 
		type: 'text' 
	}
];

const operators = [
  { value: '=', label: '=' },
  { value: '<', label: '<' },
  { value: '>', label: '>' },
  { value: 'LIKE', label: 'LIKE' }
];

let conditionCount = 0;

function addCondition() {
  conditionCount++;

  const container = document.getElementById('conditionsContainer');

  const div = document.createElement('div');
  div.className = 'd-flex align-items-center gap-2 mb-2';

  // Field dropdown
  const fieldSelect = document.createElement('select');
  fieldSelect.name = `conditions[${conditionCount}][field]`;
  fieldSelect.classList.add('form-select');
  fieldSelect.style.width = '150px';

  fields.forEach(f => {
	const option = document.createElement('option');
	option.value = f.value;
	option.textContent = f.label;
	fieldSelect.appendChild(option);
  });

  // Operator dropdown
  const operatorSelect = document.createElement('select');
  operatorSelect.name = `conditions[${conditionCount}][operator]`;
  operatorSelect.classList.add('form-select');
  operatorSelect.style.width = '100px';

  operators.forEach(op => {
	const option = document.createElement('option');
	option.value = op.value;
	option.textContent = op.label;
	operatorSelect.appendChild(option);
  });

  // Value container
  const valueContainer = document.createElement('div');
  valueContainer.style.width = '200px';

  // Create initial value field (default to text input)
  const valueInput = createValueInput(fields[0], conditionCount);
  valueContainer.appendChild(valueInput);

  // When the field changes, replace the value input
  fieldSelect.addEventListener('change', function() {
	const selectedField = fields.find(f => f.value === this.value);

	// Clear old input
	valueContainer.innerHTML = '';

	// Add new input (text or select)
	const newInput = createValueInput(selectedField, conditionCount);
	valueContainer.appendChild(newInput);
  });

  // Remove button
  const removeBtn = document.createElement('button');
  removeBtn.type = 'button';
  removeBtn.className = 'btn btn-danger btn-sm';
  removeBtn.textContent = 'Remove';
  removeBtn.onclick = () => div.remove();

  div.appendChild(fieldSelect);
  div.appendChild(operatorSelect);
  div.appendChild(valueContainer);
  div.appendChild(removeBtn);

  container.appendChild(div);
}

function createValueInput(field, conditionIndex) {
  if (field.type === 'select') {
	const select = document.createElement('select');
	select.name = `conditions[${conditionIndex}][value]`;
	select.classList.add('form-select');
	field.options.forEach(opt => {
	  const option = document.createElement('option');
	  option.value = opt.value;
	  option.textContent = opt.label;
	  select.appendChild(option);
	});
	return select;
  } else {
	const input = document.createElement('input');
	input.type = 'text';
	input.name = `conditions[${conditionIndex}][value]`;
	input.classList.add('form-control');
	return input;
  }
}

document.getElementById('searchForm').addEventListener('submit', function(e) {
	e.preventDefault(); // Stop default form submission

	const form = e.target;
	const formData = new FormData(form);
	const resultsDiv = document.getElementById('resultsContainer');

	// Clear and show loading spinner
	resultsDiv.innerHTML = `
		<div class="text-center my-4">
			<div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
			<span class="ms-2">Searching...</span>
		</div>
	`;

	// Send AJAX POST request to wine_search2.php
	fetch('actions/wine_search2.php', {
		method: 'POST',
		body: formData
	})
	.then(response => {
		if (!response.ok) throw new Error('Network response was not ok');
		return response.text();
	})
	.then(html => {
		resultsDiv.innerHTML = html;
	})
	.catch(err => {
		resultsDiv.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
	});
});



</script>
