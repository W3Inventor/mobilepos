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
    <title>Duralux || Gateways Settings</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
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
        <div class="nxl-content without-header nxl-full-content">
            <!-- [ Main Content ] start -->
            <div class="main-content d-flex">
                <!-- [ Content Sidebar ] start -->
                <?php
                include 'templates/setting-sidebar.php'
                ?>
                <!-- [ Content Sidebar  ] end -->
                <!-- [ Main Area  ] start -->
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
                                <div class="mb-5">
                                    <h4 class="fw-bold">TEXTIT.BIZ</h4>
                                    <div class="fs-12 text-muted">SMS gateways</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">User ID</label>
                                    <input type="text" class="form-control" placeholder="TextIt User ID">
                                    <small class="form-text text-muted">TextIt User ID</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">User Password</label>
                                    <input type="password" class="form-control" placeholder="TextIt User Password">
                                    <small class="form-text text-muted">TextIt User Password</small>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- [ Footer ] start -->
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
                    <!-- [ Footer ] end -->
                </div>
                <!-- [ Content Area ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </main>

    <!--! Footer Script !-->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/settings-init.min.js"></script>
    <!--! END: Theme Customizer !-->
</body>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // Load gateway settings on page load
    fetch('assets/php/helper/setting-helper/fetch_gateway_settings.php')
        .then(response => response.json())
        .then(data => {
            console.log('Fetched gateway settings:', data); // Debugging line

            // Populate each field with the fetched data
            const userIdField = document.querySelector('input[placeholder="TextIt User ID"]');
            if (userIdField) userIdField.value = data.user_id || '';

            const userPasswordField = document.querySelector('input[placeholder="TextIt User Password"]');
            if (userPasswordField) userPasswordField.value = data.user_password || '';
        })
        .catch(error => console.error('Error fetching gateway settings:', error));
});



document.querySelector('.successAlertMessage').addEventListener('click', function(e) {
    e.preventDefault();

    const formData = new FormData();
    const userId = document.querySelector('input[placeholder="TextIt User ID"]').value;
    const userPassword = document.querySelector('input[placeholder="TextIt User Password"]').value;

    if (!userId || !userPassword) {
        Swal.fire('Error!', 'Please fill in all required fields.', 'error');
        return;
    }

    formData.append('user_id', userId);
    formData.append('user_password', userPassword);

    console.log('Submitting form data:', Object.fromEntries(formData.entries())); // Debugging line

    fetch('assets/php/helper/setting-helper/update_gateway_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data); // Debugging line
        if (data.success) {
            Swal.fire('Saved!', 'Your gateway settings have been updated.', 'success');
        } else {
            Swal.fire('Error!', 'An error occurred while saving your settings: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        Swal.fire('Error!', 'Could not connect to the server: ' + error, 'error');
    });
});
</script>


</html>