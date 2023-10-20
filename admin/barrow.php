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
    <title>IMS | Borrow Equipment</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>
    <link rel="stylesheet" href="resources/css/borrow.css" defer/>
  </head>

  <body style="background: url('resources/images/system-photo/gmc-bg.png');">
    <?php require_once __DIR__ . '/components/ui.side-nav.php';?>
    <?php require_once __DIR__ . '/components/ui.top-nav.php';?>
    <?php require_once __DIR__ . '/components/js.file-links.inc.php';?>
    <?php require_once __DIR__ . '/components/msgalert.contr.inc.php';?>
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
                      <a href="barrow" class="active"> Barrow </a>
                    </li>
                    <li class="breadcrumb-item">Transaction</li>
                    <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div> <!--end row-->
          
          <section id="main-content"> 
            <div class="card"> 
              <div class="card-body p-b-0"> 

                <div class="customtab2"> 
                  <ul class="nav nav-tabs " role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#equipmentList" role="tab">
                        <span class="hidden-down">Equipment List</span> 
                      </a> 
                    </li>    
                    <li class="nav-item"> 
                      <a class="nav-link" data-toggle="tab" href="#barrowList" role="tab">
                        <span class="hidden-down">Barrow List
                          <span class="badge badge-secondary">
                            <?php if(isset($_SESSION["equipment_cart"])) { echo count($_SESSION["equipment_cart"]); } else { echo '0'; }?>
                          </span>
                        </span>
                      </a> 
                    </li>
                  </ul>
                </div> <!-- end customtab2 -->

                <div class="tab-content"> 
                  <!-- first tab -->
                  <div class="tab-pane active" id="equipmentList" role="tabpanel">           
                      <div class="table-responsive card">
                        <?php if(empty($_GET['locationRack'])): ?>
                        <div class="alert alert-light" role="alert" id="instruction">
                          <strong><i class="fa-solid fa-circle-info"></i>&nbsp; Important:</strong> Before borrowing equipment, kindly choose the specific equipment location rack from which you wish to borrow.
                        </div>
                        <?php endif; ?>
                        <div class="card-title">
                          <form action="<?=$_SERVER['PHP_SELF']?>" method="GET">
                            <div class="row">
                              <div class="col-md-5">
                                <small>LOCATION RACK OF EQUIPMENT</small><br/>
                                <input type="text" name="locationRack" id="locationRack"
                                  value="<?php if(isset($_GET['locationRack'])){ echo $_GET['locationRack']; } ?>" 
                                  class="locationRack form-control" placeholder="-- SELECT --" 
                                  list="listLocationRack" autocomplete="off"
                                />
                                <datalist id="listLocationRack">
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
                              <div class="col-md-5">
                                <small>ROOM CODE OF EQUIPMENT</small><br/>
                                <input type="text" name="roomCode" id="roomCode"
                                  value="<?php if(isset($_GET['roomCode'])){ echo $_GET['roomCode']; } ?>" 
                                  class="roomCode form-control" placeholder="-- SELECT --" 
                                  list="listRoomCode" autocomplete="off"
                                />
                                <datalist id="listRoomCode">
                                  <?php
                                    $query = "SELECT DISTINCT e.roomcode_id, rc.*
                                      FROM equipment e
                                      INNER JOIN room_code rc ON e.roomcode_id = rc.room_code_id";
                                    $stmt = mysqli_prepare($connection, $query);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    if(mysqli_num_rows($result) > 0) { //check data from result
                                      while($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                          <option value="<?= $row['room_code_name']; ?>">
                                            <?= $row['room_code_name']; ?>
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
                                <button type="button" class="btn btn-light" onclick="window.location.href='barrow'">Reset</button>
                                <button type="submit" class="btn btn-secondary">Filter</button>
                              </div>
                            </div>
                          </form><br/>  
                        </div><br/><!--end card title--> 
                        <!-- start data list table -->
                        <table id="allEquipmentTbl" class="table"> 
                          <thead>
                            <tr>
                              <th style="width: 6%;">Image</th>
                              <th style="width: 14%;">Equipment Name</th>
                              <th style="width: 8%;">Category</th>
                              <th style="width: 8%;">Type</th>
                              <th style="width: 14%;">Room Code</th>
                              <th style="width: 10%;">Borrow Quantity</th>
                              <th style="width: 8%;">Action</th>
                            </tr>
                          </thead>
                          <tbody id="equipmentData">
                            <?php
                              if(!empty($_GET)) {
                                //initialize the base SQL query without any filtering
                                $query = "SELECT e.id AS eid, e.*, 
                                  c.category_name, 
                                  t.equip_type, 
                                  l.location, 
                                  u.unit_type, 
                                  rc.room_code_name
                                FROM equipment e 
                                INNER JOIN categories c ON e.category_id = c.category_id 
                                INNER JOIN equipment_type t ON e.type_id = t.equip_id
                                INNER JOIN location_branch l ON e.location_id = l.id
                                INNER JOIN room_code rc ON e.roomcode_id = rc.room_code_id
                                INNER JOIN equipment_unit u ON e.unit_id = u.id";
                                //empty array to store ang where clause conditions
                                $conditions = []; 
                                //empty string na magre represent the types of parameters na kailangan i bound
                                $params = [];
                                //empty array to store ang parameters depende kung ilan ang conditions
                                $types = "";

                                //check if locationRack is set
                                if(isset($_GET['locationRack']) && !empty($_GET['locationRack'])) {
                                  $filterByLocationRack = $_GET['locationRack'];
                                  //add this location rack to $conditions[] for the WHERE clause condition
                                  $conditions[] = "l.location = ?";
                                  $params[] = $filterByLocationRack;
                                  $types .= "s";
                                }

                                //check if roomCode is set
                                if(isset($_GET['roomCode']) && !empty($_GET['roomCode'])) {
                                  $filterByRoomCode = $_GET['roomCode'];
                                  //ddd this room code to $conditions[] for the WHERE clause condition
                                  $conditions[] = "rc.room_code_name = ?";
                                  $params[] = $filterByRoomCode;
                                  $types .= "s";
                                }

                                //filter equipment with available quantity greater than the specified conditions
                                $conditions[] = "e.quantity > e.conditions";

                                //check if the conditions array is not empty meaning may where clause, 
                                //then add the conditions to the WHERE clause in the query
                                if (!empty($conditions)) {
                                  $query .= " WHERE " . implode(" AND ", $conditions);
                                }

                                $query .= " ORDER BY e.date_added DESC";

                                $stmt = mysqli_prepare($connection, $query);

                                //bind the parameters based on the $conditions[]
                                if (!empty($conditions)) {
                                  //repeat the types depends on the parameter na nakuha
                                  $types = str_repeat('s', count($params));
                                  //merge o pagsamahin lahat ng nakuha data
                                  //mysqli_stmt_bind_param($stmt, "s", $params)
                                  $bindParams = array_merge([$stmt, $types], $params);
                                  $refs = [];
                                  foreach ($bindParams as $key => $value) {
                                    $refs[$key] = &$bindParams[$key];
                                  }
                                  //call the bind_param() function to bind the references
                                  call_user_func_array('mysqli_stmt_bind_param', $refs);
                                }

                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if(mysqli_num_rows($result) > 0) {
                                  while($data = mysqli_fetch_assoc($result)) {
                                    ?>
                                      <tr>
                                        <td>
                                          <img data-toggle="tooltip" 
                                            data-placement="left" 
                                            title="<?=$data['location'];?>" 
                                            src="resources/images/equipment-photo-upload/<?=$data['equipment_img'].'.' .$data['img_extension']?>" 
                                            class="img-thumbnail"
                                            loading="lazy"
                                          />
                                        </td>
                                        <td><?=$data['equipment_name']?></td>
                                        <td><?=$data['category_name'];?></td>
                                        <td><?=$data['equip_type'];?></td>
                                        <td><?=$data['room_code_name'];?></td>
                                        <td>
                                          <input type="text"
                                            class="borrowQtyInput form-control"
                                            id="borrowQtyInput<?=$data['eid']?>"
                                            data-aqty="<?=$data['quantity']?>"
                                            data-ename="<?=$data['equipment_name']?>"
                                            autocomplete="off"
                                          />
                                          <input type="hidden" 
                                            class="updatedAvailableQty"
                                            data-eid="<?=$data['eid']?>"
                                            id="updatedAvailableQty<?=$data['eid']?>"
                                          />
                                        </td>
                                        <td>
                                          <button type="submit" disabled
                                            class="addBorrowBtn btn btn-success btn-addon btn-flat"
                                            id="addBorrowBtn<?=$data['eid']?>"
                                            data-action="add"
                                            data-eid="<?=$data['eid']?>"
                                            data-ename="<?=$data['equipment_name']?>"
                                            data-price="<?=$data['price']?>"
                                            data-aqty="<?=$data['quantity']?>"
                                          >
                                            <i class='ti-plus'></i> Borrow
                                          </button>
                                        </td>
                                      </tr>
                                    <?php
                                  }
                                }
                              }
                              else {
                                $query = "SELECT e.*, 
                                    c.category_name, 
                                    et.equip_type, 
                                    lb.location,
                                    rc.room_code_name
                                  FROM equipment e
                                  INNER JOIN categories c ON e.category_id = c.category_id
                                  INNER JOIN equipment_type et ON e.type_id = et.equip_id
                                  INNER JOIN location_branch lb ON e.location_id = lb.id
                                  INNER JOIN room_code rc ON e.roomcode_id = rc.room_code_id
                                  WHERE e.quantity > e.conditions                                
                                  ORDER BY e.date_added DESC
                                ";
                                $stmt = mysqli_prepare($connection, $query);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if(mysqli_num_rows($result) > 0) {
                                  while ($data = mysqli_fetch_array($result)) {
                                    ?>
                                      <tr>
                                        <td>
                                          <img data-toggle="tooltip" 
                                            data-placement="left" 
                                            title="<?=$data['location'];?>" 
                                            src="resources/images/equipment-photo-upload/<?=$data['equipment_img'].'.' .$data['img_extension']?>" 
                                            class="img-thumbnail"
                                            loading="lazy"
                                          />
                                        </td>
                                        <td><?=$data['equipment_name']?></td>
                                        <td><?=$data['category_name'];?></td>
                                        <td><?=$data['equip_type'];?></td>
                                        <td><?=$data['room_code_name'];?></td>
                                        <td>
                                          <input type="text" class="form-control" readonly/>
                                        </td>
                                        <td>
                                          <button type="submit" disabled
                                            class="btn btn-success btn-addon btn-flat">
                                            <i class='ti-plus'></i> Borrow
                                          </button>
                                        </td>
                                      </tr>
                                    <?php
                                  }
                                } else {
                                  ?>
                                  <tr>
                                    <td colspan="7" class="text-center">
                                      <h6>NO DATA HAS BEEN FOUND :(</h6>
                                    </td>
                                  </tr>
                                  <?php
                                }
                              }
                            ?> 
                          </tbody> <!--end table body-->
                        </table> <!-- end data list table -->
                      </div>  <!-- equipment-card -->
                  </div> 

                  <!-- start second tab -->
                  <div class="tab-pane fade" id="barrowList" role="tabpanel">
                    <div class="row">
                      <div class="col-md-3">
                        <?php if(isset($_GET['locationRack'])): ?>
                          <div class="row">
                            <div class="col-md-4">
                              <button type="button" class="btn btn-light border-0"
                                onclick="openModal('#createBorrowerModal')">
                                <i class="fa-solid fa-user-plus text-success"></i> Add
                              </button>&nbsp;
                            </div>
                            <div class="col-md-8">
                              <select id="barrowerName" class="form-control">
                                <option selected value="">-- SELECT --</option>
                                <?php
                                  $sql = "SELECT DISTINCT c.*, lb.*
                                    FROM costumers c
                                    INNER JOIN location_branch lb ON c.school_id = lb.id
                                    WHERE lb.location = ? AND c.costumer_status = 1";
                                  $stmt = mysqli_prepare($connection, $sql);
                                  mysqli_stmt_bind_param($stmt, "s", $_GET['locationRack']);
                                  mysqli_stmt_execute($stmt);
                                  $result = mysqli_stmt_get_result($stmt);
                                  if(mysqli_num_rows($result) > 0):
                                    while($row = mysqli_fetch_assoc($result)):
                                      ?>
                                      <option value="<?= $row['name']; ?>">
                                        <?= $row['name']; ?>
                                      </option>
                                      <?php
                                    endwhile;
                                  else:
                                    echo "<h4>No record found</h4>";
                                  endif;
                                ?>  
                              </select>
                            </div>
                          </div>
                          <div class="card p-3 nodisplay" id="borrowerCard">
                            <!-- The data fetch via ajax -->
                            <div class="card-body">
                              <input type="hidden" value="" class="checkBorrower"/>
                              <input type="hidden" value="<?=$_SESSION['user_id']?>" class="userid"/>

                              <center>
                                <label class="text-muted"><b>BID: GMC00<span id="bid"></span></b></label>
                                <img src="resources/images/system-photo/default-profile-c.jpg" alt="Costumer" 
                                class="rounded-circle mb-2" width="150" loading="lazy"/>
                                <h5 class="card-title" id="bname"></h5>
                                <div id="brole"></div>
                                <div id="bnumber"></div>
                                <div id="bschool"></div>
                              </center>
                            </div>
                          </div>
                        <div class="alert alert-light mt-2" role="alert" id="instruction">
                          <strong><i class="fa-solid fa-circle-info"></i>&nbsp; Important:</strong> Prior to borrowing equipment, please select the appropriate user borrower from the dropdown list. If the user is new and no record exists, click the 'Add' button to create an account for them.
                        </div>
                        <?php endif; ?>
                      </div>
                      <div class="col-md-9">
                        <div class="barrow-list-card">
                          <div id="barrow_table">
                            <div class="table-responsive">
                              <table class="table barrow-data-table">
                                <thead>
                                  <tr>
                                    <th style="width: 15%;">Equipment Name</th>
                                    <th style="width: 15%;">Date Barrow</th>
                                    <th style="width: 8%;">Quantity</th>
                                    <th style="width: 8%;">Price</th>
                                    <th style="width: 8%;">Sub Total</th>
                                    <th style="width: 5%;" class="text-center">Action</th>
                                  </tr>
                                </thead>
                                <?php
                                  if(!empty($_SESSION['equipment_cart'])):
                                    $total = 0; $subtotal = 0;
                                    foreach ($_SESSION['equipment_cart'] as $keys => $values):
                                      //calculate subtotal of each equipment by row
                                      $subtotal = $values["equipment_bquantity"] * $values["equipment_price"];
                                      //calculate all subtotal amount of each equipment
                                      $total = $total + ($values["equipment_bquantity"] * $values["equipment_price"]);
                                      ?>
                                      <tbody id="equipmentCartTbody">
                                        <tr>
                                          <td 
                                            data-toggle="tooltip" 
                                            data-placement="left" 
                                            title="Available Quantity: <?= $values["updatedAvailableQty"]; ?>">
                                            <?= $values["equipment_name"]; ?> 
                                          </td>
                                          <td class="equipmentsDetails"
                                            data-id="<?= $values["equipment_id"]; ?>" 
                                          >
                                            <?= date("M-d-Y / h: i A")?>
                                          </td>
                                          <td>
                                            <input type="text" autocomplete="off" 
                                              class="bquantity form-control"
                                              value="<?= $values["equipment_bquantity"]; ?>"
                                              data-id="<?= $values["equipment_id"]; ?>" 
                                              data-equipname="<?= $values["equipment_name"]; ?>"
                                              data-availqty="<?= $values["updatedAvailableQty"]?>"
                                              data-prevalue="<?=$values["equipment_bquantity"]?>"
                                              data-equipprice="<?= $values["equipment_price"]; ?>"
                                              data-equipsubtotal="<?= $subtotal; ?>"
                                              data-equiptotal="<?=$total?>"  
                                            />
                                          </td>
                                          <td><?= $values["equipment_price"]; ?></td>
                                          <td>
                                            <span 
                                              data-id="<?= $values["equipment_id"]; ?>" 
                                              class="subtotal">
                                              <?= $subtotal; ?>
                                            </span>
                                          </td>
                                          <td>
                                            <button type="button"
                                              data-action="remove" 
                                              data-id="<?= $values["equipment_id"]; ?>"  
                                              class="removeEquipmentCart btn-danger btn btn-sm" >
                                              <i class="fa-solid fa-trash"></i>
                                            </button>
                                          </td>
                                        </tr>
                                      </tbody>
                                      <?php
                                    endforeach;  
                                    ?>
                                    <tfoot id="equipmentCartTfoot">
                                      <tr>
                                        <th colspan="4" class="text-right">Total Amount</th>
                                        <th>
                                          <span class="total"><?=$total; ?></span>
                                        </th>
                                      </tr>
                                      <tr>
                                        <th colspan="6" class="text-center">
                                          <button type="button" class="btn btn-light borrowNowBtn border-0" 
                                            data-id="<?= $values["equipment_id"]; ?>" >
                                            <i class="fas fa-spinner fa-spin text-dark loading-spinner nodisplay"></i>
                                            <i class="fa-solid fa-arrow-right text-success arrow-icon"></i> Borrow now
                                          </button>
                                        </th>
                                      </tr>
                                    </tfoot> 
                                    <?php
                                  endif;
                                ?>
                              </table> <!--end table-->
                            </div>
                          </div> <!--end barrow_table-->
                        </div> <!--end barrow-list-card-->
                      </div><!--col-md-9-->
                    </div><!--row-->
                  </div> <!--tabpanel-->
                </div> <!-- end tab content -->
              </div>  <!-- end card body-->
            </div> <!-- end main card -->
          </section> <!--#end section main content-->
        </div> <!--end container-fluid-->
      </div> <!--end main-->
    </div>  <!--end content-wrap-->

    <!--Modal for creating borrower account -->
    <div class="modal fade" id="createBorrowerModal" tabindex="-1" role="create" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="create">Create Account Borrower</h5>
            <button type="button" class="close" onclick="closeModal('#createBorrowerModal')">
              <i class="ti-close"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="row g-0">
              <div class="col-md-4">
                <img src="resources/images/system-photo/default-profile-c.jpg" class="img-thumbnail rounded-circle" 
                  loading="lazy" width="150" alt="borrower"/>
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <input type="hidden" value="<?=(isset($_GET['locationRack']) && !empty($_GET['locationRack'])) ? $_GET['locationRack'] : ''?>" id="currentUri"/>
                  <small>Full Name: <span class="text-danger">*</span></small>
                  <input type="text" class="form-control" id="bfullname"/>
                  <div class="row">
                    <div class="col-md-6">
                      <small>Contact no: <span class="text-danger">*</span></small>
                      <input type="text" class="form-control contactno" id="bcontactno"/>
                    </div>
                    <div class="col-md-6">
                      <small>Role/Position: <span class="text-danger">*</span></small>
                      <select class="form-control" id="bposition">
                        <option selected value="">-- SELECT --</option>
                        <option value="Student">Student</option>
                        <option value="Faculty">Faculty</option>
                        <option value="Staff">Staff</option>
                      </select>
                    </div>
                  </div>
                  <small>Campus/Branch <span class="text-danger">*</span></small>
                  <select id="bcampus" class="form-control">
                    <option selected value="">-- SELECT --</option>
                      <?php
                        $stmt = mysqli_prepare($connection, "SELECT * FROM location_branch");
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while($row = mysqli_fetch_assoc($result)):
                          ?>
                            <option value="<?= $row['id']; ?>">
                              <?= $row['location']; ?>
                            </option>
                          <?php
                        endwhile;
                      ?> 
                    </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-light border-0" 
              onclick="closeModal('#createBorrowerModal')">Close</button>
              <button type="button" class="btn btn-success border-0" id="createBorrowerBtn">
                <i class="fa-solid fa-arrow-right"></i> Submit</button>
            </div>
        </div>
      </div>
    </div>

    <script src="resources/js/borrow.js" defer></script>
    <script defer>
      //PREVENT USER TO LOGIN SAME ACCOUNT IN DIFFERENT DEVICE OR LOCATION
      function check_sesssion_id() {
        var session_id = "<?php echo $_SESSION['session_id']; ?>";
        fetch('app/check_login.php').then(function(response){
          return response.json(); //send data in json format
        }).then(function(responseData){
          if(responseData.output == 'logout'){
            window.location.href = '../auth/logout.php';
          }
        });
      }
      setInterval(function(){
        check_sesssion_id();
      }, 10000);

      function isEmpty(input) {
        return input === "";
      }

      function openModal(modalId) {
        $(modalId).attr('data-backdrop', 'static').modal('show');
      }

      function closeModal(modalId) {
        $(modalId).modal('hide');
      }
    </script>  
  </body>
</html>