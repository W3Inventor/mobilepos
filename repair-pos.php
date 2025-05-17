<?php

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<script>console.error('PHP Error: " . addslashes("$errstr in $errfile on line $errline") . "');</script>";
    return false; // allow normal PHP error handler to run too
});

set_exception_handler(function($exception) {
    echo "<script>console.error('PHP Exception: " . addslashes($exception->getMessage()) . "');</script>";
});


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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/theme.min.css">

    <style>
        .table td input, .table td select {
            width: 100%;
        }
        .form-select, .form-control {
            min-height: 45px;
        }
        .btn-danger {
            padding: 0.5rem 0.75rem;
        }
        .select2-container {
            min-width: 250px !important;
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
                        <form id="repairInvoiceForm" method="post">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    function initSelect2(select) {
        select.select2({
            placeholder: 'Search part, serial or ID',
            tags: true,
            minimumInputLength: 1,
            ajax: {
                url: 'assets/php/helper/repair-helper/search_parts.php',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: function (data) {
                    const staticOption = {
                        id: 'static||', // static ID
                        text: 'Repairing Cost',
                        serials: []
                    };

                    const results = data.results.map(item => ({
                        id: item.id,
                        text: item.text,
                        serials: item.serials || []
                    }));

                    // If not already included, push "Repairing Cost" to top
                    if (!results.some(r => r.text === 'Repairing Cost')) {
                        results.unshift(staticOption);
                    }

                    return { results };
                }
            },
            width: '100%'
        });
    }


    function addPartRow() {
        const row = $('<tr>');
        const part = $('<select name="parts[name][]" class="form-select part-name" required></select>');
        const serial = $('<input type="text" name="parts[serial][]" class="form-control serial-box">');
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
        $('.qty').each(function () {
            const qty = parseFloat($(this).val()) || 0;
            const price = parseFloat($(this).closest('tr').find('.price').val()) || 0;
            total += qty * price;
        });
        $('#grandTotal').text(total.toFixed(2));
    }

    $('#addPart').on('click', addPartRow);
    $(document).on('input', '.qty, .price', updateTotal);
    $(document).ready(() => addPartRow());

    $('#repairInvoiceForm').on('submit', function (e) {
    e.preventDefault(); // prevent normal form submission

    const form = $(this);
    const formData = form.serializeArray();

    // Manually add total_amount since it's dynamically calculated
    const total = $('#grandTotal').text();
    formData.push({ name: 'total_amount', value: total });

    $.ajax({
        url: 'assets/php/helper/repair-helper/submit_repair_invoice.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Invoice Created',
                    text: response.success,
                    confirmButtonText: 'Done'
                }).then(() => {
                    window.location.href = 'in-house-repair.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Unknown error occurred'
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX failed:", error);
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Failed to create invoice. Please try again.'
            });
        }
    });
});

    // ✅ Handle selection from Select2 and auto-fill serial + price
    $(document).on('select2:select', '.part-name', function (e) {
    const row = $(this).closest('tr');
    const selectedData = e.params.data;
    const [accessoryId, serialNumber] = selectedData.id.split('||');

// Autofill serial
row.find('input[name^="parts[serial]"]').val(serialNumber);

// Skip AJAX for "Repairing Cost" (no accessory ID)
if (selectedData.text === 'Repairing Cost') {
    return;
}

if (!accessoryId || isNaN(accessoryId)) return;


    $.ajax({
        url: 'assets/php/helper/repair-helper/get-product-details.php',
        method: 'POST',
        data: { accessory_id: accessoryId },
        dataType: 'json',
        success: function (data) {
            if (data.prices?.length) {
                row.find('input[name^="parts[price]"]').val(data.prices[0]);
                updateTotal(); // ✅ Trigger total update after setting price
            }

        },
        error: function (xhr, status, error) {
            console.error("AJAX ERROR:", error);
        }
    });
});

</script>

</body>
</html>

