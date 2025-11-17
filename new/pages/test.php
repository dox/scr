<?php
echo pageTitle(
	"Test Page",
	"For testing purposes only"
);

?>

<button class="load-remote btn btn-primary"
		data-url="./ajax/menu_modal.php"
		data-bs-toggle="modal"
		data-bs-target="#myModal">
	Edit
</button>

<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-body" id="modalBody"></div>
	</div>
  </div>
</div>


<script>
initAjaxLoader('.load-remote', '#modalBody', {
  event: 'click',
  cache: false
});
</script>