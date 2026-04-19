<?php
$connectionsConfig = [
	'id' => 'connections-day-17',
	'title' => 'Teddy Hall Traditions',
	'subtitle' => 'A cheerful set with college life, ceremony, and study words mixed together.',
	'footer' => 'Tradition helps, but so does luck.',
	'intro_message' => 'One group belongs firmly in formal college ritual.',
	'groups' => [
		[
			'name' => 'Formal occasions',
			'color' => 'var(--cg-yellow)',
			'words' => ['BANQUET', 'GRACE', 'PROCESSION', 'TOAST'],
		],
		[
			'name' => 'Items in a student room',
			'color' => 'var(--cg-green)',
			'words' => ['DESK', 'DUVET', 'KETTLE', 'POSTER'],
		],
		[
			'name' => 'Words for argument quality',
			'color' => 'var(--cg-blue)',
			'words' => ['COHERENT', 'CONVINCING', 'NUANCED', 'RIGOROUS'],
		],
		[
			'name' => 'Seen on college grounds',
			'color' => 'var(--cg-purple)',
			'words' => ['BICYCLERACK', 'GATE', 'LAMPPOST', 'PATH'],
		],
	],
];

require __DIR__ . '/../_connections.php';
