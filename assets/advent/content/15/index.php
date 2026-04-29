<?php
$connectionsConfig = [
	'id' => 'connections-day-15',
	'title' => 'Exam Schools Shuffle',
	'subtitle' => 'Four sets inspired by assessments, argument, and Oxford routine.',
	'footer' => 'No invigilators required.',
	'intro_message' => 'One category belongs in exam season.',
	'groups' => [
		[
			'name' => 'Exam season words',
			'color' => 'var(--cg-yellow)',
			'words' => ['INVIGILATOR', 'REVISION', 'TIMETABLE', 'VENUE'],
		],
		[
			'name' => 'Ways to improve a draft',
			'color' => 'var(--cg-green)',
			'words' => ['CONDENSE', 'EXPAND', 'REORDER', 'REPHRASE'],
		],
		[
			'name' => 'Things with wheels in Oxford',
			'color' => 'var(--cg-blue)',
			'words' => ['BICYCLE', 'SUITCASE', 'TROLLEY', 'WHEELBARROW'],
		],
		[
			'name' => 'Words for certainty',
			'color' => 'var(--cg-purple)',
			'words' => ['CLEARLY', 'DEFINITELY', 'EVIDENTLY', 'SURELY'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
