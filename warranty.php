<?php
include 'config/adminacc.php';

?>

<?php
include 'config/dbconnect.php';
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <title>Exxplan || Warranty Details</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
</head>
<style>
        .color-picker {
            padding: 0;
        }

        /* Style for the favicon preview */
        #faviconPreview, .position-relative.overflow-hidden.border.border-gray-2.rounded.favicon {
            width: 100px;
            height: 100px;
            object-fit: cover;
            cursor:pointer;
        }

        /* Style for the logo preview */
        #logoPreview, .position-relative.overflow-hidden.border.border-gray-2.rounded.logo  {
            width: 180px;
            height: auto;
            object-fit: contain;
            cursor:pointer;
        }

        /* Hide file input */
        .hidden-file-input {
            display: none;
        }

        /* Style for upload buttons */
        .upload-button {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
        }
    </style>
<body>
    <!--! [Start] Navigation Manu !-->
    <?php
    include 'templates/navigation.php'
    ?>
    <!--! [End]  Navigation Manu !-->

    <!--! [Start] Header !-->
    <?php
    include 'templates/header.php'
    ?>

    <!--! [End] Header !-->

    <main class="nxl-container apps-container">
        <div class="nxl-content without-header nxl-full-content">
            <div class="main-content d-flex">
                <!-- [ Content Sidebar ] start -->
                <?php
                include 'templates/setting-sidebar.php'
                ?>
                <!-- [ Content Sidebar  ] end -->
                <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                    <div class="content-area-header bg-white sticky-top">
                        <div class="page-header-left">
                            <a href="javascript:void(0);" class="app-sidebar-open-trigger me-2">
                                <i class="feather-align-left fs-24"></i>
                            </a>
                        </div>   
                    </div>
                    <div class="content-area-body">
        <div class="card mb-5">
            <div class="card-body">

            <form action="assets/php/form-helper/addwarranty.php" id="addWarrantyForm" method="post" class="form-container pt-2">
                <div class="mb-2">
                    <label class="form-label">Warranty Type</label>
                    <input class="form-control" name="warranty">
                </div>

                <div class="mb-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" cols="30" rows="2"></textarea>
                </div>

                <div class="mb-0 d-flex">
                    <button type="submit" class="btn btn-primary">Add Warranty</button>
                    <button type="button" id="cancelEdit" class="btn btn-secondary ms-2" style="display: none;">Cancel</button>

                </div>
            </form>



            </div>
        </div>
            <div class="card-body">
                    <div class="col-lg-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body p-5">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="row">No</th>
                                                <th>Warranty Type</th>
                                                <th>Description</th>   
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="warrantyData">
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>

    </div>
</div>
</main>
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->

    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/setting-pw.js"></script>
    <script src="assets/vendors/js/circle-progress.min.js"></script>
    <script src="assets/vendors/js/dataTables.min.js"></script>
    <script src="assets/vendors/js/dataTables.bs5.min.js"></script>
    <script src="assets/vendors/js/tagify.min.js"></script>
    <script src="assets/vendors/js/tagify-data.min.js"></script>
    <script src="assets/vendors/js/quill.min.js"></script>
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/proposal-init.min.js"></script>
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <script src="assets/js/notification.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


    <!--! END: Theme Customizer !-->


    <script>
        $(document).ready(function() {

// Function to load warranties via AJAX
function loadWarranties() {
    $.ajax({
        url: 'assets/php/table-helper/fetch_warranties.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var warrantyTable = $('#warrantyData');
            warrantyTable.empty(); // Clear existing data

            // Loop through each warranty and append to the table
            $.each(data, function(index, warranty) {
                warrantyTable.append(`
                    <tr data-id="${warranty.w_id}">
                        <td>${index + 1}</td>  <!-- Sequential numbering -->
                        <td>${warranty.warranty}</td>
                        <td>${warranty.description || ''}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="#" class="avatar-text avatar-md edit-warranty" data-id="${warranty.w_id}">
                                    <i class="feather-edit"></i>
                                </a>
                                <a href="#" class="avatar-text avatar-md delete-warranty" data-id="${warranty.w_id}">
                                    <i class="feather-trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `);
            });
        },
        error: function(xhr, status, error) {
        }
    });
}

// Load warranties on page load
loadWarranties();

// Handle form submission for both add and update
$('#addWarrantyForm').on('submit', function(e) {
    e.preventDefault();

    var warranty = $('input[name="warranty"]').val();
    var warrantyId = $(this).attr('data-id') || '';

    if(warranty.trim() === '') {
        showNotification('error', 'Warranty type is required.');
        return;
    }

    var formData = $(this).serialize();
    var url = warrantyId ? 'assets/php/form-helper/update_warranty.php' : 'assets/php/form-helper/addwarranty.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: formData + '&id=' + warrantyId,
        success: function(response) {
            try {
                var result = JSON.parse(response);

                if(result.status == 'success') {
                    showNotification('success', warrantyId ? 'Warranty updated successfully.' : 'Warranty added successfully.');
                    loadWarranties(); // Refresh the table with new data
                    resetForm(); // Reset the form to its initial state
                } else {
                    showNotification('error', result.message || 'Failed to save warranty.');
                }
            } catch(e) {
                showNotification('error', 'Invalid server response.');
            }
        },
        error: function() {
            showNotification('error', 'An error occurred.');
        }
    });
});

// Handle edit button click
$(document).on('click', '.edit-warranty', function(e) {
    e.preventDefault();
    
    var warrantyId = $(this).data('id');
    
    $.ajax({
        url: 'assets/php/form-helper/get_warranty.php',
        type: 'GET',
        data: { id: warrantyId },
        dataType: 'json',
        success: function(data) {
            // Fill the form with the retrieved data
            $('input[name="warranty"]').val(data.warranty);
            $('textarea[name="description"]').val(data.description);

            // Change form action to update
            $('#addWarrantyForm').attr('data-id', warrantyId);
            $('#addWarrantyForm button[type="submit"]').text('Update Warranty');

            // Show the cancel button
            $('#cancelEdit').show();
        },
        error: function(xhr, status, error) {
        }
    });
});

// Handle cancel button click
$('#cancelEdit').on('click', function() {
    resetForm(); // Reset the form to its initial state
});

// Function to reset the form to its initial state
function resetForm() {
    $('#addWarrantyForm')[0].reset(); // Clear the form fields
    $('#addWarrantyForm').removeAttr('data-id'); // Remove the data-id attribute
    $('#addWarrantyForm button[type="submit"]').text('Add Warranty'); // Reset the submit button text
    $('#cancelEdit').hide(); // Hide the cancel button
}

// Handle delete button click
$(document).on('click', '.delete-warranty', function(e) {
    e.preventDefault();
    
    var warrantyId = $(this).data('id');

    // Show confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'assets/php/form-helper/delete_warranty.php',
                type: 'POST',
                data: { id: warrantyId },
                success: function(response) {
                    try {
                        var result = JSON.parse(response);

                        if(result.status == 'success') {
                            showNotification('success', 'Warranty deleted successfully.');
                            loadWarranties(); // Refresh the table with new data
                        } else {
                            showNotification('error', result.message || 'Failed to delete warranty.');
                        }
                    } catch(e) {
                        showNotification('error', 'Invalid server response.');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('error', 'An error occurred.');
                }
            });
        }
    });
});

});
</script>

</body>

</html>