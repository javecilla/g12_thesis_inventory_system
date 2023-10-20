<?php
session_start();
require_once __DIR__ . '/config/db.connection.php';
require_once __DIR__ . '/app/check_user.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>IMS | Dashboard</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>
  </head>
  <body style="background: url('resources/images/system-photo/gmc-bg.png');">
    <?php require_once __DIR__ . '/components/ui.side-nav.php';?>
    <?php require_once __DIR__ . '/components/ui.top-nav.php';?>
    <?php require_once __DIR__ . '/components/msgalert.contr.inc.php';?>
    <?php require_once __DIR__ . '/components/js.file-links.inc.php';?>
   
        
    <div class="content-wrap">
      <div class="main">
        <div class="container-fluid">

          <div class="row">
            <div class="col-lg-8 p-r-0 title-margin-right">
              <div class="page-header">
                <div class="page-title">
                  <h6 class="clock m-t-30"><?php echo date("M-d-Y")?> / <?php echo date(" h: i A");?></h6>
                </div> 
              </div> 
            </div>
            <div class="col-lg-4 p-l-0 title-margin-left">
              <div class="page-header">
                <div class="page-title">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                      <a href="admin-dashboard" class="active">Dashboard </a>
                    </li>
                    <li class="breadcrumb-item">Home</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div><!--end firstrow-->
               
          <section id="main-content" style="margin-top: -25px">
            <?php require_once __DIR__ . '/components/lmsgalert.inc.php';?>
            <div class="row">
              <div class="col-lg-3">
                <div class="card p-0">
                  <div class="stat-widget-three home-widget-three">
                    <div class="stat-icon bg-facebook">
                      <i class="ti-user color-success border-success"></i>
                    </div>
                    <div class="stat-content">
                      <div class="stat-text"> Admin</div>
                      <div class="stat-digit">
                        <?php
                          //fetch number of user or admin
                          $select_users = "SELECT id FROM users ORDER BY id";
                          $stmt = mysqli_prepare($connection, $select_users);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);
                          $row_users = mysqli_num_rows($result);
                        ?>
                        <input type="number" readonly value="<?= $row_users ?>"/>
                      </div>
                    </div>  
                  </div>
                </div>
              </div><!--end first widget-->
              <div class="col-lg-3">
                <div class="card p-0">
                  <div class="stat-widget-three home-widget-three">
                    <div class="stat-icon bg-info">
                      <i class="ti-layout-grid2  color-primary border-primary"></i>
                    </div>
                    <div class="stat-content">
                      <div class="stat-text"> Category</div>
                      <div class="stat-digit">
                        <?php
                          //fetch number of category
                          $select_category = "SELECT category_id FROM categories ORDER BY category_id";
                          $stmt = mysqli_prepare($connection, $select_category);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);;
                          $row_category = mysqli_num_rows($result);
                        ?>
                        <input type="number" readonly value="<?= $row_category ?>"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!--end second widget-->
              <div class="col-lg-3">
                <div class="card p-0">
                  <div class="stat-widget-three home-widget-three">
                    <div class="stat-icon bg-secondary">
                      <i class="ti-server color-cyan border-cyan"></i>
                    </div>
                    <div class="stat-content">
                      <div class="stat-text"> Equipment</div>
                      <div class="stat-digit">
                        <?php
                          //fetch number of equipment
                          $select_equipment = "SELECT id FROM equipment ORDER BY id";
                          $stmt = mysqli_prepare($connection, $select_equipment);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);;
                          $row_equipment = mysqli_num_rows($result);
                        ?>
                        <input type="number" readonly value="<?php echo $row_equipment ?>"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!--end third widget-->
              <div class="col-lg-3">
                <div class="card p-0">
                  <div class="stat-widget-three home-widget-three">
                    <div class="stat-icon bg-twitter">
                      <i class="ti-link color-danger border-danger"></i>
                    </div>
                    <div class="stat-content">
                      <div class="stat-text"> Barrower</div>
                      <div class="stat-digit">
                        <?php
                          $query = "SELECT be.*, c.* FROM barrowed_equipment be
                            INNER JOIN costumers c ON be.costumer_id = c.costumer_id
                            WHERE be.barrow_status = 1";
                          $stmt = mysqli_prepare($connection, $query);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);
                          $row_barrow = mysqli_num_rows($result);
                        ?>
                        <input type="number" readonly value="<?php echo $row_barrow ?>"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!--end fourth widget-->
            </div><!--end second row-->

            <div class="row">
              <div class="col-lg-7">
                <div class="card">
                  <div class="card-title"><h4>Recent Barrower</h4><hr/></div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th style="width: 130px;">Name</th>
                            <th style="width: 100px;">Role / Account</th>
                            <th style="width: 190px;">School / Campus</th>  
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = "
                              SELECT DISTINCT be.costumer_id, c.*, lb.*
                              FROM barrowed_equipment be
                              INNER JOIN costumers c ON be.costumer_id = c.costumer_id
                              INNER JOIN location_branch lb ON c.school_id = lb.id
                              ORDER BY be.barrow_date DESC
                              LIMIT 5";
                            $stmt = mysqli_prepare($connection, $query);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            //check data from result
                            if(mysqli_num_rows($result) > 0) {
                              while($data = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                  <td><?= $data['name']; ?></td>
                                  <td>
                                    <?php
                                      if($data['role_position'] == "Faculty") {
                                        echo '<span class="badge badge-success">Faculty</span>';
                                      } else if($data['role_position'] == "Staff") {
                                        echo '<span class="badge badge-danger">Staff</span>';
                                      } else if($data['role_position'] == "Student"){
                                        echo '<span class="badge badge-primary ">Student</span>';
                                      } else {
                                        echo "Amogus!";
                                      }
                                    ?>
                                  </td>
                                  <td class="text-left"><?= $data['location'];?></td> 
                                </tr>
                                <?php
                              }
                            } else {    
                              echo "No data found!";
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div><!--end column 8--->

              <div class="col-lg-5">
                <div class="card">
                  <div class="card-title"><h4>Recent Login </h4><hr/></div>
                    <div class="recent-comment m-t-15">
                      <?php                            
                        $query = "SELECT *, IFNULL(UNIX_TIMESTAMP(logout_time), 0) AS logout_timestamp
                          FROM users
                          ORDER BY is_logged_in DESC
                          LIMIT 5";
                        $stmt = mysqli_prepare($connection, $query);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        //check if there are any results
                        if(mysqli_num_rows($result) > 0) {
                          while($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <div class="media">
                              <div class="media-left">
                                <a href="#">
                                  <img src="resources/images/user-photo-upload/<?=$row['profile_img']?>.<?=$row['img_extension']?>" class="media-object" alt="Admin Profile" />
                                </a>
                              </div>
                              <div class="media-body">
                                <h4 class="media-heading color-primary"><?= $row['acct_name'];?></h4>
                                <p><?= $row['school_branch']; ?></p>
                                <?php
                                  $logout_timestamp = (int) $row['logout_timestamp'];
                                  if(!empty($logout_timestamp)) {
                                    if(!is_numeric($logout_timestamp)) { 
                                      echo "Error: logout_timestamp is not a numeric value.";
                                    } else {
                                      $time_diff = time() - $logout_timestamp;
                                      if($row['is_logged_in'] == 1) {
                                        //user is currently online
                                        echo "<p class='comment-date text-success'><b>Online</b></p>";
                                      } else {
                                        $time_since = time_diff($logout_timestamp, time()) . " ago";
                                        echo "<p class='comment-date'><b>$time_since</b></p>";
                                      }
                                    }
                                  } else {
                                    echo "No login record has found.";
                                  }
                                ?>
                              </div>
                            </div><!--end media--->
                            <?php
                          }
                        } else {
                          echo "No recently logged out users found.";
                        }
                      ?> 
                    </div><br/>
                  </div>
                </div>
              </div><!--end column 4-->
            </div><!---end second row-->

          </section>

        </div><!--container fluid-->
      </div><!--main-->
    </div><!--content wrap--->
    <script>
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