jQuery(document).ready(function() {
  //to add new equipment
  $(document).on('click', '.addEquipmentBtn', function(e) {
    e.preventDefault();
    const eimage = $('.equipment_img').prop('files')[0];
    const ename = $('#equipmentName').val();
    const eTypeId = $('#equipmentType_id').val();
    const eCategoryId = $('#category_id').val();
    const eLocationId = $('#equipmentLocation_id').val();
    const eRoomId = $('#equipmentRoomCode_id').val();
    const eUnitId = $('#equipmentUnit_id').val(); 
    const ePrice = $('#price').val();
    const eStock = $('#stock').val();
    const eCondition = $('#condition').val();
    const availQuantity = $('#availQuantity').val();
    const totalAmount = $('#totalAmount').val();

    //check if required fields is empty
    if(isEmpty(ename) || isEmpty(eTypeId) || isEmpty(eCategoryId) || isEmpty(eLocationId) ||
      isEmpty(eRoomId) || isEmpty(ePrice) || isEmpty(eStock) || isEmpty(eCondition)) {
      swal({
        title: " ",
        text: `<h5>Please fill the all fields that contains red asterisk "*".</h5>`,
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
    //valided success
    else {
      const formData = new FormData();
      formData.append('equipment_img', eimage);
      formData.append('equipmentName', ename);
      formData.append('equipmentType_id', eTypeId);
      formData.append('category_id', eCategoryId);
      formData.append('equipmentLocation_id', eLocationId);
      formData.append('equipmentRoomCode_id', eRoomId);
      formData.append('equipmentUnit_id', eUnitId);
      formData.append('equipmentPrice', ePrice);
      formData.append('numStock', eStock);
      formData.append('condition', eCondition);
      formData.append('availQuantity', availQuantity);
      formData.append('totalAmount', totalAmount);
      formData.append('addEquipmentBtn', true);
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
                location.reload();
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
    }   
  });

  //to update equipment
  $('.updateEquipmentBtn').on('click', function(e) {
    e.preventDefault();
    //retrieve data
    
    const formData = new FormData();
    formData.append('eId', $('#eIdModal').val());
    formData.append('eName', $('#eNameModal').val());
    formData.append('eCategory', $('#eCategoryModal').val());
    formData.append('eType', $('#eTypeModal').val());
    formData.append('eLocationRack', $('#eLocationRackModal').val());
    formData.append('eRoomCode', $('#eRoomCodeModal').val());
    formData.append('eUnitType', $('#eUnitTypeModal').val());
    formData.append('eStock', $('#currentStockForDisp').val());
    formData.append('eAvailableQty', $('#currentAvailableQtyForDisp').val());
    formData.append('eTotalAmt', $('#currentTotalAmtForDisp').val());
    formData.append('updateEquipment', true);
    // formData.forEach((value, key) => {
    //   console.table(key, value);
    // });

    //send to server
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
            }
          });
        } else {
          swal({
            title: "Updated",
            text: `<h5>${serverResponse.message}</h5>`,
            type: "error",
            confirmButtonColor: "#A9CCE3",
            confirmButtonText: "Ok",
            closeOnConfirm: false,
            html: true,
          }, function(res) {
            if(res) {
              location.reload();
            }
          });
        }
      },
      error: function(error) {
        console.error(error);
      }
    });
    
  });

  //to view equipment data on modal
  $('#tbody_equipment').on("click", ".view-btn, .edit-btn", function(e) {
  	e.preventDefault();
    $('#VIEWequipmentMODAL').modal('show'); //show modal

    const action = $(this).data('action');
    const equipment_id = $(this).data('id');
    //const equipment_id = $(this).closest('tr').find('.equipment_id').text();
    // alert(equipment_id);
    if(action.match('edit')) {
    	$('#updateEquipmentBtn').show();
    	$('#modalTitleAction').text('UPDATE');
    } else{
    	$('#updateEquipmentBtn').hide();
    	$('#modalTitleAction').text('VIEW');
    }

    //console.log(equipment_id);
    $.ajax({
      url: "app/actions.controller.php", 
     	method: "POST", 
      data: { 
        executeVIEWBtn: true,
        equipment_id: equipment_id,
        action: action
      },
      success: function(data) { 
        // console.log(data);
        $('.equipmentVIEW_data').html(data); 
      }
    });
  });

  //to delete equipment
  $('#tbody_equipment').on("click", ".delete-btn", function(e) {
    e.preventDefault();
    // alert("test");
    var deleteEquipmentId = $(this).data('id');

    swal({ //pop up confirmation
      title: "Are you sure to delete?",
      text: "Once deleted, you will not be able to recover this record",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, delete it",
      cancelButtonText: "No, cancel it",
      closeOnConfirm: false,
      closeOnCancel: false
    },
    function(willDeleteThatEquipment) {
      if(willDeleteThatEquipment) {
	      //send ajax request to server
	      $.ajax({
	        url: "app/actions.controller.php", //file to be send request
	        method: "POST", //send via post method
	        data: { //data to be retrieve $_POST[]
	          deleteEquipmentBtn: true,
	          deleteEquipmentId: deleteEquipmentId
	        },
	        success: function(result){ //if success
	          //console.log(result);
	          swal({
	            title: "Deleted!",
	            text: "Equipment record deleted successfully",
	            type: "success"
	          }, function() {
	            window.location = "equipments";
	          });
	        }
	      });
     	} else {
        swal("Cancelled!", "Equipment record is safe!", "error");
      }
    });
 	});

  //to auto calculate the total ammount (Modal Add Form)
  $(document).on('keyup', '.calc', function() {
    let totalAmount = 0;
        
    const enteredPrice  = parseInt($('#price').val());
    const enteredStock  = parseInt($('#stock').val());

    $('#availQuantity').val(enteredStock);

    totalAmount = enteredPrice * enteredStock;
    $('#totalAmount').val(totalAmount);
  });

  //sanitize user input (Do not allow any input that is not digits/numerical)
  $(document).on('keyup', '.inputU', function() {
    let inputVal = $(this).val(); 
    const cleanedVal = inputVal.replace(/\D/g, ''); //replace any non-digit character
    $(this).val(cleanedVal); 
  });

  //onchnage photo preview
  $("#input_equipment_img").on("change", function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function() {
      //replace the default image with the uploaded image
      $(".img-container img").attr("src", this.result);
    };
    //show the file URL in an input box [e.g. filename.png/jpg/jpg]
    reader.readAsDataURL(file);
  });

  //equipment data table
  $('#tbl_equipment').dataTable({ 
    ordering: true,
    bJQueryUI: true,
    sPaginationType: "full_numbers"
  });

  //tool tip
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
});