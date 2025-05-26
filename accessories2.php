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
</head>
<style>
    
    </style>

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
                                            <th>Buying Price</th>
                                            <th>Selling Price</th>
                                            <th>Quantity</th>
                                            <th>Serial Numbers</th>
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

    <!-- Serial Numbers Table Modal -->
<div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serialNumberModalLabel">Serial Numbers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="serialNumbersTable" class="table table-hover">
                        <!-- Ensure consistent classes and styles with main table -->
                    </table>
                </div>
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
                data: 'buying_price',
                render: $.fn.dataTable.render.number(',', '.', 2, 'LKR ')
            },
            {
                data: 'selling_price',
                render: $.fn.dataTable.render.number(',', '.', 2, 'LKR ')
            },
            { data: 'quantity' },
            {
                data: 'serial_numbers',
                render: function (data, type, row) {
                    if (data && data.length > 0) {
                        return `
                            <div class='hstack gap-2'>
                                <a href="#" class='avatar-text avatar-md view-serials' 
                                   data-serials='${JSON.stringify(data).replace(/"/g, '&quot;')}' 
                                   data-accessory-id='${row.accessory_id}' 
                                   data-price-id='${row.price_id}'>
                                    <i class='feather-eye'></i>
                                </a>
                            </div>`;
                    } else {
                        return 'N/A';
                    }
                }
            },
            {
                data: null,
                className: 'text-end',
                render: function (data, type, row) {
                    return `
                            <div class='hstack gap-2 justify-content-end'>
                                <a href="#" class="edit-row avatar-text avatar-md" data-accessory-id="${row.accessory_id}" data-price-id="${row.price_id}">
                                    <i class='feather-edit'></i>
                                </a>
                                <a href="#" class="delete-row avatar-text avatar-md" data-accessory-id="${row.accessory_id}" data-price-id="${row.price_id}">
                                    <i class='feather-trash-2'></i>
                                </a>
                            </div>`;
                }
            }
        ]
    });

    let serialTable;

    // Handle view serial numbers button click
    $('#accessoriesTable tbody').on('click', '.view-serials', function () {
        let serials = $(this).data('serials');
        try {
            serials = typeof serials === 'string' ? JSON.parse(serials) : serials;
        } catch (error) {
            console.error('Failed to parse serials data:', error);
            alert('An error occurred while processing serial numbers. Please try again.');
            return;
        }

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
                        return `
                            <div class='hstack gap-2 justify-content-end'>
                                <a href="#" class="avatar-text avatar-md edit-serial" title="Edit" data-serial-number="${row.serial_number}">
                                    <i class='feather-edit'></i>
                                </a>
                                <a href="#" class="avatar-text avatar-md delete-serial" title="Delete" data-serial-number="${row.serial_number}">
                                    <i class='feather-trash-2'></i>
                                </a>
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

    // Handle Delete Serial Number
    $('#serialNumbersTable').on('click', '.delete-serial', function (e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        const serialNumber = $(this).data('serial-number');

        console.log(`Deleting serial with Serial Number: ${serialNumber}`);

        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the selected serial number.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'assets/php/table-helper/delete_serial_number.php',
                    type: 'POST',
                    data: {
                        serial_number: serialNumber
                    },
                    dataType: 'json',
                    success: function (response) {
                        showNotification('success', response.message);
                        console.log('Response from server:', response);
                        if (response.status === 'success') {
                            serialTable.row(row).remove().draw();
                            Swal.fire('Deleted!', response.message, 'success');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        Swal.fire('Error!', 'Failed to delete the serial number.', 'error');
                    }
                });
            }
        });
    });

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

        row.find('td').each(function (index) {
            if (index > 0 && index < 6) { 
                const cellValue = $(this).text();
                $(this).html(`<input type="text" class="form-control" value="${cellValue}">`);
            }
        });

        $(this).replaceWith(`
            <a href="#" class="save-row avatar-text avatar-md" data-accessory-id="${accessory_id}" data-price-id="${price_id}">
                <i class='feather-save'></i>
            </a>
            <a href="#" class="close-row avatar-text avatar-md">
                <i class='feather-x'></i>
            </a>
        `);
    });

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
});

</script>

</body>

</html>
