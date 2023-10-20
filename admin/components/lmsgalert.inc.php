<?php
if (isset($_SESSION['message'])) :
?>

    <div class="alert alert-success alert-dismissible fade show" id="alert-message">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>System says:</strong> <?= $_SESSION['message']; ?>
    </div>

    <script>
        // Add JavaScript to hide the alert after 3 seconds
        setTimeout(function () {
            document.getElementById("alert-message").style.display = "none";
        }, 1000); // 1000 milliseconds = 2 seconds
    </script>

<?php
unset($_SESSION['message']);
endif;
?>
