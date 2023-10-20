 <?php
   include_once('config/db.connection.php');

   $user_name = $_SESSION['username'];
   $get_data = mysqli_query($connection, "SELECT * FROM users WHERE uname ='$user_name'");
   $row = mysqli_fetch_assoc($get_data);                    
?>  
<div class="row">
   <div class="col-lg-8 p-r-0 title-margin-right">
      <div class="page-header">
         <div class="page-title">
            <!-- <h1>WELCOME ADMIN</h1> -->
            <ol>
               <li class="breadcrumb-item active"style="margin-top: 5%;">
                  <a href="admin-dashboard">Dashboard /</a>
               </li>
            </ol>
         </div>
      </div>
   </div>

   <div class="col-lg-4 p-l-0 title-margin-left">
      <div class="page-header">
         <div class="page-title">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><?php echo date("M-d-Y")?></li>
               <li class="breadcrumb-item active"><?php echo date(" h: i A");?></li>
            </ol>
         </div>
      </div>
   </div>     
</div>
