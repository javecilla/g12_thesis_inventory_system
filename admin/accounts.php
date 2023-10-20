<?php
session_start();
require_once __DIR__ . '/config/db.connection.php';
require_once __DIR__ . '/app/check_user.php';
ini_set('display_errors',  1);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>IMS | User Management</title>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<link rel="icon" type="image/png" href="../goldenminds.favicon.png" sizes="16x16" /> 
    <?php require_once __DIR__ . '/components/css.file-links.inc.php';?>
		<link defer href="resources/css/custom.css" rel="stylesheet" />
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
                  <h6 class="clock m-t-30"><?= date("M-d-Y")?> / <?= date(" h: i A");?></h6>
                </div> 
              </div> 
            </div>
            <div class="col-lg-4 p-l-0 title-margin-left">
              <div class="page-header">
                <div class="page-title">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                      <a href="accounts" class="active"> User Management</a>
	                  </li>
	                  <li class="breadcrumb-item">Dashboard</li>
                  </ol>
                </div>
              </div>
           	</div>     
          </div><!--end row-->				

					<div class="card table-responsive">
						<div class="card-title">
							<button type="button" data-target="#user_add_accountModal" data-toggle="modal" 
								class="btn btn-light border-0">
								<i class="far fa-solid fa-circle-plus text-success"></i> Add user
							</button>
            </div><hr/>
			
	          <!--start user table-->
						<table id="tbl_users" class="table table-bordered" >
							<thead>
								<tr>
									<th style="width: 5%">UID</th>
									<th style="width: 15%">Account Name</th>
									<th style="width: 15%">Email</th>
									<th style="width: 10%">Username</th>
									<th style="width: 10%">Status</th>
									<th style="width: 15s%">Last Login</th>
									<th style="width: 8%" class="text-center">Action</th>
								</tr>
							</thead>		  
							<tbody id="tbody_users">
								<?php
									$query = "SELECT * FROM users";
									$stmt = mysqli_prepare($connection, $query);
								  mysqli_stmt_execute($stmt);
								  $result = mysqli_stmt_get_result($stmt);     
									if(mysqli_num_rows($result) > 0) { //check if data from result								
										while($user = mysqli_fetch_assoc($result)) {
											//convert date format in db
											$last_login = ($user['login_time'] === NULL) ? "No login record" : date("M-d-Y / H:i A", strtotime($user['login_time']));
											?>
												<tr>
													<td class="user_id"><?= $user['id']; ?></td>
													<td><?= $user['acct_name']; ?></td>
													<td><?= $user['email']; ?></td>
													<td><?= $user['uname']; ?></td>
													<td>
														<?php
															if($user['status'] == 1) { //active
																echo '<span class="badge badge-success">
																	<a href="app/actions.controller.php?user_id='.$user['id'].'&user_status=0" class="text-white">Active
																	</a>
																</span>';
															} else { //inactive
																echo '<span class="badge badge-danger">
																	<a href="app/actions.controller.php?user_id='.$user['id'].'&user_status=1" class="text-white">Inactive
																	</a>
																</span>';
															}
														?>	
													</td>
													<td><?= $last_login; ?></td>
													<td>
														<form action="app/actions.controller.php" method="POST">
															<!--view button-->
															<button type="button" id="<?= $user['id'];?>"
																class="view_btn btn-primary btn btn-sm m-r-4">
																<i class='ti-eye'>&#xE872;</i>
															</button>
															
															<!--delete button-->
	                            <input type="hidden" class="valUserId" value="<?= $user['id'];?>" />
	                            <button class="delUserId btn btn-danger btn-sm m-r-10" type="button">
	                              <i class='ti-trash delete-icon'>&#xE872;</i>
	                            </button>
														</form>															
													</td>
												</tr>
											<?php
										}
									} else {
										?>
											<tr><td colspan="7">No Record Found</td></tr>
										<?php
									}
								?>	  
							</tbody>
						</table>
					</div> <!--end card-->

					<div class="modal-handler">
						<!-- MODAL VIEW ACCOUNT FORM -->
						<div class="modal fade" id="user_view_accountModal" tabindex="-1" role="view" aria-labelledby="viewAccount" aria-hidden="true">
							<div class="modal-dialog">
							  <div class="modal-content">
							    <div class="modal-header">
							      <h5 class="modal-title" id="view">VIEW ACCOUNT</h5>
							      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <i class="ti-close" aria-hidden="true"> </i>
	                  </button>
							    </div>
							    <div class="modal-body">
							     	<div class="user_viewing_data">
							     		<!--data came from database by ajax request-->
							     	</div>
								    <div class="modal-footer">
		                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		                </div>
							 	 </div>
								</div>
							</div>
						</div>
				

						<!--MODAL ACCOUNT REGISTRATION FORM-->
	          <div class="modal fade" id="user_add_accountModal" tabindex="-1" role="add" aria-labelledby="addAccount" aria-hidden="true">
	            <div class="modal-dialog modal-dialog-centered" role="document">
	             	<div class="modal-content">
	             		<!--start user form-->
	                <form action="app/actions.controller.php" method="POST" enctype="multipart/form-data" id="addAccountForm">
	                  <div class="modal-header m-b-10">
	                    <h5 class="modal-title" id="register">REGISTER ACCOUNT</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                      <i class="ti-close" aria-hidden="true"> </i>
	                    </button>
	                  </div>
	                  <div class="modal-body">
	                    <div class="d-flex justify-content-center align-items-center" >  
	                      <img src="resources/images/system-photo/default-profile.jpg" class="profile_img img-admin" alt="Admin"/>
	                    </div>
	                    <!--profile image-->
	                    <div class="form-group">
	                      <center class="mt-3">
	                      	<label for="img_input" class="btn btn-light border-0 uploadImageButton" style=" cursor: pointer;">
                          	<i class="far fa-solid fa-camera text-info"></i>&nbsp; Browse Photo
                          	<input type="file" id="img_input" accept=".jpeg, .jpg, .png" 
                            	style="display: none;" class="uimage" />
                        	</label>
                        	<button type="button" id="removeImageButton" class="btn btn-light border-0" 
                						style=" cursor: pointer; display: none;">
						                <i class="fa-solid fa-minus text-danger"></i>&nbsp; Remove Photo
						              </button>
	                      </center>
	                                              
	                    </div>
	                    <!--account name-->
	                   	<input type="text" id="aname"	placeholder="Account name" 
	                   		class="form-control m-b-5" autocomplete="off"   
	                   	/>
	                   	<!--email-->
	                   	<input type="text" id="uemail" placeholder="Email" 
	                   		class="form-control m-b-5"  
	                   	/>
	                   	<!--username-->
	                    <input type="text" id="uname" placeholder="Username" 
	                    	class="form-control m-b-5" autocomplete="off"  
	                    />
	                    <!--password-->
	                    <div class="input-group" id="show_hide_password">
				                <input type="password" id="upword" 
				                	class="inputPword form-control m-b-5" placeholder="Password" 
				                	autocomplete="off" onpaste="false" 
				                /> 
				               <div class="btn-addon"><a href="#"><i class="fa fa-eye-slash eye-icon"></i></a></div>
				              </div> 
				              <!--confirm password-->
	                    <div class="input-group m-b-5" id="show_hide_cpassword">
				                <input type="password" id="cpword" 
				                	class="inputPword form-control" placeholder="Confirm Password" 
				                	autocomplete="off" onpaste="false" 
				                /> 
				                <div class="btn-addon"><a href="#"><i class="fa fa-eye-slash eye-icon"></i></a></div>
				             	</div>
	   
                    	<input type="text"
                    		id="uschool" 
	                      class="form-control mt-1"
	                      list="adminFromList"
	                      autocomplete="off"
	                      placeholder="-- SELECT --" 
                    	/>
	                    <datalist id="adminFromList">
	                      <?php
	                        $query = "SELECT DISTINCT e.location_id, lb.*
	                          FROM equipment e
	                          INNER JOIN location_branch lb ON e.location_id = lb.id";
	                        $stmt = mysqli_prepare($connection, $query);
	                        mysqli_stmt_execute($stmt);
	                        $result = mysqli_stmt_get_result($stmt);
	                        if(mysqli_num_rows($result) > 0) { //check data from result
	                          while($school = mysqli_fetch_assoc($result)) {
	                            ?>
	                              <option value="<?= $school['location']; ?>">
	                                <?= $school['location']; ?>
	                               </option>
	                            <?php
	                          }
	                        } else {
	                          echo "No data found :(";
	                        }
	                      ?>
	                    </datalist>
	                  </div> <!--end modal body-->
	                  <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	                    <button type="submit" id="addAccountBtn" class="btn btn-success">Add Account</button>
	                  </div>
	                </form>
	               </div>
	              </div>
	            </div>
						</div>
						
					</div> <!--end modal handlder-->
				</div><!--end container-->
   		</div><!--end main-->
   	</div> <!--end content-->
   	
   	<script src="resources/js/accounts.js" defer></script>
		<script defer>
			function isEmpty(field) {
				return field === "";
			}

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
		  }, 10000); //check user session every seconds
		</script>
	</body>
</html>