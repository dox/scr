<?php
$user->pageCheck('meals');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	printArray($_POST);
	
	if (isset($_POST['deleteMealUID'])) {
		$deleteMealUID = filter_input(INPUT_POST, 'deleteMealUID', FILTER_SANITIZE_NUMBER_INT);
		
		$meal = new Meal($deleteMealUID);
		$meal->delete();
	} else {
		//$newMember = new Members();
		//$newMember->create($_POST);
	}
}

echo pageTitle(
	"Meals",
	"All meals both past and present",
	[
		[
			'permission' => 'settings',
			'title' => 'Add new',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addMemberModal'
			]
		]
	]
);
?>
