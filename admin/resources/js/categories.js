jQuery(document).ready(function() {
  //Datatable
  $('#tbl_category, #tbl_equipType, #tbl_location, #tbl_unitType, #tbl_roomCode').dataTable({
    "ordering": false
  });

  //To show data of category in modal
  $('#tbody_categories').on("click", ".edit_btn", function() {
    $('#category_edit_accountModal').modal('show'); 
    $tr = $(this).closest('tr');
    var dataEditCategory = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    // console.log(dataEditCategory);
    $('#edit_id').val(dataEditCategory[0]); 
    $('#category_name').val(dataEditCategory[1]);
  });

  //To delete data of category
  $('#tbody_categories').on("click", ".delCategoryId", function(e) {
    e.preventDefault();
    var deleteCategoryId = $(this).closest("tr").find('.valCategoryId').val();
    swal({  //pop up confirmation
      title: "Are you sure to delete?",
      text: "Once deleted, you will not be able to recover this record!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, delete it",
      cancelButtonText: "No, cancel it",
      closeOnConfirm: false,
      closeOnCancel: false,
    },
    function(willDeleteCategory) {
      if(willDeleteCategory) { //user click ok
        $.ajax({ //send ajax request to server
          url: "app/actions.controller.php", //file to be send request
          method: "POST",  //send via post method
          data: {  //retrieve data
            deleteBtnSetCategory: 1,
            deleteCategoryId: deleteCategoryId
          },
          success: function(result){    //if success
            swal({
              title: "Deleted!",
              text: "Category data deleted successfully",
              type: "success"
            }, function() {
              window.location = "categories";
            });
          } //end succss
        }); //end ajax
      } else { //user click cancel
        swal("Cancelled!", "Category record is safe", "error");
      }
    });
  });

  //To show data of equipment type in modal
  $('#tbody_equipType').on("click", ".edit_equip", function() {
    $('#equipType_edit_accountModal').modal('show');
    $tr = $(this).closest('tr')
    var dataEditEquipmentType = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    $('#editEquip_id').val(dataEditEquipmentType[0]);
    $('#edit_equip_name').val(dataEditEquipmentType[1]);
  });

  //To delete data of equipment type
  $('#tbody_equipType').on("click", ".delEquipTypeId", function(e) {
    e.preventDefault();
    var deleteEquipTypeId = $(this).closest("tr").find('.valEquipTypeId').val();
    swal({
      title: "Are you sure to delete ?",
      text: "Once deleted, you will not be able to recover this record!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, delete it",
      cancelButtonText: "No, cancel it",
      closeOnConfirm: false,
      closeOnCancel: false,
    },
    function(willDeleteEquipmentType) {
      if(willDeleteEquipmentType) {
        $.ajax({
          url: "app/actions.controller.php",
          method: "POST",
          data: {
            deleteBtnSetEquipType: 1,
            deleteEquipTypeId: deleteEquipTypeId
          },
          success:function(result) {
            swal({
              title: "Deleted!",
              text: "Equipment type data deleted successfully",
              type: "success"
            }, function() {
              window.location = "categories";
            });
          }
        });
      } else {
        swal("Cancelled!", "Equipment type record is safe", "error");
      }
    });
  });
    
  //To show data of location rack in modal
  $('#tbody_location').on("click", ".edit_locrack", function() {
    $('#locrack_edit_accountModal').modal('show');
    $tr = $(this).closest('tr')
    var dataEditLocationRack = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    
    $('#editLocRack_id').val(dataEditLocationRack[0]);
    $('#edit_locrack_name').val(dataEditLocationRack[1]);
  });

  //To delete data of location rack
  $('#tbody_location').on("click", ".delLocationRackId", function(e) {
    e.preventDefault();
    var deleteLocationRackId = $(this).closest("tr").find('.valLocatonRackId').val();
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
    function(willDeleteLocationRack) {
      if(willDeleteLocationRack) {
        $.ajax({
          url: "app/actions.controller.php",
          method: "POST",
          data: {
            deleteBtnSetLocationRack: 1,
            deleteLocationRackId: deleteLocationRackId,
          },
          success:function(result){
            swal({
              title: "Deleted!",
              text: "Location rack data deleted successfully",
              type: "success"
            }, function() {
              window.location = "categories";
            });
          }
        });
      } else {
        swal("Cancelled!", "Location rack record is safe", "error");
      }
    });
  });

  //To show data of unit type in modal
  $('#tbody_unitType').on("click", ".edit_unitType", function() {
    $('#unitType_edit_accountModal').modal('show');
    $tr = $(this).closest('tr');
    var dataEditUnitType = $tr.children("td").map(function() {
      return $(this).text();
    }).get();

    $('#editUnitType_id').val(dataEditUnitType[0]);
    $('#edit_unitType_name').val(dataEditUnitType[1]);
  });

  //To delete data of unit type
  $('#tbody_unitType').on("click", ".delUnitTypeId", function(e) {
    e.preventDefault();
    var deleteUnitTypeId = $(this).closest("tr").find('.valUnitTypeId').val();
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
    function(willDeleteUnitType) {
      if(willDeleteUnitType) {
        $.ajax({
          url: "app/actions.controller.php",
          method: "POST",
          data: {
            deleteBtnSetUnitType: 1,
            deleteUnitTypeId: deleteUnitTypeId
          },
          success:function(result) {
            swal({
              title: "Deleted!",
              text: "Unit type data deleted successfully",
              type: "success"
            }, function() {
              window.location = "categories";
            });
          }
        });
      } else {
        swal("Cancelled!", "Unit type record is safe", "error");
      }
    });
  });

  //To show data of room code in modal
  $('#tbody_roomCode').on('click', '.edit_roomcode', function() {
    $('#editRoomCode').modal('show');
    $tr = $(this).closest('tr');
    var dataEditRoomCode = $tr.children('td').map(function() {
      return $(this).text();
    }).get();

    $('#editRoomCodeId').val(dataEditRoomCode[0]);
    $('#editRoomCodeName').val(dataEditRoomCode[1]);
  });

  //To delete data of room code
  $('#tbody_roomCode').on("click", ".delRoomCodeId", function(e) {
    e.preventDefault();
    var deleteRoomCodeId = $(this).closest("tr").find('.valRoomCodeId').val();
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
    function(willDeleteRoomCode) {
      if(willDeleteRoomCode) {
        $.ajax({
          url: "app/actions.controller.php",
          method: "POST",
          data: {
            deleteBtnRoomCode: 1,
            deleteRoomCodeId: deleteRoomCodeId
          },
          success:function(result) {
            swal({
              title: "Deleted!",
              text: "Room Code data deleted successfully",
              type: "success"
            }, function() {
              window.location = "categories";
            });
          }
        });
      } else {
        swal("Cancelled!", "Room Code record is safe", "error");
      }
    });
  });
});