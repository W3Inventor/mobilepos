<?php

session_start();


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth-login.php");
    exit;
}

// include 'config/adminacc.php';
include 'config/dbconnect.php';

$customer_data = null;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

if ($customer_id) {
    $stmt = $conn->prepare("SELECT full_name, nic, mobile_number, email, address FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer_data = $result->fetch_assoc();
    $stmt->close();
}

$repair_id = isset($_GET['repair_id']) ? (int)$_GET['repair_id'] : 0;
$invoice_id = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
$invoice_items = [];
if ($invoice_id) {
    // Ensure the invoice is unpaid
    $stmt = $conn->prepare("SELECT status FROM repair_invoices WHERE invoice_id = ?");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $stmt->bind_result($inv_status);
    $stmt->fetch();
    $stmt->close();
    if ($inv_status === 'unpaid') {
        // Fetch all items for this invoice
        $stmt = $conn->prepare("SELECT * FROM repair_invoice_items WHERE invoice_id = ?");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $invoice_items[] = $row;
        }
        $stmt->close();
    }
}


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
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
                        <?php $count = 0; ?>
                        <tbody>
                        <?php foreach ($invoice_items as $item): ?>
                            <?php
                                $count++;
                                $type = $item['accessory_id'] ? 'accessory' : 'manual';
                                $id   = $item['accessory_id'] ?: 0;
                                $qty  = (int)$item['quantity'];
                                $price = number_format($item['price'], 2, '.', '');
                                $total = number_format($qty * $item['price'], 2, '.', '');
                                $item_name = htmlspecialchars($item['item_name']);
                                $warranty = htmlspecialchars($item['warranty']);
                                $serial = htmlspecialchars($item['serial_number']);
                            ?>
                            <tr data-id="<?= $id ?>" data-type="<?= $type ?>" data-imei="" data-serial_number="<?= $serial ?>">
                                <td><?= $count ?></td>
                                <td class="item"><?= $item_name ?></td>
                                <td class="quantity"><?= $qty ?></td>
                                <td>LKR <?= $price ?></td>
                                <td>LKR 0.00</td>
                                <td><?= $warranty ?></td>
                                <td class="total">LKR <?= $total ?></td>
                                <td>
                                    <button class="btn btn-link text-danger delete-btn">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                            <input id="nic" name="nic" type="text" class="form-control" value="<?= $customer_data['nic'] ?? '' ?>" <?= $customer_data ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Full Name<span class="text-danger">*</span></label>
                            <input id="full_name" name="full_name" type="text" class="form-control" value="<?= $customer_data['full_name'] ?? '' ?>" <?= $customer_data ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 d-grid">
                            <label>Mobile Number<span class="text-danger">*</span></label>
                            <input id="mobile_number" name="mobile_number" type="tel" class="form-control" value="<?= $customer_data['mobile_number'] ?? '' ?>" <?= $customer_data ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input id="email" name="email" type="email" class="form-control" value="<?= $customer_data['email'] ?? '' ?>" <?= $customer_data ? 'readonly' : '' ?>>
                        </div>
                    </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <input id="address" name="address" type="text" class="form-control" value="<?= $customer_data['address'] ?? '' ?>" <?= $customer_data ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <!-- <div class="col-12">
                        <div class="d-flex">
                            <a href="#">   <p class="text-decoration-underline small" id="clearCustomerDetails" >Clear</p></a>
                        </div>
                    </div> -->

                       
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/setting-pw.js"></script>
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/settings-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="assets/js/notification.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>

    $(document).ready(function () {
        updateSubtotal();
        $('#paymentdetails').on('submit', function (e) {
            e.preventDefault();

            const customer = {
                id: $('#customer_id').val(),
                full_name: $('#full_name').val(),
                mobile_number: $('#mobile_number').val(),
                email: $('#email').val(),
                address: $('#address').val()
            };

            const payment = {
                bill_amount: parseFloat($('#bill_amount').val()),
                total_discount: parseFloat($('#total_discount').val()),
                payable_amount: parseFloat($('#payable_amount').val()),
                method: $('#payment_method').val(),
                cash_payment: parseFloat($('#cash_payment').val()) || 0,
                card_payment: parseFloat($('#card_payment').val()) || 0,
                reference: $('#reference').val()
            };

            const cart_items = [];
            $('#proposalList tbody tr').each(function () {
                const row = $(this);
                cart_items.push({
                    id: row.data('id'),
                    item_name: row.find('.item').text().trim(),
                    quantity: parseInt(row.find('.quantity').text()),
                    price: parseFloat(row.find('td:nth-child(4)').text().replace('LKR ', '')) || 0,
                    discount: parseFloat(row.find('td:nth-child(5)').text().replace('LKR ', '')) || 0,
                    warranty: row.find('td:nth-child(6)').text().trim(),
                    type: row.data('type'),
                    serial_number: row.data('serial_number') || '',
                    imei: row.data('imei') || ''
                });
            });


            
            const bill_type = $('input[name="bill_type"]:checked').val();

            $.ajax({
            url: 'assets/php/helper/payment-helper/submit_repair_payment.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                repair_id: <?php echo $repair_id; ?>,
                customer: customer,
                payment: payment,
                cart_items: cart_items,
                bill_type: bill_type
            }),
            success: function(response) {
                // Print invoice PDF if redirect link is provided
                if (response.redirect) {
                    // Open the PDF invoice in a new window for printing
                    const printWindow = window.open(response.redirect, '_blank', 
                        'width=526,height=600,scrollbars=no,toolbar=no,location=no,status=no,menubar=no');
                    printWindow.onload = function () {
                        printWindow.print();      // Open print dialog for the PDF
                        clearFormAndCart();       // Clear the form and cart after printing
                    };
                }

                // If an SMS was sent successfully
                if (response.sms_success) {
                    Swal.fire('SMS Sent', response.sms_success, 'success').then(clearFormAndCart);
                } else if (response.sms_error) {
                    Swal.fire('SMS Error', response.sms_error, 'warning').then(clearFormAndCart);
                }

                // If an Email was sent successfully
                if (response.email_success) {
                    Swal.fire('Email Sent', response.email_success, 'success').then(clearFormAndCart);
                } else if (response.email_error) {
                    Swal.fire('Email Error', response.email_error, 'warning').then(clearFormAndCart);
                }

                // If none of the above, but a generic success message is returned
                if (response.success) {
                    Swal.fire('Success', response.success, 'success').then(clearFormAndCart);
                }
            },
            error: function (xhr) {
                // Handle unexpected errors
                Swal.fire('Error', 'An unexpected error occurred: ' + xhr.responseText, 'error');
            }

        });
        
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
            var subtotal = 0;
            var totalDiscount = 0;

            $('#proposalList tbody tr').each(function () {
                const totalText = $(this).find('.total').text().replace('LKR', '').replace(',', '').trim();
                const discountText = $(this).find('td:nth-child(5)').text().replace('LKR', '').replace(',', '').trim();

                const total = parseFloat(totalText) || 0;
                const discount = parseFloat(discountText) || 0;

                subtotal += total;
                totalDiscount += discount;
            });

            $('#subtotal').text("LKR " + subtotal.toFixed(2));
            $('#bill_amount').val(subtotal.toFixed(2));
            $('#total_discount').val(totalDiscount.toFixed(2));
            $('#payable_amount').val(subtotal.toFixed(2)); // initially same as bill
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
        // Event listener for payment method change
        $('#payment_method').on('change', updatePaymentFields);

        // Event listener for cash payment input change
        $('#cash_payment').on('input', calculatePayableAmount);

        // Initialize payment fields visibility and calculations on page load
        updatePaymentFields();

</script>
</body>

</html>

