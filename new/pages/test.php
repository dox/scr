<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Summernote Lite in Bootstrap Modal</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Summernote Lite CSS -->
  <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-4">
  <h2>Summernote Lite in Modal Example</h2>
  <button class="btn btn-primary" id="openModal">Edit Content</button>
</div>

<!-- Modal -->
<div class="modal fade" id="editorModal" tabindex="-1" aria-labelledby="editorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="editorModalLabel">Edit Page Content</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		<!-- Summernote Editor -->
		<div id="summernote"></div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="saveContent">Save</button>
	  </div>
	</div>
  </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Summernote Lite JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
	// Initialize Summernote Lite
	const editor = document.getElementById('summernote');
	const summernoteInstance = new window.Summernote(editor, {
	  placeholder: 'Write your content here...',
	  tabsize: 2,
	  height: 300,
	  toolbar: [
		['style', ['bold', 'italic', 'underline', 'clear']],
		['font', ['strikethrough', 'superscript', 'subscript']],
		['para', ['ul', 'ol', 'paragraph']],
		['table', ['table']],
		['insert', ['link', 'picture']],
		['view', ['fullscreen', 'codeview']]
	  ]
	});

	// Show modal
	const modal = new bootstrap.Modal(document.getElementById('editorModal'));
	document.getElementById('openModal').addEventListener('click', () => modal.show());

	// Save content
	document.getElementById('saveContent').addEventListener('click', () => {
	  const content = summernoteInstance.getHTML(); // Get HTML content
	  console.log("Saved content:", content);
	  modal.hide();
	});
  });
</script>

</body>
</html>
