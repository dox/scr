<?php
$connectionsConfig = [
	'id' => 'connections-day-11',
	'title' => 'Oxford Libraries',
	'subtitle' => 'Sorting shelves, sources, and scholarly routines.',
	'footer' => 'No books were overdue in the making of this puzzle.',
	'intro_message' => 'One category belongs squarely to the reading room.',
	'groups' => [
		[
			'name' => 'Reading room behavior',
			'color' => 'var(--cg-yellow)',
			'words' => ['CONCENTRATE', 'HIGHLIGHT', 'REVISE', 'WHISPER'],
		],
		[
			'name' => 'Book descriptors',
			'color' => 'var(--cg-green)',
			'words' => ['HARDBACK', 'JACKET', 'MARGIN', 'SPINE'],
		],
		[
			'name' => 'Types of evidence',
			'color' => 'var(--cg-blue)',
			'words' => ['ARCHIVE', 'DATA', 'INTERVIEW', 'MANUSCRIPT'],
		],
		[
			'name' => 'Oxford sights',
			'color' => 'var(--cg-purple)',
			'words' => ['BRIDGE', 'COURTYARD', 'MEADOW', 'SPIRE'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
