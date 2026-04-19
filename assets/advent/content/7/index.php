<?php
$connectionsConfig = [
	'id' => 'connections-day-7',
	'title' => 'Tutorial Time',
	'subtitle' => 'A set inspired by Oxford tutorials, tutors, and the surrounding chaos.',
	'footer' => 'Some meetings are more intimidating than others.',
	'intro_message' => 'One category belongs squarely inside the tutorial room.',
	'groups' => [
		[
			'name' => 'Things a tutor might ask',
			'color' => 'var(--cg-yellow)',
			'words' => ['CLARIFY', 'DEFEND', 'EXPLAIN', 'JUSTIFY'],
		],
		[
			'name' => 'Desk items',
			'color' => 'var(--cg-green)',
			'words' => ['HIGHLIGHTER', 'LAPTOP', 'MUG', 'NOTEBOOK'],
		],
		[
			'name' => 'College social spaces',
			'color' => 'var(--cg-blue)',
			'words' => ['BAR', 'COMMONROOM', 'GARDEN', 'LODGE'],
		],
		[
			'name' => 'Verbs for reading',
			'color' => 'var(--cg-purple)',
			'words' => ['SCAN', 'SKIM', 'STUDY', 'SURVEY'],
		],
	],
];

require __DIR__ . '/../_connections.php';
