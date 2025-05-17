<?php
include 'config/dbconnect.php'; 
?>

<!DOCTYPE html>
<html lang="zxx">


<style>
    .select2-container--bootstrap-5 .select2-selection{
        min-height: 35px !important;
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
                                            <input type="checkbox" class="custom-control-input" id="instock" checked="checked">
                                            <label class="custom-control-label c-pointer" for="submitted">Submitted</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="soldout" checked="checked">
                                            <label class="custom-control-label c-pointer" for="processing">Processing</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="newmobile" checked="checked">
                                            <label class="custom-control-label c-pointer" for="fixed">Fixed</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="usedmobile" checked="checked">
                                            <label class="custom-control-label c-pointer" for="redytopickup">Ready to Pickup</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="usedmobile" checked="checked">
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
                                    <table class="table table-hover mb-0">
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
                                            <tr>
                                                <td>
                                                    <a href="javascript:void(0);">#896574</a>
                                                </td>
                                                <td>Alexandra Della </td>
                                                <td>123456789 </td>
                                                <td>APPLE</td>
                                                <td>15 Pro Max</td>
                                                <td>2 Green Lines in Display</td>
                                                <td>LKR 25000.00</td>
                                                <td>
                                                    <div class="hstack gap-2">
                                                        <a href="javascript:void(0);" class="avatar-text avatar-md">
                                                            <i class="feather-eye" id="bill-details"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control" data-select2-selector="status">
                                                        <option value="secondary" data-bg="bg-secondary">Submitted</option>
                                                        <option value="primary" data-bg="bg-primary">Processing</option>
                                                        <option value="success" data-bg="bg-success" selected>Fixed</option>
                                                        <option value="danger" data-bg="bg-danger">Ready to Pickup</option>
                                                        <option value="warning" data-bg="bg-warning">Paid & Pickup</option>
                                                    </select>
                                                </td>
                                                <td class="text-end">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <a href="#" class="avatar-text avatar-md save-status d-none">
                                                            <i class="feather-save"></i>
                                                        </a>
                                                        <a href="#" class="avatar-text avatar-md cancel-edit d-none">
                                                            <i class="feather-x"></i>
                                                        </a>
                                                        <a href="#" class="avatar-text avatar-md delete-status">
                                                            <i class="feather-trash-2"></i>
                                                        </a>
                                                        <a href="#" class="avatar-text avatar-md payment d-none">
                                                            <i class="feather-credit-card"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
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




</body>

</html>


