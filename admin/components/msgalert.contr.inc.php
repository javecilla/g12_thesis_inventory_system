<?php
   if(isset($_SESSION['resultMessage']) && $_SESSION['resultMessage'] != '') {
      ?>
      <script>
         const alertMessageCode = "<?=$_SESSION['resultMessageCode'];?>";
         const actionPerform = "<?=$_SESSION['actionPerform'];?>";
         if(alertMessageCode == "success") {
            if(actionPerform == "Add") {
               swal({
                  title: "Added!",
                  text: "<?php echo $_SESSION['resultMessage']; ?>",
                  type: "<?php echo $_SESSION['resultMessageCode']; ?>",
                  confirmButtonColor: "#A9CCE3",
                  confirmButtonText: "Ok",
                  closeOnConfirm: false,
                  html: true,
               });
               
            } else if(actionPerform == "Update") {
               swal({
                  title: "Updated!",
                  text: "<?php echo $_SESSION['resultMessage']; ?>",
                  type: "<?php echo $_SESSION['resultMessageCode']; ?>",
                  confirmButtonColor: "#A9CCE3",
                  confirmButtonText: "Ok",
                  closeOnConfirm: false,
                  html: true,
               });

            } else if(actionPerform == "Process") {
               swal({
                  title: "Processed!",
                  text: "<?php echo $_SESSION['resultMessage']; ?>",
                  type: "<?php echo $_SESSION['resultMessageCode']; ?>",
                  confirmButtonColor: "#A9CCE3",
                  confirmButtonText: "Ok",
                  closeOnConfirm: false,
                  html: true,
               });
            } else if(actionPerform == "barrow") {
               swal({
                  title: "Barrowed!",
                  text: "<?php echo $_SESSION['resultMessage']; ?>",
                  type: "<?php echo $_SESSION['resultMessageCode']; ?>",
                  confirmButtonColor: "#A9CCE3",
                  confirmButtonText: "Ok",
                  closeOnConfirm: false,
                  html: true,
               });
            } else if(actionPerform == "return") {
               swal({
                  title: "Returned!",
                  text: "<?php echo $_SESSION['resultMessage']; ?>",
                  type: "<?php echo $_SESSION['resultMessageCode']; ?>",
                  confirmButtonColor: "#A9CCE3",
                  confirmButtonText: "Ok",
                  closeOnConfirm: false,
                  html: true,
               });
            } 
            else {
               location.reload();
            }
            
         } else if(alertMessageCode == "warning") {
            swal({
               title: "Opps!",
               text: "<?php echo $_SESSION['resultMessage']; ?>",
               type: "<?php echo $_SESSION['resultMessageCode']; ?>",
               confirmButtonColor: "#A9CCE3",
               confirmButtonText: "Ok",
               closeOnConfirm: false,
               html: true,
            });

         } else if(alertMessageCode == "error") {
            swal({
               title: "Failed!",
               text: "<?php echo $_SESSION['resultMessage']; ?>",
               type: "<?php echo $_SESSION['resultMessageCode']; ?>",
               confirmButtonColor: "#A9CCE3",
               confirmButtonText: "Ok",
               closeOnConfirm: false,
               html: true,
            });
            
         } else {
            location.reload();
         }
      </script>
      <?php
          unset($_SESSION['resultMessage']);
   }
?>