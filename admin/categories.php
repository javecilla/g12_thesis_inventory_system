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
    <title>IMS | Categories</title>
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
           <!--testsadasd-->
            <div class="col-lg-4 p-l-0 title-margin-left">
              <div class="page-header">
                <div class="page-title">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="margin-left: -50px;">
                      <a href="categories" class="active"> Categories</a>
                    </li>
                    <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div> <!--end row-->

          <!--FOR MAIN BODY CONTENT-->
          <div class="card">
            <div class="card-body p-b-0">
              <!-- Nav tabs -->
              <div class="customtab2">                  
                <ul class="nav nav-tabs " role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#category" role="tab">
                      <span class="hidden-down">Category Type</span> 
                    </a> 
                  </li>      
                  <li class="nav-item"> 
                    <a class="nav-link" data-toggle="tab" href="#equipmentTYPE" role="tab">
                      <span class="hidden-down">Equipment Type</span>
                    </a> 
                  </li>
                  <li class="nav-item"> 
                    <a class="nav-link" data-toggle="tab" href="#location" role="tab">
                      <span class="hidden-down">Location Rack</span>
                    </a> 
                  </li>
                  <li class="nav-item"> 
                    <a class="nav-link" data-toggle="tab" href="#unitTYPE" role="tab">
                      <span class="hidden-down">Unit Type</span>
                    </a> 
                  </li>
                  <li class="nav-item"> 
                    <a class="nav-link" data-toggle="tab" href="#roomCode" role="tab">
                      <span class="hidden-down">Room Code</span>
                    </a> 
                  </li>
                </ul>
                <!--start tab content-->                   
                <div class="tab-content">
                  <!--category tab-->
                  <div class="tab-pane active" id="category" role="tabpanel">
                    <div class="p-10">
                      <div class="category-card table-responsive"><br/>
                        <div class="card-title">
                          <button type="button" data-toggle="modal" data-target="#addCategory" 
                          class="btn btn-light">
                          <i class="far fa-solid fa-circle-plus text-success"></i> Category
                        </button> 
                        </div><br/> 
                        <!--start category data table-->                                           
                        <table id="tbl_category" class="table categories-data-table table-bordered"> 
                          <thead>
                            <tr>
                              <th scope="col">CID</th>
                              <th scope="col">Category</th>
                              <th scope="col">Status</th>
                              <th scope="col">Date & Time Added</th>
                              <th scope="col">Added by</th>
                              <th scope="col" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_categories" >
                          <?php
                            $query = "SELECT c.*, a.acct_name
                              FROM categories c
                              INNER JOIN users a ON c.user_id = a.id
                              ORDER BY c.date_added DESC";
                            $stmt = mysqli_prepare($connection, $query);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            if(mysqli_num_rows($result) > 0) { 
                              while($data = mysqli_fetch_assoc($result)) { //fetch all data found
                                $date_added = date("M-d-Y / H:i A", strtotime($data['date_added'])); 
                                ?>
                                  <tr>
                                    <td><?= $data['category_id']; ?></td>
                                    <td><?= $data['category_name']; ?></td>
                                    <td>
                                      <?php
                                        if($data['category_status'] == 1) {
                                          echo '<span class="badge badge-success"><a href="app/actions.controller.php?category_id='.$data['category_id'].'&category_status=0" class="text-white" style="font-size: 12px;">AVAILABLE</a></span>';
                                        } else {
                                          echo '<span class="badge badge-danger"><a href="app/actions.controller.php?category_id='.$data['category_id'].'&category_status=1" class="text-white">NOT</a></span>';
                                        }
                                      ?> 
                                    </td>
                                    <td><?= $date_added; ?></td>
                                    <td><?= $data['acct_name']; ?></td>
                                    <td>
                                      <form action="app/actions.controller.php" method="POST">
                                        <button type="button" class="edit_btn btn-warning btn btn-sm"
                                          id="<?= $data['category_id'];?>">
                                          <i class='ti-pencil-alt' >&#xE872;</i>
                                        </button>

                                        <input type="hidden" class="valCategoryId" 
                                          value="<?=$data['category_id']?>" />
                                        <button type="button" class="delCategoryId btn-danger btn btn-sm">
                                          <i class='ti-trash' >&#xE872;</i>
                                        </button>
                                      </form>
                                    </td> 
                                  </tr>
                                <?php
                              }
                            } else {
                              ?>
                                <tr><td colspan="5">No Record Found</td></tr>
                              <?php
                            }
                          ?>
                          </tbody>
                        </table> 
                      </div> <!--end category card-->
                    </div><!--p-10-->
                  </div>

                  <!--equipment type tab-->
                  <div class="tab-pane" id="equipmentTYPE" role="tabpanel">
                    <div class="p-10">
                      <div class="equipment-type-card table-responsive"><br/>
                        <div class="card-title">
                          <button type="button"  data-toggle="modal" data-target="#addEquipType" class="btn btn-light text-capitalize">
                            <i class="far fa-solid fa-circle-plus text-success"></i> Equipment Type
                          </button> 
                        </div><br/> 
                        <!--start data table for equipment type-->                          
                        <table id="tbl_equipType" class="table equipment-type-data-table table-bordered"> 
                          <thead>
                            <tr>
                              <th scope="col">ETID</th>
                              <th scope="col">Equipment Type</th>
                              <th scope="col">Status</th>
                              <th scope="col">Date & Time Added</th>
                              <th scope="col">Added by</th>
                              <th scope="col" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_equipType" >
                            <?php
                              $query = "SELECT et.*, a.acct_name
                                FROM equipment_type et
                                INNER JOIN users a ON et.user_id = a.id 
                                ORDER BY et.date_added DESC";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if (mysqli_num_rows($result) > 0) {
                                while($data = mysqli_fetch_assoc($result)) {
                                  $date_added = date("M-d-Y / H:i A", strtotime($data['date_added'])); 
                                  ?>
                                    <tr>
                                      <td><?= $data['equip_id']; ?></td>
                                      <td><?= $data['equip_type']; ?></td>
                                      <td>
                                        <?php
                                          if ($data['equip_status'] == 1) {
                                            echo '<span class="badge badge-success"><a href="app/actions.controller.php?equipType_id='.$data['equip_id'].'&equip_status=0" class="text-white" style="font-size: 12px;">AVAILABLE</a></span>';
                                          } else {
                                            echo '<span class="badge badge-danger"><a href="app/actions.controller.php?equipType_id='.$data['equip_id'].'&equip_status=1" class="text-white">NOT</a></span>';
                                          }
                                        ?> 
                                      </td>
                                      <td><?= $date_added ?></td>
                                      <td><?= $data['acct_name']; ?></td>
                                      <td>
                                        <form action="app/actions.controller.php" method="POST">
                                          <button type="button" class="edit_equip btn-warning btn btn-sm"
                                            id="<?= $data['equip_id'];?>">
                                            <i class='ti-pencil-alt' >&#xE872;</i>
                                          </button>

                                          <input type="hidden" class="valEquipTypeId" 
                                            value="<?=$data['equip_id']?>" />
                                          <button type="button" class="delEquipTypeId btn-danger btn btn-sm">
                                            <i class='ti-trash' >&#xE872;</i>
                                          </button>
                                        </form>
                                      </td> 
                                    </tr>
                                  <?php
                                }
                              } else { //if so, no data exist or found
                                ?>
                                  <tr><td colspan="6">No Record Found</td></tr>
                                <?php
                              }
                            ?>
                          </tbody>
                        </table>
                      </div> <!--equipment type card-->
                    </div><!--p-10-->
                  </div>

                  <!--location rack tab-->
                  <div class="tab-pane" id="location" role="tabpanel">
                    <div class="p-10">
                      <div class="location-rack-card table-responsive"><br/> 
                        <div class="card-title">
                          <button type="button"  data-toggle="modal" data-target="#addLocationRack" class="btn btn-light text-capitalize">
                            <i class="far fa-solid fa-circle-plus text-success"></i> Location Rack
                          </button> 
                        </div><br/>                               
                        <table id="tbl_location" class="table equipment-type-data-table  table-bordered"> 
                          <thead>
                            <tr>
                              <th scope="col">LRID</th>
                              <th scope="col">Location Rack</th>
                              <th scope="col">Status</th>
                              <th scope="col">Date & Time Added</th>
                              <th scope="col">Added by</th>
                              <th scope="col" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_location" >
                            <?php
                              $query = "SELECT lb.*, a.acct_name
                                FROM location_branch lb
                                INNER JOIN users a ON lb.user_id = a.id 
                                ORDER BY lb.date_added DESC";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0){
                                while($data = mysqli_fetch_assoc($result)) { 
                                  $date_added = date("M-d-Y / H:i A", strtotime($data['date_added'])); 
                                  ?>
                                    <tr>
                                      <td><?= $data['id']; ?></td>
                                      <td><?= $data['location']; ?></td>
                                      <td>
                                        <?php
                                          if ($data['location_status'] == 1) {
                                            echo '<span class="badge badge-success"><a href="app/actions.controller.php?locationRack_id='.$data['id'].'&location_status=0" class="text-white" style="font-size: 12px;">AVAILABLE</a></span>';
                                          } else {
                                            echo '<span class="badge badge-danger"><a href="app/actions.controller.php?locationRack_id='.$data['id'].'&location_status=1" class="text-white">NOT</a></span>';
                                          }
                                        ?> 
                                      </td>
                                      <td><?= $date_added; ?></td>
                                      <td><?= $data['acct_name']; ?></td>
                                      <td>
                                        <form action="app/actions.controller.php" method="POST">
                                          <button type="button" class="edit_locrack btn-warning btn btn-sm"
                                            id="<?= $data['id'];?>">
                                            <i class='ti-pencil-alt' >&#xE872;</i>
                                          </button>

                                          <input type="hidden" class="valLocatonRackId" 
                                            value="<?=$data['id']?>" />
                                          <button type="button" class="delLocationRackId btn-danger btn btn-sm">
                                            <i class='ti-trash' >&#xE872;</i>
                                          </button>
                                        </form>
                                      </td> 
                                    </tr>
                                  <?php
                                }
                              } else {
                                ?>
                                  <tr><td colspan="5">No Record Found</td></tr>
                                <?php
                              }
                            ?>
                          </tbody>
                        </table>
                      </div> <!-- location rack card-->
                    </div><!--p-10-->
                  </div>

                  <!--unit type tab-->
                  <div class="tab-pane" id="unitTYPE" role="tabpanel">
                    <div class="p-10">
                      <div class="unit-type-card table-responsive"><br/>
                        <div class="card-title">
                          <button type="button"  data-toggle="modal" data-target="#addUnitType" class="btn btn-light text-capitalize">
                            <i class="far fa-solid fa-circle-plus text-success"></i> Unit Type 
                          </button>   
                        </div><br/>                                            
                        <table id="tbl_unitType" class="table equipment-type-data-table table-bordered"> 
                          <thead>
                            <tr>
                              <th scope="col">UTID</th>
                              <th scope="col">Location Rack</th>
                              <th scope="col">Status</th>
                              <th scope="col">Date & Time Added</th>
                              <th scope="col">Added by</th>
                              <th scope="col" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_unitType">
                            <?php
                              $query = "SELECT ut.*, a.acct_name
                                FROM equipment_unit ut
                                INNER JOIN users a ON ut.user_id = a.id 
                                ORDER BY ut.date_added DESC";
                              $stmt = mysqli_prepare($connection, $query);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0) {
                                while($data = mysqli_fetch_assoc($result)) { 
                                  $date_added = date("M-d-Y / H:i A", strtotime($data['date_added'])); 
                                  ?>
                                    <tr>
                                      <td><?= $data['id']; ?></td>
                                      <td><?= $data['unit_type']; ?></td>
                                      <td>
                                        <?php
                                          if($data['unit_status'] == 1) {
                                            echo '<span class="badge badge-success"><a href="app/actions.controller.php?unitType_Id='.$data['id'].'&unit_status=0" class="text-white" style="font-size: 12px;">AVAILABLE</a></span>';
                                          } else {
                                            echo '<span class="badge badge-danger"><a href="app/actions.controller.php?unitType_Id='.$data['id'].'&unit_status=1" class="text-white">NOT</a></span>';
                                          }
                                        ?> 
                                      </td>
                                      <td><?= $date_added; ?></td>
                                      <td><?= $data['acct_name']; ?></td>
                                      <td>
                                        <form action="app/actions.controller.php" method="POST">
                                          <button type="button" class="edit_unitType btn-warning btn btn-sm"
                                            id="<?= $data['id'];?>">
                                            <i class='ti-pencil-alt' >&#xE872;</i>
                                          </button>

                                          <input type="hidden" class="valUnitTypeId" 
                                            value="<?=$data['id']?>" />
                                          <button type="button" class="delUnitTypeId btn-danger btn btn-sm">
                                            <i class='ti-trash' >&#xE872;</i>
                                          </button>
                                        </form>
                                      </td> 
                                    </tr>
                                  <?php
                                }
                              } else { //if so, no data found
                                ?>
                                  <tr><td colspan="6">No Record Found</td></tr>
                                <?php
                              }
                            ?>
                          </tbody>
                        </table>
                      </div> <!--unit type card-->  
                    </div><!--p-10-->
                  </div>

                  <!--category tab-->
                  <div class="tab-pane" id="roomCode" role="tabpanel">
                    <div class="p-10">
                      <div class="category-card table-responsive"><br/>
                        <div class="card-title">
                          <button type="button" data-toggle="modal" data-target="#addRoomCode" 
                          class="btn btn-light">
                          <i class="far fa-solid fa-circle-plus text-success"></i> Room Code
                        </button> 
                        </div><br/> 
                        <!--start category data table-->                                           
                        <table id="tbl_roomCode" class="table categories-data-table table-bordered"> 
                          <thead>
                            <tr>
                              <th scope="col">RCID</th>
                              <th scope="col">Room Code</th>
                              <th scope="col">Status</th>
                              <th scope="col">Date & Time Added</th>
                              <th scope="col">Added by</th>
                              <th scope="col" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_roomCode" >
                          <?php
                            $query = "SELECT rc.*, a.acct_name
                              FROM room_code rc
                              INNER JOIN users a ON rc.user_id = a.id
                              ORDER BY rc.date_added DESC";
                            $stmt = mysqli_prepare($connection, $query);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            if(mysqli_num_rows($result) > 0) { 
                              while($data = mysqli_fetch_assoc($result)) { //fetch all data found
                                $date_added = date("M-d-Y / H:i A", strtotime($data['date_added'])); 
                                ?>
                                  <tr>
                                    <td><?= $data['room_code_id']; ?></td>
                                    <td><?= $data['room_code_name']; ?></td>
                                    <td>
                                      <?php
                                        if($data['room_code_status'] == 1) {
                                          echo '<span class="badge badge-success"><a href="app/actions.controller.php?room_code_id='.$data['room_code_id'].'&room_code_status=0" class="text-white" style="font-size: 12px;">AVAILABLE</a></span>';
                                        } else {
                                          echo '<span class="badge badge-danger"><a href="app/actions.controller.php?room_code_id='.$data['room_code_id'].'&room_code_status=1" class="text-white">NOT</a></span>';
                                        }
                                      ?> 
                                    </td>
                                    <td><?= $date_added; ?></td>
                                    <td><?= $data['acct_name']; ?></td>
                                    <td>
                                      <form action="app/actions.controller.php" method="POST">
                                        <button type="button" class="edit_roomcode btn-warning btn btn-sm"
                                          id="<?= $data['room_code_id'];?>">
                                          <i class='ti-pencil-alt' >&#xE872;</i>
                                        </button>

                                        <input type="hidden" class="valRoomCodeId" 
                                          value="<?=$data['room_code_id']?>" />
                                        <button type="button" class="delRoomCodeId btn-danger btn btn-sm">
                                          <i class='ti-trash' >&#xE872;</i>
                                        </button>
                                      </form>
                                    </td> 
                                  </tr>
                                <?php
                              }
                            } else {
                              ?>
                                <tr><td colspan="5">No Record Found</td></tr>
                              <?php
                            }
                          ?>
                          </tbody>
                        </table> 
                      </div> <!--end category card-->
                    </div><!--p-10-->
                  </div>


                </div> <!--end tab content-->
              </div><!--end customtab2-->
            </div><!--card body-->
          </div><!--card-->
        </div><!--container fluid-->
      </div> <!--main-->
    </div>  <!--content-wrap-->

      <div class="modal-handler">
        <!-- POP UP MODAL ADD CATEGORY -->
        <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="addCategory" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <div class="modal-body">
                  <input type="text" name="category_name" class="form-control" placeholder="Category name..." autocomplete="off" required> 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="add_category" class="btn btn-success">Add</button>
                </div>
              </form> 
            </div>
          </div>
        </div>

        <!-- POP UP MODAL EDIT CATEGORY -->
        <div class="modal fade" id="category_edit_accountModal" tabindex="-1" role="edit" aria-labelledby="editCategory" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Category Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <input type="hidden" name="edit_id" id="edit_id"/>
                <div class="modal-body">
                  <input type="text" name="edit_category_name" id="category_name" class="form-control"autocomplete="off"/>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="edit_category_btn" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- POP UP MODAL ADD EQUIPMENT TYPE -->
        <div class="modal fade" id="addEquipType" tabindex="-1" role="dialog" aria-labelledby="addEquipType" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Equipment Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <div class="modal-body">
                  <input type="text" name="equipment_type" class="form-control" placeholder="Equipment type..." autocomplete="off" required> 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="addEquipType" class="btn btn-success">Add</button>          
                </div>
              </form> 
            </div>
          </div>
        </div>

        <!-- POP UP MODAL EDIT EQUIPMENT TYPE -->
        <div class="modal fade" id="equipType_edit_accountModal" tabindex="-1" role="editEquipType2"  aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Equipment Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <input type="hidden" name="editEquip_id" id="editEquip_id"/>
                <div class="modal-body">
                  <input type="text" name="edit_equip_name" id="edit_equip_name" class="form-control"autocomplete="off"/>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="editEquipType" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- POP UP MODAL ADD LOCATION RACK -->
        <div class="modal fade" id="addLocationRack" tabindex="-1" role="dialog" aria-labelledby="addLocationRack" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Location Rack</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <div class="modal-body">
                  <input type="text" name="location_rack" class="form-control" placeholder="Location rack..." autocomplete="off" required> 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="addLocRack" class="btn btn-success">Add</button>
                </div>
              </form> 
            </div>
          </div>
        </div>


        <!-- POP UP MODAL EDIT LOCATION RACK -->
        <div class="modal fade" id="locrack_edit_accountModal" tabindex="-1" role="editLocRack"  aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Location Rack</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <input type="hidden" name="editLocRack_id" id="editLocRack_id"/>
                <div class="modal-body">
                  <input type="text" name="edit_locrack_name" id="edit_locrack_name" class="form-control"autocomplete="off"/>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="editLocRack" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- POP UP MODAL ADD UNIT TYPE -->
        <div class="modal fade" id="addUnitType" tabindex="-1" role="dialog" aria-labelledby="addUnitType" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Unit Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <div class="modal-body">
                  <input type="text" name="unit_type" class="form-control" placeholder="Unit type..." autocomplete="off" required> 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="addUnitType" class="btn btn-success">Add</button> 
                </div>
              </form> 
            </div>
          </div>
        </div>

        <!-- POP UP MODAL EDIT UNIT TYPE -->
        <div class="modal fade" id="unitType_edit_accountModal" tabindex="-1" role="editLocRack"  aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Unit type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <input type="hidden" name="editUnitType_id" id="editUnitType_id"/>
                <div class="modal-body">
                  <input type="text" name="edit_unitType_name" id="edit_unitType_name" class="form-control"autocomplete="off"/>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="editUnitType" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- POP UP MODAL ADD ROOM CODE -->
        <div class="modal fade" id="addRoomCode" tabindex="-1" role="dialog" aria-labelledby="addRoomCode" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Room Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <div class="modal-body">
                  <input type="text" name="room_code_name" class="form-control" placeholder="Room Code name..." autocomplete="off" required> 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="add_room_code" class="btn btn-success">Add</button>
                </div>
              </form> 
            </div>
          </div>
        </div>

        <!-- POP UP MODAL EDIT ROOM CODE -->
        <div class="modal fade" id="editRoomCode" tabindex="-1" role="editRoomCode"  aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Room Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <i class="ti-close">&#xE5CD;</i>
                </button>
              </div>
              <form action="app/actions.controller.php" method="POST">
                <input type="hidden" name="editRoomCodeId" id="editRoomCodeId"/>
                <div class="modal-body">
                  <input type="text" name="editRoomCodeName" id="editRoomCodeName" class="form-control"autocomplete="off"/>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="editRoomCodeBtn" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    <script src="resources/js/categories.js"></script>
    <script defer>
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