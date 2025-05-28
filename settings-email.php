<?php
include 'config/adminacc.php';

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
    <title>Duralux || Email Settings</title>
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
                                <div class="mb-3">
                                    <h4 class="fw-bold">SMTP Settings</h4>
                                    <div class="fs-12 text-muted">SMTP setup main email</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">From Email<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="From Email">
                                    <small class="form-text text-muted">Email [Ex: support@w3inventor.com]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">From Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="From Name">
                                    <small class="form-text text-muted">From Name [Ex: W3Inventor (Pvt.) Ltd.]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Host<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="SMTP Host">
                                    <small class="form-text text-muted">SMTP Host [Ex: smtp.gmail.com]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Port<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="SMTP Port">
                                    <small class="form-text text-muted">SMTP Port [Ex: 587 for TLS or 465 for SSL]</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Encryption</label>
                                    <select class="form-select" data-select2-selector="default">
                                        <option value="ssl" selected>SSL</option>
                                        <option value="ssl">TLS</option>
                                    </select>
                                    <small class="form-text text-muted">Encryption/Authentication Type</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Username</label>
                                    <input type="text" class="form-control" placeholder="Email">
                                    <small class="form-text text-muted">Username or email address to authenticate with the SMTP server</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Password</label>
                                    <input type="password" class="form-control" placeholder="SMTP Password">
                                    <small class="form-text text-muted">SMTP Password</small>
                                </div>
                                <hr class="mb-3">
                                <div class="mb-3">
                                    <h4 class="fw-bold">Send Test Email</h4>
                                    <div class="fs-12 text-muted">Send test email to make sure that your SMTP settings is set correctly.</div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Test Email</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Send Test Email">
                                        <a href="javascript:void(0);" class="input-group-text">Send Test</a>
                                    </div>
                                    <small class="form-text text-muted">Send Test Email [Ex: test_1@email.com, test_2@email.com, test_3@email.com]</small>
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

    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/setting-pw.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/settings-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <!--! END: Theme Customizer !-->

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Load SMTP settings on page load
    fetch('assets/php/helper/setting-helper/fetch_smtp_settings.php')
        .then(response => response.json())
        .then(data => {
            console.log('Fetched SMTP settings:', data); // Debugging line

            // Check and populate each field only if it exists in the HTML
            const fromEmail = document.querySelector('input[placeholder="From Email"]');
            if (fromEmail) fromEmail.value = data.from_email || '';

            const fromName = document.querySelector('input[placeholder="From Name"]');
            if (fromName) fromName.value = data.from_name || '';

            const smtpHost = document.querySelector('input[placeholder="SMTP Host"]');
            if (smtpHost) smtpHost.value = data.smtp_host || '';

            const smtpPort = document.querySelector('input[placeholder="SMTP Port"]');
            if (smtpPort) smtpPort.value = data.smtp_port || '';

            const encryptionSelect = document.querySelector('select[data-select2-selector="default"]');
            if (encryptionSelect) encryptionSelect.value = data.encryption || 'ssl';

            const smtpUsername = document.querySelector('input[placeholder="Email"]');
            if (smtpUsername) smtpUsername.value = data.smtp_username || '';

            const smtpPassword = document.querySelector('input[placeholder="SMTP Password"]');
            if (smtpPassword) smtpPassword.value = data.smtp_password || '';
        })
        .catch(error => console.error('Error fetching SMTP settings:', error));
});

document.querySelector('.successAlertMessage').addEventListener('click', function(e) {
    e.preventDefault();

    const formData = new FormData();

    const requiredFields = [
        { name: 'from_email', element: document.querySelector('input[placeholder="From Email"]') },
        { name: 'from_name', element: document.querySelector('input[placeholder="From Name"]') },
        { name: 'smtp_host', element: document.querySelector('input[placeholder="SMTP Host"]') },
        { name: 'smtp_port', element: document.querySelector('input[placeholder="SMTP Port"]') },
        { name: 'encryption', element: document.querySelector('select[data-select2-selector="default"]') },
        { name: 'smtp_username', element: document.querySelector('input[placeholder="Email"]') },
        { name: 'smtp_password', element: document.querySelector('input[placeholder="SMTP Password"]') }
    ];

    let missingFields = false;
    requiredFields.forEach(field => {
        if (field.element && field.element.value) {
            formData.append(field.name, field.element.value);
        } else {
            console.warn(`Missing required field: ${field.name}`);
            missingFields = true;
        }
    });

    if (missingFields) {
        Swal.fire('Error!', 'Please fill in all required fields.', 'error');
        return; // Stop if any required field is missing
    }

    console.log('Submitting form data:', Object.fromEntries(formData.entries()));

    fetch('assets/php/helper/setting-helper/update_smtp_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data);
        if (data.success) {
            Swal.fire('Saved!', 'Your SMTP settings have been updated.', 'success');
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




</body>

</html>