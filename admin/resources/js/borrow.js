jQuery(document).ready(function() {
  //submit borrow list of data in cart
  $('#equipmentCartTfoot').on('click', '.borrowNowBtn', function(e) {
    e.preventDefault();
    const checkSelected = $('.checkBorrower').val();
    $('.loading-spinner').removeClass('nodisplay');
    $('.arrow-icon').addClass('nodisplay');
    
    if(isEmpty(checkSelected)) {
      swal({
        title: " ",
        text: `<h5>Please choose a borrower from the dropdown list. If the borrower does not have a record, click the 'Add' button to create their account.</h5>`,
        type: "info",
        confirmButtonColor: "#A9CCE3",
        confirmButtonText: "Ok",
        closeOnConfirm: false,
        html: true,
      }, function(res) {
        if(res) {
          $('.loading-spinner').addClass('nodisplay');
          $('.arrow-icon').removeClass('nodisplay');
          swal.close();
        }
      });
    } else {
      const bid = $('#bid').text();
      const uid = $('.userid').val();
      let barrowData = []; //retrieve all data equipment in cart and store in array
      $('#equipmentCartTbody tr').each(function() {
        let equipmentId = $(this).closest('tr').find('.equipmentsDetails').data('id');
        let bquantity = $(this).closest('tr').find('.bquantity').val();
        let subtotal = parseFloat($(this).closest('tr').find('.subtotal').text());
        const equipments = {
          id: equipmentId,
          quantity: bquantity,
          subtotal: subtotal
        };
        barrowData.push(equipments);
      });
      //console.table(barrowData);
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
                  $('.loading-spinner').addClass('nodisplay');
                  $('.arrow-icon').removeClass('nodisplay');
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
                  $('.loading-spinner').addClass('nodisplay');
                  $('.arrow-icon').removeClass('nodisplay');
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

  //fetch the borrower data
  $(document).on('change', '#barrowerName', function() {
    const bname = $(this).val();
    $('.checkBorrower').val(bname);
    //to show the borrower card
    $('#borrowerCard').removeClass('nodisplay'); 
    $('#instruction').addClass('nodisplay');
    $.ajax({
      url: "app/actions.controller.php",
      method: "GET",
      dataType: "JSON",
      data: { bname: bname },
      success: function(data) {
        //fetch all data from the server response
        $('#bid').text(data.costumer_id);
        $('#bname').text(data.costumer_name);
        $('#brole').text(data.costumer_role_position);
        $('#bnumber').text(data.costumer_phone_number);
        $('#bschool').text(data.costumer_school_branch);
      },
      error: function(error) {
        console.log(error);
      }
    });   
  });

  //remove item from cart
  $(document).on('click', '.removeEquipmentCart', function(e) {
    e.preventDefault();
    $.ajax({
      url: "app/actions.controller.php",
      method: "POST",
      dataType:"JSON",
      data: {
        action: $(this).data('action'),
        equipment_id: $(this).data('id')
      },
      success: function(response) {
        $('#barrow_table').html(response.barrow_table);
        $('.badge').text(response.cart_item); //update badge in cart 
      },
      error: function(error){
        console.log(error);
      }
    });
  });

  //cartInput 
  $(document).on('keyup', '.bquantity', function() {
    const previousVal = $(this).data('prevalue');
    const equipName = $(this).data('equipname');
    const equipPrice = parseFloat($(this).data('equipprice'));
    const equipSubtotal = parseFloat($(this).data('equipsubtotal'));
    const equipTotal = parseFloat($(this).data('equiptotal'));

    const availableQty = parseFloat($(this).data('availqty'));

    let subtotals = $(this).closest('tr').find('.subtotal');
    let inputVal = $(this).val();
    let UpdateSubTotal = 0, newTotal = 0;

    //sanitize input
    let cleanedVal = inputVal.replace(/\D/g, ''); //replace any non-digit character
    $(this).val(cleanedVal); 
    inputVal = parseFloat(cleanedVal);

    //check entered borrow qty is greater than available qty
    if(inputVal > availableQty) {
      //send error message
      swal({
        title: "",
        text: `<h5>There are only <b>${availableQty}</b> available quantity left for <b>${equipName}</b>. Barrow quantity cannot exceed available quantity.</h5>`,
        type: "info",
        html: true,
      });
      //then set the borrow qty to remaining available qty
      subtotals.text(equipSubtotal);
      $('.total').text(equipTotal);
      $(this).val(availableQty);
    } else {
      //check if empty or not
      if(isEmpty(inputVal) || isNaN(inputVal) || inputVal == 0) {
        //then set the borrow qty to remaining available qty
        subtotals.text(equipSubtotal);
        $('.total').text(equipTotal);
        $(this).val('').focus();
      } else {
        //update subtotal price for each equipment in table cart
        UpdateSubTotal = equipPrice * inputVal;
        subtotals.text(UpdateSubTotal);

        //update the total ammount of all equipment in table cart
        subtotals.each(function(index) {
          newTotal += parseFloat($(this).text());
        });
        //display total ammount calculated
        $('.total').text(newTotal);
      }
    }                              
  });

  //borrowButton
  $(document).on('click', '.addBorrowBtn', function(e) {
    e.preventDefault();
    const eid = $(this).data('eid');
    const dataForm = {
      action: $(this).data('action'),
      equipment_id: eid,
      equipment_name: $(this).data('ename'),
      equipment_price: $(this).data('price'),
      availableQuantity: $(this).data('aqty'),
      equipment_bquantity: $(`#borrowQtyInput${eid}`).val(),
      updatedAvailableQty: $(`#updatedAvailableQty${eid}`).val(),
    };
    //console.table(dataForm);
    $.ajax({
      url: "app/actions.controller.php",
      method: "POST",
      dataType:"JSON",
      data: dataForm,
      success: function(data) {
        if(data.success) {
          $('#barrow_table').html(data.barrow_table);
          $('.badge').text(data.cart_item); //update badge in cart 
          $(`#addBorrowBtn${eid}`).attr('disabled', true);
          $(`#borrowQtyInput${eid}`).val(0);
        }
      },
      error: function(error) {
        console.log(error);
      }
    });
  });

  //borrowInput
  $(document).on('keyup', '.borrowQtyInput, .contactno', function() {
    var ename = $(this).data('ename');
    var inputVal = $(this).val(); 
    var availableQty = parseInt($(this).data('aqty'));

    //sanitize input
    const cleanedVal = inputVal.replace(/\D/g, ''); //replace any non-digit character
    $(this).val(cleanedVal); 

    inputVal = parseInt(cleanedVal);
    let updateAvailableQty = availableQty - inputVal;

    //find the corresponding "Borrow" button based on data-eid attribute
    const eid = $(this).closest('td').next('td').find('.addBorrowBtn, .updatedAvailableQty').data('eid');
    const borrowButton = $(`#addBorrowBtn${eid}`);
    const updatedAqty = $(`#updatedAvailableQty${eid}`);

    //check if this input is not empty, then borrow button will be un disabled
    if(isEmpty(inputVal) || isNaN(inputVal)) {
      borrowButton.attr('disabled', true);
      updatedAqty.val(0);
    } else {
      borrowButton.attr('disabled', false);
      updatedAqty.val(updateAvailableQty);
    }

    //check entered borrow qty is greater than available qty
    if(inputVal > availableQty) {
      //send error message
      swal({
        title: "",
        text: `<b>Failed to borrow equipment:</b> It seems you are trying to borrow <u><b>${inputVal}</b></u> ${ename} where there are only <u><b>${availableQty}</b></u> available quantity on ${ename}`,
        type: "info",
        html: true,
      });
      //then set the borrow qty to empty
      $(this).val('');
      updatedAqty.val(0); 
    }
  });

  //to create borrow account/record
  $(document).on('click', '#createBorrowerBtn', function(e) {
    e.preventDefault();
    const currentUri = $('#currentUri').val();
    let bfullname = $('#bfullname').val();
    let bcontactno = $('#bcontactno').val();
    let bposition = $('#bposition').val();
    let bcampus = $('#bcampus').val();
    if(isEmpty(bfullname) || isEmpty(bcontactno) || isEmpty(bposition) || isEmpty(bcampus)) {
      swal({
        title: "",
        text: `<h4>All field is required!</h4>`,
        type: "info",
        html: true,
      }, function(isConfirm) {
        swal.close();
      });
    } else {
      $.ajax({
        url: "app/actions.controller.php",
        method: "POST",
        dataType: "JSON",
        data: {
          createBorrowerBtn: true,
          bfullname: bfullname,
          bcontactno: bcontactno,
          bposition: bposition,
          bcampusId: bcampus
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
              html: true,
            }, function(res) {
              if(res) {
                window.location.href = `barrow?locationRack=${currentUri}`;
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
                window.location.href = `barrow?locationRack=${currentUri}`;
              }
            });
          }
        },
        error: function(error) {
          console.log(error);
        }
      });
    }
  });

  //equipment data table
  $('#allEquipmentTbl').dataTable({ 
    ordering: true,
    bJQueryUI: true,
    sPaginationType: "full_numbers"
  });

  //tool tip
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
});