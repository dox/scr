<?php
$connectionsConfig = [
	'id' => 'connections-day-16',
	'title' => 'Oxford History Hour',
	'subtitle' => 'A puzzle with a slightly more historical and scholarly flavour.',
	'footer' => 'Old stones, old books, and tidy categories.',
	'intro_message' => 'Look for one set that feels especially archival.',
	'groups' => [
		[
			'name' => 'Archival materials',
			'color' => 'var(--cg-yellow)',
			'words' => ['CHARTER', 'DIARY', 'LETTER', 'REGISTER'],
		],
		[
			'name' => 'Words for age',
			'color' => 'var(--cg-green)',
			'words' => ['ANCIENT', 'EARLY', 'MEDIEVAL', 'MODERN'],
		],
		[
			'name' => 'College stonework',
			'color' => 'var(--cg-blue)',
			'words' => ['COLUMN', 'COURSE', 'PARAPET', 'TRACERY'],
		],
		[
			'name' => 'Academic conversation words',
			'color' => 'var(--cg-purple)',
			'words' => ['CLAIM', 'CONTEXT', 'METHOD', 'THEORY'],
		],
	],
];

require __DIR__ . '/../_connections.php';
