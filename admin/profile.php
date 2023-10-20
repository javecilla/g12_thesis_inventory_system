<?php
session_start();
require_once __DIR__ . '/config/db.connection.php';
require_once __DIR__ . '/app/check_user.php';
ini_set('display_errors',  1);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>IMS | Profile</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>
  </head>
  <body style="background: url('resources/images/system-photo/gmc-bg.png');">
    <?php require_once __DIR__ . '/components/ui.side-nav.php';?>
    <?php require_once __DIR__ . '/components/ui.top-nav.php';?>
    <?php require_once __DIR__ . '/components/js.file-links.inc.php';?>
    <?php require_once __DIR__ . '/components/msgalert.contr.inc.php';?>
    <div class="content-wrap">
      <div class="container"><br/>
        <?php require_once __DIR__ . '/components/lmsgalert.inc.php';?>
        <div class="row">
          <div class="col-sm-7">
            <div class="card">
              <!-- Profile Picture -->
              <div class="card">
                <div class="card-body">
                  <div class="d-flex flex-column align-items-center text-center">
                    <div class="profile-container">
                      <img src="resources/images/user-photo-upload/<?= $row['profile_img'] . '.' . $row['img_extension']?>" alt="Admin" class="rounded-circle profile_img" width="200"/>
                    </div>
                    <div class="mt-3">
                      <form action="app/actions.controller.php" method="POST" enctype="multipart/form-data">
                        <label for="inputTag" class="btn btn-light border-0" style=" cursor: pointer;">
                          <i class="far fa-solid fa-camera text-info"></i>&nbsp; Change profile
                          <input type="file" id="inputTag" accept=".jpeg, .jpg, .png" 
                            style="display: none;" class="uimage" />
                        </label>
                        <button type="submit" data-uid="<?= $row['id']?>" disabled 
                          class="btn btn-light m-b-9 savePhotoBtn border-0">
                          <i class="far fa-solid fa-floppy-disk text-success"></i> Save
                        </button>   
                      </form>
                    </div>
                  </div>
                </div>
              </div><br/>
              <!-- User Account -->
              <form action="app/actions.controller.php" method="POST">
                <!--USERS ACCOUNT NAME-->
                <div class="row m-b-8">
                  <div class="col-2">
                    <span class="contact-title">Name:</span>
                  </div>
                  <div class="col-10">
                    <input type="text" class="form-control m-l-13"
                      autocomplete="off" id="aname"
                      value="<?= $row['acct_name'];?>" />
                  </div>
                </div>

                <!--USERS SCHOOL BRANCH-->
                <div class="row">
                  <div class="col-2">
                    <span class="contact-title">Admin from:</span>
                  </div>
                  <div class="col-10">
                    <input type="text"
                      value="<?=$row['school_branch']?>" 
                      class="form-control m-l-13"
                      list="adminFromList"
                      autocomplete="off" id="uschool" 
                    />
                    <datalist id="adminFromList">
                      <?php
                        $query = "SELECT DISTINCT e.location_id, lb.*
                          FROM equipment e
                          INNER JOIN location_branch lb ON e.location_id = lb.id";
                        $stmt = mysqli_prepare($connection, $query);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if(mysqli_num_rows($result) > 0) { //check data from result
                          while($school = mysqli_fetch_assoc($result)) {
                            ?>
                              <option value="<?= $school['location']; ?>">
                                <?= $school['location']; ?>
                               </option>
                            <?php
                          }
                        } else {
                          echo "No data found :(";
                        }
                      ?>
                    </datalist>
                  </div>
                </div><br/>

                <!--USERS EMAIL-->
                <div class="row m-b-8">
                  <div class="col-2">
                    <span class="contact-title">Email:</span>
                  </div>
                  <div class="col-10">
                    <input type="text" class="form-control m-l-13" name="email" 
                    value="<?= $row['email'];?>" readonly/>
                  </div>
                </div><br/>
                <center>
                  <button type="submit" id="updateInfoBtn" data-uid="<?= $row['id']?>" 
                    class="btn btn-light border-0" style="width: 50%;">
                    <i class="far fa-solid fa-floppy-disk text-success"></i> Update Information
                  </button> 
                </center>  
              </form>
            </div>
          </div>
          <div class="col-sm-5">
            <div class="card">
              <!-- tab -->
              <div class="customtab2">
                <ul class="nav nav-tabs " role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#changepass" role="tab">
                      <span class="hidden-down">Password</span> 
                    </a> 
                  </li>      
                  <li class="nav-item"> 
                    <a class="nav-link" data-toggle="tab" href="#export" role="tab">
                      <span class="hidden-down">Backups database</span>
                    </a> 
                  </li>
                </ul>
                <div class="tab-content">
                  <!-- first tab -->
                  <div class="tab-pane active" id="changepass" role="tabpanel">
                    <form action="app/actions.controller.php" method="POST" id="changepassForm">
                      <ul class="list-group list-group-flush" style="border: none;"><br/>
                        <li>
                          <div class="m-b-5">
                            <span>Username:</span>
                            <input type="text" value="<?= $row['uname'];?>" class="form-control" readonly/>
                          </div>
                          <div class="m-b-5">
                            <span>Old Password:</span>
                            <input type="password" id="oldPass" class="oldPassword inputPword form-control"/>
                          </div>
                          <div class="m-b-5">
                            <span>New Password:</span>
                            <input type="password" id="newPass" class="newPassword inputPword form-control" />
                          </div>
                          <div>
                            <span>Confirm Password:</span>
                            <input type="password" id="confirmPass" class="confirmPassword inputPword form-control m-b-5" />
                          </div>
                          <center>
                            <button type="submit" id="updatePasswordBtn"
                              data-uid="<?= $row['id']?>" 
                              class="btn btn-light m-l-20 m-b-9 border-0">
                              <i class="far fa-solid fa-key text-success"></i> Update Password
                            </button>
                          </center>   
                        </li>
                      </ul>
                    </form>
                  </div><!--end first tab-->

                  <div class="tab-pane" id="export" role="tabpanel"><br/>
                    <label>Database</label><br/>
                    <button type="button" id="dbBackupBtn" onclick="$('#maintenanceModal').modal('show')" 
                    class="btn btn-light text-capitalize border-0" > 
                      <i class="fas fa-solid fa-cloud-arrow-down text-primary"></i> Back up now
                    </button><br/><br/>
                    <label>System Documentation</label><br/>
                    <button type="button" onclick="$('#maintenanceModal').modal('show')" class="btn btn-light border-0">
                      <i class="far fa-solid fa-file-arrow-down text-info "></i> Download
                    </button>
                  </div><!--end second tab-->
                </div><!--end tab content-->
              </div><!--end costumtab2-->
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
              <div class="card">
              <div class="card-body">
                <h5 class="card-title">Activity Log</h5>
                <p class="card-text">Working on this features...</p>
              </div>
            </div>
          </div>
        </div>
      </div>  
    </div><!--end content wrap-->

    <div id="maintenanceModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <i class="fa-solid fa-xmark fa-lg"></i>
            </button>
          </div>
          <div class="modal-body mb-3">
            <img src="resources/images/system-photo/documentationbg.jpg" width=400 class="img-responsive m-l-20"/>
            <h5 class="text-danger text-center">THIS FEATURES IS UNDER DEVELOPMENT.</h5>
          </div>
        </div>
      </div>
    </div>
    <script src="resources/js/profile.js"></script>
    <script defer>
      function isEmpty(field) {
        return field === "";
      }

      //PREVENT USER TO LOGIN SAME ACCOUNT IN DIFFERENT DEVICE OR LOCATION
      function check_sesssion_id() {
        var session_id = "<?php echo $_SESSION['session_id']; ?>";
        fetch('app/check_login.php').then(function(response){
          return response.json();
        }).then(function(responseData){
          if(responseData.output == 'logout'){
            window.location.href = 'auth/logout.php';
          }
        });
      }
      setInterval(function(){
        check_sesssion_id();
      }, 10000);
    </script>
  </body>
</html>
