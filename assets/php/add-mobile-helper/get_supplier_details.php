<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX and if the necessary parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['company_name']) || isset($_GET['supplier_name']))) {
    $company_name = isset($_GET['company_name']) ? $_GET['company_name'] : null;
    $supplier_name = isset($_GET['supplier_name']) ? $_GET['supplier_name'] : null;

    // Prepare the base query
    $query = "SELECT supplier_id, company_name, supplier_name, mobile_number, address 
              FROM supplier 
              WHERE ";
    
    // Add conditions based on the provided parameters
    $conditions = [];
    $params = [];
    $types = '';
    
    if ($company_name) {
        $conditions[] = "company_name = ?";
        $params[] = $company_name;
        $types .= 's';
    }
    
    if ($supplier_name) {
        $conditions[] = "supplier_name = ?";
        $params[] = $supplier_name;
        $types .= 's';
    }
    
    // Check if any conditions were added
    if (!empty($conditions)) {
        // Combine the conditions
        $query .= implode(' AND ', $conditions);
        
        // Prepare and execute the statement
        $stmt = $conn->prepare($query);
        
        // Check if the preparation was successful
        if (!$stmt) {
            echo json_encode(['error' => 'Statement preparation failed: ' . $conn->error]);
            exit;
        }

        // Bind parameters and execute the query
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a supplier was found
        if ($result->num_rows > 0) {
            $supplier = $result->fetch_assoc();

            // Return the supplier details as JSON
            echo json_encode([
                'supplier_id' => $supplier['supplier_id'],
                'company_name' => $supplier['company_name'],
                'supplier_name' => $supplier['supplier_name'],
                'mobile_number' => $supplier['mobile_number'],
                'address' => $supplier['address'],
            ]);
        } else {
            // Return an error if no supplier was found
            echo json_encode(['error' => 'Supplier not found']);
        }
    } else {
        // If no conditions were set, output an error message
        echo json_encode(['error' => 'No valid search parameters provided.']);
    }
} else {
    // If the request is invalid, return an error message
    echo json_encode(['error' => 'Invalid request method or parameters.']);
}

$conn->close();
?>
