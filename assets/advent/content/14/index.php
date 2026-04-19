<?php
$connectionsConfig = [
	'id' => 'connections-day-14',
	'title' => 'Hall and Library',
	'subtitle' => 'Today leans into Oxford places, habits, and bits of academic language.',
	'footer' => 'Formal hall may not help you solve it, but it sets the mood.',
	'intro_message' => 'One category is all about communal college life.',
	'groups' => [
		[
			'name' => 'Shared college spaces',
			'color' => 'var(--cg-yellow)',
			'words' => ['BUTTERY', 'DININGHALL', 'LAUNDRY', 'QUAD'],
		],
		[
			'name' => 'Words in referencing',
			'color' => 'var(--cg-green)',
			'words' => ['AUTHOR', 'EDITION', 'JOURNAL', 'VOLUME'],
		],
		[
			'name' => 'Likely after dark in Oxford',
			'color' => 'var(--cg-blue)',
			'words' => ['BELL', 'LAMP', 'MOONLIGHT', 'SHADOW'],
		],
		[
			'name' => 'Study actions',
			'color' => 'var(--cg-purple)',
			'words' => ['ANNOTATE', 'MEMORISE', 'PRACTISE', 'RECALL'],
		],
	],
];

require __DIR__ . '/../_connections.php';
