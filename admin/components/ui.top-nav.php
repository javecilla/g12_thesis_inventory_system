<!--FOR TOP NAVBAR-->
   <div class="header">
      <div class="container-fluid">
         <div class="row">
            <div class="col-lg-12">

               <div class="float-left">
                  <!-- FOR MENU BUTTON -->
                  <div class="hamburger sidebar-toggle">
                     <span class="line"></span>
                     <span class="line"></span>
                     <span class="line"></span>
                  </div>
               </div> <!--float-left-->

               <div class="float-right">      
                  <div class="dropdown dib">
                     <div class="header-icon" data-toggle="dropdown">
                        <?php
                           $user_name = $_SESSION['username'];
                           $get_data = mysqli_query($connection, "SELECT * FROM users WHERE uname ='$user_name'");
                           $row = mysqli_fetch_assoc($get_data);                    
                        ?>      
                        <span class="user-avatar">
                           <?php echo $row['acct_name']?>&nbsp;&nbsp;<i class="fa-solid fa-angle-down f-s-10"></i>
                        </span>
                        <div class="drop-down dropdown-profile dropdown-menu dropdown-menu-right">
                           <div class="dropdown-content-heading">
                              <span class="text-left">
                                 <img src="resources/images/user-photo-upload/<?php echo $row['profile_img'] . '.' . $row['img_extension']?>" width="40" height="40" class="rounded-circle">
                                 <label style="color: green; font-size: 15px;">&nbsp;Online</label>
                              </span>
                           </div> <!--dropdown heading-->
                           <div class="dropdown-content-body">
                              <ul>
                                 <li>
                                    <a onclick="window.location.href='profile';">
                                       &nbsp;<i class="fa-solid fa-user" style="opacity: .8"></i>&nbsp;&nbsp;
                                       <span>Profile</span>
                                    </a>
                                 </li>
                                  <li>
                                    <a onclick="window.location.href='accounts';">
                                       &nbsp;<i class="fa-solid fa-users" style="opacity: .8"></i>&nbsp;&nbsp;
                                       <span>User Management</span>
                                    </a>
                                 </li>                         
                                 <li>
                                   <a onclick="window.location.href='proponents';">
                                       &nbsp;<i class="fa-solid fa-map" style="opacity: .8"></i>&nbsp;&nbsp;
                                       <span>Proponents</span>
                                   </a>
                                 </li>
                                 <li>
                                    <a onclick="window.location.href='../auth/logout';">
                                       &nbsp;<i class="fa-solid fa-right-from-bracket" style="opacity: .8"></i>&nbsp;&nbsp;
                                       <span>Logout</span>
                                    </a>
                                 </li>
                              </ul>
                           </div> <!--dropdown content-->
                        </div> <!--dropdown profile-->
                     </div> <!--dropdown header-->
                  </div> <!--dropdown dib-->

               </div> <!--float-right-->

             </div> <!--col-lg-12-->
         </div> <!--row-->
      </div> <!--container-->
   </div> <!--header-->