<?php
	if(isset($_SESSION['danger_message']))	:
?>

	<div class="alert alert-danger alert-dismissible fade show">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>System says:</strong> <?= $_SESSION['danger_message']; ?>
	</div>

<?php 
	unset($_SESSION['danger_message']);
	endif;
?>