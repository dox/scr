<?php
$connectionsConfig = [
	'id' => 'connections-day-4',
	'title' => 'Oxford Openings',
	'subtitle' => 'A gentle start: four Oxford-flavoured groups to sort.',
	'footer' => 'A little college lore goes a long way.',
	'intro_message' => 'Expect a mix of place names, study life, and traditions.',
	'groups' => [
		[
			'name' => 'Oxford terms',
			'color' => 'var(--cg-yellow)',
			'words' => ['HILARY', 'MICHAELMAS', 'TRINITY', 'VACATION'],
		],
		[
			'name' => 'Teaching formats',
			'color' => 'var(--cg-green)',
			'words' => ['LECTURE', 'SEMINAR', 'TUTORIAL', 'WORKSHOP'],
		],
		[
			'name' => 'College rooms',
			'color' => 'var(--cg-blue)',
			'words' => ['CHAPEL', 'HALL', 'JCR', 'LIBRARY'],
		],
		[
			'name' => 'Essay verbs',
			'color' => 'var(--cg-purple)',
			'words' => ['ANALYSE', 'ARGUE', 'COMPARE', 'DISCUSS'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
