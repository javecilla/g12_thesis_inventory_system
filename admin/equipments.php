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
    <title>IMS | Equipment Management</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 

    <link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>

    <style type="text/css" defer>
      /*costumizing tooltip*/
      .tooltip-inner {
        background-color: #E5E7E9 !important;
        color: #424949;
      }
      .equipname {
        text-indent: 8px!important;
      }
      .nodrop {
        cursor: no-drop!important;
      }
    </style>
   </head>
   <body style="background: url('resources/images/system-photo/gmc-bg.png');">
    <?php require_once __DIR__ . '/components/ui.side-nav.php';?>
    <?php require_once __DIR__ . '/components/ui.top-nav.php';?>
    <?php require_once __DIR__ . '/components/js.file-links.inc.php';?>
    <?php require_once __DIR__ . '/components/msgalert.contr.inc.php';?>

    <div class="content-wrap">
      <div class="main">
        <div class="container"><br/>
          <?php require_once __DIR__ . '/components/lmsgalert.inc.php';?>
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
                      <a href="equipments" class="active"> Equipment Management</a>
                    </li>
                    <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div> <!--end row-->

          <section id="main-content">
            <div class="card table-responsive">
              <div class="card-title">
                <button type="button"  data-target="#ADDequipmentMODAL" data-toggle="modal" 
                class="btn btn-light border-0">
                <i class="far fa-solid fa-circle-plus text-success"></i> Add Equipment
              </button><hr/>
              </div> 
              
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
                    <button type="button" class="btn btn-light border-0" onclick="window.location.href='equipments'">Reset</button>
                    <button type="submit" class="btn btn-secondary">Filter</button>
                  </div>
                </div>
              </form><br/>  
              <!--start data table for equipments-->                 
              <table id="tbl_equipment" class="table" >    
                <thead>
                  <tr class="tbl-heading">
                    <th style="width: 3%;">EID</th>
                    <th style="width: 17%;">&nbsp;&nbsp;Equipment Name</th>
                    <th style="width: 10%;">&nbsp;&nbsp;Category</th>
                    <th style="width: 5%;">Stock</th>
                    <th style="width: 6%;">Inuse</th>
                    <th style="width: 6%;">Quantity</th>
                    <th style="width: 8%;"> Condtion</th>
                    <th style="width: 10%;"> Status</th>
                    <th class="text-center" style="width: 13%;">Action</th>
                  </tr>
                </thead>
                <tbody id="tbody_equipment">   
                  <?php 
                    if(!empty($_GET)) {
                      //Initialize yung base SQL query without any filtering
                      $query = "SELECT e.*, c.category_name, t.equip_type, l.location, u.unit_type, rc.room_code_name
                        FROM equipment e 
                        INNER JOIN categories c ON e.category_id = c.category_id 
                        INNER JOIN equipment_type t ON e.type_id = t.equip_id
                        INNER JOIN location_branch l ON e.location_id = l.id
                        INNER JOIN room_code rc ON e.roomcode_id = rc.room_code_id
                        INNER JOIN equipment_unit u ON e.unit_id = u.id";

                      //initialize tong empty array para dito i store and WHERE clause conditions
                      $conditions = []; 

                      //initialize naman tong empty array to store yung mga parameters para i bind ang WHERE clause conditions
                      $params = [];

                      //initialize bind types to empty string
                      $types = "";

                      //check if locationRack is set
                      if(isset($_GET['locationRack']) && !empty($_GET['locationRack'])) {
                        $filterByLocationRack = $_GET['locationRack'];
                        //add this location rack in a $condition[] for WHERE clause condition
                        $conditions[] = "l.location = ?";
                        $params[] = $filterByLocationRack;
                        $types .= "s";
                      }

                      //check if roomCode is set
                      if(isset($_GET['roomCode']) && !empty($_GET['roomCode'])) {
                        $filterByRoomCode = $_GET['roomCode'];
                        //add this room code in a $condition[] for WHERE clause condition
                        $conditions[] = "rc.room_code_name = ?";
                        $params[] = $filterByRoomCode;
                        $types .= "s";
                      }

                      //check if condition is not empty, then i a-add kung ano man ang laman ng $condition[] sa WHERE clause to the query
                      if(!empty($conditions)) {
                        $query .= " WHERE " . implode(" AND ", $conditions);
                      }

                      $query .= " ORDER BY e.date_added DESC";
                      $stmt = mysqli_prepare($connection, $query);

                      //i-bind yung mga parameters based doon sa the $conditions[]
                      if(!empty($conditions)) {
                        $types = str_repeat('s', count($params));
                        $bindParams = array_merge([$stmt, $types], $params);
                        $refs = [];
                        foreach ($bindParams as $key => $value) {
                          $refs[$key] = &$bindParams[$key];
                        }
                        //call the bind_param() function to bind yung mga referrence
                        call_user_func_array('mysqli_stmt_bind_param', $refs);
                      }

                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);
                      if(mysqli_num_rows($result) > 0){ //check data from result
                        while($data = mysqli_fetch_assoc($result)) {
                          ?>
                          <tr>
                              <td class="equipment_id"><?=$data['id'];?></td>
                              <td data-toggle="tooltip" data-placement="left" title="<?=$data['location']?>" >
                                <?=$data['equipment_name'];?>
                                <!-- hidden price of equipment -->
                                <input type="hidden" id="equipmentPriceTbl<?=$data['id'];?>"
                                  value="<?=$data['price'];?>"/>
                                <!-- hidden total amount of equipment -->
                                <input type="hidden" id="equipmentTotalAmtTbl<?=$data['id'];?>"
                                  value="<?=$data['amount'];?>"/>
                              </td>
                              <td>&nbsp;&nbsp;<?=$data['category_name'];?></td>
                              <td>&nbsp;&nbsp;<?=$data['stock'];?></td>
                              <td>&nbsp;&nbsp;<?=$data['in_use'];?></td>
                              <td class="td-quantiy">&nbsp;&nbsp;<?=$data['quantity'];?></td>
                              <td>
                                <?php
                                /*check if available quantity in db is mababa sa conditions, ex: 10then
                                set the conditon into [critical] otherwise if not [good]
                                5 < 10 
                                */

                                if($data['quantity'] < $data['conditions']) {
                                  echo '<span class="badge badge-danger">Critical</span>';
                                } else {
                                  echo '<span class="badge badge-success">Good</span>';
                                }
                                ?>
                                <!-- hidden condition of equipment -->
                                <input type="hidden" value="<?=$data['conditions']?>" 
                                id="equipmentCondition<?=$data['id']?>"/>
                              </td>
                              <td class="td-quantiy">
                                <?php
                                if($data['quantity'] < $data['conditions']) { //not available
                                  echo '<span class="badge badge-danger">NOT
                                      </span>';
                                      
                                    } else { //available
                                      echo '<span class="badge badge-success">Available
                                      </span>';
                                    }
                                    // if($data['status'] == 1) { //active
                                    //   echo '<span class="badge badge-success">
                                    //     <a href="app/actions.controller.php?equipment_id='.$data['id'].'&equipment_status='.($data['status'] == 1 ? 0 : 1).'" class="text-white">Available
                                    //     </a>
                                    //   </span>';
                                    // } else { //inactive
                                    //   echo '<span class="badge badge-danger">
                                    //     <a href="app/actions.controller.php?equipment_id='.$data['id'].'&equipment_status='.($data['status'] == 1 ? 0 : 1).'" class="text-white">NOT
                                    //     </a>
                                    //   </span>';
                                    // }
                                  ?> 
                              </td>
                              <td class="td-action">
                                <form action="app/actions.controller.php" method="POST">
                                  <!--view button-->
                                  <button type="button" class="view-btn btn btn-primary btn-sm"
                                    data-id="<?=$data['id']?>"
                                    data-action="view">
                                    <i class='ti-eye view-icon'>&#xE872;</i>
                                  </button>

                                  <!--edit button-->
                                  <button type="button" class="edit-btn btn btn-warning btn-sm"
                                    data-id="<?=$data['id']?>" 
                                    data-action="edit">
                                    <i class='ti-pencil-alt edit-icon'>&#xE872;</i>
                                  </button>

                                  <!--delete button-->
                                  <button type="button" class="delete-btn btn btn-danger btn-sm m-r-10"
                                    data-id="<?=$data['id']?>">
                                    <i class='ti-trash delete-icon'>&#xE872;</i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                          <?php
                        }
                      } 
                    } //end if
                    else {
                      $query = "SELECT e.*, c.category_name, t.equip_type, l.location, u.unit_type
                        FROM equipment e 
                        INNER JOIN categories c ON e.category_id = c.category_id 
                        INNER JOIN equipment_type t ON e.type_id = t.equip_id
                        INNER JOIN location_branch l ON e.location_id = l.id
                        INNER JOIN equipment_unit u ON e.unit_id = u.id
                        ORDER BY e.date_added DESC";
                      $stmt = mysqli_prepare($connection, $query);
                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);                          
                      if(mysqli_num_rows($result) > 0) {
                        while($data = mysqli_fetch_assoc($result)) { 
                          ?> 
                            <tr>
                              <td class="equipment_id"><?=$data['id'];?></td>
                              <td data-toggle="tooltip" data-placement="left" title="<?=$data['location']?>" >
                                <?=$data['equipment_name'];?>
                                <!-- hidden price of equipment -->
                                <input type="hidden" id="equipmentPriceTbl<?=$data['id'];?>"
                                  value="<?=$data['price'];?>"/>
                                <!-- hidden total amount of equipment -->
                                <input type="hidden" id="equipmentTotalAmtTbl<?=$data['id'];?>"
                                  value="<?=$data['amount'];?>"/>
                              </td>
                              <td>&nbsp;&nbsp;<?=$data['category_name'];?></td>
                              <td>&nbsp;&nbsp;<?=$data['stock'];?></td>
                              <td>&nbsp;&nbsp;<?=$data['in_use'];?></td>
                              <td class="td-quantiy">&nbsp;&nbsp;<?=$data['quantity'];?></td>
                              <td>
                                <?php
                                /*check if available quantity in db is mababa sa conditions, ex: 10then
                                set the conditon into [critical] otherwise if not [good]
                                5 < 10 
                                */

                                if($data['quantity'] < $data['conditions']) {
                                  echo '<span class="badge badge-danger">Critical</span>';
                                } else {
                                  echo '<span class="badge badge-success">Good</span>';
                                }
                                ?>
                                <!-- hidden condition of equipment -->
                                <input type="hidden" value="<?=$data['conditions']?>" 
                                id="equipmentCondition<?=$data['id']?>"/>
                              </td>
                              <td class="td-quantiy">
                                <?php
                                if($data['quantity'] < $data['conditions']) { //not available
                                  echo '<span class="badge badge-danger">NOT
                                        </a>
                                      </span>';
                                      
                                    } else { //available
                                      echo '<span class="badge badge-success">Available
                                      </span>';
                                    }
                                  ?> 
                              </td>
                              <td class="td-action">
                                <form action="app/actions.controller.php" method="POST">
                                  <!--view button-->
                                  <button type="button" class="view-btn btn btn-primary btn-sm"
                                    data-id="<?=$data['id']?>"
                                    data-action="view">
                                    <i class='ti-eye view-icon'>&#xE872;</i>
                                  </button>

                                  <!--edit button-->
                                  <button type="button" class="edit-btn btn btn-warning btn-sm"
                                    data-id="<?=$data['id']?>" 
                                    data-action="edit">
                                    <i class='ti-pencil-alt edit-icon'>&#xE872;</i>
                                  </button>

                                  <!--delete button-->
                                  <button type="button" class="delete-btn btn btn-danger btn-sm m-r-10"
                                    data-id="<?=$data['id']?>">
                                    <i class='ti-trash delete-icon'>&#xE872;</i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                          <?php
                        }
                      } else {  //if so no data found, show this message
                        ?>
                          <tr><td colspan="13">No Record Found</td></tr>
                        <?php
                      }
                    }
                  ?>
                </tbody>
              </table>
            </div><!--card body-->
          </section><!--end section main content-->

          <div class="modal-handler">
            <!--ADD EQUIPMENT MODAL FORM--->
            <div class="modal fade" id="ADDequipmentMODAL" tabindex="-1" role="add"  aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">ADD NEW EQUIPMENT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <i class="ti-close">&#xE5CD;</i>
                    </button>
                  </div>
                  <form action="app/actions.controller.php" method="POST" 
                    enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-body">
                      <!--image interface-->
                      <div class="img-container d-flex justify-content-center align-items-center">  
                        <img src="resources/images/system-photo/upload.jpg" id="pImage" style="width: 200px; height: 130px;" />
                      </div> 

                      <!--input equipment image-->
                      <small>Upload Equipment Image</small><br/>
                      <input type="file" accept="images/png, jpg, jpeg" 
                        id="input_equipment_img" class="form-control m-b-5 image equipment_img"
                      /> 

                      <!--equipment name--> 
                      <small>Enter Equipment Name <span class="text-danger"><b>*</b></span></small><br/>
                      <input type="text" id="equipmentName" class="form-control m-b-5" 
                        placeholder="Equipment Name" autocomplete="off" required 
                      />

                      <div class="row">
                        <div class="col-md-6">
                          <!--equipment type selection-->
                          <small>Select type of equipment <span class="text-danger"><b>*</b></span></small><br/>
                          <select id="equipmentType_id" class="text-muted form-control m-b-5" >
                            <option selected value="">-- SELECT --</option>
                            <?php
                              $query = "SELECT * FROM equipment_type  WHERE equip_status = 1";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0) {
                                foreach($result as $equipment_type) {
                                  ?>
                                    <option value="<?= $equipment_type['equip_id']; ?>">
                                      <?= $equipment_type['equip_type']; ?>
                                    </option>
                                  <?php
                                }
                              } else { echo "No data found."; }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <!--category selection-->
                          <small>Select category of equipment <span class="text-danger"><b>*</b></span></small><br/>
                          <select id="category_id" class="text-muted form-control m-b-5" >
                            <option selected value="">-- SELECT --</option>
                            <?php
                              $query = "SELECT * FROM categories WHERE category_status = 1";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0) {
                                foreach ($result as $category) {
                                  ?>
                                    <option value="<?= $category['category_id']; ?>">
                                      <?= $category['category_name']; ?>
                                    </option>
                                  <?php
                                }
                              } else { echo "No data found."; }
                            ?>
                          </select> 
                        </div>
                      </div>

                      <!--location rack selection-->
                      <small>Select location rack of equipment <span class="text-danger"><b>*</b></span></small><br/>
                      <select id="equipmentLocation_id" class="text-muted form-control m-b-5" >
                        <option selected value="">-- SELECT --</option>
                        <?php
                          $query = "SELECT * FROM location_branch WHERE location_status = 1";
                          $stmt = mysqli_prepare($connection, $query);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);
                          if(mysqli_num_rows($result) > 0) {
                            foreach ($result as $location_rack) {
                              ?>
                                <option value="<?= $location_rack['id']; ?>">
                                  <?= $location_rack['location']; ?>
                                </option>
                              <?php
                            }
                          } else { echo "No data found."; }
                        ?>
                      </select>

                      <div class="row">
                        <div class="col-md-6">
                          <!--room code selection-->
                          <small>Select room code of equipment <span class="text-danger"><b>*</b></span></small><br/>
                          <select id="equipmentRoomCode_id" class="text-muted form-control m-b-5" >
                            <option selected value="">-- SELECT --</option>
                            <?php
                              $query = "SELECT * FROM room_code WHERE room_code_status = 1";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0) {
                                foreach ($result as $roomcode):
                                  ?>
                                    <option value="<?= $roomcode['room_code_id']; ?>">
                                      <?= $roomcode['room_code_name']; ?>
                                    </option>
                                  <?php
                                endforeach;
                              } else { echo "No data found."; }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <!--unit types selection-->
                          <small>Select unit type of equipment</small><br/>
                          <select id="equipmentUnit_id" class="text-muted form-control m-b-5">
                            <option selected value="">-- SELECT --</option>
                            <?php
                              $query = "SELECT * FROM equipment_unit WHERE unit_status = 1";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0) {
                                foreach ($result as $unit_type) {
                                  ?>
                                    <option value="<?= $unit_type['id']; ?>">
                                      <?= $unit_type['unit_type']; ?>
                                    </option>
                                  <?php
                                }
                              } else { echo "No data found."; }
                            ?>
                          </select>
                        </div>
                      </div>

                      <div class="row">
                        <!--equipment price-->
                        <div class="col-4">
                          <small>Price of Equipment <span class="text-danger"><b>*</b></span></small><br/>
                          <input type="text" name="equipmentPrice" 
                            id="price"
                            placeholder="Price" class="form-control calc inputU" autocomplete="off"   
                          />
                        </div>
                         <!--number of stock-->
                        <div class="col-4">
                          <small>Number of Stock <span class="text-danger"><b>*</b></span></small><br/>
                          <input type="text" name="numStock" 
                            id="stock"
                            placeholder="Stock" class="form-control calc inputU" 
                            autocomplete="off"   
                          />
                        </div>
                        <div class="col-4">
                          <small>Set Condition <span class="text-danger"><b>*</b></span></small><br/>
                          <input type="text" name="condition" 
                            id="condition" class="form-control inputU" 
                            placeholder="Condition" autocomplete="off"  
                          />
                        </div>
                      </div>

                      <div class="row">
                        <!--available quantity-->
                        <div class="col-6">
                          <small>Available Quantity</small><br/>
                          <input type="number" id="availQuantity"
                            placeholder="0" class="form-control" readonly 
                            
                          /> 
                        </div>
                        <!--total ammount-->
                        <div class="col-6">
                          <small>Total Amount </small><br/>
                          <input type="number" id="totalAmount"
                            placeholder="0" class="form-control" readonly 
                          />
                        </div>
                      </div>
                    </div><!--close modal body-->
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="button" class="addEquipmentBtn btn btn-success">Submit</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>                           

            <!--VIEW GENERAL EQUIPMENT MODAL FORM -->
            <div class="modal fade" id="VIEWequipmentMODAL" tabindex="-1" role="view" aria-hidden="true">
              <br/><br/><br/><br/><br/><br/><br/>
              <div class="modal-dialog modal-lg mt-5">
                <div class="modal-content">
                  <form action="app/actions.controller.php" method="POST">
                  <div class="modal-header">
                    <h5 class="modal-title"><span id="modalTitleAction"></span> EQUIPMENT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <i class="ti-close" aria-hidden="true"> </i>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="equipmentVIEW_data">
                      <!--data come from server ajax request-->
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                      Close
                    </button>
                    <button type="button" 
                      class="btn btn-success updateEquipmentBtn" 
                      id="updateEquipmentBtn" 
                      style="display: none">Update
                    </button>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div> <!--end modal handler-->
          
        </div><!--container fluid-->
      </div> <!--div main-->
    </div> <!--content wrap--> 

    <script src="resources/js/equipments.js"></script>  
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
    </script> 
  </body>
</html>
