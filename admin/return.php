<?php
session_start();
require_once __DIR__ . '/config/db.connection.php';
require_once __DIR__ . '/app/check_user.php';
ini_set('display_errors',  1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>IMS | Return Equipment</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>
    <link rel="stylesheet" type="text/css" href="resources/css/return.css" defer/>
  </head>

  <body style="background: url('resources/images/system-photo/gmc-bg.png');">
    <?php require_once __DIR__ . '/components/ui.side-nav.php';?>
    <?php require_once __DIR__ . '/components/ui.top-nav.php';?>
    <?php require_once __DIR__ . '/components/js.file-links.inc.php';?>
    <?php require_once __DIR__ . '/components/msgalert.contr.inc.php';?>

    <!--FOR HEADER CONTENT-->
    <div class="content-wrap">
      <div class="main">
        <div class="container-fluid">

          <div class="row">
            <div class="col-lg-8 p-r-0 title-margin-right">
              <div class="page-header">
                <div class="page-title">
                  <h6 class="clock"><?php echo date("M-d-Y")?> / <?php echo date(" h: i A");?></h6>
                </div> 
              </div> 
            </div>

            <div class="col-lg-4 p-l-0 title-margin-left">
              <div class="page-header">
                <div class="page-title">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="margin-left: -100px;">
                      <a href="admin-return" class="active"> Return </a>
                    </li>
                    <li class="breadcrumb-item">Barrow</li>
                    <li class="breadcrumb-item">Transaction</li>
                    <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div> <!--end row-->
               
          <section id="main-content">
            <!-- start card table -->
            <div class="card table-responsive">
              <div class="card-title pr">
                <form action="<?=$_SERVER['PHP_SELF']?>" method="GET">
                  <div class="row">
                    <div class="col-md-10">
                      <small>BRANCH / CAMPUS OF BORROWER</small><br/>
                      <input type="text" name="schoolBranch" id="schoolBranch"
                        value="<?php if(isset($_GET['schoolBranch'])){ echo $_GET['schoolBranch']; } ?>" 
                        class="schoolBranch form-control" placeholder="-- SELECT --" 
                        list="listSchoolBranch" autocomplete="off"
                      />
                      <datalist id="listSchoolBranch">
                        <?php
                          $query = "SELECT DISTINCT e.location_id, lb.*
                            FROM equipment e
                            INNER JOIN location_branch lb ON e.location_id = lb.id";
                          $stmt = mysqli_prepare($connection, $query);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);
                          if(mysqli_num_rows($result) > 0) { //check data from result
                            while($row = mysqli_fetch_assoc($result)) {
                              ?>
                                <option value="<?= $row['location']; ?>">
                                  <?= $row['location']; ?>
                                </option>
                              <?php
                            }
                          } else {
                            echo "No data found :(";
                          }
                        ?>
                      </datalist>
                    </div>
                    <div class="col-md-2">
                      <small>ACTIONS</small><br/>
                      <button type="button" class="btn btn-light" onclick="window.location.href='return'">
                        Reset</button>
                      <button type="submit" class="btn btn-secondary">Filter</button>
                    </div>
                  </div>
                </form><br/>        
              </div>   
              <!--start table-->
              <table id="barrowerList" class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 5%;">BID</th>
                    <th style="width: 5%;">Image</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 14%;">Contact Number</th>             
                    <th style="width: 5%;">Role</th>
                    <th style="width: 5%;">Status</th>
                    <th style="width: 6%;" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody id="transacRecord">
                  <?php
                    if(isset($_GET['schoolBranch']) && !empty($_GET['schoolBranch'])) {
                      $sql = "SELECT c.*, lb.* 
                        FROM costumers c
                        INNER JOIN location_branch lb ON c.school_id = lb.id
                        WHERE lb.location = ?
                        ORDER BY c.costumer_id DESC";
                      $stmt = mysqli_prepare($connection, $sql);
                      mysqli_stmt_bind_param($stmt, "s", $_GET['schoolBranch']);
                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);
                      if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                          ?>
                            <tr>
                              <td>GMC00<?=$row['costumer_id']?></td>
                              <td><img src="resources/images/system-photo/default-profile-c.jpg" alt="Costumer" 
                                class="rounded-circle img-thumbnail" loading="lazy"/></td>
                              <td><?=$row['name']?></td>
                              <td><?=$row['phone_number']?></td>
                              <td>
                                <?php
                                  if($row['role_position'] == "Faculty") {
                                    echo '<span class="badge badge-success">Faculty</span>';
                                  } else if($row['role_position'] == "Staff") {
                                    echo '<span class="badge badge-danger">Staff</span>';
                                  } else if($row['role_position'] == "Student"){
                                    echo '<span class="badge badge-primary ">Student</span>';
                                  } else {
                                    echo "Amogus!";
                                  }
                                ?>
                              </td>
                              <td>
                                <?php
                                  if($row['costumer_status'] == 1) { // allowed
                                    echo '
                                      <label class="switch">
                                        <input type="checkbox" onclick="window.location.href=\'app/actions.controller.php?id='.$row['costumer_id'].'&status='.($row['costumer_status'] == 1 ? 0 : 1).'\'" checked />
                                        <span class="slider round"></span>
                                      </label>
                                    ';
                                  } else { // block
                                    echo '
                                      <label class="switch">
                                        <input type="checkbox" onclick="window.location.href=\'app/actions.controller.php?id='.$row['costumer_id'].'&status='.($row['costumer_status'] == 1 ? 0 : 1).'\'" />
                                        <span class="slider round"></span>
                                      </label>
                                    ';
                                  }
                                ?>
                              </td>
                              <td>
                                <button type="button" 
                                  class="btn-primary btn btn-sm"
                                  onclick="window.location.href='app/costumer.record?gmcbid=<?=$row['costumer_id'];?>';">
                                    <i class='ti-eye view-icon'>&#xE872;</i>
                                </button>
                                <input type="hidden" class="valCostumerId" value="<?=$row['costumer_id']?>" />
                                <button type="button" name="" class="delCostumerId btn-danger btn btn-sm m-r-10">
                                  <i class='ti-trash' >&#xE872;</i>
                                </button>
                              </td>
                            </tr>
                          <?php //end divider
                        } //while
                      } //if num rows
                    } //end if isset
                    else {
                      $sql = "SELECT c.*, lb.*
                        FROM costumers c
                        INNER JOIN location_branch lb ON c.school_id = lb.id
                        ORDER BY c.costumer_id ASC";
                      $stmt = mysqli_prepare($connection, $sql);
                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);
                      if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                          ?>
                          <tr>
                            <td>GMC00<?=$row['costumer_id']?></td>
                            <td><img src="resources/images/system-photo/default-profile-c.jpg" alt="Costumer" 
                                class="rounded-circle img-thumbnail" loading="lazy"/></td>
                            <td><?=$row['name']?></td>
                            <td><?=$row['phone_number']?></td>
                            <td>
                              <?php
                                if($row['role_position'] == "Faculty") {
                                  echo '<span class="badge badge-success">Faculty</span>';
                                } else if($row['role_position'] == "Staff") {
                                  echo '<span class="badge badge-danger">Staff</span>';
                                } else if($row['role_position'] == "Student"){
                                  echo '<span class="badge badge-primary ">Student</span>';
                                } else {
                                  echo "Amogus!";
                                }
                              ?>
                            </td>
                            <td>
                              <?php
                                if($row['costumer_status'] == 1) { // allowed
                                  echo '
                                    <label class="switch">
                                      <input type="checkbox" onclick="window.location.href=\'app/actions.controller.php?id='.$row['costumer_id'].'&status='.($row['costumer_status'] == 1 ? 0 : 1).'\'" checked />
                                      <span class="slider round"></span>
                                    </label>
                                  ';
                                } else { // block
                                  echo '
                                    <label class="switch">
                                      <input type="checkbox" onclick="window.location.href=\'app/actions.controller.php?id='.$row['costumer_id'].'&status='.($row['costumer_status'] == 1 ? 0 : 1).'\'" />
                                      <span class="slider round"></span>
                                    </label>
                                  ';
                                }
                              ?>
                            </td>
                            <td>
                              <button type="button" 
                                class="btn-primary btn btn-sm"
                                onclick="window.location.href='app/costumer.record?gmcbid=<?=$row['costumer_id'];?>';">
                                  <i class='ti-eye view-icon'>&#xE872;</i>
                              </button>
                              <button type="button" data-id="<?=$row['costumer_id']?>" 
                                class="delCostumerId btn-danger btn btn-sm m-r-10">
                                <i class='ti-trash' >&#xE872;</i>
                              </button>
                            </td>
                          </tr>
                          <?php
                        }
                      } else {
                        echo "No data found :(";
                      }
                    }
                  ?>
                </tbody> 
              </table>                 
            </div> <!--end card-->
          </section>          
        </div> <!--end container fluid-->
      </div> <!--end div main-->
    </div> <!--end content-wrap-->

    <script src="resources/js/return.js"></script>
    <script type="text/javascript">
      //PREVENT USER TO LOGIN SAME ACCOUNT IN DIFFERENT DEVICE OR LOCATION
      function check_sesssion_id() {
        var session_id = "<?php echo $_SESSION['session_id']; ?>";
        fetch('app/check_login.php').then(function(response){
          return response.json();
        }).then(function(responseData){
          if(responseData.output == 'logout'){
            window.location.href = '../auth/logout.php';
          }
        });
      }
      setInterval(function(){
      check_sesssion_id();
      }, 10000);
    </script>
  </body>
</html>