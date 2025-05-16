<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessories Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <style>
        .table-container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            text-align: right;
        }
        .btn-refresh {
            margin-bottom: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="table-container">
    <h2>Accessories Inventory</h2>
    <button class="btn-refresh" onclick="fetchAccessories()">Refresh Data</button>
    <table id="accessoriesTable" class="display">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Accessory Name</th>
                <th>Brand</th>
                <th>Color</th>
                <th>Other Variation</th>
                <th>Buying Price</th>
                <th>Selling Price</th>
                <th>Quantity</th>
                <th>Serial Numbers</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated here by AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for showing serial numbers -->
<div id="serialNumberModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Serial Numbers</h3>
        <ul id="serialNumberList">
            <!-- Serial numbers will be listed here -->
        </ul>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#accessoriesTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            order: [[6, 'desc']], // Default sort by Selling Price
            ajax: {
                url: 'get_accessories.php', // PHP script to fetch accessory data
                type: 'GET',
                dataSrc: '',
                error: function(xhr, status, error) {
                    console.error('Error fetching accessory data:', error);
                }
            },
            columns: [
                { data: 'barcode' },
                { data: 'accessory_name' },
                { data: 'brand' },
                { data: 'color' },
                { data: 'other' },
                { data: 'buying_price', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
                { data: 'selling_price', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
                { data: 'quantity' },
                {
                    data: 'serial_numbers',
                    render: function(data, type, row) {
                        if (data && data.length > 0) {
                            return `<button class="btn-view-serials" data-serials="${data.join(', ')}">View</button>`;
                        } else {
                            return 'N/A';
                        }
                    }
                }
            ]
        });

        // Event listener for viewing serial numbers in a modal
        $('#accessoriesTable tbody').on('click', '.btn-view-serials', function() {
            const serials = $(this).data('serials').split(', ');
            $('#serialNumberList').empty();
            serials.forEach(serial => {
                $('#serialNumberList').append(`<li>${serial}</li>`);
            });
            $('#serialNumberModal').show();
        });

        // Close modal
        $('.close').on('click', function() {
            $('#serialNumberModal').hide();
        });
    });

    // Function to manually refresh data
    function fetchAccessories() {
        $('#accessoriesTable').DataTable().ajax.reload();
    }
</script>

</body>
</html>
