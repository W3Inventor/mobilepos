<?php
// Include the database connection
include '../../../../config/dbconnect.php'; // Adjust the path accordingly

// Check if $conn is defined
if (!isset($conn)) {
    die("Database connection failed: conn is not defined.");
}

// Get the product ID and type from the AJAX request
$product_id = $conn->real_escape_string(trim($_POST['product_id'])); // Expecting IMEI here
$product_type = $conn->real_escape_string($_POST['product_type']); // Ensure the correct type is being handled

// Debugging step: Log inputs to verify the received data
error_log("Received Product ID: $product_id, Product Type: $product_type");

// Define the query based on the product type
switch ($product_type) {
    case 'accessory':
        $query = "
            SELECT 
                a.accessory_id AS id, 
                a.accessory_name AS name, 
                a.brand, 
                'Accessory' AS type,
                s.serial_number
            FROM accessories a
            LEFT JOIN serial_numbers s ON a.accessory_id = s.accessory_id
            WHERE a.accessory_id = '$product_id'";
        break;

    case 'variation_1':
        $query = "
            SELECT vid_1 AS id, model AS name, brand, 'Variation 1' AS type, '' AS price 
            FROM variation_1 
            WHERE vid_1 = '$product_id'";
        break;

    case 'variation_2':
        $query = "
            SELECT vid_2 AS id, '' AS name, '' AS brand, 'Variation 2' AS type, selling AS price 
            FROM variation_2 
            WHERE vid_2 = '$product_id'";
        break;

    case 'mobile':
        // Query to fetch mobile details using IMEI
        $query = "
            SELECT m.imei AS id, v1.model AS name, v1.brand, 'Mobile' AS type, v2.selling AS price
            FROM mobile m
            LEFT JOIN variation_1 v1 ON m.vid_1 = v1.vid_1
            LEFT JOIN variation_2 v2 ON m.vid_2 = v2.vid_2
            WHERE m.imei = '$product_id'"; // Ensure the query checks the correct IMEI field
        break;

    default:
        error_log("Invalid product type received: $product_type"); // Log error for debugging
        echo json_encode(['error' => 'Invalid product type']);
        exit;
}

// Execute the query and check for errors
$result = $conn->query($query);

error_log("Received Product ID: $product_id, Product Type: $product_type");

if ($result === false) {
    error_log('Query error: ' . $conn->error); // Log query errors for debugging
    echo json_encode(['error' => 'Query error: ' . $conn->error]);
    exit;
} else {
    error_log("Query successful for Product ID: $product_id, Product Type: $product_type");
}


// Check if the product was found
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    error_log('Fetched Product Details: ' . print_r($product, true));


    // Fetch warranties
    $warrantyQuery = "SELECT w_id AS id, warranty FROM warranty";
    $warrantyResult = $conn->query($warrantyQuery);
    $warranties = [];

    while ($row = $warrantyResult->fetch_assoc()) {
        $warranties[] = $row;
    }

    $product['warranties'] = $warranties;

    // Fetch prices based on the product type
    if ($product_type === 'accessory') {
        $priceQuery = "
            SELECT selling 
            FROM accessories_price 
            WHERE accessory_id = '$product_id'";
        $priceResult = $conn->query($priceQuery);
        $prices = [];

        while ($priceRow = $priceResult->fetch_assoc()) {
            $prices[] = $priceRow['selling'];
        }
        error_log('Fetched Prices: ' . print_r($prices, true));

        $product['prices'] = $prices;
    } elseif ($product_type === 'mobile') {
        // Fetch prices related to the mobile's variation_2
        $priceQuery = "
            SELECT v2.selling 
            FROM variation_2 v2
            LEFT JOIN mobile m ON m.vid_2 = v2.vid_2
            WHERE m.imei = '$product_id'";
        $priceResult = $conn->query($priceQuery);
        $prices = [];

        while ($priceRow = $priceResult->fetch_assoc()) {
            $prices[] = $priceRow['selling'];
        }

        $product['prices'] = $prices;
    }

    // Return product details as JSON
    echo json_encode($product);
} else {
    error_log("Product not found for ID: $product_id, Type: $product_type"); // Log the exact issue
    echo json_encode(['error' => 'Product not found']);
}

$conn->close();
?>
