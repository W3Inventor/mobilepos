<?php
include 'config/dbconnect.php'; 

// Get the filter and condition parameters from the URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$condition = isset($_GET['condition']) ? $_GET['condition'] : 'all';

// Base SQL query: Count IMEIs per exact combination
$query = "
SELECT 
    v1.brand, 
    v1.model, 
    v1.ram, 
    v1.storage, 
    v1.colour, 
    v1.trcsl, 
    mb.`condition`, 
    mb.status,
    COUNT(mb.imei) AS total_quantity, -- Correct IMEI count per combination
    ROUND(SUM(v2.selling) / NULLIF(COUNT(mb.imei), 0), 2) AS average_selling_price
FROM 
    variation_1 v1
JOIN 
    mobile mb ON v1.vid_1 = mb.vid_1
JOIN 
    variation_2 v2 ON mb.vid_2 = v2.vid_2
";

// Array to store filtering conditions
$conditions = [];

// Apply status filter (soldout or in_stock)
if ($filter == 'soldout') {
    $conditions[] = "mb.status = 'Out of Stock'";
} elseif ($filter == 'in_stock') {
    $conditions[] = "mb.status = 'In Stock'";
}

// Apply condition filter (new or used)
if ($condition == 'new') {
    $conditions[] = "mb.`condition` = 'New'";
} elseif ($condition == 'used') {
    $conditions[] = "mb.`condition` = 'Used'";
}

// Add conditions to the query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Group by all relevant fields to ensure accurate aggregation
$query .= "
GROUP BY 
    v1.brand, v1.model, v1.ram, v1.storage, v1.colour, 
    v1.trcsl, mb.`condition`, mb.status
ORDER BY 
    v1.brand, v1.model;
";

// Execute the query
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>













<!DOCTYPE html>
<html lang="zxx">


<style>

p.fw-bold{
    margin-bottom: 0 !important;
}
.text-l{
    text-align:left !important
}

.text-c{
    text-align:center !important
}

.text-r{
    text-align:right !important
}

.text-end::before, .text-end::after {
    display: none !important;
}

@media (max-width: 768px) {

    .mw-1300{
        min-width: 1300px;
    }



}

  
  .imeitablesec {
    display: none;
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
    <title>Exxplan || Mobile Stock</title>
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
                        <h5 class="m-b-10">Mobile Stock </h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">Mobile Stock</li>
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
                                            <input type="checkbox" class="custom-control-input" id="instock" checked="checked">
                                            <label class="custom-control-label c-pointer" for="instock">In Stock</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="soldout" checked="checked">
                                            <label class="custom-control-label c-pointer" for="soldout">Sold Out</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="newmobile" checked="checked">
                                            <label class="custom-control-label c-pointer" for="newmobile">New Mobile</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="usedmobile" checked="checked">
                                            <label class="custom-control-label c-pointer" for="usedmobile">Used Mobile</label>
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


            <div class="bg-white py-3 border-bottom rounded-0 p-md-0 mb-0">
                <div class="d-md-none d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="page-content-left-open-toggle">
                        <i class="feather-align-left fs-20"></i>
                    </a>
                </div>
                <!-- <div class="d-flex align-items-center justify-content-between">
                    <div class="nav-tabs-wrapper page-content-left-sidebar-wrapper">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-content-left-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                        <ul class="nav nav-tabs nav-tabs-custom-style" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#newitemstab">New</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#useditemstab">Used</button>
                            </li>
                        </ul>
                    </div>
                </div> -->
            </div>

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
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="proposalList">
                                        <thead>
                                            <tr>
                                                
                                                <th scope="row">Brand Name</th>
                                                <th>Model</th>
                                                <th>RAM</th>
                                                <th>Storage</th>
                                                <th>Colour</th>
                                                <th>Quantity</th>
                                                <th>Condition</th>
                                                <th>TRCSL</th>
                                                <th>Avarage Price</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                                            <?php while ($row = $result->fetch_assoc()): ?>

                                            <tr>                                            
                                                
                                            <td class="fs-13"><?php echo htmlspecialchars($row['brand']); ?></td>
                                            <td class="fs-13"><?php echo htmlspecialchars($row['model']); ?></td>
                                            <td class="fs-13"><?php echo htmlspecialchars($row['ram']); ?></td>
                                            <td class="fs-13"><?php echo htmlspecialchars($row['storage']); ?></td>
                                            <td class="fs-13"><?php echo htmlspecialchars($row['colour']); ?></td>
                                            <td class="fs-13"><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                                            
                                            <td class="fs-13"><?php echo htmlspecialchars($row['condition']); ?></td>
                                            <td class="fs-13"><?php echo htmlspecialchars($row['trcsl']); ?></td>
                                            <?php if ($filter == 'soldout'): ?>
                                            <td class="fs-13">-</td>
                                            <?php else: ?>
                                                <td class="fs-13"><?php echo htmlspecialchars($row['average_selling_price']); ?></td>
                                                <?php endif; ?>
                                            <td>
                                                <?php 
                                                    if ($filter == 'soldout') {
                                                        echo 'Out Of Stock ';
                                                    } else {
                                                        echo htmlspecialchars($row['status']);
                                                    }
                                                ?>
                                            </td>
                                                                

                                            
                                            <td class="text-end">
                                                    <div class="hstack gap-2 justify-content-end">
                                                    <a href="javascript:void(0);" class="avatar-text avatar-md">
                                                    <i class="feather-eye" id="bill-details"></i>
                                                        </a>

                                                        <a href="#" class="avatar-text avatar-md">
                                                            <i class="feather-edit"></i>
                                                        </a>
                                                        <a href="#" class="avatar-text avatar-md">
                                                            <i class="feather-trash-2"></i>
                                                        </a>
                                                    </div>
                                                </td>   
                                            </tr>
                                            <?php endwhile; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="8" class="text-center">No data available</td>
                                                            </tr>
                                                        <?php endif; ?>
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="billDetailsModalLabel">Bill Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <div id="billDetailsContent">
        <!-- Content will be loaded here dynamically -->
    </div>
</div>
<div class="modal-footer justify-content-end">
    <nav aria-label="Bill pagination">
        <ul class="pagination justify-content-center" id="pagination">
            <!-- Pagination items will be dynamically inserted here -->
        </ul>
    </nav>    
    </div>
  </div>
</div>



    <!--! [End] Main Content !-->

    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
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



    <script>
$(document).ready(function () {
    let currentBillIndex = 0;
    let billsData = [];

    // Function to display bill details in the modal
    function displayBillDetails(index) {
        const bill = billsData[index];
        let content = `
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label class="form-label">Bill Number</label>
                    <p>${bill.billno}</p>
                </div>
                <div class="col-md-6 mb-1 text-end">
                    <label class="form-label">Company Name</label>
                    <p>${bill.company_name}</p>
                </div>
                <div class="col-md-6 mb-1">
                    <label class="form-label">Date</label>
                    <p>${bill.date}</p>
                </div>
                <div class="col-md-6 mb-1 text-end">
                    <label class="form-label">Supplier Name</label>
                    <p>${bill.supplier_name}</p>
                </div>
                <div class="col-md-6 mb-1">
                    <div class="hstack gap-2">
                        <p class="form-label mb-0">Selling Price:</p>
                        <span id="sellingPriceText" class="fw-normal mb-0">${bill.amount}</span>
                        <input type="text" class="form-control mb-0 fw-normal w-50" id="sellingPriceInput" value="${bill.amount}" style="display: none;">
                        <a href="javascript:void(0);" class="avatar-text avatar-md mb-0" id="sellingPriceEdit">
                            <i class="feather-edit"></i>
                        </a>
                        <a href="javascript:void(0);" class="avatar-text avatar-md mb-0" id="sellingPriceSave" style="display: none;">
                            <i class="feather-save"></i>
                        </a>
                        <a href="javascript:void(0);" class="avatar-text avatar-md mb-0" id="sellingPriceCancel" style="display: none;">
                            <i class="feather-x"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 mb-1 hstack gap-2" style="justify-content: end;">
                    <p class="form-label mb-0">Available Quantity:</p>
                    <p class="mb-0">${bill.quantity2}</p>
                </div>
            </div>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th class="text-start">IMEI Number</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>`;

        bill.imeis.forEach((imei, i) => {
            content += `
                <tr data-index="${i}">
                    <td class="text-start">
                        <span class="imei-text">${imei.imei_number}</span>
                        <input type="text" class="form-control imei-input d-none" value="${imei.imei_number}" />
                    </td>
                    <td class="text-center">${imei.status}</td>
                    <td class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <a href="javascript:void(0);" class="avatar-text avatar-md edit-imei">
                                <i class="feather-edit"></i>
                            </a>
                            <a href="javascript:void(0);" class="avatar-text avatar-md save-imei d-none">
                                <i class="feather-save"></i>
                            </a>
                            <a href="javascript:void(0);" class="avatar-text avatar-md cancel-edit d-none">
                                <i class="feather-x"></i>
                            </a>
                            <a href="javascript:void(0);" class="avatar-text avatar-md delete-imei">
                                <i class="feather-trash-2"></i>
                            </a>
                        </div>
                    </td>
                </tr>`;
        });

        content += `</tbody></table>`;
        $('#billDetailsContent').html(content);

        bindIMEIActionEvents(); // Bind action events for IMEIs
        bindEditEvents(); // Bind edit/save/cancel events for price
    }

    // Handle IMEI row actions (edit, save, cancel, delete)
    function bindIMEIActionEvents() {
        $('.edit-imei').on('click', function () {
            const row = $(this).closest('tr');
            row.find('.imei-text').hide();
            row.find('.imei-input').removeClass('d-none').focus();
            $(this).hide();
            row.find('.save-imei, .cancel-edit').removeClass('d-none');
        });

        $('.save-imei').on('click', function () {
            const row = $(this).closest('tr');
            const newIMEI = row.find('.imei-input').val();
            const imeiIndex = row.data('index');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to update this IMEI.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'assets/php/helper/mobile-helper/update_imei.php',
                        type: 'POST',
                        data: { 
                            oldIMEI: billsData[currentBillIndex].imeis[imeiIndex].imei_number, 
                            newIMEI 
                        },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.success) {
                                showNotification('success', 'IMEI updated successfully.');
                                row.find('.imei-text').text(newIMEI).show();
                                row.find('.imei-input').addClass('d-none');
                                billsData[currentBillIndex].imeis[imeiIndex].imei_number = newIMEI;
                            } else {
                                showNotification('error', 'Failed to update IMEI.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            showNotification('error', 'Error occurred while updating IMEI.');
                        }
                    });

                    row.find('.save-imei, .cancel-edit').addClass('d-none');
                    row.find('.edit-imei').show();
                }
            });
        });

        $('.cancel-edit').on('click', function () {
            const row = $(this).closest('tr');
            row.find('.imei-input').val(row.find('.imei-text').text()).addClass('d-none');
            row.find('.imei-text').show();
            $(this).addClass('d-none');
            row.find('.save-imei').addClass('d-none');
            row.find('.edit-imei').show();
        });

        $('.delete-imei').on('click', function () {
            const row = $(this).closest('tr');
            const imeiIndex = row.data('index');
            const imei = billsData[currentBillIndex].imeis[imeiIndex].imei_number;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'assets/php/helper/mobile-helper/delete_imei.php',
                        type: 'POST',
                        data: { imei },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.success) {
                                showNotification('success', 'IMEI deleted successfully.');
                                row.remove();
                                billsData[currentBillIndex].imeis.splice(imeiIndex, 1);
                            } else {
                                showNotification('error', 'Failed to delete IMEI.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            showNotification('error', 'Error occurred while deleting IMEI.');
                        }
                    });
                }
            });
        });
    }


    // Handle price edit/save/cancel
    function bindEditEvents() {
        $('#sellingPriceEdit').on('click', function () {
            $('#sellingPriceText').hide();
            $('#sellingPriceInput').show().focus();
            $('#sellingPriceEdit').hide();
            $('#sellingPriceSave, #sellingPriceCancel').show();
        });

        $('#sellingPriceSave').on('click', function () {
            let newPrice = $('#sellingPriceInput').val();
            let imeis = billsData[currentBillIndex].imeis.map(imei => imei.imei_number);

            $.ajax({
                url: 'assets/php/helper/mobile-helper/update_selling_price.php',
                type: 'POST',
                data: { imeis, newSellingPrice: newPrice },
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        showNotification('success', 'Price updated successfully.');
                        $('#sellingPriceText').text(newPrice).show();
                        $('#sellingPriceInput').hide();
                        $('#sellingPriceEdit').show();
                        $('#sellingPriceSave, #sellingPriceCancel').hide();
                        billsData[currentBillIndex].amount = newPrice;
                    } else {
                        showNotification('error', 'Failed to update price.');
                    }
                },
                error: function () {
                    showNotification('error', 'Error occurred while updating price.');
                }
            });
        });

        $('#sellingPriceCancel').on('click', function () {
            $('#sellingPriceInput').val(billsData[currentBillIndex].amount).hide();
            $('#sellingPriceText').show();
            $('#sellingPriceEdit').show();
            $('#sellingPriceSave, #sellingPriceCancel').hide();
        });
    }
        // Pagination logic
        function updatePagination() {
            $('#pagination').empty();
            billsData.forEach((_, i) => {
                const activeClass = i === currentBillIndex ? 'active' : '';
                $('#pagination').append(`
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#" data-index="${i}">${i + 1}</a>
                    </li>
                `);
            });

            $('.page-link').on('click', function (e) {
                e.preventDefault();
                currentBillIndex = $(this).data('index');
                displayBillDetails(currentBillIndex);
                updatePagination(); // Refresh pagination to reflect active page
            });
        }

        // Filtering logic
        function updateFilters() {
            let filterParams = [];
            let conditionParams = [];

            if ($('#instock').is(':checked')) filterParams.push('in_stock');
            if ($('#soldout').is(':checked')) filterParams.push('soldout');
            if ($('#newmobile').is(':checked')) conditionParams.push('new');
            if ($('#usedmobile').is(':checked')) conditionParams.push('used');

            const filter = filterParams.length === 1 ? filterParams[0] : 'all';
            const condition = conditionParams.length === 1 ? conditionParams[0] : 'all';
            const newUrl = `${window.location.pathname}?filter=${filter}&condition=${condition}`;

            window.history.pushState(null, '', newUrl);
            loadFilteredData(filter, condition);
        }

        // Load filtered data via AJAX
        function loadFilteredData(filter, condition) {
            $.ajax({
                url: 'mobile.php',
                type: 'GET',
                data: { filter, condition },
                success: function (response) {
                    const newTableBody = $(response).find('#proposalList tbody').html();
                    $('#proposalList tbody').html(newTableBody);
                    bindModalEvents(); // Rebind modal events after data reload
                },
                error: function () {
                    alert('Failed to fetch data.');
                }
            });
        }

        // Fetch data on click of the eye icon
        function bindModalEvents() {
            $('.feather-eye').click(function () {
                const row = $(this).closest('tr');
                const brand = row.find('td:eq(0)').text();
                const model = row.find('td:eq(1)').text();
                const ram = row.find('td:eq(2)').text();
                const storage = row.find('td:eq(3)').text();
                const colour = row.find('td:eq(4)').text();
                const condition = row.find('td:eq(6)').text();
                const trcsl = row.find('td:eq(7)').text();
                const status = row.find('td:eq(9)').text();

                $.ajax({
                    url: 'assets/php/helper/mobile-helper/fetch_bill_details.php',
                    type: 'POST',
                    data: { 
                        brand, model, ram, storage, colour, condition, trcsl, status 
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.error) {
                            alert(response.error);
                        } else {
                            billsData = response;
                            currentBillIndex = 0;
                            displayBillDetails(currentBillIndex);
                            updatePagination(); // Generate pagination
                            $('#billDetailsModal').modal('show'); // Open the modal
                        }
                    },
                    error: function () {
                        showNotification('error', 'Failed to fetch bill details.');
                    }
                });
            });
        }

        // Initialize all event listeners
        function initializeEvents() {
            $('.filter-dropdown input[type="checkbox"]').on('change', updateFilters);
            bindModalEvents(); // Bind modal-related events

            // Load the initial filters from the URL
            const urlParams = new URLSearchParams(window.location.search);
            const filter = urlParams.get('filter') || 'all';
            const condition = urlParams.get('condition') || 'all';
            loadFilteredData(filter, condition); // Load data based on filters
        }

        // Call initialize function on document ready
        initializeEvents();
    });



</script>




</body>

</html>

