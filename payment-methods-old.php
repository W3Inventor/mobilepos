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
    <title>Exxplan || Payment Methods</title>
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
    small{
        font-size: 0.8em !important;
        }
    .select2-container--bootstrap-5 .select2-selection--single {
        padding: .75rem !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option{
        font-size:14px !important;
    }
    .select2-container--bootstrap-5 .select2-selection{
        font-size:14px !important;
    }
    .select2-container .select2-selection--single{
        height: 45px;
    }
    i.undefined{
        display:none !important;
    }
        .content-area-body{
        padding: 50px !important; 
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
    <!--! [Start] Main Content !-->
    <main class="nxl-container apps-container">
        <div class="nxl-content">
            

            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Payment Methods</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">Payment Methods</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
 
                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div> 

            <!-- [ page-header ] end -->

            <div class="content-area-body">
                <div class="card mb-5">
                    <div class="card-body">

                        <form action="assets/php/form-helper/add_payment_method.php" id="addPaymentMethodForm" method="post" class="form-container pt-2">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-2">
                                        <label class="form-label">Payment Method<span class="text-danger">*</span></label>
                                        <input class="form-control" name="payment_method" id="payment-method" type="text">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-4">
                                        <label class="form-label d-block">Payment Method Type<span class="text-danger">*</span></label>
                                        <select class="form-select" data-select2-selector="icon" id="payment_method_type" name="payment_method_type" required>
                                            <option value="Cash Payment">Cash Payment</option>
                                            <option value="Card Payment">Card Payment</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="BNPL">Buy Now, Pay Later Services</option>
                                            <option value="Cash + Card">Cash + Card</option>
                                            <option value="Bank Transfer + Card">Bank Transfer + Card</option>
                                            <option value="Cash + BNPL">Cash + Buy Now, Pay Later</option>
                                            <option value="Card + BNPL">Card + Buy Now, Pay Later</option>
                                            <option value="Bank Transfer + BNPL">Bank Transfer + Buy Now, Pay Later</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="mb-2">
                                        <label class="form-label">Cost <span id="option-one"></span><span class="text-danger">*</span></label>
                                        <input class="form-control" name="cost-one" id="cost-two" type="text">
                                        <small>Please add "%" mark after the value if you want to add percentage</small>
                                    </div>
                                </div>

                                <div class="col-3 cost-2">
                                    <div class="mb-2">
                                        <label class="form-label">Cost <span id="option-two"></span><span class="text-danger">*</span></label>
                                        <input class="form-control" name="cost-two" id="cost-two" type="text">
                                        <small>Please add "%" mark after the value if you want to add percentage</small>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-2">
                                        <label class="form-label">Reference</label>
                                        <input class="form-control" name="reference" id="reference" type="text">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0 d-flex">
                                <button type="submit" class="btn btn-primary">Add Payment Method</button>
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
                                                <th>Payment Method</th>
                                                <th>Cost One</th>
                                                <th>Cost Two</th>
                                                <th>Type</th>
                                                <th>Reference</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paymentmethodData">
                                        
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

    <script>
        $(document).ready(function () {

// Function to load payment methods via AJAX
function loadPaymentMethods() {
    $.ajax({
        url: 'assets/php/table-helper/fetch_payment_methods.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            var paymentTable = $('#paymentmethodData');
            paymentTable.empty(); // Clear existing data

            // Loop through each payment method and append to the table
            $.each(data, function (index, method) {
                paymentTable.append(`
                    <tr data-id="${method.pmethod_id}">
                        <td>${index + 1}</td>
                        <td>${method.payment_method}</td>
                        <td>${method.cost_one}</td>
                        <td>${method.cost_two}</td>
                        <td>${method.payment_method_type}</td>
                        <td>${method.reference || ''}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="#" class="avatar-text avatar-md edit-method" data-id="${method.pmethod_id}">
                                    <i class="feather-edit"></i>
                                </a>
                                <a href="#" class="avatar-text avatar-md delete-method" data-id="${method.pmethod_id}">
                                    <i class="feather-trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `);
            });
        },
        error: function (xhr, status, error) {
            showNotification('error', 'Failed to load payment methods.');
        }
    });
}

// Load payment methods on page load
loadPaymentMethods();

// Function to handle showing/hiding cost-two and updating labels
function updateCostFields() {
    var selectedOption = $('#payment_method_type').val();
    var splitOptions = selectedOption.split(' + ');

    if (splitOptions.length === 2) {
        // Show cost-two and update labels
        $('.cost-2').show();
        $('#option-one').text('for ' + splitOptions[0]);
        $('#option-two').text('for ' + splitOptions[1]);
        $('.cost-one').removeClass('col-6').addClass('col-3'); // Adjust classes for cost-one
    } else {
        // Hide cost-two and adjust class for cost-one
        $('.cost-2').hide();
        $('#option-one').text(''); // Reset label
        $('#option-two').text(''); // Reset label
        $('.cost-one').removeClass('col-3').addClass('col-6'); // Adjust classes for cost-one
    }
}

// Trigger update on page load and when the selection changes
updateCostFields();
$('#payment_method_type').change(updateCostFields);

// Handle form submission for both add and update
$('#addPaymentMethodForm').on('submit', function (e) {
    e.preventDefault();

    var formData = $(this).serialize();

    console.log(formData); // Debugging: Log the data being sent

    $.ajax({
        type: 'POST',
        url: 'assets/php/form-helper/add_payment_method.php',
        data: formData,
        success: function (response) {
            console.log(response); // Debugging: Log the server response

            try {
                var result = JSON.parse(response);

                if (result.status == 'success') {
                    showNotification('success', 'Payment method added successfully.');
                    loadPaymentMethods(); // Refresh the table with new data
                    resetForm(); // Reset the form to its initial state
                } else {
                    showNotification('error', result.message || 'Failed to save payment method.');
                }
            } catch (e) {
                showNotification('error', 'Invalid server response.');
            }
        },
        error: function () {
            showNotification('error', 'An error occurred.');
        }
    });
});

// Handle edit button click
$(document).on('click', '.edit-method', function (e) {
    e.preventDefault();

    var methodId = $(this).data('id');

    $.ajax({
        url: 'assets/php/form-helper/get_payment_method.php',
        type: 'GET',
        data: { id: methodId },
        dataType: 'json',
        success: function (data) {
            // Fill the form with the retrieved data
            $('input[name="payment_method"]').val(data.payment_method);
            $('input[name="cost-one"]').val(data.cost_one);
            $('input[name="cost-two"]').val(data.cost_two);
            $('select[name="payment_method_type"]').val(data.payment_method_type).trigger('change');
            $('input[name="reference"]').val(data.reference);

            // Change form action to update
            $('#addPaymentMethodForm').attr('data-id', methodId);
            $('#addPaymentMethodForm button[type="submit"]').text('Update Payment Method');

            // Show the cancel button
            $('#cancelEdit').show();
        },
        error: function (xhr, status, error) {
            showNotification('error', 'Failed to fetch payment method.');
        }
    });
});

// Handle cancel button click
$('#cancelEdit').on('click', function () {
    resetForm(); // Reset the form to its initial state
});

// Function to reset the form to its initial state
function resetForm() {
    $('#addPaymentMethodForm')[0].reset(); // Clear the form fields
    $('#addPaymentMethodForm').removeAttr('data-id'); // Remove the data-id attribute
    $('#addPaymentMethodForm button[type="submit"]').text('Add Payment Method'); // Reset the submit button text
    $('#cancelEdit').hide(); // Hide the cancel button
    updateCostFields(); // Reset cost fields visibility and labels
}

// Handle delete button click
$(document).on('click', '.delete-method', function (e) {
    e.preventDefault();

    var methodId = $(this).data('id');

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
                url: 'assets/php/form-helper/delete_payment_method.php',
                type: 'POST',
                data: { id: methodId },
                success: function (response) {
                    try {
                        var result = JSON.parse(response);

                        if (result.status == 'success') {
                            showNotification('success', 'Payment method deleted successfully.');
                            loadPaymentMethods(); // Refresh the table with new data
                        } else {
                            showNotification('error', result.message || 'Failed to delete payment method.');
                        }
                    } catch (e) {
                        showNotification('error', 'Invalid server response.');
                    }
                },
                error: function (xhr, status, error) {
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
