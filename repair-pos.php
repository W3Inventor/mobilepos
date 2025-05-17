<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth-login.php");
    exit;
}

include 'config/dbconnect.php';
include 'templates/navigation.php';
include 'templates/header.php';

if (!isset($_GET['repair_id'])) {
    die("Repair ID missing");
}
$repair_id = intval($_GET['repair_id']);
$stmt = $conn->prepare("SELECT r.ir_id, r.imei, r.brand, r.model FROM in_house_repair r WHERE r.ir_id = ?");
$stmt->bind_param("i", $repair_id);
$stmt->execute();
$repair = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Repair POS Invoice</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="assets/css/theme.min.css">

    <style>
        .table td input {
            width: 100%;
        }
        .form-select, .form-control {
            min-height: 45px;
        }
        .btn-danger {
            padding: 0.5rem 0.75rem;
        }
    </style>
</head>
<body>
<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Repair POS Invoice</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Repair POS</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Device: <?php echo htmlspecialchars($repair['brand'] . ' ' . $repair['model']); ?> (IMEI: <?php echo htmlspecialchars($repair['imei']); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <form id="repairInvoiceForm" method="post" action="assets/php/helper/repair-helper/submit_repair_invoice.php">
                            <input type="hidden" name="repair_id" value="<?php echo $repair_id; ?>">

                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Part Name</th>
                                        <th>Serial Number</th>
                                        <th>Warranty</th>
                                        <th>Quantity</th>
                                        <th>Price (LKR)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="partsBody"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td colspan="2" class="fw-bold"><span id="grandTotal">0.00</span> LKR</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="mb-3">
                                <button type="button" class="btn btn-secondary me-2" id="addPart">+ Add Part</button>
                                <button type="submit" class="btn btn-primary">Create Invoice</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- FIXED LOAD ORDER -->
<script src="assets/vendors/js/jquery.min.js"></script>
<script src="assets/vendors/js/select2.min.js"></script>
<script>
    function initSelect2(select) {
        select.select2({
            placeholder: 'Search part or type to add',
            tags: true,
            minimumInputLength: 1,
            ajax: {
                url: 'assets/php/helper/repair-helper/search_parts.php',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term })
            },
            width: '100%'
        });
    }

    function addPartRow() {
        const row = $('<tr>');
        const part = $('<select name="parts[name][]" class="form-select part-name" required></select>');
        const serial = $('<input type="text" name="parts[serial][]" class="form-control">');
        const warranty = $('<input type="text" name="parts[warranty][]" class="form-control">');
        const qty = $('<input type="number" name="parts[qty][]" value="1" min="1" class="form-control qty" required>');
        const price = $('<input type="number" name="parts[price][]" step="0.01" class="form-control price" required>');
        const removeBtn = $('<button type="button" class="btn btn-danger">X</button>').on('click', function () {
            row.remove();
            updateTotal();
        });

        row.append($('<td>').append(part))
            .append($('<td>').append(serial))
            .append($('<td>').append(warranty))
            .append($('<td>').append(qty))
            .append($('<td>').append(price))
            .append($('<td>').append(removeBtn));

        $('#partsBody').append(row);
        initSelect2(part);
    }

    function updateTotal() {
        let total = 0;
        $('.qty').each(function (i, el) {
            const qty = parseFloat($(el).val()) || 0;
            const price = parseFloat($(el).closest('tr').find('.price').val()) || 0;
            total += qty * price;
        });
        $('#grandTotal').text(total.toFixed(2));
    }

    $(document).on('input', '.qty, .price', updateTotal);
    $('#addPart').on('click', addPartRow);
    $(document).ready(() => addPartRow());

    $('#repairInvoiceForm').on('submit', function () {
        const total = $('#grandTotal').text();
        $('<input>').attr({ type: 'hidden', name: 'total_amount', value: total }).appendTo(this);
    });

    // Auto-fill serial + price when part is selected
$(document).on('change', '.part-name', function () {
    const row = $(this).closest('tr');
    const accessoryId = $(this).val();
    if (!accessoryId) return;

    $.ajax({
        url: 'assets/php/helper/repair-helper/get-product-details.php',
        method: 'POST',
        data: { accessory_id: accessoryId },
        dataType: 'json',
        success: function (data) {
            if (data.prices && data.prices.length > 0) {
                // Set the first price
                row.find('input[name^="parts[price]"]').val(data.prices[0]);
            }

            if (data.serials && data.serials.length > 0) {
                const serialInput = row.find('input[name^="parts[serial]"]');
                const serialSelect = $('<select class="form-select serial-dropdown"></select>');

                serialSelect.append('<option value="">Select serial</option>');
                data.serials.forEach(serial => {
                    serialSelect.append(`<option value="${serial}">${serial}</option>`);
                });

                serialInput.replaceWith(serialSelect);

                // When serial is selected, insert it back into a hidden input before submit
                serialSelect.on('change', function () {
                    const selectedSerial = $(this).val();
                    const hiddenInput = $('<input type="hidden" name="parts[serial][]" />');
                    hiddenInput.val(selectedSerial);
                    $(this).after(hiddenInput);
                    $(this).remove(); // remove dropdown once selected
                });

            }
        },
        error: function (xhr, status, err) {
            console.error('Failed to fetch product details:', err);
        }
    });
});

</script>

<script src="assets/vendors/js/vendors.min.js"></script>
<script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
<script src="assets/js/common-init.min.js"></script>
<script src="assets/js/settings-init.min.js"></script>
<script src="assets/js/theme-customizer-init.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>


