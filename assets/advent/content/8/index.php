<?php
$connectionsConfig = [
	'id' => 'connections-day-8',
	'title' => 'College Landmarks',
	'subtitle' => 'Oxford spaces, scholarly language, and campus routines await.',
	'footer' => 'Picture old stone, bicycles, and hurried essays.',
	'intro_message' => 'Look for one group that feels very architectural.',
	'groups' => [
		[
			'name' => 'Architectural features',
			'color' => 'var(--cg-yellow)',
			'words' => ['GARGOYLE', 'GATE', 'SPIRE', 'STAIRCASE'],
		],
		[
			'name' => 'Research actions',
			'color' => 'var(--cg-green)',
			'words' => ['CITE', 'INTERPRET', 'MEASURE', 'OBSERVE'],
		],
		[
			'name' => 'Things to submit',
			'color' => 'var(--cg-blue)',
			'words' => ['DISSERTATION', 'ESSAY', 'FORM', 'PROPOSAL'],
		],
		[
			'name' => 'Oxford on a rainy day',
			'color' => 'var(--cg-purple)',
			'words' => ['BROLLY', 'PAVEMENT', 'PUDDLE', 'SCARF'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
