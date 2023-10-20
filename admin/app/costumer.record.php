<?php
session_start();
require_once __DIR__ . '/../config/db.connection.php';
ini_set('display_errors',  1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>IMS| Borrower Record</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/png" href="../../goldenminds.favicon.png" sizes="16x16" /> 

       <!--LIBRARY ICONS FILE-->
    <link href="../../vendor/libs/bootstrap/css/icons/themify-icons.css" rel="stylesheet">
    <link href="../../vendor/libs/bootstrap/css/icons/helper.css" rel="stylesheet">
    <link href="../../vendor/libs/icons/all.min.css" rel="stylesheet">
   
    <!--LIBRARY FRAMEWORK[BOOTSTRAP] FILE-->
    <link href="../../vendor/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../vendor/libs/bootstrap/css/bootstrap.animate.css" rel="stylesheet">

    <link href="../../vendor/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="../../vendor/libs/datatables/datatables.min.css" rel="stylesheet"/>
    <link href="../../vendor/libs/datatables/datatables.css" rel="stylesheet"/>

    <!-- CSS CORE FILE -->
    <link defer href="../resources/css/style.css" rel="stylesheet" />    
    <!--LIBRARY FRAMEWORK[JQUERY] FILE-->
    <script src="../../vendor/plugins/jquery/jquery-3.7.0.min.js"></script>
    <!--LIBRARY FRAMEWORK[BOOTSTRAP] FILE-->
    <script src="../../vendor/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../vendor/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="../../vendor/libs/bootstrap/js/preloader/pace.min.js"></script>

    <script src="../../vendor/libs/datatables/datatables.min.js"></script>
    <script src="../../vendor/libs/datatables/datatables.js"></script>

    <!-- JS CORE FILE -->
    <script src="../resources/js/scripts.js" defer></script>
    <link rel="stylesheet"href="../resources/css/return.css"/>
  </head>
  <body style="background: url('../resources/images/system-photo/gmc-bg.png');">
     <?php require_once __DIR__ . '/../components/msgalert.contr.inc.php';?>

    <div class="content-wrap mt-5">
      <div class="main">
        <div class="container">
          <section id="main-content">
            <?php 
              $bid = (isset($_GET['gmcbid']) && !empty($_GET['gmcbid'])) 
                ? $_GET['gmcbid'] : '';
              //get data for this borrower id
              $query = "SELECT c.*, lb.* FROM costumers c
                        INNER JOIN location_branch lb ON c.school_id = lb.id
                        WHERE c.costumer_id = ?";
              $stmt = mysqli_prepare($connection, $query);
              mysqli_stmt_bind_param($stmt, "i", $bid);
              mysqli_stmt_execute($stmt);
              $result_customer = mysqli_stmt_get_result($stmt);
              $costumer = mysqli_fetch_assoc($result_customer);

              //count the number of unique pending equipment for this borrower id
              $query = "SELECT COUNT(DISTINCT equipment_id) AS count FROM barrowed_equipment 
                        WHERE costumer_id = ? AND barrow_status = 1";
              $stmt = mysqli_prepare($connection, $query);
              mysqli_stmt_bind_param($stmt, "i", $bid);
              mysqli_stmt_execute($stmt);
              $result_count = mysqli_stmt_get_result($stmt);
              $pending =  mysqli_fetch_assoc($result_count);

            ?>
            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <img src="../resources/images/system-photo/default-profile-c.jpg" class="card-img-top" alt="customer" loading="lazy"/>
                  <div class="card-body mt-2">
                    <h5 class="card-title"><?=$costumer['name']?></h5>
                    <p class="card-text"></p>
                    <p class="card-text"><?=$costumer['phone_number']?><br/><?=$costumer['location']?></p>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">GMC00<?=$costumer['costumer_id']?></div>
                      </div>
                      <?=$costumer['role_position']?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">Status</div>
                        Active
                      </div>
                      <span class="badge bg-light rounded-pill">
                        <i class="fa-solid fa-circle text-success"></i>
                      </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">Pending</div>
                        Equipment to be return
                      </div>
                      <span class="badge bg-danger rounded-pill">
                        <?=($pending['count']) ? $pending['count'] : '0'?>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-md-8">
                <div class="card table-responsive">
                  <div class="customtab2">
                    <ul class="nav nav-tabs" role="tablist"> 
                      <li class="nav-item"> 
                        <a class="nav-link active" data-toggle="tab" href="#pending" role="tab">
                          <span class="hidden-down">Pending
                            <?php
                              if($pending['count']) {
                                ?><span class="badge bg-secondary"><?=$pending['count']?></span><?php
                              }
                            ?>
                          </span>
                        </a> 
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#allRecord" role="tab">
                          <span class="hidden-down">All Record</span> 
                        </a> 
                      </li>
                    </ul>
                  </div>
                  <div class="tab-content">
                    <div class="tab-pane active" id="pending" role="tabpanel">
                      <div class="card-body">
                        <div class="buttons mt-2 mb-2">
                          <button type="button" 
                            onclick="window.location.href='../return'" 
                            class="btn btn-light text-capitalize border-0">
                            <i class="fa-sharp fa-solid fa-arrow-left"></i> Back
                          </button> 
                          <button type="button" 
                            onclick="window.location.href='costumer.invoice?schoolbranch=<?=$costumer['location']?>&&gmcbid=<?=$costumer['costumer_id']?>'" 
                            class="btn btn-light text-capitalize border-0" >
                            <i class="fas fa-print text-primary"></i> Print
                          </button> 
                          <button type="button" 
                            class="btn btn-light text-capitalize border-0">
                            <i class="far fa-file-pdf text-danger"></i> Export
                          </button> 
                        </div>
                        <table id="tbl_pending" class="table">
                          <thead>
                            <tr>
                              <th style="width: 4%">Image</th>
                              <th style="width: 9%">Equipment</th>
                              <th style="width: 5%">Borrow Qty</th>
                              <th style="width: 5%">Price</th>
                              <th style="width: 5%">Sub total</th>
                              <th style="width: 5%">Return Qty</th>
                            </tr>
                          </thead>
                          <tbody id="equipmentBorrowedTbody">
                            <?php
                              $sql = "SELECT be.*, c.*, e.* 
                                    FROM barrowed_equipment be
                                    INNER JOIN costumers c ON be.costumer_id = c.costumer_id
                                    INNER JOIN equipment e ON be.equipment_id = e.id
                                    WHERE c.costumer_id = ? AND be.barrow_status = 1
                                    ORDER BY be.barrow_date DESC";
                              $stmt = mysqli_prepare($connection, $sql);
                              mysqli_stmt_bind_param($stmt, 'i', $bid);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);

                              $equipmentData = []; //dito i store ang accumulated equipment data
                              $totalAmount = 0; //initialize the total amount

                              if(mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_array($result)) {
                                  $equipmentId = $row['equipment_id'];
                                  $equipmentName = $row['equipment_name'];
                                  $equipmentImg = $row['equipment_img'].'.'.$row['img_extension'];
                                  $barrowQty = $row['barrow_qty'];
                                  $price = $row['price'];
                                  $subtotalAmount = $row['subtotal_amount'];
                                  $borrowId = $row['barrow_id'];

                                  //calculate the total amount for this equipment and add it to the total
                                  $totalAmount += $subtotalAmount;

                                  if(isset($equipmentData[$equipmentName])) {
                                    //if equipment already seen or exist, accumulate quantities and subtotal
                                    $equipmentData[$equipmentName]['barrow_qty'] += $barrowQty;
                                    $equipmentData[$equipmentName]['subtotal_amount'] += $subtotalAmount;
                                  } else {
                                    //first time seeing or not exist this equipment, store its data
                                    $equipmentData[$equipmentName] = [
                                      'barrow_qty' => $barrowQty,
                                      'price' => $price,
                                      'subtotal_amount' => $subtotalAmount,
                                      'barrow_id' =>  $borrowId,
                                      'equipment_id' => $equipmentId,
                                      'equipment_img' => $equipmentImg
                                    ];
                                  }
                                }
                                foreach ($equipmentData as $equipmentName => $data) {
                                  ?>
                                  <tr>
                                    <td>
                                      <img src="../resources/images/equipment-photo-upload/<?=$data['equipment_img']?>" class="img-thumbnail" 
                                      alt="<?=$equipmentName?>" loading="lazy" />
                                    </td>
                                    <td><?= $equipmentName ?></td>
                                    <td>
                                      <span class="borrowqty"
                                        data-bid="<?=$data['barrow_id']?>">
                                        <?= $data['barrow_qty'] ?>
                                      </span>
                                    <td><?= $data['price'] ?></td>
                                    <td>
                                      <span class="subtotal"
                                        data-bid="<?=$data['barrow_id']?>">
                                        <?= $data['subtotal_amount'] ?>
                                      </span>
                                    </td>
                                    <td>
                                      <input type="text" 
                                        class="returnQtyInput form-control" 
                                        data-eid="<?=$data['equipment_id']?>"
                                        data-bid="<?=$data['barrow_id']?>"
                                        data-bqty="<?=$data['barrow_qty']?>"
                                        data-price="<?=$data['price']?>"
                                        data-subtotal="<?=$data['subtotal_amount']?>"
                                        data-total="<?=$totalAmount ?>"
                                      />
                                    </td>
                                  </tr>
                                  <?php
                                }
                              } else {
                                ?>
                                  <tr>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                  </tr>
                                <?php
                              }
                            ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <th colspan="4" class="text-right">Total Amount: </th>
                              <th>
                                <span class="total"><?= $totalAmount ?></span>
                              </th>
                            </tr>
                            <tr>
                              <th colspan="6" class="text-center">
                                <button type="button" class="btn btn-light returnNowBtn" disabled 
                                  data-id="">
                                  <i class="fas fa-spinner fa-spin text-dark loading-spinner nodisplay"></i>
                                  <i class="fa-solid fa-arrow-right text-success arrow-icon"></i> Return now
                                </button>
                                <input type="hidden" class="bid" value="<?=$bid?>"/>
                                <input type="hidden" class="uid" value="<?=$_SESSION['user_id']?>"/>
                              </th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                    <div class="tab-pane" id="allRecord" role="tabpanel">
                      <div class="card-body">
                        <table id="tbl_returned" class="table">
                          <thead>
                            <tr>
                              <th style="width: 1%">Image</th>
                              <th style="width: 8%">Equipment</th>
                              <th style="width: 10%">Borrowed Qty</th>
                              <th style="width: 10%">Returned Qty</th>
                              <th style="width: 10%">Date Returned</th>
                              <th style="width: 10%">Issued By</th>
                            </tr>
                          </thead>
                          <tbody id="equipmentBorrowedTbody">
                            <?php
                              $sql = "SELECT be.*, c.*, e.*, u.* 
                                    FROM barrowed_equipment be
                                    INNER JOIN costumers c ON be.costumer_id = c.costumer_id
                                    INNER JOIN equipment e ON be.equipment_id = e.id
                                    INNER JOIN users u ON be.admin_id = u.id
                                    WHERE c.costumer_id = ? AND be.barrow_status = 0  
                                    ORDER BY be.barrow_date DESC 
                                    LIMIT 10";
                              $stmt = mysqli_prepare($connection, $sql);
                              mysqli_stmt_bind_param($stmt, 'i', $bid);
                              mysqli_stmt_execute($stmt);
                              $result = mysqli_stmt_get_result($stmt);
                              if(mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_array($result)) {
                                  $date_added = date("M-d-Y / H:i A", strtotime($row['return_date'])); 
                                  ?>
                                  <tr>
                                    <td>
                                      <img src="../resources/images/equipment-photo-upload/<?=$row['equipment_img']?>.<?=$row['img_extension']?>" class="img-thumbnail" 
                                      alt="<?=$row['equipment_name']?>" loading="lazy" />
                                    </td>
                                    <td><?=$row['equipment_name']?></td>
                                    <td><?=$row['returned_qty']?></td>
                                    <td><?=$row['returned_qty']?></td>
                                    <td><?=$date_added?></td>
                                    <td><?=$row['acct_name']?></td>
                                  </tr>
                                  <?php
                                }
                              } else {
                                ?>
                                  <tr>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                  </tr>
                                <?php
                              }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div> <!--end container fluid-->
      </div> <!--end div main-->
    </div> <!--end content-wrap-->
    <script src="../resources/js/return.js"></script>
    <script type="text/javascript">
      function isEmpty(field) {
        return field === "";
      }
    </script>
    <?php mysqli_stmt_close($stmt); ?>
  </body>
</html>