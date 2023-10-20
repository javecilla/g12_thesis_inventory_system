<?php
session_start();  
require_once __DIR__ . '/../config/db.connection.php'; 
ini_set('display_errors',  1);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>IMS | Inventory Report</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="icon" type="image/png" href="../../goldenminds.favicon.png" sizes="16x16" /> 
	  <link href="../../vendor/libs/icons/all.min.css" rel="stylesheet">
	  <link href="../../vendor/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	  <link defer href="../resources/css/style.css" rel="stylesheet" />
	  <style type="text/css" media="print">
			/* remove unwanted element that don't need in print	*/
	    @media print{
	      .noprint, .noprint *{
	        display: none!important;
	      }
	    }
	    body {
	    	background: url('../resources/images/system-photo/gmc-bg.png');
	    }
	  </style>
	</head>
<body onload="print()"> <!--onload="print()"-->
	<center>
		<div class="card table-responsive" style="width:100%;"> 
			<div class="card-title">
				<img src="../resources/images/system-photo/gmc.png" class="img-responsive" width="70" />
				<h5>Inventory Report of
					<?=(!empty($_GET['locationRack'])) ? '<br/>'. urldecode($_GET['locationRack']) : 'All Record';?>
					<?=(!empty($_GET['roomCode'])) ? '<br/>Room code: '. urldecode($_GET['roomCode']) : '';?>
					<?=(!empty($_GET['from_date']) && !empty($_GET['to_date'])) ? '<br/>From date: '.date("F d, Y", strtotime($_GET['from_date'])). '<br/>To date: '.date("F d, Y", strtotime($_GET['to_date'])) : '';?> 
				</h5>
				<small class="text-uppercase">Issued by Inventory Admin: <br/>
					<strong><?=$_SESSION['acct_name']?></strong>
				</small>				
			</div><hr/>
      <table id="tblPrint" class="table table-responsive " style="width:100%">
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
	      <tbody id="tbodyPrint">
	      	<?php
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
	                       } else { echo '<i style="font-size: 14px;">Good</i>'; }
	                     ?>
                    </td>
                  </tr>
                <?php
              }
            }
	      	?>                           
	      </tbody>
      </table><br/>
      <button type="button" class="btn btn-light border-0 noprint"
        onclick="back()">
        <i class="fa-sharp fa-solid fa-arrow-left"></i> Back
      </button> 
    </div> <!--end card-->
	</center>
	<script type="text/javascript">
		//window.location.replace();
		function back() {
      const lr = encodeURIComponent('<?=$_GET['locationRack']?>').replace(/%20/g, '+');
      const rc = encodeURIComponent('<?=$_GET['roomCode']?>').replace(/%20/g, '+');
      const fd = '<?=$_GET['from_date']?>';
      const td = '<?=$_GET['to_date']?>';
      window.location.replace(`../report?locationRack=${lr}&roomCode=${rc}&from_date=${fd}&to_date=${td}`);
		}
	</script>
</body>
</html>