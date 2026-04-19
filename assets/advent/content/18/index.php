<?php
$connectionsConfig = [
	'id' => 'connections-day-18',
	'title' => 'Seminar Season',
	'subtitle' => 'Discussion, debate, and academic habits drive today’s board.',
	'footer' => 'The best answers are grouped, not spoken aloud.',
	'intro_message' => 'One category sounds exactly like a seminar room.',
	'groups' => [
		[
			'name' => 'Seminar actions',
			'color' => 'var(--cg-yellow)',
			'words' => ['CHALLENGE', 'COMMENT', 'QUESTION', 'RESPOND'],
		],
		[
			'name' => 'Printed academic things',
			'color' => 'var(--cg-green)',
			'words' => ['ARTICLE', 'BOOKLET', 'MONOGRAPH', 'SYLLABUS'],
		],
		[
			'name' => 'Oxford weather companions',
			'color' => 'var(--cg-blue)',
			'words' => ['BOOTS', 'COAT', 'GLOVES', 'UMBRELLA'],
		],
		[
			'name' => 'Words tied to data',
			'color' => 'var(--cg-purple)',
			'words' => ['CHART', 'GRAPH', 'SAMPLE', 'TABLE'],
		],
	],
];

require __DIR__ . '/../_connections.php';
