<?php
$user->pageCheck('settings');

echo pageTitle(
	"Site Settings",
	"Customise the behaviour, display and configuration of this site",
	[
		[
			'permission' => 'settings',
			'title' => 'Add new',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addSettingModal'
			]
		]
	]
);
?>

<div class="alert alert-danger text-center"><strong>Warning!</strong> Making changes to these settings can disrupt the running of this site.  Proceed with caution.</div>

<div class="accordion" id="accordionExample">
  <?php
  foreach ($settings->getAll() as $setting) {
	  // Determine the show states based on the 'settingUID' parameter
	  $isActive = isset($_GET['settingUID']) && $_GET['settingUID'] == $setting['uid'];
	  $headingShow = $isActive ? "accordion-button show" : "accordion-button collapsed";
	  $settingShow = $isActive ? "accordion-collapse show" : "accordion-collapse collapse";
  
	  // Generate item name and the start of the output string
	  $itemName = "collapse-" . $setting['uid'];
	  $output = "<div class=\"accordion-item\">
				  <h2 class=\"accordion-header\" id=\"{$setting['uid']}\">
					  <button class=\"{$headingShow}\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#{$itemName}\" aria-expanded=\"true\" aria-controls=\"{$itemName}\">
						  <strong>{$setting['name']}</strong>: {$setting['description']} <span class=\"badge bg-secondary\">{$setting['type']}</span>
					  </button>
				  </h2>
				  <div id=\"{$itemName}\" class=\"{$settingShow}\" aria-labelledby=\"{$setting['uid']}\" data-bs-parent=\"#accordionExample\">
					  <div class=\"accordion-body\">
						  <form method=\"post\" id=\"form-{$setting['uid']}\" action=\"{$_SERVER['REQUEST_URI']}\">";
  
	  // Handle different setting types
	  switch ($setting['type']) {
		  case 'numeric':
			  $output .= "<div class=\"input-group\">
							  <input type=\"number\" class=\"form-control\" id=\"value\" name=\"value\" value=\"{$setting['value']}\">
							  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
						  </div>";
			  break;
  
		  case 'boolean':
			  $checked = ($setting['value'] == "true") ? "checked" : "";
			  $output .= "<div class=\"form-check\">
							  <input type=\"hidden\" id=\"value\" name=\"value\" value=\"false\">
							  <input type=\"checkbox\" class=\"form-check-input\" id=\"value\" name=\"value\" value=\"true\" {$checked}>
							  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
						  </div>";
			  break;
  
		  case 'html':
			  $output .= "<textarea rows=\"10\" class=\"form-control\" id=\"value\" name=\"value\">" . htmlspecialchars($setting['value'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</textarea>
						  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
			  break;
  
		  case 'hidden':
			  $output .= "Setting cannot be changed here";
			  break;
  
		  default:
			  $output .= "<div class=\"input-group\">
							  <input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"{$setting['value']}\">
							  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
						  </div>";
			  break;
	  }
  
	  // Add the hidden UID field and close the form
	  $output .= "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"{$setting['uid']}\">
				  </form>
			  </div>
		  </div>
	  </div>";
  
	  // Output the result
	  echo $output;
  }
  ?>
</div>


<!-- Add Setting Modal -->
<div class="modal fade" tabindex="-1" id="addSettingModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Test Modal <span class="text-danger"><strong>WARNING!</strong></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				Test Modal
				<p><span class="text-danger"><strong>WARNING!</strong> Are you sure you want to delete this member?</p>
				<p>This will also delete <strong>all</strong> bookings (past and present) for this member.<p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
			</div>
		</div>
	</div>
</div>