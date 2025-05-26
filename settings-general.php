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
    <title>Duralux || General Settings</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
			<script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
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
                        <div class="page-header-right ms-auto">
                            <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                                <a href="javascript:void(0);" class="text-danger">Cancel</a>
                                <a href="javascript:void(0);" class="btn btn-primary successAlertMessage">
                                    <i class="feather-save me-2"></i>
                                    <span>Save Changes</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="content-area-body">
                        <div class="card mb-0">
                            <div class="card-body"> 
                                <div class="mb-3">
                                    <label class="form-label">Favicon</label>
                                    <div class="position-relative overflow-hidden border border-gray-2 rounded favicon">
                                        <img id="faviconPreview" src="uploads/default_favicon.png" class="upload-pic rounded" alt="Favicon Preview">
                                        <input id="faviconUpload" class="hidden-file-input" type="file" accept=".png, .jpg, .jpeg, .ico">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <div class="position-relative overflow-hidden border border-gray-2 rounded logo">
                                        <img id="logoPreview" src="uploads/default_logo.png" class="upload-pic" alt="Logo Preview">
                                        <input id="logoUpload" class="hidden-file-input" type="file" name="logo" accept="image/*">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="companyName" placeholder="Company Name">
                                    <small class="form-text text-muted">Your company name [Ex: W3Inventor]</small>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" placeholder="Company Address">
                                    <small class="form-text text-muted">Address Line 1 [Ex: 318/A/3, Mahalwarawa]</small>
                                </div>
                                <div class="mb-2">
                                    <input type="text" class="form-control" placeholder="Company City">
                                    <small class="form-text text-muted">Address Line 2 [Ex: ]</small>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Company State">
                                    <small class="form-text text-muted">City [Ex: Pannipitiya]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="tel" class="form-control" placeholder="Phone">
                                    <small class="form-text text-muted">Phone [Ex: +94 (70) 272 0000]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Land Line</label>
                                    <input type="tel" class="form-control" placeholder="Phone">
                                    <small class="form-text text-muted">Phone [Ex: +94 (11) 227 9135]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" class="form-control" placeholder="Company Main Domain">
                                    <small class="form-text text-muted"> Company Website [Ex: w3inventor.com]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Brand Color</label>
                                    <input type="color" class="form-control color-picker">
                                    <small class="form-text text-muted"> Select Your Brand Color</small>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <footer class="footer">
                        <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                            <span>Copyright ©</span>
                            <script>
                                document.write(new Date().getFullYear());
                            </script>
                        </p>
                        <div class="d-flex align-items-center gap-4">
                            <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Help</a>
                            <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Terms</a>
                            <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacy</a>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </main>
   
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/setting-pw.js"></script>

    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/settings-init.min.js"></script>



    <script>
    // Trigger file upload on image click
    document.getElementById('faviconPreview').addEventListener('click', function() {
        document.getElementById('faviconUpload').click();
    });

    document.getElementById('logoPreview').addEventListener('click', function() {
        document.getElementById('logoUpload').click();
    });

    // Preview Favicon
    document.getElementById('faviconUpload').addEventListener('change', function(event) {
        displayImagePreview(event, 'faviconPreview');
    });

    // Preview Logo
    document.getElementById('logoUpload').addEventListener('change', function(event) {
        displayImagePreview(event, 'logoPreview');
    });

    // Function to display image preview
    function displayImagePreview(event, previewId) {
        const input = event.target;
        const previewImage = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Load settings from the database on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetch('assets/php/helper/setting-helper/fetch_settings.php')
            .then(response => {
                console.log("Fetch settings response:", response);
                return response.json();
            })
            .then(data => {
                console.log("Fetched settings data:", data); // Log the data to check contents
                // Populate form fields with fetched data
                document.getElementById('faviconPreview').src = data.favicon_path || 'uploads/default_favicon.png';
                document.getElementById('logoPreview').src = data.logo_path || 'uploads/default_logo.png';
                document.getElementById('companyName').value = data.company_name || '';
                document.querySelector('input[placeholder="Company Address"]').value = data.address_line1 || '';
                document.querySelector('input[placeholder="Company City"]').value = data.address_line2 || '';
                document.querySelector('input[placeholder="Company State"]').value = data.city || '';
                document.querySelector('input[placeholder="Phone"]').value = data.mobile || '';
                document.querySelectorAll('input[placeholder="Phone"]')[1].value = data.landline || '';
                document.querySelector('input[placeholder="Company Main Domain"]').value = data.website || '';
                document.querySelector('.color-picker').value = data.brand_color || '#000000';
            })
            .catch(error => console.error("Error fetching settings:", error)); // Log any fetch error
    });

    // Save settings on "Save Changes" button click
    document.querySelector('.successAlertMessage').addEventListener('click', function(e) {
        e.preventDefault();

        // Collect form data
        const formData = new FormData();
        formData.append('favicon', document.getElementById('faviconUpload').files[0]);
        formData.append('logo', document.getElementById('logoUpload').files[0]);
        formData.append('company_name', document.getElementById('companyName').value);
        formData.append('address_line1', document.querySelector('input[placeholder="Company Address"]').value);
        formData.append('address_line2', document.querySelector('input[placeholder="Company City"]').value);
        formData.append('city', document.querySelector('input[placeholder="Company State"]').value);
        formData.append('mobile', document.querySelector('input[placeholder="Phone"]').value);
        formData.append('landline', document.querySelectorAll('input[placeholder="Phone"]')[1].value);
        formData.append('website', document.querySelector('input[placeholder="Company Main Domain"]').value);
        formData.append('brand_color', document.querySelector('.color-picker').value);

        // Send the form data to update_settings.php
        fetch('assets/php/helper/setting-helper/update_settings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log("Update settings response:", response);
            return response.json();
        })
        .then(data => {
            console.log("Update settings data:", data); // Log the response data for debugging
            if (data.success) {
                Swal.fire('Saved!', 'Your settings have been updated.', 'success');
                // Update previews with the new paths after save
                document.getElementById('faviconPreview').src = data.favicon_path;
                document.getElementById('logoPreview').src = data.logo_path;
            } else {
                Swal.fire('Error!', 'An error occurred while saving your settings: ' + (data.error || 'Unknown error'), 'error');
                console.error("Error details:", data.error); // Log any specific error from the server
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Could not connect to the server.', 'error');
            console.error("Network error or server issue:", error); // Log if the fetch itself fails
        });
    });
</script>

</body>

</html>