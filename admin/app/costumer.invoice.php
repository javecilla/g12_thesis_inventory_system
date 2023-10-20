<?php
declare(strict_types = 1);
session_start();  
require_once __DIR__ . '/../config/db.connection.php'; 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>IMS | Inventory Invoice</title>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="icon" type="image/png" href="../../goldenminds.favicon.png" sizes="16x16" /> 
    <link href="../../vendor/libs/icons/all.min.css" rel="stylesheet">
    <link href="../../vendor/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link defer href="../resources/css/style.css" rel="stylesheet" />
    <style type="text/css" media="print">
      @media print{
        .noprint, .noprint *{
          display: none!important;
        }
      }
    </style>
    <style type="text/css">
      body {
        background: url('../resources/images/system-photo/gmc-bg.png');
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
      } .noedit {
        border: none;
        pointer-events: none;
      } .article {
        text-align: justify;
        margin: 0 auto;
        max-width: 800px;
      } .paragraph {
        text-align: justify;
        text-indent: 0;
        margin-bottom: 20px;
      } .signed, .issueby {
        text-align: right;
        font-style: italic;
      }
    </style>
  </head>
  <body onload="print()">
    <section id="main-content">
      <div class="container mb-5 mt-3">
        <div class="card">
          <?php
            $bid = (isset($_GET['gmcbid']) && !empty($_GET['gmcbid'])) ? $_GET['gmcbid'] : ''; 
            $query = "SELECT c.*, lb.* 
                      FROM costumers c
                      INNER JOIN location_branch lb ON c.school_id = lb.id
                      WHERE c.costumer_id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "i", $bid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $costumer = mysqli_fetch_assoc($result); 
          ?>
          <center>
            <img src="../resources/images/system-photo/gmc.png" class="img-responsive" width="70" />
            <h4>Inventory System</h4>
            <h5><?=$costumer['location']?></h5>
            <span>Name: <b><?=$costumer['name']?></b></span>
            <span>Contact Number: <b><?=$costumer['phone_number']?></b></span>
            <br/>
            <span>Issued by: <b><?=$_SESSION['acct_name']?></b></span>
            <span>Payment Date: <b><?= date("M-d-Y / h: i A")?></b></span>
          </center>
          <div class="my-2 table-responsive p-5">
            <table class="table">
              <thead>
                <tr>
                  <th style="width: 8%">BID</th>
                  <th style="width: 15%">Equipment Name</th>
                  <th style="width: 10%">Equipment Price</th>
                  <th style="width: 10%">Barrow Quantity</th>
                  <th style="width: 10%">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $sql = "SELECT be.*, c.*, e.* 
                      FROM barrowed_equipment be
                      INNER JOIN costumers c ON be.costumer_id = c.costumer_id
                      INNER JOIN equipment e ON be.equipment_id = e.id
                      WHERE be.costumer_id = ? AND be.barrow_status = 1
                      ORDER BY be.barrow_date DESC";
                  $stmt = mysqli_prepare($connection, $sql);
                  mysqli_stmt_bind_param($stmt, 'i', $bid);
                  mysqli_stmt_execute($stmt);
                  $result = mysqli_stmt_get_result($stmt);

                  $equipmentData = []; //dito i store ang accumulated equipment data
                  $totalAmount = 0; //initialize the total amount

                  if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_array($result)) {
                      $equipmentName = $row['equipment_name'];
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
                        ];
                      }
                    }
                    foreach($equipmentData as $equipmentName => $data) {
                      ?>
                      <tr>
                        <td>GMC00<?=$data['barrow_id']?></td>
                        <td><?= $equipmentName ?></td>
                        <td><?= $data['price'] ?></td>
                        <td><?= $data['barrow_qty'] ?></td>
                        <td><?= $data['subtotal_amount'] ?></td>
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
                    </tr>
                    <?php
                  }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="4" class="text-right">Total Amount: </th>
                  <th class="text-right">
                    <span class="total"><?= $totalAmount ?></span>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
            
          <div class="article">
            <p class="paragraph">
              By borrowing equipment from Golden Minds, I agree to pay for any damages or losses incurred during my use of the equipment, as well as any failure to return it in a timely manner. I understand that failure to pay for any damages or losses may result in sanctions, including but not limited to legal action or the suspension of borrowing privileges.
            </p>
            <p class="paragraph">
              Additionally, I acknowledge that the equipment is the property of the Golden Minds and that I am responsible for its safekeeping and appropriate use. I agree to comply with all applicable laws and regulations, as well as any specific terms and conditions set forth by the establishment.
            </p>
            <p class="paragraph">
              By signing below, I acknowledge that I have read, understood, and agreed to all of the above terms and conditions.
            </p>
            <p class="signed">Signature: ___________________</p>
          </div>
          <button type="button" 
            class="btn btn-light border-0 noprint"
            onclick="window.location.replace('costumer.record?gmcbid=<?=$bid?>')">
            <i class="fa-sharp fa-solid fa-arrow-left"></i> Back
          </button> 
        </div>
      </div>
    </section>
  </body>
</html>