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
                            <h5 class="m-b-10">In-House Repair</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item">In-House Repair</li>
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
    <form action="assets/php/helper/repair-helper/in_house_repair_submit.php" id="inhouserepairForm" method="post" class="form-container pt-2">
    <div class="row">
        <!-- Supplier Details -->
        <div class="col-md-5">

        <h5 class="text-center mb-4">Device Details</h5>
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label>IMEI<span class="text-danger">*</span></label>
                        <input name="imei" id="imei" type="text" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
            <div class="col-6">
                <div class="mb-3">
                    <label>Brand<span class="text-danger">*</span></label>
                    <select id="brand_name" name="brand_name" class="form-control" style="width:100%;" required>
                        <option value="">Select or type to add new</option>
                    </select>
                </div>
            </div>

                <div class="col-6">
                    <div class="mb-3">
                        <label>Phone Model<span class="text-danger">*</span></label>
                        <input id="phone_model" name="phone_model" type="text" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label>Reason (Issue)<span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="10" required style="height: 100px;"></textarea>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label>Estimate Amount<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">LKR</span>
                                <input id="estimate_amount" name="estimate_amount" type="number" step="0.01" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label>Upload Images<span class="text-danger">*</span></label>
                        <input 
                            type="file" 
                            name="images[]" 
                            id="imageUpload" 
                            class="form-control" 
                            accept="image/*" 
                            multiple>
                        <small class="form-text text-muted">You can upload up to 5 images. Uploaded images will be shown below.</small>
                        <span id="image-error" class="text-danger" style="display:none;">You can only upload up to 5 images.</span>
                    </div>

                    <!-- Preview Section -->
                    <div id="imagePreviewContainer" class="mt-3 d-flex flex-wrap gap-3"></div>

                    <!-- Hidden Input for Uploaded Image Paths -->
                    <input type="hidden" id="uploadedImagePaths" name="uploaded_image_paths">

                    </div>
                </div>

            
        </div>

        <!-- Vertical Line -->
        <div class="col-md-1 d-flex justify-content-center align-items-center">
            <div class="vr" style="height: 100%; width: 1px; background-color: #000;"></div>
        </div>

        <!-- Accessory Details -->
        <div class="col-md-6">

        <h5 class="text-center mb-4">Customer Details</h5>
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
            

                
                <div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
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
        // 1. Initialize Brand Dropdown with Select2
        $('#brand_name').select2({
            tags: true,
            placeholder: 'Select or type to add new',
            ajax: {
                url: 'assets/php/helper/repair-helper/get_brands.php',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.map(item => ({ id: item.brand, text: item.brand }))
                    };
                }
            }
        });

        // 2. Initialize Phone Input
        const iti = window.intlTelInput(document.querySelector("#mobile_number"), {
            initialCountry: "auto",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            separateDialCode: true
        });

        // 3. Autofill Customer on Input
        $('#nic, #email, #mobile_number').on('input', function () {
            const nic = $('#nic').val();
            const email = $('#email').val();
            const mobile = $('#mobile_number').val();
            if (nic || email || mobile) {
                $.post('assets/php/helper/payment-helper/customer-fetch.php', { nic, email, mobile }, function (data) {
                    if (data.success) {
                        $('#nic').val(data.customer.nic);
                        $('#full_name').val(data.customer.full_name);
                        $('#mobile_number').val(data.customer.mobile_number);
                        $('#email').val(data.customer.email);
                        $('#address').val(data.customer.address);
                    }
                }, 'json');
            }
        });

        // 4. NIC Validation
        $('#nic').on('input', function () {
            const pattern = /^(?:\d{9}[VvXx]|\d{12})$/;
            const isValid = pattern.test(this.value);
            $('#nic-error').toggle(!isValid);
            this.setCustomValidity(isValid ? '' : 'Invalid NIC');
        });

        // 5. Image Upload with AJAX
        let uploadedImagePaths = [];
        $('#imageUpload').on('change', function () {
            const files = this.files;
            const preview = $('#imagePreviewContainer');
            const imageError = $('#image-error');

            if (uploadedImagePaths.length + files.length > 5) {
                imageError.show();
                return;
            } else {
                imageError.hide();
            }

            [...files].forEach(file => {
                const formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: 'assets/php/helper/repair-helper/upload_image.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success(response) {
                        if (response.success) {
                            uploadedImagePaths.push(response.imagePath);
                            $('#uploadedImagePaths').val(uploadedImagePaths.join(','));

                            const previewHTML = `
                                <div class="uploaded-image-wrapper position-relative">
                                    <img src="${response.imagePath}" class="img-thumbnail" style="width: 100px; height: 100px;">
                                    <button type="button" class="btn-close position-absolute top-0 end-0" data-index="${uploadedImagePaths.length - 1}"></button>
                                </div>`;
                            preview.append(previewHTML);
                        } else {
                            alert('Upload failed: ' + response.error);
                        }
                    },
                    error(xhr) {
                        console.error('Upload error:', xhr.responseText);
                        alert('Upload failed.');
                    }
                });
            });

            this.value = '';
        });

        // 6. Remove Image
        $('#imagePreviewContainer').on('click', '.btn-close', function () {
            const index = $(this).data('index');
            uploadedImagePaths.splice(index, 1);
            $(this).parent().remove();
            $('#uploadedImagePaths').val(uploadedImagePaths.join(','));
        });

        // 7. Form Submit via AJAX
        $('#inhouserepairForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (response) {
                    try {
                        const json = (typeof response === 'string') ? JSON.parse(response) : response;
                        // Handle Print: open PDF and auto-print
                        if (json.redirect) {
                            const printWin = window.open(json.redirect, '_blank', 
                                'width=526,height=600,scrollbars=no,toolbar=no,location=no,status=no,menubar=no');
                            if (printWin) {
                                printWin.onload = function () {
                                    printWin.print();
                                    // Reset form after printing
                                    $('#inhouserepairForm')[0].reset();
                                    $('#uploadedImagePaths').val('');
                                    $('#imagePreviewContainer').empty();
                                };
                            }
                            return; // Exit after handling print
                        }
                        // Handle SMS outcome
                        if (json.sms_success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'SMS Sent',
                                text: json.sms_success
                            }).then(() => {
                                $('#inhouserepairForm')[0].reset();
                                $('#uploadedImagePaths').val('');
                                $('#imagePreviewContainer').empty();
                            });
                        } else if (json.sms_error) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'SMS Error',
                                text: json.sms_error || 'Failed to send SMS.'
                            }).then(() => {
                                // Even if SMS failed, the repair is saved, so we still reset the form
                                $('#inhouserepairForm')[0].reset();
                                $('#uploadedImagePaths').val('');
                                $('#imagePreviewContainer').empty();
                            });
                        }
                        // Handle Email outcome
                        if (json.email_error) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Email Error',
                                text: json.email_error
                            }).then(() => {
                                $('#inhouserepairForm')[0].reset();
                                $('#uploadedImagePaths').val('');
                                $('#imagePreviewContainer').empty();
                            });
                        } else if (json.success) {
                            // json.success is used here for the email success message or generic success
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: json.success
                            }).then(() => {
                                $('#inhouserepairForm')[0].reset();
                                $('#uploadedImagePaths').val('');
                                $('#imagePreviewContainer').empty();
                            });
                        }
                        // Handle DB error (if any)
                        if (json.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Failed',
                                text: json.error || 'Unknown error occurred.'
                            });
                            // (Form not reset here so user can correct and resubmit if needed)
                        }
                    } catch (err) {
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'AJAX request failed', 'error');
                }
            });
        });

    });
</script>



</body>

</html>