<?php
include 'config/dbconnect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h2 style='color:red;'>No repair ID specified.</h2>");
}
$repair_id = (int) $_GET['id'];

// Fetch repair + customer
$repair_stmt = $conn->prepare("SELECT r.ir_id, r.status, r.brand, r.model, r.imei, r.reason, r.estimate_price, c.full_name
    FROM in_house_repair r
    JOIN customers c ON r.customer_id = c.customer_id
    WHERE r.ir_id = ?");
$repair_stmt->bind_param("i", $repair_id);
$repair_stmt->execute();
$repair_result = $repair_stmt->get_result()->fetch_assoc();
$repair_stmt->close();

if (!$repair_result) {
    die("<h2 style='color:red;'>No repair record found for ID $repair_id</h2>");
}

// Define ordered statuses
$all_statuses = ['Submitted', 'Processing', 'Fixed', 'Ready to Pickup', 'Paid & Pickup'];

// Fetch history
$status_history = [];
$history_stmt = $conn->prepare("SELECT status, timestamp FROM repair_status_history WHERE repair_id = ? ORDER BY timestamp ASC");
$history_stmt->bind_param("i", $repair_id);
$history_stmt->execute();
$result = $history_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $status_history[strtolower($row['status'])] = $row['timestamp'];
}
$history_stmt->close();

// Determine current step
$current_step_index = 0;
foreach ($all_statuses as $i => $status) {
    if (isset($status_history[strtolower($status)])) {
        $current_step_index = $i;
    }
}

// Set custom width per step
$progress_width_map = [10, 30, 50, 70, 100];
$progress_width = $progress_width_map[$current_step_index] ?? 10;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repair Tracking - <?= htmlspecialchars($repair_result['ir_id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .timeline-container {
            background: #f4f6f9;
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            counter-reset: progress-counter;
        }

        .progressbar {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 20px 0 30px;
        }

        .progress-line-bg {
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #d3d3d3;
            z-index: 0;
        }

        .progress-line-fill {
            position: absolute;
            top: 25px;
            left: 0;
            height: 4px;
            background-color: #0d6efd;
            z-index: 1;
            transition: width 0.4s ease-in-out;
        }

        .progress-step {
            position: relative;
            text-align: center;
            z-index: 2;
            width: 20%;
        }

        .progress-step::before {
            counter-increment: progress-counter; 
            content: counter(progress-counter);  
            width: 30px;
            height: 30px;
            line-height: 30px;
            border: 2px solid #6c757d;
            display: block;
            text-align: center;
            margin: 0 auto 10px;
            border-radius: 50%;
            background-color: #fff;
            color: #6c757d;
            font-weight: bold;
            margin-top: 10px;
        }

        .progress-step.active::before {
            border-color: #0d6efd;
            background-color: #0d6efd;
            color: #fff;
         }
        .progress-step.current::before {
            animation: wavePulse 1.5s infinite;
        }


        .progress-step .label {
            font-size: 13px;
            margin-top: 5px;
            font-weight: 500;
        }

        .status-timeline {
            margin-top: 30px;
        }

        .status-entry {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .status-entry:last-child {
            border-bottom: none;
        }

        .status-entry time {
            color: #0d6efd;
            font-weight: bold;
        }
        .attention {
            position: relative;
            padding: 5px 10px;
            border: 2px solid #0d6efd;
            border-radius: 12px;
            background-color: #fff;
            animation: wavePulse 1.5s infinite;
            box-shadow: 0 0 0 rgba(0, 17, 255, 0.7);
        }
        @keyframes wavePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 17, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 0, 128, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 0, 128, 0);
            }
        }
        .timestamp {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #555;
            background-color: #f5f5f5;
            padding: 6px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-right: 8px;
            font-weight: 500 !important;
        }

        .timestamp .divider {
            margin: 0 8px;
            color: #999;
            font-weight: 600;
        }

        .status {
            font-weight: bold;
            color: #007bff;
            font-size: 14px;
            background: #e8f0fe;
            padding: 4px 8px;
            border-radius: 4px;
        }

    </style>
</head>
<body class="container py-5">
    <h3 class="mb-4">Repair Tracking for <?= htmlspecialchars($repair_result['full_name']) ?></h3>

    <div class="timeline-container">
        <p><strong>Repair ID:</strong> <?= htmlspecialchars($repair_result['ir_id']) ?><br>
           <strong>Device:</strong> <?= htmlspecialchars($repair_result['brand'] . ' ' . $repair_result['model']) ?><br>
           <strong>IMEI:</strong> <?= htmlspecialchars($repair_result['imei']) ?><br>
           <strong>Reason:</strong> <?= htmlspecialchars($repair_result['reason']) ?><br>
           <strong>Estimate Price:</strong> <?= htmlspecialchars($repair_result['estimate_price']) ?><br>
           <strong>Current Status:</strong> <span class="badge attention bg-primary"><?= htmlspecialchars($repair_result['status']) ?></span>
        </p>

        <div class="progressbar">
            <div class="progress-line-bg"></div>
            <div class="progress-line-fill" style="width: <?= $progress_width ?>%;"></div>

            <?php foreach ($all_statuses as $i => $status): ?>
                <?php
                    $is_active = $i <= $current_step_index;
                    $is_current = $i === $current_step_index;
                    $step_class = $is_active ? 'active' : '';
                    $step_class .= $is_current ? ' current' : '';
                ?>
                <div class="progress-step <?= $step_class ?>">
                    <div class="label"><?= htmlspecialchars($status) ?></div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="status-timeline">
            <h5 class="mb-3">Status Timeline</h5>
            <?php foreach ($status_history as $status => $timestamp): ?>
                <div class="status-entry">
                    <time class="timestamp"><?= date('Y-m-d', strtotime($timestamp)) ?> <span class="divider">|</span><?= date('h:i A', strtotime($timestamp)) ?></time> – <strong class="status"><?= ucfirst($status) ?></strong>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="text-center mt-5">
        <p>Need help? Contact support.</p>
    </div>
</body>
</html>
