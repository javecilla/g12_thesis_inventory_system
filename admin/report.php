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
    <title>IMS | Inventory Report</title>
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
    <div class="content-wrap">
      <div class="main">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-8 p-r-0 title-margin-right">
              <div class="page-header">
                <div class="page-title">
                  <h6 class="clock m-t-30"><?php echo date("M-d-Y")?> /<?php echo date(" h: i A");?></h6>
                </div> 
              </div> 
            </div>
            <div class="col-lg-4 p-l-0 title-margin-left">
              <div class="page-header">
                <div class="page-title">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="margin-left: -90px;">
                      <a href="report" class="active"> Generate Report </a>
                    </li>
                    <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>     
          </div>
          <!-- start main content -->
          <section id="main-content">
            <div class="card">
              <form method="GET" action="<?=$_SERVER['PHP_SELF']?>">
                <div class="row">
                  <div class="col-md-3">
                    <small>LOCATION RACK OF EQUIPMENT</small>
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
                  <div class="col-md-3">
                    <small>ROOM CODE OF EQUIPMENT</small>
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
                    <small>FROM DATE</small><br/>
                    <input type="date" class="form-control" name="from_date" id="fromDate" 
                    value="<?php if(isset($_GET['from_date'])){ echo $_GET['from_date']; } ?>" />
                  </div>
                  <div class="col-md-2">
                    <small>TO DATE</small><br/>
                    <input type="date" class="form-control" name="to_date" id="toDate"
                    value="<?php if(isset($_GET['to_date'])){ echo $_GET['to_date']; } ?>" />
                  </div>
                  <div class="col-md-2">
                    <small>ACTIONS</small><br/>
                    <button type="button" class="btn btn-light border-0" onclick="window.location.href='report'">
                        Reset</button>
                    <button type="submit" class="btn btn-secondary">Filter</button>
                  </div>
                </div> 
              </form><hr/>
              <div class="buttons">
                <button type="button" class="btn btn-light border-0" 
                  id="print" onclick="generateReport('print')" 
                  <?=(empty($_GET)) ? 'disabled' : ''?>> 
                  <i class="fas fa-solid fa-print text-primary"></i> Print
                </button>
                <button type="button" class="btn btn-light border-0" 
                  id="pdf" onclick="generateReport('pdf')" 
                  <?=(empty($_GET)) ? 'disabled' : ''?>> 
                  <i class="fas fa-solid fa-file-pdf text-danger"></i> PDF
                </button>
                <button type="button" class="btn btn-light border-0" 
                  id="excel" onclick="generateReport('excel')" 
                  <?=(empty($_GET)) ? 'disabled' : ''?>> 
                  <i class="fas fa-regular fa-file-excel text-success"></i> Excel
                </button>
              </div><br/>
              <div class="table-responsive">
                <table id="tblEquipmentsRecord" class="table table-responsive" style="width:100%; display:nowrap;">
                  <thead>
                    <tr>
                      <th style="width: 1%;">EID</th>
                      <th style="width: 14%;">Equipment Name</th>
                      <th style="width: 8%;">Category</th>
                      <th style="width: 14%;">Type</th>
                      <th style="width: 6%;">Unit</th>
                      <th style="width: 2%;">Stock</th>
                      <th style="width: 2%;">Inused</th>
                      <th style="width: 2%;">Qty</th>
                      <th style="width: 3%;">Condition</th>
                    </tr>
                  </thead>
                  <tbody id="tbodyEquipmentsRecord">
                    <?php
                      if(!empty($_GET)) {
                        $locationRack = (isset($_GET['locationRack'])) ? $_GET['locationRack'] : '';
                        $roomCode = (isset($_GET['roomCode'])) ? $_GET['roomCode'] : '';
                        $from_date = (isset($_GET['from_date'])) ? $_GET['from_date'] : '';
                        $to_date = (isset($_GET['to_date'])) ? $_GET['to_date'] : '';
                        $query = "SELECT e.*, 
                            c.category_name, 
                            t.equip_type, 
                            l.location, 
                            u.unit_type,
                            r.room_code_name
                          FROM equipment e 
                          INNER JOIN categories c ON e.category_id = c.category_id 
                          INNER JOIN equipment_type t ON e.type_id = t.equip_id
                          INNER JOIN location_branch l ON e.location_id = l.id
                          INNER JOIN equipment_unit u ON e.unit_id = u.id
                          INNER JOIN room_code r ON e.roomcode_id = r.room_code_id
                          WHERE 1"; // 'WHERE 1' is used as a placeholder for your filters

                        //empty array to store ang where clause conditions
                        $conditions = []; 
                        //empty string na magre represent the types of parameters na kailangan i bound
                        $paramTypes = ''; 
                        //empty array to store ang parameters depende kung ilan ang conditions
                        $params = [];

                        //for single filtering
                        if(!empty($locationRack)) {
                          $paramTypes .= 's';
                          $conditions[] = "l.location = ?";
                          $params[] = &$locationRack;
                        }

                        if(!empty($roomCode)) {
                          $paramTypes .= 's';
                          $conditions[] = "r.room_code_name = ?";
                          $params[] = &$roomCode;
                        }

                        //double filtering
                        if(!empty($from_date) && !empty($to_date)) {
                          $paramTypes .= 'ss';
                          $conditions[] = "e.date_added BETWEEN ? AND ?";
                          $params[] = &$from_date;
                          $params[] = &$to_date;
                        }

                        if(!empty($locationRack) && !empty($roomCode)) {
                          $paramTypes .= 'ss';
                          $conditions[] = "l.location = ? AND r.room_code_name = ?";
                          $params[] = &$locationRack;
                          $params[] = &$roomCode;
                        }

                        //quadrapol filtering
                        if(!empty($locationRack) && !empty($roomCode) && !empty($from_date) && !empty($to_date)) {
                          $paramTypes .= 'ssss';
                          $conditions[] = "l.location = ? AND r.room_code_name = ? AND e.date_added BETWEEN ? AND ?";
                          $params[] = &$locationRack;
                          $params[] = &$roomCode;
                          $params[] = &$from_date;
                          $params[] = &$to_date;
                        }

                        //check if any conditions are added, and append them to the query
                        if(!empty($conditions)) {
                          $query .= " AND " . implode(" AND ", $conditions);
                        }

                        $stmt = mysqli_prepare($connection, $query);
                        //combine the parameter types and values
                        array_unshift($params, $paramTypes);

                        //call the mysqli_stmt_bind_param() para sa dynamic na binding parameter
                        //then emerge lahat ng parameters sa $param[] array
                        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $params));
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if(mysqli_num_rows($result) > 0) {
                          while($equipment = mysqli_fetch_assoc($result)) {
                            ?>
                              <tr>
                                <td><?=$equipment['id']?></td>
                                <td><?=$equipment['equipment_name']?></td>
                                <td><?=$equipment['category_name']?></td>
                                <td><?=$equipment['equip_type']?></td>
                                <td><?=$equipment['unit_type']?></td>
                                <td><?=$equipment['stock']?></td>
                                <td><?=$equipment['in_use']?></td>
                                <td><?=$equipment['quantity']?></td>
                                <td>
                                <?php
                                   if($equipment['quantity'] < $equipment['conditions']) {
                                      echo '<i style="font-size: 14px;">Critical</i>';
                                   } else {
                                      echo '<i style="font-size: 14px;">Good</i>';
                                   }
                                ?>
                                </td>
                              </tr>
                            <?php
                          }
                        }
                      } else {
                        $query = "SELECT e.*, 
                            c.category_name, 
                            t.equip_type, 
                            l.location, 
                            u.unit_type
                          FROM equipment e 
                          INNER JOIN categories c ON e.category_id = c.category_id 
                          INNER JOIN equipment_type t ON e.type_id = t.equip_id
                          INNER JOIN location_branch l ON e.location_id = l.id
                          INNER JOIN equipment_unit u ON e.unit_id = u.id
                          ORDER BY e.date_added ASC";
                        $stmt = mysqli_prepare($connection, $query);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if(mysqli_num_rows($result) > 0) {
                          while($equipment = mysqli_fetch_assoc($result)) {
                            ?>
                              <tr>
                                <td><?=$equipment['id']?></td>
                                <td><?=$equipment['equipment_name']?></td>
                                <td><?=$equipment['category_name']?></td>
                                <td><?=$equipment['equip_type']?></td>
                                <td><?=$equipment['unit_type']?></td>
                                <td><?=$equipment['stock']?></td>
                                <td><?=$equipment['in_use']?></td>
                                <td><?=$equipment['quantity']?></td>
                                <td>
                                <?php
                                   if($equipment['quantity'] < $equipment['conditions']) {
                                      echo '<i style="font-size: 14px;">Critical</i>';
                                   } else {
                                      echo '<i style="font-size: 14px;">Good</i>';
                                   }
                                ?>
                                </td>
                              </tr>
                            <?php
                          }
                        }
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div> <!--end card-->
          </section>
        </div>
      </div>
    </div>
    
    <script src="resources/js/report.js" defer></script>
    <script defer>
      function isEmpty(field) {
        return field === '';
      }
    
      //generate report
      function generateReport(actions) {
        //window.print();
        const lr = encodeURIComponent($('#locationRack').val()).replace(/%20/g, '+');
        const rc = encodeURIComponent($('#roomCode').val()).replace(/%20/g, '+');
        const fd = $('#fromDate').val();
        const td = $('#toDate').val();
        
        //check selected action button
        (actions.match(/print|pdf|excel/)) 
        ? window.location.href=`app/${actions}.report?locationRack=${lr}&roomCode=${rc}&from_date=${fd}&to_date=${td}`
        : alert("Something went wrong!");
      }

      //check loggedin user, prevent login one acc in two diff device
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