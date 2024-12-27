<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';
$vendorID = $_POST['vendorID'] ?? ''; // Get the VendorID from the request
$supplyID = $_POST['SupplyID'] ?? ''; // Get the SupplyID from the request

switch ($action) {
    case 'getDrugs':
        if ($vendorID) {
            getDrugs($conn, $vendorID);
        } else {
            echo json_encode(["status" => "error", "message" => "Vendor ID is required"]);
        }
        break;
    case 'getDrugDetails':
        if ($supplyID) {
            getDrugDetails($conn, $supplyID);
        } else {
            echo json_encode(["status" => "error", "message" => "SupplyID is required"]);
        }
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

// Function to get drug supplies for a specific vendor
function getDrugs($conn, $vendorID) {
    $query = "
        SELECT 
            DS.SupplyID, 
            DD.BrandName, 
            DD.DrugImage, 
            DD.Price
        FROM 
            Drug_Supply DS
        JOIN 
            Vendor V ON DS.VendorID = V.VendorID
        JOIN 
            Drug_Details DD ON DS.SupplyID = DD.SupplyID
        WHERE 
            V.VendorID = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $vendorID); // Bind the vendorID parameter
        $stmt->execute();
        $result = $stmt->get_result();
        
        $drugs = [];
        
        while ($row = $result->fetch_assoc()) {
            $drugs[] = [
                'SupplyID' => $row['SupplyID'],
                'BrandName' => $row['BrandName'],
                'DrugImage' => $row['DrugImage'],
                'Price' => $row['Price']
            ];
        }
        
        if (count($drugs) > 0) {
            echo json_encode(["status" => "success", "drugs" => $drugs]);
        } else {
            echo json_encode(["status" => "error", "message" => "No drugs found for this vendor"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Error preparing the query"]);
    }

    $conn->close();
}

// Function to get drug details by SupplyID
function getDrugDetails($conn, $supplyID) {
    $query = "
        SELECT
            DD.DrugID,
            DS.Quantity,
            DD.BrandName,
            DD.GenericName,
            DD.Active_Ingredient,
            DD.Dosage,
            DD.Dosage_Form,
            DD.Manufacturer,
            DD.Manufacture_Date,
            DD.SideEffects,
            DD.Price,
            DD.DrugImage
        FROM
            Drug_Supply DS
        JOIN
            Drug_Details DD ON DS.SupplyID = DD.SupplyID
        WHERE
            DS.SupplyID = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $supplyID); // Bind the SupplyID parameter
        $stmt->execute();
        $result = $stmt->get_result();
        
        $drugDetails = [];

        if ($row = $result->fetch_assoc()) {
            $drugDetails = [
                'DrugID' => $row['DrugID'],
                'Quantity' => $row['Quantity'],
                'BrandName' => $row['BrandName'],
                'GenericName' => $row['GenericName'],
                'Active_Ingredient' => $row['Active_Ingredient'],
                'Dosage' => $row['Dosage'],
                'Dosage_Form' => $row['Dosage_Form'],
                'Manufacturer' => $row['Manufacturer'],
                'Manufacture_Date' => $row['Manufacture_Date'],
                'SideEffects' => $row['SideEffects'],
                'Price' => $row['Price'],
                'DrugImage' => $row['DrugImage']
            ];

            echo json_encode(["status" => "success", "drugDetails" => $drugDetails]);
        } else {
            echo json_encode(["status" => "error", "message" => "No details found for this SupplyID"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Error preparing the query"]);
    }

    $conn->close();
}
?>
