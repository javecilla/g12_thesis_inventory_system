<?php
/********************************************************
 *  ©JEROME AVECILLA -> ICT 12 DIGNIFIED S.Y 2022-2023
 * ******************************************************/
ini_set('display_errors',  1);
include_once('config/db.connection.php');

   //validate user inside the system
   try {
      $timeout = 10 * 60;  //10 minutes

      if(isset($_SESSION['username']) && isset($_SESSION['session_id'])) {
         //check if the user's status has changed to inactive
         $user_id = $_SESSION['user_id'];
         $query = "SELECT status FROM users WHERE id = '$user_id'";
         $result = mysqli_query($connection, $query);
         $row = mysqli_fetch_assoc($result);

         if($_SESSION['user_status'] != $row['status']) {
            header('Location: ../auth/logout.php');
            exit();
         }
         //check if the user has been inactive for more than 10 minutes
         if(isset($_SESSION['last_active_time']) && (time() - $_SESSION['last_active_time']) > $timeout) {
            header('Location: ../auth/logout.php');
            exit();
         }
         //update the user's last active time
         $_SESSION['last_active_time'] = time();
      }

      //check user is not logged in, prevent access to the system 
      if(!isset($_SESSION['username']) && !isset($_SESSION['session_id'])) {
           header('Location: ../auth/login?failed=' .urlencode('empty-field-access-denied'));
           exit();
       }
   } catch(Exception $e) {
       echo "An error occured: " . $e->getMessage();
   }


/********************************************
    FUNCTIONS TO CONVERT DATE-TIME
*********************************************/
function time_diff($start, $end) {
    $diff = $end - $start;
    if ($diff < 60) {
        return "$diff seconds";
    } elseif ($diff < 3600) {
        $diff = round($diff / 60);
        return "$diff minutes";
    } elseif ($diff < 86400) {
        $diff = round($diff / 3600);
        return "$diff hours";
    } elseif ($diff < 604800) {
        $diff = round($diff / 86400);
        return "$diff days";
    } elseif ($diff < 2592000) {
        $diff = round($diff / 604800);
        return "$diff weeks";
    } elseif ($diff < 31536000) {
        $diff = round($diff / 2592000);
        return "$diff months";
    } else {
        $diff = round($diff / 31536000);
        return "$diff years";
    }
}
?>