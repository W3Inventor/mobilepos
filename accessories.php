<?php
include 'config/dbconnect.php';

// Fetch accessories along with their price and serial numbers
$query = "SELECT a.accessory_id, a.brand, a.accessory_name, p.buying AS buying_price, 
                 p.selling AS selling_price, a.quantity,
                 GROUP_CONCAT(sn.serial_number) AS serial_numbers
          FROM accessories a
          JOIN accessories_price p ON a.accessory_id = p.accessory_id
          LEFT JOIN serial_numbers sn ON a.accessory_id = sn.accessory_id
          GROUP BY a.accessory_id
          ORDER BY a.accessory_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <title>Exxplan || POS System</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/notification.css">

    <style>
        /* .dataTables_wrapper .row:first-child{
            border-bottom: 0 !important;
        } */

        .dataTables_wrapper .row:last-child{
            border-top: 0 !important;
        }

        div#proposalList2_length{
            display: none !important;
        }

        input.form-control.form-control-sm{
            padding: 0 10px !important;
        }

        .modal-body .col-sm-12.col-md-5 {
            visibility: hidden !important;
        }

        .modal-body a.page-link {
            font-size: 12px !important;
        }

    </style>

</head>


<body>
    <?php include 'templates/navigation.php'; ?>
    <?php include 'templates/header.php'; ?>

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Accessory Stock</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">Accessories</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <div class="dropdown filter-dropdown">
                                <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                    <i class="feather-filter me-2"></i>
                                    <span>Filter</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="instock">
                                            <label class="custom-control-label c-pointer" for="instock">In Stock</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="soldout">
                                            <label class="custom-control-label c-pointer" for="soldout">Sold Out</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="newmobile">
                                            <label class="custom-control-label c-pointer" for="newmobile">New Mobile</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="usedmobile">
                                            <label class="custom-control-label c-pointer" for="usedmobile">Used Mobile</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="add-accessories.php" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Add new Accessories</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch m-5">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                            <table class="table table-hover" id="accessoriesTable">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Brand</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated here by AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Updated Bill Details Modal HTML with Edit/Delete Options Matching mobile.php -->
    <div class="modal fade" id="billDetailsModal" tabindex="-1" aria-labelledby="billDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="billDetailsModalLabel">Bill Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="billDetailsContent">
                <!-- Dynamic content goes here -->
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <nav aria-label="Bill pagination">
                <ul class="pagination justify-content-center" id="billPagination">
                    <!-- Pagination items dynamically inserted -->
                </ul>
                </nav>
            </div>
            </div>
        </div>
    </div>





    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/setting-pw.js"></script>
    <script src="assets/js/common-init.min.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function () {
            const table = $('#accessoriesTable').DataTable({
                ajax: {
                    url: 'assets/php/table-helper/get_accessories.php',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: 'accessory_id' },
                    { data: 'brand' },
                    { data: 'accessory_name' },
                    {
                        data: 'selling_price',
                        render: function (data) {
                            return typeof data === 'string' && data.includes('-')
                                ? `LKR ${data}`
                                : `LKR ${parseFloat(data).toFixed(2)}`;
                        }
                    },
                    { data: 'quantity' },
                    {
                        data: null,
                        className: 'text-end',
                        render: function (data, type, row) {
                            const hasSerials = row.serial_numbers && row.serial_numbers.length > 0;
                            const eyeButton = `
                                <a href="#" class="avatar-text avatar-md view-serials" 
                                data-serials='${JSON.stringify(row.serial_numbers).replace(/"/g, '&quot;')}'
                                data-accessory-id="${row.accessory_id}"
                                data-price-id="${row.price_id}">
                                    <i class='feather-eye'></i>
                                </a>`;
                            return `
                                <div class='hstack gap-2 justify-content-end'>
                                    ${eyeButton}
                                </div>`;
                        }
                    }
                ],
                pageLength: 10,
                responsive: true,
                drawCallback: function (settings) {
                    const api = this.api();
                    const pagination = $(this)
                        .closest('.dataTables_wrapper')
                        .find('.dataTables_paginate');
                    const info = $(this)
                        .closest('.dataTables_wrapper')
                        .find('#accessoriesTable_info');

                    if (api.page.info().pages <= 1) {
                        pagination.hide();
                        info.hide();
                    } else {
                        pagination.show();
                        info.show();
                    }
                }

            });


            let serialTable;

            // Handle view serial numbers button click
            $('#accessoriesTable tbody').on('click', '.view-serials', function () {
                let serials = $(this).data('serials');
                serials = serials ? (typeof serials === 'string' ? serials.split(',') : serials) : [];

                const accessoryId = $(this).data('accessory-id');
                const priceId = $(this).data('price-id');
                populateSerialNumbersTable(serials, accessoryId, priceId);
                $('#serialNumberModal').modal('show');
            });

            // Populate serial numbers in the modal's table
            function populateSerialNumbersTable(serials, accessoryId, priceId) {

                serialTable = $('#serialNumbersTable').DataTable({
                    destroy: true,
                    data: serials.map((serial, index) => ({
                        serial_number: serial,
                        serial_id: index + 1,
                        accessory_id: accessoryId,
                        price_id: priceId
                    })),
                    columns: [
                        {
                            title: 'Serial Number',
                            data: 'serial_number',
                            className: 'text-start',
                            render: function (data) {
                                return `<span class="serial-number">${data}</span>`;
                            }
                        },
                        {
                            title: 'Actions',
                            data: null,
                            className: 'text-end',
                            render: function (data, type, row) {
                                const hasSerials = row.serial_numbers && row.serial_numbers.length > 0;
                                const eyeButton = `
                                <a href="#" class="avatar-text avatar-md view-serials" 
                                    data-serials='${JSON.stringify(row.serial_numbers).replace(/"/g, '&quot;')}' 
                                    data-accessory-id='${row.accessory_id}' 
                                    data-price-id='${row.price_id}'>
                                    <i class='feather-eye'></i>
                                </a>`;
                                const editButton = `
                                <a href="#" class="edit-row avatar-text avatar-md" 
                                    data-accessory-id="${row.accessory_id}" 
                                    data-price-id="${row.price_id}">
                                    <i class='feather-edit'></i>
                                </a>`;
                                const deleteButton = `
                                <a href="#" class="delete-row avatar-text avatar-md" 
                                    data-accessory-id="${row.accessory_id}" 
                                    data-price-id="${row.price_id}">
                                    <i class='feather-trash-2'></i>
                                </a>`;
                                return `
                                <div class='hstack gap-2 justify-content-end'>
                                    ${eyeButton}
                                    ${!hasSerials ? editButton + deleteButton : ''}
                                </div>`;
                            }
                            }

                    ],
                    dom: '<"top"lf>rt<"bottom"ip><"clear">',
                    paging: true,
                    searching: true,
                    autoWidth: false,
                    responsive: true,
                    language: {
                        emptyTable: 'No serial numbers available',
                        paginate: {
                            previous: "&laquo;",
                            next: "&raquo;"
                        }
                    }
                });
            }

            // Handle edit row button click
            $('#accessoriesTable tbody').on('click', '.edit-row', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const accessory_id = $(this).data('accessory-id');
                const price_id = $(this).data('price-id');

                if (!accessory_id || !price_id) {
                    alert('Error: Accessory ID or Price ID is missing. Unable to update the record.');
                    console.log('Missing accessory_id or price_id:', { accessory_id, price_id });
                    return;
                }

                console.log('Captured for edit:', { accessory_id, price_id });

                // Only make Quantity field editable
                const quantityCell = row.find('td:eq(4)');
                const quantityValue = quantityCell.text();
                quantityCell.html(`<input type="number" class="form-control" value="${quantityValue}">`);

                $(this).replaceWith(`
                    <a href="#" class="save-row avatar-text avatar-md" data-accessory-id="${accessory_id}" data-price-id="${price_id}">
                    <i class='feather-save'></i>
                    </a>
                    <a href="#" class="close-row avatar-text avatar-md">
                    <i class='feather-x'></i>
                    </a>
                `);
            });


            // Handle Edit Serial Number
            $('#serialNumbersTable').on('click', '.edit-serial', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const serialNumberCell = row.find('.serial-number');
                const currentSerial = serialNumberCell.text();

                row.data('original', currentSerial);

                serialNumberCell.html(`<input type="text" class="form-control" value="${currentSerial}">`);

                $(this).replaceWith(`
                    <a href="#" class=" avatar-text avatar-md save-serial" title="Save">
                        <i class='feather-save'></i>
                    </a>
                    <a href="#" class="avatar-text avatar-md cancel-serial" title="Cancel">
                        <i class='feather-x'></i>
                    </a>
                `);
            });

            // Handle Save Edited Serial Number
            $('#serialNumbersTable').on('click', '.save-serial', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const newSerial = row.find('input').val();
                const originalSerial = row.data('original');


                $.ajax({
                    url: 'assets/php/table-helper/update_serial_number.php',
                    type: 'POST',
                    data: {
                        new_serial: newSerial,
                        original_serial: originalSerial
                    },
                    dataType: 'json',
                    success: function (response) {
                        showNotification('success', response.message);
                        if (response.status === 'success') {
                            row.find('.serial-number').text(newSerial);
                            row.find('.save-serial').replaceWith('<a href="#" class="avatar-text avatar-md edit-serial" title="Edit"><i class="feather-edit"></i></a>');
                            row.find('.cancel-serial').remove();
                            row.removeData('original'); // clear stored value

                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error - Status:', status, error, 'Response:', xhr.responseText);
                        alert('Failed to update the serial number.');
                    }
                });
            });

            // Handle Cancel Edit
            $('#serialNumbersTable').on('click', '.cancel-serial', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const originalSerial = row.data('original');

                row.find('.serial-number').text(originalSerial);
                row.find('.save-serial').replaceWith('<a href="#" class="avatar-text avatar-md edit-serial" title="Edit"><i class="feather-edit"></i></a>');
                $(this).remove();
            });

            // Handle view serial numbers button click
            $('#accessoriesTable tbody').on('click', '.view-serials', function () {
            const serials = $(this).data('serials');
            const accessoryId = $(this).data('accessory-id');
            const priceId = $(this).data('price-id');

            if (serials && serials.length > 0) {
                fetchBillDetails(accessoryId).then(response => {
                    if (response.status === 'success') {
                        const bills = Array.isArray(response.data) ? response.data : [response.data];
                        initializeBillPagination(bills);
                        displayBillDetails(bills[0]);
                        $('#billDetailsModal').modal('show');
                    } else {
                        $('#billDetailsContent').html('<p class="text-danger">' + response.message + '</p>');
                        $('#billPagination').empty();
                        $('#billDetailsModal').modal('show');
                    }
                });
            }
            else {
                            // No serial numbers; fetch and display bill details only
                            fetchBillDetails(accessoryId).then(response => {
            if (response.status === 'success') {
                const bills = Array.isArray(response.data) ? response.data : [response.data];
                initializeBillPagination(bills); // show pagination
                displayBillDetails(bills[0]); // show first bill
                $('#billDetailsModal').modal('show');
            } else {
                $('#billDetailsContent').html('<p class="text-danger">' + response.message + '</p>');
                $('#billPagination').empty();
                $('#billDetailsModal').modal('show');
            }
            });

            }
            });

            // Function to group serials by bill ID
            function groupSerialsByBill(serials) {
            const billsMap = {};
            serials.forEach(serial => {
                const billId = serial.bill_id;
                if (!billsMap[billId]) {
                billsMap[billId] = [];
                }
                billsMap[billId].push(serial);
            });
            return Object.entries(billsMap).map(([billId, serials]) => ({ billId, serials }));
            }

            // Function to initialize pagination
            function initializeBillPagination(bills) {
            const pagination = $('#billPagination');
            pagination.empty();
            bills.forEach((bill, index) => {
                const pageItem = $(`
                <li class="page-item ${index === 0 ? 'active' : ''}">
                    <a class="page-link" href="#">${index + 1}</a>
                </li>
                `);
                pageItem.on('click', function (e) {
                e.preventDefault();
                pagination.find('.page-item').removeClass('active');
                $(this).addClass('active');
                displayBillDetails(bill);
                });
                pagination.append(pageItem);
            });
            }

            // Function to display bill details
            function displayBillDetails(bill) {
            const content = renderBillDetails(bill);
            $('#billDetailsContent').html(content);
            }

            // Function to render bill details
            function renderBillDetails(bill) {
            if (!bill) {
                return '<p class="text-danger">Failed to load bill details.</p>';
            }

            let html = '';

            html += `
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Bill Number</label>
                        <div>${bill.billno || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 text-end">
                        <label class="form-label">Date</label>
                        <div>${bill.date || 'N/A'}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <div>LKR ${bill.bill_amount || '0.00'}</div>
                    </div>
                    <div class="col-md-6 text-end">
                        <label class="form-label">Supplier</label>
                        <div>${bill.supplier_name || 'Unknown'}</div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Quantity:</label>
                        <span id="quantityText">${bill.quantity}</span>
                        <input type="number" class="form-control w-50 d-none" id="quantityInput" value="${bill.quantity}" data-price-id="${bill.price_id}">
                        ${(Array.isArray(bill.serials) && bill.serials.length > 0) 
                            ? '' 
                            : `<a href="#" class="avatar-text avatar-md" id="editQuantity"><i class="feather-edit"></i></a>`
                        }
                        <a href="#" class="d-none avatar-text avatar-md" id="saveQuantity" data-price-id="${bill.price_id}"><i class="feather-save"></i></a>
                        <a href="#" class="d-none avatar-text avatar-md" id="cancelQuantity"><i class="feather-x"></i></a>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                        <label class="form-label mb-0">Selling Price:</label>
                        <span id="priceText">LKR ${bill.selling_price}</span>
                        <input type="number" class="form-control w-50 d-none" id="priceInput" 
                            value="${bill.selling_price}" data-price-id="${bill.price_id}">
                        <a href="#" class="avatar-text avatar-md" id="editPrice"><i class="feather-edit"></i></a>
                        <a href="#" class="d-none avatar-text avatar-md" id="savePrice" data-price-id="${bill.price_id}"><i class="feather-save"></i></a>
                        <a href="#" class="d-none avatar-text avatar-md" id="cancelPrice"><i class="feather-x"></i></a>
                    </div>
                </div>
            `;
        if (Array.isArray(bill.serials) && bill.serials.length > 0) {
                    bill.serials.forEach(serial => {
                html += `
                    <h6 class="fw-bold mb-3">Serial Numbers</h6>
                    <table class="table table-bordered" id="proposalList2">
                    <thead class="table-light">
                        <tr>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                
                    html += `
                        <tr>
                            <td><span class="serial-number">${serial.serial_number}</span></td>
                            <td>${serial.status}</td>
                            <td class="text-end">
                                <div class="hstack gap-2 justify-content-end">
                                    <a href="#" class="avatar-text avatar-md edit-serial-btn" data-serial-number="${serial.serial_number}">
                                        <i class="feather-edit"></i>
                                    </a>
                                    <a href="#" class="avatar-text avatar-md delete-serial-btn" data-serial-number="${serial.serial_number}">
                                        <i class="feather-trash-2"></i>
                                    </a>
    
                                </div>                     
                            </td>
                        </tr>
                    `;
                    });
                }


                html += `</tbody></table>`;


                    setTimeout(() => {
                        if ($.fn.DataTable.isDataTable('#proposalList2')) {
                                    $('#proposalList2').DataTable().destroy();
                                }

                                $('#proposalList2').DataTable({
                                    paging: true,
                                    searching: true,
                                    ordering: true,
                                    responsive: true,
                                    pageLength: 10, // or your desired number of rows per page
                                    drawCallback: function (settings) {
                                        const api = this.api();
                                        const pagination = $(this)
                                            .closest('.dataTables_wrapper')
                                            .find('.dataTables_paginate');

                                        // Show pagination only if more than one page
                                        if (api.page.info().pages <= 1) {
                                            pagination.hide();
                                        } else {
                                            pagination.show();
                                        }
                                    }
                                });
                           
                        $('#editQuantity').on('click', () => {
                            $('#billDetailsModal').modal('hide');
                            Swal.fire({
                            title: 'Enter Admin Password',
                            input: 'password',
                            inputLabel: 'Password',
                            inputPlaceholder: 'Enter admin password',
                            inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
                            showCancelButton: true,
                            confirmButtonText: 'Confirm',
                            showLoaderOnConfirm: true,
                            preConfirm: (password) => {
                                if (!password) {
                                    Swal.showValidationMessage('Password is required');
                                    return;
                                }

                                const formData = new URLSearchParams();
                                formData.append('admin_password', password);

                                return fetch('assets/php/table-helper/verify_admin_password.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(res => {
                                    if (res.status !== 'success') {
                                        throw new Error(res.message || 'Invalid password');
                                    }
                                    return true;
                                })
                                .catch(err => Swal.showValidationMessage(err.message));
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                            }).then(result => {
                            if (result.isConfirmed) {
                                $('#billDetailsModal').modal('show');
                                $('#quantityText').hide();
                                $('#quantityInput').removeClass('d-none').focus();
                                $('#editQuantity').hide();
                                $('#saveQuantity, #cancelQuantity').removeClass('d-none');
                            } else {
                                $('#billDetailsModal').modal('show');
                            }
                            });
                        });

                        $('#editPrice').on('click', () => {
                            $('#billDetailsModal').modal('hide');
                            Swal.fire({
                            title: 'Enter Admin Password',
                            input: 'password',
                            inputLabel: 'Password',
                            inputPlaceholder: 'Enter admin password',
                            inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
                            showCancelButton: true,
                            confirmButtonText: 'Confirm',
                            showLoaderOnConfirm: true,
                            preConfirm: (password) => {
                                if (!password) {
                                    Swal.showValidationMessage('Password is required');
                                    return;
                                }

                                const formData = new URLSearchParams();
                                formData.append('admin_password', password);

                                return fetch('assets/php/table-helper/verify_admin_password.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(res => {
                                    if (res.status !== 'success') {
                                        throw new Error(res.message || 'Invalid password');
                                    }
                                    return true;
                                })
                                .catch(err => Swal.showValidationMessage(err.message));
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                            }).then(result => {
                            if (result.isConfirmed) {
                                $('#billDetailsModal').modal('show');
                                $('#priceText').hide();
                                $('#priceInput').removeClass('d-none').focus();
                                $('#editPrice').hide();
                                $('#savePrice, #cancelPrice').removeClass('d-none');
                            } else {
                                $('#billDetailsModal').modal('show');
                            }
                            });
                        });

                        // Save Quantity
                        $(document).off('click', '#saveQuantity').on('click', '#saveQuantity', function (e) {
                            e.preventDefault();
                            const newQty = $('#quantityInput').val();
                            const priceId = $('#priceInput').data('price-id') || $('#saveQuantity').data('price-id');

                            if (!priceId || isNaN(newQty)) {
                                alert('Invalid quantity or price ID');
                                return;
                            }

                            $.ajax({
                                url: 'assets/php/table-helper/update_bill_quantity.php',
                                type: 'POST',
                                data: {
                                    price_id: priceId,
                                    quantity: newQty
                                },
                                dataType: 'json',
                                success: function (res) {
                                    if (res.status === 'success') {
                                        $('#quantityText').text(newQty).show();
                                        $('#quantityInput').addClass('d-none');
                                        $('#saveQuantity, #cancelQuantity').addClass('d-none');
                                        $('#editQuantity').show();
                                        showNotification('success', res.message);
                                    } else {
                                        showNotification('error', res.message);
                                    }
                                },
                                error: function (xhr) {
                                    showNotification('error', 'Failed to update quantity');
                                    console.error(xhr.responseText);
                                }
                            });
                        });


                        // Cancel Quantity
                        $(document).off('click', '#cancelQuantity').on('click', '#cancelQuantity', function (e) {
                            e.preventDefault();
                            const originalQty = $('#quantityText').text();
                            $('#quantityInput').val(originalQty).addClass('d-none');
                            $('#quantityText').show();
                            $('#saveQuantity, #cancelQuantity').addClass('d-none');
                            $('#editQuantity').show();
                        });

                        // Save Price
                        $(document).off('click', '#savePrice').on('click', '#savePrice', function (e) {
                            e.preventDefault();
                            const newPrice = $('#priceInput').val();
                            const priceId = $('#priceInput').data('price-id') || $('#savePrice').data('price-id');

                            if (!priceId || isNaN(newPrice)) {
                                alert('Invalid price or price ID');
                                return;
                            }

                            $.ajax({
                                url: 'assets/php/table-helper/update_bill_price.php',
                                type: 'POST',
                                data: {
                                    price_id: priceId,
                                    price: newPrice
                                },
                                dataType: 'json',
                                success: function (res) {
                                    if (res.status === 'success') {
                                        $('#priceText').text('LKR ' + newPrice).show();
                                        $('#priceInput').addClass('d-none');
                                        $('#savePrice, #cancelPrice').addClass('d-none');
                                        $('#editPrice').show();
                                        showNotification('success', res.message);
                                    } else {
                                        showNotification('error', res.message);
                                    }
                                },
                                error: function (xhr) {
                                    showNotification('error', 'Failed to update price');
                                    console.error(xhr.responseText);
                                }
                            });


                                    
                    });


                    // Cancel Price
                    $(document).off('click', '#cancelPrice').on('click', '#cancelPrice', function (e) {
                        e.preventDefault();
                        const originalPrice = $('#priceText').text().replace('LKR ', '');
                        $('#priceInput').val(originalPrice).addClass('d-none');
                        $('#priceText').show();
                        $('#savePrice, #cancelPrice').addClass('d-none');
                        $('#editPrice').show();
                    });
                 }, 100);


            return html;
            }

            // Function to fetch bill details when no serial numbers are present
            function fetchBillDetails(accessoryId) {
            return $.ajax({
                url: 'assets/php/table-helper/get_bill_details.php',
                type: 'GET',
                data: { accessory_id: accessoryId },
                dataType: 'json'
            });
            }

            




            // Handle save row button click
            $('#accessoriesTable tbody').on('click', '.save-row', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const accessory_id = $(this).data('accessory-id');
                const price_id = $(this).data('price-id');

                if (!accessory_id || !price_id) {
                    alert('Error: Accessory ID or Price ID is missing. Unable to update the record.');
                    console.log('Missing accessory_id or price_id:', { accessory_id, price_id });
                    return;
                }

                const quantityInput = parseInt(row.find('td:eq(5) input').val());
                const serialsData = $(this).closest('tr').find('.view-serials').data('serials');
                const minQuantity = serialsData ? serialsData.length : 0;

                if (quantityInput < minQuantity) {
                    alert(`Error: The minimum quantity is ${minQuantity} due to existing serial numbers.`);
                    return;
                }

                const rowData = {
                    accessory_id: accessory_id,
                    price_id: price_id,
                    brand: row.find('td:eq(1) input').val(),
                    accessory_name: row.find('td:eq(2) input').val(),
                    buying_price: parseFloat(row.find('td:eq(3) input').val().replace(/[^\d.-]/g, '')),
                    selling_price: parseFloat(row.find('td:eq(4) input').val().replace(/[^\d.-]/g, '')),
                    quantity: quantityInput
                };

                console.log('Saving row data:', rowData);

                $.ajax({
                    url: 'assets/php/table-helper/update_accessory.php',
                    type: 'POST',
                    data: rowData,
                    dataType: 'json',
                    success: function (response) {
                        showNotification('success', response.message);
                        console.log('Response from server:', response);
                        if (response.status === 'success') {
                            table.ajax.reload();
                        } else {
                            alert(response.message);
                            console.log('Server error response:', response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        alert('Failed to update the accessory. Please check your input and try again.');
                    }
                });
            });

            // Handle close edit row button click
            $('#accessoriesTable tbody').on('click', '.close-row', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // Handle delete row button click
            $('#accessoriesTable tbody').on('click', '.delete-row', function (e) {
                e.preventDefault();
                const accessory_id = $(this).data('accessory-id');
                const price_id = $(this).data('price-id');

                if (!accessory_id || !price_id) {
                    alert('Error: Accessory ID or Price ID is missing. Unable to delete the record.');
                    console.log('Missing accessory_id or price_id:', { accessory_id, price_id });
                    return;
                }




                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will delete the accessory and all related data.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'assets/php/table-helper/delete_accessory.php',
                            type: 'POST',
                            data: { accessory_id: accessory_id, price_id: price_id },
                            dataType: 'json',
                            success: function (response) {
                                showNotification('success', response.message);
                                if (response.status === 'success') {
                                    table.ajax.reload();
                                    Swal.fire('Deleted!', response.message, 'success');
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'Failed to delete the accessory.', 'error');
                            }
                        });
                    }
                });
            });

            $(document).off('click', '.edit-serial-btn').on('click', '.edit-serial-btn', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const serialNumberSpan = row.find('.serial-number');
                const currentSerial = serialNumberSpan.text();

                $('#billDetailsModal').modal('hide');

                Swal.fire({
                    title: 'Enter Admin Password',
                    input: 'password',
                    inputLabel: 'Password',
                    inputPlaceholder: 'Enter admin password',
                    inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    showLoaderOnConfirm: true,
                    preConfirm: (password) => {
                        if (!password) return Swal.showValidationMessage('Password is required');
                        return $.post('assets/php/table-helper/verify_admin_password.php', {
                            admin_password: password
                        }).then(res => {
                            if (res.status !== 'success') throw new Error(res.message);
                            return true;
                        }).catch(err => Swal.showValidationMessage(err.message));
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(result => {
                    $('#billDetailsModal').modal('show');

                    if (result.isConfirmed) {
                        serialNumberSpan.html(`<input type="text" class="form-control form-control-sm serial-edit-input" value="${currentSerial}">`);
                        row.find('.edit-serial-btn').replaceWith(`
                            <a href="#" class="avatar-text avatar-md save-serial-btn" data-original="${currentSerial}">
                                <i class="feather-save"></i>
                            </a>
                            <a href="#" class="avatar-text avatar-md cancel-serial-btn">
                                <i class="feather-x"></i>
                            </a>
                        `);
                    }
                });
            });


            // Save serial
            $(document).off('click', '.save-serial-btn').on('click', '.save-serial-btn', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const original = $(this).data('original');
            const newSerial = row.find('.serial-edit-input').val();

            $.post('assets/php/table-helper/update_serial_number.php', {
                original_serial: original,
                new_serial: newSerial
            }, res => {
                if (res.status === 'success') {
                    row.find('.serial-number').text(newSerial);
                    row.find('.save-serial-btn').remove();
                    row.find('.cancel-serial-btn').remove();
                    row.find('.hstack').prepend(`
                        <a href="#" class="avatar-text avatar-md edit-serial-btn" data-serial-number="${newSerial}">
                            <i class="feather-edit"></i>
                        </a>
                    `);
                    row.removeData('original');
                    showNotification('success', res.message);
                } else {
                    showNotification('error', res.message);
                }
            }, 'json');
        });


            // Cancel edit
            $(document).off('click', '.cancel-serial-btn').on('click', '.cancel-serial-btn', function (e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const original = row.find('.save-serial-btn').data('original');
                row.find('.serial-number').text(original);
                row.find('.save-serial-btn').remove();
                row.find('.cancel-serial-btn').remove();
                row.find('.hstack').prepend(`
                    <a href="#" class="avatar-text avatar-md edit-serial-btn" data-serial-number="${original}">
                        <i class="feather-edit"></i>
                    </a>
                `);
                row.removeData('original');
            });


            $(document).off('click', '.delete-serial-btn').on('click', '.delete-serial-btn', function (e) {
                e.preventDefault();
                const serial = $(this).data('serial-number');
                const row = $(this).closest('tr');

                $('#billDetailsModal').modal('hide');

                Swal.fire({
                    title: 'Enter Admin Password to Delete',
                    input: 'password',
                    inputLabel: 'Password',
                    inputPlaceholder: 'Enter admin password',
                    inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    showLoaderOnConfirm: true,
                    preConfirm: (password) => {
                        if (!password) return Swal.showValidationMessage('Password is required');
                        return $.post('assets/php/table-helper/verify_admin_password.php', {
                            admin_password: password
                        }).then(res => {
                            if (res.status !== 'success') throw new Error(res.message);
                            return true;
                        }).catch(err => Swal.showValidationMessage(err.message));
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(result => {
                    $('#billDetailsModal').modal('show');

                    if (result.isConfirmed) {
                        $.post('assets/php/table-helper/delete_serial_number.php', {
                            serial_number: serial
                        }, res => {
                            if (res.status === 'success') {
                                row.remove();
                                showNotification('success', res.message);
                            } else {
                                showNotification('error', res.message);
                            }
                        }, 'json');
                    }
                });
            });




            
        });

    </script>

</body>

</html>
