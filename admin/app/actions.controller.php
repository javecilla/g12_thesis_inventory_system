<?php

session_start();
require_once __DIR__ . '/../config/db.connection.php';
require_once __DIR__ . '/functions.php';
ini_set('display_errors',  1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$query = "SELECT * FROM users WHERE uname = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);
$user_id = $row['id'];


/********************************************************
 *  ©JEROME AVECILLA -> ICT 12 DIGNIFIED S.Y 2022-2023
 *******************************************************/

/***ACTIONS FOR LOGIN ***/

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
  //retrieve and sanitize user inputs
  $username = mysqli_real_escape_string($connection, strtolower($_POST['username']));
  $password = mysqli_real_escape_string($connection, $_POST['password']);

  if($username === "" || $password === "") {
    $_SESSION['resultMessage'] = "All fields is required!!";
    $_SESSION['resultMessageCode'] = "warning";
    $_SESSION['actionPerform'] = "login";       
  } else {
    //function to validate user against database
    #src code can find in app folder -> functions.php
    validate_login_user($username, $password);
  }

  //header("Location: ../debugging-page");
  header("Location: ../../auth/login");
  exit();     
}

/***ACTIONS FOR ADMIN PROFILE ***/

else if(isset($_POST['changeProfileBtn'])) {
  $image_name = mysqli_real_escape_string($connection, $_FILES['uimage']['name']);
  $image_name = strtolower(trim($image_name));  
  $image_name = preg_replace("/[^a-z0-9.]/", "_", $image_name);
  $image_size = $_FILES['uimage']['size'];
  $image_tmp = $_FILES['uimage']['tmp_name'];
  $image_type = $_FILES['uimage']['type'];

  $imageExtension = explode('.', $image_name);
  $image_name = $imageExtension[0]; 
  $imageExtension = strtolower(end($imageExtension));

  $result = update_user_profile($_POST['uid'], $image_name, $imageExtension, $image_tmp);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }
  
  header('Content-Type: application/json');
  echo json_encode($response);
}

else if(isset($_POST['updatePasswordBtn'])) {
  $opword = mysqli_real_escape_string($connection, $_POST['opword']);
  $npword = mysqli_real_escape_string($connection, $_POST['npword']);

  $result = change_password($_POST['uid'], $opword, $npword);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);        
}

else if(isset($_POST['updateInfoBtn'])) {
  $acct_name =  mysqli_real_escape_string($connection, $_POST['aname']);
  $school_branch = mysqli_real_escape_string($connection, $_POST['uschool']);

  $result = update_user_info($_POST['uid'], $acct_name, $school_branch); 
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);
}
 
/***ACTIONS FOR USER MANAGEMENT ***/

else if(isset($_POST['addAccountBtn'])) {
  $accname = mysqli_real_escape_string($connection, $_POST['uaname']);
  $email = mysqli_real_escape_string($connection, $_POST['uemail']);
  $username = mysqli_real_escape_string($connection, $_POST['uname']);
  $password = mysqli_real_escape_string($connection, $_POST['upword']);
  $school = $_POST['uschool'];

  if(isset($_FILES['uimage']) && !empty($_FILES['uimage']['name'])) {
    //equipment imagae initialization
    $image_name = $_FILES['uimage']['name'];
    $image_name = strtolower(trim($image_name));  
    $image_name = preg_replace("/[^a-z0-9.]/", "_", $image_name);

    $image_size = $_FILES['uimage']['size'];
    $image_tmp = $_FILES['uimage']['tmp_name'];
    $image_type = $_FILES['uimage']['type'];

    $imageExtension = explode('.', $image_name); 
    $image_name = $imageExtension[0]; 
    $imageExtension = strtolower(end($imageExtension));

  } else {
    $image_name = 'admin';
    $imageExtension = 'png';
    $image_tmp = '';
  }

  // $data = [
  //   'accname' => $accname,
  //   'email' => $email,
  //   'username' => $username,
  //   'password' => $password,
  //   'school' => $school,
  //   'image_name' => $image_name,
  //   'imageExtension' => $imageExtension,
  //   'image_tmp' => $image_tmp
  // ];

  // echo "<pre>";
  // print_r($data);
  // echo "</pre>";

  $result = create_new_user($accname, $username, $password, $school, $email, $image_name,  $imageExtension, $image_tmp); 
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);            
}

else if(isset($_POST['deleteUserBtnSet'])) {
   delete_existing_user($_POST['deleteUserId']); 
}

else if(isset($_POST['checking_viewbtn'])) {
   view_user_inmodal($_POST['user_id']);   
}

else if(isset($_GET['user_id'])) {
   update_user_status($_GET['user_id'], $_GET['user_status']);
}

/***ACTIONS FOR CATEGORY PAGE ***/

//Category

else if(isset($_POST['add_category'])) {
   $category_name = mysqli_real_escape_string($connection, $_POST['category_name']);
   add_new_category($category_name); 
}

else if(isset($_POST['edit_category_btn'])) {
   $category_name = mysqli_real_escape_string($connection, $_POST['edit_category_name']);
   update_category($_POST['edit_id'], $category_name);
}

else if(isset($_POST['deleteBtnSetCategory'])) {
   delete_category($_POST['deleteCategoryId']); 
}

else if(isset($_GET['category_id'])) {
   update_category_status($_GET['category_id'], $_GET['category_status']);
}

//Equipment Type

else if(isset($_POST['addEquipType'])) {
   $equipmentType = mysqli_real_escape_string($connection, $_POST['equipment_type']);
   add_equipment_type($equipmentType);
}

else if(isset($_POST['editEquipType'])) {
   $equipmentTypeName = mysqli_real_escape_string($connection, $_POST['edit_equip_name']);
   update_equipment_type($_POST['editEquip_id'], $equipmentTypeName);
}

else if(isset($_POST['deleteBtnSetEquipType'])) {
   delete_equipment_type($_POST['deleteEquipTypeId']);
}

else if(isset($_GET['equipType_id'])) {
   update_equipmentType_status($_GET['equipType_id'], $_GET['equip_status']);
}


//Location Rack

else if(isset($_POST['addLocRack'])) {
   $locationRack = mysqli_real_escape_string($connection, $_POST['location_rack']);
   add_location_rack($locationRack);
}

else if(isset($_POST['editLocRack'])) {
   $locationRackName = mysqli_real_escape_string($connection, $_POST['edit_locrack_name']);
   update_location_rack($_POST['editLocRack_id'], $locationRackName);
}

else if(isset($_POST['deleteBtnSetLocationRack'])) {
   delete_location_rack($_POST['deleteLocationRackId']);
}

else if(isset($_GET['locationRack_id'])) {
   update_locationRack_status($_GET['locationRack_id'], $_GET['location_status']);
}

//Unit Type

else if(isset($_POST['addUnitType'])) {
   $unitType = mysqli_real_escape_string($connection, $_POST['unit_type']);
   add_unit_type($unitType);          
}

else if(isset($_POST['editUnitType'])) {
   $unitTypeName = mysqli_real_escape_string($connection, $_POST['edit_unitType_name']);
   update_unit_type($_POST['editUnitType_id'], $unitTypeName);
}

else if(isset($_POST['deleteBtnSetUnitType'])) {
   delete_unit_type($_POST['deleteUnitTypeId']);
   $deleteThatUnitType = $_POST['deleteUnitTypeId'];
}

else if(isset($_GET['unitType_Id'])) {
   update_unitType_status($_GET['unitType_Id'], $_GET['unit_status']);
}

//Room Code

else if(isset($_POST['add_room_code'])) {
   $roomCodeName = mysqli_real_escape_string($connection, $_POST['room_code_name']);
   add_room_code($roomCodeName);
}

else if(isset($_POST['editRoomCodeBtn'])) {
   $roomCodeName = mysqli_real_escape_string($connection, $_POST['editRoomCodeName']);
   update_room_code($_POST['editRoomCodeId'], $roomCodeName);
}

else if(isset($_POST['deleteBtnRoomCode'])) {
   delete_room_code($_POST['deleteRoomCodeId']);
}

else if(isset($_GET['room_code_id'])) {
   update_roomCode_status($_GET['room_code_id'], $_GET['room_code_status']);
}


/***ACTIONS FOR EQUIPMENT MANAGEMENT ***/ 

else if(isset($_POST['addEquipmentBtn'])) {
  // //retrieve and sanitize all user inputs
  $category_id = $_POST['category_id'];
  $equipment_name =  mysqli_real_escape_string($connection, $_POST['equipmentName']);
  $equipment_type_id = $_POST['equipmentType_id'];   
  $location_rack_id = $_POST['equipmentLocation_id'];
  $roomCode_id = $_POST['equipmentRoomCode_id'];
  $unit_type_id = $_POST['equipmentUnit_id']; 
  $price = mysqli_real_escape_string($connection, $_POST['equipmentPrice']);
  $stock = mysqli_real_escape_string($connection, $_POST['numStock']);
  $quantity = $_POST['availQuantity'];
  $amount = $_POST['totalAmount'];
  $condition = $_POST['condition'];


  if(isset($_FILES['equipment_img']) && !empty($_FILES['equipment_img']['name'])) {
    //equipment imagae initialization
    $image_name = $_FILES['equipment_img']['name'];
    $image_name = strtolower(trim($image_name));  
    $image_name = preg_replace("/[^a-z0-9.]/", "_", $image_name);

    $image_size = $_FILES['equipment_img']['size'];
    $image_tmp = $_FILES['equipment_img']['tmp_name'];
    $image_type = $_FILES['equipment_img']['type'];

    $imageExtension = explode('.', $image_name); 
    $image_name = $imageExtension[0]; 
    $imageExtension = strtolower(end($imageExtension));

  } else {
    $image_name = 'noimage';
    $imageExtension = 'png';
    $image_tmp = '';
  }


  $result = add_new_equipment($category_id, $equipment_name, $equipment_type_id, $location_rack_id, $roomCode_id, $unit_type_id, $price, $stock, $quantity, $amount, $condition, $image_name, $imageExtension, $image_tmp, $user_id);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);
}

else if(isset($_POST['executeVIEWBtn'])) {
  view_equipment($_POST['equipment_id'], $_POST['action']);
}

else if(isset($_POST['updateEquipment'])) {
  $response = [];
  //retrieve and sanitize all user inputs
  $eId = $_POST['eId'];
  $ename = mysqli_real_escape_string($connection, $_POST['eName']);
  $ecategory = $_POST['eCategory'];
  $etype = $_POST['eType'];
  $elocation = $_POST['eLocationRack'];
  $eroom = $_POST['eRoomCode'];
  $eunit = $_POST['eUnitType'];
  $estock = $_POST['eStock'];
  $eavailableqty = $_POST['eAvailableQty'];
  $etotalamt = $_POST['eTotalAmt'];


  // $response;
  $result = update_equipment($ename, $ecategory, $etype, $elocation, $eroom, $eunit, $estock, $eavailableqty, $etotalamt, $user_id, $eId);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
     $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);
}

else if(isset($_POST['updateImageEquipment'])) {

  if(isset($_FILES['eImage']) && !empty($_FILES['eImage']['name'])) {
    //equipment imagae initialization
    $image_name = $_FILES['eImage']['name'];
    $image_name = strtolower(trim($image_name));  
    $image_name = preg_replace("/[^a-z0-9.]/", "_", $image_name);

    $image_size = $_FILES['eImage']['size'];
    $image_tmp = $_FILES['eImage']['tmp_name'];
    $image_type = $_FILES['eImage']['type'];

    $imageExtension = explode('.', $image_name); 
    $image_name = $imageExtension[0]; 
    $imageExtension = strtolower(end($imageExtension));

  } else {
    $image_name = 'noimage';
    $imageExtension = 'png';
    $image_tmp = '';
  }

  update_equipment_image($image_name, $imageExtension, $image_tmp, $_POST['eid'], $user_id);

}

else if(isset($_POST['deleteEquipmentBtn'])) {
  delete_equipment($_POST['deleteEquipmentId']);
}


/*********************************
 *ACTIONS FOR TRANSACTION
 ********************************/

else if(isset($_POST["equipment_id"])) {
  insert_into_cart_list($_POST["equipment_id"], $_POST['action']);
}

else if(isset($_POST["placeBarrowBtn"])) {

  $costumer_id = $_POST['costumer_id'];
  $UserAdminId = $_POST['UserAdminId'];
  $barrowStatus = $_POST['barrowStatus'];
  $barrowData = $_POST['barrowData'];
  // echo "<pre>";
  // print_r($barrowData);
  // echo "</pre>";

  //function to perform barrow the equipment
  $result = barrow_equipment($UserAdminId, $costumer_id, $barrowStatus, $barrowData);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);
}

else if(isset($_POST['returnDataBtn'])) {
  $costumer_id = $_POST['costumer_id'];
  $usersAdminId = $_POST['usersAdminId'];
  $toReturnData = $_POST['toReturnData'];

  // echo "<pre>";
  // print_r($toReturnData);
  // echo "</pre>";

  //function to return barrowed the equipment
  $result = return_equipment($costumer_id, $usersAdminId, $toReturnData);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);
}

/*********************************
 *ACTIONS FOR BORROWER
 ********************************/

else if(isset($_POST['createBorrowerBtn'])) {
  $bfullname = mysqli_real_escape_string($connection, $_POST['bfullname']);
  $bcontactno = mysqli_real_escape_string($connection, $_POST['bcontactno']);
  $broleposition = $_POST['bposition'];
  $bcampusid = $_POST['bcampusId'];
  
  $result = insert_costumer_details($bfullname, $bcontactno, $bcampusid, $broleposition, $user_id);
  if($result['success']) {
    $response = ['success' => true, 'message' => $result['message']];
  } else {
    $response = ['success' => false, 'message' => $result['message']];
  }

  header('Content-Type: application/json');
  echo json_encode($response);
}

else if(isset($_GET["bname"])) {
  $result = get_borrower_info($_GET["bname"]);
  $data = [
    'costumer_id' => $result["costumer_id"],
    'costumer_name' => $result["name"],
    'costumer_phone_number' => $result["phone_number"],
    'costumer_school_branch' => $result["location"],
    'costumer_role_position' => $result["role_position"]
  ];
  header('Content-Type: application/json');
  echo json_encode($data);
}

else if(isset($_POST['deleteBtnSet'])) {
  delete_borrower($_POST['deleteCostumerId']);
}

else if(isset($_GET['id'])) {
  update_borrower_status($_GET['id'], $_GET['status']);
}