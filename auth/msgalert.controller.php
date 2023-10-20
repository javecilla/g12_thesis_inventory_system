<?php
   if(isset($_SESSION['resultMessage']) && $_SESSION['resultMessage'] != '') {
      ?>
      <script>
         const alertMessageCode = "<?=$_SESSION['resultMessageCode'];?>";
         let actionPerform = "<?=$_SESSION['actionPerform'];?>";
         if(alertMessageCode == "success") {
            if(actionPerform == "login") {
               swal({
                  title: "Login Successfully!",
                  text: "<h5 class='text-muted'><?php echo $_SESSION['resultMessage']; ?></h5>",
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
               text: "<h5 class='text-muted'><?php echo $_SESSION['resultMessage']; ?></h5>",
               imageUrl: "../admin/resources/images/system-photo/gmc.png",
               confirmButtonColor: "#F8C471",
               confirmButtonText: "Ok",
               closeOnConfirm: false,
               html: true,
            });

         } else if(alertMessageCode == "error") {
            swal({
               title: "Failed!",
               text: "<h5 class='text-muted'><?php echo $_SESSION['resultMessage']; ?></h5>",
               imageUrl: "../admin/resources/images/system-photo/gmc.png",
               confirmButtonColor: "#F8C471",
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