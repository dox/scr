<?php
pageAccessCheck("wine");

$wineClass = new wineClass();

if (isset($_POST['conditions'])) {
	foreach ($_POST['conditions'] AS $condition) {
		$conditionFields[] = $condition['field'];
	} 
	$subtitle = "Filtering wines on: " . implode(", ", $conditionFields);
	
	foreach ($_POST['conditions'] as $index => $cond) {
		$field = $cond['field'] ?? '';
		$operator = $cond['operator'] ?? '';
		$value = $cond['value'] ?? '';
	
		// Special handling for LIKE
		if ($operator === 'LIKE') {
			$whereParts[] = "$field LIKE '%$value%'";
		} else {
			$whereParts[] = "$field $operator '$value'";
		}
	}
	
	$whereClause = '';
	if (!empty($whereParts)) {
		$whereClause = 'WHERE ' . implode(' AND ', $whereParts);
	}
	
	// Output for testing:
	$sql  = "SELECT * FROM wine_wines ";
	$sql .= $whereClause;
	
	echo $sql;

	$wines = $db->query($sql)->fetchAll();
	echo count($wines) . " wines found";
	
	
} else {
	// not searching yet
	$subtitle = "Filter wines based on multiple criteria";
}

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

<form id="searchForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
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

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($wines AS $wine) {
	$wine = new wine($wine['uid']);
	
	echo $wine->card();
}
?>
</div>


<?php
foreach ($wineClass->listFromWines("supplier") AS $supplier) {
	$suppliers[] = ['value' => $supplier['supplier'], 'label' => $supplier['supplier']];
}
?>		

<script>
const suppliers = <?php echo json_encode($suppliers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

const fields = [
  { 
	value: 'name', 
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
	value: 'department',
	label: 'Department',
	type: 'select',
	options: [
	  { value: 'HR', label: 'HR' },
	  { value: 'IT', label: 'IT' },
	  { value: 'Sales', label: 'Sales' }
	]
  },
  {
	value: 'age',
	label: 'Age',
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
</script>
