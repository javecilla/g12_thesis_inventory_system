<?php
	if(isset($_SESSION['warning_message']))	:
?>

	<div class="alert alert-warning alert-dismissible fade show">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>System says:</strong> <?= $_SESSION['warning_message']; ?>
	</div>

<?php 
	unset($_SESSION['warning_message']);
	endif;
?>