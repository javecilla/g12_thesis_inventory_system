jQuery(document).ready(function() {
	//to return equipment btn
	$(document).on('click', '.returnNowBtn', function(e) {
		e.preventDefault();
		$('.loading-spinner').removeClass('nodisplay');
    $('.arrow-icon').addClass('nodisplay');

		const borrowerId = $('.bid').val();
		const userId = $('.uid').val();
		
		let toReturnData = [];

		$('#equipmentBorrowedTbody tr').each(function() {
			let bid = $(this).closest('tr').find('.returnQtyInput').data('bid');
			let eid = $(this).closest('tr').find('.returnQtyInput').data('eid');
			let ubqty = parseFloat($(this).closest('tr').find('.borrowqty').text());
			let ustotal = parseFloat($(this).closest('tr').find('.subtotal').text())
			let rinput = parseFloat($(this).closest('tr').find('.returnQtyInput').val());

			//check if the "Return Qty" input is not empty for each rows
			if(!isNaN(rinput) && !isEmpty(rinput) && rinput > 0) {
				//if this not empty then include this to oject
				//otherwise it will not include
				const barrowedEquipment = {
					barrow_id: bid,
					equipment_id: eid,
					quantity: ubqty,
					subTotal: ustotal,
					returnedQty: rinput
				};
				toReturnData.push(barrowedEquipment);
			}
		});
		//console.table(toReturnData);
		$.ajax({
      url: "actions.controller.php", 
      method: "POST",
      dataType: "JSON", 
      data: { 
       	returnDataBtn: true,
        costumer_id: borrowerId,
        usersAdminId: userId,
        toReturnData:toReturnData
      }, 
      success:function(response) {
        const serverResponse = JSON.parse(JSON.stringify(response));
        setTimeout(function() {
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
                window.location.href = `costumer.record?gmcbid=${borrowerId}`;
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
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
	});

	//return qty input
	$(document).on('keyup', '.returnQtyInput', function() {
		const bid = $(this).data('bid');
		const bqty = parseFloat($(this).data('bqty'));
		const price = parseFloat($(this).data('price'));
		const subtotal = parseFloat($(this).data('subtotal'));
		const totalAmt = parseFloat($(this).data('total'));

		//sanitize input
		let inputVal = $(this).val();
    let cleanedVal = inputVal.replace(/\D/g, ''); //replace any non-digit character
    $(this).val(cleanedVal); 
    inputVal = parseFloat(cleanedVal);

    //update button status when input changes
    let returnNowBtn = $('.returnNowBtn');
		returnNowBtn.prop('disabled', true);

    const borrowQtyElement = $(this).closest('tr').find('.borrowqty');
    const subtotalElement = $(this).closest('tr').find('.subtotal');

    //check entered return qty is greater than borrowed qty
    if(inputVal > bqty) {
    	//send error message
      swal({
        title: "",
        text: `<h5>Return quantiy cannnot exceed the borrowed quantiy</h5>`,
        type: "info",
        html: true,
      });
      //then set the return qty to empty
      borrowQtyElement.text(bqty);
      subtotalElement.text(subtotal);
      $('.total').text(totalAmt);
      $(this).val('').focus();
    } else {
    	//check if empty or not
    	if(isEmpty(inputVal) || isNaN(inputVal) || inputVal == 0) {
        //then set the borrow qty to remaining available qty
        borrowQtyElement.text(bqty);
        subtotalElement.text(subtotal);
        $('.total').text(totalAmt);
        $(this).focus();
    	} else {
    		returnNowBtn.prop('disabled', false);
    		//update borrow qty
    		let updatedBorrowQty = bqty - inputVal;
    		borrowQtyElement.text(updatedBorrowQty);

    		//update subtotal for each equipment in table cart
    		let updatedSubTotal = price * updatedBorrowQty;
        subtotalElement.text(updatedSubTotal);

        //recalculate the total amount by iterating through all equipment rows in table cart
        let newTotal = 0;
    		$('.subtotal').each(function(index) {
    			newTotal += parseFloat($(this).text());
    		});
    		$('.total').text(newTotal);
    	}
    }
	}); 

	//delete costumer|barrower 
  $('#transacRecord').on("click", ".delCostumerId", function(e) {
    e.preventDefault();
    const deleteCostumerId = $(this).data('id');
    //confirm to delete this borrower record
    swal({
     	title: "Are you sure to delete?",
     	text: "Once deleted, you will not be able to recover this record",
     	type: "warning",
     	showCancelButton: true,
     	confirmButtonColor: "#DD6B55",
     	confirmButtonText: "Yes, delete it",
     	cancelButtonText: "No, cancel it",
     	closeOnConfirm: false,
     	closeOnCancel: false,
    },
    function(willDelete) {
      if(willDelete) {
        $.ajax({
          url: "app/actions.controller.php",
          method: "POST",
          data: {
            deleteBtnSet: true,
            deleteCostumerId: deleteCostumerId,
          },
          success: function(data) { 
            swal({
              title: "Deleted!",
              text: "Barrower data deleted successfully",
              type: "success"
            }, function() {
               window.location = "return";
            });
          }
        });
      } else {
        swal("Cancelled!", "Barrower record is safe", "error");
      }
    });
  });

	//data table
  $('#barrowerList, #tbl_pending, #tbl_returned').dataTable({
    ordering: true,
    bJQueryUI: true,
    sPaginationType: "full_numbers"
  });
});