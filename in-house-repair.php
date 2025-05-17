<?php
include 'config/dbconnect.php'; 
?>

<!DOCTYPE html>

<html lang="zxx">

```
<style>
   .form-control{
            padding: .375rem .75rem !important;
    }
</style>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>Exxplan || In-House Repairing</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css" />
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css" />
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/tagify.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/tagify-data.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/quill.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/notification.css">
    <!-- Lightbox2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />



    
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
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
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <main class="nxl-container">
        <div class="nxl-content">
                <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">In-House Repairing </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">In-House Repairing</li>
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
                        <div class="dropdown filter-dropdown">
                            <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                <i class="feather-filter me-2"></i>
                                <span>Filter</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="submitted" checked="checked">
                                        <label class="custom-control-label c-pointer" for="submitted">Submitted</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="processing" checked="checked">
                                        <label class="custom-control-label c-pointer" for="processing">Processing</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="fixed" checked="checked">
                                        <label class="custom-control-label c-pointer" for="fixed">Fixed</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="redytopickup" checked="checked">
                                        <label class="custom-control-label c-pointer" for="redytopickup">Ready to Pickup</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="pickup" checked="checked">
                                        <label class="custom-control-label c-pointer" for="pickup">Paid & Pickup</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="add-mobile.php" class="btn btn-md btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Add Mobile</span>
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
        <div class="main-content">
            <div class="col-lg-12">
                <?php
                        // Display success or error messages
                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                            unset($_SESSION['message']);
                        }
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                            unset($_SESSION['error']);
                        }
                ?>
                <div class="tab-pane fade active show" id="newitemstab">        
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card stretch stretch-full">
                                <div class="card-body custom-card-action p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="proposalList">
                                            <thead>
                                                <tr>
                                                    <th>Repiair ID</th>
                                                    <th>Customer</th>
                                                    <th>IMEI</th>
                                                    <th>Brand</th>
                                                    <th>Model</th>
                                                    <th>Reason</th>
                                                    <th>Estimate Price</th>
                                                    <th>Images</th>
                                                    <th class="wd-250">Status</th>
                                                    <th class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = "SELECT r.*, c.full_name FROM in_house_repair r
                                                        LEFT JOIN customers c ON r.customer_id = c.customer_id
                                                        ORDER BY r.ir_id DESC";
                                                $result = $conn->query($query);
                                                while ($row = $result->fetch_assoc()):
                                                    $data_images = !empty($row['images']) ? explode(',', $row['images']) : [];
                                                    $status = $row['status'] ?? 'Submitted';
                                                ?>
                                                <tr data-repair-id="<?= $row['ir_id'] ?>">
                                                    <td>#<?= htmlspecialchars($row['ir_id']) ?></td>
                                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['imei']) ?></td>
                                                    <td><?= htmlspecialchars($row['brand']) ?></td>
                                                    <td><?= htmlspecialchars($row['model']) ?></td>
                                                    <td><?= htmlspecialchars($row['reason']) ?></td>
                                                    <td>LKR <?= number_format($row['estimate_price'], 2) ?></td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <a href="javascript:void(0);" 
                                                            class="avatar-text avatar-md view-images"
                                                            data-images="<?= htmlspecialchars(json_encode($data_images), ENT_QUOTES, 'UTF-8') ?>">
                                                                <i class="feather-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select class="form-control repair-status" data-original="<?= $status ?>">
                                                            <?php
                                                            $statuses = ['Submitted', 'Processing', 'Fixed', 'Ready to Pickup', 'Paid & Pickup'];
                                                            foreach ($statuses as $s):
                                                                $selected = ($s === $status) ? 'selected' : '';
                                                                echo "<option value='$s' $selected>$s</option>";
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="hstack gap-2 justify-content-end">
                                                            <a href="#" class="avatar-text avatar-md save-status d-none" title="Save">
                                                                <i class="feather-save"></i>
                                                            </a>
                                                            <a href="#" class="avatar-text avatar-md cancel-edit d-none" title="Cancel">
                                                                <i class="feather-x"></i>
                                                            </a>
                                                            <a href="#" class="avatar-text avatar-md delete-status" title="Delete">
                                                                <i class="feather-trash-2"></i>
                                                            </a>
                                                            <a href="#" class="avatar-text avatar-md payment <?= $status === 'Ready to Pickup' ? '' : 'd-none' ?>" title="Payment">
                                                                <i class="feather-credit-card"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>      
            </div>               
        </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="billDetailsModal" tabindex="-1" aria-labelledby="billDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-light">
        <div class="modal-header">
            <h5 class="modal-title">Repair Images</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="billDetailsContent" class="d-flex flex-wrap gap-2 justify-content-center">
            <!-- Lightbox image links go here -->
            </div>
        </div>
        </div>
    </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script>
        $(document).ready(function () {
            const statusOrder = ['Submitted', 'Processing', 'Fixed', 'Ready to Pickup', 'Paid & Pickup'];

            // Handle status change with step validation
            $('.repair-status').on('change', function () {
                const select = $(this);
                const row = select.closest('tr');
                const original = select.data('original');
                const selected = select.val();

                const currentIndex = statusOrder.indexOf(original);
                const selectedIndex = statusOrder.indexOf(selected);

                const saveBtn = row.find('.save-status');
                const cancelBtn = row.find('.cancel-edit');
                const deleteBtn = row.find('.delete-status');
                const paymentBtn = row.find('.payment');

                // 🔒 If current is "Ready to Pickup", only allow "Paid & Pickup"
                if (original === 'Ready to Pickup' && selected !== 'Paid & Pickup') {
                    select.val(original); // revert
                    Swal.fire('Not Allowed', 'You can only move forward to "Paid & Pickup" from "Ready to Pickup".', 'error');
                    return;
                }

                // 🔒 Prevent jumping more than one step forward
                if (selectedIndex === currentIndex + 1 || (original === 'Ready to Pickup' && selected === 'Paid & Pickup')) {
                    saveBtn.removeClass('d-none');
                    cancelBtn.removeClass('d-none');
                    deleteBtn.addClass('d-none');
                    paymentBtn.toggleClass('d-none', selected !== 'Ready to Pickup');
                } else if (selectedIndex < currentIndex) {
                    select.val(original); // revert
                    Swal.fire('Not Allowed', 'You cannot go back to a previous step once progressed.', 'error');
                } else {
                    select.val(original); // revert
                    Swal.fire('Invalid Change', 'Please follow the correct order of status updates.', 'error');
                }
            });


            // Cancel edit
            $('.cancel-edit').on('click', function () {
                const row = $(this).closest('tr');
                const select = row.find('.repair-status');
                select.val(select.data('original')).trigger('change');
                row.find('.save-status, .cancel-edit').addClass('d-none');
                row.find('.delete-status').removeClass('d-none');
            });

            // Save status
            $('.save-status').on('click', function () {
                const row = $(this).closest('tr');
                const repairId = row.data('repair-id');
                const status = row.find('.repair-status').val();

                if (status === 'Ready to Pickup') {
                    // First update status only, then redirect to repair-pos.php
                    $.post('assets/php/helper/repair-helper/update_repair_status.php', {
                        repair_id: repairId,
                        status: status
                    }, function (res) {
                        if (res.success) {
                            Swal.fire({
                                title: 'Redirecting...',
                                text: 'Please wait while we prepare the invoice page.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Redirect to repair-pos.php with the repair ID
                                window.location.href = 'repair-pos.php?repair_id=' + repairId;
                            });
                        } else {
                            Swal.fire('Error', res.error || 'Update failed', 'error');
                        }
                    }, 'json');
                } else {
                    // Normal status update flow
                    $.post('assets/php/helper/repair-helper/update_repair_status.php', {
                        repair_id: repairId,
                        status: status
                    }, function (res) {
                        if (res.success) {
                            Swal.fire('Success', res.success, 'success');
                            row.find('.repair-status').data('original', status);
                            row.find('.save-status, .cancel-edit').addClass('d-none');
                            row.find('.delete-status').removeClass('d-none');
                            row.find('.payment').toggleClass('d-none', status !== 'Ready to Pickup');
                        } else {
                            Swal.fire('Error', res.error || 'Update failed', 'error');
                        }
                    }, 'json');
                }
            });


            // Image preview (lightbox-style)
            $(document).on('click', '.view-images', function () {
                try {
                    const raw = $(this).attr('data-images');
                    const images = JSON.parse(raw);
                    let html = '';

                    if (!Array.isArray(images) || images.length === 0) {
                        html = "<p class='text-muted'>No images uploaded.</p>";
                    } else {
                        images.forEach((path, index) => {
                            html += `<a href="${path}" data-lightbox="repair-images" data-title="Image ${index + 1}">
                                        <img src="${path}" class="img-thumbnail m-2" style="max-width:150px;">
                                    </a>`;
                        });
                    }

                    $('#billDetailsContent').html(html);
                    $('#billDetailsModal').modal('show');
                } catch (e) {
                    console.error("Invalid JSON in data-images:", e);
                    $('#billDetailsContent').html("<p class='text-danger'>Unable to load images.</p>");
                    $('#billDetailsModal').modal('show');
                }
            });
        });

        // Delete button
        $(document).on('click', '.delete-status', function () {
            const row = $(this).closest('tr');
            const repairId = row.data('repair-id');

            Swal.fire({
                title: 'Enter Admin Password',
                input: 'password',
                inputLabel: 'Password',
                inputPlaceholder: 'Enter admin password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Password is required');
                        return false;
                    }

                    return $.post('assets/php/helper/repair-helper/delete_repair.php', {
                        repair_id: repairId,
                        admin_password: password
                    }, null, 'json')
                    .then(response => {
                        if (!response.success) {
                            throw new Error(response.error || 'Unauthorized or failed');
                        }
                        return response;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error.message);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Deleted!', result.value.success, 'success');
                    row.remove();
                }
            });
        });

        //filters
        $(document).ready(function () {
            function applyFilters() {
                let activeStatuses = [];

                // Collect selected filters
                $('.filter-dropdown input[type="checkbox"]').each(function () {
                    if ($(this).is(':checked')) {
                        const label = $(this).next('label').attr('for');
                        activeStatuses.push(label.replace('submitted', 'Submitted')
                                                .replace('processing', 'Processing')
                                                .replace('fixed', 'Fixed')
                                                .replace('redytopickup', 'Ready to Pickup')
                                                .replace('pickup', 'Paid & Pickup'));
                    }
                });

                // Loop through rows and show/hide based on status
                $('#proposalList tbody tr').each(function () {
                    const row = $(this);
                    const status = row.find('.repair-status').val();
                    if (activeStatuses.includes(status)) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }

            // Attach filter logic
            $('.filter-dropdown input[type="checkbox"]').on('change', applyFilters);

            // Run once on load
            applyFilters();
        });
    </script>

</body>
```

</html>

