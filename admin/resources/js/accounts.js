jQuery(document).ready(function() {
	//to create new account
	$(document).on('click', '#addAccountBtn', function(e) {
		e.preventDefault();
		//retrieve data input
		let uimage = $('.uimage').prop('files')[0];
		let uaname = $('#aname').val();
		let uemail = $('#uemail').val();
		let uname = $('#uname').val();
		let upword = $('#upword').val();
		let cpword = $('#cpword').val();
		let uschool = $('#uschool').val();

		//check if required field is empty
		if(isEmpty(uaname) || isEmpty(uemail) || isEmpty(uname) || 
			isEmpty(upword) || isEmpty(cpword) || isEmpty(uschool)) {
			swal({
	      title: " ",
	      text: `<h5>All fields is required!</h5>`,
	      type: "info",
	      confirmButtonColor: "#A9CCE3",
	      confirmButtonText: "Ok",
	      closeOnConfirm: false,
	      html: true,
      }, function(res) {
       	if(res) {
        	swal.close();
        	return;
       	}
    	});
		}
		//validate if email is valid
		else if(!uemail.includes('@')) {
			swal({
	      title: " ",
	      text: `<h5>Invalid email address! Should include "@"</h5>`,
	      type: "info",
	      confirmButtonColor: "#A9CCE3",
	      confirmButtonText: "Ok",
	      closeOnConfirm: false,
	      html: true,
      }, function(res) {
       	if(res) {
        	swal.close();
        	return;
       	}
    	});
		}
		//validate if new password is atleast 8 charaters or passowrd short
		else if(upword.length < 8) { 
			swal({
	      title: " ",
	      text: `<h5>Password should be at least 8 characters.</h5>`,
	      type: "info",
	      confirmButtonColor: "#A9CCE3",
	      confirmButtonText: "Ok",
	      closeOnConfirm: false,
	      html: true,
      }, function(res) {
       	if(res) {
        	swal.close();
        	return;
       	}
    	});
		}
		//validate if password and confirm pass is match
		else if(!cpword.match(upword)) { 
			swal({
	      title: " ",
	      text: `<h5>Password and confirm password do not match.</h5>`,
	      type: "info",
	      confirmButtonColor: "#A9CCE3",
	      confirmButtonText: "Ok",
	      closeOnConfirm: false,
	      html: true,
      }, function(res) {
       	if(res) {
        	swal.close();
        	return;
       	}
    	});
		}
		//validated success
		else {
			const formData = new FormData();
			formData.append('uimage', uimage);
			formData.append('uaname', uaname);
			formData.append('uemail', uemail);
			formData.append('uname', uname);
			formData.append('upword', upword);
			formData.append('uschool', uschool);
			formData.append('addAccountBtn', true);

			//send server request
			$.ajax({
				url: "app/actions.controller.php",
				method: "POST",
				dataType: "JSON",
				data: formData,
		    processData: false,
		    contentType: false,
		    success: function(response) {
		    	const serverResponse = JSON.parse(JSON.stringify(response));
      		if(serverResponse.success) {
      			swal({
				      title: " ",
				      text: `<h5>${serverResponse.message}</h5>`,
				      type: "success",
				      confirmButtonColor: "#A9CCE3",
				      confirmButtonText: "Ok",
				      closeOnConfirm: false,
				      html: true
			      }, function(res) {
			        if(res) {
								location.reload();
			        }
			      });
      		} else {
      			swal({
		          title: "",
		          text: `<h5>${serverResponse.message}</h5>`,
		          type: "error",
		          confirmButtonColor: "#A9CCE3",
		          confirmButtonText: "Ok",
		          closeOnConfirm: false,
		          html: true
		        }, function(res) {
		          if(res) {
		            swal.close();
		          }
		        });
      		}
		    },
		    error: function(xhr, status, error) {
		    	console.log(xhr.responseText);
		    }
			});
		}
	});

	//To delete user account
	$('#tbody_users').on("click", ".delUserId", function(e) {
		e.preventDefault();
		var deleteUserId = $(this).closest("tr").find('.valUserId').val();
		// console.log(deleteUserId);
		swal({ //pop up confirmation
			title: "Are you sure to delete?",
	    text: "Once deleted, you will not be able to recover this user record!",
	    type: "warning",
	    showCancelButton: true,
	    confirmButtonColor: "#DD6B55",
	    confirmButtonText: "Yes, delete it!",
	    cancelButtonText: "No, cancel it!",
	    closeOnConfirm: false,
	    closeOnCancel: false
		},
		function(willDeleteUser) {
			if(willDeleteUser) { //if user click delete
				//send ajax request to server
				$.ajax({
					method: "POST", //send via post method
					url: "app/actions.controller.php", //file to be send request
					data: { //data to be retrieve via $_POST[]
						deleteUserBtnSet: true, 		
						deleteUserId: deleteUserId	
					},
					success:function(result) { //if success
						swal({
		          title: "Deleted!",
		          text: "User record deleted successfully",
		          type: "success"
		        }, function() {
		         	window.location = "accounts"; //redirect to same page
		        });
					}
				});
			} else { //user click cancel
				swal("Cancelled!", "User record is safe!", "error");
			}
		});
	});

	//To show user data in modal
	$('#tbody_users').on("click", ".view_btn", function() {
		$('#user_view_accountModal').modal('show'); //show modal
		var user_id = $(this).closest('tr').find('.user_id').text();
		$.ajax({
			method: "POST",
			url: "app/actions.controller.php",
			data: {
				checking_viewbtn:true,
				user_id:user_id
			},
			success: function(data) {
				//console.log(data);
				$('.user_viewing_data').html(data);
			}
		});
	});

	//onchange the profile
	$(document).on('change', '#img_input', function() {
		const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function() {
      //replace the default image with the uploaded image
      $(".profile_img").attr("src", this.result);
      $('.uploadImageButton').hide();
      $('#removeImageButton').show();
    };
    //show the file URL in an input box [e.g. filename.png/jpg/jpg]
    reader.readAsDataURL(file);
	});

	//remove uploaded photo
	$('#removeImageButton').on('click', function() {
		$('.profile_img').attr('src', 'resources/images/system-photo/default-profile.jpg');
		$('#img_input').val('');
		$('.uploadImageButton').show();
		$(this).hide();
	});

	//sanitize input password
	$('.inputPword').on('keyup', function() {
		let inputVal = $(this).val(); 
    const cleanedVal = inputVal.replace(/[^A-Za-z0-9]/g, '');
    $(this).val(cleanedVal); 
	});

	//hide and show password
	$("#show_hide_password a, #show_hide_cpassword a").on('click', function(e) {
		e.preventDefault();
		if($('#show_hide_password input, #show_hide_cpassword input').attr("type") == "text"){
		  $('#show_hide_password input, #show_hide_cpassword input').attr('type', 'password');
		  $('#show_hide_password i, #show_hide_cpassword i').addClass( "fa-eye-slash" );
		  $('#show_hide_password i, #show_hide_cpassword i').removeClass( "fa-eye" );
		} else if($('#show_hide_password input, #show_hide_cpassword input').attr("type") == "password"){
		  $('#show_hide_password input, #show_hide_cpassword input').attr('type', 'text');
		  $('#show_hide_password i, #show_hide_cpassword i').removeClass( "fa-eye-slash" );
		  $('#show_hide_password i, #show_hide_cpassword i').addClass( "fa-eye" );
		 }
	});

	//users data table
	$('#tbl_users').dataTable({ 
    ordering: true,
    bJQueryUI: true,
    sPaginationType: "full_numbers"
  });
}); 