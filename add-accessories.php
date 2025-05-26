<?php
// include 'config/adminacc.php';
include 'config/dbconnect.php';

?>


<style>


/* Chrome, Safari, Edge, Opera */
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}


.form-control.invalid {
        border-color: #dc3545; /* Red border for invalid input */
    }

    .form-control.invalid::after {
        content: attr(title); /* Use title attribute for tooltip content */
        position: absolute;
        background-color: #dc3545;
        color: #fff;
        padding: 5px;
        border-radius: 4px;
        white-space: nowrap;
        font-size: 12px;
        top: 100%; /* Position below the input */
        left: 0;
        z-index: 1000;
    }

    /* Adjust positioning for better tooltip display */
    .form-control-wrapper {
        position: relative;
    }

.select2-container{
    z-index: 1 !important;
}

    .iti__country-list{

        z-index: 2 !important;
    }

    .iti{
        display: block !important;
    }
    .select2-selection__placeholder{
        color:#283c50 !important;
        font-weight: 500;
        line-height: 1.5;
        ont-size: 13px;

    }

    .select2-selection__arrow{
        margin-top:10px !important;
    }
    .content-area-body{
        padding: 30px 30px !important; 
    }

    i.undefined{
        display:none !important;
    }

    /* .mb-6.form-check {
    margin-top: 3rem !important;
} */

.mb-7 {
    margin-bottom: 1.9rem;
}
</style>

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
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->

    <main class="nxl-container">
    <div class="nxl-content">

                    <!-- [ page-header ] start -->
                    <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Add Accessories</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item">Add Accessories</li>
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

                                <a href="add-accessories.php" class="btn btn-md btn-light">
                                    <i class="feather-plus me-2"></i>
                                    <span>Accessories Without Serial Numbers</span>
                                </a>

                                <a href="add-accessories-2.php" class="btn btn-md btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Accessories With Serial Numbers</span>
                                </a>

                            
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
    <div class="card mb-0">
    <div class="card-body">
    <form action="assets/php/form-helper/addaccessories.php" id="addaccessoriesForm" method="post" class="form-container pt-2">
    <div class="row">
        <!-- Supplier Details -->
        <div class="col-md-5">
            <h5 class="text-center mb-4">Supplier Details</h5>
            <div class="row">
                <div class="col-6">
                    <div class="mb-5">
                        <label>Bill No<span class="text-danger">*</span></label>
                        <input id="bill_no" name="billno" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Bill Amount<span class="text-danger">*</span></label>
                        <input id="bill_amount" name="bill_amount" type="number" step="0.01" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-5">
                        <label>Company Name</label>
                        <select id="company_name" name="company_name" class="form-control" style="width:100%;" required>
                            <option value="">Select or type to add new</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Supplier Name<span class="text-danger">*</span></label>
                        <select id="supplier_name" name="supplier_name" class="form-control" style="width:100%;" required>
                            <option value="">Select or type to add new</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-5 teldiv">
                        <label>Mobile Number<span class="text-danger">*</span></label>
                        <input id="mobile_number" name="mobile_number" type="tel" class="form-control" required>
                        <input type="hidden" id="country_code" name="country_code">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Address</label>
                        <input id="address" name="address" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label for="date">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>

        <!-- Vertical Line -->
        <div class="col-md-1 d-flex justify-content-center align-items-center">
            <div class="vr" style="height: 100%; width: 1px; background-color: #000;"></div>
        </div>

        <!-- Accessory Details -->
        <div class="col-md-6">
            <h5 class="text-center mb-4">Accessory Details</h5>
            <div class="row">
                <div class="col-6">
                <div class="mb-5">
                        <label>Barcode<span class="text-danger">*</span></label>
                        <input name="accessory_id" id="barcode" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Brand<span class="text-danger">*</span></label>
                        <select id="brand_name" name="brand_name" class="form-control" style="width:100%;" required>
                            <option value="">Select or type to add new</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo htmlspecialchars($brand['id']); ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-5">
                        <label>Accessory Name<span class="text-danger">*</span></label>
                        <input id="accessory_name" name="accessory_name" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Quantity<span class="text-danger">*</span></label>
                        <input name="quantity" id="quantity" type="number" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="mb-5">
                        <label>Color</label>
                        <input name="color" id="color" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Other Variation</label>
                        <input name="other" id='other' type="text" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="mb-5">
                        <label>Buying Price<span class="text-danger">*</span></label>
                        <input name="buying" id="buying_price" type="number" step="0.01" class="form-control" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-5">
                        <label>Selling Price<span class="text-danger">*</span></label>
                        <input name="selling" id='selling_price' type="number" step="0.01" class="form-control" required>
                    </div>
                </div>
            </div>
                
                <div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Add Accessories</button>
                    </div>
                </div>

            

        </div>
    </div>
</form>

</div>

    </div>
    </div>


</div>
</main>
    <!--! [End] Main Content !-->
    
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/setting-pw.js"></script>
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/settings-init.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="assets/js/notification.js"></script>


    


<script>

$(document).ready(function() {
    // Initialize intl-tel-input
    const mobileInput = document.querySelector("#mobile_number");
    const iti = window.intlTelInput(mobileInput, {
        initialCountry: "auto",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        separateDialCode: true
    });

    // Real-time validation function with tooltips
    function validatePrices() {
        const buyingPrice = parseFloat(document.getElementById("buying_price").value);
        const sellingPrice = parseFloat(document.getElementById("selling_price").value);

        let isValid = true;

        // Clear previous tooltips
        clearTooltips();

        // Selling Price < Buying Price
        if (sellingPrice < buyingPrice) {
            showTooltip("selling_price", "Selling Price must be greater than Buying Price.");
            isValid = false;
        }

        return isValid;
    }

    // Function to show tooltip
    function showTooltip(elementId, message) {
        const element = document.getElementById(elementId);
        element.classList.add("invalid");
        element.setAttribute("title", message);
    }

    // Function to clear all tooltips
    function clearTooltips() {
        const elements = document.querySelectorAll(".form-control");
        elements.forEach(function(element) {
            element.classList.remove("invalid");
            element.removeAttribute("title");
        });
    }

    // Attach real-time validation to relevant input fields
    document.getElementById("buying_price").addEventListener("input", validatePrices);
    document.getElementById("selling_price").addEventListener("input", function() {
        validatePrices();  // Trigger validation when selling price is updated
    });

    // Handle barcode input change to autofill accessory details
    $('#barcode').on('input', function() {
        const accessory_id = $(this).val();

        // Check if the accessory ID (barcode) is not empty and at least 3 characters long
        if (accessory_id.length >= 3) {
            $.ajax({
                url: 'assets/php/add-accessory-helper/check_barcode.php', // PHP script to handle the check
                type: 'GET',
                data: { accessory_id: accessory_id },
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        // Autofill the accessory details
                        $('#accessory_name').val(response.accessory_name);

                        // Set the value for the brand select2 dropdown
                        const brandOption = new Option(response.brand, response.brand, true, true);
                        $('#brand_name').append(brandOption).trigger('change');

                        $('#color').val(response.color);
                        $('#other').val(response.other);
                    } else {
                        // Clear the fields if barcode not found
                        clearAccessoryFields();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching accessory details:', error);
                }
            });
        } else {
            // Clear fields if the input is less than 3 characters
            clearAccessoryFields();
        }
    });

    // Function to clear accessory-related fields
    function clearAccessoryFields() {
        $('#accessory_name').val('');
        $('#brand_name').val(null).trigger('change'); // Clear the select2 field
        $('#color').val('');
        $('#other').val('');
    }

    // Form submission with AJAX
    $('#addaccessoriesForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Perform validation
        if (!validatePrices()) {
            return; // Stop form submission if validation fails
        }

        // Serialize the form data
        var formData = $(this).serialize();

        // AJAX request to submit the form data
        $.ajax({
            url: 'assets/php/form-helper/addaccessories.php', // The PHP script that handles the form submission
            type: 'POST',
            data: formData,
            dataType: 'json', // Expect JSON response from server
            success: function(response) {
                // Display a success or error notification based on the response
                if (response.status === 'success') {
                    showNotification('success', response.message);

                    // Clear form fields
                    resetForm();
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle error and display an error notification
                showNotification('error', 'Error occurred while adding mobile');
            }
        });
    });

    // Initialize Select2 for Company Name
    $('#company_name').select2({
        tags: true,
        placeholder: 'Select or type to add new',
        ajax: {
            url: 'assets/php/add-mobile-helper/get_companies.php',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.company_name,
                            id: item.company_name
                        }
                    })
                };
            }
        }
    });

    // Initialize Select2 for Supplier Name
    $('#supplier_name').select2({
        tags: true,
        placeholder: 'Select or type to add new',
        ajax: {
            url: 'assets/php/add-mobile-helper/get_suppliers.php',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.supplier_name,
                            id: item.supplier_name
                        }
                    })
                };
            }
        }
    });

    // Fetch Supplier Details based on Company Name or Supplier Name
    function fetchSupplierDetails(companyName, supplierName) {
        $.ajax({
            url: 'assets/php/add-mobile-helper/get_supplier_details.php',
            method: 'GET',
            data: { company_name: companyName, supplier_name: supplierName },
            dataType: 'json',
            success: function(data) {
                if (data && !data.error) {
                    // Always update the address and mobile number based on the latest selection
                    $('#mobile_number').val(data.mobile_number || '');
                    $('#address').val(data.address || '');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching supplier details:', error);
            }
        });
    }

    // Event listeners for company and supplier name changes
    $('#company_name').on('change', function() {
        var companyName = $(this).val();
        var supplierName = $('#supplier_name').val();
        fetchSupplierDetails(companyName, supplierName);
    });

    $('#supplier_name').on('change', function() {
        var supplierName = $(this).val();
        var companyName = $('#company_name').val();
        fetchSupplierDetails(companyName, supplierName);
    });

    // Fill Supplier Details based on Bill Number
    $('#bill_no').on('input', function() {
        var billNo = $(this).val();
        if (billNo) {
            $.ajax({
                url: 'assets/php/add-mobile-helper/get_bill_details.php',
                method: 'GET',
                data: { billno: billNo },
                dataType: 'json',
                success: function(data) {
                    if (data && !data.error) {
                        // Autofill Bill Amount
                        $('#bill_amount').val(data.bill.bill_amount);

                        // Set and trigger Select2 for Company Name
                        const companyOption = new Option(data.supplier.company_name, data.supplier.company_name, true, true);
                        $('#company_name').append(companyOption).trigger('change');

                        // Set and trigger Select2 for Supplier Name
                        const supplierOption = new Option(data.supplier.supplier_name, data.supplier.supplier_name, true, true);
                        $('#supplier_name').append(supplierOption).trigger('change');

                        // Autofill Mobile Number and Address
                        $('#mobile_number').val(data.supplier.mobile_number);
                        $('#address').val(data.supplier.address);

                        // Autofill Date if provided
                        if (data.bill.date) {
                            $('#date').val(data.bill.date);
                        }
                    } else {
                        clearBillFields();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching bill details:', error);
                }
            });
        } else {
            clearBillFields();
        }
    });

    // Function to clear bill-related fields
    function clearBillFields() {
        $('#bill_amount').val('');
        $('#company_name').val(null).trigger('change'); // Clear Select2 field
        $('#supplier_name').val(null).trigger('change'); // Clear Select2 field
        $('#mobile_number').val('');
        $('#address').val('');
    }

    // Initialize Select2 for Brand Name
    $('#brand_name').select2({
        tags: true,
        placeholder: 'Select or type to add new',
        ajax: {
            url: 'assets/php/add-accessory-helper/get_brands.php',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.brand,
                            id: item.brand
                        }
                    })
                };
            }
        }
    });

    // Reset form after successful submission
    function resetForm() {
        $('#addaccessoriesForm')[0].reset();
        $('#company_name').val(null).trigger('change');
        $('#supplier_name').val(null).trigger('change');
        $('#brand_name').val(null).trigger('change');
        $('#model_name').val(null).trigger('change');
        $('#ram').val(null).trigger('change');
        $('#storage').val(null).trigger('change');
        $('#color').val(null).trigger('change');
    }

    // Function to show notifications
    function showNotification(type, message) {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Success' : 'Error',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }
});


</script>


</body>

</html>