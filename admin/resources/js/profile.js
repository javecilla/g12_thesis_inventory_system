jQuery(document).ready(function() {
	//update info
	$('#updateInfoBtn').on('click', function(e) {
		e.preventDefault();
		const uid = $(this).data('uid');
		const aname = $('#aname').val();
		const uschool = $('#uschool').val();
		//send it to the server
	   $.ajax({	
		  url: "app/actions.controller.php",
		  method: "POST",
		  dataType: "JSON",
		  data: {
		   	uid: uid,
		   	aname: aname,
		   	uschool: uschool,
		   	updateInfoBtn: true
		  },
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
			       	swal.close();
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
	  })
	});

	//update password
	$('#updatePasswordBtn').on('click', function(e) {
		e.preventDefault();
		const uid = $(this).data('uid');
		const opword = $('#oldPass').val();
		const npword = $('#newPass').val();
		const cpword = $('#confirmPass').val();

		//validate if old, new and confirm password is empty
		if(isEmpty(opword) || isEmpty(npword) || isEmpty(cpword)) {
			swal({
	      title: " ",
	      text: `<h5>Please fill in all password fields</h5>`,
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
		else if(npword.length < 8) { 
			swal({
	      title: " ",
	      text: `<h5>New password should be at least 8 characters.</h5>`,
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
		//validate if pword and confirm password is match
		else if(!cpword.match(npword)) { 
			swal({
	      title: " ",
	      text: `<h5>New password and confirm password do not match.</h5>`,
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
		//validate success send server request
		else {
				//send it to the server
	      $.ajax({	
		      url: "app/actions.controller.php",
		      method: "POST",
		      dataType: "JSON",
		      data: {
		      	uid: uid,
		      	opword: opword,
		      	npword: npword,
		      	updatePasswordBtn: true
		      },
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
			        		$('#changepassForm')[0].reset();
			          	swal.close();
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
	      })
		}
	});

	//sanitize input password
	$('.inputPword').on('keyup', function() {
		let inputVal = $(this).val(); 
    const cleanedVal = inputVal.replace(/[^A-Za-z0-9]/g, '');
    $(this).val(cleanedVal); 
	});

	//save profile
	$('.savePhotoBtn').on('click', function(e) {
	  e.preventDefault();
	  const btn = $('.savePhotoBtn');
	  const uid = $(this).data('uid');
	  const uimage = $('#inputTag').prop('files')[0];
	  if(uimage) { // Check if an image is selected
	    const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];
	    if(allowedFormats.includes(uimage.type)) {
	      const formData = new FormData();
	      formData.append('uid', uid);
	      formData.append('uimage', uimage);
	      formData.append('changeProfileBtn', true);
	      //send it to the server
	      $.ajax({	
		      url: "app/actions.controller.php",
		      method: "POST",
		      data: formData,
		      dataType: "JSON",
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
			        		btn.prop('disabled', true);
			          	swal.close();
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
	    } else {
	    	swal({
	        title: " ",
	        text: `<h5>Please select an image in JPEG, PNG, or JPG format.</h5>`,
	        type: "info",
	        confirmButtonColor: "#A9CCE3",
	        confirmButtonText: "Ok",
	        closeOnConfirm: false,
	        html: true,
      	}, function(res) {
        	if(res) {
          	swal.close();
        	}
      	});
	    }
	  } else {
	  	swal({
	      title: " ",
	      text: `<h5>Please select an image!</h5>`,
	      type: "info",
	      confirmButtonColor: "#A9CCE3",
	      confirmButtonText: "Ok",
	      closeOnConfirm: false,
	      html: true,
      }, function(res) {
       	if(res) {
        	swal.close();
       	}
    	});
	  }
	});

	//onchange the profile
	$(document).on('change', '#inputTag', function() {
		const saveBtn = $('.savePhotoBtn');
		saveBtn.prop('disabled', false);
		const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function() {
      //replace the default image with the uploaded image
      $(".profile_img").attr("src", this.result);
    };
    //show the file URL in an input box [e.g. filename.png/jpg/jpg]
    reader.readAsDataURL(file);
	});
});