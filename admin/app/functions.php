<?php
/********************************************************
 *  ©JEROME AVECILLA -> ICT 12 DIGNIFIED S.Y 2022-2023
 * ******************************************************/
require_once __DIR__ . '/../config/db.connection.php';
ini_set('display_errors',  1);
error_reporting(E_ALL);


/********************************************
    FUNCTION FOR LOGIN AUTHENTICATION
*********************************************/

function validate_login_user($username, $password) {
   global $connection;

    //prepare statement
    $stmt = mysqli_prepare($connection, "SELECT * FROM users WHERE uname = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    try {
      //check if user exists in db
      if(mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         $_SESSION['acct_name'] = $row['acct_name'];
         //check if password is match in db
         if($row['pword'] !== null && password_verify($password, $row['pword'])) {
            //check if account is active 
            if($row['status'] == 1) {
                //generate new session id and update user session id and login time in db
                session_regenerate_id();
                $user_session_id = session_id();
                $stmt = mysqli_prepare($connection, "UPDATE users SET session_id = ?, is_logged_in = 1, login_time = NOW() WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $user_session_id, $row['id']);  
                mysqli_stmt_execute($stmt);

                //store user data in session variable
                $_SESSION['session_id'] = $user_session_id;
                $_SESSION['username'] = $row['uname']; 
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_status'] = $row['status'];
                $_SESSION['verify_status'] = $row['verified'];
                $_SESSION['login_time'] = $row['login_time'];
                $_SESSION['last_active_time'] = time();

                //then redirect to the dashboard
                header("Location: ../dashboard");
                $_SESSION['message'] = "WELCOME ADMIN";
                exit();
            } else { //account status is inactive
                $_SESSION['resultMessage'] = "Your account, <b>'".$_SESSION['acct_name']."'</b>, is currently inactive. If you require more information about your account status, please contact the administrator for assistance.";
               $_SESSION['resultMessageCode'] = "warning";
               $_SESSION['actionPerform'] = "login";   
            }
            
         } else { //incorrect password
            $_SESSION['login_attempts']++; //increment login attempt
            $_SESSION['resultMessage'] = "Invalid <span><b>username or password</b></span>. Please try again.";
            $_SESSION['resultMessageCode'] = "warning";
            $_SESSION['actionPerform'] = "login";   
         }
       } else { //user not found
         $_SESSION['login_attempts']++;
         $_SESSION['resultMessage'] = "Invalid <span><b>username or password</b></span>. Please try again.";
         $_SESSION['resultMessageCode'] = "warning";
         $_SESSION['actionPerform'] = "login";   
      }

      header("Location: ../../auth/login");
      exit();

      mysqli_stmt_close($stmt); //close all statement query action
       
    } catch (Exception $e) {
       echo "An error occurred: " . $e->getMessage();
    } 
}

/********************************************
    FUNCTIONS FOR USER PROFILE
*********************************************/

function update_user_profile($uid, $imgname, $imgext, $imgtmp) {
  global $connection;

  //move user upload photo into user-photo-upload folder
  move_uploaded_file($imgtmp, "../resources/images/user-photo-upload/".$imgname.'.'.$imgext);

  $query = "UPDATE users SET profile_img = ?, img_extension = ? WHERE id = ?";    
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, 'ssi', $imgname, $imgext, $uid);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  if(!$result) {
    $response = ['success' => false, 'message' => '[ERROR: Updating User Profile Image]'];   
  } 

  $response = ['success' => true, 'message' => 'Profile Updated Successfully!'];

  return $response;
}

function change_password($uid, $oldPassword, $newPassword) {
  global $connection;

  //checking user credentials
  $query = "SELECT pword FROM users WHERE id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $uid);
  mysqli_stmt_execute($stmt);
  $getResult = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($getResult);

  //check if entered password is match to the database password
  if(password_verify($oldPassword, $row['pword'])) {
    //passwords match; then proceed with updating the password.

    //hash the password before it save in database
    $hashpwd = password_hash($newPassword, PASSWORD_DEFAULT);
    $query = "UPDATE users SET pword = ? WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $hashpwd, $uid);
    mysqli_stmt_execute($stmt);
    $updateResult = mysqli_stmt_get_result($stmt);
    if(!$updateResult) {
      $response = ['success' => false, 'message' => '[ERROR: Updating user password!]'];
    } 
    $response = ['success' => true, 'message' => 'Password updated successfully.'];
  } else {
    $response = ['success' => false, 'message' => 'Old password does not match in database password!'];
  }  

  return $response;   
}

function update_user_info($uid, $acctName, $schoolBranch) {
  global $connection;

  $query = "UPDATE users SET acct_name=?, school_branch=? WHERE id=?";    
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ssi", $acctName, $schoolBranch, $uid);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  if(!$result) {
    $response = ['success' => false, 'message' => '[ERROR: Failed to update account info]'];
  } 
  $response = ['success' => true, 'message' => 'Account Info updated successfully.'];

  return $response;   
}


/***********************************************************
    FUNCTIONS FOR USER MANAGEMENT->ACCOUNT REGISTRATION
**********************************************************/

function create_new_user($accname, $username, $password, $school, $email, $imgname, $imgext, $imgtmp) {
  global $connection;

  //hash the user's entered password 
  $hashedpword = password_hash($password, PASSWORD_DEFAULT);

  //move user upload photo into user-photo-upload folder
  move_uploaded_file($imgtmp, "../resources/images/user-photo-upload/" .$imgname.'.'.$imgext);

  $query = "INSERT INTO users (acct_name, uname, pword, school_branch, email, profile_img, img_extension) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "sssssss", $accname, $username, $hashedpword, $school, $email, $imgname, $imgext);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  if(!$result) {
    $response = ['success' => false, 'message' => '[ERROR: Failed to create user account!]'];
  }

  $response = ['success' => true, 'message' => 'New account record successfully created.']; 

  return $response;
}

function delete_existing_user($userId) {
    global $connection;

    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function view_user_inmodal($userId) {
  global $connection;
  $sql = "SELECT * FROM users WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "i", $userId);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  //check if data is exist or not               
  if(mysqli_num_rows($result) > 0) {
    foreach($result as $user):
      ?>
        <div class="img-container d-flex justify-content-center align-items-center">  
          <img src="resources/images/user-photo-upload/<?= $user['profile_img'].'.'.$user['img_extension'];?>"id="pImage" alt="User Profile" 
          style="width: 9rem!important; height: 8.1rem;">
        </div><br/>
        <p class="form-control" style="margin-bottom: 5px;"><?= $user['acct_name']; ?></p>
        <p class="form-control" style="margin-bottom: 15px;"><?= $user['school_branch']; ?></p>
        <p class="form-control" style="margin-bottom: 15px;"><?= $user['email']; ?></p>
      <?php
    endforeach;  
  } 
  else {
    echo "<h5>No Record Found</h5>";
  }
}

function update_user_status($userId, $userStatus) {
  global $connection;
  $sql = "UPDATE users SET status = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "ii", $userStatus, $userId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "User account status updated successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";
  } else {
    $_SESSION['resultMessage'] = "Failed to update user status";
    $_SESSION['resultMessageCode'] = "error";
    $_SESSION['actionPerform'] = "Update";
  }
  header('Location: ../accounts');
  exit();
}


/********************************************
  FUNCTIONS FOR CATEGORY PAGE
*********************************************/

//Category 

function add_new_category($categoryName) {
  global $connection;

  $stmt = mysqli_prepare($connection, "SELECT * FROM users WHERE uname = ?");
  mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_array($result);
  $user_id = $row['id'];

  //read subbmitted data from db
  $stmt = mysqli_prepare($connection, "SELECT COUNT(*) AS count FROM categories WHERE category_name = ?");
  mysqli_stmt_bind_param($stmt, "s", $categoryName);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);

  //check if categories already exist in db
  if($row['count'] == 0) { 
    $stmt = mysqli_prepare($connection, "INSERT INTO categories (`category_name`, `date_added`, `user_id`) 
      VALUES (?, NOW(), ?)");
    mysqli_stmt_bind_param($stmt, "si", $categoryName, $user_id);
    $result = mysqli_stmt_execute($stmt);
    if($result) {
      $_SESSION['resultMessage'] = "Successfully added <b>$categoryName</b> as new category record.";
      $_SESSION['resultMessageCode'] = "success";
      $_SESSION['actionPerform'] = "Add";
    } else {
      $_SESSION['resultMessage'] = "Failed to add new category";
      $_SESSION['resultMessageCode'] = "error";
    }
  } else { 
    $_SESSION['resultMessage'] = "Failed to add new category: <b>$categoryName</b> is already exist."; 
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function update_category($categoryId, $categoryName) {
  global $connection;
    
  $stmt = mysqli_prepare($connection, "UPDATE categories SET category_name = ? WHERE category_id = ?");
  mysqli_stmt_bind_param($stmt, "si", $categoryName, $categoryId);
  mysqli_stmt_execute($stmt);
  if(mysqli_stmt_affected_rows($stmt) > 0) { //check result if it is successfully executed
    $_SESSION['resultMessage'] = "Successfully updated category CID $categoryId to $categoryName";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";
  } else {
    $_SESSION['resultMessage'] = "Failed to update category: $categoryName is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function delete_category($categoryId) {
  global $connection;

  $stmt = mysqli_prepare($connection, "DELETE FROM categories  WHERE category_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $categoryId);
  mysqli_stmt_execute($stmt);
}

function update_category_status($categoryId, $categoryStatus) {
  global $connection;

  $query = "UPDATE categories SET category_status = ? WHERE category_id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ii", $categoryStatus, $categoryId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Category status updated successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";   
  } else {
    $_SESSION['resultMessage'] = "Faield to update category status";
    $_SESSION['resultMessageCode'] = "error";
    $_SESSION['actionPerform'] = "Update"; 
  }

  header('Location: ../categories');
  exit();
}

//Equipment Type

function add_equipment_type($equipmentType) {
  global $connection;

  $query = "SELECT * FROM users WHERE uname = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_array($result);

  //read submitted data from db
  $sql = "SELECT COUNT(*) as count FROM equipment_type WHERE equip_type = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "s", $_POST['equipment_type']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $category = mysqli_fetch_assoc($result);

  //checking equipment type in db : 1 = exist and 0 = not exist
  if($category['count'] == 0) {
    $query = "INSERT INTO equipment_type (`equip_type`, `user_id`) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $equipmentType, $user['id']);
    $result = mysqli_stmt_execute($stmt);
        
    if($result) {
      $_SESSION['resultMessage'] = "Successfully added $equipmentType as new equipment type record.";
      $_SESSION['resultMessageCode'] = "success";
      $_SESSION['actionPerform'] = "Add";
    } else {
      $_SESSION['resultMessage'] = "Failed to add new equipment type";
      $_SESSION['resultMessageCode'] = "error";
    }
  } else {
    $_SESSION['resultMessage'] = "Failed to add new equipment type: $equipmentType is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function update_equipment_type($equipmentTypeId, $equipmentTypeName) {
  global $connection;

  $stmt = mysqli_prepare($connection, "UPDATE equipment_type SET equip_type = ? WHERE equip_id = ?");
  mysqli_stmt_bind_param($stmt, "si", $equipmentTypeName, $equipmentTypeId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Equipment Type Updated Successfully ";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";
  } else {
    $_SESSION['resultMessage'] = "Failed to update equipment type: $equipmentTypeName is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function delete_equipment_type($equipmentTypeId) {
  global $connection;

  $stmt = mysqli_prepare($connection, "DELETE FROM equipment_type WHERE equip_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $equipmentTypeId);
  mysqli_stmt_execute($stmt);
}

function update_equipmentType_status($equipmentTypeId, $equipmentTypeStatus) {
  global $connection;

  $query = "UPDATE equipment_type SET equip_status = ? WHERE equip_id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ii", $equipmentTypeStatus, $equipmentTypeId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Equipment Type status updated successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";   
  } else {
    $_SESSION['resultMessage'] = "Faield to update equipment type status";
    $_SESSION['resultMessageCode'] = "error";
    $_SESSION['actionPerform'] = "Update"; 
  }
  header('location: ../categories');
  exit();
}

//Location Rack

function add_location_rack($locationRack) {
  global $connection;

  $query = "SELECT * FROM users WHERE uname = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_array($result);

  $sql = "SELECT COUNT(*) as count FROM location_branch WHERE location = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "s", $_POST['location_rack']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $locrack = mysqli_fetch_assoc($result);

  if($locrack['count'] == 0) {     
    $query = "INSERT INTO location_branch (`location`, `user_id`) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $locationRack, $user['id']);
    $result = mysqli_stmt_execute($stmt);
    if($result) {
      $_SESSION['resultMessage'] = "Successfully added $locationRack as new location rack record.";
      $_SESSION['resultMessageCode'] = "success";
      $_SESSION['actionPerform'] = "Add";
    } else {
      $_SESSION['resultMessage'] = "Failed to add new Location Rack";
      $_SESSION['resultMessageCode'] = "error";
    }
  } else { 
    $_SESSION['resultMessage'] = "Failed to add new Location Rack: $locationRack is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function update_location_rack($locationRackId, $locationRackName) {
  global $connection;

  $query = "UPDATE location_branch SET location = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "si", $locationRackName, $locationRackId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Location Rack Updated Successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";
  } else {
    $_SESSION['resultMessage'] = "Failed to update Location Rack: $locationRackName is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function delete_location_rack($locationRackId) {
  global $connection;

  $query = "DELETE FROM location_branch WHERE id = ? LIMIT 1";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $locationRackId);
  mysqli_stmt_execute($stmt);
}

function update_locationRack_status($locationRackId, $locationRackStatus) {
  global $connection;
  $query = "UPDATE location_branch SET location_status = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ii", $locationRackStatus, $locationRackId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Location Rack status updated successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";   
  } else {
    $_SESSION['resultMessage'] = "Faield to update location rack status";
    $_SESSION['resultMessageCode'] = "error";
    $_SESSION['actionPerform'] = "Update"; 
  }

  header('location: ../categories');
  exit();
}

//Unit Type

function add_unit_type($unitType) {
  global $connection;

  $query = "SELECT * FROM users WHERE uname = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_array($result);

  $sql = "SELECT COUNT(*) as count FROM equipment_unit WHERE unit_type = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "s", $_POST['unit_type']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $unittype = mysqli_fetch_assoc($result);

  if($unittype['count'] == 0) {
    $query = "INSERT INTO equipment_unit (`unit_type`, `user_id`) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $unitType, $user['id']);
    $result = mysqli_stmt_execute($stmt);
    if($result) {
      $_SESSION['resultMessage'] = "Successfully added $unitType as new unit type.";
      $_SESSION['resultMessageCode'] = "success";
      $_SESSION['actionPerform'] = "Add";
    } else {
      $_SESSION['resultMessage'] = "Failed to add new unit type";
      $_SESSION['resultMessageCode'] = "error";
    }
  } else {
    $_SESSION['resultMessage'] = "Failed to add new unit type: $unitType is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function update_unit_type($unitTypeId, $unitTypeName) {
  global $connection;

  $sql = "UPDATE equipment_unit SET unit_type = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "si", $unitTypeName, $unitTypeId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Equipment Type Updated Successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";
  } else {
    $_SESSION['resultMessage'] = "Failed to update unit type: $unitTypeName is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function delete_unit_type($unitTypeId) {
  global $connection;

  $query = "DELETE FROM equipment_unit WHERE id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $unitTypeId);
  mysqli_stmt_execute($stmt);
}

function update_unitType_status($unitTypeId, $unitTypeStatus) {
  global $connection;

  $query = "UPDATE equipment_unit SET unit_status = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ii", $unitTypeStatus, $unitTypeId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Unit Type status updated successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";   
  } else {
    $_SESSION['resultMessage'] = "Faield to update unit type status";
    $_SESSION['resultMessageCode'] = "error";
    $_SESSION['actionPerform'] = "Update"; 
  }

  header('location: ../categories');
  exit();
}

//Room Code

function add_room_code($roomCodeName) {
  global $connection;

  $query = "SELECT * FROM users WHERE uname = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_array($result);

  $sql = "SELECT COUNT(*) as count FROM room_code WHERE room_code_name = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "s", $roomCodeName);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $roomcode = mysqli_fetch_assoc($result);

  if($roomcode['count'] == 0) {
    $query = "INSERT INTO room_code (`room_code_name`, `user_id`) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $roomCodeName, $user['id']);
    $result = mysqli_stmt_execute($stmt);
    if($result) {
      $_SESSION['resultMessage'] = "Successfully added $roomCodeName as new room code.";
      $_SESSION['resultMessageCode'] = "success";
      $_SESSION['actionPerform'] = "Add";
    } else {
      $_SESSION['resultMessage'] = "Failed to add new room code";
      $_SESSION['resultMessageCode'] = "error";
    }
  } else {
    $_SESSION['resultMessage'] = "Failed to add new room code: $roomCodeName is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function update_room_code($roomCodeId, $roomCodeName) {
  global $connection;

  $sql = "UPDATE room_code SET room_code_name = ? WHERE room_code_id = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "si", $roomCodeName, $roomCodeId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Room Code Updated Successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";
  } else {
    $_SESSION['resultMessage'] = "Failed to update room code: $roomCodeName is already exist.";
    $_SESSION['resultMessageCode'] = "warning";
  }

  header("Location: ../categories");
  exit();
}

function delete_room_code($roomCodeId) {
  global $connection;

  $query = "DELETE FROM room_code WHERE room_code_id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $roomCodeId);
  mysqli_stmt_execute($stmt);
}

function update_roomCode_status($roomCodeId, $roomCodeStatus) {
  global $connection;

  $query = "UPDATE room_code SET room_code_status = ? WHERE room_code_id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ii", $roomCodeStatus, $roomCodeId);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
    $_SESSION['resultMessage'] = "Room code status updated successfully";
    $_SESSION['resultMessageCode'] = "success";
    $_SESSION['actionPerform'] = "Update";   
  } else {
    $_SESSION['resultMessage'] = "Faield to update room code status";
    $_SESSION['resultMessageCode'] = "error";
    $_SESSION['actionPerform'] = "Update"; 
  }

  header('location: ../categories');
  exit();
}

/********************************************
    FUNCTIONS FOR EQUIPMENT MANAGEMENT PAGE
*********************************************/

function add_new_equipment($cid, $ename, $etid, $lrid, $rcid, $utid, $price, $stock, $quantity, $amount, $condition, $imgname, $imgext, $imgtmp, $uid) {

  global $connection;

  //if this temp is not empty means there is image submitted then let upload image in specified path
  if(!empty($imgtmp)) {
    move_uploaded_file($imgtmp, "../resources/images/equipment-photo-upload/" .$imgname.'.'.$imgext);
  }

  $query = "INSERT INTO equipment (category_id, equipment_name, type_id, location_id, roomcode_id, unit_id, price, stock, quantity, amount, conditions, equipment_img, img_extension, user_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ";         
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ssssssssssssss", $cid, $ename, $etid, $lrid, $rcid, $utid, $price, $stock, $quantity, $amount, $condition, $imgname, $imgext, $uid);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  if(!$result) {
    $response = ['success' => false, 'message' => '[ERROR: Failed to add new equipment record]'];
  } 
  $response = ['success' => true, 'message' => 'Sucessfully added new equipment record'];

  return $response; 
}

function view_equipment($equipmentId, $action) {
  global $connection;

  $query = "SELECT e.id AS equipmentId, e.*, 
      c.*, 
      t.*, 
      l.id AS locationId, l.location, 
      u.id AS unitTypeId, u.unit_type,
      a1.acct_name AS adminAdd, a1.id,
      a2.acct_name AS adminUpdate, a2.id, 
      rc.*
    FROM equipment e 
    INNER JOIN categories c ON e.category_id = c.category_id 
    INNER JOIN equipment_type t ON e.type_id = t.equip_id
    INNER JOIN location_branch l ON e.location_id = l.id
    INNER JOIN equipment_unit u ON e.unit_id = u.id
    INNER JOIN room_code rc ON e.roomcode_id = rc.room_code_id
    INNER JOIN users a1 ON e.user_id = a1.id -- for user id who add the equipment
    LEFT JOIN users a2 ON e.m_userid = a2.id -- for user id who update the equipment
    WHERE e.id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $equipmentId);     
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if(mysqli_num_rows($result) > 0)  {
    while($row = mysqli_fetch_assoc($result)) {
      //convert date format in db
      $date_added = date("M-d-Y / H:i A", strtotime($row['date_added'])); 
      $date_updated = ($row['date_updated'] !== NULL) 
      ? date("M-d-Y / H:i A", strtotime($row['date_updated']))
      : 'No modified record occured'; 
      ?>
      <input type="hidden" value="<?=$row['equipmentId']?>" id="eIdModal"/>
        <div class="img-container d-flex justify-content-center align-items-center img-thumbnail">  
          <img id="previewImage" src="resources/images/equipment-photo-upload/<?= $row['equipment_img'].'.'.$row['img_extension']; ?>" alt="Admin" class="rounded-circle" width="200"/>
        </div> 

        <?php if($action === 'edit'): ?>
          <form method="POST" action="app/actions.controller.php" enctype="multipart/form-data">
            <center class="mt-3">
              <label id="uploadImageButton" class="btn btn-light" style=" cursor: pointer;">
                <i class="far fa-solid fa-camera text-info"></i>&nbsp; Change Photo
                <input type="file" class="imageInput" name="eImage" id="eImageModal"
                  accept=".jpeg, .jpg, .png" style="display: none;"/>
              </label>
              <button type="button" id="removeImageButton" class="btn btn-light" 
                style=" cursor: pointer; display: none;">
                <i class="fa-solid fa-minus text-danger"></i>&nbsp; Remove Photo
              </button>
              <button type="submit" id="saveImageButton" name="updateImageEquipment" class="btn btn-light" 
                style=" cursor: pointer; display: none;">
                <i class="fa-solid fa-floppy-disk text-success"></i>&nbsp; Save Changes
              </button>
              <input class="form-control" type="hidden" name="eid" value="<?=$row['equipmentId']?>"/>
            </center>
          </form>
        <?php endif; ?>

        <div class="row">
          <div class="col-md-4">
            <small>Equipment Name</small>
            <?php if($action === 'edit'): ?>
              <input type="text" value="<?= $row['equipment_name']; ?>" 
                class="form-control m-b-3" id="eNameModal" autocomplete="off"/>
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['equipment_name']; ?>"
              />
            <?php endif; ?>
          </div>
          <div class="col-md-4">
            <small>Category</small>
            <?php if($action === 'edit'): ?>
              <select id="eCategoryModal" class="text-muted form-control m-b-5" >
                <option selected value="<?=$row['category_id']?>"><?= $row['category_name']; ?></option>
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
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['category_name']; ?>"
              />
            <?php endif; ?>
          </div>
          <div class="col-md-4">
            <small>Equipment Type</small>
            <?php if($action === 'edit'): ?>
              <select id="eTypeModal" class="text-muted form-control m-b-5" >
                <option selected value="<?= $row['equip_id']; ?>"><?= $row['equip_type']; ?></option>
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
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['equip_type']; ?>"
              />
            <?php endif; ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <small>Location Rack</small>
            <?php if($action === 'edit'): ?>
              <select id="eLocationRackModal" class="text-muted form-control m-b-5" >
                <option selected value="<?= $row['locationId']; ?>"><?= $row['location']; ?></option>
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
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['location']; ?>"
              />
            <?php endif; ?>
          </div>
          <div class="col-md-6">
            <small>Room Code</small>
            <?php if($action === 'edit'): ?>
              <select id="eRoomCodeModal" class="text-muted form-control m-b-5" >
                <option selected value="<?= $row['room_code_id']; ?>"><?= $row['room_code_name']; ?></option>
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
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['room_code_name']; ?>"
              /> 
            <?php endif; ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <small>Unit Type</small>
            <?php if($action === 'edit'): ?>
              <select id="eUnitTypeModal" class="text-muted form-control m-b-5">
                <option selected value="<?=$row['unitTypeId']?>"><?=$row['unit_type']?></option>
                  <?php
                    $query = "SELECT * FROM equipment_unit WHERE unit_status = 1";
                    $stmt = mysqli_prepare($connection, $query);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if(mysqli_num_rows($result) > 0) {
                      foreach ($result as $unit_type):
                        ?>
                          <option value="<?= $unit_type['id']; ?>">
                            <?= $unit_type['unit_type']; ?>
                          </option>
                        <?php
                      endforeach;
                    } else { echo "No data found."; }
                  ?>
                </select>
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['unit_type']; ?>"
              /> 
            <?php endif; ?>
            
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <small>Condition</small>
                <input type="text" class="form-control m-b-3" readonly 
                  value="<?= ($row['quantity'] < $row['conditions']) ? 'Critical' : 'Good'?>"
                />       
              </div>
              <div class="col-md-6">
                <small>Status</small>
                <input type="text" class="form-control m-b-3" readonly 
                  value="<?= ($row['quantity'] < $row['conditions']) ? 'Not Available' : 'Available'?>"
                />
              </div>
            </div>
            
          </div>
        </div>
        <hr/>
        <div class="row">
          <div class="col-md-3">
            <small>Price</small>
            <?php if($action === 'edit'): ?>
              <input type="text" class="form-control m-b-3" readonly 
                id="equipmentPrice"
                value="<?=$row['price']?>" 
              />
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="₱<?= $row['price']; ?>"
              />
            <?php endif; ?>
          </div>
          <div class="col-md-2">
            <small>Stocks</small>
            <?php if($action === 'edit'): ?>
              <input type="text" readonly
                class="calcUpdated form-control m-b-3"  
                value="<?=$row['stock']?>"
                id="currentStockForDisp" 
              />
              <input type="hidden" 
                class="form-control m-b-3"  
                value="<?=$row['stock']?>"
                id="currentStockForCalc" 
              />
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['stock']; ?>"
              />
            <?php endif; ?>
          </div>
          <div class="col-md-2">
            <small>In use</small>
            <?php if($action === 'edit'): ?>
              <input type="text" class="form-control m-b-3" readonly value="<?=$row['in_use']?>" />
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['in_use']; ?>"
              />
            <?php endif; ?>
          </div>
          <div class="col-md-2">
            <small>Available Quantity</small>
            <?php if($action === 'edit'): ?>
              <input type="text" readonly
                class="calcUpdated form-control m-b-3" 
                value="<?=$row['quantity']?>" 
                id="currentAvailableQtyForDisp" 
              />
              <input type="hidden" 
                class="form-control m-b-3" 
                value="<?=$row['quantity']?>" 
                id="currentAvailableQtyForCalc" 
              />
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="<?= $row['quantity']; ?>"
              /> 
            <?php endif; ?>
          </div>
          <div class="col-md-3">
            <small>Total Amount</small>
            <?php if($action === 'edit'): ?>
              <input type="text" readonly 
                class="calcUpdated form-control m-b-3"
                value="<?=$row['amount']?>"
                id="currentTotalAmtForDisp" 
              />
              <input type="hidden" 
                class="form-control m-b-3"
                value="<?=$row['amount']?>"
                id="currentTotalAmtForCalc" 
              />
            <?php else: ?>
              <input type="text" class="form-control m-b-3" readonly 
                value="₱<?= $row['amount']; ?>"
              /> 
            <?php endif; ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-6">
            <div class="row"> 
              <div class="col-md-6">
                <?php if($action === 'edit'): ?>
                  <small>Enter number of stock to be added</small>
                  <input type="text" placeholder="0" autocomplete="off"
                    class="calcUpdated add form-control m-b-3"  
                    id="addStock" 
                  />
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <?php if($action === 'edit'): ?>
                  <small>Enter number of stock to be minus</small>
                  <input type="text" placeholder="0" autocomplete="off"
                    class="calcUpdated reduce form-control m-b-3"  
                    id="minusStock" 
                  />
                <?php endif; ?>
              </div>
            </div>

          </div>
          <div class="col-md-3"></div>
        </div>
        <script type="text/javascript">
          function updateStock(userInput) {
            //initialize data
            let updatedStock = 0, updatedAvailableQty = 0, updatedTotalAmt = 0;
            //retrieve value
            const currentStockForCalc = parseInt($('#currentStockForCalc').val());
            const currentAvailableQtyForCalc = parseInt($('#currentAvailableQtyForCalc').val());
            const currentTotalAmtForCalc = parseInt($('#currentTotalAmtForCalc').val());

            let inputStock;
            if(userInput.match('add')) {
              inputStock = parseInt($('#addStock').val());
            } else if(userInput.match('reduce')) {
              inputStock = parseInt($('#minusStock').val());
            }

            const equipmentPrice = parseInt($('#equipmentPrice').val());
            //check if stock input is not a number or empty then
            if(isEmpty(inputStock) || isNaN(inputStock)) {
              //it will restore the previous values
              $('#currentStockForDisp').val(currentStockForCalc);
              $('#currentAvailableQtyForDisp').val(currentAvailableQtyForCalc);
              $('#currentTotalAmtForDisp').val(currentTotalAmtForCalc);
            } else {
              //if user is reducing stock AND inputStock is greater than currentStock
              if(userInput.match('reduce') && inputStock > currentStockForCalc) {
                //then set inputStock to empty
                $('#minusStock').val('');
                updatedStock = currentStockForCalc; //updated stock is set to currentStock
                updatedAvailableQty = currentAvailableQtyForCalc; //available quantity is unchanged
                updatedTotalAmt = equipmentPrice * updatedStock; //total amount is recalculated
              } else {
                //check if userinput if adding stock then it will add their
                //input to the current stock otherwise it will reduce
                updatedStock = userInput.match('add')
                  ? currentStockForCalc + inputStock //add
                  : currentStockForCalc - inputStock; //reduce

                updatedAvailableQty = userInput.match('add')
                  ? currentAvailableQtyForCalc + inputStock //add
                  : currentAvailableQtyForCalc - inputStock; //reduce

                //update the total amount by multiplying the price of equipment
                //to their updated stock
                updatedTotalAmt = equipmentPrice * updatedStock;
              }

              //then display the preview result
              $('#currentStockForDisp').val(updatedStock);
              $('#currentAvailableQtyForDisp').val(updatedAvailableQty);
              $('#currentTotalAmtForDisp').val(updatedTotalAmt);
            }
          }

          jQuery(document).ready(function() {
            //sanitize stock input
            $('#addStock, #minusStock').on('keyup', function() {
              let inputVal = $(this).val(); 
              const cleanedVal = inputVal.replace(/\D/g, ''); //replace any non-digit character
              $(this).val(cleanedVal); 
            });  

            //event listener for adding stock
            $(document).on('keyup', '.calcUpdated.add', function() {
              const inputAdd = $(this).val();
              //check if addstock input is not empty, meaning may laman
              //then yung minus stock na input will be disabled
              $('#minusStock').prop('readonly', !isEmpty(inputAdd));
              updateStock('add');
            });

            //event listener for reducing stock
            $(document).on('keyup', '.calcUpdated.reduce', function() {
              const inputReduce = $(this).val();
               //check if inputReduce input is not empty, meaning may laman
              //then yung add stock na input will be disabled
              $('#addStock').prop('readonly', !isEmpty(inputReduce));
              updateStock('reduce');
            });

            //to preview image
            $(document).on('change', '.imageInput', function() {
              const input = this;
              if(input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                  $('#previewImage').attr('src', e.target.result);
                  $('#uploadImageButton').hide();
                  $('#removeImageButton').show();
                  $('#saveImageButton').show();
                };

                reader.readAsDataURL(input.files[0]);
              }
            });

            //to remove photo image
            $('#removeImageButton').on('click', function () {
              $('#previewImage').attr('src', 'resources/images/equipment-photo-upload/<?= $row['equipment_img'].'.'.$row['img_extension']; ?>');
              $('.imageInput').val('');
              $('#uploadImageButton').show();
              $('#saveImageButton').hide();
              $(this).hide();
            });
          });
        </script>
        
        <?php if($action === 'view'): ?>
          <hr/>
          <div class="row">
            <div class="col-md-6">
              <small>Date Added</small>
              <input type="text" class="form-control m-b-3" value="<?= $date_added; ?>" readonly/>
            </div>
            <div class="col-md-6">
              <small>Added By</small>
              <input type="text" class="form-control m-b-3" 
                value="<?= ($row['adminAdd'] !== NULL) ? $row['adminAdd'] : 'No modified record occured'; ?>" 
                readonly
              />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <small>Date Updated</small> 
              <input type="text" class="form-control m-b-3" value="<?= $date_updated; ?>" readonly/>
            </div>
            <div class="col-md-6">
              <small>Updated By</small>
              <input type="text" class="form-control m-b-3" 
                value="<?= ($row['adminUpdate'] !== NULL) ? $row['adminUpdate'] : 'No modified record occured'; ?>" 
                readonly
              />
            </div>
          </div>
        <?php endif; ?>
      <?php
    }   
  } else { 
    ?><h5>No Record Found</h5><?php 
  }
}

function update_equipment_image($imgname, $imgext, $imgtmp, $eid, $uid) {
  global $connection;

  //var_dump($imgname, $imgext, $imgtmp, $eid, $uid);
  //if this temp is not empty means there is image submitted then let upload image in specified path
  if(!empty($imgtmp)) {
    move_uploaded_file($imgtmp, "../resources/images/equipment-photo-upload/" .$imgname.'.'.$imgext);
  }

  $query = "UPDATE equipment SET equipment_img = ?, 
    img_extension = ?,
    date_updated = NOW(),
    m_userid = ?
    WHERE id = ? 
  ";         
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ssii", $imgname, $imgext, $uid, $eid);
  $result = mysqli_stmt_execute($stmt);

  $_SESSION['message'] = ($result) ? "Equipment image successfully change." : "Something went wrong!" . mysqli_error($connection);

  header("Location: ../equipments");
  exit();
}

function update_equipment($ename, $ecategory, $etype, $elocation, $eroom, $eunit, $estock, $eavailableqty, $etotalamt, $uid, $eid) {
    global $connection;

    $result = [];

    $sql = "UPDATE equipment SET category_id = ?,
        equipment_name = ?,
        type_id = ?,
        location_id = ?,
        roomcode_id = ?,
        unit_id = ?,
        stock = ?,
        quantity = ?,
        amount = ?,
        date_updated = NOW(),
        m_userid = ?
        WHERE id = ?
      ";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'isiiiiiiiii', $ecategory, $ename, $etype, $elocation, $eroom, $eunit, $estock, $eavailableqty, $etotalamt, $uid, $eid);
    // Execute the statement
    if(mysqli_stmt_execute($stmt)) {
      $result = ['success' => true, 'message' => 'Equipment Updated Successfully!'];
    } else {
      $result = ['success' => false, 'message' => 'Faield Updated Equipment!'];
    }
    
    return $result;
}

function delete_equipment($equipmentId) {
  global $connection;

  //start deleting data
  $query = "DELETE FROM equipment WHERE id = ? LIMIT 1"; 
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $equipmentId);
  mysqli_stmt_execute($stmt);

}

/********************************************
    FUNCTIONS FOR TRANSACTION
*********************************************/

function insert_into_cart_list($equipmentID, $action) {
  global $connection;

  $barrow_table = '';
  $message = ''; 
  //start performing actions
  try {
    //add equipment into barrow cart list
    if($action == "add") {
      if(isset($_SESSION['equipment_cart'])) {
        $is_available = 0;
        foreach($_SESSION['equipment_cart'] as $keys => $values) {
          //check if this equipment in cart is already set and user want to add another or equipment 
          //that is same with this equipment id it will increment that equipment in cart
          if($_SESSION['equipment_cart'][$keys]['equipment_id'] == $_POST['equipment_id']) {
            $is_available++;
            //add the barrow qty in cart to user submited new cart
            $_SESSION['equipment_cart'][$keys]['equipment_bquantity'] = $_SESSION['equipment_cart'][$keys]['equipment_bquantity'] + $_POST['equipment_bquantity'];
          }
        }

        //check if this equipment is greater than 1 means etong bagong sinubmit ni user na
        //equiupment sa cart ay existing na
        if($is_available < 1) {
          $item_array = [
            'equipment_id'         =>  $_POST['equipment_id'],
            'equipment_name'       =>  $_POST['equipment_name'],
            'equipment_price'      =>  $_POST['equipment_price'],
            'availableQuantity'    =>  $_POST['availableQuantity'],
            'equipment_bquantity'  =>  $_POST['equipment_bquantity'],
            'updatedAvailableQty'  =>  $_POST['updatedAvailableQty'] 
          ];
          $_SESSION['equipment_cart'][] = $item_array;

          // $_SESSION['resultMessage'] = "Equipment has been added in to list!";
          // $_SESSION['resultMessageCode'] = "success";
          // $_SESSION['actionPerform'] = "Add";
        }
      } else {
        $item_array = [
          'equipment_id'         =>  $_POST['equipment_id'],
          'equipment_name'       =>  $_POST['equipment_name'],
          'equipment_price'      =>  $_POST['equipment_price'],
          'availableQuantity'    =>  $_POST['availableQuantity'],
          'equipment_bquantity'  =>  $_POST['equipment_bquantity'],
          'updatedAvailableQty'  =>  $_POST['updatedAvailableQty'] 
        ];
        $_SESSION['equipment_cart'][] = $item_array;

        // $_SESSION['resultMessage'] = "Equipment has been added in to list!";
        // $_SESSION['resultMessageCode'] = "success";
        // $_SESSION['actionPerform'] = "Add";
      }
    }
      
    //delete or remove equipment in barrow cart list
    if($action == "remove") {
      foreach($_SESSION['equipment_cart'] as $keys => $values) {
        if($values["equipment_id"] == $_POST["equipment_id"]) {
          unset($_SESSION["equipment_cart"][$keys]);
        }
      }
    }

    //update or change the quantity of equipment in barrow cart list
    if($action == "bquantity_change") {
      foreach($_SESSION['equipment_cart'] as $keys => $values) {
        if($values["equipment_id"] == $_POST["equipment_id"]) {
          $_SESSION["equipment_cart"][$keys]['equipment_bquantity'] == $_POST["bquantity"];
        }
      }
    }

    //display the equipment barrow in table
    $barrow_table .= '
      '.$message.'
      <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th style="width: 24%;">Equipment Name</th>
            <th style="width: 24%;">Date Barrow</th>
            <th style="width: 12%;">Quantity</th>
            <th style="width: 12%;">Price</th>
            <th style="width: 12%;">Sub Total</th>
            <th style="width: 10%;" class="text-center">Action</th>
          </tr>
        </thead>
      ';
      //check if cart is empty
      if(!empty($_SESSION['equipment_cart'])) {
        $total = 0; $subtotal = 0;
        foreach($_SESSION['equipment_cart'] as $keys => $values) {
          //calculate subtotal of each equipment by row
          $subtotal = $values["equipment_bquantity"] * $values["equipment_price"];
          //calculate all subtotal amount of each equipment
          $total = $total + ($values["equipment_bquantity"] * $values["equipment_price"]);
          $barrow_table .= ' 
            <tbody id="equipmentCartTbody">
              <tr>
                <td
                  data-toggle="tooltip"
                  data-placement="left"
                  title="Available Quantity: '.$values["updatedAvailableQty"].'">
                  '.$values["equipment_name"].'
                </td>
                <td class="equipmentsDetails"
                  data-id="'.$values["equipment_id"].'">
                  '.date("M-d-Y / h: i A").'
                </td>
                <td>
                  <input type="text" autocomplete="off" 
                    class="bquantity form-control"
                    value="'.$values["equipment_bquantity"].'"
                    data-id="'.$values["equipment_id"].'" 
                    data-equipname="'.$values["equipment_name"].'"
                    data-availqty="'.$values["updatedAvailableQty"].'"
                    data-prevalue="'.$values["equipment_bquantity"].'"
                    data-equipprice="'.$values["equipment_price"].'"
                    data-equipsubtotal="'.$subtotal.'"
                    data-equiptotal="'.$total.'"  
                  />
                <td>
                <td>'.$values["equipment_price"].'</td>
                <td>
                  <span
                    data-id="'.$values["equipment_id"].'"  
                    class="subtotal">
                    '.$subtotal.'
                  </span>
                </td>
                <td>
                  <button type="button" 
                    data-action="remove" 
                    data-id="'.$values["equipment_id"].'"  
                    class="removeEquipmentCart btn-danger btn btn-sm">
                    <i class="fa-solid fa-trash"></i>
                  </button> 
                </td>
              </tr>
            </tbody>
          ';
        }
        $barrow_table .= ' 
          <tfoot id="equipmentCartTfoot">
            <tr>
              <th colspan="4" class="text-right">Total Amount</th>
              <th>
                <span class="total">'.$total.'</span>
              </th>
            </tr>
            <tr>
              <th colspan="6" class="text-center">
                <button type="button" class="btn btn-light borrowNowBtn border-0" 
                  data-id="'.$values["equipment_id"].'" >
                  <i class="fas fa-spinner fa-spin text-dark loading-spinner nodisplay"></i>
                  <i class="fa-solid fa-arrow-right text-success arrow-icon"></i> Borrow now
                </button>
              </th>
            </tr>
          </tfoot>                                 
        ';
      }
      $barrow_table .= '</table>
        </div>
        <script>
        jQuery(document).ready(function() {
          $("#equipmentCartTfoot").on("click", ".borrowNowBtn", function(e) {
            e.preventDefault();
            const checkSelected = $(".checkBorrower").val();
            $(".loading-spinner").removeClass("nodisplay");
            $(".arrow-icon").addClass("nodisplay");
            
            if(isEmpty(checkSelected)) {
              swal({
                title: " ",
                text: `<h5>Please choose a borrower from the dropdown list. If the borrower does not have a record, click the "Add" button to create their account.</h5>`,
                type: "info",
                confirmButtonColor: "#A9CCE3",
                confirmButtonText: "Ok",
                closeOnConfirm: false,
                html: true,
              }, function(res) {
                if(res) {
                  $(".loading-spinner").addClass("nodisplay");
                  $(".arrow-icon").removeClass("nodisplay");
                  swal.close();
                }
              });
            } else {
              const bid = $("#bid").text();
              const uid = $(".userid").val();
              let barrowData = []; 
              $("#equipmentCartTbody tr").each(function() {
                let equipmentId = $(this).closest("tr").find(".equipmentsDetails").data("id");
                let bquantity = $(this).closest("tr").find(".bquantity").val();
                let subtotal = parseFloat($(this).closest("tr").find(".subtotal").text());
                const equipments = {
                  id: equipmentId,
                  quantity: bquantity,
                  subtotal: subtotal
                };
                barrowData.push(equipments);
              });
              $.ajax({
                url: "app/actions.controller.php",
                method: "POST",
                dataType: "JSON",
                data: {
                  placeBarrowBtn: true,
                  costumer_id: bid,
                  UserAdminId: uid,
                  barrowStatus: 1,
                  barrowData: barrowData
                },
                success:function(response) {   
                  const serverResponse = JSON.parse(JSON.stringify(response));
                  setTimeout(function() {
                    if(serverResponse.success) {
                      swal({
                        title: "Updated",
                        text: `<h5>${serverResponse.message}</h5>`,
                        type: "success",
                        confirmButtonColor: "#A9CCE3",
                        confirmButtonText: "Ok",
                        closeOnConfirm: false,
                        html: true,
                      }, function(res) {
                        if(res) {
                          location.reload();
                          $(".loading-spinner").addClass("nodisplay");
                          $(".arrow-icon").removeClass("nodisplay");
                        }
                      });
                    } else {
                      swal({
                        title: " ",
                        text: `<h5>${serverResponse.message}</h5>`,
                        type: "error",
                        confirmButtonColor: "#A9CCE3",
                        confirmButtonText: "Ok",
                        closeOnConfirm: false,
                        html: true,
                      }, function(res) {
                        if(res) {
                          location.reload();
                          $(".loading-spinner").addClass("nodisplay");
                          $(".arrow-icon").removeClass("nodisplay");
                        }
                      });
                    }
                  }, 2000); 
                }, error: function(xhr, status, error) {
                  console.log(xhr.responseText);
                }
              }); 
            }   
          });
        });
        </script>
      ';
      $output = [
        'success'       =>  true,
        'barrow_table'  =>  $barrow_table,
        'cart_item'     =>  count($_SESSION['equipment_cart']) //count how many equipment is in cart
      ];

      echo json_encode($output); //convert data in json format and send back to ajax request  
    } 
  catch(Exception $e) {
    echo "An error occured: " . $e->getMessage(); 
  }
}

function barrow_equipment($UserAdminId, $costumer_id, $barrowStatus, $barrowData) {
  global $connection;

  try {
    mysqli_autocommit($connection, FALSE); // start transaction

    // Use for each loop to process each equipment in the cart
    foreach ($barrowData as $equipment) {
      $equipmentId = $equipment['id'];
      $barrowQuantity = $equipment['quantity'];
      $subTotal = $equipment['subtotal'];

      // Check if the equipment record already exists for the customer
      $select_existing_borrow = "SELECT * FROM barrowed_equipment WHERE costumer_id = ? AND equipment_id = ? AND barrow_status = ?";
      $stmt = mysqli_prepare($connection, $select_existing_borrow);
      mysqli_stmt_bind_param($stmt, "iii", $costumer_id, $equipmentId, $barrowStatus);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $existingRecord = mysqli_fetch_assoc($result);

      if ($existingRecord) {
        // Update the existing record
        $updateBorrowedEquipment = "UPDATE barrowed_equipment SET barrow_qty = barrow_qty + ?, subtotal_amount = subtotal_amount + ? WHERE barrow_id = ?";
        $stmt = mysqli_prepare($connection, $updateBorrowedEquipment);
        mysqli_stmt_bind_param($stmt, "iii", $barrowQuantity, $subTotal, $existingRecord['barrow_id']);
        $updateResult = mysqli_stmt_execute($stmt);
        if (!$updateResult) {
          $response = ['success' => false, 'message' => 'Error updating existing equipment record'];
        }
      } else {
        // Insert a new equipment record
        $insertBorrowedEquipment = "INSERT INTO barrowed_equipment (costumer_id, equipment_id, barrow_status, barrow_date, barrow_qty, subtotal_amount, user_id) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $insertBorrowedEquipment);
        mysqli_stmt_bind_param($stmt, "iiiiii", $costumer_id, $equipmentId, $barrowStatus, $barrowQuantity, $subTotal, $UserAdminId);
        $insertBorrowedEquipment = mysqli_stmt_execute($stmt);
        if (!$insertBorrowedEquipment) {
          $response = ['success' => false, 'message' => 'Error inserting new equipment record'];
        }
      }

      $select_equipment = "SELECT * FROM equipment WHERE id = ?";
      $stmt = mysqli_prepare($connection, $select_equipment);
      mysqli_stmt_bind_param($stmt, "i", $equipmentId);
      mysqli_stmt_execute($stmt);
      $getDataResult = mysqli_stmt_get_result($stmt);
      if(!$getDataResult) {
        $response = ['success' => false, 'message' => throw new Exception("[ERROR: Failed getting data Equipments]")];
      }
      $row = mysqli_fetch_assoc($getDataResult);

      //UPDATE THE QUANTITY AND INUSE OF EQUIPMENT
      $updateInUse = $row['in_use'] + $barrowQuantity; //5+5 =10
      $updateAvailableQty = $row['quantity'] - $barrowQuantity; //5-5 =0

      $update_inuse_aquantity = "UPDATE equipment 
        SET in_use = ?, quantity = ?
        WHERE id = ?";
      $stmt = mysqli_prepare($connection, $update_inuse_aquantity);
      mysqli_stmt_bind_param($stmt, "iii", $updateInUse, $updateAvailableQty, $equipmentId);
      $updateEquipmentDetails   = mysqli_stmt_execute($stmt);
      if(!$updateEquipmentDetails) {
        $response = ['success' => false, 'message' => throw new Exception("[ERROR: Updating Equipment Details]")];
      }

      //CHECK IF EQUIPMENT IS NO AVAILABLE STOCK, THEN UPDATE STATUS
      if($updateAvailableQty == 0) {
        $update_equipment_status = "UPDATE equipment
          SET status = 0
          WHERE id = ?";
        $stmt = mysqli_prepare($connection, $update_equipment_status);
        mysqli_stmt_bind_param($stmt, "i", $equipmentId);
        $updateStatusRes = mysqli_stmt_execute($stmt);
        if(!$updateStatusRes) {
          $response = ['success' => false, 'message' => throw new Exception("[ERROR: Updating Equipment Status]")];
        }
      }
    }

    mysqli_commit($connection); // commit transaction

    // After inserting data in the cart into the database, unset all equipment in the cart
    if (isset($_SESSION['equipment_cart'])) {
      unset($_SESSION['equipment_cart']);
    }

    $response = ['success' => true, 'message' => 'Equipments borrowed successfully!'];
  } catch (Exception $e) {
    mysqli_rollback($connection); // rollback transaction
    $response = ['success' => false, 'message' => $e->getMessage()];
  }

  return $response;
}

function return_equipment($costumer_id, $usersAdminId, $toReturnData) {
  global $connection;
  try {
    mysqli_autocommit($connection, FALSE); // start transaction
    //use for each loop to update each equipment
    foreach($toReturnData as $barrowedEquipment) {
      $barrowId = $barrowedEquipment['barrow_id'];
      $equipmentId = $barrowedEquipment['equipment_id'];
      $remainingBorrowQty = $barrowedEquipment['quantity'];
      $returnInputQty = $barrowedEquipment['returnedQty'];
      $remainingSubtotal = $barrowedEquipment['subTotal'];

      $checkReturnQty = "SELECT * FROM barrowed_equipment  
        WHERE barrow_id = ? AND costumer_id = ?";
      $stmt = mysqli_prepare($connection, $checkReturnQty);
      mysqli_stmt_bind_param($stmt, "ii", $barrowId, $costumer_id);
      mysqli_stmt_execute($stmt);
      $resultGetReturnQty = mysqli_stmt_get_result($stmt);
      $rowReturnQty = mysqli_fetch_assoc($resultGetReturnQty);

      //CHECK IF RETURN QTY COLUMN IS HAVE RECORD NOT NULL OR EMPTY
      //means naka pag soli na si borrower at may record na siya ngayon 
      //yung isosoli niya na panibago, i a add nalang doon sa existing record
      $returnQtyUpdate = 0;
      if($rowReturnQty['returned_qty'] != 0 || $rowReturnQty['returned_qty'] != NULL) {
        $returnQtyUpdate = $rowReturnQty['returned_qty'] + $returnInputQty; //4 + 2 = 6
      } else {
        $returnQtyUpdate = $rowReturnQty['returned_qty'] + $returnInputQty; //0 + 2 = 2
      }

      //UPDATE BARROW EQUIPMENT AND SUBTOTAL
      $updateBqtyAndStotal = "UPDATE barrowed_equipment 
        SET barrow_qty = ?,
            returned_qty = ?,
            subtotal_amount = ?
        WHERE barrow_id = ? AND costumer_id = ?";
      $stmt = mysqli_prepare($connection, $updateBqtyAndStotal);
      mysqli_stmt_bind_param($stmt, "iiiii", $remainingBorrowQty, $returnQtyUpdate, $remainingSubtotal, $barrowId, $costumer_id);
      mysqli_stmt_execute($stmt);
      $resultUpdatingBqtyAndStotal = mysqli_stmt_get_result($stmt);
      if(!$resultUpdatingBqtyAndStotal) {
        $response = ['success' => false, 'message' => '[ERROR] Failed to updating returning equipment data'];
      }

      $checkBarrowQty = "SELECT be.*, e.*
        FROM barrowed_equipment be 
        INNER JOIN equipment e ON be.equipment_id = e.id
        WHERE be.barrow_id = ? AND be.costumer_id = ?";
      $stmt = mysqli_prepare($connection, $checkBarrowQty);
      mysqli_stmt_bind_param($stmt, "ii", $barrowId, $costumer_id);
      mysqli_stmt_execute($stmt);
      $resultGetBorrowQty = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_assoc($resultGetBorrowQty);

            
      //CHECK IF BARROWED EQUIPMENT IS FULLY RETURN THEN UPDATE BARROW STATUS
      //check row barrow_qty
      if($row['barrow_qty'] == 0) {
        $updateBarrowStatus = "UPDATE barrowed_equipment 
          SET barrow_status = 0, return_date = NOW(), admin_id = ?
          WHERE barrow_id = ? AND costumer_id = ?";
        $stmt = mysqli_prepare($connection, $updateBarrowStatus);
        mysqli_stmt_bind_param($stmt, "iii", $usersAdminId, $barrowId, $costumer_id);
        mysqli_stmt_execute($stmt);
        $resultUpdatingBarrowStatus = mysqli_stmt_get_result($stmt);
        if(!$resultUpdatingBarrowStatus) {
          $response = ['success' => false, 'message' => '[ERROR] Failed to updating return equipment status'];
        }
      }

      //UPDATE THE QUANTITY AND INUSE OF EQUIPMENT
      $updateAvailableQty = intval($row['quantity']) + intval($returnInputQty); //12 + 2 = 14
      $updateInUse = intval($row['in_use']) - intval($returnInputQty); //8 - 2 = 6        

      $updateEquipments = "UPDATE equipment 
        SET quantity = ?, in_use = ?
        WHERE id = ?";
      $stmt = mysqli_prepare($connection, $updateEquipments);
      mysqli_stmt_bind_param($stmt, "iii", $updateAvailableQty, $updateInUse, $equipmentId);
      mysqli_stmt_execute($stmt);
      $resultUpdatingEquipments = mysqli_stmt_get_result($stmt);
      if(!$resultUpdatingEquipments) {
        $response = ['success' => false, 'message' => '[ERROR] Failed to updating equipment inuse and available quantity'];
      }

      //CHECK IF EQUIPMENT IS BACK AVAILABLE STOCK, THEN UPDATE STATUS OF THE EQUIPMENT
      if($updateAvailableQty > 1) {
        $update_equipment_status = "UPDATE equipment
          SET status = 1
          WHERE id = ?";
        $stmt = mysqli_prepare($connection, $update_equipment_status);
        mysqli_stmt_bind_param($stmt, "i", $equipmentId);
        mysqli_stmt_execute($stmt);
        $resultUpdatingEquipmentStatus = mysqli_stmt_get_result($stmt);
        if(!$resultUpdatingEquipmentStatus) {
          $response = ['success' => false, 'message' => '[ERROR] Failed to updating equipment status'];
        }
      }
    }  //end loop
    mysqli_commit($connection); // commit transaction

    $response = ['success' => true, 'message' => 'Equipments returned successfully!'];
  } catch (Exception $e) {
    mysqli_rollback($connection); // rollback transaction
    $response = ['success' => false, 'message' => '[ERROR] An error occurred:'. $e->getMessage()];
  }

  return $response;
}

/********************************************
    FUNCTIONS FOR COSTUMER
*********************************************/

function insert_costumer_details($bfullname, $bcontactno, $bcampusid, $broleposition, $user_id) {
  global $connection;

  $query = "INSERT INTO costumers(name, phone_number, school_id, role_position, admin_id) 
    VALUES (?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ssssi", $bfullname, $bcontactno, $bcampusid, $broleposition, $user_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  if(!$result) {
    $response = ['success' => false, 'message' => "[ERROR] Creating new borrower"];
  }
  $response =  ['success' => true, 'message' => 'New borrower record successfully created!'];

  return $response;
}

function get_borrower_info($borrowName) {
  global $connection;

  $sql = "SELECT c.*, lb.location
      FROM costumers c
      INNER JOIN location_branch lb ON c.school_id = lb.id 
      WHERE name = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "s",$borrowName);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);
  return $row;
}

function update_borrower_status($borrowerId, $borrowerStatus) {
  global $connection;

  $query = "UPDATE costumers SET costumer_status = ? WHERE costumer_id = ?";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "ii", $borrowerStatus, $borrowerId);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $_SESSION['resultMessage'] = "Costumer or Barrower status updated successfully";
  $_SESSION['resultMessageCode'] = "success";
  $_SESSION['actionPerform'] = "Update";

  header('location: ../return');
  exit();
}

function delete_borrower($borrowerId) {
  global $connection;

  $query ="DELETE FROM costumers WHERE costumer_id = ? LIMIT 1";
  $stmt = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($stmt, "i", $borrowerId);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  header('Location: ../return');
  exit();     
}