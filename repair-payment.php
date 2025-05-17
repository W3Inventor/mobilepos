<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth-login.php");
    exit;
}

// include 'config/adminacc.php';
include 'config/dbconnect.php';


$paymentMethods = [];
$query = "SELECT pmethod_id, payment_method, payment_method_type, cost_one, cost_two FROM payment_methods";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $paymentMethods[] = $row;
    }
}



?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>Duralux || SEO Settings</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
</head>


<style>
.ml-0{
    margin-left:0 !important;
}
.mr-0{
    margin-right:0 !important;
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

@media only screen and (min-width: 1024px) {

    .mleft-0{
        margin-left: 0 !important;
    }
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
<main class="nxl-container">
    <div class="nxl-content">

                <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Point of Sales </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">POS</li>
                </ul>
            </div>
        </div>

            <!-- [ page-header ] end -->
<div class="main-content">
    <!-- Cart Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Cart</h5>
                
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="proposalList">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Warranty</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Cart items here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Sub Total</strong></td>
                                <td id="subtotal">LKR 0.00</td>
                                <td></td> <!-- Empty cell for delete button space -->
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
    <!-- Customer Details Section -->
    <div class="col-xxl-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Customer Details</h5>
            </div>
            <div class="card-body p-3 d-flex flex-column">
                <form id="customerdetails" method="post" class="form-container pt-2 flex-fill">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>NIC<span class="text-danger">*</span></label>
                            <input id="nic" name="nic" type="text" class="form-control" required>
                            <span id="nic-error" class="text-danger" style="display:none;">Please enter a valid NIC number.</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Full Name<span class="text-danger">*</span></label>
                            <input id="full_name" name="full_name" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 d-grid">
                            <label>Mobile Number<span class="text-danger">*</span></label>
                            <input id="mobile_number" name="mobile_number" type="tel" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input id="email" name="email" type="email" class="form-control">
                        </div>
                    </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <input id="address" name="address" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <a href="#">   <p class="text-decoration-underline small" id="clearCustomerDetails" >Clear</p></a>
                        </div>
                    </div>

                       
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Details Section -->
    <div class="col-xxl-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Payment Details</h5>
            </div>
            <div class="card-body p-3 d-flex flex-column">
                <form id="paymentdetails" method="post" class="form-container pt-2 flex-fill">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Bill Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input id="bill_amount" name="bill_amount" type="number" step="0.01" value="0.00"class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Payment Method<span class="text-danger">*</span></label>
                                <select class="form-select" data-select2-selector="icon" id="payment_method" name="payment_method" required>
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <option value="<?php echo htmlspecialchars($method['pmethod_id']); ?>">
                                            <?php echo htmlspecialchars($method['payment_method']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Cash<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input id="cash_payment" name="cash_payment" type="number" step="0.01" class="form-control" required>
                                </div>
                                <small id="paymentwithcost1"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Card<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input id="card_payment" name="card_payment" type="number" step="0.01" class="form-control" required readonly>
                                </div>
                                <small id="paymentwithcost2"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Payable Amount<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                        <input id="payable_amount" name="payable_amount" type="number" step="0.01" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Total Discount</label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                        <input id="total_discount" name="total_discount" type="number" step="0.01" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label">Reference</label>
                                <input id="reference" name="reference" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label d-block">Bill Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="print_radio" name="bill_type" value="print" checked>
                                    <label class="form-check-label" for="print_radio">Print</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="sms_radio" name="bill_type" value="sms">
                                    <label class="form-check-label" for="sms_radio">SMS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="email_radio" name="bill_type" value="email">
                                    <label class="form-check-label" for="email_radio">Email</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-4 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary w-100">Pay</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

</div>

</div>
</main>


<!-- Modal Structure -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="productModalLabel">Product Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="productForm">
            <div class="row">
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="product_id" class="form-label">ID/IMEI</label>
                    <input type="text" class="form-control" id="product_id" name="product_id" readonly>
                </div>
                </div>
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" id="brand" name="brand" readonly>
                </div>
                </div>
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="model" class="form-label">Model</label>
                    <input type="text" class="form-control" id="model" name="model" readonly>
                </div>
                </div>
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="price" class="form-label">Price (Selling)</label>
                    <div class="input-group">
                        <span class="input-group-text">LKR</span>
                        <select class="form-control" id="price" name="price">
            <!-- Options will be populated here -->
        </select>
                    </div>
                </div>
                </div>
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="warranty" class="form-label">Warranty</label>
                    <select class="form-select" id="warranty" name="warranty"></select>
                </div>
                </div>
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="discount" class="form-label">Discount</label>
                    <input type="text" class="form-control" id="discount" name="discount">
                </div>
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveProductBtn">Save</button>
        </div>
        </div>
    </div>
</div>

    <!--! BEGIN: Vendors JS !-->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/settings-init.min.js"></script>>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="assets/js/notification.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>

$(document).ready(function () {

    

    $('#clearCustomerDetails').on('click', function () {
        // Clear all input fields
        $('#customerdetails input[type="text"], #customerdetails input[type="email"], #customerdetails input[type="tel"]').val('');

        // Hide any error messages
        $('#nic-error').hide();
    });



    $('#nic, #email, #mobile_number').on('input', function () {
        const nic = $('#nic').val();
        const email = $('#email').val();
        const mobile = $('#mobile_number').val();

        if (nic || email || mobile) {
            // Make an AJAX call to fetch customer details
            $.ajax({
                url: 'assets/php/helper/payment-helper/customer-fetch.php',
                method: 'POST',
                data: { nic: nic, email: email, mobile: mobile },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // Autofill customer details if found
                        $('#nic').val(data.customer.nic);
                        $('#full_name').val(data.customer.full_name);
                        $('#mobile_number').val(data.customer.mobile_number);
                        $('#email').val(data.customer.email);
                        $('#address').val(data.customer.address);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }
    });





    // Prevent the default form submission when hitting Enter in the search input
    $('#search').on('keydown', function (event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent the Enter key from submitting the form
        }
    });


    });

  // Function to update the subtotal and bill amount
    function updateSubtotal() {
        let subtotal = 0;

        // Sum the total for all cart items
        $('#proposalList tbody tr').each(function () {
            const total = parseFloat($(this).find('.total').text().replace('LKR ', '')) || 0;
            subtotal += total;
        });

        // Update the subtotal and bill amount
        $('#subtotal').text("LKR " + subtotal.toFixed(2));
        $('#bill_amount').val(subtotal.toFixed(2));
    }

    // Handle delete button click with confirmation
    $(document).on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const row = $(this).closest('tr');

        // Confirmation alert using SweetAlert
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
                // Remove the item row and update the subtotal
                row.remove();
                updateSubtotal();
                Swal.fire('Deleted!', 'The item has been deleted from your cart.', 'success');
            }
        });
    });


    // Function to update the subtotal, bill amount, and total discount in the payment details
    function updateSubtotal() {
        var subtotal = 0;
        var totalDiscount = 0;

        // Iterate through each row in the cart table
        $('#proposalList tbody tr').each(function () {
            var total = parseFloat($(this).find('.total').text().replace('LKR ', '')); // Extract and parse the total amount
            var discount = parseFloat($(this).find('td:nth-child(5)').text().replace('LKR ', '')) || 0; // Extract and parse the discount amount
            subtotal += total; // Sum the total amounts
            totalDiscount += discount; // Sum the discount amounts
        });

        // Update the subtotal and bill amount
        $('#subtotal').text("LKR " + subtotal.toFixed(2)); // Display the updated subtotal
        $('#bill_amount').val(subtotal.toFixed(2)); // Set the numeric value for the bill amount
        $('#bill_amount').siblings('#bill_amount').text("LKR " + subtotal.toFixed(2)); // Display the updated bill amount with the prefix

        // Update the total discount field
        $('#total_discount').val(totalDiscount.toFixed(2)); // Set the numeric value for the total discount
        $('#total_discount').siblings('#total_discount').text("LKR " + totalDiscount.toFixed(2)); // Display the updated total discount with the prefix

        // Calculate the payable amount whenever the subtotal is updated
        calculatePayableAmount();
    }

    // Initialize intl-tel-input
    const mobileInput = document.querySelector("#mobile_number");
    const iti = window.intlTelInput(mobileInput, {
        initialCountry: "auto",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        separateDialCode: true
    });

    // NIC validation
    document.getElementById('nic').addEventListener('input', function () {
        var nicInput = this.value;
        var nicError = document.getElementById('nic-error');

        // Regular expression for validating NIC: 12 digits or 9 digits followed by 'V' or 'X'
        var nicPattern = /^(?:\d{9}[VvXx]|\d{12})$/;

        if (nicPattern.test(nicInput)) {
            nicError.style.display = 'none';
            this.setCustomValidity('');  // Clear any previous validation message
        } else {
            nicError.style.display = 'block';
            this.setCustomValidity('Invalid NIC number');  // Show a custom validation message
        }
    });

    // Payment methods initialization
    var paymentMethods = <?php echo json_encode($paymentMethods); ?>;
    var paymentMethodDetails = {};

    // Populate the map with payment method details
    paymentMethods.forEach(method => {
        paymentMethodDetails[method.pmethod_id] = method;
    });

    // Function to update visibility and behavior of fields based on payment method
    // Update visibility and required behavior of fields based on payment method
    function updatePaymentFields() {
        var selectedMethodId = $('#payment_method').val();
        var selectedMethod = paymentMethodDetails[selectedMethodId] || {};
        var selectedMethodType = selectedMethod.payment_method_type || '';

        // Handle single payment methods (like Cash, Bank Transfer, BNPL, or Card Payment)
        if (['Bank Transfer', 'Cash Payment', 'BNPL', 'Card Payment'].includes(selectedMethodType)) {
            $('#cash_payment').closest('.mb-4').hide();
            $('#card_payment').closest('.mb-4').hide();

            // Remove required attributes to avoid validation errors when fields are hidden
            $('#cash_payment').prop('required', false);
            $('#card_payment').prop('required', false);
        } else {
            // For mixed payment methods, show and set labels appropriately
            var types = selectedMethodType.split(' + ');
            $('#cash_payment').closest('.mb-4').show().find('label').text(types[0] || 'Cash');
            $('#card_payment').closest('.mb-4').show().find('label').text(types[1] || 'Card');

            // Add required attributes to visible fields
            $('#cash_payment').prop('required', true);
            $('#card_payment').prop('required', true);
        }

        // Recalculate payable amount based on selected method
        calculatePayableAmount();
    }

    // Calculate the payable amount and update display elements
    function calculatePayableAmount() {
        var billAmount = parseFloat($('#bill_amount').val()) || 0;
        var cashPayment = parseFloat($('#cash_payment').val()) || 0;
        var cardPayment = billAmount - cashPayment; // Calculate remaining card payment

        // Get payment method and cost details
        var selectedMethodId = $('#payment_method').val();
        var selectedMethod = paymentMethodDetails[selectedMethodId] || {};
        var costOne = selectedMethod.cost_one || '0';
        var costTwo = selectedMethod.cost_two || '0';
        var selectedMethodType = selectedMethod.payment_method_type || '';

        // Initialize payable amounts
        var cashPayable = 0;
        var cardPayable = 0;

        // Handle single payment methods
        if (['Bank Transfer', 'Cash Payment', 'BNPL', 'Card Payment'].includes(selectedMethodType)) {
            cashPayable = applyCost(billAmount, costOne); // Apply cost to the entire amount
            cardPayable = 0; // No card payment required

            // Hide the cash and card fields for single payment methods
            $('#cash_payment').closest('.mb-4').hide();
            $('#card_payment').closest('.mb-4').hide();
        } else {
            // Apply costs to both cash and card for mixed methods
            cashPayable = applyCost(cashPayment, costOne);
            cardPayable = applyCost(cardPayment, costTwo);

            // Ensure both fields are shown
            $('#cash_payment').closest('.mb-4').show();
            $('#card_payment').closest('.mb-4').show();
        }

        // Update the card payment field
        $('#card_payment').val(cardPayment.toFixed(2));

        // Update the small elements displaying cash and card payables
        $('#paymentwithcost1').text("Cash Payable: LKR " + cashPayable.toFixed(2));
        $('#paymentwithcost2').text("Card Payable: LKR " + cardPayable.toFixed(2));

        // Update the payable amount field
        var totalPayable = cashPayable + cardPayable;
        $('#payable_amount').val(totalPayable.toFixed(2));
    }

    // Apply cost logic (percentage or fixed)
    function applyCost(baseValue, cost) {
        if (cost.includes('%')) {
            var percentage = parseFloat(cost.replace('%', '')) / 100;
            return baseValue + (baseValue * percentage);
        } else {
            return baseValue + parseFloat(cost);
        }
    }

    // Initialize payment fields and set up event listeners
    $(document).ready(function () {
        // Event listener for payment method change
        $('#payment_method').on('change', updatePaymentFields);

        // Event listener for cash payment input change
        $('#cash_payment').on('input', calculatePayableAmount);

        // Initialize payment fields visibility and calculations on page load
        updatePaymentFields();
    });

    // Handle form submission for payment
    $('#paymentdetails').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData();

        // Collect Customer Details
        formData.append('nic', $('#nic').val());
        formData.append('full_name', $('#full_name').val());
        formData.append('mobile_number', iti.getNumber());
        formData.append('email', $('#email').val());
        var emailValue = $('#email').val();
        formData.append('email', emailValue);
        console.log('Email address:', emailValue); 
        formData.append('address', $('#address').val());

        // Collect Payment Details
        formData.append('bill_amount', $('#bill_amount').val());
        formData.append('total_discount', $('#total_discount').val());
        formData.append('payable_amount', $('#payable_amount').val());
        formData.append('payment_method', $('#payment_method').val());
        formData.append('cash_payment', $('#cash_payment').val() || 0);
        formData.append('card_payment', $('#card_payment').val() || 0);
        formData.append('payment_cost_1', $('#paymentwithcost1').text().replace('Cash Payable: LKR ', '') || 0);
        formData.append('payment_cost_2', $('#paymentwithcost2').text().replace('Card Payable: LKR ', '') || 0);
        formData.append('reference', $('#reference').val());

        // Collect Bill Type
        var billType = $('input[name="bill_type"]:checked').val();
        formData.append('bill_type', billType);



        // Collect Cart Items
        var cartItems = [];
        $('#proposalList tbody tr').each(function () {
            var item = {
                id: $(this).data('id'),
                item_name: $(this).find('.item').text().trim(),
                price: parseFloat($(this).find('td:nth-child(4)').text().replace('LKR ', '')),
                discount: parseFloat($(this).find('td:nth-child(5)').text().replace('LKR ', '')) || 0,
                quantity: parseInt($(this).find('.quantity').text()),
                total: parseFloat($(this).find('.total').text().replace('LKR ', '')),
                warranty: $(this).find('td:nth-child(6)').text(),
                type: $(this).data('type') || 'accessory',
                imei: $(this).data('imei'),
                serial_number: $(this).data('serial_number')
            };
            cartItems.push(item);
        });
        formData.append('cart_items', JSON.stringify(cartItems));

        // AJAX request to submit the payment details
        $.ajax({
            url: 'assets/php/helper/payment-helper/submit_payment.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                console.log("Server Response:", response); // Debugging line

                if (response.redirect) {
                    var printWindow = window.open(response.redirect, '_blank', 'width=526,height=600,scrollbars=no,toolbar=no,location=no,status=no,menubar=no');
                    printWindow.onload = function () {
                        printWindow.print();
                        clearFormAndCart();
                    };
                }

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.success
                    }).then(() => {
                        clearFormAndCart();
                    });
                }

                if (response.sms_success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SMS Sent',
                        text: response.sms_success
                    }).then(() => {
                        clearFormAndCart();
                    });
                } else if (response.sms_error) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'SMS Error',
                        text: response.sms_error
                    }).then(() => {
                        clearFormAndCart();
                    });
                }

                if (response.email_error) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email Error',
                        text: response.email_error
                    }).then(() => {
                        clearFormAndCart();
                    });
                } else if (response.email_success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.email_success
                    }).then(() => {
                        clearFormAndCart();
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'An error occurred: ' + xhr.responseText
                }).then(() => {
                    clearFormAndCart();
                });
            }
        });

    function clearFormAndCart() {
        // Clear form fields
        $('#paymentdetails').trigger("reset");
        $('#customerdetails').trigger("reset");

        // Clear cart table
        $('#proposalList tbody').empty();

        // Reset subtotal, total discount, and payable amount displays
        $('#subtotal').text("LKR 0.00");
        $('#bill_amount').val("0.00");
        $('#total_discount').val("0.00");
        $('#payable_amount').val("0.00");

        // Clear additional fields or reset custom display elements if needed
        $('#paymentwithcost1').text("");
        $('#paymentwithcost2').text("");
    }
});

</script>
</body>

</html>